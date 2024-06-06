@extends('adminlte::page')

@section('title', 'User | Dashboard')

@section('content_header')
    @include('content_header')
@stop
@section('content')


            <table class="table  table-borderless table-hover table-striped ">
                    <tr><th>{{__('labels.user_name')}}</th><td>{{$user->user_name}}</td></tr>
                    <tr><th>{{__('labels.email')}}</th><td>{{$user->email}}</td></tr>
                    <tr>
                        <th>{{__('labels.user_role')}}</th>
                        <td>
                          {{ $user->role_name}}
                        </td>
                    </tr>

                    <tr><th>{{__('labels.created_by')}}</th>
                        <td>
                            @php
                                $name = \App\Models\User::where('id',$user->created_by)->select('user_name')->first();
                            @endphp
                            @if($name)
                                {{ $name->user_name }}
                            @else
                                -
                            @endif

                        </td></tr>
                    <tr><th>{{__('labels.created_at')}}</th><td>{{ date('d/m/Y H:i:s', strtotime($user->created_at)) }}</td></tr>

                    @php
                        $name = \App\Models\User::where('id',$user->updated_by)->select('user_name')->first();
                    @endphp
                    @if ($name)
                        <tr><th>{{__('labels.updated_by')}}</th>
                            <td>
                                {{ $name->user_name }}
                            </td>
                        </tr>
                    @endif

                    @if ($user->updated_at)
                        <tr><th>{{__('labels.updated_at')}}</th><td>{{ date('d/m/Y H:i:s', strtotime($user->updated_at)) }}</td></tr>
                    @endif

            </table>


            @include('back-btn')


<script>
</script>
@endsection
