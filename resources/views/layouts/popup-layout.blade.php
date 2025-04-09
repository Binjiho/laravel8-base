<!DOCTYPE html>
<html lang="ko">
<head>
    @include('layouts.components.baseHead')
</head>
<body>
    @yield('contents')

    @include('layouts.components.spinner')

    {{--addScript--}}
    @yield('addScript')
</body>
</html>
