@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
    <link type="text/css" rel="stylesheet" href="/assets/css/slick.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/common.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/jquery-ui.min.css">
@endsection

@section('contents')
    <div class="write-wrap mb-30">
        <ul>
            <li class="write-wrap-tit">
                <div class="form-group form-group-text n3">
                    <div class="text-wrap">
                        이름: {{ $user->name_kr ?? '' }}
                    </div>

                    <span class="text">|</span>

                    <div class="text-wrap">
                        ID: {{ $user->id ?? '' }}
                    </div>

                    <span class="text">|</span>

                    <div class="text-wrap">
                        회원등급: {{ $userConfig['level'][$user->level ?? ''] ?? '' }}
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <div class="table-wrap" style="margin-bottom: 20px;">
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

            </tbody>
        </table>
    </div>

    <form id="register-frm" action="" method="post" onsubmit="" data-sid="{{ !empty($user->sid) ? $user->sid : '' }}" data-case="user-update">
        <input type="hidden" name="gubun" value="{{ $user->gubun ?? '' }}" readonly>

        <fieldset>
            <legend class="hide">회원가입</legend>

            @include('auth.join.form.type'.($user->gubun ?? ''))

            <div class="sub-tit-wrap">
                <h3 class="sub-tit">관리자 정보 입력</h3>
            </div>
            <ul class="write-wrap">
                <li>
                    <div class="form-tit">
                        회원등급 - 세부등급
                    </div>
                    <div class="form-con">
                        <select class="form-item select-level" name="level">
                            @foreach($userConfig['level'] as $key => $val)
                                <option value="{{ $key }}" {{ ($user->level ?? '') == $key ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </li>
                <li>
                    <div class="form-tit">
                        관리자 메모
                    </div>
                    <div class="form-con">
                        <textarea name="memo" id="memo" cols="30" rows="10" style="border: 1px solid #cbd3d9; resize: none; padding: 10px;">{{ $user->memo ?? '' }}</textarea>
                    </div>
                </li>
            </ul>

            <div class="btn-wrap text-center">
                <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
                <button type="submit" class="btn btn-type1 color-type6">수정</button>
            </div>

        </fieldset>
    </form>
@endsection

@section('addScript')
    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
    <script>
        const form = '#register-frm';
        const dataUrl = '{{ route('member.data') }}';

        const boardSubmit = () => {
            let ajaxData = newFormData(form);

            if(confirm("회원 정보를 수정 하시겠습니까?")){
                callMultiAjax(dataUrl, ajaxData);
            }else{
                return false;
            }
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
