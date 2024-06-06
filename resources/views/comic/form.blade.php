<input type="hidden" name="current_index" value="{{$comic->current_index ?? 1}}">
<input type="hidden" name="comic_id" value="{{$comic->id}}">



<div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('Title') }} <span class="text-red">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" autocomplete="title"
                    autofocus placeholder="Enter Title" value="{{ old('title' , $comic->title) }}">
                    <span class="invalid-feedback" role="alert">
                    <strong></strong>
                </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label>{{ __('Category') }} <span class="text-red">*</span></label>
                    <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror">
                        <option value="">Choose Category</option>
                        @foreach (config("web_constant.category_type") as $cid => $cvalue)

                        <option value="{{ $cid }}" @if(old('category_id', $comic->category_id)==$cid) selected @endif>
                        {{ $cvalue }}</option>

                        @endforeach

                    </select>
                    <span class="invalid-feedback" role="alert">
                    <strong></strong>
                    </span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('PDF File') }} <span class="text-red">*</span><span class="photo_upload_description">( {{$upload_pdf_description}} )</span></label>

                    <div class="row mb-2" id="video_path_row" @if (!$comic->pdf_path) style="display: none;" @endif>
                        <div class="col-md-9">
                            <input id="pdf_path" type="text" class="form-control @error('pdf_path') is-invalid @enderror"
                                name="pdf_path" value="{{ old('pdf_path', $comic->pdf_path) }}" autocomplete="pdf_path"
                                autofocus placeholder="Video Link" @if ($comic->pdf_path)
                                readonly
                                @else
                                style="display: none;"
                                @endif>
                            </div>
                        <div class="col-md-3">
                            <div class="mr-3 ml-2">
                                <a href="{{$comic->pdf_path}}" class="form-control hpy-btn btn btn-info " target="blank">
                                    {{ __('View Pdf') }}
                                </a>
                            </div>
                        </div>
                    </div>



                    <input type="file" class="@error('pdf') is-invalid @enderror photo_file form-input form-control" id="pdf_file" name="pdf_file" accept="application/pdf">
                    {{-- <input type="file" id="video-file" class="@error('photo') is-invalid @enderror photo_file form-input form-control" name="pdf_path" placeholder="{{__('Upload Video File')}}" @if((isset($is_update) && count(old())==0) || old('virtual_video')!=null) style="display:none" @else style="" @endif> --}}
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                    <input type="hidden" name="virtual_pdf" id="virtual-pdf" value="{{$comic->pdf_path}}">

                </div>
            </div><br>

            <div class="col-md-6" style="display: block!important;" >

                <label for="" style="display: block;">{{__('Cover Photo')}}<span class="font-weight-light">( 200 x 225 px )</span> <span class="text-red">* </span> <span class="photo_upload_description">( {{$upload_photo_description}} )</span> </label>

                <div class="row preview-row" @if(isset($is_update)) style="" @else style="display:none" @endif>
                    <div class="col-md-10">
                        <img id="upload-preview" src="{{ $comic->cover_photo }}" alt="upload photo" style="width:100%"/>
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

                    <input type="hidden" name="virtual_img" id="virtual-img" value="{{$comic->cover_photo}}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('PDF Download Size') }}<span class="text-red"> * </span><span class="photo_upload_description">( {{$download_size}} )</span>  </label>
                    <input id="download_size" type="text" class="form-control @error('download_size') is-invalid @enderror"
                        name="download_size" value="{{ old('download_size', rtrim($comic->download_size, ' MB')) }}"
                        autocomplete="download_size" autofocus placeholder="0.0">
                        <span class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                </div>
            </div>



        </div>

        @include('save-btn')


<script>

$(document).ready(function() {

$('#category_id').select2({
    placeholder : 'Select Category Type',
    allowClear : true
});

 $('.save-btn').click(function(){
    var that = $(this);
    var formData = new FormData(document.getElementById("comic_create"));
    var comic_id = $('form#comic_create').find('input[name=comic_id]').val();

    if(comic_id.length>0){
      var route = '/comic/'+comic_id;
      formData.append("_method", 'PUT');
    }else if(comic_id.length==0){
      var route = '/comic';
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
            window.location.href = '/comic?index='+data.index;
        },

        error: function(e){
            removeProcessing(that);

            if(e.responseJSON.title!=undefined){
                $('form#comic_create').find('input[name=title]').addClass('is-invalid');
                $('form#comic_create').find('input[name=title]').siblings().find('strong').html(e.responseJSON.title)
            }

            if(e.responseJSON.cover_photo!=undefined){
                $('form#comic_create').find('input[name=cover_photo]').addClass('is-invalid');
                $('form#comic_create').find('input[name=cover_photo]').siblings().find('strong').html(e.responseJSON.cover_photo)
            }

            if(e.responseJSON.pdf_file!=undefined){
                $('form#comic_create').find('input[name=pdf_file]').addClass('is-invalid');
                $('form#comic_create').find('input[name=pdf_file]').siblings().find('strong').html(e.responseJSON.pdf_file)
            }
            if(e.responseJSON.category_id!=undefined){
                $('form#comic_create').find('select[name="category_id"]').addClass('is-invalid');
                $('form#comic_create').find('select[name="category_id"] ').siblings().find('strong').html(e.responseJSON.category_id);
            }
            if(e.responseJSON.download_size!=undefined){
                $('form#comic_create').find('input[name="download_size"]').addClass('is-invalid');
                $('form#comic_create').find('input[name="download_size"] ').siblings().find('strong').html(e.responseJSON.download_size);
            }

            return false;
        }
        })
    });
    });


</script>
