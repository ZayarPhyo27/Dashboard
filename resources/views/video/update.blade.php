@extends('adminlte::page')

@section('title', 'Video | Dashboard')

@section('content_header')
    @include('content_header')
@stop

@section('content')
<form method="POST" class="data-form" id="video-form" action="{{ url('video/'.$video->id) }}">
    @csrf
    @method('PUT')
    @include('video.form')
</form>
@stop
