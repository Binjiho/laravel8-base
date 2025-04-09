<?php

namespace App\Services;

use App\Models\MailSend;
use App\Models\WiseUMailBody;
use App\Models\WiseUMailInterface;
use App\Models\WiseUMailLog;
use Illuminate\Support\Facades\DB;

/**
 * Class MailSendServices
 * @package App\Services
 */
class MailRealSendServices extends AppServices
{
    private $mailConfig;

    private $secretariatMail = 'kosenv@kosenv.or.kr'; // 학회사무국 메일

    private $plannerMail = ''; // 기획자 메일

    public function __construct()
    {
        $this->mailConfig = getConfig('mail');
    }

    // 메일 수신 대상 추가
    private function mailSendTartgetAppend($mailData)
    {
        return [
            [ // 사무국
                'receiver_name' => $mailData['receiver_name'],
                'receiver_email' => $this->secretariatMail,
                'body' => $mailData['body'],
            ],

            [ // 기획자
                'receiver_name' => $mailData['receiver_name'],
                'receiver_email' => $this->plannerMail,
                'body' => $mailData['body'],
            ],
        ];
    }

    public function mailSendService($mailData, $case, $additionalData = null)
    {
        switch ($case) {
            case 'user-create':
                $subject = '[대한환경공학회] 회원가입을 축하 드립니다.';

                $data[] = $mailData;
//                $data = array_merge($data, $this->mailSendTartgetAppend($mailData));
                break;

            case 'forget-password':
                $subject = '[대한환경공학회] 임시 비밀번호 안내 드립니다.';

                $data[] = $mailData;
//                $data = array_merge($data, $this->mailSendTartgetAppend($mailData));
                break;

            case 'fee-ok':
                $subject = '[대한환경공학회] 회비 완납 안내 드립니다.';

                $data[] = $mailData;
//                $data = array_merge($data, $this->mailSendTartgetAppend($mailData));
                break;
            case 'fee-request':
                $subject = '[대한환경공학회] 회비 입금 요청 드립니다.';

                $data[] = $mailData;
//                $data = array_merge($data, $this->mailSendTartgetAppend($mailData));
                break;

            case 'registration-ok':
            case 'registration-refund':
            case 'abstract-ok':
            case 'support-ok':
            case 'support-bank':
                $subject = $additionalData['subject'];

                $data[] = $mailData;
//                $data = array_merge($data, $this->mailSendTartgetAppend($mailData));
                break;

            // 관리자 발송 메일
            case 'admin-type-send':
            case 'admin-target-resend':
                $subject = $additionalData['subject'];

                $sender = [
                    'sender_name' => $additionalData['sender_name'],
                    'sender_email' => $additionalData['sender_email'],
                ];

                $data = $mailData;
                $data = array_merge($data, $this->mailSendTartgetAppend($mailData[0]));
                break;

            default:
                return notFoundRedirect();
        }

        return $this->mailSend($data, $subject, $sender ?? null);
    }

    // 메일 발송 로직
    private function mailSend($mailData, $subject, $sender = null)
    {
        $this->sender_name = env('APP_NAME');
        $this->sender_email = env('APP_EMAIL');
        $this->ecare_no = env('ECARE_NUMBER');


        $this->transaction();

        try {
            //서버에 odbc17 에러로 PDO로 연결
            $wiseUconnection = wiseuConnection();

            foreach ($mailData as $key => $data) {
                $now = now();
                $seq = $now->timestamp . $now->micro;

                $body = $data['body'];

                $receiver_name = $data['receiver_name'];
                $receiver_email = $data['receiver_email'];

                $stmt = $wiseUconnection->prepare("INSERT INTO NVREALTIMEACCEPT 
                    (ECARE_NO, RECEIVER_ID, CHANNEL, SEQ, REQ_DT, REQ_TM, TMPL_TYPE, RECEIVER_NM, RECEIVER, SENDER_NM, SENDER, SUBJECT, SEND_FG, DATA_CNT) 
                    VALUES (:ECARE_NO, :RECEIVER_ID, :CHANNEL, :SEQ, :REQ_DT, :REQ_TM, :TMPL_TYPE, :RECEIVER_NM, :RECEIVER, :SENDER_NM, :SENDER, :SUBJECT, :SEND_FG, :DATA_CNT)");

                $stmt->execute([
                    ':ECARE_NO' => $this->ecare_no,
                    ':RECEIVER_ID' => $seq,
                    ':CHANNEL' => 'M',
                    ':SEQ' => $seq,
                    ':REQ_DT' => $now->format('Ymd'),
                    ':REQ_TM' => $now->format('His'),
                    ':TMPL_TYPE' => 'T',
                    ':RECEIVER_NM' => $receiver_name,
                    ':RECEIVER' => $receiver_email,
                    ':SENDER_NM' => $sender['sender_name'] ?? $this->sender_name,
                    ':SENDER' => $sender['sender_email'] ?? $this->sender_email,
                    ':SUBJECT' => $subject,
                    ':SEND_FG' => 'R',
                    ':DATA_CNT' => 1,
                ]);

                $stmt = $wiseUconnection->prepare("INSERT INTO NVREALTIMEACCEPTDATA (SEQ, DATA_SEQ, ATTACH_YN, DATA) VALUES (:SEQ, :DATA_SEQ, :ATTACH_YN, :DATA)");

                $stmt->execute([
                    ':SEQ' => $seq,
                    ':DATA_SEQ' => 1,
                    ':ATTACH_YN' => 'N',
                    ':DATA' => $body,
                ]);



//                if (php_sapi_name() != 'cli') { // 크론탭 아닐때
//                    // 특정 ip 예외처리
//                    switch ($_SERVER['REMOTE_ADDR']) {
//                        case '218.235.94.247':
//                            $receiver_email = 'jh2.park@m2community.co.kr';
//                            break;
//                    }
//                }

//                //인터페이스 테이블
//                WiseUMailInterface::insert([
//                    'ECARE_NO' => $this->ecare_no,
//                    'RECEIVER_ID' => $seq,
//                    'CHANNEL' => 'M',
//                    'SEQ' => $seq,
//                    'REQ_DT' => $now->format('Ymd'),
//                    'REQ_TM' => $now->format('His'),
//                    'TMPL_TYPE' => 'T',
//                    'RECEIVER_NM' => $receiver_name,
//                    'RECEIVER' => $receiver_email,
//                    'SENDER_NM' => $sender['sender_name'] ?? $this->sender_name,
//                    'SENDER' => $sender['sender_email'] ?? $this->sender_email,
//                    'SUBJECT' => $subject,
//                    'SEND_FG' => 'R',
//                    'DATA_CNT' => 1,
//                ]);
//
//                //메일 body
//                WiseUMailBody::insert([
//                    'SEQ' => $seq,
//                    'DATA_SEQ' => 1,
//                    'ATTACH_YN' => 'N',
//                    'DATA' => $body,
//                ]);

                // 메일 발송내역 저장
                MailSend::insert([
                    'ml_sid' => $data['ml_sid'] ?? 0,
                    'wiseu_seq' => $seq,
                    'receiver_name' => $receiver_name,
                    'receiver_email' => $receiver_email,
                    'subject' => $subject,
                    'contents' => $body,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return 'suc';
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    // 메일 발송 상태 업데이트
    public function mailSendStatusUpdate()
    {
        $mailConfig = $this->mailConfig;

        $whereIn = MailSend::where('status', 'R')->whereRaw('DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)')->limit(1000)->pluck('wiseu_seq');

        if (!$whereIn->isEmpty()) {
            $wiseuLog = WiseUMailLog::where('ECARE_NO', $mailConfig['eCareNo'])->whereIn('CUSTOMER_KEY', $whereIn)->get();

            foreach ($wiseuLog as $row) {
                $code = $row->ERROR_CD;

                if (!empty($mailConfig['code'][$code])) {
                    $mail_send = MailSend::where('wiseu_seq', $row->CUSTOMER_KEY)->first();

                    if ($code === '250' || $code === '000') {
                        $mail_send->status = 'S';
                    } else {
                        $mail_send->status = 'F';
                    }

                    $mail_send->status_msg = $mailConfig['code'][$code];
                    $mail_send->update();
                }
            }
        }
    }
}
