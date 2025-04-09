@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    @include('layouts.include.sub-menu-wrap')

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            <!-- s:인트로 -->
            <ul class="mypage-menu">
                <li><a href="{{ route('mypage.pwCheck') }}">
                        <p>개인정보 수정</p>
                        <div class="bottom">
                            <img src="/assets/image/sub/ic_mypage01.png" alt="">
                            <div class="btn"><span>바로가기</span><i class="arrow">아이콘</i></div>
                        </div>
                    </a></li>
                <li><a href="{{ route('mypage.password') }}">
                        <p>비밀번호 변경</p>
                        <div class="bottom">
                            <img src="/assets/image/sub/ic_mypage02.png" alt="">
                            <div class="btn"><span>바로가기</span><i class="arrow">아이콘</i></div>
                        </div>
                    </a></li>
                <li><a href="{{ route('mypage.fee') }}">
                        <p>회비납부 현황</p>
                        <div class="bottom">
                            <img src="/assets/image/sub/ic_mypage03.png" alt="">
                            <div class="btn"><span>바로가기</span><i class="arrow">아이콘</i></div>
                        </div>
                    </a></li>
                <li><a href="{{ route('mypage.work_attend') }}">
                        <p>학술대회 참석현황</p>
                        <div class="bottom">
                            <img src="/assets/image/sub/ic_mypage04.png" alt="">
                            <div class="btn"><span>바로가기</span><i class="arrow">아이콘</i></div>
                        </div>
                    </a></li>
                <li><a href="{{ route('mypage.withdraw') }}">
                        <p>회원탈퇴</p>
                        <div class="bottom">
                            <div class="btn"><span>바로가기</span><i class="arrow">아이콘</i></div>
                        </div>
                    </a></li>
            </ul>
            <!-- //e:인트로-->

        </div>
    </article>
@endsection

@section('addScript')

@endsection
