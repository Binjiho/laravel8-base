@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="sub-tit-wrap">
        <h3 class="sub-tit">메일 {{ empty($mail->sid) ? '등록' : '수정' }}</h3>
    </div>

    <form id="mail-frm" method="post" action="{{ route('mail.data') }}" data-sid="{{ $mail->sid ?? 0 }}" data-case="mail-{{ empty($mail->sid) ? 'create' : 'update' }}" data-send="N">
        <fieldset>
            <div class="write-wrap">
                <ul>
                    <li>
                        <div class="form-tit"><strong class="required">*</strong> 제목</div>
                        <div class="form-con">
                            <input type="text" name="subject" id="subject" value="{{ $mail->subject ?? '' }}" class="form-item">
                        </div>
                    </li>

                    <li>
                        <div class="form-tit"><strong class="required">*</strong> 발송자</div>
                        <div class="form-con">
                            <input type="text" name="sender_name" id="sender_name" value="{{ $mail->sender_name ?? env('APP_NAME') }}" class="form-item">
                        </div>
                    </li>

                    <li>
                        <div class="form-tit"><strong class="required">*</strong> 발송자 이메일</div>
                        <div class="form-con n2">
                            <input type="text" name="sender_email" id="sender_email" value="{{ $mail->sender_email ?? env('APP_EMAIL') }}" class="form-item">
                        </div>
                    </li>

                    <li>
                        <div class="form-tit"><strong class="required">*</strong> 수신대상</div>
                        <div class="form-con">
                            <div class="radio-wrap cst">
                                @foreach($mailConfig['send_type'] as $key => $val)
                                    <label for="send_type_{{ $key }}" class="radio-group">
                                        <input type="radio" name="send_type" id="send_type_{{ $key }}" value="{{ $key }}" {{ ($mail->send_type ?? '') == $key ? 'checked' : '' }}>
                                        {{ $val }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </li>


                    <li style="display:{{ ($mail->send_type ?? '') == 1 ? '' : 'none' }};" id="level-li">
                        <div class="form-tit"><strong class="required">*</strong> 회원등급</div>
                        <div class="form-con">
                            <div class="checkbox-wrap cst">
                                <label for="level_all" class="checkbox-group">
                                    <input type="checkbox" id="level_all" {{ count($mail->level ?? []) == count(getConfig('user')['level']) ? 'checked' : '' }}>
                                    전체
                                </label>

                                @foreach(getConfig('user')['level'] ?? [] as $key => $val)
                                    <label for="level_{{ $key }}" class="checkbox-group">
                                        <input type="checkbox" name="level[]" id="level_{{ $key }}" value="{{ $key }}" {{ array_search($key, $mail->level ?? []) !== false ? 'checked' : '' }}>
                                        {{ $val }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </li>

                    <li style="display:{{ ($mail->send_type ?? '') == 2 ? '' : 'none' }};" id="address-li">
                        <div class="form-tit"><strong class="required">*</strong> 주소록</div>
                        <div class="form-con">
                            <select name="ma_sid" id="ma_sid" class="form-item">
                                <option value="">주소록을 선택해주세요.</option>
                                @foreach($address ?? [] as $row)
                                    <option value="{{ $row->sid }}" {{ ($mail->ma_sid ?? '') == $row->sid ? 'selected' : '' }}>{{ $row->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </li>

                    <li style="display:{{ ($mail->send_type ?? '') == 9 ? '' : 'none' }};" id="test-li">
                        <div class="form-tit"><strong class="required">*</strong> 테스트 이메일</div>
                        <div class="form-con">
                            <input type="text" name="test_email" id="test_email" value="{{ $mail->test_email ?? '' }}" class="form-item">
                        </div>
                    </li>

                    <li>
                        <div class="form-tit"><strong class="required">*</strong> 메일폼</div>
                        <div class="form-con">
                            <div class="radio-wrap cst">
                                @foreach($mailConfig['admin_template'] ?? [] as $key => $val)
                                    <label for="template_{{ $key }}" class="radio-group">
                                        <input type="radio" name="template" id="template_{{ $key }}" value="{{ $key }}" {{ ($mail->template ?? '') == $key ? 'checked' : '' }}>
                                        {{ $val['name'] }}
                                    </label>
                                @endforeach

                                <a href="javascript:void(0);" class="btn btn-small color-type10" id="mail-preview">미리보기</a>
                            </div>
                        </div>
                    </li>

                    <li>
                        <div class="form-tit"><strong class="required">*</strong> 버튼사용</div>
                        <div class="form-con">
                            <div class="radio-wrap cst">
                                @foreach($mailConfig['use_btn'] ?? [] as $key => $val)
                                    <label for="use_btn_{{ $key }}" class="radio-group">
                                        <input type="radio" name="use_btn" id="use_btn_{{ $key }}" value="{{ $key }}" {{ ($mail->use_btn ?? '') == $key ? 'checked' : '' }}>
                                        {{ $val }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </li>

                    <li id="link-li" style="display: {{ ($mail->use_btn ?? '9') == '9' ? 'none' : '' }}">
                        <div class="form-tit"><strong class="required">*</strong> 링크</div>
                        <div class="form-con">
                            <input type="text" name="link_url" id="link_url" value="{{ $mail->link_url ?? '' }}" class="form-item">
                        </div>
                    </li>

                    @if(!empty($mail->sid) && !$mail->files->isEmpty())
                        <li>
                            <div class="form-tit"> 첨부파일</div>
                            <div class="form-con">
                                <div class="radio-wrap cst">
                                    @foreach($mail->files as $key => $file)
                                        <div style="width: 100%;">
                                            <label for="plupload_file_del_{{ $key }}" class="checkbox-group">
                                                <input type="checkbox" name="plupload_file_del[]" id="plupload_file_del_{{ $key }}" value="{{ $file->sid }}">
                                                <b style="color: #e95d5d;">삭제</b> -
                                            </label>

                                            <a href="{{ $file->downloadUrl() }}">{{ $file->filename }} (다운 : {{ number_format($file->download) }})</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @endif

                    <li>
                        <div class="form-con">
                            <textarea name="contents" id="contents" class="tinymce">{{ $mail->contents ?? '' }}</textarea>
                        </div>
                    </li>

                    <li>
                        <div class="form-con">
                            <div id="plupload"></div>
                        </div>
                    </li>
                </ul>
            </div>
        </fieldset>

        <div class="btn-wrap text-center">
            <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
            <button type="submit" class="btn btn-type1 color-type1" id="submit">{{ empty($mail->sid) ? '등록' : '수정' }}</button>
            <button type="submit" class="btn btn-type1 color-type6" id="submit-send">{{ empty($mail->sid) ? '등록' : '수정' }} 후 발송</button>
        </div>
    </form>
@endsection

@section('addScript')
    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('plugins/plupload/2.3.6/plupload.full.min.js') }}"></script>
    <script src="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/jquery.plupload.queue.min.js') }}"></script>
    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>

    <script>
        const form = '#mail-frm';
        const dataUrl = $(form).attr('action');

        pluploadInit({
            multipart_params: {
                directory: '{{ $mailConfig['directory'] }}',
            },
            filters: {
                max_file_size: '20mb'
            },
        });

        $(document).on('change', 'input:radio[name=send_type]', function () {
            $('#test-li').hide();
            $('#test-li').find('input[type=text]').val('');

            $('#level-li').hide();
            $('#level-li').find('input[type=checkbox]').prop('checked', false);

            $('#address-li').hide();
            $('#address-li').find('select').val('');

            switch ($(this).val()) {
                case '1':
                    $('#level-li').show();
                    break;

                case '2':
                    $('#address-li').show();
                    break;

                case '9':
                    $('#test-li').show();
                    break;
            }
        });

        $(document).on('click', '#level_all', function() {
            const target = $("input:checkbox[name='level[]']");
            target.prop('checked', $(this).is(':checked'));
        });

        $(document).on('click', "input:checkbox[name='level[]']", function() {
            const target = $("#level_all");
            const levelLength = $("input:checkbox[name='level[]']").length;
            const levelCheckedLength = $("input:checkbox[name='level[]']:checked").length;

            target.prop('checked', (levelLength == levelCheckedLength));
        });

        $(document).on('change', "input[name=use_btn]", function() {
            const target = $('#link-li');

            if ($(this).val() == '9') {
                target.hide();
                target.find('input').val('');
            } else {
                target.show();
            }
        });

        $(document).on('click', '#mail-preview', function () {
            const subject = $('input[name=subject]');
            const template = $('input[name=template]');
            const use_btn = $('input[name=use_btn]');
            const link_url = $('input[name=link_url]');

            if (isEmpty(subject.val())) {
                alert('메일제목을 입력 해주세요.');
                subject.focus();
                return false;
            }

            if (!template.is(':checked')) {
                alert('메일폼을 선택 해주세요.');
                template.focus();
                return false;
            }

            if (!use_btn.is(':checked')) {
                alert('버튼사용 여부를 선택 해주세요.');
                use_btn.focus();
                return false;
            }

            if ($('input[name=use_btn]:checked').val() != '9') {
                if (isEmpty(link_url.val())) {
                    alert('링크를 입력 해주세요.');
                    link_url.focus();
                    return false;
                }
            }

            let tinyVal = tinymce.get('contents').getContent();
            // tinyVal = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
            tinyVal = tinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제

            if (isEmpty(tinyVal)) {
                alert('메일 내용을 입력 해주세요.');
                return false;
            }

            let ajaxData = newFormData(form);
            ajaxData.append('case', 'mail-upsert-preview');
            ajaxData.append('contents', tinymce.get('contents').getContent());

            const plupload_queue = $('#plupload').pluploadQueue();

            $(plupload_queue.files).each(function (k, v) {
                ajaxData.append('plupload[]', v.name);
            });

            callbackMultiAjax(dataUrl, ajaxData, function (data, error) {
                if (data) {
                    if (data.alert) {
                        ajaxSuccessData(data);
                    } else {
                        const popupWidth = 1100;
                        const popupHeight = 800;
                        const popupY = (window.screen.height / 2) - (popupHeight / 2);
                        const popupX = (window.screen.width / 2) - (popupWidth / 2);

                        const mailPreivew = window.open('', 'mail-preview', 'status=no, height=' + popupHeight + ', width=' + popupWidth + ', left=' + popupX + ', top=' + popupY);

                        mailPreivew.document.open();
                        mailPreivew.document.write(data.html);
                    }
                } else {
                    console.log(error);
                    // alert('Preview Error');
                    // location.reload();
                }
            });
        });

        $(document).on('click',  '#submit', function () {
            $(form).data('send', 'N');
        });

        $(document).on('click',  '#submit-send', function () {
            $(form).data('send', 'Y');
        });

        $(document).on('submit',  form, function () {
            const subject = $('input[name=subject]');
            const sender_name = $('input[name=sender_name]');
            const sender_email = $('input[name=sender_email]');
            const send_type = $('input[name=send_type]');
            const template = $('input[name=template]');
            const use_btn = $('input[name=use_btn]');
            const link_url = $('input[name=link_url]');

            if (isEmpty(subject.val())) {
                alert('제목을 입력 해주세요.');
                subject.focus();
                return false;
            }

            if (isEmpty(sender_name.val())) {
                alert('발송자를 입력 해주세요.');
                sender_name.focus();
                return false;
            }

            if (isEmpty(sender_email.val())) {
                alert('발송 이메일을 입력 해주세요.');
                sender_email.focus();
                return false;
            }

            if (!emailCheck(sender_email.val())) {
                alert('올바른 이메일 형식이 아닙니다.');
                sender_email.focus();
                return false;
            }

            if (!send_type.is(':checked')) {
                alert('수신대상을 선택 해주세요.');
                send_type.focus();
                return false;
            }

            switch ($('input[name=send_type]:checked').val()) {
                case '1':
                    if ($("input[name='level[]']:checked").length == 0) {
                        alert('회원을 선택 해주세요.');
                        $("input[name='level[]']").focus();
                        return false;
                    }

                    break;

                case '2':
                    if (isEmpty($("select[name='ma_sid']").val())) {
                        alert('주소록을 선택 해주세요.');
                        $("select[name='ma_sid']").focus();
                        return false;
                    }

                    break;

                case '9':
                    if (isEmpty($("input[name='test_email']").val())) {
                        alert('테스트 이메일을 입력 해주세요.');
                        $("input[name='test_email']").focus();
                        return false;
                    }

                    if (!emailCheck($("input[name='test_email']").val())) {
                        alert('올바른 이메일 형식이 아닙니다.');
                        $("input[name='test_email']").focus();
                        return false;
                    }

                    break;
            }

            if (!template.is(':checked')) {
                alert('메일폼을 선택 해주세요.');
                template.focus();
                return false;
            }

            if (!use_btn.is(':checked')) {
                alert('버튼사용 여부를 선택 해주세요.');
                use_btn.focus();
                return false;
            }

            if ($('input[name=use_btn]:checked').val() != '9') {
                if (isEmpty(link_url.val())) {
                    alert('링크를 입력 해주세요.');
                    link_url.focus();
                    return false;
                }
            }

            let tinyVal = tinymce.get('contents').getContent();
            // tinyVal = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
            tinyVal = tinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제

            if (isEmpty(tinyVal)) {
                alert('메일 내용을 입력 해주세요.');
                return false;
            }

            let ajaxData = newFormData(form);
            ajaxData.append('send', $(form).data('send'));
            ajaxData.append('contents', tinymce.get('contents').getContent());
            ajaxData.append('plupload_file', JSON.stringify(plupladFile));

            const plupload_queue = $('#plupload').pluploadQueue();

            let fileCnt = plupload_queue.files.length;
            fileCnt = (fileCnt - previousUploadedFilesCount);

            if (fileCnt > 0) {
                spinnerShow();
                plupload_queue.start();

                plupload_queue.bind('UploadComplete', function(up, files) {
                    spinnerHide();

                    if (plupload_queue.total.failed === 0) {
                        previousUploadedFilesCount = up.files.length; // 업로드된 파일 수를 저장
                        mailSubmit();
                    } else {
                        alert('파일 업로드 실패');
                        location.reload();
                    }
                });

                return false;
            }

            mailSubmit();
        });

        const mailSubmit = () => {
            let ajaxData = newFormData(form);
            ajaxData.append('send', $(form).data('send'));
            ajaxData.append('contents', tinymce.get('contents').getContent());
            ajaxData.append('plupload_file', JSON.stringify(plupladFile));

            callMultiAjax(dataUrl, ajaxData);
        }
    </script>
@endsection
