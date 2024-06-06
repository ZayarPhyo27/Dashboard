@extends('adminlte::page')

@section('title', 'Comic | Dashboard')
@section('content_header')
    @include('content_header')
@stop

@section('content')
<form method="POST" class="data-form" id="comic_create" action="{{ route('comic.index') }}" >
    @csrf
    @include('comic.form')
</form>
@stop


