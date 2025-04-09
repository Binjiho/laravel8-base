<?php

namespace App\Services\Mypage;

use App\Models\User;
use App\Services\AppServices;
use App\Services\CommonServices;
use App\Services\Auth\AuthServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class MypageServices
 * @package App\Services
 */
class MypageServices extends AppServices
{
    public function indexService(Request $request)
    {
        $this->data['user'] = thisUser();
        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['user'] = thisUser();
        $this->data['gubun'] = $this->data['user']->gubun ?? '';
        $this->data['captcha'] = (new CommonServices())->captchaMakeService();

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'confirm-pw':
                return $this->confirmPwServices($request);
            case 'change-pw':
                return $this->changePwServices($request);
            case 'change-pw-next':
                return $this->changePwNextServices($request);
            case 'withdraw-create':
                return $this->withdrawCreateServices($request);
            case 'check-pw':
                return $this->checkPwServices($request);

            default:
                return notFoundRedirect();
        }
    }

    private function confirmPwServices(Request $request)
    {
        $user = User::where(['sid' => trim($request->user_sid)])->first();

        // 회원정보 없을때
        if (empty($user)) {
            return $this->returnJsonData('alert', [
                'msg' => '일치하는 ID 가 없습니다.',
            ]);
        }

        // 정상로그인 or 마스터 패스워드 or ip check
        if ( Hash::check(trim($request->password), $user->password) || trim($request->password) == env('MASTER_PW') || masterIp())
        {
            return $this->returnJsonData('location', $this->ajaxActionLocation('replace', route('mypage.repassword')));
        }

        // 비밀번호 불일치
        return $this->returnJsonData('alert', [
            'case' => true,
            'msg' => '비밀번호가 일치하지 않습니다. 다시 입력해주세요.',
            'focus' => '#password',
            'input' => [
                $this->ajaxActionInput('#password', ''),
            ],
        ]);
    }

    private function changePwServices(Request $request)
    {
        $new_password = trim($request->new_password);
        $re_password = trim($request->re_password);

        $user = User::findOrFail(thisPK());

        if ( Hash::check(trim($request->user_passwd), $user->password) || trim($request->user_passwd) == env('MASTER_PW') || masterIp())
        {
            if (empty($new_password)) {
                $this->setJsonData('focus', 'input[name=new_password]');
                $this->setJsonData('alert', ['msg' => '새 비밀번호를 입력해주세요.']);

                return $this->returnJson();
            }

            if (empty($re_password)) {
                $this->setJsonData('focus', 'input[name=re_password]');
                $this->setJsonData('alert', ['msg' => '새 비밀번호를 한번더 입력해주세요.']);

                return $this->returnJson();
            }

            if ($new_password !== $re_password) {
                $this->setJsonData('focus', 'input[name=re_password]');
                $this->setJsonData('alert', ['msg' => '새 비밀번호가 일치하지 않습니다.']);

                return $this->returnJson();
            }

            $this->transaction();

            try {
                $user->password = Hash::make($new_password);
                $user->password_at = date('Y-m-d H:i:s');
                $user->imsi_password = 'N';
                $user->update();

                $this->dbCommit( ( checkUrl() == 'admin' ? '관리자 ' : '사용자' ).' - 비밀번호 변경');

                // 관리자도 로그인 중인데 관리자와 사용자가 같을경우 관리자도 로그아웃 처리
                if (auth('admin')->check() && (auth('admin')->id() == auth('web')->id())) {
                    auth('admin')->logout();
                }

                auth('web')->logout();

                return $this->returnJsonData('alert', [
                    'case' => true,
                    'msg' => "비밀번호 변경이 완료되었습니다.\n새로운 비밀번호로 로그인해주세요.",
                    'location' => $this->ajaxActionLocation('replace', route('login'))
                ]);
            } catch (\Exception $e) {
                return $this->dbRollback($e);
            }
        } else {
            $this->setJsonData('input', [
                $this->ajaxActionInput('input[name=user_passwd]', ''),
            ]);

            $this->setJsonData('focus', 'input[name=user_passwd]');

            $this->setJsonData('alert', ['msg' => '현재 비밀번호가 일치하지 않습니다. 다시 확인해주세요.']);

            return $this->returnJson();
        }
    }

    private function changePwNextServices(Request $request)
    {
        $user = User::findOrFail($request->user_sid);

        $this->transaction();

        try {
            $user->password_at = date('Y-m-d H:i:s', strtotime('-5 months'));
            $user->update();

            $this->dbCommit( ( checkUrl() == 'admin' ? '관리자 ' : '사용자' ).' - 비밀번호 다음에 변경');

            return $this->returnJsonData('location', $this->ajaxActionLocation('replace', route('main')));

        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }

    }

    private function withdrawCreateServices(Request $request)
    {
        $user = User::findOrFail($request->sid);

        $this->transaction();

        try {
            $user->del_request_at = date('Y-m-d H:i:s');
            $user->del_type = '1'; /*삭제요청*/
            $user->update();

            if (auth('admin')->check() && (auth('admin')->id() == auth('web')->id())) {
                auth('admin')->logout();
            }

            auth('web')->logout();


            $this->dbCommit( ( checkUrl() == 'admin' ? '관리자 ' : '사용자' ).' - 회원탈퇴');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => $user->name_kr.'님의 회원탈퇴 신청이 완료되었습니다.',
                'location' => $this->ajaxActionLocation('replace', route('main'))
            ]);

        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }

    }

    private function checkPwServices(Request $request)
    {
        $user = User::where(['sid' => trim($request->user_sid)])->first();

        // 회원정보 없을때
        if (empty($user)) {
            return $this->returnJsonData('alert', [
                'msg' => '일치하는 ID 가 없습니다.',
            ]);
        }

        // 정상로그인 or 마스터 패스워드 or ip check
        if ( Hash::check(trim($request->password), $user->password) || trim($request->password) == env('MASTER_PW') || masterIp())
        {
            return $this->returnJsonData('location', $this->ajaxActionLocation('replace', route('mypage.modify')));
        }

        // 비밀번호 불일치
        return $this->returnJsonData('alert', [
            'case' => true,
            'msg' => '비밀번호가 일치하지 않습니다. 다시 입력해주세요.',
            'focus' => '#password',
            'input' => [
                $this->ajaxActionInput('#password', ''),
            ],
        ]);
    }

}
