<input type="hidden" name="current_index" value="{{$article->current_index ?? 1}}">
<input type="hidden" name="article_id" value="{{$article->id}}">

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Article Title <span class="text-red">*</span></label>
            <input id="article_title" type="text" class="form-control" name="title" value="{{ $article->title }}" placeholder="Article Tilte">
            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{ __('Category') }} <span class="text-red">*</span></label>
            <select id="article-category" name="category_id" class="form-control">
                <option value="">Select Category</option>
                @foreach (config("web_constant.category_type") as $k=>$t)
               <option value="{{$k}}"  @if(old('category_id', $article->category_id)==$k) selected @endif>{{$t}}</option>
              @endforeach
            </select>
            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>
        </div>
    </div>


    <div class="col-md-4" >
        <label for="" style="display: block;">{{__('Cover Photo')}}
           <span class="font-weight-light">( 320 x 200px ) </span> <span class="text-red">* </span>
        </label>

        <div class="row preview-row" @if((isset($is_update) && count(old())==0) || old('virtual_img')!=null ) style="" @else style="display:none" @endif>
            <div class="col-md-10">
                <img id="upload-preview" src="{{ $article->cover_photo }}" alt="upload photo" style="width:100%"/>
            </div>
            <div class="col-md-1">
                <span class="remove-photo btn btn-link"><i class="fas fa-times "></i></span>
            </div>
        </div>
        <div class="row ">
            <input type="file" id="single-photo" class=" photo_file form-input form-control" name="cover_photo"
             placeholder="{{__('Upload Photo')}}" @if((isset($is_update) && count(old())==0) || old('virtual_img')!=null) style="display:none" @else style="" @endif>
            <input type="hidden" class="" >
            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>
            <input type="hidden" name="virtual_img" id="virtual-img" value="{{$article->cover_photo}}">
        </div>
    </div>

    <!-- <div class="col-md-12">
    <div class="form-group">
        <label>{{ __('Description') }} <span class="text-red">*</span></label>
        <textarea   class="form-control " name="description"
        rows="7" placeholder="Description">{{  $article->description }}</textarea>
        <span class="invalid-feedback" role="alert">
                    <strong></strong>
        </span>
    </div>
</div> -->

<div class="col-md-12" >
        <h5>Article Details</h5>
        <table class="table data-table table-bordered article-detail-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Article Detail</th>
                    <th style="width: 50px!important;" class="action-col"><span></span></th>
                </tr>
            </thead>
            <tbody>

                @if($article->details->count() == 0)
                    <tr class="update-article-detail">
                        <td><span class="tb-index">1</span></td>
                        <td>
                        <div class="row">
                                <div class="col-md-12">
                                    <label for="" class="mt-1">Description <span class="text-red">*</span></label>
                                    <textarea name="detail_description[]" class="form-control detail_description" id="detail_description_0" cols="30" rows="5"></textarea>
                                    <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="">Upload Photo
                                        <span class="font-weight-light">( 320 x 200px )</span><span class="photo_upload_description">( {{$upload_photo_description}} )</span></label>
                                    <div class="row preview-row" style="display:none">
                                        <div class="col-md-2">
                                            <img id="upload-preview" src="#" alt="upload photo" style="width:100%"/>
                                        </div>
                                        <div class="col-md-1">
                                            <span class="remove-photo btn btn-link"><i class="fas fa-times "></i></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <input type="file" id="single-photo" class=" photo_file form-input form-control" name="photo_path[]" placeholder="{{__('Upload Photo')}}">
                                        <input type="hidden" class="" >
                                        <span class="invalid-feedback" role="alert">
                                            <strong></strong>
                                        </span>

                                        <input type="hidden" name="detail_virtual_img[]" id="virtual-img" value="">
                                    </div>
                                </div>
                            </div>


                        </td>
                        <td>
                            <span class="btn remove-detail" style="display: none;"><i class="fa fa-times"></i></span>
                        </td>
                    </tr>
                @else
                    @foreach ($article->details as $k => $detail)

                        <tr class="update-article-detail">
                            <td><span class="tb-index">{{$k+1}}</span></td>
                            <td>
                            <div class="row">
                                  <div class="col-md-12">
                                        <label for="">Description <span class="text-red">*</span></label>
                                        <textarea name="detail_description[]" class="form-control detail_description" id="detail_description_{{$k}}" cols="30" rows="5">{{$detail->detail_description}}</textarea>
                                        <span class="invalid-feedback" role="alert">
                                            <strong></strong>
                                        </span>
                                  </div>
                               </div>
                               <div class="row">
                                   <div class="col-md-12">
                                        <label for="">Upload Photo
                                            <span class="font-weight-light">( 320 x 200px )</span> <span class="photo_upload_description">( {{$upload_photo_description}} )</span></label>
                                        <div class="row preview-row" @if(isset($is_update) && $detail->photo_path!=null) style="" @else style="display:none" @endif>
                                            <div class="col-md-2">
                                                <img id="upload-preview" src="{{$detail->photo_path}}" alt="upload photo" style="width:100%"/>
                                            </div>
                                            <div class="col-md-1">
                                                <span class="remove-photo btn btn-link"><i class="fas fa-times "></i></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <input type="file" id="single-photo" class=" photo_file form-input form-control" name="photo_path[]" placeholder="{{__('Upload Photo')}}" @if(isset($is_update) && $detail->photo_path!=null) style="display:none" @else style="" @endif>
                                            <input type="hidden" class="" >
                                            <span class="invalid-feedback" role="alert">
                                                <strong></strong>
                                            </span>

                                            <input type="hidden" name="detail_virtual_img[]" id="virtual-img" value="{{$detail->photo_path}}">
                                        </div><br>
                                   </div>
                               </div>


                            </td>
                            <td>
                                <span class="btn remove-detail" @if($loop->first) style="display: none;" @endif><i class="fa fa-times"></i></span>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><span class="btn btn-info btn-sm add-detail"><i class="fa fa-plus"></i></span></td>
                </tr>
            </tfoot>
        </table>

</div>

@include('save-btn')
<br>


<script>
$(document).ready(function(){
    $('.update-article-detail').each(function(i,e){
        CKEDITOR.replace('detail_description_'+i, {
            filebrowserUploadUrl: "{{ route('article.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form',
            image_previewText: ' '
        });
    });


    $('select[name="category_id"]').select2({
        placeholder : 'Select Category',
        allowClear : true
    });


    $('.save-btn').click(function(){
        var that = $(this);
        var formData = new FormData(document.getElementById("article-form"));
        var article_id = $('form#article-form').find('input[name=article_id]').val();
        if(article_id.length > 0){
            var route = '/article/'+article_id;
            formData.append('_method', 'PUT');
        }else if(article_id.length == 0){
            var route = '/article';
        }

        let detail_desc = "";
        $.each( CKEDITOR.instances, function(i,e) {
            detail_desc += e.getData()+"@#$%^&*";
        });


        formData.append('detail_desc', detail_desc);

        $.ajax({
            url : route,
            method : "POST",
            data : formData,
            dataType : 'json',
            cache : false,
            contentType : false,
            processData : false,
            headers : {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('article')
            },
            beforeSend : function(){
                clearErrorMsg();
            },
            success : function(data){
                window.location.href = '/article?index='+data.index;
            },
            complete : function (data) {

            },
            error: function(e){
                removeProcessing(that);
                if(e.responseJSON.category_id!=undefined){
                    $('form#article-form').find('select[name="category_id"]').addClass('is-invalid');
                    $('form#article-form').find('select[name="category_id"]').siblings().find('strong').html(e.responseJSON.category_id)
                }

                // if(e.responseJSON.description!=undefined){
                // $('form#article-form').find('textarea[name="description"]').addClass('is-invalid');
                // $('form#article-form').find('textarea[name="description"] ').siblings().find('strong').html(e.responseJSON.description);
                // }

                if(e.responseJSON.cover_photo!=undefined){
                    $('form#article-form').find('input[name="cover_photo"]').addClass('is-invalid');
                    $('form#article-form').find('input[name="cover_photo"]').siblings().find('strong').html(e.responseJSON.cover_photo)
                }

                if(e.responseJSON.title!=undefined){
                    $('form#article-form').find('input[name="title"]').addClass('is-invalid');
                    $('form#article-form').find('input[name="title"]').siblings().find('strong').html(e.responseJSON.title)
                }

                for(let i=0; i < $('table.article-detail-table > tbody > tr').length; i++ ){

                    if(e.responseJSON['photo_path.'+i]!=undefined){
                        $('table.article-detail-table > tbody > tr:eq('+i+') ').find('input[name="photo_path[]"]').addClass('is-invalid');
                        $('table.article-detail-table > tbody > tr:eq('+i+') ').find('input[name="photo_path[]"]').siblings().find('strong').html(e.responseJSON['photo_path.'+i]);
                    }

                    if(e.responseJSON['detail_description.'+0]!=undefined){
                        $('table.article-detail-table > tbody > tr:eq('+i+') ').find('textarea[name="detail_description[]"]').addClass('is-invalid');
                        $('table.article-detail-table > tbody > tr:eq('+i+') ').find('textarea[name="detail_description[]"]').siblings().find('strong').html(e.responseJSON['detail_description.'+i]);
                    }
                }

                return false;
            }
        })
    });


    $('.add-detail').click(function(){
        let tr = $('table.article-detail-table > tbody > tr:last').clone();
        $(tr).find('.remove-detail').css('display','');
        $(tr).find('textarea').val(null);
        $(tr).find('input').val(null);
        $(tr).find(' span.text-red').css('display','none');
        $(tr).find('input,textarea').removeClass('is-invalid');
        $(tr).find('.invalid-feedback strong').html(null);
        $(tr).find('.preview-row').css('display','none');
        $(tr).find('#upload-preview').attr('src','#');
        $(tr).find('#single-photo').css('display','block');

        let rm_ck = $(tr).find('.detail_description').attr('id');

        let len = $('table.article-detail-table > tbody > tr').length;
        $(tr).find('.tb-index').html(len+1);


        let rd_id = parseInt(Math.random() * 999);


        // $(tr).find('.detail_description').attr('name','detail_description_'+len+'[]');
        $(tr).find('.detail_description').attr('id','detail_description_'+rd_id);

        $('table.article-detail-table > tbody').append(tr);

        CKEDITOR.replace("detail_description_"+rd_id, {
            filebrowserUploadUrl: "{{ route('article.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form'
        });

        $('table.article-detail-table > tbody > tr:last').find('#cke_'+rm_ck).remove();
        $('table.article-detail-table > tbody > tr:last').find('#cke_detail_description_0').remove();

    });

    $(document).on('click','.remove-detail',function(){
        let k = $(this).parent().closest('tr').find('.detail_description').attr('id');
        $(this).parent().closest('tr').remove();

        $('table.article-detail-table > tbody > tr').each(function(i,e){
            let j = i+1;
            $(e).find('.tb-index').html(j);

        });
    });

});


</script>
