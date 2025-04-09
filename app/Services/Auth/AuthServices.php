<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;
use App\Services\MailRealSendServices;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthServices
 * @package App\Services
 */
class AuthServices extends AppServices
{
    public function signupAction(Request $request)
    {
        $this->data['gubun'] = $request->gubun ?? '';
        $this->data['captcha'] = (new CommonServices())->captchaMakeService();

        if(!empty($request->sid)){
            $this->data['user'] = User::findOrFail($request->sid);
            $this->data['target_name'] = $this->data['user']->name_kr ?? null;
            if($this->data['user']->gubun != 'N'){
                $this->data['target_name'] = $this->data['user']->manager ?? null;
            }
        }

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'register-step1':
                return $this->step1Service($request);
            case 'register-step2':
                return $this->step2Service($request);
            case 'uid-check':
                return $this->uidCheckServices($request);
            case 'captcha-compare':
                return $this->captchaCompareServices($request);
            case 'forget-uid':
                return $this->forgetUid($request);
            case 'forget-password':
                return $this->forgetPassword($request);
            case 'user-modify':
                return $this->userModifyServices($request);

            default:
                return notFoundRedirect();
        }
    }

    private function step1Service(Request $request)
    {
        $now_step = $request->step ?? 1;
        $next_step = $now_step+1;

        return $this->returnJsonData('location', $this->ajaxActionLocation('replace', route('join',['gubun'=>$request->gubun, 'step'=>$next_step]) ));
    }

    private function step2Service(Request $request)
    {
        $now_step = $request->step ?? 1;
        $next_step = $now_step+1;

        $this->transaction();

        try {
            $user = new User();
            $user->setByData($request);

            $user->save(); // created_at만 생성됨

            // 메일 발송
            $mailData = [
                'receiver_name' => $user->name_kr ?? '',
                'receiver_email' => $user->email ?? '',
                'body' => view("template.user-create", ['user' => $user])->render(),
            ];

            $mailResult = (new MailRealSendServices())->mailSendService($mailData, 'user-create');

            if ($mailResult != 'suc') {
                return $mailResult;
            }
            // END 메일 발송

            $this->dbCommit( ( checkUrl() == 'admin' ? '관리자 ' : '사용자' ).' - 회원가입');

            return $this->returnJsonData('location', $this->ajaxActionLocation('replace', route('join',['gubun'=>$request->gubun, 'step'=>$next_step, 'sid'=>$user->sid ?? 0 ]) ));
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function uidCheckServices(Request $request)
    {
        $user = User::withTrashed()->where(['id' => $request->id, 'del'=>'N'])->first();

        if (empty($user)) {
            $this->setJsonData('data', [
                $this->ajaxActionData('#id', 'chk', 'Y'),
            ]);

            return $this->returnJsonData('alert', [
                'msg' => '사용가능한 아이디 입니다.',
            ]);
        } else {
            $this->setJsonData('focus', '#user_id');

            return $this->returnJsonData('alert', [
                'msg' => '사용중인 ID입니다. 다른 ID 입력해주세요.',
            ]);
        }
    }

    private function captchaCompareServices(Request $request)
    {
        if($request->captcha_input === session('captcha')){
            $this->setJsonData('log', 'suc');

            $this->setJsonData('data', [
                $this->ajaxActionData('#captcha_input', 'chk', 'Y'),
                'log' => 'suc',
            ]);

            return $this->returnJson();
        }
        $this->setJsonData('data', [
            $this->ajaxActionData('#captcha_input', 'chk', 'N'),
            'log' => 'fail',
        ]);

        return $this->returnJson();
    }

    private function forgetUid(Request $request)
    {
        $user = User::where(['name_kr' => $request->name_kr, 'email'=>$request->email, 'del'=>'N'])->first();

        $this->setJsonData('addCss', [
            $this->ajaxActionCss('.find-result-con', 'display', 'block'),
        ]);

        if(!empty($user)){
            $html = $request->name_kr."님의 아이디는 <span class=\"text-red\">".$user->id."</span> 입니다.";

            return $this->returnJsonData('html', [
                $this->ajaxActionHtml('.find-result-con', $html),
            ]);
        }else{
            $this->setJsonData('input', [
                $this->ajaxActionInput('#forget-frm input[name=name_kr]', ''),
                $this->ajaxActionInput('#forget-frm input[name=email]', ''),
            ]);

            $html = "일치하는 정보가 없습니다. <br class=\"m-show\">가입 정보를 다시 확인 해주세요.";
            $html .="<div class=\"btn-wrap\">";
            $html .="<a href='".route('joinInfo')."' class=\"btn btn-small color-type4\">회원가입</a>";
            $html .="</div>";

            return $this->returnJsonData('html', [
                $this->ajaxActionHtml('.find-result-con', $html),
            ]);
        }
    }

    private function forgetPassword(Request $request)
    {
        $user = User::where(['id'=>$request->user_id, 'name_kr' => $request->name_kr, 'del'=>'N'])->first();

        if (empty($user)) {

            return $this->returnJsonData('result', [
                'res' => 'NOT',
                'msg' => '일치하는 정보가 없습니다.',
            ]);

        } else {

            $this->transaction();

            try {
                $tempPassword = $this->tempPassword();

//                $user->user_passwd = hash("sha512", $tempPassword);
                $user->password = Hash::make($tempPassword);
                $user->imsi_password = 'Y';
                $user->password_at = date('Y-m-d H:i:s');
                $user->update();

                $user->tempPassword = $tempPassword;

                // 임시비밀번호 메일 발송
                $mailData = [
                    'receiver_name' => $user->name_kr,
                    'receiver_email' => $user->email,
                    'body' => view("template.forget-password", ['user' => $user, 'tempPassword'=>$tempPassword])->render(),
                    'etc' => null,
                ];

                $mailResult = (new MailRealSendServices())->mailSendService($mailData, 'forget-password');

                if ($mailResult != 'suc') {
                    return $mailResult;
                }

                $this->dbCommit( ( checkUrl() == 'admin' ? '관리자 ' : '사용자' ).' - 비밀번호 찾기');

                return $this->returnJsonData('result', [
                    'res' => 'SUC',
                    'msg' => $user->name_kr.'님의 초기화된 비밀번호는 <span class=\"text-red\">'.$tempPassword.'</span>입니다.',
                ]);

            } catch (\Exception $e) {
                return $this->dbRollback($e);
            }
        }
    }
    private function tempPassword()
    {
        $feed1 = "0123456789";
        $feed2 = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $tempPassword = '';

        for ($i = 0; $i < 3; $i++) {
            $tempPassword .= substr($feed1, rand(0, strlen($feed1) - 1), 1);
        }

        for ($i = 0; $i < 3; $i++) {
            $tempPassword .= substr($feed2, rand(0, strlen($feed2) - 1), 1);
        }

        return str_shuffle($tempPassword);
    }

    private function userModifyServices(Request $request)
    {
        $this->transaction();

        try {
            $user = User::findOrFail($request->sid);

            // 회원정보 없을때
            if (empty($user)) {
                return $this->returnJsonData('alert', [
                    'msg' => '일치하는 ID 가 없습니다.',
                ]);
            }
            $user->setByData($request);
            $user->update();

            $this->dbCommit( ( checkUrl() == 'admin' ? '관리자 ' : '사용자' ).' - 회원정보 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '수정 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

}
