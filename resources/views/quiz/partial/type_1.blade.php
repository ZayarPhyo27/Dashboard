<table class="table table-bordered data-table type-1-table" style="width : 100%;">
    <thead>
        <tr>
            <th width="50">No</th>
            <th>Question</th>
            <th>Type</th>
            <th>Created By</th>
            <th>Published By</th>
            <th>Published At</th>
            <th class="status-col">Status</th>
            @if(auth()->user()->can('quiz-edit') || auth()->user()->can('quiz-view') || auth()->user()->can('quiz-publish') || auth()->user()->can('quiz-active') || auth()->user()->can('quiz-delete'))
              <th class="action-col"><span>Action</span></th>
            @else
              <th class="hide-col" ><span></span></th>
            @endif
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
  $(document).ready(function(){

})
</script>
