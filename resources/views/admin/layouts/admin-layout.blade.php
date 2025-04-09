<!doctype html>
<html lang="ko" class="root-text-sm">

<head>
    @include('admin.layouts.components.baseHead')
</head>

<body style="">
<div class="wrap admin">
    @include('admin.layouts.include.header')

    <section id="container" class="inner-layer">
        @yield('contents')
    </section>
    
    @include('admin.layouts.include.footer')
</div>

@include('admin.layouts.components.spinner')

@yield('addScript')
</body>
</html>
