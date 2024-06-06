<?php

namespace App\Http\Controllers;
use Auth;
use DataTables;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PodcastController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:podcast|podcast-create|podcast-edit|podcast-view|podcast-delete|podcast-publish|podcast-active', ['only' => ['index']]);
        $this->middleware('permission:podcast-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:podcast-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:podcast-view', ['only' => ['show']]);
        $this->middleware('permission:podcast-delete', ['only' => ['destroy']]);
        $this->middleware('permission:podcast-publish', ['only' => ['publish']]);
        $this->middleware('permission:podcast-active', ['only' => ['active']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)

    {
        if ($request->ajax()){
            $data = podcast::orderBy('id', 'desc')->get();
            return Datatables::of($data)
                                ->addIndexColumn()
                                ->addColumn('action', function($row){
                                    $btn = '';
                                    if(auth()->user()->can('podcast-edit') || auth()->user()->can('podcast-delete') ||
                                    auth()->user()->can('podcast-view') || auth()->user()->can('podcast-publish') ||
                                    auth()->user()->can('podcast-active')){
                                        $btn = ' <a id="actions" class="nav-link dropdown-toggle black-text actions" href="#"
                                         role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                </a>
                                        <div class="dropdown-menu action-list" aria-labelledby="actions">';


                                         if(auth()->user()->can('podcast-publish') && $row->status==1){
                                            $btn .= '<a class="dropdown-item text-blue publish-data" href="#"
                                             action="podcast/' . $row->id . '/publish"> Publish</a>';

                                         }
                                         if (auth()->user()->can('podcast-active') && $row->status == 2) {
                                            $btn .= '<a class="dropdown-item text-blue deactive-data" href="#" action="podcast/' . $row->id . '/active">Deactivate</a>';
                                        }
                                        if (auth()->user()->can('podcast-active') && $row->status == 3) {
                                            $btn .= '<a class="dropdown-item text-blue deactive-data" href="#" action="podcast/' . $row->id . '/active">Activate</a>';
                                        }
                                         if(auth()->user()->can('podcast-edit')){
                                            $btn .= '<a class="dropdown-item text-blue edit-data"
                                            href="#" action="/podcast/' . $row->id . '/edit">Edit</a>';
                                        }
                                         if(auth()->user()->can('podcast-view')){

                                          $btn .= ' <a class="dropdown-item  text-blue " href="/podcast/'.$row->id.'">View
                                          Detail</a>';
                                         }

                                        if(auth()->user()->can('podcast-delete') && $row->status==1){
                                            $btn .= '<a class="dropdown-item text-blue delete-data"
                                            action="/podcast/'.$row->id.'" href="#">Delete</a>';
                                        }


                                        $btn .=  '</div>';
                                    }
                                    return $btn;
                                })
                                ->addColumn('status', function ($row) {
                                    return "<span class='status_" . config("web_constant.status.$row->status") . "'>" . config("web_constant.status.$row->status") . "</span>";
                                })
                                ->addColumn('category_id', function ($row) {
                                    return config('web_constant.category_type.'. $row->category_id);
                                })
                                ->addColumn('cover_photo', function ($row) {
                                    // return '<img src=' . $row->cover_photo . ' width="40" height="40"/>';src="{{url('public/Image/'.$key->image')}}"
                                    return '<img src=' . $row->cover_photo .  ' width="40" height="40"/>';
                                })
                                ->addColumn('title', function ($row) {
                                    return '<a  href=' . $row->audio_path . ' target="_blank" rel="noopener noreferrer" />' . $row->title . '</a>';
                                })
                                ->addColumn('published_at', function($row){
                                    return $row->published_at==null ? $row->published_at : date('d/m/Y',strtotime($row->published_at));
                                })

                                ->rawColumns(['published_at','title', 'category','cover_photo', 'status','action'])

                                ->make(true);
        }

        return view('podcast.index',[
            'create_url' => '/podcast/create',
            'create_permission' => 'podcast-create',
            'keyword' => 'podcast',
            'title' => 'Podcast Lists',
        ]);



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $podcast = new Podcast();
        return view ('podcast.create',[
            'podcast' =>$podcast,
            'title' => 'Create New Podcast',
            'list_url' => 'podcast',
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
            'category_id' =>'required',
            'title' =>'required',
            'duration' => 'required|regex:/^\d{1,2}(:[\d]{2})?$/',
            'download_size' => 'required|regex:/^\d{1,2}(\.\d{1})?$/',
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'audio_file' => 'required|file|mimes:audio/mpeg,mpga,mp3,m4a|max:6000',
        ]);

        $request['status'] = 1;

        if($validator->fails()){
           return response($validator->errors(), 422);
        }
        $result = true;
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['download_size'] = $data['download_size'] . ' MB';
            $data['created_by'] = Auth::id();

            if ($request->has('cover_photo')) {
                $photo = Storage::disk('s3')->put('images', $request->cover_photo);
                $data['cover_photo'] = Storage::disk('s3')->url($photo);
            }
            if ($request->has('audio_file')) {
                $audio = Storage::disk('s3')->put('videos', $request->audio_file);
                $data['audio_path'] = Storage::disk('s3')->url($audio);
            }

            $podcast = Podcast::create($data);
            if (!$podcast) {
                $result = false;
                DB::rollback();
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => 'This podcast was created successfully!']);
        } else {
            session(['error' => 'This podcast can not create!']);
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
        //
        $data = Podcast::where('podcasts.id', $id)
                ->leftJoin('users as createdUsers','createdUsers.id','podcasts.created_by')
                ->leftJoin('users as updatedUsers','updatedUsers.id','podcasts.updated_by')
                ->leftJoin('users as publishedUsers','publishedUsers.id','podcasts.published_by')
                ->leftJoin('users as deactivatedUsers','deactivatedUsers.id','podcasts.deactivated_by')
                ->first([
                    'createdUsers.user_name as created_user_name',
                    'updatedUsers.user_name as updated_user_name',
                    'publishedUsers.user_name as published_user_name',
                    'deactivatedUsers.user_name as deactivated_user_name',
                    'podcasts.*'
                ]);

        return view('podcast.show', [
            'title' => 'Podcast Detail',
            'podcast' => $data ,
            'list_url' => 'podcast'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit( Podcast $podcast , Request $request)
    {
        //
        $podcast->current_index = $request->index;
        return view('podcast.edit',[
            'podcast' => $podcast,
            'title' => 'Update podcast',
            'list_url' => 'podcast',
            'is_update' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Podcast $podcast)
    {
        //
        if (!$request->has('cover_photo') && $request->virtual_img == null) {
            $request->merge([
                'cover_photo' => null,
            ]);
        } else {
            $request->merge([
                'cover_photo' => $request->virtual_img ?? $request->cover_photo,
            ]);
        }

        if (!$request->has('audio_file') && $request->virtual_audio == null) {
            $request->merge([
                'audio_file' => null,
            ]);
        } else {
            $request->merge([
                'audio_file' => $request->virtual_audio ?? $request->audio_file,
            ]);
        }

        $validator = Validator::make($request->all(),[
            'category_id' => 'required',
            'title' =>'required',
            'duration' => 'required|regex:/^\d{1,2}(:[\d]{2})?$/',
            'download_size' => 'required|regex:/^\d{1,2}(\.\d{1})?$/',
            'cover_photo' => $request->virtual_img==null ? 'required|image|mimes:jpeg,png,jpg,svg|max:2048' : 'required',
            'audio_file' =>$request->virtual_audio==null ? 'required|file|mimes:audio/mpeg,mpga,mp3,m4a|max:6000' : 'required',

        ]
        );
        if($validator->fails()){
            return response($validator->errors(), 422);
         }
        $result = true;
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['download_size'] = $data['download_size'] . ' MB';
            $data['updated_by'] = Auth::id();

            if ($request->virtual_img == null) {
                $photo = Storage::disk('s3')->put('images', $request->cover_photo);
                $data['cover_photo'] = Storage::disk('s3')->url($photo);
            }

            if ($request->virtual_audio == null) {
                $audio = Storage::disk('s3')->put('videos', $request->audio_file);
                $data['audio_path'] = Storage::disk('s3')->url($audio);
            }

            $data = $podcast->update($data);

            if (!$data) {
                $result = false;
                DB::rollback();
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => "This Podcast was updated successfully!"]);
        } else {
            session(['error' => "This Podcast can not update!"]);
        }


        return json_encode(['success' => $result, 'index' => $request->current_index]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Podcast $podcast)
    {
        $result = true;
        DB::beginTransaction();
        try {


             $podcast->delete();
             DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if($result){
            session(['success' => "Podcast was deleted successfully!"]);
        }else{
            session(['error' => "Podcast can not delete!"]);
        }

        return redirect('podcast?index='.$request->current_index);

    }

    public function publish(Request $request, $id)
    {
        $podcast = Podcast::find($id);
        $ans = Podcast::where('id', $id)
                    ->update([
                        'status' => 2,
                        'published_by' => Auth::id(),
                        'published_at' => date('Y-m-d H:i:s'),
                    ]);

        if ($ans) {
            session(['success' => 'podcast was published successfully!']);
        } else {
            session(['error' => 'podcast can not publish!']);
        }


        return redirect('/podcast?index=' . $request->current_index);

    }

    public function active(Request $request, $id)
    {
        $podcast = Podcast::find($id);
        $status = null;

        if ($podcast->status == 2) {
            $status = 3;
            $msg1 = "deactivated";
            $msg2 = "deactive";
        } elseif ($podcast->status == 3) {
            $status = 2;
            $msg1 = "activated";
            $msg2 = "active";
        }

        $ans = Podcast::where('id', $id)
            ->update([
                'status' => $status,
                'deactivated_by' => Auth::id(),
                'deactivated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($ans) {
            session(['success' => "podcast was $msg1 successfully!"]);
        } else {
            session(['error' => "podcast can not $msg2!"]);
        }

        return redirect('/podcast?index=' . $request->current_index);

    }

}
