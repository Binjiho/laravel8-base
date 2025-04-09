<div id="inicisResultMap">
    <input type="text" name="resultCode" id="resultCode" value="{{ $resultMap->resultCode ?? '' }}" readonly> {{-- 결과 코드 --}}
    <input type="text" name="resultMsg" id="resultMsg" value="{{ $resultMap->resultMsg ?? '' }}" readonly> {{-- 결과 메세지 --}}
    <input type="text" name="TotPrice" id="TotPrice" value="{{ $resultMap->TotPrice ?? '' }}" readonly> {{-- 실제 결제금액 --}}
    <input type="text" name="MOID" id="MOID" value="{{ $resultMap->MOID ?? '' }}" readonly> {{-- 주문번호 --}}
    <input type="text" name="tid" id="tid" value="{{ $resultMap->tid ?? '' }}" readonly> {{-- 거래번호 --}}
</div>
