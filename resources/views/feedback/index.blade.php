@extends('adminlte::page')
@section('title', 'Feedback | Dashboard')
@section('content_header')
   @include('content_header')
@stop

@section('content')

    <div class="row">
        @include('common-search')

            <div class="col-md-3">
                <span class="fdr"  placeholder="From Date">
                    <input type="date" name="min" class="form-control from_date fromDate" id="min" placeholder="From Date">
                </span>
            </div>
            <div class="col-md-3">
                <span class="fdr"  placeholder="To Date">

                    <input type="date" name="max" class="form-control to_date toDate" id="max" placeholder="To Date">
                </span>
            </div>
    </div>
    <br>

    <table class="table table-bordered rating-table">
        <thead>
            <tr class="tb-header">
                <th style="width:10px;">#</th>
                {{-- <th>Date</th>
                <th>Customer Name</th> --}}
                {{-- <th>User Id</th> --}}
                <th>App Feedback</th>
                <th>Game Feedback</th>
                <th>Feedback Description</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($feedback as $index => $feedback)
                <tr>
                    <td class="data-index">{{ ++$index }}</td>
                    {{-- <td class="data-index">{{ date('d/m/Y',strtotime($feedback->created_at)) }}</td>
                    <td class="text-right">{{ $feedback->name }}</td> --}}
                    <td class="text-center">{{ $feedback->app_feedback }} </td>
                    <td class="text-center">{{ $feedback->game_feedback}}</td>
                    <td class="text-left">{{ $feedback->feedback_description }}</td>
                    <td class="text-left">{{ date('d/m/Y',strtotime($feedback->created_at))}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="table table-bordered average-rating-table">
        <thead>
            <tr class="tb-header">
                <th style="width:10px;">#</th>
                {{-- <th>Date</th>
                <th>Customer Name</th> --}}
                <th>Feedback Name</th>
                <th>Average Feedback Value</th>
            </tr>
        </thead>
        <tbody>
                <tr>
                    <td> 1</td>
                    <td> App Feedback </td>
                    <td class="text-left">{{ $feedback_avg_ratings->average_app_feedback}} ( {{ $feedback_avg_ratings->total_app_feedback }} )</td>
                </tr>
                <tr>
                    <td> 2</td>
                    <td> Game Feedback </td>
                    <td class="text-left">{{ $feedback_avg_ratings->average_game_feedback}} ( {{ $feedback_avg_ratings->total_game_feedback }} )</td>
                </tr>
        </tbody>
    </table>

    <script>
        const fromDateInput = document.getElementsByClassName('from_date')[0];
        const toDateInput = document.getElementsByClassName('to_date')[0];

        // Add event listener to the "from date" field
        fromDateInput.addEventListener('change', function() {
            const fromDateValue = new Date(this.value);

            // Enable all dates in the "to date" field
            toDateInput.min = '';
            toDateInput.max = '';

            if (fromDateValue) {
                const minDate = fromDateValue.toISOString().split('T')[0];
                toDateInput.min = minDate;
            }
        });

        $(document).ready(function() {

            $('#min').on('change', function() {
                if ($(this).val().length == 0) {
                    $(this).parent().attr('placeholder', 'From Date ');
                    $(this).addClass('fdr');
                } else {
                    var dateV= $(this).val();
                    $(this).parent().attr('placeholder', dateV);
                }
                dateSearch();
            });
            $('#max').on('change', function() {
                if ($(this).val().length == 0) {
                    $(this).parent().attr('placeholder', 'To Date ');
                    $(this).addClass('fdr');
                } else {
                    var dateV= $(this).val();
                    $(this).parent().attr('placeholder', dateV);
                }
                dateSearch();
            });


        function dateSearch(){
            var from_date = $('#min').val();
            var to_date = $('#max').val();
            $('#from_date').val(from_date);
            $('#to_date').val(to_date);

            // table.ajax.url("{{ url('article')}}"+"?category_id="+search_val).load();
            // Make an AJAX request to retrieve the table data
            $.ajax({
            url: '/feedback', // Replace with the URL to fetch the data from the server
            method: 'GET',
            data: {
                    from_date: from_date,
                    to_date: to_date,
                }, // Send the selected option to the server
            dataType: 'json',
            success: function(response){
                var feedback_rating = response.feedback_rating;
                var feedback_avg = response.feedback_avg;

                $('.rating-table tbody').empty();
                $('.average-rating-table tbody').empty();


            if( response != "" ){

                var rowCount = 1;
                // Append new rows to the table

                if(feedback_rating.length != 0){
                    feedback_rating.forEach(function(row) {
                        var createdAt = new Date(row.created_at);
                        // Get the day, month, and year components
                        var day = createdAt.getDate();
                        var month = createdAt.getMonth() + 1; // Month is zero-based, so add 1
                        var year = createdAt.getFullYear();

                        // Format the date as "dd/mm/YYYY"
                        var formattedDate = day + '/' + month + '/' + year;

                    var rowData = '<tr>' +
                        '<td>' + rowCount + '</td>' +
                        '<td class="text-center">' + row.app_feedback + '</td>' +
                        '<td class="text-center">' + row.game_feedback + '</td>' +
                        '<td class="text-left">' + row.feedback_description + '</td>' +
                        '<td class="text-left">' + formattedDate + '</td>' +
                        '</tr>';

                    $('.rating-table tbody').append(rowData);
                    rowCount++;
                    });
                    }
                else{
                    var rowData = '<tr>' +
                        '<td colspan=6 class="text-center text-danger"> No data is found. </td>' +
                        '</tr>';
                    $('.rating-table tbody').append(rowData);
                    rowCount++;
                }
                if(feedback_rating.length != 0){

                    var rowData = '<tr>' +
                        '<td>' + 1 + '</td>' +
                        '<td class="text-left"> App Feedback </td>' +
                        '<td class="text-left">' + feedback_avg.average_app_feedback + '('+ feedback_avg.total_app_feedback+')</td></tr>' +
                        '<tr>' +
                        '<td>' + 2 + '</td>' +
                        '<td class="text-left"> Game Feedback </td>' +
                        '<td class="text-left">' + feedback_avg.average_game_feedback + '('+ feedback_avg.total_game_feedback+')</td>' +
                        '</tr>';

                    $('.average-rating-table tbody').append(rowData);

                    }
                else{
                    var rowData = '<tr>' +
                        '<td colspan=3 class="text-center text-danger"> No data is found. </td>' +
                        '</tr>';
                    $('.average-rating-table tbody').append(rowData);

                }
            }

            },
            error: function(error) {
            console.log('Error:', error);
            }
        });
        }
        })
    </script>
@endsection
