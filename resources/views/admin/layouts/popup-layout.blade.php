<!DOCTYPE html>
<html lang="ko">
<head>
    @include('layouts.components.baseHead')

    <style>
        /* paging */
        .paging-wrap{
            margin-top: 60px;
            text-align: center;
        }
        .paging{
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }
        .paging > li{
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 1px solid transparent;
            margin: 1px;
            vertical-align: top;
            -webkit-transition: 0.3s ease;
            transition: 0.3s ease;
        }
        .paging > .num:hover,
        .paging > .num.on{
            border-color: #a3a3a3;
            background-color: #a3a3a3;
        }
        .paging > .num:hover > a,
        .paging > .num.on > a{
            color: #ffffff;
        }
        .paging > li > a{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100% 0;
            height: 100%;
            padding: 3px 0;
            font-size: 1.4rem;
            font-weight: 500;
            -webkit-transition: 0.3s ease;
            transition: 0.3s ease;
        }
        .paging > li:not(.num){
            border: 1px solid #cccccc;
        }
        .paging .prev{
            margin-right: 15px;
        }
        .paging .next{
            margin-left: 15px;
        }
    </style>
</head>
<body>
<div id="popup-wrap" style="font-size: large;">
    <div style="padding: 35px;">
        @yield('contents')
    </div>
</div>

@include('admin.layouts.components.spinner')

{{--addScript--}}
@yield('addScript')
</body>
</html>
