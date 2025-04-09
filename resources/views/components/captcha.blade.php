<div class="captcha">
    <span class="img"><img id="captcha_img" src="{{ $captcha }}"></span>
    <button type="button" onclick="refreshCaptcha();"><img src="{{ asset('assets/image/icon/ic_refresh.png') }}" class="refresh"></button>
    <input type="text" name="captcha_input" id="captcha_input" value="" class="form-item" data-chk="N">
</div>