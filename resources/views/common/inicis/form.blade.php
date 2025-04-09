<!--테스트 JS
<script language="javascript" type="text/javascript" src="https://stgstdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
-->
<!--운영 JS>  -->
<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>

<form id="inicisF" method="post" target="inicisIframe">
    <input type="hidden" name="version" value="{{ $version }}">
    <input type="hidden" name="gopaymethod" value="{{ $gopaymethod }}">
    <input type="hidden" name="mid" value="{{ $mid }}">
    <input type="hidden" name="oid" value="{{ $oid }}">
    <input type="hidden" name="price" value="{{ $price }}">
    <input type="hidden" name="timestamp" value="{{ $timestamp }}">
    <input type="hidden" name="use_chkfake" value="{{ $use_chkfake }}">
    <input type="hidden" name="signature" value="{{ $signature }}">
    <input type="hidden" name="verification" value="{{ $verification }}">
    <input type="hidden" name="mKey" value="{{ $mKey }}">
    <input type="hidden" name="currency" value="{{ $currency }}">
    <input type="hidden" name="goodname" value="{{ $goodname }}">
    <input type="hidden" name="buyername" value="{{ $buyername }}">
    <input type="hidden" name="buyertel" value="{{ $buyertel }}">
    <input type="hidden" name="buyeremail" value="{{ $buyeremail }}">
    <input type="hidden" name="returnUrl" value="{{ $returnUrl }}">
    <input type="hidden" name="closeUrl" value="{{ $closeUrl }}">
    <input type="hidden" name="acceptmethod" value="{{ $acceptmethod }}">
</form>

<script>
    setTimeout(() => {
        INIStdPay.pay('inicisF');
    }, 500);
</script>
