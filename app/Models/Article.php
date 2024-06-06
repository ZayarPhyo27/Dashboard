<?php

namespace App\Models;
use App\Models\ArticleDetail;
use App\Models\ArticleQuiz;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'title','category_id','cover_photo','status','created_by','updated_by','deleted_by','published_by','published_at'
    ];

    public $timestamps = true;

    public function details()
    {
        return $this->hasMany(ArticleDetail::Class,'article_id');
    }

    public function description()
    {
        return $this->hasMany(ArticleDetail::Class,'article_id');
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }
    public function quizs()
    {
        return $this->hasMany(ArticleQuiz::Class,'article_id');
    }
    public static function saveDetail($data,$article_id)
    {
        $result = true;
        for ($i=0; $i < count($data['detail_description']) ; $i++) { 
            
            if($data['detail_virtual_img'][$i]==null && isset($data['photo_path'][$i])){
                $photo = Storage::disk('s3')->put('images', $data['photo_path'][$i]);
                $file[]= $data['photo_path'][$i];
                $photo_path = Storage::disk('s3')->url($photo);
            }else{
                $photo_path = $data['detail_virtual_img'][$i];
            }
 
            if($data['detail_description'][$i]!=null){
      $ans = ArticleDetail::create([
                                    'article_id' => $article_id,
                                    'photo_path' =>  $photo_path,
                                    'detail_description' => $data['detail_description'][$i]
                    ]);

                if(!$ans){
                    $result = false;
                    break;
                }
            }
       
        }
      

        return $result;
        
    }

}
