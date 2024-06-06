<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Comic;
use App\Models\Podcast;
use App\Models\Quiz;
use App\Models\Video;
use DB;

class ContentService
{

    protected $namespace = 'App\Services';

    public function getArticles( $expression )
    {
        $articles = Article::where('status', config('constant.status.Active'))
            ->where('created_at', $expression, config('constant.application_launch_date'))
            ->with([
                'description' => function ($q) {
                    $q->select([
                        'article_id',
                        'detail_description as text',
                        'photo_path as image',
                    ]);
                },

                'quizzes' => function ($q) {
                    $q->where('status', config('constant.status.Active'))
                        ->where('content_type', config('constant.quiz_content_types.Article'))
                        ->with('options:quiz_id,id,option_name as name,is_correct')
                        ->select([
                            'quizs.id',
                            'quiz_type',
                            'question',
                            'answer_description'
                        ]);
                }
            ])
            ->select([
                'id',
                'title',
                'category_id',
                'cover_photo as cover_image'
            ])
            ->get()
            ->toArray();

        for ($i = 0; $i < count($articles); $i++) {

            $articles[$i]['cover_image'] = base64_encode(file_get_contents($articles[$i]['cover_image']));

            for ($j = 0; $j < count($articles[$i]['description']); $j++) {

                if ( !( $articles[$i]['description'][$j]['image'] == null || $articles[$i]['description'][$j]['image'] == null ))
                    $articles[$i]['description'][$j]['image'] = base64_encode(file_get_contents($articles[$i]['description'][$j]['image']));
            }
        }

        return $articles;
    }

    public function getComics( $expression )
    {

        $comics =  Comic::where('status', config('constant.status.Active'))
            ->where('created_at', $expression, config('constant.application_launch_date'))
            ->select([
                'id',
                'title',
                'category_id',
                'cover_photo as thumbnail',
                'download_size', 
                'pdf_path as url'
            ])
            ->get()
            ->toArray();

        for ($i = 0; $i < count($comics); $i++) {

            if (! ( $comics[$i]['thumbnail'] == null || $comics[$i]['thumbnail'] == '' ))
                $comics[$i]['thumbnail'] = base64_encode(file_get_contents($comics[$i]['thumbnail']));
        }

        return $comics;
    }

    public function getPodcasts( $expression )
    {

        $podcasts = Podcast::where('status', config('constant.status.Active'))
            ->where('created_at', $expression, config('constant.application_launch_date'))
            ->select([
                'id',
                'title',
                'category_id',
                'duration',
                'cover_photo as cover_image',
                'download_size',
                'audio_path as url'
            ])
            ->get()
            ->toArray();


        for ($i = 0; $i < count($podcasts); $i++) {

            if ( !( $podcasts[$i]['cover_image'] == null || $podcasts[$i]['cover_image'] == '' ) )
                $podcasts[$i]['cover_image'] = base64_encode(file_get_contents($podcasts[$i]['cover_image']));
        }

        return $podcasts;
    }

    public function getVideos( $expression )
    {

        $videos = Video::where('status', config('constant.status.Active'))
            ->where('created_at', $expression, config('constant.application_launch_date'))
            ->select([
                'id',
                'title',
                'category_id',
                'duration',
                'cover_photo as thumbnail',
                'download_size',
                'video_path as url'
            ])
            ->get()
            ->toArray();


        for ($i = 0; $i < count($videos); $i++) {

            if (! ($videos[$i]['thumbnail'] == null || $videos[$i]['thumbnail'] == '') )
                $videos[$i]['thumbnail'] = base64_encode(file_get_contents($videos[$i]['thumbnail']));
        }

        return $videos;
    }
}
