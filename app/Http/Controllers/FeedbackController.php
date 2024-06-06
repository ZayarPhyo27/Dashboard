<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    protected $is_admin;

    public function __construct()
    {
        $this->middleware('role_or_permission:feedback-list', ['only' => ['index']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $feedbacks = new Feedback();
         if($request->ajax()){

            if($request->from_date){
                $feedbacks = $feedbacks->where('feedbacks.created_at', '>=', $request->from_date);

            }
            if($request->to_date){
                $feedbacks = $feedbacks->where('feedbacks.created_at', '<=', $request->to_date);
            }

            $feedback_rating = $feedbacks->get();

            $feedback_avg_ratings = $feedbacks->selectRaw('ROUND(AVG(feedbacks.app_feedback), 2) as average_app_feedback,
                                                  ROUND(AVG(feedbacks.game_feedback), 2) as average_game_feedback,
                                                  COUNT(NULLIF(feedbacks.app_feedback, 0)) as total_app_feedback,
                                                  COUNT(NULLIF(feedbacks.game_feedback, 0)) as total_game_feedback')
                                                  ->first();
            $result = [
                "feedback_rating" => $feedback_rating,
                "feedback_avg" => $feedback_avg_ratings,
                ];

            return json_encode($result);
         }
         else{
            $feedback_avg_ratings = $feedbacks->selectRaw('ROUND(AVG(feedbacks.app_feedback), 2) as average_app_feedback,
                                                  ROUND(AVG(feedbacks.game_feedback), 2) as average_game_feedback,
                                                  COUNT(NULLIF(feedbacks.app_feedback, 0)) as total_app_feedback,
                                                  COUNT(NULLIF(feedbacks.game_feedback, 0)) as total_game_feedback')
                                            ->first();

            $feedbacks = $feedbacks->latest()->get();

                return view('feedback.index', [
                    'title' => 'Feedback Lists',
                    'route' => 'feedback',
                    'keyword' => 'feedback',
                    'feedback' => $feedbacks,
                    'feedback_avg_ratings' => $feedback_avg_ratings
                ]);
        }

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
    }
}
