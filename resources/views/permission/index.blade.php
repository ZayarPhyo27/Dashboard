@extends('adminlte::page')
@section('title', 'Dashboard')
@section('content_header')
    @include('content_header')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
     <nav>
        <div class="nav nav-tabs role-permission-tab" id="nav-tab" role="tablist">
            @foreach($roles as $role)
              @if($role->id==1 || $role->id==2 )
                 <a class="nav-link @if($role->id==$role_id) active @endif" id="nav-{{$role->id}}-tab" data-toggle="tab" href="#nav-{{$role->id}}" role="tab" aria-controls="nav-{{$role->id}}" aria-selected="true">{{$role->name}}</a>
              @endif
            @endforeach
        </div>
      </nav>
      <div class="tab-content" id="nav-tabContent">
        @foreach($roles as $role)
            @php
                $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$role->id)
                                        ->leftJoin('permissions','permissions.id','role_has_permissions.permission_id')
                                        ->pluck('permissions.name')
                                        ->all();
            @endphp
            <div class="tab-pane fade @if($role->id == $role_id) show active @endif" id="nav-{{$role->id}}" role="tabpanel" aria-labelledby="nav-{{$role->id}}-tab">
                <div class="row" style="margin: 1% 0;">
                    <div class="col-md-2 RS_all">
                        <input type="checkbox" name="selectAll" id="selectAll">&nbsp; Select All
                    </div>
                </div>
                {!! Form::model($role, ['method' => 'POST','route' => ['permission.store'], 'class' => 'permission-form']) !!}
                    @csrf
                    @method('POST')
                    <input type="hidden" name="role_id" value="{{$role->id}}">
                    <div class="row" style="margin: 0;">
                        <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">Role Permission</h6>
                            <span>{{ Form::checkbox('permission[]', 'permission-list', in_array('permission-list', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                  {{ "permission-list" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'permission-create', in_array('permission-create', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                  {{ "role-permission-update" }}
                            </span>
                        </div>

                        {{-- <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">General Setting</h6>
                            <span>{{ Form::checkbox('permission[]', 'setting-list', in_array('setting-list', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                  {{ "view-setting" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'setting-create', in_array('setting-create', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                  {{ "update-setting" }}
                            </span>
                        </div> --}}

                        <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">User Management</h6>
                            <span>{{ Form::checkbox('permission[]', 'user-list', in_array('user-list', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "user-list" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'user-create', in_array('user-create', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "user-create" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'user-edit', in_array('user-edit', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "user-edit" }}
                            </span>
                            {{-- <span>{{ Form::checkbox('permission[]', 'user-block', in_array('user-block', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "user-block/unblock" }}
                            </span> --}}
                            <span>{{ Form::checkbox('permission[]', 'user-view', in_array('user-view', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "user-view" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'user-delete', in_array('user-delete', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "user-delete" }}
                            </span>

                        </div>

                        <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">Video Management </h6>
                            <span>{{ Form::checkbox('permission[]', 'video-list', in_array('video-list', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "video-list" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'video-create', in_array('video-create', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "video-create" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'video-edit', in_array('video-edit', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "video-edit" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'video-publish', in_array('video-publish', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "video-publish" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'video-active', in_array('video-active', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "video-active/deactive" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'video-delete', in_array('video-delete', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "video-delete" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'video-view', in_array('video-view', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "video-view" }}
                            </span>

                        </div>


                        <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">Quiz Management</h6>
                            <span>{{ Form::checkbox('permission[]', 'quiz-list', in_array('quiz-list', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "quiz-list" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'quiz-create', in_array('quiz-create', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "quiz-create" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'quiz-edit', in_array('quiz-edit', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "quiz-edit" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'quiz-publish', in_array('quiz-publish', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "quiz-publish" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'quiz-active', in_array('quiz-active', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "quiz-active/deactive" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'quiz-view', in_array('quiz-view', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "quiz-view" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'quiz-delete', in_array('quiz-delete', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "quiz-delete" }}
                            </span>

                        </div>

                        <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">Podcast Managemnt </h6>
                            <span>{{ Form::checkbox('permission[]', 'podcast-list', in_array('podcast-list', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "podcast-list" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'podcast-create', in_array('podcast-create', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "podcast-create" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'podcast-edit', in_array('podcast-edit', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "podcast-edit" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'podcast-publish', in_array('podcast-publish', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "podcast-publish" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'podcast-active', in_array('podcast-active', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "podcast-active/deactive" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'podcast-view', in_array('podcast-view', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "podcast-view" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'podcast-delete', in_array('podcast-delete', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "podcast-delete" }}
                            </span>
                        </div>

                        <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">Comic Management</h6>
                            <span>{{ Form::checkbox('permission[]', 'comic-list', in_array('comic-list', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "comic-list" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'comic-create', in_array('comic-create', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "comic-create" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'comic-edit', in_array('comic-edit', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "comic-edit" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'comic-publish', in_array('comic-publish', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "comic-publish" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'comic-active', in_array('comic-active', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "comic-active/deactive" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'comic-view', in_array('comic-view', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "comic-view" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'comic-delete', in_array('comic-delete', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "comic-delete" }}
                            </span>
                        </div>

                        <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">Article Management </h6>
                            <span>{{ Form::checkbox('permission[]', 'article-list', in_array('article-list', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "article-list" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'article-create', in_array('article-create', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "article-create" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'article-edit', in_array('article-edit', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "article-edit" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'article-publish', in_array('article-publish', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "article-publish" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'article-active', in_array('article-active', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "article-active/deactive" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'article-delete', in_array('article-delete', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "article-delete" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'article-view', in_array('article-view', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "article-view" }}
                            </span>
                        </div>

                        <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">Feedback Management </h6>
                            <span>{{ Form::checkbox('permission[]', 'feedback-list', in_array('feedback-list', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "feedback-list" }}
                            </span>
                        </div>

                        {{-- <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">Reports</h6>
                            <span>{{ Form::checkbox('permission[]', 'user-info-report', in_array('user-info-report', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "user-information-report" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'monthly-user-report', in_array('monthly-user-report', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "monthly-user-report" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'monthly-course-report', in_array('monthly-course-report', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "monthly-course-report" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'monthly-service-report', in_array('monthly-service-report', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "monthly-service-report" }}
                            </span>

                        </div> --}}




                    <!-- <div class="col-md-2 RS_role">
                            <h6 > <input type="checkbox" id="select_module"  name="select_module">Notification</h6>
                            <span>{{ Form::checkbox('permission[]', 'notification', in_array('notification', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "notification" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'notification-create', in_array('notification-create', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "notification-create" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'notification-update', in_array('notification-update', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "notification-update" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'notification-delete', in_array('notification-delete', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "notification-delete" }}
                            </span>
                            <span>{{ Form::checkbox('permission[]', 'notification-push', in_array('notification-push', $rolePermissions) ? true : false, array('class' => 'name')) }}
                                {{ "notification-push" }}
                            </span>

                    </div> -->

                    </div><br>

                    @include('save-btn')
                </form>
          </div>
        @endforeach
        </div>
      </div>
   </div>

<script>
    $(document).ready(function(){
            load();

            checkPermission();

            $('input[name=selectAll]').click(function(){
                if($(this).is(":checked"))
                    $(this).parent().parent().parent().find('input[type=checkbox]').prop('checked',true);
                else  $(this).parent().parent().parent().find('input[type=checkbox]').prop('checked',false);
            });

            $('input[type=checkbox]').click(function(){
                    if(!$(this).is(":checked")){
                        $('input[name=selectAll]').prop('checked',false);
                        $(this).parent().parent().find('h6').find('input[type=checkbox]').prop('checked',false);
                    }else{
                           if($(this).parent().parent().find('input:checkbox:not(:checked):not(#select_module)').length ==0)
                              $(this).parent().parent().find('h6').find('input[type=checkbox]').prop('checked',true);
                    }
            });

            $(document).on('click','#select_module',function(){
                if($(this).is(':checked')){
                    $(this).parent().parent().find('input[type=checkbox]').prop('checked',true);
                }else{
                    $(this).parent().parent().find('input[type=checkbox]').prop('checked',false);
                }

                checkPermission();
            });

            $('.save-btn').click(function(){
                $(this).parent().parent().parent().submit();
            })
        });

        function load(){
              $.each($('input[name=select_module]'),function(i,e){
                  if($(e).parent().parent().find('input:checkbox:not(:checked):not(#select_module)').length ==0){
                        $(e).prop('checked',true);
                  }
              });

              checkPermission();
        }

        function checkPermission(){
            if($("#nav-1").find('input[name=select_module]:not(:checked)').length==0)
               $("#nav-1").find('#selectAll').prop('checked',true);

            if($("#nav-2").find('input[name=select_module]:not(:checked)').length==0)
               $("#nav-2").find('#selectAll').prop('checked',true);

            if($("#nav-3").find('input[name=select_module]:not(:checked)').length==0)
               $("#nav-3").find('#selectAll').prop('checked',true);

            if($("#nav-4").find('input[name=select_module]:not(:checked)').length==0)
               $("#nav-4").find('#selectAll').prop('checked',true);

            if($("#nav-5").find('input[name=select_module]:not(:checked)').length==0)
               $("#nav-5").find('#selectAll').prop('checked',true);

            if($("#nav-6").find('input[name=select_module]:not(:checked)').length==0)
               $("#nav-6").find('#selectAll').prop('checked',true);

            if($("#nav-7").find('input[name=select_module]:not(:checked)').length==0)
               $("#nav-7").find('#selectAll').prop('checked',true);

        }
</script>
@stop
