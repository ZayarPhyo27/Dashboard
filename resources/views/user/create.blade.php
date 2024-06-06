@extends('adminlte::page')

@section('title', 'User | Dashboard')

@section('content_header')
    @include('content_header')
@stop

@section('content')
<form method="POST" class="data-form" id="user-form" action="{{ url('user') }}">
    @csrf
    @include('user.form')
</form>
@stop
