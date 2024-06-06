<?php

namespace App\Http\Controllers;


use App\Http\Controllers\VideoController;
use App\Models\Video;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_or_permission:video-list|video-create|video-edit|video-delete|video-publish|video-active', ['only' => ['index']]);
        $this->middleware('role_or_permission:video-create', ['only' => ['create', 'store']]);
        $this->middleware('role_or_permission:video-edit', ['only' => ['edit', 'update']]);
        $this->middleware('role_or_permission:video-delete', ['only' => ['destroy']]);
        $this->middleware('role_or_permission:video-publish', ['only' => ['publish']]);
        $this->middleware('role_or_permission:video-active', ['only' => ['active']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = new Video();

            if($request->video_status!=null)
               $data = $data->where('status',$request->video_status);

            if ($request->video_cate != null) {
                $data = $data->where('category_id', $request->video_cate);
            }

            $data = $data->orderBy('id', 'DESC');

            // $data = $data ->leftJoin('users as createdUsers', 'createdUsers.id', 'videos.created_by')
            //               ->leftJoin('users as publishedUsers', 'publishedUsers.id', 'videos.published_by');

            $data = $data->get([
                'videos.*',
                // 'createdUsers.user_name as created_user_name',
                // 'publishedUsers.user_name as published_user_name',
            ]);



            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->user()->can('video-edit') || auth()->user()->can('video-delete')) {
                        $btn = ' <a id="actions" class="nav-link dropdown-toggle black-text actions" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    </a>
                                    <div class="dropdown-menu action-list" aria-labelledby="actions">';

                        if (auth()->user()->can('video-edit')) {
                            $btn .= '<a class="dropdown-item text-blue edit-video edit-data" href="#" action="video/' . $row->id . '/edit"> Edit</a>';
                        }

                        if (auth()->user()->can('video-publish') && $row->status == 1) {
                            $btn .= '<a class="dropdown-item text-blue publish-data" href="#" action="video/' . $row->id . '/publish"> Publish</a>';
                        }

                        if (auth()->user()->can('video-active') && $row->status == 2) {
                            $btn .= '<a class="dropdown-item text-blue deactive-data" href="#" action="video/' . $row->id . '/active">Deactive</a>';
                        }

                        if (auth()->user()->can('video-active') && $row->status == 3) {
                            $btn .= '<a class="dropdown-item text-blue active-data" href="#" action="video/' . $row->id . '/active">Activate</a>';
                        }

                        if (auth()->user()->can('video-delete')&& $row->status == "1") {
                            $btn .= '<a class="dropdown-item text-blue edit-video delete-data" href="#" action="video/' . $row->id . '">Delete</a>';
                        }

                        if(auth()->user()->can('video-view'))
                                          $btn .= '<a class="dropdown-item text-blue view-video view-data" href="video/' . $row->id . '"> View Detail</a>';

                        $btn .= '</div>';
                    }
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    return "<span class='status_" . config("web_constant.status.$row->status") . "'>" . config("web_constant.status.$row->status") . "</span>";
                })
                ->addColumn('cover_photo', function ($row) {
                    // return '<img src=' . $row->cover_photo . ' width="40" height="40"/>';src="{{url('public/Image/'.$key->image')}}"
                    return '<img src=' . $row->cover_photo .  ' width="40" height="40"/>';
                })
                ->addColumn('published_at', function($row){
                    return $row->published_at==null ? $row->published_at : date('d/m/Y',strtotime($row->published_at));
                })
                ->addColumn('category_name', function ($row) {
                    return config("web_constant.category_type.$row->category_id");
                })
                ->addColumn('video_title', function ($row) {
                    return '<a  href=' . $row->video_path . ' target="_blank" rel="noopener noreferrer" />' . $row->title . '</a>';
                })

                ->rawColumns(['cover_photo', 'action', 'status', 'video_title','published_at'])
                ->make(true);

        }

        $create_permission = false;
        if(auth()->user()->can('video-create')){
            $create_permission = 'video-create';
        }

        return view('video.index',[
            'create_url' => 'video/create',
            'create_permission' => $create_permission,
            'keyword' => 'video',
            'title' => 'Video Lists'
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $video = new Video();
        return view('video.create', [
            'video' => $video,
            'title' => 'Create New Video',
            'list_url' => 'video',
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
                        'category_id' => 'required',
                        'title' => ['required', 'string', 'max:255'],
                        'video_file' => 'required|mimetypes:video/mp4',
                        'duration' => 'required|regex:/^\d{1,2}(:[\d]{2})?$/',
                        'download_size' => 'required|regex:/^\d{1,2}(\.\d{1})?$/',
                        'cover_photo' => 'required|mimes:jpeg,png,jpg|max:2048',
                ]);

        if ($validator->fails()) {
            return response($validator->messages(), 422);
        }

        $result = true;

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['download_size'] = $data['download_size'] . ' MB';
            $data['created_by'] = Auth::id();
            $data['status'] = 1;

            // S3 configuration
            if ($request->has('video_file')) {
                $video = Storage::disk('s3')->put('videos', $request->video_file);
                $data['video_path'] = Storage::disk('s3')->url($video);
            }

            if ($request->has('cover_photo')) {
                $photo = Storage::disk('s3')->put('images', $request->cover_photo);
                $data['cover_photo'] = Storage::disk('s3')->url($photo);
            }

            // if( Video::where('order',$request->order)->count() > 0){
            //     Video::where('order','>=',$request->order)->increment('order',1);
            // }

            $video = Video::create($data);
            if (!$video) {
                $result = false;
                DB::rollback();
            }
            // if($request->keyword_id!=null){
            //     if (!Keyword::saveExploreKeyword($request->keyword_id, $video->id, 3)) {
            //         $result = false;
            //         DB::rollback();
            //     }
            // }
            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => 'Video was created successfully!']);
        } else {
            session(['error' => 'Video can not create!']);
        }

        return json_encode(['success' => $result, 'index' => $request->current_index]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        $video = Video::where('videos.id', $video->id)
                ->leftJoin('users as createdUsers','createdUsers.id','videos.created_by')
                ->leftJoin('users as updatedUsers','updatedUsers.id','videos.updated_by')
                ->leftJoin('users as publishedUsers','publishedUsers.id','videos.published_by')
                ->leftJoin('users as deactivatedUsers','deactivatedUsers.id','videos.deactivated_by')
                ->first([
                    'createdUsers.user_name as created_user_name',
                    'updatedUsers.user_name as updated_user_name',
                    'publishedUsers.user_name as published_user_name',
                    'deactivatedUsers.user_name as deactivated_user_name',
                    'videos.*'
                ]);

        return view("video.show",[
            'video' => $video,
            'title' => 'Video Detail',
            'list_url' => 'video',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Video $video)
    {
        //
        $video->current_index = $request->index;
        return view('video.update', [
            'video' => $video,
            'title' => 'Update Video',
            'list_url' => 'video',
            'is_update' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
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

        if (!$request->has('video_file') && $request->virtual_video == null) {
            $request->merge([
                'video_file' => null,
            ]);
        } else {
            $request->merge([
                'video_file' => $request->virtual_video ?? $request->video_file,
            ]);
        }

        $validator = Validator::make($request->all(), [
                                    'title' => ['required', 'string', 'max:255'],
                                    'video_file' => $request->virtual_video == null ? 'required|mimetypes:video/mp4' : 'required',
                                    'duration' => 'required|regex:/^\d{1,2}(:[\d]{2})?$/',
                                    'download_size' => 'required|regex:/^\d{1,2}(\.\d{1})?$/',
                                    'cover_photo' => $request->virtual_img==null ? 'required|mimes:jpg,jpeg,png|max:2048' : 'required',
                                    // 'keyword_id' => 'required|array',
                                    'category_id' => 'required',
                                  ],
                                  [
                                    'duration.date_format' => 'The video duration is invalid format.',
                                  ]
                    );

        if ($validator->fails()) {
            return response($validator->messages(), 422);
        }

        $result = true;
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['updated_by'] = Auth::id();
            $data['download_size'] = $data['download_size'] . ' MB';
            if ($request->virtual_img == null) {
                $photo = Storage::disk('s3')->put('images', $request->cover_photo);
                $data['cover_photo'] = Storage::disk('s3')->url($photo);
            }


            if ($request->virtual_video == null) {
                $s3_video = Storage::disk('s3')->put('videos', $request->video_file);
                $data['video_path'] = Storage::disk('s3')->url($s3_video);
            }

            // if($request->virtual_img == null)
            // {
            //     $file= $request->file('cover_photo');
            //     $extension= $file->getClientOriginalExtension();
            //     $filename = time().'.'.$extension;
            //     $file-> move('public/Image/', $filename);
            //     $data['cover_photo'] = $filename; // Save the new and correct file name into the input

            // }

            $ans = $video->update($data);

            if (!$ans) {
                $result = false;
                DB::rollback();
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => "Video was updated successfully!"]);
        } else {
            session(['error' => "Video can not update!"]);
        }

        return json_encode(['success' => $result, 'index' => $request->current_index]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Video $video)
    {
        //
        $result = true;
        DB::beginTransaction();
        try {
            // ExploreKeyword::where(['explore_id' => $video->id, 'explore_type' => 3])->delete();
            $video->deleted_by = Auth::id();

            $video->delete();
            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => "Video was deleted successfully!"]);
        } else {
            session(['error' => "Video can not delete!"]);
        }

        return redirect('video?index=' . $request->current_index);
    }

    public function publish(Request $request, $id)
    {
        $result = Video::where('id', $id)->update(['status' => 2, 'published_by' => Auth::id(), 'published_at' => date('Y-m-d H:i:s')]);

        if ($result) {
            session(['success' => "Video was published successfully!"]);
        } else {
            session(['error' => "Video was not published!"]);
        }

        return redirect('/video?index=' . $request->current_index);
    }

    public function active(Request $request, $id)
    {
        $video = Video::where('id', $id)->first();
        $result = true;
        DB::beginTransaction();
        try {
            if ($video->status == 3) {
                $msg = "activated";

                $video->status = 2;
                // $video->deactivated_by = Auth::id();
                // $video->deactivated_at = date('Y-m-d H:i:s');
                $video->save();

            } elseif ($video->status == 2) {
                $msg = "deactivated";
                $video->status = 3;
                $video->deactivated_by = Auth::id();
                $video->deactivated_at = date('Y-m-d H:i:s');
                $video->save();

            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => "Article was $msg successfully!"]);
        } else {
            session(['error' => "Article was not $msg!"]);
        }

        // return json_encode(['success' => $result, 'index' => $request->current_index]);
        return redirect('/video?index=' . $request->current_index);
    }

}
