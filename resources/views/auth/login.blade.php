@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    @include('layouts.include.sub-menu-wrap')

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            <!-- s:login -->
            <div class="login-wrap type5">
                <div class="login-form">
                    <form id="login-frm" action="" method="post">
                        <fieldset>
                            <legend class="hide">로그인</legend>
                            <div class="login-tit-wrap">
                                <h3 class="login-tit">
                                    LOGIN
                                </h3>
                                <p>
                                    대한환경공학회 홈페이지에 오신 것을 환영합니다.<br><br>
                                    학회 홈페이지 개편에 따라 모든 회원님의 비밀번호가 초기화되었습니다.
                                    <strong class="text-deep-green">초기화된 비밀번호는 회원님의 생년월일 8자리</strong> 입니다.<br>
                                    보안을 위해 초기 비밀번호로 로그인하신 후 반드시 새로운 비밀번호로 변경해 주시기 바랍니다.<br>
                                    초기화된 비밀번호 예시 : 20250101
                                </p>
                            </div>
                            <div class="input-box">
                                <div class="form-group">
                                    <input type="text" name="user_id" id="user_id" class="form-item" placeholder="아이디를 입력하세요.">
                                    <input type="password" name="user_passwd" id="user_passwd" class="form-item" placeholder="비밀번호를 입력하세요.">
                                </div>
                                <button type="submit" class="btn btn-login">로그인</button>
                                <div class="checkbox-wrap blue cst">
                                    <label for="chk1" class="checkbox-group"><input type="checkbox" name="chk" id="chk1"> 아이디 저장</label>
                                </div>
                            </div>
                            <div class="btn-wrap">
                                <a href="{{ route('joinInfo') }}" class="btn btn-signup">회원계정이 없으신가요? &nbsp;회원가입하기</a>
                                <a href="{{ route('findId') }}" class="btn color-type6">아이디/비밀번호 찾기</a>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <!-- //e:login-->

        </div>
    </article>
@endsection

@section('addScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.5/js.cookie.min.js"></script>
    <script>
        const form = '#login-frm';
        const dataUrl = '{{ route('login') }}';

        // 쿠키에서 uid를 가져와 입력 필드에 채워 넣기
        $(document).ready(function () {
            const savedUid = Cookies.get('user_id'); // 쿠키에서 'user_id' 가져오기
            if (savedUid) {
                $("#user_id").val(savedUid); // 아이디 입력 필드에 값 설정
                $("#chk1").prop("checked", true); // 체크박스 선택 상태
            }
        });

        defaultVaildation();

        $(form).validate({
            rules: {
                user_id: {
                    isEmpty: true,
                },
                user_passwd: {
                    isEmpty: true,
                },
            },
            messages: {
                user_id: {
                    isEmpty: "아이디를 입력 해주세요.",
                },
                user_passwd: {
                    isEmpty: "비밀번호를 입력 해주세요.",
                },
            },
            submitHandler: function () {
                boardSubmit();
            }
        });

        const boardSubmit = () => {
            const user_id = $("#user_id").val();
            const rememberMe = $("#chk1").is(":checked");

            // 체크박스가 선택된 경우 쿠키 저장
            if (rememberMe) {
                Cookies.set('user_id', user_id, { expires: 30 }); // 쿠키를 7일간 저장
            } else {
                Cookies.remove('user_id'); // 체크박스가 해제되면 쿠키 삭제
            }
            callAjax(dataUrl, formSerialize(form), true);
        }
    </script>
@endsection
