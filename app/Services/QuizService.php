<?php

namespace App\Services;

use App\Models\Quiz;

class QuizService{

    public function getGameQuizzes(){

        return Quiz::where('status', config('constant.status.Active'))
            ->where('content_type', config('constant.quiz_content_types.Game'))
            ->with('options:quiz_id,option_name,is_correct')
        ->select([
            'id',
            'question',
            'quiz_type',
            'answer_description',
        ])
        ->get();    
            
    }
}

?>