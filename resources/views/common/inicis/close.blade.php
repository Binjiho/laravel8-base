<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $('#inicisResultMap').remove(); // 결과값 DIV 제거 후 초기화
</script>

{{-- 결제 정보값이 있을경우 --}}
@if(!empty($resultMap) && !empty($resultMap->resultMsg ))
    @if(($resultMap->resultCode ?? '') == '0000')
        @include('common.inicis.resultMap', ['resultMap' => $resultMap])

        <script>
            // 결제 성공
            $('form', parent.document).append($('#inicisResultMap')) // 결과값 inicisResultMap form 에 append

            window.parent.postMessage( // 결제 성공후 부모창 form submit
                // 전달할 data (부모창에서 호출할 함수명)
                { functionName : 'submit' }
                // 부모창의 도메인
                // , 'http://abc.com'
                // 모든 도메인에 대하여 허용 ( 추천하지 않음 )
                , '*'
            );
        </script>
    @else
        <script>
            // 결제 실패 (error)
            alert('{{ $resultMap->resultMsg }}');
        </script>
    @endif
@endif

{{-- 결제창 닫는 스크립트 --}}
<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay_close.js" charset="UTF-8"></script>

<script>
    $('#inicisF').remove() // 결제 호출 폼 삭제
</script>

</body>
</html>
