@extends('adminlte::page')

@section('title', 'Video | Dashboard')

@section('content_header')
    @include('content_header')
@stop

@section('content')
<form method="POST" class="data-form" id="video-form" action="{{ url('video') }}">
    @csrf
    @include('video.form')
</form>
@stop
