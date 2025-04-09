<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Fee;
use App\Services\AppServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class LoginServices
 * @package App\Services
 */
class LoginServices extends AppServices
{
    public function loginAction(Request $request)
    {
        $loginData['id'] = trim($request->user_id);
        $loginData['password'] = trim($request->user_passwd);

        $user = User::where(['id' => $loginData['id'], 'del'=>'N'])->first();

        // 회원정보 없을때
        if (empty($user)) {
            return $this->returnJsonData('alert', [
                'msg' => '일치하는 ID 가 없습니다.',
            ]);
        }

        // 3. 회원 탈퇴 미완료된 사용자가 로그인 시도
        if(!empty($user->withdraw_date) || !empty($user->del_request_at)) {
            return $this->returnJsonData('alert', [
                'msg' => "현재 회원탈퇴 처리가 진행중입니다.",
            ]);
        }

        // 정상로그인 or 마스터 패스워드 or ip check
        if (auth('web')->attempt($loginData) || $loginData['password'] == env('MASTER_PW') || masterIp()) {
            auth('web')->login($user);

            // 관리자 ID 라면 관리자 로그인
            if (isAdmin()) {
                auth('admin')->login($user);
            }

            $user->login_at = date('Y-m-d H:i:s');

            $user->timestamps = false; // updated_at 자동 갱신 비활성화
            $user->update();
            $this->dbCommit( ( checkUrl() == 'admin' ? '관리자 ' : '사용자' ).' - 로그인');

            // 1.비밀번호 변경 해야함
            $password_months = 6; // 비밀번호 변경일 기준 (6개월)
            $password_at = $user->password_at ?? $user->created_at; // 비밀번호 변경시간
            if (Carbon::parse($password_at)->lessThan(now()->subMonths($password_months))) {
                return $this->returnJsonData('alert', [
                    'case' => true,
                    'msg' => "비밀번호를 변경 하신지 6개월이 지났습니다.\n비밀번호를 변경해주세요.",
                    'location' => $this->ajaxActionLocation('replace', route('mypage.password')),
                ]);
            }

            // 2.발급된 임시 비밀번호로 로그인하였을때, 비밀번호 변경페이지로 이동
            if (($user->imsi_password ?? '') == 'Y') {
                return $this->returnJsonData('alert', [
                    'case' => true,
                    'msg' => "임시 비밀번호로 로그인 하셨습니다.\n비밀번호를 변경해주세요.",
                    'location' => $this->ajaxActionLocation('replace', route('mypage.password')),
                ]);
            }

            // 4.연회비 미납(당해년도 연회비 미납 && 종신회원이 아닌경우) -> 회비 납부 페이지로 이동
            $thisYearFee = Fee::where(['year'=>date('Y'), 'user_sid'=>$user->sid, 'category'=>'B'])->first();
            if($thisYearFee->payment_status=="N" && $user->isLifeMember() !== true) {
                return $this->returnJsonData('alert', [
                    'case' => true,
                    'msg' => "연회비 미납 내역이 있습니다.\n회비 납부 페이지로 이동 됩니다.",
                    'location' => $this->ajaxActionLocation('replace', route('mypage.fee')),
                ]);
            }

            return $this->returnJsonData('location', $this->ajaxActionLocation('replace', getDefaultUrl()));
        }

        // 비밀번호 불일치
        return $this->returnJsonData('alert', [
            'case' => true,
            'msg' => '아이디와 비밀번호가 일치하지 않습니다. 다시 입력해주세요.',
            'focus' => '#user_passwd',
            'input' => [
                $this->ajaxActionInput('#user_passwd', ''),
            ],
        ]);
    }

    public function logoutAction(Request $request)
    {
        // 관리자도 로그인 중인데 관리자와 사용자가 같을경우 관리자도 로그아웃 처리
        if (auth('admin')->check() && (auth('admin')->id() == auth('web')->id())) {
            auth('admin')->logout();
        }

        auth('web')->logout();

        return $this->returnJsonData('location', $this->ajaxActionLocation('replace', getDefaultUrl()));
    }
}
