<input type="hidden" name="current_index" value="{{$user->current_index ?? 1}}">
<input type="hidden" name="user_id" value="{{$user->id}}">
@if ($is_profile)
    <input type="hidden" name="auth_role" value="{{$user->role_id}}">
@endif
<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label>User Role <span class="text-red">*</span></label>
            <select class="form-control select2 user-role-select2 @error('role_id') is-invalid @enderror @if($is_profile) profile-role-id @endif" name="role_id" style="width: 100%;">
                <option value=""></option>
                @foreach (config("web_constant.user_roles") as $k => $r)
                    @if($k != 3)
                        <option value="{{$k}}" @if(old('role_id', $user->role_id)==$k) selected @endif>{{$r}}</option>
                    @endif
                @endforeach
            </select>
            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>

        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Name') }} <span class="text-red">*</span></label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="user_name" value="{{ old('name', $user->user_name) }}" autocomplete="name" autofocus placeholder="Name"  >

            <span class="invalid-feedback" role="alert" >
                <strong></strong>
            </span>

        </div>
    </div>
</div>

<div class="row psw">
    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('E-Mail Address') }} <span class="text-red">*</span></label>
            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" placeholder="E-Mail Address">

            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>
        </div>
    </div>

    {{-- <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Phone No') }} <span class="text-red">*</span></label>
            <input id="phone_no" type="text" class="form-control @error('phone_no') is-invalid @enderror" name="phone_no" value="{{ old('phone_no', $user->phone_no) }}" autocomplete="phone_no" placeholder="Phone No">

            @error('phone_no')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div> --}}
</div>

<div class="row psw">
    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Password') }} <span class="text-red">*</span></label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password" placeholder="Password">

            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Confirm Password') }} <span class="text-red">*</span></label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" placeholder="Confirm Password">
        </div>
        <span class="invalid-feedback" role="alert">
            <strong></strong>
        </span>
    </div>
</div><br>

    @include('save-btn')


<script>
$(document).ready(function(){
    $('.user-role-select2').select2({
        placeholder : 'Select User Role',
        allowClear : true
    });

    $('.profile-role-id').prop('disabled','disabled');

    $('.save-btn').click(function(){

        var that = $(this);
        var formData = new FormData(document.getElementById("user-form"));
        var user_id = $('form#user-form').find('input[name=user_id]').val();

        if(user_id.length>0){
        var route = '/user/'+user_id;
        formData.append("_method", 'PUT');
        }else if(user_id.length==0){
        var route = '/user';
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
            beforeSend : function(){
                clearErrorMsg();
            },
            success : function(data){
                window.location.href = '/user?index='+data.index;
            },
            complete : function (data) {

            },
            error: function(e){
                removeProcessing(that);
                if(e.responseJSON.user_name!=undefined){
                $('form#user-form').find('input[name="user_name"]').addClass('is-invalid');
                $('form#user-form').find('input[name="user_name"] ').siblings().find('strong').html(e.responseJSON.user_name);
                }
                if(e.responseJSON.email!=undefined){
                $('form#user-form').find('input[name="email"]').addClass('is-invalid');
                $('form#user-form').find('input[name="email"] ').siblings().find('strong').html(e.responseJSON.email);
                }
                if(e.responseJSON.password!=undefined){
                $('form#user-form').find('input[name="password"]').addClass('is-invalid');
                $('form#user-form').find('input[name="password"] ').siblings().find('strong').html(e.responseJSON.password);
                }

                if(e.responseJSON.role_id!=undefined){
                $('form#user-form').find('select[name="role_id"]').addClass('is-invalid');
                $('form#user-form').find('select[name="role_id"] ').siblings().find('strong').html(e.responseJSON.role_id);
                }
                if(e.responseJSON.order!=undefined){
                $('form#user-form').find('input[name="order"]').addClass('is-invalid');
                $('form#user-form').find('input[name="order"] ').siblings().find('strong').html(e.responseJSON.order);
                }

                return false;
            }
        })
    });

})


</script>
