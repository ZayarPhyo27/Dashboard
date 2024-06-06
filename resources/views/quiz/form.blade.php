<input type="hidden" name="current_index" value="{{$quiz->current_index ?? 1}}">
<input type="hidden" name="quiz_id" value="{{$quiz->id}}">
<div class="row">
    <div class="col-md-7">
        <div class="form-group">
            <label>Category  <span class="text-red">*</span></label>
            <select class="form-control select2" name="content_type" style="width: 100%;">
                <option value=""></option>
                @foreach (config("web_constant.quiz_content_types") as $k => $t)
                    <option value="{{$k}}" @if($quiz->content_type==$k) selected @endif>{{$t}}</option>
                @endforeach
            </select>
            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>
        </div>
    </div>
</div>

<div class="article" style="display: none;">
    <div class="row">
        <div class="col-md-7">
            <label>{{ __('Article') }} <span class="text-red">*</span></label><br>
            <select name="select_article_id" class="form-control" style="width: 100%;">
                <option value="">Select Article</option>
                @foreach (\App\Models\Article::where('status',2)->get() as $c)
                    <option value="{{$c->id}}" data="{{Crypt::encryptString($c)}}">{{$c->title}}</option>
                @endforeach
            </select>
        </div>

    </div><br>

    <div class="row " >

       <div class="col-md-7">
            <h5>Article Lists</h5>
            <span class="invalid-feedback" role="alert">
                <strong></strong>
            </span>
            <table class="table data-table table-bordered faq-table">
                <thead>
                    <th style="width: 50px;">No.</th>
                    <th>Article</th>
                    <th style="width: 50px;" class="action-col"><span></span></th>
                </thead>
                <tbody>
                    @if ($quiz->articles->count() > 0)
                        @foreach ($quiz->articles as $k => $content)
                        <tr key="{{$content->article_id}}">
                            <td><span class="faq-index">{{$k+1}}</span></td>
                            <td>{{$content->title}}</td>
                            <td>
                                <span class="btn remove-faq">
                                    <i class="fa fa-times"></i>
                                </span>
                                <input type="hidden" name="article_id[]" value="{{ $content->article_id }}">
                            </td>
                        </tr>
                        @endforeach
                    @endif

                </tbody>

            </table>

            <div>
                <input type="hidden" name="" id="content_error">
                <span class="invalid-feedback" role="alert">
                    <strong></strong>
                </span>
            </div>
        </div>


    </div><br>
    <div class="row">
        <div class="col-md-6">
            <strong class="text-red quiz-err"></strong>
        </div>
        <div class="col-md-6">
            <strong class="text-red faq-err"></strong>
        </div>
    </div><br>
    </div>

<table class="table quizzes-list-table">
    <tbody>
      <tr>
        <td>
            <label for="">Quiz Type </label>
            <div class="row">
                @foreach (config("web_constant.quiz_types") as $type => $type_name)
                    <div class="col-md-2 q-type-{{$type}}">
                        <input type="radio" id="quiz_type" class="qtype-{{$type}}" value="{{$type}}" name="quiz_type[]" @if( $quiz->quiz_type==$type || ($quiz->quiz_type==null && $loop->first)) checked @endif> <span class="qType">{{$type_name}}</span>
                    </div>
                @endforeach
                <div class="col-md-2 remove-quiz"></div>
            </div><br>

            <div class="row">
                <div class="col-md-7">
                    <label for="">Quiz Question <span class="text-red">*</span><span class="photo_upload_description">( {{$quiz_question}} )</span></label>
                    <textarea  name="question[]" class="form-control" placeholder="Quiz Question" rows="3" maxlength="260">{{$quiz->question}}</textarea>
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-7 option_names">
                    <div class="true_false" >
                        @if($quiz->options!=null && $quiz->quiz_type==1)
                            @foreach ($quiz->options as $opt)
                                <div style="display: flex;" class="mt-2">
                                    <input type="radio" id="is_correct" name="is_correct[]" value="{{$opt->is_correct}}" @if($opt->is_correct) checked @endif>
                                    <input type="text" class="form-control ml-1 quiz-options t_f" name="option_name[]" value="{{$opt->option_name}}" readonly>
                                    <input type="hidden" name="option_id[]" value="{{$opt->id}}">
                                </div>
                            @endforeach
                        @else
                            <div style="display: flex;">
                                <input type="radio" id="is_correct" name="is_correct[]" value="1" checked>
                                <input type="text" class="form-control ml-1  quiz-options t_f" name="option_name[]" value="TRUE (မှန်)" readonly>
                                <input type="hidden" name="option_id[]" value="">
                            </div>
                            <div style="display: flex;" class="mt-3">
                                <input type="radio" id="is_correct" name="is_correct[]" value="0">
                                <input type="text" class="form-control ml-1  quiz-options t_f" name="option_name[]" value="FALSE (မှား)" readonly>
                                <input type="hidden" name="option_id[]" value="">
                            </div>
                        @endif

                    </div>

                    <div class="single_choice" style="display: none;">
                        @if($quiz->options!=null && ($quiz->quiz_type==2 || $quiz->quiz_type==4))
                            @foreach ($quiz->options as $opt)
                                <div class="options">
                                    <div class="sc" style="display: flex;">
                                        <input type="radio" id="is_correct" name="is_correct[]" value="{{$opt->is_correct}}" @if($opt->is_correct) checked @endif>
                                        <input type="text" class="form-control ml-1 quiz-options" name="option_name[]" value="{{$opt->option_name}}" placeholder="Quiz Option" maxlength="25">
                                        @if(!$loop->first)
                                            <span class="btn remove-option"><i class="fa fa-times"></i></span>
                                        @endif
                                    </div>
                                    <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                                    <input type="hidden" name="option_id[]" value="{{$opt->id}}">
                                </div>
                            @endforeach
                        @else
                            <div class="options">
                                <div style="display: flex;"  class="sc">
                                    <input type="radio" id="is_correct" name="is_correct[]" value="1">
                                    <input type="text" class="form-control ml-1  quiz-options" name="option_name[]" value="" placeholder="Quiz Option" maxlength="25">
                                </div>
                                <span class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </span>
                                <input type="hidden" name="option_id[]" value="">
                            </div>
                        @endif
                    </div>
                    <div class="multiple_choice" style="display: none;">
                        @if($quiz->options!=null && $quiz->quiz_type==3)
                            @foreach ($quiz->options as $opt)
                                <div class="options">
                                    <div style="display: flex;" class="mc">
                                        <input type="checkbox" id="is_correct" name="is_correct[]" value="{{$opt->is_correct}}" @if($opt->is_correct) checked @endif>
                                        <input type="text" class="form-control ml-1  quiz-options" name="option_name[]" value="{{$opt->option_name}}" placeholder="Quiz Option" maxlength="25">
                                        @if(!$loop->first)
                                        <span class="btn remove-option"><i class="fa fa-times"></i></span>
                                        @endif
                                    </div>
                                    <span class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </span>
                                    <input type="hidden" name="option_id[]" value="{{$opt->id}}">
                                </div>
                            @endforeach
                        @else
                            <div class="options">
                                <div style="display: flex;" class="mc">
                                    <input type="checkbox" id="is_correct" name="is_correct[]" value="1" checked>
                                    <input type="text" class="form-control ml-1  quiz-options" name="option_name[]" value="" placeholder="Quiz Option" maxlength="25">
                                </div>
                                <span class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </span>
                                <input type="hidden" name="option_id[]" value="">
                            </div>

                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <input type="hidden" name="other_validation_err">
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <span class="btn btn-sm btn-info float-right mt-2 add-option" style="display: none;">
                        <i class="fa fa-plus"></i>
                    </span>
                </div>
            </div><br>

            <div class="row">
                <div class="col-md-7">
                    <label for="">Answer Description <span class="photo_upload_description">( {{$answer_description}} )</span></label>
                    <textarea name="answer_description[]" class="form-control" placeholder="Answer Description" rows="5" maxlength="200">{{$quiz->answer_description}}</textarea>
                    <span class="invalid-feedback" role="alert">
                        <strong></strong>
                    </span>
                </div>
            </div><br>
          </td>
        </tr>
    </tbody>
    <tfoot>
        @if($quiz->id==null)
            <tr>
                <td class="text-right">
                    <span class="btn btn-sm btn-info float-right mt-2 add-quiz">
                        <i class="fa fa-plus"></i>
                    </span>
                </td>
            </tr>
        @endif
    </tfoot>
</table>

@include('save-btn')

<script>
$(document).ready(function(){

    checkContentType();
    $('select[name=content_type]').change(function(){
        checkContentType();
    });
    $('select[name="select_article_id"]').select2({
        placeholder : 'Select article',
        allowClear : true
    });

    $('.add-quiz').click(function(){
        let tr = $('table.quizzes-list-table > tbody > tr:last').clone();
        let rd_id = parseInt(Math.random() * 999);
        $(tr).find('#quiz_type').attr('name','quiz_type_'+rd_id);
        $(tr).find('#is_correct').attr('name','is_correct_'+rd_id+"[]");
        $(tr).find('.quiz-options').attr('name','option_name_'+rd_id+'[]');
        $(tr).find('input:not(".t_f,#quiz_type"),textarea').val(null);
        $(tr).find('#is_correct').val(0);
        $(tr).find('input[type=radio],input[type=checkbox]').prop('checked',false);
        $(tr).find('#quiz_type:first').prop('checked',true);
        $(tr).find('input,textarea').removeClass('is-invalid');
        $(tr).find('.invalid-feedback strong').html(null);
        $(tr).find('.remove-quiz').html('<span class="btn btn-sm btn-link float-right mt-2 rmq"> <i class="fa fa-times"></i></span>')

        $('table.quizzes-list-table > tbody').append(tr);

        checkQuizType();

    });

    $(document).on('click','.rmq',function(){
        $(this).closest('tr').remove();
    });

    function checkContentType(){
        if($('select[name=content_type] option:selected').val()==1){
            $('.article').css('display','none');

        }else if($('select[name=content_type] option:selected').val()==2){
            $('.article').css('display','block');
        }
    }
    $('select[name=select_article_id]').change(function(){
        let data = decryptData($('select[name=select_article_id] option:selected').attr('data'));
        $('select[name=select_article_id]').val(null).trigger('change.select2');

        if($('table.faq-table > tbody > tr[key='+data.id+']').length > 0) return false;

        let k = $('table.faq-table > tbody > tr').length + 1;

        let tr = "<tr class='tr' key='"+data.id+"'>"+
                    "<td><span class='faq-index'>"+k+"</span></td>"+
                    "<td>"+data.title+"</td>"+
                    "<td><span class='btn remove-faq'><i class='fa fa-times'></i></span><input type='hidden' class='article_id' name='article_id[]' value='"+data.id+"'></td>"+
                 "</tr>";
        $('table.faq-table > tbody').append(tr);
        $('.faq-err').html(null);
    });


    $(document).on('click','.remove-faq',function(){
       $(this).parent().closest('tr').remove();
       $('table.faq-table > tbody > tr').each(function(i,e){
            $(e).find('.faq-index').html(i+1);
        });
    });

    checkQuizType();
    checkGame();
    $('select[name=content_type]').select2({ placeholder : "Select Category", allowClear : true});

    $(document).on('click','.add-option',function(){
        let rd_id = parseInt(Math.random() * 999);
        let tr = $(this).closest('tr');
        let rename =  $(tr).find('#is_correct:last').attr('name');
        // let rename = 'is_correct_'+rd_id+"[]";
        // $(tr).find('#is_correct').attr('name',rename);

        let quizType = $(tr).find('#quiz_type:checked').val();
        // alert(quizType);

        if (quizType === '2') { // Single choice
            let optionsCount = $(tr).find('.single_choice .options').length;

            if (optionsCount > 3) {
                alert('You can only have a maximum of 4 options.');
                return; // Prevent adding more options if the limit is reached
            }

            // Rest of the code to add a new single choice option
        } else if (quizType === '3') { // Multiple choice
            let optionsCountMultiple = $(tr).find('.multiple_choice .options').length;

            if (optionsCountMultiple > 3) {
                alert('You can only have a maximum of 4 options.');
                return; // Prevent adding more options if the limit is reached
            }
        }

        if($(tr).find('#quiz_type:checked').val()==2 || $(tr).find('#quiz_type:checked').val()==4){ //if single choice
            let new_option = '<div class="mt-2 options">'+
                                '<div style="display: flex;" class="sc">'+
                                    '<input type="radio" id="is_correct" value="0" name="'+rename+'">'+
                                    '<input type="text" class="form-control ml-1 quiz-options" name="option_name[]" value="" placeholder="Quiz Option" maxlength="25">'+
                                    '<span class="btn remove-option"><i class="fa fa-times"></i></span>'+
                                '</div>'+
                                '<span class="invalid-feedback" role="alert">'+
                                    '<strong></strong>'+
                                '</span>'+
                                '<input type="hidden" name="option_id[]" value="">'+
                             '</div>';

            $(tr).find('.single_choice').append(new_option);
        }else if($(tr).find('#quiz_type:checked').val()==3){ //if multiple choice
            let new_option = '<div class="mt-2 options">'+
                                '<div  style="display: flex;" class="mc">'+
                                    '<input type="checkbox" id="is_correct" value="0" name="'+rename+'">'+
                                    '<input type="text" class="form-control ml-1 quiz-options" name="option_name[]" value="" placeholder="Quiz Option" maxlength="25">'+
                                    '<span class="btn remove-option"><i class="fa fa-times"></i></span>'+
                                '</div>'+
                                '<span class="invalid-feedback" role="alert">'+
                                    '<strong></strong>'+
                                '</span>'+
                                '<input type="hidden" name="option_id[]" value="">'+
                            '</div>';

            $(tr).find('.multiple_choice').append(new_option);
        }

        $(tr).find('input[name=other_validation_err]').removeClass('is-invalid');
        $(tr).find('input[name=other_validation_err]').siblings().find('strong').html(null);
    });

    $(document).on('click','#quiz_type',function(){
        checkQuizType();
    });

    $('select[name=content_type]').change(function(){
        $(".tr").remove();
        checkGame();
    });

    $(document).on('click','.remove-option',function(){
        $(this).parent().parent().remove();
    });

    $(document).on('click','#is_correct',function(){
        let name = $(this).attr('name');
        if($(this).attr('type')=="radio"){
            $('input[name="'+name+'"]').val(0);
            $(this).val(1);
            $('input[name=other_validation_err]').removeClass('is-invalid');
            $('input[name=other_validation_err]').siblings().find('strong').html(null);
        }else{
            if($(this).is(':checked')){
                $(this).val(1);
                $('input[name=other_validation_err]').removeClass('is-invalid');
                $('input[name=other_validation_err]').siblings().find('strong').html(null);
            }else $(this).val(0);
        }
    });

    $('.save-btn').click(function(){
        var that = $(this);
        var formData = new FormData(document.getElementById("quiz-form"));
        var quiz_id = $('form#quiz-form').find('input[name=quiz_id]').val();
        if(quiz_id.length > 0){
            var route = '/quiz/'+quiz_id;
            formData.append('_method', 'PUT');
        }else if(quiz_id.length == 0){
            var route = '/quiz';
        }

        let option_name = [];
        let option_id = [];
        let is_correct = [];
        let option_names = [];
        let option_ids = [];
        let is_corrects = [];
        let quiz_types = [];
        let valid = true;
        $('table.quizzes-list-table > tbody > tr').each(function(i,el){
            let correctAns = 0;
            quiz_types.push($(el).find('#quiz_type:checked').val());
            if($(el).find('#quiz_type:checked').val()==1){
                $(el).find('.true_false div').each(function(i,e){
                    let opt = $(e).find('.quiz-options').val();
                    let is_ans = $(e).find('#is_correct').val();
                    let opt_id = $(e).find('input[name="option_id[]"]').val();

                    option_name.push(opt);
                    is_correct.push(is_ans);
                    option_id.push(opt_id);

                    if($(e).find('#is_correct').is(':checked')) correctAns=1;
                });
            }else if($(el).find('#quiz_type:checked').val()==2 || $(el).find('#quiz_type:checked').val()==4){
                $(el).find('.single_choice div.options').each(function(i,e){

                    let opt = $(e).find('.quiz-options').val();
                    let is_ans = $(e).find('#is_correct').val();
                    let opt_id = $(e).find('input[name="option_id[]"]').val();

                    option_name.push(opt);
                    is_correct.push(is_ans);
                    option_id.push(opt_id);
                    if($(e).find('#is_correct').is(':checked')) correctAns=1;
                });
            }else if($(el).find('#quiz_type:checked').val()==3){
                $(el).find('.multiple_choice div.options').each(function(i,e){
                    let opt = $(e).find('.quiz-options').val();
                    let is_ans = $(e).find('#is_correct').val();
                    let opt_id = $(e).find('input[name="option_id[]"]').val();

                    option_name.push(opt);
                    is_correct.push(is_ans);
                    option_id.push(opt_id);

                    if($(e).find('#is_correct').is(':checked')) correctAns=1;
                });
            }

            option_names[i] = option_name;
            option_ids[i] = option_id;
            is_corrects[i] = is_correct;
            if(($(el).find('#quiz_type:checked').val()==3 || $(el).find('#quiz_type:checked').val()==4) && option_name.length < 3 ){
                $(el).find('input[name=other_validation_err]').addClass('is-invalid');
                $(el).find('input[name=other_validation_err]').siblings().find('strong').html('The quiz option must be at least 3 options.');
                valid = false;
            }
            if(($(el).find('#quiz_type:checked').val()==2) && option_name.length < 2 ){
                $(el).find('input[name=other_validation_err]').addClass('is-invalid');
                $(el).find('input[name=other_validation_err]').siblings().find('strong').html('The quiz option must be at least 2 options.');
                valid = false;
            }
            if( correctAns==0){
                $(el).find('input[name=other_validation_err]').addClass('is-invalid');
                $(el).find('input[name=other_validation_err]').siblings().find('strong').html('The correct answer must be at least one option.');
                valid = false;
            }

            option_name = ['&&&'];
            option_id = ['&&&'];
            is_correct = ['&&&'];

        });

        if(!valid) return false;

        formData.append('option_name', option_names);
        formData.append('is_correct', is_corrects);
        formData.append('option_id', option_ids);
        formData.append('quiz_type', quiz_types);

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
                window.location.href = '/quiz?index='+data.index+'&type='+data.type;
            },
            complete : function (data) {

            },
            error: function(e){
                removeProcessing(that);
                if(e.responseJSON.content_type!=undefined){
                    $('form#quiz-form').find('select[name=content_type]').addClass('is-invalid');
                    $('form#quiz-form').find('select[name=content_type]').siblings().find('strong').html(e.responseJSON.content_type)
                }

                if(e.responseJSON.article_id!=undefined){
                    $('form#quiz-form').find('#content_error').addClass('is-invalid');
                    $('form#quiz-form').find('#content_error').siblings().find('strong').html(e.responseJSON.article_id)
                }

                $('table.quizzes-list-table > tbody > tr').each(function(k,el){
                    console.log(e.responseJSON['question.'+0]);
                    if(e.responseJSON['question.'+k]!=undefined){
                        $(el).find('textarea[name="question[]"]').addClass('is-invalid');
                        $(el).find('textarea[name="question[]"]').siblings().find('strong').html(e.responseJSON['question.'+k])
                    }

                    if(e.responseJSON['description.'+k]!=undefined){
                        $(el).find('textarea[name=description]').addClass('is-invalid');
                        $(el).find('textarea[name=description]').siblings().find('strong').html(e.responseJSON['description.'+k])
                    }

                    if($(el).find('#quiz_type:checked').val()==1){
                        for(let i=0; i < $(el).find('.true_false div').length; i++ ){
                            if(e.responseJSON['option_name.'+k+'.'+i]!=undefined){
                                $(el).find('.true_false div:eq('+i+') ').find('.option_names').addClass('is-invalid');
                                $(el).find('.true_false div:eq('+i+') ').find('.option_names').siblings().find('strong').html(e.responseJSON['option_name.'+k+'.'+i]);
                            }
                        }
                    }else if($(el).find('#quiz_type:checked').val()==2 || $(el).find('#quiz_type:checked').val()==4){
                        for(let i=0; i < $(el).find('.single_choice div.options').length; i++ ){
                            if(e.responseJSON['option_name.'+k+'.'+i]!=undefined){
                                $(el).find('.single_choice div.options:eq('+i+') ').find('div.sc').addClass('is-invalid');
                                $(el).find('.single_choice div.options:eq('+i+') ').find('strong').html(e.responseJSON['option_name.'+k+'.'+i]);
                            }
                        }
                    }else if($(el).find('#quiz_type:checked').val()==3){
                        for(let i=0; i < $(el).find('.multiple_choice div.options').length; i++ ){
                            if(e.responseJSON['option_name.'+k+'.'+i]!=undefined){
                                $(el).find('.multiple_choice div.options:eq('+i+') ').find('div.mc').addClass('is-invalid');
                                $(el).find('.multiple_choice div.options:eq('+i+') ').find('strong').html(e.responseJSON['option_name.'+k+'.'+i]);
                            }
                        }
                    }
                })

                return false;
            }
        })
    });
});

function checkQuizType(){
    $('table.quizzes-list-table > tbody > tr').each(function(i,e){
        if($(e).find('#quiz_type:checked').val()==1){
            $(e).find('.true_false').css('display','');
            $(e).find('.add-option').css('display','none');
            $(e).find('.single_choice').css('display','none');
            $(e).find('.multiple_choice').css('display','none');
        }else if($(e).find('#quiz_type:checked').val()==2 || $(e).find('#quiz_type:checked').val()==4){
            $(e).find('.true_false').css('display','none');
            $(e).find('.single_choice').css('display','');
            $(e).find('.multiple_choice').css('display','none');
            $(e).find('.add-option').css('display','');
        }else{
            $(e).find('.true_false').css('display','none');
            $(e).find('.single_choice').css('display','none');
            $(e).find('.multiple_choice').css('display','');
            $(e).find('.add-option').css('display','');
        }
    });
}
function checkGame(){
    if($('select[name=content_type]').val()==3){
        $('.q-type-3').css('display','none');
        if($('#quiz_type:checked').val()==3){
            $("input[name='quiz_type'][value=1]").prop('checked',true);
            $('.q-type-3').css('display','none');
            checkQuizType();
        }

    }else {
        $('.q-type-3').css('display','block');
    }

}



</script>
