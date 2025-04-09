<?php

namespace App\Services\Inicis;

use App\Services\Inicis\INIStdPayUtil;
use Illuminate\Http\Request;
use App\Services\AppServices;
use App\Services\Inicis\CreateIdModule;
use App\Services\Inicis\HttpClient;
use App\Services\Inicis\properties;

/**
 * Class InicisServices
 * @package App\Services
 */
class INISServices extends AppServices
{
    public function INISInitService(Request $request)
    {
        $SignatureUtil = new INIStdPayUtil();

        if( masterIp() ) {
            $mid = env('INICIS_TEST_MID'); // TEST아이디
            $signKey = env('INICIS_TEST_SIGNKEY'); // TEST signkey
        }else{
            $mid = env('INICIS_MID'); // 상점아이디
            $signKey = env('INICIS_SIGNKEY'); // 웹 결제 signkey
        }

        $mKey = $SignatureUtil->makeHash($signKey, "sha256");
        $timestamp = $SignatureUtil->getTimestamp();

        $oid = $mid . "_" . $timestamp; // 가맹점 주문번호(가맹점에서 직접 설정)
        $price = $request->price; // 상품가격(특수기호 제외, 가맹점에서 직접 설정)

        $params = [
            "oid" => $oid,
            "price" => $price,
            "timestamp" => $timestamp
        ];

        $signature = $SignatureUtil->makeSignature($params);

        $params['signKey'] = $signKey;
        $verification = $SignatureUtil->makeSignature($params);

        $this->data['version'] = '1.0';
        $this->data['gopaymethod'] = $request->payment_method ?? 'Card';
//        $this->data['gopaymethod'] = 'Card';
        $this->data['mid'] = $mid;
        $this->data['oid'] = $oid;
        $this->data['price'] = $price;
        $this->data['timestamp'] = $timestamp;
        $this->data['use_chkfake'] = "Y"; // PC결제 보안강화 사용 ["Y" 고정]
        $this->data['signature'] = $signature;
        $this->data['verification'] = $verification;
        $this->data['mKey'] = $mKey;
        $this->data['currency'] = "WON";
        $this->data['goodname'] = $request->goodname;
        $this->data['buyername'] = $request->buyername;
        $this->data['buyertel'] = str_replace('_', '', $request->buyertel);
        $this->data['buyeremail'] = $request->buyeremail;
        $this->data['returnUrl'] = route('inicis.result');
        $this->data['closeUrl'] = route('inicis.close');
        $this->data['acceptmethod'] = "below1000:centerCd(Y):popreturn";
        
        return $this->returnJsonData('append', [
            $this->ajaxActionHtml('body', view('common.inicis.form', $this->data)->render())
        ]);
    }

    public function INISResultService(Request $request)
    {
        $util = new INIStdPayUtil();
        $prop = new properties();

        if( masterIp() ) {
            $dev_signKey = env('INICIS_TEST_SIGNKEY'); // TEST signkey
        }else{
            $dev_signKey = env('INICIS_SIGNKEY'); // 웹 결제 signkey
        }

        try {
            //#############################
            // 인증결과 파라미터 수신
            //#############################

            if (strcmp("0000", $request["resultCode"]) == 0) {

                //############################################
                // 1.전문 필드 값 설정(***가맹점 개발수정***)
                //############################################
                $mid = $request["mid"];
                $signKey = $dev_signKey; // 웹 결제 signkey
                $timestamp = $util->getTimestamp();
                $charset = "UTF-8";
                $format = "JSON";
                $authToken = $request["authToken"];
                $authUrl = $request["authUrl"];
                $netCancel = $request["netCancelUrl"];
                $merchantData = $request["merchantData"];

                //##########################################################################
                // 승인요청 API url (authUrl) 리스트 는 properties 에 세팅하여 사용합니다.
                // idc_name 으로 수신 받은 센터 네임을 properties 에서 include 하여 승인요청하시면 됩니다.
                //##########################################################################
                $idc_name = $request["idc_name"];
                $authUrl = $prop->getAuthUrl($idc_name);

                if (strcmp($authUrl, $request["authUrl"]) == 0) {

                    //#####################
                    // 2.signature 생성
                    //#####################
                    $signParam["authToken"] = $authToken;   // 필수
                    $signParam["timestamp"] = $timestamp;   // 필수
                    // signature 데이터 생성 (모듈에서 자동으로 signParam을 알파벳 순으로 정렬후 NVP 방식으로 나열해 hash)
                    $signature = $util->makeSignature($signParam);

                    $veriParam["authToken"] = $authToken;   // 필수
                    $veriParam["signKey"] = $signKey;     // 필수
                    $veriParam["timestamp"] = $timestamp;   // 필수
                    // verification 데이터 생성 (모듈에서 자동으로 signParam을 알파벳 순으로 정렬후 NVP 방식으로 나열해 hash)
                    $verification = $util->makeSignature($veriParam);


                    //#####################
                    // 3.API 요청 전문 생성
                    //#####################
                    $authMap["mid"] = $mid;            // 필수
                    $authMap["authToken"] = $authToken;      // 필수
                    $authMap["signature"] = $signature;      // 필수
                    $authMap["verification"] = $verification;   // 필수
                    $authMap["timestamp"] = $timestamp;      // 필수
                    $authMap["charset"] = $charset;        // default=UTF-8
                    $authMap["format"] = $format;         // default=XML

                    try {

                        $httpUtil = new HttpClient();

                        //#####################
                        // 4.API 통신 시작
                        //#####################

                        $authResultString = "";
                        if ($httpUtil->processHTTP($authUrl, $authMap)) {
                            $authResultString = $httpUtil->body;
                        } else {
                            $resultMap['resultMsg'] = "Http Connect Error\n" . $httpUtil->errormsg;
                            return $this->returnData($resultMap);
                        }

                        //############################################################
                        //5.API 통신결과 처리(***가맹점 개발수정***)
                        //############################################################

                        $resultMap = json_decode($authResultString, true);
                        return $this->returnData($resultMap);
                    } catch (\Exception $e) {
                        //    $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
                        //####################################
                        // 실패시 처리(***가맹점 개발수정***)
                        //####################################
                        //---- db 저장 실패시 등 예외처리----//
                        $resultMap['resultMsg'] = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';

                        //#####################
                        // 망취소 API
                        //#####################

                        $netcancelResultString = ""; // 망취소 요청 API url(고정, 임의 세팅 금지)
                        $netCancel = $prop->getNetCancel($idc_name);

                        if (strcmp($netCancel, $request["netCancelUrl"]) == 0) {

                            if ($httpUtil->processHTTP($netCancel, $authMap)) {
                                $netcancelResultString = $httpUtil->body;
                            } else {
                                $resultMap['resultMsg'] = "Http Connect Error\n" . $httpUtil->errormsg;
                            }
//                            echo "<br/>## 망취소 API 결과 ##<br/>";

                            /*##XML output##*/
                            //$netcancelResultString = str_replace("<", "&lt;", $$netcancelResultString);
                            //$netcancelResultString = str_replace(">", "&gt;", $$netcancelResultString);

                            // 취소 결과 확인
//                            echo "<p>" . $netcancelResultString . "</p>";
                        }

                        return $this->returnData($resultMap);
                    }

                } else {
                    $resultMap['resultMsg'] = "authUrl check Fail";
                    return $this->returnData($resultMap);
                }
            } else {
                $resultMap['resultMsg'] = "인증실패 (오류코드:". $request["resultCode"] . ")";
                return $this->returnData($resultMap);
            }

        } catch (\Exception $e) {
            $resultMap['resultMsg'] = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
            return $this->returnData($resultMap);
        }
    }

    private function returnData($resultMap)
    {
        return redirect()->route('inicis.close', $resultMap);
    }
}
