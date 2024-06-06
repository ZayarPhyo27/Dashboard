<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    <script src="{{ asset('/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{url('js/ckeditor/ckeditor.js')}}"></script>


    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')
    <script src="{{ asset('js/md5.js') }}"></script>
    <script src="{{ asset('js/aes.js') }}"></script>

    {{-- Base Stylesheets --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

        @if(config('adminlte.google_fonts.allowed', true))
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        @endif
    @else
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @endif

    {{-- Extra Configured Plugins Stylesheets --}}
    @include('adminlte::plugins', ['type' => 'css'])

    {{-- Livewire Styles --}}
    @if(config('adminlte.livewire'))
        @if(app()->version() >= 7)
            @livewireStyles
        @else
            <livewire:styles />
        @endif
    @endif

    {{-- Custom Stylesheets (post AdminLTE) --}}
    @yield('adminlte_css')

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/logo.png') }}" />
    {{-- Favicon --}}
    @if(config('adminlte.use_ico_only'))
        {{-- <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/logo.svg') }}" />
        <link rel="icon" href="{{ asset('vendor/adminlte/dist/img/logo.jpg') }}"> --}}
        {{-- <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" /> --}}
    @elseif(config('adminlte.use_full_favicon'))
        <link rel="icon" href="{{ asset('vendor/adminlte/dist/img/logo.jpg') }}">
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicons/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicons/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicons/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicons/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicons/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicons/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicons/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicons/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('favicons/android-icon-192x192.png') }}">
        <link rel="manifest" crossorigin="use-credentials" href="{{ asset('favicons/manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script>
        function decryptData(encrypted){
            var key = "<?php echo config('app.key') ?>";
                key = key.replace('base64:','');
            encrypted = atob(encrypted);
            encrypted = JSON.parse(encrypted);
            const iv = CryptoJS.enc.Base64.parse(encrypted.iv);
            const value = encrypted.value;

            key = CryptoJS.enc.Base64.parse(key);
            var decrypted = CryptoJS.AES.decrypt(value, key, {
                iv: iv
            });

            decrypted = decrypted.toString(CryptoJS.enc.Utf8);
            return JSON.parse(decrypted);
        }
    </script>

</head>

<body class="@yield('classes_body')" @yield('body_data')>

    {{-- Body Content --}}
    @yield('body')

    {{-- Base Scripts --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @else
        <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    @endif

    {{-- Extra Configured Plugins Scripts --}}
    @include('adminlte::plugins', ['type' => 'js'])

    {{-- Livewire Script --}}
    @if(config('adminlte.livewire'))
        @if(app()->version() >= 7)
            @livewireScripts
        @else
            <livewire:scripts />
        @endif
    @endif

    {{-- Custom Scripts --}}
    @yield('adminlte_js')

    <!-- Modal -->
    <div class="modal fade" id="delete-confirm-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="delete-confirm-modal-Label" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="delete-confirm-modal-Label">Confirm</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
               <h5>Are you sure you want to delete?</h5>
            </div>
            <div class="modal-footer">
                <form action="" method="post" id="confirm-form">
                    @method('DELETE')
                    @csrf
                    <input type="hidden" name="current_index" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary confirm-process">Sure</button>
                </form>
            </div>
        </div>
        </div>
    </div>

     <!-- Modal -->
     <div class="modal fade" id="publish-confirm-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="publish-confirm-modal-Label" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="publish-confirm-modal-Label">Confirm</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
               <h5>Are you sure you want to publish?</h5>
            </div>
            <div class="modal-footer">
                <form action="" method="get" id="confirm-form">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="current_index" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary confirm-process">Sure</button>
                </form>
            </div>
        </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="active-confirm-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="active-confirm-modal-Label" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="active-confirm-modal-Label">Confirm</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
               <h5>Are you sure you want to activate?</h5>
            </div>
            <div class="modal-footer">
                <form action="" method="get" id="confirm-form">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="current_index" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary confirm-process">Sure</button>
                </form>
            </div>
        </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="deactive-confirm-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="deactive-confirm-modal-Label" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="deactive-confirm-modal-Label">Confirm</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
               <h5>Are you sure you want to de-activate?</h5>
            </div>
            <div class="modal-footer">
                <form action="" method="get" id="confirm-form">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="current_index" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary confirm-process">Sure</button>
                </form>
            </div>
        </div>
        </div>
    </div>


     <!-- Modal -->
     <div class="modal fade" id="push-confirm-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="push-confirm-modal-Label" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="push-confirm-modal-Label">Confirm</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
               <h5>Are you sure you want to push?</h5>
            </div>
            <div class="modal-footer">
                <form action="" method="get" id="confirm-form">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="current_index" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary confirm-process">Sure</button>
                </form>
            </div>
        </div>
        </div>
    </div>

</body>

</html>
