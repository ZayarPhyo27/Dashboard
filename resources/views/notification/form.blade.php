<input type="hidden" name="current_index" value="{{$notification->current_index ?? 1}}">
<input type="hidden" name="notification_id" value="{{$notification->id}}">
<div class="row">
    <div class="col-sm-12">
    <div class="form-group">
        <label>{{ __('Title') }} <span class="text-red">*</span></label>
        <input id="noti-title" type="text" class="form-control"
                name="title" value="{{  $notification->title }}" autocomplete="title"
                autofocus placeholder="Title">
                <span class="invalid-feedback" role="alert">
                    <strong></strong>
                </span>

    </div>
    </div>

    <div class="col-sm-12">
    <div class="form-group">
        <label>{{ __('Description') }} <span class="text-red">*</span></label>
        <textarea id="editor1"  class="form-control " name="description"
        rows="7" placeholder="Description">{{  $notification->description }}</textarea>

        <span class="invalid-feedback" role="alert">
                    <strong></strong>
        </span>
    </div>
    </div>

    <!-- <div class="col-sm-12">
    <div class=" text-left">
        <a href="{{ url('notification') }}" class="btn btn-danger orange-text cancal-btn">Cancel</a>
        <span class="btn btn-primary dark-text save-btn save-noti">Save</span>
    </div>
    </div> -->
    @include('save-btn')

</div>

<script>


    $(document).ready(function() {
  
     $('.save-btn').click(function(){
        var that = $(this);
        var formData = new FormData(document.getElementById("notification_create"));
        var notification_id = $('form#notification_create').find('input[name=notification_id]').val();

        if(notification_id.length>0){
          var route = '/notification/'+notification_id;
          formData.append("_method", 'PUT');
        }else if(notification_id.length==0){
          var route = '/notification';
        }
        $.ajax({
            url : route,
            method : "POST",
            data : formData,
            dataType : 'json',
            cache : false,
            contentType : false,
            processData : false,
            headers : {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            success : function(data){
                window.location.href = '/notification?index='+data.index;
            },
            
            error: function(e){
                removeProcessing(that);

                if(e.responseJSON.title!=undefined){
                    $('form#notification_create').find('input[name=title]').addClass('is-invalid');
                    $('form#notification_create').find('input[name=title]').siblings().find('strong').html(e.responseJSON.title)
                }
             
                if(e.responseJSON.description!=undefined){
                $('form#notification_create').find('textarea[name=description]').addClass('is-invalid');
                $('form#notification_create').find('textarea[name=description] ').siblings().find('strong').html(e.responseJSON.description);
                }

                return false;
            }
            })
        });
        });
       





</script>