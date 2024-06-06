@extends('adminlte::page')
@section('title', 'Article | Dashboard')
@section('content_header')
    @include('content_header')
@stop

@section('content')
<style>
    a.q-detail{
        color: black!important;
    }

</style>
<div>
     @php
         $created_user_name = $article->created_user_name;
         $updated_user_name = $article->updated_user_name;
         $published_user_name = $article->published_user_name;
         $deactivated_user_name = $article->deactivated_user_name;
         $created_at = date('d-M-Y H:i:s',strtotime($article->created_at));
         $updated_at = $article->updated_by==null ? '' : date('d-M-Y H:i:s',strtotime($article->updated_at));
         $published_at = $article->published_at==null ? '' : date('d-M-Y H:i:s',strtotime($article->published_at));
         $deactivated_at = $article->deactivated_at==null ? '' : date('d-M-Y H:i:s',strtotime($article->deactivated_at));

     @endphp


     <div class="row">
        <div class="col-md-12">
        <table class="detail-table" style="margin-bottom: 30px;">
               <tbody>

               <tr>
                <th>Title</th>
                <td>{{ $article->title}}</td>
              </tr>

              <tr>
                <th>Category</th>
                <td>{{config("web_constant.category_type.$article->category_id")}}</td>
              </tr>

              <tr>
                <th>Status</th>
                <td><span class="status_{{config("web_constant.status.$article->status")}}">{{config("web_constant.status.$article->status")}}</span>
                </td>
              </tr>

              <tr>
                <th>Cover Photo</th>
                <td><img src="{{$article->cover_photo}}" alt="" width="100" height="auto" style="object-fit: contain;">
                </td>
              </tr>

              <tr>
                <th colspan="2">Article Detail</th>
              </tr>





                  @foreach ($article->details as $k => $detail)

                      <tr>
                      <td>
                            {!! $detail->detail_description !!}
                          </td>

                          <td>@if($detail->photo_path!=null) <img src="{{$detail->photo_path}}" alt="" width="100" height="auto"> @endif</td>

                      </tr>
                  @endforeach

                    @if($article->created_user_name!=null)
                    <tr>
                        <th> Created By </th>
                        <td>{{ $article->created_user_name}} </td>
                    </tr>
                    @endif

                    @if($created_at!=null)
                    <tr>
                        <th> Created At</th>
                        <td>{{$created_at}} </td>
                    </tr>
                    @endif

                    @if($article->updated_user_name!=null)
                    <tr>
                        <th> Updated By </th>
                        <td>{{ $article->updated_user_name}} </td>
                    </tr>
                    @endif

                    @if($updated_at!=null)
                    <tr>
                        <th> Updated At</th>
                        <td>{{$updated_at}} </td>
                    </tr>
                    @endif

                    @if($article->published_user_name!=null)
                    <tr>
                        <th> Published By </th>
                        <td>{{ $article->published_user_name}} </td>
                    </tr>
                    @endif


                    @if($published_at!=null)
                    <tr>
                        <th> Published At</th>
                        <td>{{$published_at}} </td>
                    </tr>
                    @endif
                    @if($deactivated_user_name!=null)
                    <tr>
                        <th> Deactivated By </th>
                        <td>{{ $deactivated_user_name}} </td>
                    </tr>
                    @endif


                    @if($deactivated_at!=null)
                    <tr>
                        <th> Deactivated At</th>
                        <td>{{$deactivated_at}} </td>
                    </tr>
                    @endif

               </tbody>
            </table>
        </div>
        </div>
        <div class="row" style="padding-bottom:30px;">
            <div class="col-sm-12">
                <h5>Quiz Lists</h5><br>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="width : 50px;">No.</th>
                            <th>Quiz Question</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($article->quizs as $k => $quiz)
                            <tr>
                                <td>{{$k+1}}</td>
                                <td><a class="q-detail" href="{{url('quiz/'.$quiz->quiz_id)}}">{{$quiz->question}}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>

@stop
