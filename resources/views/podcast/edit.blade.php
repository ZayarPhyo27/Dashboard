@extends('adminlte::page')

@section('title', 'Podcast | Dashboard')

@section('content_header')
    @include('content_header')
@stop

@section('content')
    <form method="POST" class="data-form" action="{{ url('podcast/'.$podcast->id) }}" id="podcast_create">
        @csrf
        @method('PUT')
        @include('podcast.form')
    </form>
@stop
