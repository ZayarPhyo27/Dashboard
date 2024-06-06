@extends('adminlte::page')
@section('title', 'Article | Dashboard')
@section('content_header')
    @include('content_header')
@stop

@section('content')
<div class="custom-container">
    <div class="row">
        @include('common-search')
        <div class="{{$filter_class}}">
            <select name="role_id" id="selectRole" class="form-control search-category type-filter-select2">
                <option value=""></option>
                @foreach (config('web_constant.category_type') as $s => $t)
                    <option value="{{ $s }}">{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="{{$filter_class}}">
            <select name="status" id="selectStatus" class="form-control type-filter-select2">
                <option value=""></option>
                @foreach (config("web_constant.status") as $k=>$t)
                    <option value="{{$k}}">{{$t}}</option>
                @endforeach
            </select>
        </div>
    </div><br>

        <table class="table table-bordered data-table">
            <thead>
                <tr class="tb-header">
                    <th style="width:10px;">#</th>
                    <th>{{__('labels.title')}}</th>
                    <th>{{__('labels.category_name')}}</th>
                    <th width="200px">{{__('labels.cover_photo')}}</th>
                    <th class="status-col">{{__('labels.status')}}</th>
                    <th width="20%">{{__('labels.actions')}}</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div> <br>

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
                    displayStart: "{{ $current_index }}",
                    ajax: "{{ url('article') }}",
                    createdRow: function( row, data, dataIndex ) {
                          if(data.action.length == 0)
                            $(row).find('td.action-col').css('display', 'none');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', class : 'data-index'},
                        {data: 'title', name: 'title'},
                        {data: 'category_id', name: 'category_id'},
                        {data: 'cover_photo', name: 'cover_photo'},
                        {data: 'status', name: 'status'},
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
                placeholder : 'Select Cateory',
                allowClear : true
            });

            $('#selectStatus').select2({
            placeholder : 'Select Status',
            allowClear : true
        });

        $(document).on('change', '.type-filter-select2', function() {
            searchFun();
        });

        function searchFun(){
            var search_val = $('.search-category').val();
            var search_status = $('#selectStatus').val();
            table.ajax.url("{{ url('article')}}"+"?category_id="+search_val+"&search_status="+search_status).load();

        }

})
</script>

@stop
