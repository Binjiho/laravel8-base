@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    @include('layouts.include.sub-menu-wrap')

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            <!-- s:비밀번호 변경 -->
            <div class="find-form-wrap">
                <form id="forget-frm" action="" method="post" data-case="check-pw">
                    <input type="hidden" name="user_sid" value="{{ $user->sid ?? 0 }}" readonly>
                    {{--                    <input type="hidden" name="user_id" value="{{ $user->id ?? '' }}" readonly>--}}
                    <fieldset>
                        <legend class="hide">비밀번호 변경</legend>
                        <div class="find-form">
                            <div class="find-tit-wrap">
                                <div class="bg-grey">
                                    <img src="/assets/image/sub/img_pass.png" alt="">
                                </div>
                                <p>
                                    안전한 개인정보 확인을 위하여<br>
                                    접속하신 계정의 비밀번호를 입력해주시기 바랍니다.
                                </p>
                            </div>
                            <ul class="write-wrap">
                                <li>
                                    <div class="form-tit">아이디</div>
                                    <div class="form-con">{{ $user->id ?? '' }}</div>
                                </li>
                                <li>
                                    <div class="form-tit">비밀번호</div>
                                    <div class="form-con">
                                        <input type="password" name="password" id="password" class="form-item">
                                    </div>
                                </li>
                            </ul>
                            <div class="btn-wrap text-center">
                                <a href="{{ route('main') }}" class="btn btn-type1 color-type7 btn-line">취소</a>
                                <a href="javascript:$('#forget-frm').submit();" class="btn btn-type1 color-type6">확인</a>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <!-- //e:비밀번호 변경-->

        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const frm = '#forget-frm';
        const dataUrl = '{{ route('mypage.data') }}';

        defaultVaildation();

        $(frm).validate({
            rules: {
                password: {
                    isEmpty: true,
                },
            },
            messages: {
                password: {
                    isEmpty: '비밀번호를 입력해주세요.',
                },

            },
            submitHandler: function () {
                boardSubmit();
            }
        });

        const boardSubmit = () => {
            let ajaxData = newFormData(frm);
            callMultiAjax(dataUrl, ajaxData);
        }

    </script>
@endsection
