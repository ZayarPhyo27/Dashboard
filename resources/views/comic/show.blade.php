@extends('adminlte::page')

@section('title', 'Comic | Dashboard')
@section('content_header')
@include('content_header')
@stop

@php
    $created_user_name = $comic->created_user_name;
    $updated_user_name = $comic->updated_user_name;
    $published_user_name = $comic->published_user_name;
    $deactivated_user_name = $comic->deactivated_user_name;
    $created_at = date('d-M-Y H:i:s',strtotime($comic->created_at));
    $updated_at = $comic->updated_by==null ? '' : date('d-M-Y H:i:s',strtotime($comic->updated_at));
    $published_at = $comic->published_at==null ? '' : date('d-M-Y H:i:s',strtotime($comic->published_at));
    $deactivated_at = $comic->deactivated_at==null ? '' : date('d-M-Y H:i:s',strtotime($comic->deactivated_at));
@endphp
@section('content')

<table class="table  table-bordered detail-table table-hover">

        <tr>
            <td clas="fw-bold"><b>Title </b></td>
            <td>{{ $comic->title }}</td>
        </tr>
        <tr>
            <td clas="fw-bold"><b>Category </b></td>
            <td> {{ config('web_constant.category_type.'. $comic->category_id)  }}</td>
        </tr>
        <tr>
            <td clas="fw-bold"><b>Cover Photo </b></td>
            <td>
                @if ($comic->cover_photo <> null)
                    <img src="{{$comic->cover_photo}}" alt="Cover" style="width: auto; height: 100px;">
                    @else
                    -
                    @endif
            </td>
        </tr>
        <tr>
            <td clas="fw-bold"><b>PDF File </b></td>
            <td>
                @if ($comic->pdf_path <> null)

                     <a href="{{$comic->pdf_path}}" target="_blank">{{asset('pdf/'.$comic->pdf_path)}}</a>


                    @else
                    -
                    @endif
            </td>
        </tr>

        <tr><th>{{__('labels.download_size')}}</th>
            <td>
                {{ $comic->download_size}}
            </td>
        </tr>

        <tr>
            <th>Status</th>
            <td><span class="status_{{config("web_constant.status.$comic->status")}}">{{config("web_constant.status.$comic->status")}}</span>
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




