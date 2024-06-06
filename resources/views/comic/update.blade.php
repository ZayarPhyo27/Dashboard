@extends('adminlte::page')

@section('title', 'Comic | Dashboard')

@section('content_header')
    @include('content_header')
@stop

@section('content')
    <form method="POST" class="data-form" action="{{ url('comic/'.$comic->id) }}" id="comic_create">
        @csrf
        @method('PUT')
        @include('comic.form')
    </form>
@stop
