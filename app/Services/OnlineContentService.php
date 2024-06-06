<?php

namespace App\Services;

use App\Models\UserDownloadRecord;

class OnlineContentService
{
    public function saveUserDownloadRecord($request)
    {
        return UserDownloadRecord::create($request);
    }

    public function checkHaveUpdate($user_id)
    {

        $expression = '>';

        if ((count($this->getVideos($expression, $user_id)) > 0) ||
            (count($this->getComics($expression, $user_id)) > 0) ||
            (count($this->getPodcasts($expression, $user_id)) > 0) ||
            (count($this->getArticles($expression, $user_id)) > 0)
        )
            return true;

        return false;
    }

    public function getOnlineContent($user_id)
    {

        $expression = '>=';

        return response()->json([
            'animated_videos' => $this->getVideos($expression, $user_id),
            'comics' => $this->getComics($expression, $user_id),
            'podcasts' => $this->getPodcasts($expression, $user_id),
            'articles' => $this->getArticles($expression, $user_id),
        ]);
    }

    public function getVideos($expression, $user_id)
    {

        $contentService = new ContentService();

        $user_records = UserDownloadRecord::where('user_id', $user_id)->where('content_type', config('constant.content_types.Video'))->get();

        $videos = $contentService->getVideos($expression);

        return $this->removeDownloadedRecord($videos, $user_records);
    }

    public function getComics($expression, $user_id)
    {

        $contentService = new ContentService();

        $user_records = UserDownloadRecord::where('user_id', $user_id)->where('content_type', config('constant.content_types.Comic'))->get();

        $comics = $contentService->getComics($expression);

        return $this->removeDownloadedRecord($comics, $user_records);
    }

    public function getPodcasts($expression, $user_id)
    {

        $contentService = new ContentService();

        $user_records = UserDownloadRecord::where('user_id', $user_id)->where('content_type', config('constant.content_types.Podcast'))->get();

        $podcasts = $contentService->getPodcasts($expression);

        return $this->removeDownloadedRecord($podcasts, $user_records);
    }

    public function getArticles($expression, $user_id)
    {

        $contentService = new ContentService();

        $user_records = UserDownloadRecord::where('user_id', $user_id)->where('content_type', config('constant.content_types.Article'))->get();

        $articles = $contentService->getArticles($expression);

        return $this->removeDownloadedRecord($articles, $user_records);
    }

    public function removeDownloadedRecord($contents, $records)
    {

        if (count($contents) != 0 || count($records) != 0) {

            $contents = array_filter($contents, function ($content) use ($records) {
                foreach ($records as $record) {
                    if ($content['id'] == $record->content_id) {
                        return false;
                    }
                }
                return true;
            });
        }

        return array_values($contents);
    }
}
