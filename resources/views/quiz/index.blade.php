@extends('adminlte::page')

@section('title', 'Quiz | Dashboard')

@section('content_header')
    @include('content_header')
@stop

@section('content')

    @php
        $active_tab = isset($_GET['type']) ? $_GET['type'] : 1;

    @endphp
    <div class="row">
        @include('common-search')
        <div class="col-md-3">
            <select class="form-control select2 type-filter-select2" name="quiz_type" style="width: 100%;">
                <option value=""></option>
                @foreach (config('web_constant.quiz_types') as $k => $t)
                    <option value="{{ $k }}">{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-control select2 status-filter-select2 quiz_status" name="quiz_type" style="width: 100%;">
                <option value=""></option>
                @foreach (config('web_constant.status') as $k => $t)
                    <option value="{{ $k }}">{{ $t }}</option>
                @endforeach
            </select>
        </div>

    </div><br>

    <div class="row">
        <div class="col-md-12">

            <ul class="nav nav-tabs sub-nav-tabs" id="myTab" role="tablist">
                @foreach (config('web_constant.quiz_content_types') as $k => $type)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link @if($active_tab == $k) active @endif"
                            id="type-{{ $k }}-tab" data-toggle="tab" href="#type-{{ $k }}"
                            role="tab" aria-controls="type-{{ $k }}"
                            aria-selected="true">{{ $type }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content sub-tab-content" id="myTabContent">
                <div class="tab-pane fade @if ($active_tab == 1) show active @endif" id="type-1"
                    role="tabpanel" aria-labelledby="type-1-tab">
                    @include('quiz.partial.type_1')
                </div>

                <div class="tab-pane fade @if ($active_tab == 2) show active @endif" id="type-2"
                    role="tabpanel" aria-labelledby="type-2-tab">
                    @include('quiz.partial.type_2')
                </div>

            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var table1 = $('.type-1-table').DataTable({
                    paging: true,
                    lengthChange: false,
                    searching: true,
                    ordering: false,
                    info: false,
                    autoWidth: true,
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: "{{ url('quiz?content_type=1') }}",
                    createdRow: function( row, data, dataIndex ) {
                          if(data.action.length==0)
                            $(row).find('td.action-col').css('display', 'none');
                      },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', class : 'data-index'},
                        {data: 'question', name: 'question'},
                        {data: 'quiz_type', name: 'quiz_type'},
                        {data: 'created_user_name', name: 'created_user_name'},
                        {data: 'published_user_name', name: 'published_user_name'},
                        {data: 'published_at', name: 'published_at'},
                        {data: 'status', name: 'status'},
                        {data: 'action', name: 'action', orderable: false, searchable: false, className :'action-col'},
                    ]
                  });

            // table 2
            var table2 = $('.type-2-table').DataTable({
                    paging: true,
                    lengthChange: false,
                    searching: true,
                    ordering: false,
                    info: false,
                    autoWidth: true,
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: "{{ url('quiz?content_type=2') }}",
                    createdRow: function( row, data, dataIndex ) {
                          if(data.action.length==0)
                            $(row).find('td.action-col').css('display', 'none');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', class : 'data-index'},
                        {data: 'question', name: 'question'},
                        {data: 'quiz_type', name: 'quiz_type'},
                        {data: 'created_user_name', name: 'created_user_name'},
                        {data: 'published_user_name', name: 'published_user_name'},
                        {data: 'published_at', name: 'published_at'},
                        {data: 'status', name: 'status'},
                        {data: 'action', name: 'action', orderable: false, searchable: false, className :'action-col'},
                    ]
                  });

            $('.common-search').on('keyup', function() {

                if ($('#type-1-tab').hasClass('active'))
                    table1.search(this.value).draw();
                else if ($('#type-2-tab').hasClass('active'))
                    table2.search(this.value).draw();
            });

            $('.common-search').on('search', function() {
                if ($('#type-1-tab').hasClass('active'))
                    table1.search(this.value).draw();
                else if ($('#type-2-tab').hasClass('active'))
                    table2.search(this.value).draw();
            });

            $('.type-filter-select2').select2({
                placeholder: "Filter By Type",
                allowClear: true
            });

            $(document).on('change', '.type-filter-select2, .status-filter-select2', function() {
                var quiz_type = $('.type-filter-select2').val();
                var quiz_status = $('.quiz_status').val();

                if ($('#type-1-tab').hasClass('active'))
                    table1.ajax.url("{{ url('quiz?quiz_type=') }}" + quiz_type + "&quiz_status=" + quiz_status + '&content_type=1')
                    .load();
                else if ($('#type-2-tab').hasClass('active'))
                    table2.ajax.url("{{ url('quiz?quiz_type=') }}" + quiz_type + "&quiz_status=" + quiz_status + '&content_type=2')
                    .load();

            });

            $('.status-filter-select2').select2({
                placeholder: "Filter By Status",
                allowClear: true
            });

          $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                let quiz_type = '';
                let quiz_status = '';
                if($('.type-filter-select2').val().length > 0 || $('.status-filter-select2').val().length > 0){
                    $('.type-filter-select2').val(null).trigger('change.select2');
                    $('.status-filter-select2').val(null).trigger('change.select2');

                    if(e.relatedTarget.id=="type-1-tab"){ // previous active tab
                        table1.ajax.url("{{ url('quiz?quiz_type=')}}"+quiz_type+'&content_type=1&quiz_status='+quiz_status).load();
                    }else if(e.relatedTarget.id=="type-2-tab"){ // previous active tab
                        table2.ajax.url("{{ url('quiz?quiz_type=')}}"+quiz_type+'&content_type=2&quiz_status='+quiz_status).load();
                    }
                }

                if($('.common-search').val().length > 0){
                    $('.common-search').val(null);
                    if(e.relatedTarget.id=="type-1-tab"){ // previous active tab
                      table1.search( '' ).draw();
                    }else if(e.relatedTarget.id=="type-2-tab"){ // previous active tab
                      table2.search( '' ).draw();
                    }
                }
          })
     });
   </script>
@stop
