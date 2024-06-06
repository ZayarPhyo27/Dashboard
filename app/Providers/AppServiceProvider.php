<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        // URL::forceScheme('https');

        View::share('filter_class', 'col-md-3 col-sm-6 col-xs-12');

        $index = $request->index ?? 1;
        $index = (intval(floor((intval($index) - 1)/10))*10 ) ;

        View::share('upload_photo_description','The uploaded file size must be less than 2MB.');
        View::share('upload_video_description','The file type must be mp4 and less than 20MB.');
        View::share('upload_pdf_description','The file type must be pdf.');
        View::share('quiz_question','Quiz question must be less than 260 characters. Answer Option must be less than 25 characters.');
        View::share('answer_description','Answer description must be less than 200 characters.');
        View::share('duration_format','Min:Sec');
        View::share('download_size','MB');
        View::share('current_index',$index);
        Paginator::useBootstrap();
    }
}
