@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    @include('layouts.include.sub-menu-wrap')

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            <div class="table-wrap">
                <table class="cst-table">
                    <caption class="hide">테이블</caption>
                    <colgroup>
                        <col style="width: 25%;">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th scope="row">회원 가입일</th>
                        <td class="text-left">{{ $user->created_at ?? '' }}</td>
                    </tr>
                    <tr>
                        <th scope="row">최종 회원정보 수정일</th>
                        <td class="text-left">{{ $user->updated_at ?? '' }}</td>
                    </tr>
                    <tr>
                        <th scope="row">회원등급</th>
                        <td class="text-left">{{ $userConfig['gubun'][ $user->gubun ?? '' ] }}</td>
                    </tr>
                    <tr>
                        <th scope="row">회원 세부 등급</th>
                        <td class="text-left">{{ $userConfig['grade'][ $user->gubun ?? '' ][$user->grade ?? ''] }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="btn-wrap text-right mt-10">
                <a href="{{ route('mypage.fee') }}" class="btn btn-small color-type8">회비납부</a>
                <a href="{{ route('mypage.withdraw') }}" class="btn btn-small color-type9">회원탈퇴</a>
            </div>

            <!-- s:회원가입 Form -->
            <div class="write-form-wrap">
                <form id="register-frm" action="" method="post" onsubmit="" data-sid="{{ !empty($user->sid) ? $user->sid : '' }}" data-case="user-modify">
                    <input type="hidden" name="gubun" value="{{ request()->gubun ?? '' }}" readonly>

                    <fieldset>
                        <legend class="hide">회원가입</legend>

                        @include('auth.join.form.type'.$gubun)

                        <div class="sub-tit-wrap">
                            <h3 class="sub-tit">자동화 프로그램 입력 방지</h3>
                        </div>
                        <p class="help-text text-red mb-10">*정보 보안을 위해 아래 적힌 문자를 입력하신 후 등록 가능합니다.</p>
                        <ul class="write-wrap">
                            <li>
                                <div class="form-con">
                                    @include('components.captcha')
                                </div>
                            </li>
                        </ul>

                        <div class="btn-wrap text-center">
                            <a href="{{ route('main') }}" class="btn btn-type1 color-type7 btn-line">취소</a>
                            <button type="submit" class="btn btn-type1 color-type6">수정</button>
                        </div>

                    </fieldset>
                </form>
            </div>
            <!-- //e:회원가입 Form -->
        </div>
    </article>
@endsection

@section('addScript')
    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
    <script>
        const form = '#register-frm';
        const dataUrl = '{{ route('auth.data') }}';

        //캡챠
        $(document).on('keyup', '#captcha_input', function() {
            const _captcha_input = $(this).val();
            callNoneSpinnerAjax(dataUrl, {
                'case': 'captcha-compare',
                'captcha_input': _captcha_input,
            });
        });

        const boardSubmit = () => {
            let ajaxData = newFormData(form);

            callMultiAjax(dataUrl, ajaxData);
        }

        function openDaumPostcode(kind){
            if( kind == "company" ){
                var space = "company_";
            }else{
                var space = "home_";
            }

            new daum.Postcode({
                oncomplete: function(data) {
                    $(":text[name='"+space+"zipcode']").val(data.zonecode);
                    $(":text[name='"+space+"address']").val(data.address).focus();
                }
            }).open();
        }

        //Email 형
        $(document).on("change",".emailOnly", function() {
            if( !isCorrectEmail( $(this).val() ) ) {
                alert('이메일 형식으로 입력해주세요.');
                $(this).val('').focus();
            }
        });

        function isCorrectEmail(email) {
            if(!email) return false;
            return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(email);
        }
    </script>

    @yield('reg-script')
@endsection