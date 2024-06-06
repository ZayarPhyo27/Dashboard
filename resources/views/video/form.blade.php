<input type="hidden" name="current_index" value="{{$video->current_index ?? 1}}">
<input type="hidden" name="video_id" value="{{$video->id}}">
<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label>Category Type <span class="text-red">*</span></label>
            <select class="form-control select2 video-role-select2 @error('category_id') is-invalid @enderror" name="category_id" style="width: 100%;">
                <option value=""></option>
                @foreach (config("web_constant.category_type") as $k => $r)

                    <option value="{{$k}}" @if(old('category_id', $video->category_id)==$k) selected @endif>{{$r}}</option>

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
            <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $video->title) }}" autocomplete="title" autofocus placeholder="Title Name">

            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>

        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Video') }} <span class="text-red">*</span><span class="photo_upload_description">( {{$upload_video_description}} )</span></label>
            <div class="row mb-2" id="video_path_row" @if (!$video->video_path) style="display: none;" @endif>
                <div class="col-md-9">
                    <input id="video_path" type="text" class="form-control"
                        name="video_path" value="{{ old('video_path', $video->video_path) }}" autocomplete="video_path"
                        autofocus placeholder="Video Link" @if ($video->video_path)
                        readonly
                        @else
                        style="display: none;"
                        @endif>
                    </div>
                <div class="col-md-3">
                    <div class="mr-3 ml-2">
                        <a href="{{$video->video_path}}" class="form-control hpy-btn btn btn-info " target="blank">
                            {{ __('View Video') }}
                        </a>
                    </div>
                </div>
            </div>


            <input type="file" class="@error('photo') is-invalid @enderror photo_file form-input form-control" id="video_file" name="video_file" accept="video/mp4">
            {{-- <input type="file" id="video-file" class="@error('photo') is-invalid @enderror photo_file form-input form-control" name="video_file" placeholder="{{__('Upload Video File')}}" @if((isset($is_update) && count(old())==0) || old('virtual_video')!=null) style="display:none" @else style="" @endif> --}}
            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>
            <input type="hidden" name="virtual_video" id="virtual-video" value="{{$video->video_path}}">

        </div>
    </div><br>

    <div class="col-md-6" style="display: block!important;" >
        <label for="" style="display: block;">{{__('Cover Photo')}}<span class="font-weight-light">( 200 x 155 px )</span> <span class="text-red">* </span> <span class="photo_upload_description">( {{$upload_photo_description}} )</span> </label>

        <div class="row preview-row" @if(isset($is_update)) style="" @else style="display:none" @endif>
            <div class="col-md-10">
                <img id="upload-preview" src="{{ $video->cover_photo }}" alt="upload photo" style="width:100%"/>
            </div>
            <div class="col-md-1">
                <span class="remove-photo btn btn-link"><i class="fas fa-times "></i></span>
            </div>
        </div>
        <div class="row">
            <input type="file" id="single-photo" class="@error('photo') is-invalid @enderror photo_file form-input form-control" name="cover_photo" placeholder="{{__('Upload Photo')}}" @if((isset($is_update) && count(old())==0) || old('virtual_img')!=null) style="display:none" @else style="" @endif>
            <input type="hidden" class=" @error('content_cover_photo') is-invalid @enderror" >
            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>

            <input type="hidden" name="virtual_img" id="virtual-img" value="{{$video->cover_photo}}">
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Video Duration') }}<span class="text-red"> * </span><span class="photo_upload_description">( {{$duration_format}} )</span>  </label>
            <input id="duration" type="text" class="form-control"
                name="duration" value="{{ old('duration', $video->duration) }}"
                autocomplete="duration" autofocus placeholder="00:00">
                <span class="invalid-feedback" role="alert">
                    <strong></strong>
                </span>

        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Video Download Size') }}<span class="text-red"> * </span><span class="photo_upload_description">( {{$download_size}} )</span>  </label>
            <input id="download_size" type="text" class="form-control"
                name="download_size" value="{{ old('download_size', rtrim($video->download_size, ' MB')) }}"
                autocomplete="download_size" autofocus placeholder="0.0">
                <span class="invalid-feedback" role="alert">
                    <strong></strong>
                </span>
        </div>
    </div>

</div>

@include('save-btn')

<script>
$(document).ready(function(){
    $('.video-role-select2').select2({
        placeholder : 'Select Category Type',
        allowClear : true
    });

    $('.save-btn').click(function(){

        var that = $(this);
        var formData = new FormData(document.getElementById("video-form"));
        var video_id = $('form#video-form').find('input[name=video_id]').val();

        if(video_id.length>0){
        var route = '/video/'+video_id;
        formData.append("_method", 'PUT');
        }else if(video_id.length==0){
        var route = '/video';
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
                window.location.href = '/video?index='+data.index;
            },
            complete : function (data) {

            },
            error: function(e){
                removeProcessing(that);
                if(e.responseJSON.title!=undefined){
                $('form#video-form').find('input[name="title"]').addClass('is-invalid');
                $('form#video-form').find('input[name="title"] ').siblings().find('strong').html(e.responseJSON.title);
                }
                if(e.responseJSON.video_file!=undefined){
                $('form#video-form').find('input[name="video_file"]').addClass('is-invalid');
                $('form#video-form').find('input[name="video_file"] ').siblings().find('strong').html(e.responseJSON.video_file);
                }
                if(e.responseJSON.cover_photo!=undefined){
                $('form#video-form').find('input[name="cover_photo"]').addClass('is-invalid');
                $('form#video-form').find('input[name="cover_photo"] ').siblings().find('strong').html(e.responseJSON.cover_photo);
                }

                if(e.responseJSON.category_id!=undefined){
                $('form#video-form').find('select[name="category_id"]').addClass('is-invalid');
                $('form#video-form').find('select[name="category_id"] ').siblings().find('strong').html(e.responseJSON.category_id);
                }
                if(e.responseJSON.duration!=undefined){
                $('form#video-form').find('input[name="duration"]').addClass('is-invalid');
                $('form#video-form').find('input[name="duration"] ').siblings().find('strong').html(e.responseJSON.duration);
                }
                if(e.responseJSON.download_size!=undefined){
                $('form#video-form').find('input[name="download_size"]').addClass('is-invalid');
                $('form#video-form').find('input[name="download_size"] ').siblings().find('strong').html(e.responseJSON.download_size);
                }

                return false;
            }
        })
    });

})


</script>
