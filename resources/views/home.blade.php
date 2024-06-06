@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

    {{-- @php
        dd($user = auth()->user()->roles)
    @endphp --}}
    @if(auth()->user()->can('publish articles') )
        <h1 class="m-0 text-dark">Dashboard</h1>
    @endif

@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="mb-0">You are logged in!</p>
                </div>
            </div>
        </div>
    </div>
@stop
