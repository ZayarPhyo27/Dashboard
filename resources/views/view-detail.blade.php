<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>Created By</th>
                    <td>{{$created_user_name}}</td>
                    <th>Created At</th>
                    <td>{{$created_at}}</td>
                </tr>
                <tr>
                    @if($updated_user_name!=null)
                        <th>Updated By</th>
                        <td>{{$updated_user_name}}</td>
                    @endif
                    @if($updated_at!=null)
                        <th>Updated At</th>
                        <td>{{$updated_at}}</td>
                    @endif
                </tr>
                <tr>
                    @if($published_user_name!=null)
                        <th>Published By</th>
                        <td>{{$published_user_name}}</td>
                    @endif
                    @if($published_at!=null)
                        <th>Published At</th>
                        <td>{{$published_at}}</td>
                    @endif
                </tr>
                <tr>
                    @if($deactivated_user_name!=null)
                        <th>Deactivated By</th>
                        <td>{{$deactivated_user_name}}</td>
                    @endif
                    @if($deactivated_at!=null)
                        <th>Deactivated At</th>
                        <td>{{$deactivated_at}}</td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
 </div><br>

 @include('back-btn')
