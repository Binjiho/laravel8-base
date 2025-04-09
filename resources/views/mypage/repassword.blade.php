@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    @include('layouts.include.sub-menu-wrap')

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            <!-- s:비밀번호 변경 -->
            <div class="find-form-wrap">
                <form id="forget-frm" action="" method="post" data-case="change-pw">
                    <input type="hidden" name="user_sid" value="{{ $user->sid ?? 0 }}" readonly>
                    <fieldset>
                        <legend class="hide">비밀번호 변경</legend>
                        <div class="find-form">
                            <div class="find-tit-wrap">
                                <img src="/assets/image/sub/ic_pass.png" alt="">
                                <h3 class="find-tit">비밀번호 변경</h3>
                            </div>
                            <div class="bg-box">
                                <ul class="list-type list-type-dot">
                                    <li>{{ $user->name_kr ?? '' }} 회원님의 개인정보보호를 위하여 6개월 이상 비밀번호를 변경하지 않은 경우 비밀번호 변경 안내를 하고 있습니다.</li>
                                    <li>비밀번호 변경을 원하지 않을 경우 <span class="text-red">[ 다음에 변경하기 ]</span> 버튼 클릭으로 1개월 동안 안내를 받지 않을 수 있습니다.</li>
                                    <li>비밀번호는 숫자, 영문소문자, 특수문자를 조합하여 사용하시는 것이 안전하며, 주기적(최소 6개월)으로 변경 하시기 바랍니다.</li>
                                </ul>
                            </div>
                            <ul class="write-wrap">
                                <li>
                                    <div class="form-tit">현재 비밀번호</div>
                                    <div class="form-con">
                                        <input type="password" name="user_passwd" id="user_passwd" class="form-item">
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">새 비밀번호</div>
                                    <div class="form-con">
                                        <input type="password" name="new_password" id="new_password" class="form-item">
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">비밀번호 확인</div>
                                    <div class="form-con">
                                        <input type="password" name="re_password" id="re_password" class="form-item">
                                        <div class="help-text text-red mt-10">
                                            새 비밀번호를 재입력 해주세요.
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <ul class="list-type list-type-dot">
                                <li>쉬운 비밀번호나 자주 쓰는 사이트의 비밀번호가 같을 경우, 도용되기 쉬우므로 주기적으로 변경하셔서 사용하는 것이 좋습니다.</li>
                                <li>아이디와 주민등록번호, 생일, 전화번호 등 개인정보와 관련된 숫자, 연속된 숫자, 반복된 문자 등 다른 사람이 쉽게 알아 낼 수 있는 비밀번호는
                                    개인정보 유출의 위험이 높으므로 사용을 자제해 주시기 바랍니다.
                                </li>
                            </ul>
                            <div class="btn-wrap text-center">
                                <a href="javascript:;" id="next_check" class="btn btn-type1 color-type7 btn-line">다음에 변경하기</a>
                                <button type="submit" class="btn btn-type1 color-type6">변경</button>
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

        $(document).on('click', '#next_check', function() {
            const _user_sid = $("input[name='user_sid']").val();
            callNoneSpinnerAjax(dataUrl, {
                'case': 'change-pw-next',
                'user_sid': _user_sid,
            });
        });


        defaultVaildation();

        $(frm).validate({
            rules: {
                user_passwd: {
                    isEmpty: true,
                },
                new_password: {
                    isEmpty: true,
                    minlength: 8,
                    maxlength: 16,
                    pwCheck: true,
                },
                re_password: {
                    isEmpty: true,
                    equalTo: "input[name=new_password]",
                },
            },
            messages: {
                user_passwd: {
                    isEmpty: '현재 비밀번호를 입력해주세요.',
                },
                new_password: {
                    isEmpty: '새 비밀번호를 입력해주세요.',
                    minlength: '새 비밀번호는 최소 8자 글자로 입력해주세요.',
                    pwCheck: '새 비밀번호는 8~16자 영문, 숫자를 조합하여 입력하세요.'
                },
                re_password: {
                    isEmpty: '비밀번호 확인을 입력해주세요.',
                    equalTo: "입력하신 새 비밀번호와 동일하게 입력해주세요.",
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
