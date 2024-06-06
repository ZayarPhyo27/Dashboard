<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use DataTables;
use App\Models\Quiz;
use App\Models\Article;
use App\Models\QuizOption;
use App\Models\ArticleQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:quiz-list|quiz-create|quiz-view|quiz-edit|quiz-publish|quiz-active|quiz-delete', ['only' => ['index']]);
        $this->middleware('permission:quiz-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:quiz-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:quiz-view', ['only' => ['show']]);
        $this->middleware('permission:quiz-delete', ['only' => ['destroy']]);
        $this->middleware('permission:quiz-publish', ['only' => ['publish']]);
        $this->middleware('permission:quiz-active', ['only' => ['active']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = new Quiz();

            if ($request->content_type != null) {
                $data = $data->where('content_type', $request->content_type);
            }

            if ($request->quiz_type != null) {
                $data = $data->where('quiz_type', $request->quiz_type);
            }

            if ($request->quiz_status != null) {
                $data = $data->where('quizs.status', $request->quiz_status);
            }
            $data = $data->leftJoin('users as createdUsers', 'createdUsers.id', 'quizs.created_by')
                         ->leftJoin('users as publishedUsers', 'publishedUsers.id', 'quizs.published_by');

            $data = $data->latest()
                        ->get([
                            'quizs.*',
                            'createdUsers.user_name as created_user_name',
                            'publishedUsers.user_name as published_user_name',
                        ]);

            return Datatables::of($data)
                                ->addIndexColumn()
                                ->addColumn('action', function ($row) {
                                    $btn = '';
                                    if (auth()->user()->can('quiz-edit') || auth()->user()->can('quiz-view') || auth()->user()->can('quiz-active') || auth()->user()->can('quiz-publish') || auth()->user()->can('quiz-delete')) {
                                        $btn = ' <a id="actions" class="nav-link dropdown-toggle black-text actions" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                    </a>
                                                    <div class="dropdown-menu action-list" aria-labelledby="actions">';
                                        if (auth()->user()->can('quiz-edit')) {
                                            $btn .= '<a class="dropdown-item text-blue edit-data" href="#" action="quiz/' . $row->id . '/edit"> Edit</a>';
                                        }

                                        if (auth()->user()->can('quiz-publish') && $row->status == 1) {
                                            $btn .= '<a class="dropdown-item text-blue publish-data" href="#" action="quiz/' . $row->id . '/publish"> Publish</a>';
                                        }

                                        if (auth()->user()->can('quiz-active') && $row->status == 2) {
                                            $btn .= '<a class="dropdown-item text-blue deactive-data" href="#" action="quiz/' . $row->id . '/active">Deactivate</a>';
                                        }

                                        if (auth()->user()->can('quiz-active') && $row->status == 3) {
                                            $btn .= '<a class="dropdown-item text-blue active-data" href="#" action="quiz/' . $row->id . '/active">Activate</a>';
                                        }

                                        if (auth()->user()->can('quiz-view')) {
                                            $btn .= '<a class="dropdown-item text-blue view-data" href="quiz/' . $row->id . '"> View Details</a>';
                                        }

                                        if (auth()->user()->can('quiz-delete')&& $row->status == "1") {
                                            $btn .= '<a class="dropdown-item text-blue delete-data" href="#" action="/quiz/' . $row->id . '">Delete</a>';
                                        }

                                        $btn .= '</div>';
                                    }
                                    return $btn;
                                })
                                ->addColumn('status', function ($row) {
                                    return "<span class='status_" . config("web_constant.status.$row->status") . "'>" . config("web_constant.status.$row->status") . "</span>";
                                })
                                ->addColumn('quiz_type', function ($row) {
                                    return config("web_constant.quiz_types.$row->quiz_type");
                                })
                                ->addColumn('published_at', function ($row) {
                                    return $row->published_at == null ? $row->published_at : date('d/m/Y', strtotime($row->published_at));
                                })
                                ->rawColumns(['action', 'status', 'quiz_type', 'published_at'])
                                ->make(true);
        }

        return view('quiz.index', [
            'create_url' => 'quiz/create',
            'create_permission' => 'quiz-create',
            'keyword' => 'quiz',
            'title' => 'Quiz Lists'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $quiz = new Quiz();
        $content = new Article();

        return view('quiz.create', [
            'quiz' => $quiz,
            'content' => $content,
            'title' => 'Create New Quiz',
            'list_url' => 'quiz',
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
        $request->merge([
            'option_name' => array_map(function($arr){ return  explode(',', $arr);},explode(',&&&,', $request->option_name)) ,
            'is_correct' =>  array_map(function($arr){ return  explode(',', $arr);},explode(',&&&,', $request->is_correct)) ,
            'option_id' =>  array_map(function($arr){ return  explode(',', $arr);},explode(',&&&,', $request->option_id)) ,
            'quiz_type' => explode(',', $request->quiz_type) ,
        ]);

        $validator = Validator::make($request->all(),[
                                    'question.*' => $request->content_type==2 ?  'required|max:260' : 'required',
                                    'answer_description.*' => 'nullable',
                                    // 'select_topic_id'=> 'required',
                                    'content_type' => 'required',
                                    'option_name.*.*' => 'required|max:25',
                                    'article_id' => ($request->content_type == 2) ?  'required' : 'nullable',
                                ],[
                                    'question.*.required' => 'The quiz question field is required..' ,
                                    'content_type.required' => 'The category field is required.' ,
                                    'option_name.*.*.required' => 'The quiz option field is required.',
                                    'article_id.required' => 'The article is required.',
                                    'question.*.max' => 'The quiz question may not be greater than 260 characters.',
                                    'option_name.*.*.max' => 'The quiz option may not be greater than 25 characters.'
                                ]);

        if ($validator->fails()) {
            return response($validator->messages(), 422);
        }

        $result = true;
        DB::beginTransaction();
        try {
            for ($i=0; $i < count($request->quiz_type) ; $i++) {
                $data = [];
                $data['created_by'] = Auth::id();
                $data['status'] = 1;
                $data['question'] = $request->question[$i] ?? ' ';
                $data['quiz_type'] = $request->quiz_type[$i];
                $data['content_type'] = $request->content_type;
                $data['answer_description'] = $request->answer_description[$i];

                if($request->content_type==2)
                  $data['course_id'] = $request->course_id;

                $quiz = Quiz::create($data);

                if (!$quiz) {
                    $result = false;
                    DB::rollback();
                    break;
                }

                if ($result) {
                    if (!Quiz::saveOption($request->option_name[$i],$request->is_correct[$i],$request->option_id[$i], $quiz->id)) {
                        $result = false;
                        DB::rollback();
                        break;
                    }
                }

                if($result && $request->content_type==2){
                    if(!Quiz::saveArticleQuiz($request->all(),$quiz->id)){
                        $result = false;
                        DB::rollback();
                        break;
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => 'Quiz was created successfully!']);
        } else {
            session(['error' => 'Quiz can not create!']);
        }

        return json_encode(['success' => $result, 'index' => $request->current_index, 'type' => $request->content_type]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Quiz $quiz)
    {
        $quiz = Quiz::where('quizs.id', $quiz->id)
                    ->leftJoin('users as createdUsers', 'createdUsers.id', 'quizs.created_by')
                    ->leftJoin('users as updatedUsers', 'updatedUsers.id', 'quizs.updated_by')
                    ->leftJoin('users as publishedUsers', 'publishedUsers.id', 'quizs.published_by')
                    ->leftJoin('users as deactivatedUsers', 'deactivatedUsers.id', 'quizs.deactivated_by')
                    ->with([
                        'articles' => function($q){
                            $q->leftJoin('articles','articles.id','article_quiz.article_id')
                              ->select('article_quiz.*','articles.title');
                        }
                    ])
                    ->first([
                        'createdUsers.user_name as created_user_name',
                        'updatedUsers.user_name as updated_user_name',
                        'publishedUsers.user_name as published_user_name',
                        'deactivatedUsers.user_name as deactivated_user_name',
                        'quizs.*',
                    ]);

        return view('quiz.view', [
            'quiz' => $quiz,
            'title' => 'Quiz Detail',
            'list_url' => 'quiz',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function edit(Quiz $quiz, Request $request)
    {
        $quiz = Quiz::where('quizs.id',$quiz->id)
                           ->with([
                              'articles' => function($q){
                                $q->leftJoin('articles','articles.id','article_quiz.article_id')
                                  ->select('article_quiz.*','articles.title');
                              }
                           ])
                           ->first();

        $quiz->current_index = $request->index;

        $content = new Article();

        return view('quiz.update', [
            'quiz' => $quiz,
            'content' => $content,
            'title' => 'Update Quiz',
            'list_url' => 'quiz',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quiz $quiz)
    {
        $request->merge([
            'option_name' => array_map(function($arr){ return  explode(',', $arr);},explode(',&&&,', $request->option_name)) ,
            'is_correct' =>  array_map(function($arr){ return  explode(',', $arr);},explode(',&&&,', $request->is_correct)) ,
            'option_id' =>  array_map(function($arr){ return  explode(',', $arr);},explode(',&&&,', $request->option_id)) ,
            'quiz_type' => explode(',', $request->quiz_type) ,
        ]);

        $validator = Validator::make($request->all(),[
                                    'question.*' => $request->content_type==2 ?  'required|max:260' : 'required',
                                    'answer_description.*' => 'nullable',
                                    // 'select_topic_id'=> 'required',
                                    'content_type' => 'required',
                                    'option_name.*.*' => 'required|max:25',
                                    'article_id' => ($request->content_type == 2) ?  'required' : 'nullable',
                                ],[
                                    'question.*.required' => 'The quiz question field is required..' ,
                                    'content_type.required' => 'The category field is required.' ,
                                    'option_name.*.*.required' => 'The quiz option field is required.',
                                    'article_id.required' => 'The article is required.',
                                    'question.*.max' => 'The quiz question may not be greater than 260 characters.',
                                    'option_name.*.*.max' => 'The quiz option may not be greater than 25 characters.'
                                ]);

        if ($validator->fails()) {
            return response($validator->messages(), 422);
        }

        $result = true;
        DB::beginTransaction();
        try {
            for ($i=0; $i < count($request->quiz_type) ; $i++) {
                $data = [];
                $data['updated_by'] = Auth::id();
                $data['question'] = $request->question[$i] ?? ' ';
                $data['quiz_type'] = $request->quiz_type[$i];
                $data['content_type'] = $request->content_type;
                $data['answer_description'] = $request->answer_description[$i];

                // if($request->content_type==2)
                //    $data['course_id'] = $request->course_id[$i];

                $ans = $quiz->update($data);

                if (!$ans) {
                    $result = false;
                    DB::rollback();
                    break;
                }

                if ($result) {
                    $old_options = array_filter($request->option_id[$i]);
                    QuizOption::where('quiz_id', $quiz->id)->whereNotIn('id', $old_options)->forceDelete();
                    if (!Quiz::saveOption($request->option_name[$i],$request->is_correct[$i],$request->option_id[$i], $quiz->id)) {
                        $result = false;
                        DB::rollback();
                        break;
                    }
                }

                if($result && $request->content_type==2){
                    ArticleQuiz::where('quiz_id', $quiz->id)->forceDelete();
                    if(!Quiz::saveArticleQuiz($request->all(),$quiz->id)){
                        $result = false;
                        DB::rollback();
                        break;
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($result) {
            session(['success' => 'Quiz was updated successfully!']);
        } else {
            session(['error' => 'Quiz can not update!']);
        }

        return json_encode(['success' => $result, 'index' => $request->current_index, 'type' => $request->content_type]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz, Request $request)
    {
        $result = true;
        DB::beginTransaction();
        try {

            $ans = Quiz::where('id', $quiz->id)

                ->update([
                    'deleted_by' => Auth::id(),
                ]);
            ArticleQuiz::where('quiz_id', $quiz->id)->delete();

            if (!$ans) {
                $result = false;
                DB::rollback();
            }

            if (!$quiz->delete()) {
                $result = false;
                DB::rollback();
            }

            DB::commit();
        } catch (\Throwable $th) {
            dd($th);
        }

        if ($ans) {
            session(['success' => "Quiz was deleted successfully!"]);
        } else {
            session(['error' => "Quiz can not delete!"]);
        }

        return redirect('quiz?index=' . $request->current_index . "&type=" . $quiz->content_type);
    }

    public function publish(Request $request, $id)
    {
        $quiz = Quiz::find($id);
        $ans = Quiz::where('id', $id)
                    ->update([
                        'status' => 2,
                        'published_by' => Auth::id(),
                        'published_at' => date('Y-m-d H:i:s'),
                    ]);

        if ($ans) {
            session(['success' => 'Quiz was published successfully!']);
        } else {
            session(['error' => 'Quiz can not publish!']);
        }

        return redirect('quiz?index=' . $request->current_index . "&type=" . $quiz->content_type);
    }

    public function active(Request $request, $id)
    {
        $quiz = Quiz::find($id);
        $status = null;

        if ($quiz->status == 2) {
            $status = 3;
            $msg1 = "deactivated";
            $msg2 = "deactive";
        } elseif ($quiz->status == 3) {
            $status = 2;
            $msg1 = "activated";
            $msg2 = "active";
        }

        $ans = Quiz::where('id', $id)
            ->update([
                'status' => $status,
                'deactivated_by' => Auth::id(),
                'deactivated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($ans) {
            session(['success' => "Quiz was $msg1 successfully!"]);
        } else {
            session(['error' => "Quiz can not $msg2!"]);
        }

        return redirect('quiz?index=' . $request->current_index . "&type=" . $quiz->content_type);
    }
}
