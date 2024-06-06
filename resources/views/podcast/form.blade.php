<input type="hidden" name="current_index" value="{{$podcast->current_index ?? 1}}">
<input type="hidden" name="podcast_id" value="{{$podcast->id}}">

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
        <label>{{ __('Category') }} <span class="text-red">*</span></label>
        <select class="form-control audio-select2 select2 @error('category_id') is-invalid @enderror" name="category_id" style="width: 100%;">
            <option value=""></option>
            @foreach (config("web_constant.category_type") as $cid => $cvalue)
              <option value="{{ $cid }}" @if(old('category_id', $podcast->category_id)==$cid) selected @endif>
                {{ $cvalue }}</option>
                @endforeach

            </select>
            <span class="invalid-feedback" role="alert"><strong></strong></span>
        </div>

    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Title') }} <span class="text-red">*</span></label>
            <input type="text" name="title" id="title" class="form-control" autocomplete="name"
            autofocus placeholder="Enter Title" value="{{ old('title', $podcast->title) }}">
            <span class="invalid-feedback" role="alert">
            <strong></strong>
        </span>
        </div>
    </div>

     <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Audio') }} <span class="text-red">*</span><span class="photo_upload_description">( The file type must be mp3. )</span></label>
            <div class="row mb-2" id="video_path_row" @if (!$podcast->audio_path) style="display: none;" @endif>
                <div class="col-md-9">
                    <input id="audio_path" type="text" class="form-control"
                    name="audio_path" value="{{ old('audio_path', $podcast->audio_path) }}" autocomplete="audio_path"
                    autofocus placeholder="Audio Link" @if ($podcast->audio_path)
                       readonly
                       @else
                       style="display: none;"
                    @endif>
                </div>
                <div class="col-md-3">
                    <div class="mr-3 ml-2">
                        <a href="{{$podcast->audio_path}}" class="form-control hpy-btn btn btn-info " target="blank">
                            {{ __('View Video') }}
                        </a>
                    </div>
                </div>
            </div>



            <input type="file" class="@error('photo') is-invalid @enderror photo_file form-input form-control" id="audio_file" name="audio_file" accept=".mp3">
            {{-- <input type="file" id="audio-file" class="@error('photo') is-invalid @enderror photo_file form-input form-control" name="audio_path" placeholder="{{__('Upload Audio File')}}" @if((isset($is_update) && count(old())==0) || old('virtual_audio')!=null) style="display:none" @else style="" @endif> --}}
            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>
            <input type="hidden" name="virtual_audio" id="virtual-audio" value="{{$podcast->audio_path}}">

        </div>
    </div><br>

    <div class="col-md-6" style="display: block!important;" >
        <label for="" style="display: block;">{{__('Cover Photo')}}<span class="font-weight-light">( 360 x 432px )</span> <span class="text-red">* </span> <span class="photo_upload_description">( {{$upload_photo_description}} )</span> </label>

        <div class="row preview-row" @if(isset($is_update)) style="" @else style="display:none" @endif>
            <div class="col-md-10">
                <img id="upload-preview" src="{{ $podcast->cover_photo }}" alt="upload photo" style="width:100%"/>
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

            <input type="hidden" name="virtual_img" id="virtual-img" value="{{$podcast->cover_photo}}" >
        </div>
    </div><br>



    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Duration') }}<span class="text-red"> * </span><span class="photo_upload_description">( {{$duration_format}} )</span>  </label>
            <input id="duration" type="text" class="form-control"
                name="duration" value="{{ old('duration', $podcast->duration) }}"
                autocomplete="duration" autofocus placeholder="00:00">
                <span class="invalid-feedback" role="alert">
                    <strong></strong>
                </span>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __('Podcast Download Size') }}<span class="text-red"> * </span><span class="photo_upload_description">( {{$download_size}} )</span>  </label>
            <input id="download_size" type="text" class="form-control"
                name="download_size" value="{{ old('download_size', rtrim($podcast->download_size, ' MB')) }}"
                autocomplete="download_size" autofocus placeholder="0.0">
                <span class="invalid-feedback" role="alert">
                    <strong></strong>
                </span>
        </div>
    </div>

    </div><br>

        @include('save-btn')


<script>

$(document).ready(function() {
    $('.audio-select2').select2({
        placeholder : 'Select Category Type',
        allowClear : true
    });

 $('.save-btn').click(function(){
    var that = $(this);
    var formData = new FormData(document.getElementById("podcast_create"));
    var podcast_id = $('form#podcast_create').find('input[name=podcast_id]').val();

    if(podcast_id.length>0){
      var route = '/podcast/'+podcast_id;
      formData.append("_method", 'PUT');
    }else if(podcast_id.length==0){
      var route = '/podcast';
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
            window.location.href = '/podcast?index='+data.index;
        },

        error: function(e){
            removeProcessing(that);

            if(e.responseJSON.title!=undefined){
                $('form#podcast_create').find('input[name=title]').addClass('is-invalid');
                $('form#podcast_create').find('input[name=title]').siblings().find('strong').html(e.responseJSON.title)
            }

            if(e.responseJSON.duration!=undefined){
                $('form#podcast_create').find('input[name=duration]').addClass('is-invalid');
                $('form#podcast_create').find('input[name=duration]').siblings().find('strong').html(e.responseJSON.duration)
            }

            if(e.responseJSON.cover_photo!=undefined){
                $('form#podcast_create').find('input[name=cover_photo]').addClass('is-invalid');
                $('form#podcast_create').find('input[name=cover_photo]').siblings().find('strong').html(e.responseJSON.cover_photo)
            }

            if(e.responseJSON.audio_file!=undefined){
                $('form#podcast_create').find('input[name=audio_file]').addClass('is-invalid');
                $('form#podcast_create').find('input[name=audio_file]').siblings().find('strong').html(e.responseJSON.audio_file)
            }
            if(e.responseJSON.category_id!=undefined){
                $('form#podcast_create').find('select[name="category_id"]').addClass('is-invalid');
                $('form#podcast_create').find('select[name="category_id"] ').siblings().find('strong').html(e.responseJSON.category_id);
            }
            if(e.responseJSON.download_size!=undefined){
                $('form#podcast_create').find('input[name="download_size"]').addClass('is-invalid');
                $('form#podcast_create').find('input[name="download_size"] ').siblings().find('strong').html(e.responseJSON.download_size);
            }

            return false;
        }
        })
    });
    });


</script>
