@extends('adminlte::page')
@section('title', 'Notification')
@section('content_header')
   @include('content_header')
@stop

@section('content')
<div class="custom-container" style=" padding-bottom: 50px;">
    <div class="row">
        @include('common-search')
        <div class="{{$filter_class}}">
            <select name="role_id" id="selectNotiStatus" class="form-control">
            <option value=""></option>
        @foreach (config("web_constant.notification_status") as $k=>$t)
            <option value="{{$t}}">{{$t}}</option>
        @endforeach
            </select>
        </div>
    </div><br>
<table class="table table-bordered data-table data-table-show">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th width="500px">Description</th>
                    <th>Status</th>
                    <th width="200px">Action</th>
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
                    //stateSave: true,
                    ajax: "{{ url('notification') }}",
                    createdRow: function( row, data, dataIndex ) {
                          if(data.action.length==0)
                            $(row).find('td.action-col').css('display', 'none');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', class : 'data-index'},
                        {data: 'title', name: 'title'},
                        {data: 'description', name: 'description'},
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
        $('.edit-data').on( 'click', function () {
            console.log($('.page-item.active a').attr('data-dt-idx'));




        } );

        $('#selectNotiStatus').select2({
                placeholder : 'Select Status',
                allowClear : true
            });

            $('#selectNotiStatus').on('change', function(){
               var search = $(this).val();
               table.search( search ).draw();


            });

})
</script>
@stop



