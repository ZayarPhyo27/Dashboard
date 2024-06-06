@extends('adminlte::page')
@section('title', 'Podcast | Dashboard')
@section('content_header')
   @include('content_header')
@stop

@section('content')
<div class="custom-container" style=" padding-bottom: 50px;">
<div class="row">
        @include('common-search')

        <div class="col-md-3">
                    <select id="selectType" class="form-control">
                        <option value="">Select Type</option>
                        @foreach (config("web_constant.category_type") as $cid => $cvalue)
                    <option value="{{ $cvalue }}">{{ $cvalue }}</option>
                @endforeach
                    </select>
                </div>
        <div class="col-md-3">
                    <select id="selectStatus" class="form-control">
                        <option value="">Select Status</option>
                        @foreach (config('web_constant.status') as $sid => $svalue)
                            <option value="{{ $svalue }}">{{ $svalue }}</option>
                        @endforeach
                    </select>
                </div>
        <!-- <div class="col-md-3">
            <select name="status" id="status" class="form-control">
                <option value=""></option>
                @foreach (config('web_constant.status') as $k => $t)
                    <option value="{{ $k }}">{{ $t }}</option>
                @endforeach
            </select>
        </div> -->
    </div><br>
<table class="table table-bordered data-table data-table" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th >Category</th>
                    <th>{{__('labels.duration') }} (Min/Sec)</th>
                    <th>{{__('labels.download_size')}}</th>
                    <th>{{__('labels.cover_photo')}}</th>
                    <th>{{__('labels.published_at')}}</th>
                    <th class="status-col">Status</th>
                    <th  >Action</th>
                </tr>
            </thead>
            <tbody>


            </tbody>
        </table>
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
                    displayStart : "{{$current_index}}",
                    ajax: "{{url('podcast') }}",
                    createdRow: function( row, data, dataIndex ) {
                          if(data.action.length==0)
                            $(row).find('td.action-col').css('display', 'none');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', class : 'data-index'},
                        {data: 'title', name: 'title'},
                        {data: 'category_id', name: 'category_id'},
                        {data: 'duration', name: 'duration'},
                        {data: 'download_size', name: 'download_size'},
                        {data: 'cover_photo', name: 'cover_photo'},
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
        $('#selectType').select2({
            placeholder: 'Filter By Category',
            allowClear: true});

        $('#selectType').on('change', function(){
        search = $(this).val();
        table.column(2).search(search, true, false).draw();

        });

        $('#selectStatus').select2({
            placeholder: 'Filter By Status',
            allowClear: true});

        $('#selectStatus').on('change', function(){

        //
        var search = [];

        $.each($('#selectStatus option:selected'), function(){
                search.push($(this).val());
        });
        search = search.join('|');
        table.column(5).search(search, true, false).draw();

        });


})
</script>
@stop



