@extends('adminlte::page')

@section('title', 'Podcast | Dashboard')
@section('content_header')
@include('content_header')
@stop


@php
    $created_user_name = $podcast->created_user_name;
    $updated_user_name = $podcast->updated_user_name;
    $published_user_name = $podcast->published_user_name;
    $deactivated_user_name = $podcast->deactivated_user_name;
    $created_at = date('d-M-Y H:i:s',strtotime($podcast->created_at));
    $updated_at = $podcast->updated_by==null ? '' : date('d-M-Y H:i:s',strtotime($podcast->updated_at));
    $published_at = $podcast->published_at==null ? '' : date('d-M-Y H:i:s',strtotime($podcast->published_at));
    $deactivated_at = $podcast->deactivated_at==null ? '' : date('d-M-Y H:i:s',strtotime($podcast->deactivated_at));
@endphp
@section('content')

<table class="table  table-bordered detail-table table-hover">

        <tr>
            <td clas="fw-bold"><b>Title </b></td>
            <td>{{ $podcast->title }}</td>
        </tr>
        <tr>
            <td clas="fw-bold"><b>Category </b></td>
            <td> {{ config('web_constant.category_type.'. $podcast->category_id)  }}</td>
        </tr>
        <tr>
            <td clas="fw-bold"><b>Cover Photo </b></td>
            <td>
                @if ($podcast->cover_photo <> null)
                    <img src="{{ $podcast->cover_photo}}" alt="Cover" style="width: auto; height: 100px;">
                    @else
                    -
                    @endif
            </td>
        </tr>
        <tr>
            <td clas="fw-bold"><b>Audio File </b></td>
            <td>
                @if ($podcast->audio_path <> null)

                    <audio controls>
                    <source src="{{$podcast->audio_path}}" type="audio/mpeg">
                     </audio>
                    @else
                    -
                    @endif
            </td>        </tr>

        <tr>
            <td><b>Duration ( min) </b></td>
            <td>{{ $podcast->duration }}</td>
        </tr>
        <tr><th>{{__('labels.download_size')}}</th>
            <td>
                {{ $podcast->download_size}}
            </td>
        </tr>
        <tr>
            <th>Status</th>
            <td><span class="status_{{config("web_constant.status.$podcast->status")}}">{{config("web_constant.status.$podcast->status")}}</span>
            </td>
        </tr>


        <tr>
            <td><b>Created By </b></td>
            <td> {{ $created_user_name }}</td>
        </tr>
        <tr>
            <td><b>Created At</b></td>
            <td> {{ $created_at }}</td>
        </tr>

        @if ($updated_user_name)
        <tr>
            <td><b>Updated By</b></td>
            <td> {{ $updated_user_name }}</td>
        </tr>
        @endif

        @if ($updated_at)
        <tr>
            <td><b>Updated At</b></td>
            <td> {{ $updated_at }}</td>
        </tr>
        @endif

        @if ($published_user_name)
        <tr>
            <td><b>Published By</b></td>
            <td> {{ $published_user_name }}</td>
        </tr>
        @endif

        @if ($published_at)
        <tr>
            <td><b>Published At</b></td>
            <td> {{ $published_at }}</td>
        </tr>
        @endif

        @if ($deactivated_user_name)
        <tr>
            <td><b>Deactivated By</b></td>
            <td> {{ $deactivated_user_name }}</td>
        </tr>
        @endif

        @if ($deactivated_at)
        <tr>
            <td><b>Deactivated At</b></td>
            <td> {{ $deactivated_at }}</td>
        </tr>
        @endif
</table>

@include('back-btn')





@endsection




