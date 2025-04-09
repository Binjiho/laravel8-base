@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    @include('layouts.include.sub-menu-wrap')

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            <!-- s:회원탈퇴 -->
            <div class="find-form-wrap">
                <form action="{{ route('mypage.data') }}" id="register-frm" method="post" onsubmit="return false;" data-case="withdraw-create" data-sid="{{ $user->sid ?? 0 }}">
                    <fieldset>
                        <legend class="hide">회원탈퇴</legend>
                        <div class="find-form">
                            <div class="bg-box bg-img-box">
                                <img src="/assets/image/sub/ic_mypage08.png" alt="">
                                <div class="text-wrap">
                                    <p class="tit">탈퇴 신청 전 아래 사항을 반드시 확인해주세요.</p>
                                    <ul class="list-type list-type-dot text-left">
                                        <li>회원탈퇴 시 대한환경공학회의 모든 정보가 삭제되며, 탈퇴 후 복구가 불가능합니다.</li>
                                        <li>본인이 직접 신청하셔야 하며, 회원 DB에 있는 정보와 일치하여야만 탈퇴가 가능합니다.</li>
                                        <li>신청된 탈퇴 내역은 <strong class="text-red">학회의 확인 후 신청일로부터 10일 이내 탈퇴 처리</strong> 됩니다.</li>
                                    </ul>
                                </div>
                            </div>
                            <ul class="write-wrap">
                                <li>
                                    <div class="form-tit">이름</div>
                                    <div class="form-con">
                                        {{ $user->name_kr ?? '' }}
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">아이디</div>
                                    <div class="form-con">
                                        {{ $user->id ?? '' }}
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">회원구분</div>
                                    <div class="form-con">
                                        {{ $userConfig['gubun'][$user->gubun ?? ''] ?? '' }}
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">학회 가입 일</div>
                                    <div class="form-con">
                                        {{ !empty($user->created_at) ? $user->created_at->format('Y.m.d') : '' }}
                                    </div>
                                </li>
                            </ul>
                            <div class="btn-wrap text-center">
                                <button type="submit" class="btn btn-type1 color-type6">회원탈퇴 신청</button>
                                <a href="{{ route('main') }}" class="btn btn-type1 color-type7 btn-line">닫기</a>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <!-- //e:회원탈퇴-->

        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const form = '#register-frm';
        const dataUrl = '{{ route('mypage.data') }}';

        defaultVaildation();

        $(form).validate({
            rules: {

            },
            messages: {

            },
            submitHandler: function () {
                boardSubmit();
            }
        });

        const boardSubmit = () => {
            if(confirm("회원탈퇴신청 버튼을 누르시면 모든 정보가 삭제되며,\n이후 복구가 어렵습니다. 회원탈퇴 신청을 하시겠습니까?")){
                let ajaxData = newFormData(form);
                callMultiAjax(dataUrl, ajaxData);
            }else{
                return false;
            }
        }
    </script>
@endsection