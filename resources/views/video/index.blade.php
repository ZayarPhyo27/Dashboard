@extends('adminlte::page')
@section('title', 'Video | Dashboard')
@section('content_header')
    @include('content_header')
@stop

@section('content')
<div class="custom-container">
    <div class="row">
        @include('common-search')
        <div class="{{$filter_class}}">
            <select name="role_id" id="selectCate" class="form-control type-filter-select2">
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
                    <th>{{__('labels.cover_photo')}}</th>
                    <th>{{__('labels.duration') }} (Min/Sec)</th>
                    <th>{{__('labels.download_size')}}</th>
                    <th>{{__('labels.published_at')}}</th>
                    <th class="status-col">{{__('labels.status')}}</th>
                    {{-- @can('edit-user','delete-user','view-user') --}}
                    <th width="20%">{{__('labels.actions')}}</th>
                    {{-- @endcan --}}
                </tr>
            </thead>
            <tbody>

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
                    displayStart: "{{ $current_index }}",
                    ajax: "{{ url('video') }}",
                    createdRow: function( row, data, dataIndex ) {
                          if(data.action.length == 0)
                            $(row).find('td.action-col').css('display', 'none');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', class : 'data-index'},
                        {data: 'video_title', name: 'video_title'},
                        {data: 'category_name', name: 'category_name'},
                        {data: 'cover_photo', name: 'cover_photo'},
                        {data: 'duration', name: 'duration'},
                        {data: 'download_size', name: 'download_size'},
                        {data: 'published_at', name: 'published_at'},
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

        $('#selectCate').select2({
                placeholder : 'Select Category Type',
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
            var video_status = $('#selectStatus').val();
            var video_cate = $('#selectCate').val();

            table.ajax.url("{{ url('video')}}"+"?video_status="+video_status+"&video_cate="+ video_cate).load();
        }

})
</script>

@stop
