@extends('adminlte::page')
@section('title', 'Dashboard')
@section('content_header')
@include('content_header')
@stop

@section('content')
<form method="POST" action="{{ route('notification.index') }}" enctype="multipart/form-data" id="notification_create" >
        @csrf
        @include('notification.form')
    </form>
@stop



