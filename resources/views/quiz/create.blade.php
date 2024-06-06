@extends('adminlte::page')

@section('title', 'Quiz | Dashboard')

@section('content_header')
    @include('content_header')
@stop

@section('content')
<div class="card-body">
    <form method="POST" class="data-form" action="{{ url('quiz') }}" id="quiz-form">
        @csrf
        @include('quiz.form')
    </form>
</div>
@stop
