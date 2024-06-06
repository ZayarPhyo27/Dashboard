@extends('adminlte::page')

@section('title', 'Video | Dashboard')

@section('content_header')
    @include('content_header')
@stop
    @php
        $created_user_name = $video->created_user_name;
        $updated_user_name = $video->updated_user_name;
        $published_user_name = $video->published_user_name;
        $deactivated_user_name = $video->deactivated_user_name;
        $created_at = date('d-M-Y H:i:s',strtotime($video->created_at));
        $updated_at = $video->updated_by==null ? '' : date('d-M-Y H:i:s',strtotime($video->updated_at));
        $published_at = $video->published_at==null ? '' : date('d-M-Y H:i:s',strtotime($video->published_at));
        $deactivated_at = $video->deactivated_at==null ? '' : date('d-M-Y H:i:s',strtotime($video->deactivated_at));
    @endphp
@section('content')

    <table class="table  table-borderless detail-table table-hover table-striped ">
            <tr><th>{{__('labels.title')}}</th><td> {{ $video->title}}</td></tr>
            <tr><th>{{__('labels.category_name')}}</th><td>{{config("web_constant.category_type.$video->category_id")}}</td></tr>
            <tr><th>{{__('labels.cover_photo')}}</th><td> <img src="{{$video->cover_photo}}" alt="" width="80" height="100"></td></tr>
            <tr><th>{{__('labels.video_link')}}</th>
                <td>
                    <a href="{{ $video->video_path}}" target="blank">{{ $video->video_path}}</a>
                </td>
            </tr>
            <tr><th>{{__('labels.duration')}}</th>
                <td>
                    {{ $video->duration}}
                </td>
            </tr>
            <tr><th>{{__('labels.download_size')}}</th>
                <td>
                    {{ $video->download_size}}
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="status_{{config("web_constant.status.$video->status")}}">{{config("web_constant.status.$video->status")}}</span>
                </td>
            </tr>
            <tr><th>{{__('labels.created_by')}}</th><td>{{ $created_user_name }}</td></tr>
            <tr><th>{{__('labels.created_at')}}</th><td>{{ $created_at }}</td></tr>
            @if($video->updated_user_name)
                <tr><th>{{__('labels.updated_by')}}</th><td>{{ $video->updated_user_name }}</td></tr>
            @endif

            @if($updated_at)
                <tr><th>{{__('labels.updated_at')}}</th><td>{{ $updated_at }}</td></tr>
            @endif

            @if($published_user_name)
                <tr><th>{{__('labels.published_by')}}</th><td>{{ $published_user_name  }}</td></tr>
            @endif

            @if($published_at)
                <tr><th>{{__('labels.published_at')}}</th><td>{{ $published_at  }}</td></tr>
            @endif
            @if($deactivated_user_name)
            <tr><th>{{__('labels.deactivated_by')}}</th><td>{{ $deactivated_user_name }}</td></tr>
            @endif

            @if($deactivated_at)
                <tr><th>{{__('labels.deactivated_at')}}</th><td>{{ $deactivated_at }}</td></tr>
            @endif

    </table>

    @include('back-btn')
<script>
</script>
@endsection
