<?php

namespace App\Services\Admin\Member;

use App\Models\User;
use App\Models\Fee;
use App\Exports\MemberExcel;
use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class MemberServices
 * @package App\Services
 */
class MemberServices extends AppServices
{
    public function indexService(Request $request)
    {
        $li_page = $request->li_page ?? 20;

        $memberCase = $request->case;
        $level = $request->level;

        $name_kr = $request->name_kr;
        $id = $request->id;
        $email = $request->email;
        $license_number = $request->license_number;
        $company = $request->company;
        $phone = $request->phone;
        $emailReception = $request->emailReception;

        $query = User::orderByDesc('sid'); // 삭제 제외 전체 회원 (승인 & 미승인)

        switch ($memberCase) {
            case 'gubunN' : // 일반회원
                $excelName = '일반회원';
                $query = User::where('gubun', 'N')->orderByDesc('created_at');
                break;
            case 'gubunS' : // 특별회원
                $excelName = '특별회원';
                $query = User::where('gubun', 'S')->orderByDesc('created_at');
                break;
            case 'gubunG' : // 단체회원
                $excelName = '단체회원';
                $query = User::where('gubun', 'G')->orderByDesc('created_at');
                break;
            case 'withdraw' : // 탈퇴회원
                $excelName = '탈퇴회원';
                $query = User::where('del_type', 1)->orderByDesc('created_at');
                break;
            case 'elimination' : // 삭제회원
                $excelName = '삭제회원';
                $query = User::onlyTrashed()->where('del', 'Y')->where('del_type', 2)->orderByDesc('created_at');
                break;
        }

        if ($level) {
            $query->where('level', $level);
        }

        if ($name_kr) {
            $query->where(function ($q) use($name_kr) {
                $q->where('name_kr', 'like', "%{$name_kr}%")
                    ->orWhere('name_en', 'like', "%{$name_kr}%");
            });
        }
        if ($id) {
            $query->where('id', 'like', "%{$id}%");
        }
        if ($email) {
            $query->where(function ($q) use($email) {
                $q->where('email', 'like', "%{$email}%")
                    ->orWhere('managerEmail', 'like', "%{$email}%");
            });
        }

        if ($license_number) {
            $query->where('license_number', 'like', "%{$license_number}%");
        }

        if ($company) {
            $query->where('company', 'like', "%{$company}%");
        }

        if ($phone) {
            $query->where(function ($q) use($phone) {
                $q->where('phone', 'like', "%{$phone}%")
                    ->orWhereRaw("REPLACE(phone, '-', '') LIKE ?", ["%" . str_replace('-', '', $phone) . "%"])
                    ->orWhere('managerTel', 'like', "%{$phone}%")
                    ->orWhereRaw("REPLACE(managerTel, '-', '') LIKE ?", ["%" . str_replace('-', '', $phone) . "%"]);
            });
        }

        if ($emailReception) {
            $query->where('emailReception', $emailReception);
        }

        // 엑셀 다운로드 할때
        if ($request->excel) {
            $this->data['total'] = $query->count();
            $this->data['collection'] = $query->lazy();
            return (new CommonServices())->excelDownload(new MemberExcel($this->data), date('Y-m-d').'_'.($excelName ?? '회원정보'));
        }

        $list = $query->paginate($li_page);
        $this->data['list'] = setListSeq($list);
        $this->data['li_page'] = $li_page;
        $this->data['memberCase'] = empty($memberCase) ? [] : ['case' => $memberCase];

        // 레벨별 카운트
        $this->data['levelCnt'] = User::get('level')->groupBy('level')
            ->map(function ($group) {
                return $group->count();
            });

        // 전체 유저 수 카운트
        $this->data['levelCnt']['total'] = User::count();

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['user'] = User::withTrashed()->findOrFail($request->sid);
        $this->data['captcha'] = (new CommonServices())->captchaMakeService();

        return $this->data;
    }

    public function popupSearchService(Request $request)
    {
        $field = $request->field;
        $keyword = $request->keyword;

        if (!empty($field) && !empty($keyword)) {
            $query = User::orderByDesc('sid'); // 삭제 제외 전체 회원 (승인 & 미승인)

            switch ($field) {
                case 'id':
                    $query->where('id', 'like', "%{$keyword}%");
                    break;

                case 'name':
                    $query->where(function ($q) use ($keyword) {
                        $q->where('name_kr', 'like', "%{$keyword}%")
                            ->orWhere('name_en', 'like', "%{$keyword}%");
                    });
                    break;

                case 'company':
                    $query->where('company', 'like', "%{$keyword}%");
                    break;

            }

            $list = $query->paginate(20);
            $this->data['list'] = setListSeq($list);
        }

        return $this->data;
    }


    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'user-update':
                return $this->userUpdateServices($request);
            case 'user-delete': // 어드민 회원 삭제 처리
                return $this->userDelete($request);
            case 'user-eliminationDelete': // 탈퇴 회원 삭제 처리
                return $this->userEliminationDelete($request);
//            case 'user-forceDelete': // 회원 완전 삭제 처리
//                return $this->userForceDelete($request);
            case 'user-restore': // 회원 정보 복원 (탈퇴회원)
                return $this->userRestore($request);
            case 'user-login':
                return $this->userLogin($request);
            case 'pw-reset':
                return $this->passwordReset($request);
            case 'change-level':
                return $this->changeLevel($request);
            case 'change-isAdmin':
                return $this->changeIsAdmin($request);
            case 'select-member-info':
                return $this->selectMemberInfo($request);
            default:
                return notFoundRedirect();
        }
    }

    private function userUpdateServices(Request $request)
    {
        $this->feeConfig = config('site.fee');
        $this->transaction();

        try {
            $user = User::findOrFail($request->sid);

            $user->timestamps = false; // updated_at 자동 갱신 비활성화

            /**
             * 관리자페이지에서 생일 변경시 55세이상이면, 종신회비(미납일시) 업데이트
             */
            if($user->birth != $request->birth){
                $isOlder = User::isAge55OrOlder($request->birth);
                if($isOlder){
                    $lifeFee = Fee::where(['user_sid'=>$user->sid, 'del'=>'N', 'gubun'=>'N', 'category'=>'C', 'payment_status'=>'N'])->first();
                    if($lifeFee){
                        $lifeFee->price = $this->feeConfig['price']['N']['D'];
                        $lifeFee->update();
                    }
                }else{
                    $lifeFee = Fee::where(['user_sid'=>$user->sid, 'del'=>'N', 'gubun'=>'N', 'category'=>'C', 'payment_status'=>'N'])->first();
                    if($lifeFee){
                        $lifeFee->price = $this->feeConfig['price']['N']['C'];
                        $lifeFee->update();
                    }
                }
            }

            /**
             * 관리자페이지에서 레벨 변경시 업데이트
             */
            if($user->level != $request->level){
                $user->level = $request->level;
                $user->gubun = $request->gubun;
                $user->grade = $request->grade;
            }

            $user->setByData($request);

            $user->update();

            $this->dbCommit( ( checkUrl() == 'admin' ? '관리자 ' : '사용자' ).' - 회원정보 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '회원정보가 수정 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function userDelete(Request $request)
    {
        $this->transaction();

        try {
            $user = User::findOrFail($request->sid);

            $user->timestamps = false; // updated_at 자동 갱신 비활성화

            $user->delete();

            $this->dbCommit('관리자 - 회원 탈퇴처리');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function userEliminationDelete(Request $request)
    {
        $this->transaction();

        try {
            $user = User::onlyTrashed()->findOrFail($request->sid);

            $user->timestamps = false; // updated_at 자동 갱신 비활성화
            $user->delete();

            $this->dbCommit('관리자 - 회원 삭제처리');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

//    private function userForceDelete(Request $request)
//    {
//        $this->transaction();
//
//        try {
//            $user = User::onlyTrashed()->findOrFail($request->sid);
//            $user->forceDelete();
//
//            $this->dbCommit('관리자 회원 완전 삭제');
//
//            return $this->returnJsonData('alert', [
//                'case' => true,
//                'msg' => '회원정보가 전부 삭제 되었습니다.',
//                'location' => $this->ajaxActionLocation('reload'),
//            ]);
//        } catch (\Exception $e) {
//            return $this->dbRollback($e);
//        }
//    }

    private function userRestore(Request $request)
    {
        $this->transaction();

        try {
            $user = User::findOrFail($request->sid);

            $user->timestamps = false; // updated_at 자동 갱신 비활성화
            $user->del_type = null;
            $user->del_request_at = null;
            $user->update();

//            Fee::onlyTrashed()->where('user_sid', $user->sid)->restore(); // 회비 복구

            $this->dbCommit('관리자 - 회원 정보복원');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '복원 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function passwordReset(Request $request)
    {
        $this->transaction();

        try {
            $reset_pw = 'kosenv1234';

            $user = User::withTrashed()->findOrFail($request->sid);

            $user->timestamps = false; // updated_at 자동 갱신 비활성화
            $user->password = Hash::make($reset_pw);
            $user->update();

            $this->dbCommit('관리자 회원 비밀번호 초기화');

            return $this->returnJsonData('alert', [
                'msg' => "비밀번호 초기화 되었습니다.\n초기화 비밀번호 : {$reset_pw}"
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function changeLevel(Request $request)
    {
        $this->transaction();

        try {
            $user = User::withTrashed()->findOrFail($request->sid);

            $user->timestamps = false; // updated_at 자동 갱신 비활성화

            switch ($request->value){
                case 'NA':
                    $user->gubun = 'N';
                    $user->grade = 'A';
                    break;
                case 'NB':
                    $user->gubun = 'N';
                    $user->grade = 'B';
                    break;
                case 'SA':
                    $user->gubun = 'S';
                    $user->grade = 'A';
                    break;
                case 'SB':
                    $user->gubun = 'S';
                    $user->grade = 'B';
                    break;
                case 'SC':
                    $user->gubun = 'S';
                    $user->grade = 'C';
                    break;
                case 'G':
                    $user->gubun = 'G';
                    $user->grade = NULL;
                    break;
                default:
                    break;
            }
            $user->level = $request->value;
            $user->update();

            $this->dbCommit('관리자 - 회원등급 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '수정 되었습니다.',
                'location' => $this->ajaxActionLocation('reload')
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function changeIsAdmin(Request $request)
    {
        $this->transaction();

        try {
            $user = User::withTrashed()->findOrFail($request->sid);

            $user->timestamps = false; // updated_at 자동 갱신 비활성화
            $user->is_admin = $request->value;
            $user->update();

            $this->dbCommit('관리자 - 관리자지정 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '수정 되었습니다.',
                'location' => $this->ajaxActionLocation('reload')
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function userLogin(Request $request)
    {
        $user = User::findOrFail($request->sid);
        auth('web')->login($user);

        return $this->returnJsonData('location', $this->ajaxActionLocation('blank', env('APP_URL')));
    }

    private function selectMemberInfo(Request $request)
    {
        $user = User::withTrashed()->findOrFail($request->user_sid);
        $customUser = $user->addCustomData();

        return $this->returnJsonData('user', $customUser);
    }
}
