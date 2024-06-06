@extends('adminlte::page')

@section('title', 'Podcast | Dashboard')
@section('content_header')
    @include('content_header')
@stop

@section('content')
<form method="POST" class="data-form" id="podcast_create" action="{{ route('podcast.index') }}" >
    @csrf
    @include('podcast.form')
</form>
@stop


