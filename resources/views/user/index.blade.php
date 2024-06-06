@extends('adminlte::page')
@section('title', 'User | Dashboard')
@section('content_header')
    @include('content_header')
@stop

@section('content')
<div class="custom-container">
    <div class="row">
        @include('common-search')
        <div class="{{$filter_class}}">
            <select name="role_id" id="selectRole" class="form-control">
                <option value=""></option>
                @foreach (DB::table('roles')->get() as $s)
                    @if ( $s->id != 3)
                        <option value="{{ $s->name }}">{{ $s->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div><br>

        <table class="table table-bordered data-table">
            <thead>
                <tr class="tb-header">
                    <th style="width:10px;">#</th>
                    <th>{{__('labels.user_name')}}</th>
                    <th>{{__('labels.email')}}</th>
                    <th>{{__('labels.user_role')}}</th>
                    {{-- @can('edit-user','delete-user','view-user') --}}
                        <th width="20%">{{__('labels.actions')}}</th>
                    {{-- @endcan --}}
                </tr>
            </thead>
            <tbody>
                {{-- @foreach ($users as $index=>$c) --}}
                    {{-- <td>{{ ++$index }}</td>
                    <td>{{ $c->user_name }}</td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->role_name }}</td> --}}
                    {{-- <td><span class="status-col status_{{ config('web_constant.status.'. $c->status)  }}">{{ config('web_constant.status.'. $c->status)  }}</span></td> --}}
                    {{-- @can('edit-user','delete-user','view-user')
                        <td class="action-col">
                        <form action="{{ URL::to('user/'.$c->id)}}" method="post">
                                @csrf
                                @method('DELETE')

                            @can('edit-user','delete-user','view-user')

                                <a id="" class="nav-link dropdown-toggle black-text actions" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                </a>

                                <div class="dropdown-menu action-list" aria-labelledby="actions">
                                    @can('view-user')
                                        <a class="dropdown-item text-blue productView" href="/user/{{$c->id}}">View Detail</a>
                                    @endcan

                                    @can('edit-user')
                                        <a class="dropdown-item text-blue" href="/user/{{$c->id}}/edit">Edit</a>
                                    @endcan

                                    @can('delete-user')
                                        <a action="/user/{{ $c->id }}" class="dropdown-item text-blue delete-data" href="#">Delete</a>
                                    @endcan


                                </div>
                            @endcan
                        </form>
                    @endcan --}}
                    {{-- </td> --}}
                {{-- </tr> --}}
                {{-- @endforeach --}}
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function () {
       var table = $('.data-table').DataTable({
                    paging: true,
                    lengthChange: false,
                    searching: true,
                    ordering: false,
                    info: false,
                    autoWidth: true,
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    displayStart: '{{ $current_index }}',
                    ajax: "{{ url('user') }}",
                    createdRow: function( row, data, dataIndex ) {
                          if(data.action.length==0)
                            $(row).find('td.action-col').css('display', 'none');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', class : 'data-index'},
                        {data: 'user_name', name: 'user_name'},
                        {data: 'email', name: 'email'},
                        {data: 'role_name', name: 'role_name'},
                        {data: 'action', name: 'action', orderable: false, searchable: false, className :'action-col'},
                    ]
                  });

       $('.common-search').on( 'keyup', function () {
            table.search( this.value ).draw();
        } );

        $('.common-search').on( 'search', function () {
            table.search( this.value ).draw();
        } );

        $('#selectRole').select2({
                placeholder : 'Select User Role',
                allowClear : true
            });

        $('#selectRole').on('change', function(){
               var search = $(this).val();
               if(search.length>0)
                table.column(3).search('^' + search + '$', true, false ).draw();
               else
                table.column(3).search(search, true, false ).draw();
            });

})
</script>

@stop
