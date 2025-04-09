<footer id="footer">
    <button type="button" class="btn-top js-btn-top">
        <img src="/assets/image/admin/common/ic_top.png" alt="">
        TOP
    </button>

    <div class="footer-wrap inner-layer">
        <div class="footer-con">
            <span class="footer-logo">Secretariat of {{ env('APP_NAME') }}</span>

            {{ env('APP_ADDR') }}

            <ul>
                <li><strong>E-Mail</strong>. <a href="mailto:{{ env('APP_EMAIL') }}" target="_blank">{{ env('APP_EMAIL') }}</a></li>
                <li><strong>TEL</strong>. <a href="tel:{{ env('APP_TEL') }}" target="_blank">{{ env('APP_TEL') }}</a></li>
                <li><strong>FAX</strong>. {{ env('APP_FAX') }}</li>
            </ul>
        </div>
    </div>

    <p class="copy">Copyright © {{ env('APP_NAME') }}. All Rights Reserved.</p>
</footer>
