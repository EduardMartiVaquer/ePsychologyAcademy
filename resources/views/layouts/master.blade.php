<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }}</title>
    <meta name="description" content="{{ __('index.description') }}">

    <!-- Favicon -->
    @include('layouts.favicon')

    <!-- Style -->
    <link rel="preload" href="/webfonts/fa-brands-400.woff2" as="font" crossorigin>
    <link rel="preload" href="/webfonts/fa-solid-900.woff2" as="font" crossorigin>
    
    <link rel="preload" href="/fonts/HelveticaNeue/HelveticaNeueRegular.ttf" as="font" crossorigin>
    <link rel="preload" href="/fonts/HelveticaNeue/HelveticaNeueMedium.ttf" as="font" crossorigin>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google -->
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id" content="{{ env('GOOGLE_CLIENT_ID') }}">

    <script>
        window.FontAwesomeConfig = { autoReplaceSvg: 'nest' }
        
        //CSRF Token
        window.Laravel = {'csrfToken': "{{ csrf_token() }}"};

        //Reset token for password recover
        window.resetToken = "{{ $resetToken }}";
        window.oldEmail = "{{ $oldEmail }}";

        //Lang and localization files
        window.lang = '{{ App::getLocale() }}';
        window.fallback_locale = "{{ config('app.fallback_locale') }}";
    </script>
    <link async defer href="{{ mix('/css/app.css') }}" rel="stylesheet" as="style" type="text/css">
</head>
<body>
    @include('layouts.index')
    
    @include('footer')
    
    <script src="{{ mix('/js/app.js') }}"></script>
    
</body>
</html>