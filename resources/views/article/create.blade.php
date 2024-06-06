@extends('adminlte::page')
@section('title', 'Article | Dashboard')
@section('content_header')
    @include('content_header')
@stop

@section('content')

    <form method="POST" class="data-form" action="{{ url('article') }}" id="article-form" enctype="multipart/form-data">
        @csrf
        @include('article.form')
    </form>
@stop
