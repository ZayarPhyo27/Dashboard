<?php

namespace App\Http\Controllers;
use App\Models\Article;
use App\Models\ArticleDetail;
use App\Models\ArticleQuiz;
use DB;
use Auth;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class ArticleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:article-list|article-create|article-edit|article-delete|article-publish|article-active|article-view', ['only' => ['index']]);
        $this->middleware('permission:article-create', ['only' => ['create','store']]);
        $this->middleware('permission:article-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:article-view', ['only' => ['show']]);
        $this->middleware('permission:article-publish', ['only' => ['publish']]);
        $this->middleware('permission:article-active', ['only' => ['active']]);
        $this->middleware('permission:article-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {



            $data = new Article();

            if ($request->search_status != null) {
                $data = $data->where('status', $request->search_status);
            }

            if ($request->category_id!= null) {
                $data = $data->where('category_id', $request->category_id);
            }



            $data = $data->orderBy('id', 'DESC');

            // if ($request->article_status != null) {
            //     $data = $data->where('articles.status', $request->article_status);
            // }

            $data = $data ->leftJoin('users as createdUsers', 'createdUsers.id', 'articles.created_by')
                          ->leftJoin('users as publishedUsers', 'publishedUsers.id', 'articles.published_by');



            $data = $data->get([
                'articles.*',
                'createdUsers.user_name as created_user_name',
                'publishedUsers.user_name as published_user_name',

            ]);

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '';
                        if(auth()->user()->can('article-edit') || auth()->user()->can('article-delete') || auth()->user()->can('article-publish') || auth()->user()->can('article-active')){
                            $btn = ' <a id="actions" class="nav-link dropdown-toggle black-text actions" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    </a>
                                    <div class="dropdown-menu action-list" aria-labelledby="actions">';
                            if(auth()->user()->can('article-edit'))
                              $btn .= '<a class="dropdown-item text-blue edit-article edit-data" href="#" action="article/'.$row->id.'/edit"> Edit</a>';

                            if(auth()->user()->can('article-publish') && $row->status == "1")
                               $btn .= '<a class="dropdown-item text-blue edit-article publish-data" href="#" action="/article/'.$row->id.'/publish">Publish</a>';

                            if(auth()->user()->can('article-active') && $row->status == "3")
                                $btn .= '<a class="dropdown-item text-blue edit-article active-data" href="#" action="/article/'.$row->id.'/active">Activate</a>';

                            if(auth()->user()->can('article-active') && $row->status == "2")
                               $btn .= '<a class="dropdown-item text-blue edit-article deactive-data" href="#" action="/article/'.$row->id.'/active">Deactivate</a>';

                            if(auth()->user()->can('article-view'))
                               $btn .= '<a class="dropdown-item text-blue  view-data" href="/article/'.$row->id.'">View Detail</a>';


                            if(auth()->user()->can('article-delete')&& $row->status == "1")
                               $btn .= '<a class="dropdown-item text-blue edit-article delete-data" href="#" action="/article/'.$row->id.'">Delete</a>';

                            $btn .=  '</div>';
                        }
                        return $btn;
                    })
                    ->addColumn('category_id', function($row){
                        return "<span>".config("web_constant.category_type.$row->category_id")."</span>";
                    })
                    ->addColumn('status', function($row){
                        return "<span class='status_".config("web_constant.status.$row->status")."'>".config("web_constant.status.$row->status")."</span>";
                    })
                    ->addColumn('cover_photo', function ($row) {
                        return '<img src='.$row->cover_photo.' width="150" height="100"/>';
                    })
                    ->rawColumns(['tile','category_id','cover_photo', 'status','action'])
                    ->make(true);
        }

        return view('article.index',[
                    'title' => 'Article Lists',
                    'create_permission' => 'article-create',
                    'route' => 'article',
                    'keyword' => 'article',
                    'create_url' => '/article/create',
               ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $article = new Article();
        return view('article.create',[
                    'article' => $article,
                    'title' => 'Create New Article',
                    'list_url' => 'article',
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
        if(!$request->has('photo_path')){
            $photo = [];
            for ($i=0; $i < count($request->detail_description) ; $i++) {
                array_push($photo,null);
            }
            $request->merge([
                'photo_path' => $photo
            ]);
        }
        $detail_desc = explode('@#$%^&*',$request->detail_desc);
        unset($detail_desc[count($detail_desc) - 1]);

        $request->merge([
            'detail_description' => $detail_desc,
        ]);

        $validator = Validator::make($request->all(),[
            'category_id' => 'required',
            'cover_photo' => 'required|mimes:jpg,jpeg,png|max:2048',
            'title' =>  'required',
            'detail_description.0' => 'required',
            'photo_path.*' => 'nullable|max:2048',
        ],[
            'detail_description.0.required' => 'The description field is required.',
        ]);

        if($validator->fails()){
            return response($validator->messages(), 422);
        }

        $result = true;
        DB::beginTransaction();
        try {
            $data = $request->all();

            $data['created_by'] = Auth::id();
            $data['status'] = 1;


            if(!$request->has('photo_path')){
                $photo = [];
                for ($i=0; $i < count($request->detail_description) ; $i++) {
                    array_push($photo,null);
                }
                $request->merge([
                    'photo_path' => $photo
                ]);
            }





        // if(isset($request->cover_photo)){

        //         $file= $request->file('cover_photo');
        //         $extension= $file->getClientOriginalExtension();
        //         $filename = time().'.'.$extension;
        //         $file-> move('public/Image/', $filename);
        //         $data['cover_photo'] = $filename; // Save the new and correct file name into the input

        //     }


       if(isset($request->cover_photo)){
                $photo = Storage::disk('s3')->put('images', $request->cover_photo);
                $data['cover_photo'] = Storage::disk('s3')->url($photo);
        }

         $article = Article::create($data);
         if(!$article){
            $result = false;
            DB::rollback();
        }

        if($result){
            if(!Article::saveDetail($request->all(),$article->id)){
                $result = false;
                DB::rollback();
            }
         }

         DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if($result){
            session(['success' => "Article was created successfully!"]);
        }else{
            session(['error' => "Article can not create!"]);
        }

        return json_encode(['success' => $result, 'index' => $request->current_index]);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {

        $article = Article::where('articles.id',$article->id)
        ->leftJoin('users as createdUsers','createdUsers.id','articles.created_by')
        ->leftJoin('users as updatedUsers','updatedUsers.id','articles.updated_by')
        ->leftJoin('users as publishedUsers','publishedUsers.id','articles.published_by')
        ->leftJoin('users as deactivatedUsers', 'deactivatedUsers.id', 'articles.deactivated_by')
        ->with([
            'quizs' => function($q){
                $q->leftJoin('quizs','quizs.id','article_quiz.quiz_id')
                ->select('quizs.question','article_quiz.*');
            },
      ])
        ->first([
             'createdUsers.user_name as created_user_name',
             'updatedUsers.user_name as updated_user_name',
             'publishedUsers.user_name as published_user_name',
             'deactivatedUsers.user_name as deactivated_user_name',
             'articles.*'
        ]);
        return view('article.view',[
            'article' => $article,
            'title' => 'Article Detail',
            'list_url' => 'article',
       ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article,Request $request)
    {
        // $article = Article::where('articles.id',$article->id)
        // ->first();
        $article->current_index = $request->index;
        return view('article.update',[
        'article' => $article,
        'title' => "Update Article",
        'is_update' => true,
        'list_url' => 'article',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        if(!$request->has('cover_photo') && $request->virtual_img==null){
            $request->merge([
                'cover_photo' => null
            ]);
        }else{
            $request->merge([
                'cover_photo' => $request->virtual_img ?? $request->cover_photo
            ]);
        }

        if(!$request->has('category_id')){
            $request->merge([
                'category_id' => null,
            ]);
        }

        $detail_desc = explode('@#$%^&*',$request->detail_desc);
        unset($detail_desc[count($detail_desc) - 1]);

        $request->merge([
            'detail_description' => $detail_desc,
        ]);

        $validator = Validator::make($request->all(),[
            'category_id' => 'required',
            'cover_photo' => $request->virtual_img==null ? 'required|image|mimes:jpeg,png,jpg,svg|max:2048' : 'required',
            'title' =>  'required',
            'detail_description.0' => 'required',
            'photo_path.*' => 'nullable|max:2048',
        ],[
            'detail_description.0.required' => 'The description field is required.'
        ]);
        if($validator->fails()){
            return response($validator->messages(), 422);
        }

        $result = true;
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['updated_by'] = Auth::id();
            // if($request->virtual_img==null){
            //     $file= $request->file('cover_photo');
            //     $extension= $file->getClientOriginalExtension();
            //     $filename = time().'.'.$extension;
            //     $file-> move('public/Image/', $filename);
            //     $data['cover_photo'] = $filename;
            //  }

            if($request->virtual_img==null){
                $photo = Storage::disk('s3')->put('images', $request->cover_photo);
                $data['cover_photo'] = Storage::disk('s3')->url($photo);
             }

             $ans = $article->update($data);

             if(!$ans){
                 $result = false;
                 DB::rollback();
             }

             if($result){

                ArticleDetail::where('article_id',$article->id)->forceDelete();
                if(!Article::saveDetail($request->all(),$article->id)){
                    $result = false;
                    DB::rollback();
                }
             }

         DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if($result){
            session(['success' => "Article was updated successfully!"]);
        }else{
            session(['error' => "Article can not update!"]);
        }

        return json_encode(['success' => $result, 'index' => $request->current_index]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article,Request $request)
    {
        $result = true;
        DB::beginTransaction();
        try {

            $ans = Article::where('id',$article->id)
                        ->update([
                            'deleted_by' => Auth::id(),
                        ]);
             ArticleDetail::where('article_id', $article->id)->delete();

             if(!$ans){
                 $result = false;
                 DB::rollback();
             }

             if(!$article->delete()){
                $result = false;
                DB::rollback();
             }

             DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }
        if($ans){
            session(['success' => "Article was deleted successfully!"]);
        }else{
            session(['error' => "Article can not delete!"]);
        }

        return redirect('article?index='.$request->current_index);
    }

    public function upload(Article $article)
    {
        //
    }

    public function publish(Request $request, $id)
    {
        $result = Article::where('id', $id)->update(['status' => 2, 'published_by' => Auth::id(), 'published_at' => date('Y-m-d H:i:s')]);

        if ($result) {
            session(['success' => "Article was published successfully!"]);
        } else {
            session(['error' => "Article was not published!"]);
        }

        return redirect('/article?index=' . $request->current_index);
    }

    public function active(Request $request, $id)
    {

        $article = Article::where('id', $id)->first();
        $result = true;
        DB::beginTransaction();
        try {
            if ($article->status == 3) {
                $msg = "activated";
                $article->status = 2;
                // $article->deactived_by = Auth::id();
                // $article->deactived_at = date('Y-m-d H:i:s');
                $article->save();

            } elseif ($article->status == 2) {
                $msg = "deactivated";
                $article->status = 3;
                $article->deactivated_by = Auth::id();
                $article->deactivated_at = date('Y-m-d H:i:s');
                $article->save();

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

        return redirect('/article?index=' . $request->current_index);
    }
}
