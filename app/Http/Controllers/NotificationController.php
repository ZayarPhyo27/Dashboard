<?php

namespace App\Http\Controllers;
use DB;
use DataTables;
use App\Models\Notification;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PushNotification;
use Illuminate\Support\Facades\Validator;


class NotificationController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
     {
         $this->middleware('permission:notification|notification-create|notification-update|notification-delete|notification-push', ['only' => ['index']]);
         $this->middleware('permission:notification-create', ['only' => ['create', 'store']]);
         $this->middleware('permission:notification-update', ['only' => ['edit', 'update']]);
         $this->middleware('permission:notification-delete', ['only' => ['destroy']]);
         $this->middleware('permission:notification-push', ['only' => ['push']]);
     }
    public function index(Request $request)
    
    {
        // $notifications = Notification::orderBy('id', 'desc')->get();
        // return view('notification.index',[
        //     'notifications' => $notifications,
        //     'title' => 'Notification Lists',
        // ]);
        if ($request->ajax()){
            $data = Notification::orderBy('id', 'desc')->get(); 
            return Datatables::of($data)
                                ->addIndexColumn()
                                ->addColumn('action', function($row){
                                    $btn = '';
                                    if(auth()->user()->can('notification-update') || auth()->user()->can('notification-delete') || auth()->user()->can('notification-push') ){
                                        $btn = ' <a id="actions" class="nav-link dropdown-toggle black-text actions" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                </a>
                                                 <div class="dropdown-menu action-list" aria-labelledby="actions">';
                                        if(auth()->user()->can('notification-push') && $row->status==1)
                                        $btn .= '<a class="dropdown-item text-blue push-data" href="#"
                                       action="/notification/'.$row->id.'/push">Push</a>';
                                        if(auth()->user()->can('notification-update') && $row->status==1)
                                          $btn .= '<a class="dropdown-item text-blue edit-data" 
                                         href="#" action="/notification/' . $row->id . '/edit">Edit</a>';
                        
                                          $btn .= ' <a class="dropdown-item  text-blue " href="/notification/'.$row->id.'">View
                                          Detail</a>';
            
                                        if(auth()->user()->can('notification-delete') && $row->status==1)
                                          $btn .= '<a class="dropdown-item text-blue delete-data"
                                          action="/notification/'.$row->id.'" href="#">Delete</a>';
            
                                        $btn .=  '</div>';
                                    }
                                    return $btn;
                                })
                                ->addColumn('status', function ($row) {
                                    return "<span class='status_" . config("web_constant.notification_status.$row->status") . "'>" . config("web_constant.notification_status.$row->status") . "</span>";
                                })

                                ->rawColumns(['name', 'description', 'status','action'])

                                ->make(true);
        }
        $create_permission = false;
        if(auth()->user()->can('notification-create')){
            $create_permission = 'notification-create';
        }
        return view('notification.index',[
            'create_url' => '/notification/create',
            'create_permission' => $create_permission,
            'keyword' => 'notification',
            'title' => 'All Notification',
        ]);
        
        

    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $notification = new Notification();
        return view('notification.create',[
            'notification' => $notification,
            'title' => 'Create New Notification',
            'list_url' => 'notification',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required',
        ]);

        $request['status'] = 1;

        if($validator->fails()){
           return response($validator->errors(), 422);
        }
        $result = true;
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['created_by'] = Auth::id();
            $notification = Notification::create($data);
            if (!$notification) {
                $result = false;
                DB::rollback();
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => 'This notification was created successfully!']);
        } else {
            session(['error' => 'This notification can not create!']);
        }

        return json_encode(['success' => $result, 'index' => $request->current_index]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notification = Notification::where('notification.id',$id)
        ->leftJoin('users as createdUsers','createdUsers.id','notification.created_by')
        ->leftJoin('users as updatedUsers','updatedUsers.id','notification.updated_by')
        ->leftJoin('users as pushedUsers','pushedUsers.id','notification.pushed_by')
        ->first([
             'createdUsers.user_name as created_user_name',
             'updatedUsers.user_name as updated_user_name',
             'pushedUsers.user_name as pushed_user_name',
             'notification.*'
        ]);
        return view('notification.show',[
            'notification' => $notification,
            'title' => 'Notification Detail',
            'list_url' => 'notification',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,Notification $notification)
    {
        $notification->current_index = $request->index;
        // $notification = Notification::findOrFail($id);
        return view('notification.edit',[
            'notification' => $notification,
            'title' => 'Update Notification',
            'list_url' => 'notification',
        ]);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required',
        ]
        );
        if($validator->fails()){
            return response($validator->errors(), 422);
         }
        $result = true;
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['updated_by'] = Auth::id();
            $data['updated_at'] = date('Y-m-d H:i:s');
            $notification = Notification::findOrFail($id);
            $ans = $notification->update($data);

            if (!$ans) {
                $result = false;
                DB::rollback();
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => "This notification was updated successfully!"]);
        } else {
            session(['error' => "This notification can not update!"]);
        }
       

        return json_encode(['success' => $result, 'index' => $request->current_index]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $noti = Notification::findOrFail($id);
        $result = true;
        DB::beginTransaction();
        try {
            $ans = Notification::where('id', $noti->id)
                ->update([
                    'deleted_by' => Auth::id(),
                ]);

            if(!$ans){
                DB::rollback();
                $result = false;
            }else {
                if(!$noti->delete()){
                    DB::rollback();
                    $result = false;
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => "This notification was deleted successfully!"]);
        } else {
            session(['error' => "This notification can not delete!"]);
        }

        return redirect('notification?index='.$request->current_index);

    }

    public static function push(Request $request, $id)
    {
        $data['status'] = 2;
        $data['pushed_by'] = Auth::id();
        $data['pushed_at'] = date("Y-m-d H:i:s");
        $result = true;
        DB::beginTransaction();
        try {
            $ans = Notification::where('id', $id)->update($data);

            if (!$ans) {
                $result = false;
                DB::rollback();
            }

            $noti = Notification::find($id);
            $users = User::pluck('fcm_token')->toArray();
            PushNotification::pushNoti($users, $noti->title,$noti->id, $noti->description);

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            // dd($result);
            session(['success' => "This notification was pushed successfully!"]);
        } else {
            session(['error' => "This notification can not push!"]);
        }

        return redirect('/notification?index=' . $request->current_index);

    }
}
