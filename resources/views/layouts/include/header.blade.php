<header id="header" class="js-header">
    <div class="header-wrap">
        <div class="inner-layer">

            <h1 class="header-logo">
                <a href="/"><img src="/assets/image/common/h1_logo.png" alt=""></a>
            </h1>

            <ul class="util-menu">
                @if(thisAuth()->check())
                    <li><a href="javascript:logout();">LOGOUT</a></li>
                    <li><a href="">MYPAGE</a></li>
                @else
                    <li><a href=""><img src="/assets/image/common/ic_login.png" alt="">LOGIN</a></li>
                    <li><a href=""><img src="/assets/image/common/ic_signup.png" alt="">SIGN UP</a></li>
                @endif
                @auth('admin')
                <li class="admin"><a href="{{ env('APP_URL') }}/admin"><img src="/assets/image/common/ic_admin.png" alt="">ADMIN</a></li>
                @endauth
            </ul>

            <button type="button" class="btn btn-menu-open js-btn-menu-open"><span class="hide">메뉴 열기</span></button>
        </div>
    </div>

    <div id="dim" class="js-dim"></div>
    <nav id="gnb" class="inner-layer">
        <div class="m-gnb-header">
            <img src="/assets/image/common/h1_logo.png" alt="">
            <button type="button" class="btn btn-menu-close js-btn-menu-close"><span class="hide">메뉴 닫기</span></button>
        </div>
        <ul class="util-menu">
            @if(thisAuth()->check())
                <li class="logout"><a href="javascript:logout();"><img src="/assets/ko/assets/image/common/ic_util_logout.png" alt="">로그아웃</a></li>
                <li class="mypage"><a href="">마이페이지</a></li>
            @else
                <li><a href="" class="btn btn-type1 color-type2">로그인</a></li>
                <li><a href="" class="btn btn-type1 color-type5">회원가입</a></li>
            @endif

        </ul>

        <div class="gnb-wrap">
            <ul class="gnb js-gnb">
                @foreach($menu['main'] as $key => $val)
                    @if($val['continue']) @continue @endif
                    <li >
                        <a href="{{ empty($val['url']) ? route($val['route'], $val['param']) : $val['url'] }}" ><span>{!! $val['name'] !!}</span></a>

                        @foreach($menu['sub'][$key] ?? [] as $sKey => $sVal)
                            @if($loop->first)
                                <ul>
                                    @endif
                                    <li><a href="{{ empty($sVal['url']) ? route($sVal['route'], $sVal['param']) : $sVal['url'] }}" >{!! $sVal['name'] !!}</a></li>
                                    @if($loop->last)
                                </ul>
                            @endif
                        @endforeach
                    </li>
                @endforeach
            </ul>
        </div>

        @auth('admin')
        <div class="btn-wrap t-show m-show">
            <a href="{{ env('APP_URL') }}/admin" class="btn btn-type1 color-type3">ADMIN</a>
        </div>
        @endauth
    </nav>
</header>