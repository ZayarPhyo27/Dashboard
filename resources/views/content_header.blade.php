<div class="row">
    <div class="col-md-6 col-sm-6 col-sm-12 col-xs-12">
        <h3 class="text-left">{{$title}}</h3>
    </div>
    <div class="col-md-6 col-sm-6 col-sm-12 col-xs-12">
        @isset($create_permission)
            @can($create_permission)
                <div class="text-right">
                    <a href="{{$create_url}}"> 
                        <x-adminlte-button label="Create {{ucwords($keyword)}}" theme="primary" icon="fas fa-plus"  /> 
                    </a>
                </div>
            @endcan
        @endisset
        
    </div>
</div><br>

@include('flash-message')