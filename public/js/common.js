$(document).on('click','.edit-data, .publish-content, .hide-content',function(){
    let action = $(this).attr('action');
    let index = $(this).parent().parent().closest('tr').find('.data-index').html();
    $(this).attr('href',action+"?index="+index);
});

$(document).on('click','.save-btn', function(){
    $(this).addClass('disabled');
    $(this).html('Processing...');
});

$(document).on('click','.confirm-delete',function(){
    $(this).parent().submit();
});

$(document).on('click','.delete-data', function(){

   let action = $(this).attr('action');
   let index = $(this).parent().parent().parent().closest('tr').find('.data-index').html();
   $('#delete-confirm-modal .modal-footer #confirm-form').find('input[name=current_index]').val(index);
   $('#delete-confirm-modal .modal-footer #confirm-form').attr('action',action);
   $('#delete-confirm-modal').modal('show');
});

$(document).on('click','.publish-data', function(){

    let action = $(this).attr('action');
    let index = $(this).parent().parent().parent().closest('tr').find('.data-index').html();
    $('#publish-confirm-modal .modal-footer #confirm-form').find('input[name=current_index]').val(index);
    $('#publish-confirm-modal .modal-footer #confirm-form').attr('action',action);
    $('#publish-confirm-modal').modal('show');
 });

 $(document).on('click','.active-data', function(){

    let action = $(this).attr('action');
    let index = $(this).parent().parent().parent().closest('tr').find('.data-index').html();
    $('#active-confirm-modal .modal-footer #confirm-form').find('input[name=current_index]').val(index);
    $('#active-confirm-modal .modal-footer #confirm-form').attr('action',action);
    $('#active-confirm-modal').modal('show');
 });

 $(document).on('click','.push-data', function(){

    let action = $(this).attr('action');
    let index = $(this).parent().parent().parent().closest('tr').find('.data-index').html();
    $('#push-confirm-modal .modal-footer #confirm-form').find('input[name=current_index]').val(index);
    $('#push-confirm-modal .modal-footer #confirm-form').attr('action',action);
    $('#push-confirm-modal').modal('show');

 });

 $(document).on('click','.deactive-data', function(){

    let action = $(this).attr('action');
    let index = $(this).parent().parent().parent().closest('tr').find('.data-index').html();
    $('#deactive-confirm-modal .modal-footer #confirm-form').find('input[name=current_index]').val(index);
    $('#deactive-confirm-modal .modal-footer #confirm-form').attr('action',action);
    $('#deactive-confirm-modal').modal('show');
 });

 $(document).on('click','.confirm-process', function(){
       $(this).parent().submit();
 });

function removeProcessing(that){
   $(that).removeClass('disabled');
   $(that).html('Save');
}

function clearErrorMsg(){
    $(document).find('.invalid-feedback strong').html(null);
    $(document).find('form.data-form input').removeClass('is-invalid');
    $(document).find('form.data-form select').removeClass('is-invalid');
    $(document).find('form.data-form textarea').removeClass('is-invalid');
}

$(document).on("change","input[type=file]",function(){
    readURL(this);
});

$(document).on("change","#video_file",function(){
    $('#video_path_row').hide();
    $('#virtual-video').val(null);
});
$(document).on("change","#audio_file",function(){
    $('#video_path_row').hide();
    $('#virtual-audio').val(null);
});

$(document).on("change","#pdf_file",function(){
    $('#video_path_row').hide();
    $('#virtual-pdf').val(null);
});

$(document).on("click",".remove-photo",function(){
    $(this).parent().parent().parent().find('input[type=file]').val(null);
    $(this).parent().parent().find("#upload-preview").attr('src','');
    $(this).parent().parent().css('display','none');
    $(this).parent().parent().parent().find('#virtual-img').val(null);
    $(this).parent().parent().parent().find('input[type=file]').css('display','block');
    $(this).parent().parent().parent().find('img.img_none').removeClass('img_none');
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        console.log(input.files[0]);
        if((input.files[0].size / 1024) > (20 * 1024)){
           $(input).val(null);
           alert('The uploaded file is exceed 20MB. ');
           return false;
        }else{
            reader.onload = function (e) {
                $(input).parent().parent().find('#upload-preview').attr('src', e.target.result);
                $(input).parent().parent().find('.preview-row').css('display','');
                if($(input).parent().parent().find('.preview-row').length > 0){
                    $(input).css('display','none');
                }
                $(input).siblings().find('strong').html(null);
                if($(input).parent().parent().find('input[name="preview[]"')!=undefined){
                    $(input).parent().parent().find('input[name="preview[]"').val(e.target.result)
                }
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
}

