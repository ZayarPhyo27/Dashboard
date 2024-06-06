<?php

namespace App\Models;

use App\Models\QuizOption;
use App\Models\ArticleQuiz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $table = "quizs";
    
    protected $hidden = ['pivot'];

    protected  $fillable = [
        'question',
        'quiz_type',
        'content_type',
        'status',
        'answer_description',
        'deleted_by',
        'created_by',
        'updated_by',
        'published_by',
        'published_at'
    ];

    public function options()
    {
        return $this->hasMany(QuizOption::Class, 'quiz_id');
    }

    public function articles()
    {
        return $this->hasMany(ArticleQuiz::Class,'quiz_id');
    }

    public static function saveOption($option_name,$corrects,$option_id,$quiz_id){
        $ans = true;
        for ($i=0; $i < count($option_name); $i++) {
            if($option_id[$i]==null)
                $option = QuizOption::create([
                                         'quiz_id' => $quiz_id,
                                         'option_name' => $option_name[$i],
                                         'is_correct' => $corrects[$i],
                                    ]);
            else $option = QuizOption::where('id',$option_id[$i])
                                           ->update([
                                                'option_name' => $option_name[$i],
                                                'is_correct' => $corrects[$i],
                                           ]);

            if(!$option){
                $ans = false;
                break;
            }
        }

        return $ans;
    }

    public static function saveArticleQuiz($data,$quiz_id)
    {

        $result = true;
        for ($i=0; $i < count($data['article_id']) ; $i++) {
            $ans = ArticleQuiz::create([
                                 'quiz_id' => $quiz_id,
                                 'article_id' => $data['article_id'][$i]
                  ]);

            if(!$ans){
                $result = false;
                break;
            }
        }

        return $result;

    }
}
