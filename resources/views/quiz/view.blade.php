@extends('adminlte::page')

@section('title', 'Quiz | Dashboard')

@section('content_header')
    @include('content_header')
@stop

@section('content')
<div class="">
     @php
         $created_user_name = $quiz->created_user_name;
         $updated_user_name = $quiz->updated_user_name;
         $published_user_name = $quiz->published_user_name;
         $deactivated_user_name = $quiz->deactivated_user_name;

         $created_at = date('d-M-Y H:i:s',strtotime($quiz->created_at));
         $updated_at = $quiz->updated_by==null ? '' : date('d-M-Y H:i:s',strtotime($quiz->updated_at));
         $published_at = $quiz->published_at==null ? '' : date('d-M-Y H:i:s',strtotime($quiz->published_at));
         $deactivated_at = $quiz->deactivated_at==null ? '' : date('d-M-Y H:i:s',strtotime($quiz->deactivated_at));

     @endphp

     <div class="row">
        <div class="col-md-12">
        <table class="detail-table quiz" style="margin-bottom: 30px;">
               <tbody>

               <tr>
                <th>Category</th>
                <td>
                     <span>{{config("web_constant.quiz_content_types.$quiz->content_type")}}</span>
                </td>
              </tr>

              <tr>
                <th>Quiz Type</th>
                <td><span>{{config("web_constant.quiz_types.$quiz->quiz_type")}}</span></td>
              </tr>

              <tr>
                <th>Status</th>
                <td><span class="status_{{config("web_constant.status.$quiz->status")}}">{{config("web_constant.status.$quiz->status")}}</span>
                </td>
              </tr>

              <tr>
                <th>Answer Description</th>
                <td><span>{{$quiz->answer_description}}</span></td>
              </tr>

              <tr>
                <th>Quiz Question</th>
                <td><span>{{$quiz->question}}</span></td>
              </tr>

              <tr>
                <th>Options</th>
                <td>
                @foreach ($quiz->options as $opt)
                    <div class="row">
                        <div class="col-md-12">
                            @if($quiz->quiz_type==1 || $quiz->quiz_type==2)
                            <input type="radio" @if($opt->is_correct) checked @endif disabled>
                            @else
                            <input type="checkbox" @if($opt->is_correct) checked @endif disabled>
                            @endif
                            {{$opt->option_name}}
                        </div>
                    </div><br>
                @endforeach
                </td>
              </tr>


               </tbody>
            </table>
        </div>
        </div>


     @if($quiz->content_type==2)
        <div class="row " >

            <div class="col-md-12">
                <h5>Article Lists</h5>
                <span class="invalid-feedback" role="alert">
                    <strong></strong>
                </span>
                <table class="table data-table table-bordered faq-table">
                    <thead>
                        <th style="width: 50px;">No.</th>
                        <th>Article</th>
                    </thead>
                    <tbody>
                        @if ($quiz->articles->count() > 0)
                            @foreach ($quiz->articles as $k => $content)
                            <tr key="{{$content->article_id}}">
                                <td><span class="faq-index">{{$k+1}}</span></td>
                                <td>{{$content->title}}</td>
                            </tr>
                            @endforeach
                        @endif

                    </tbody>

                </table>
            </div>
        </div><br>
    @endif

     @include('view-detail')
</div>
@stop
