@if ($message = Session::has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="alertMessage" style="display: none">
        <strong>{{Session::get('success') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
    </div>

@elseif ($message = Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alertMessage" style="display: none">
        <strong>{{Session::get('error') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<script>
    $(document).ready(function(){
        setTimeout(() => {
            $('.alert').css('display','block');

            $("#alertMessage").fadeTo(1000, 500).slideUp(500, function(){
                <?php 
                    session()->forget('success');
                    session()->forget('error');
                ?>
            });
        }, 1000);
    })
</script>