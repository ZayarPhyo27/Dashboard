@extends('adminlte::page')

@section('title', 'Notification')

@section('content_header')
@include('content_header')
@stop

@section('content')


<table class="detail-table">
<tr>
                <th>Title</th>
                <td>{{ $notification->title}}</td>
            </tr>
           
            
            <tr>
                <th>Description</th>
                <td>
                    {{ $notification->description}}
                </td>
            </tr>

            @if($notification->created_user_name!=null)
            <tr>
                <th> Created By </th>
                <td>{{ $notification->created_user_name}} </td>
            </tr>            
            @endif

            <tr>
                <th>Created At</th>
                <td>
                {{ date('d/m/Y H:i:s', strtotime( $notification->created_at)) }}
                </td>
            </tr>

            @if($notification->updated_user_name!=null)
            <tr>
                <th> Updated By </th>
                <td>{{ $notification->updated_user_name}} </td>
            </tr>            
            @endif

            <tr>
                <th>Updated At</th>
                <td>
                {{ date('d/m/Y H:i:s', strtotime( $notification->updated_at)) }}
                </td>
            </tr>

            @if($notification->pushed_user_name!=null)
            <tr>
                <th> Pushed By </th>
                <td>{{ $notification->pushed_user_name}} </td>
            </tr>            
            @endif

            @if($notification->pushed_at!=null)
            <tr>
                <th> Pushed At </th>
                <td>{{ date('d/m/Y H:i:s', strtotime( $notification->pushed_at)) }} </td>
            </tr>            
            @endif
           

            @if($notification->deleted_at!=null)
                <tr><th>{{__('Deleted By')}} </th><td>{{ $notification->deleted_user }}</td></tr>
                <tr><th>{{__('Deleted At')}} </th><td>{{ date('d/m/Y H:i:s', strtotime( $notification->deleted_at)) }}</td></tr>
            
            @endif
    </table>
    @stop