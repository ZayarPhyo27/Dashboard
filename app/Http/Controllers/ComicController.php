<?php

namespace App\Http\Controllers;
use Auth;
use DataTables;
use App\Models\Comic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ComicController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:comic|comic-create|comic-edit|comic-view|comic-delete|comic-publish|comic-active', ['only' => ['index']]);
        $this->middleware('permission:comic-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:comic-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:comic-view', ['only' => ['show']]);
        $this->middleware('permission:comic-delete', ['only' => ['destroy']]);
        $this->middleware('permission:comic-publish', ['only' => ['publish']]);
        $this->middleware('permission:comic-active', ['only' => ['active']]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $data = new Comic();

            if($request->comic_status!=null)
               $data = $data->where('status',$request->comic_status);

            if ($request->comic_cate != null) {
                $data = $data->where('category_id', $request->comic_cate);
            }

            $data = $data->orderBy('id', 'DESC');

            $data = $data->get([
                'comics.*',

            ]);

            // $data = comic::orderBy('id', 'desc')->get();
            return Datatables::of($data)
                                ->addIndexColumn()
                                ->addColumn('action', function($row){
                                    $btn = '';
                                    if(auth()->user()->can('comic-edit') || auth()->user()->can('comic-delete') ||
                                    auth()->user()->can('comic-view') || auth()->user()->can('comic-publish') ||
                                    auth()->user()->can('comic-active')){
                                        $btn = ' <a id="actions" class="nav-link dropdown-toggle black-text actions" href="#"
                                         role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                </a>
                                        <div class="dropdown-menu action-list" aria-labelledby="actions">';

                                        if(auth()->user()->can('comic-publish') && $row->status==1){
                                            $btn .= '<a class="dropdown-item text-blue publish-data" href="#"
                                             action="comic/' . $row->id . '/publish"> Publish</a>';

                                         }
                                         if (auth()->user()->can('comic-active') && $row->status == 2) {
                                            $btn .= '<a class="dropdown-item text-blue deactive-data"
                                            href="#" action="comic/' . $row->id . '/active">Deactivate</a>';
                                        }
                                        if (auth()->user()->can('comic-active') && $row->status == 3) {
                                            $btn .= '<a class="dropdown-item text-blue deactive-data"
                                            href="#" action="comic/' . $row->id . '/active">Activate</a>';
                                        }

                                         if(auth()->user()->can('comic-edit')){
                                            $btn .= '<a class="dropdown-item text-blue edit-data"
                                            href="#" action="/comic/' . $row->id . '/edit">Edit</a>';
                                        }
                                         if(auth()->user()->can('comic-view')){

                                          $btn .= ' <a class="dropdown-item  text-blue " href="/comic/'.$row->id.'">View
                                          Detail</a>';
                                         }

                                        if(auth()->user()->can('comic-delete') && $row->status==1){
                                            $btn .= '<a class="dropdown-item text-blue delete-data"
                                            action="/comic/'.$row->id.'" href="#">Delete</a>';
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
                                    return '<a  href=' . $row->pdf_path . ' target="_blank" rel="noopener noreferrer" />' . $row->title . '</a>';
                                })
                                ->addColumn('published_at', function($row){
                                    return $row->published_at==null ? $row->published_at : date('d/m/Y',strtotime($row->published_at));
                                })

                                ->rawColumns(['published_at','title','cover_photo', 'category', 'status','action'])

                                ->make(true);
        }

        return view('comic.index',[
            'create_url' => '/comic/create',
            'create_permission' => 'comic-create',
            'keyword' => 'comic',
            'title' => 'Comic Lists',
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
        // dd("Create");
        $comic = new Comic();
        return view ('comic.create',[
            'comic' =>$comic,
            'title' => 'Create New Comic',
            'list_url' => 'comic',
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
        //
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'title' =>'required',
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'pdf_file' => 'required|mimes:pdf|max:6144',
            'download_size' => 'required|regex:/^\d{1}(\.\d{1})?$/'],
            [
                'download_size.regex' => 'Download size must be less than 10MB.'
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

            if ($request->has('pdf_file')) {
                $pdf = Storage::disk('s3')->put('pdfs', $request->pdf_file);
                $data['pdf_path'] = Storage::disk('s3')->url($pdf);
            }


            $comic = Comic::create($data);
            if (!$comic) {
                $result = false;
                DB::rollback();
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }
        if ($result) {
            session(['success' => 'This Comic was created successfully!']);
        } else {
            session(['error' => 'This Comic can not create!']);
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
        $data = Comic::where('comics.id', $id)
                ->leftJoin('users as createdUsers','createdUsers.id','comics.created_by')
                        ->leftJoin('users as updatedUsers','updatedUsers.id','comics.updated_by')
                        ->leftJoin('users as publishedUsers','publishedUsers.id','comics.published_by')
                        ->leftJoin('users as deactivatedUsers','deactivatedUsers.id','comics.deactivated_by')
                        ->first([
                            'createdUsers.user_name as created_user_name',
                            'updatedUsers.user_name as updated_user_name',
                            'publishedUsers.user_name as published_user_name',
                            'deactivatedUsers.user_name as deactivated_user_name',
                            'comics.*'
                        ]);

        return view('comic.show', [
            'title' => 'Comic Detail',
            'list_url' => "comic",
            'comic' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Comic $comic , Request $request)
    {

        $comic->current_index = $request->index;
        return view('comic.update',[
            'comic' => $comic,
            'title' => 'Update Comic',
            'list_url' => 'comic',
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
    public function update( Comic $comic , Request $request )
    {

        if (!$request->has('cover_photo') && $request->virtual_img == null) {
            $request->merge([
                'cover_photo' => null,
            ]);
        } else {
            $request->merge([
                'cover_photo' => $request->virtual_img ?? $request->cover_photo,
            ]);
        }


        if (!$request->has('pdf_file') && $request->virtual_pdf == null) {
            $request->merge([
                'pdf_file' => null,
            ]);
        } else {
            $request->merge([
                'pdf_file' => $request->virtual_pdf ?? $request->pdf_file,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title' =>'required',
            'cover_photo' => $request->virtual_img==null ? 'required|image|mimes:jpeg,png,jpg,svg|max:2048' : 'required',
            'pdf_file' => $request->virtual_pdf == null ? 'required|mimes:pdf|max:6144' : 'required',
            'category_id' => 'required',
            'download_size' => 'required|regex:/^\d{1}(\.\d{1})?$/',
        ],[
            'download_size.regex' => 'Download size must be less than 10MB.'
        ]);

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

            if ($request->virtual_pdf == null) {
                $pdf = Storage::disk('s3')->put('pdfs', $request->pdf_file);
                $data['pdf_path'] = Storage::disk('s3')->url($pdf);
            }


            $data = $comic->update($data);

            if (!$data) {
                $result = false;
                DB::rollback();
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => "This Comic was updated successfully!"]);
        } else {
            session(['error' => "This Comic can not update!"]);
        }


        return json_encode(['success' => $result, 'index' => $request->current_index]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $data = Comic::where('id', $id)->delete();
        return redirect()->route('comic.index');
    }
    public function publish(Request $request, $id)
    {
        $comic = Comic::find($id);
        $ans = Comic::where('id', $id)
                    ->update([
                        'status' => 2,
                        'published_by' => Auth::id(),
                        'published_at' => date('Y-m-d H:i:s'),
                    ]);

        if ($ans) {
            session(['success' => 'Comic was published successfully!']);
        } else {
            session(['error' => 'Comic can not publish!']);
        }


        return redirect('/comic?index=' . $request->current_index);

    }

    public function active(Request $request, $id)
    {
        $comic = Comic::find($id);
        $status = null;

        if ($comic->status == 2) {
            $status = 3;
            $msg1 = "deactivated";
            $msg2 = "deactive";
        } elseif ($comic->status == 3) {
            $status = 2;
            $msg1 = "activated";
            $msg2 = "active";
        }

        $ans = Comic::where('id', $id)
            ->update([
                'status' => $status,
                'deactivated_by' => Auth::id(),
                'deactivated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($ans) {
            session(['success' => "comic was $msg1 successfully!"]);
        } else {
            session(['error' => "comic can not $msg2!"]);
        }

        return redirect('/comic?index=' . $request->current_index);

    }

}
