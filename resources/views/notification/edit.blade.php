@extends('adminlte::page')

@section('title', 'Notification Update')

@section('content_header')
@include('content_header')
@stop

@section('content')
<form method="POST" class="data-form"  action="{{ ('notification/'.$notification->id) }}" enctype="multipart/form-data" id="notification_create" >
        @csrf
        @method('PUT')
        @include('notification.form')
    </form>
@stop



