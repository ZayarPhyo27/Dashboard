<?php

namespace App\Http\Controllers;


use DB;
use DataTables;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_or_permission:user-list', ['only' => ['index']]);
        $this->middleware('role_or_permission:user-create', ['only' => ['store']]);
        $this->middleware('role_or_permission:user-edit', ['only' => ['update']]);
        $this->middleware('role_or_permission:user-view', ['only' => ['show']]);
        $this->middleware('role_or_permission:user-delete', ['only' => ['destroy']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()){
            // dd($request->all());
            $data = new User();

            $data = $data->leftJoin('roles','roles.id','users.role_id')
                          ->select('roles.name as role_name','users.*')->get();

            return Datatables::of($data)
                                ->addIndexColumn()
                                ->addColumn('action', function($row){
                                    $btn = '';
                                    if(auth()->user()->can('user-list') || auth()->user()->can('user-edit') || auth()->user()->can('user-delete') || auth()->user()->can('user-view')){
                                        $btn = '<a id="actions" class="nav-link dropdown-toggle black-text actions" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                </a>
                                                <div class="dropdown-menu action-list" aria-labelledby="actions">';

                                        if(auth()->user()->can('user-edit'))
                                          $btn .= ' <a class="dropdown-item text-blue edit-user edit-data" href="#" action="user/'.$row->id.'/edit"> Edit</a>';

                                        // if(auth()->user()->can('user-block') && $row->status==1 && $row->role_id==4)
                                        //   $btn .= '<a class="dropdown-item text-blue block-data" href="#" action="user/'.$row->id.'/block"> Block</a>';

                                        // if(auth()->user()->can('user-block')&& $row->status==0  && $row->role_id==4)
                                        //   $btn .= '<a class="dropdown-item text-blue unblock-data" href="#" action="user/'.$row->id.'/block"> Unblock</a>';

                                        if(auth()->user()->can('user-view'))
                                          $btn .= '<a class="dropdown-item text-blue view-user view-data" href="user/'.$row->id.'"> View Detail</a>';

                                        if(auth()->user()->can('user-delete'))
                                          $btn .= '<a class="dropdown-item text-blue edit-user delete-data" href="#" action="/user/'.$row->id.'">Delete</a>';

                                        $btn .=  '</div>';
                                    }
                                    return $btn;
                                })
                                ->make(true);
        }

        $create_permission = false;
        if(auth()->user()->can('user-create')){
            $create_permission = 'user-create';
        }
        return view('user.index',[
            'create_url' => 'user/create',
            'create_permission' => $create_permission,
            'keyword' => 'user',
            'title' => 'User Lists'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();
        return view('user.create',[
                    'user' => $user,
                    'title' => 'Create New User',
                    'list_url' => 'user',
                    'is_profile' => false
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
        $validator = Validator::make($request->all(), [
            'user_name' => ['required', 'string',Rule::unique('users')->whereNull('deleted_at'),'max:50','regex:/^[^\~\!\@\#\$\$\%\^\&\*\+\=\\\`\<\>\?\_\:\;\(\)\-\.\,]{1,100}[^\~\!\@\#\$\$\%\^\&\*\+\=\\\`\<\>\?\_\:\;]*$/'],
            'role_id' => ['required'],
            'password' => $request->role_id == 5 ? 'nullable' : ['required', 'string', 'min:8', 'confirmed'],
            // 'phone_no' => $request->role_id == 5 ? 'nullable' : ['required', Rule::unique('users')->whereNull('deleted_at'), 'regex:/^[\-\d-]{8,12}$/'],
            'email' => $request->role_id == 5 ? 'nullable' : ['required', Rule::unique('users')->whereNull('deleted_at'),'regex:/[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}/'],
        ],
        [
           'role_id.required' => 'The user role field is required.',
        ]);

        if ($validator->fails()) {
            return response($validator->messages(), 422);
        }

        $result = true;
        DB::beginTransaction();
        try {
             $data = $request->all();
             $data['created_by'] = Auth::id();
            //  $data['status'] = 1;

             if($request->password!=null)
                $data['password'] = Hash::make($data['password']);

             $user = User::create($data);

            //  $role->syncPermissions($permissions);
             $user->syncRoles([$request->role_id]);

             if(!$user){
                 $result = false;
                 DB::rollback();
             }

             DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if($result){
            session(['success' => 'User was created successfully!']);
        }else{
            session(['error' => 'User can not create!']);
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
        $user = User::where('users.id',$id)
                    ->leftJoin('roles','roles.id','users.role_id')
                    ->select('roles.name as role_name','users.*')->first();
        $title = "User Detail";
        $list_url = "user";
        return view('user.show',compact('user','title','list_url'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,User $user)
    {
        $user->current_index = $request->index;
        return view('user.update',[
                    'user' => $user,
                    'title' => 'Update User',
                    'list_url' => 'user',
                    'is_profile' => false
                ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $validator = Validator::make($request->all(), [
            'user_name' => ['required', 'string', Rule::unique('users')->whereNull('deleted_at')->ignore($user->id),'max:50','regex:/^[^\~\!\@\#\$\$\%\^\&\*\+\=\\\`\<\>\?\_\:\;\(\)\-\.\,]{1,100}[^\~\!\@\#\$\$\%\^\&\*\+\=\\\`\<\>\?\_\:\;]*$/'],
            'role_id' => $request->auth_role != null ? 'nullable' : 'required',
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'email' => ['required', Rule::unique('users')->whereNull('deleted_at')->ignore($user->id), 'regex:/[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}/'],
        ],
        [
           'role_id.required' => 'The user role field is required.',
        ]);

        if ($validator->fails()) {
            return response($validator->messages(), 422);
        }

        $result = true;
        DB::beginTransaction();
        try {
             $data = $request->all();
             $data['updated_by'] = Auth::id();

             if($request->password!=null){
                $data['password'] = Hash::make($data['password']);
             }else $data['password'] = $user->password;

             $ans = $user->update($data);

             if($request->auth_role){
                $user->syncRoles([$request->auth_role]);
             }
             else{
                $user->syncRoles([$request->role_id]);
             }

             if(!$ans){
                 $result = false;
                 DB::rollback();
             }

             DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        $m = "User";
        if($result){
            session(['success' => "$m was updated successfully!"]);
        }else{
            session(['error' => "$m can not update!"]);
        }
        return json_encode(['success' => $result, 'index' => $request->current_index]);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,User $user)
    {
        $result = true;
        DB::beginTransaction();
        try {

            //  $user->deleted_by = Auth::id();
            //  $user->save();
             $user->delete();
             DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if($result){
            session(['success' => "User was deleted successfully!"]);
        }else{
            session(['error' => "User can not delete!"]);
        }

        return redirect('user?index='.$request->current_index);
    }

    public function profile()
    {
        $user = auth()->user();
        return view('user.update',[
                    'user' => $user,
                    'title' => 'User Profile',
                    'list_url' => '/',
                    'is_profile' => true
                ]);
    }

}
