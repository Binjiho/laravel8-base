<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Services\Mypage\MypageServices;
use Illuminate\Http\Request;

class MypageController extends Controller
{
    private $mypageServices;

    public function __construct()
    {
        $this->mypageServices = (new MypageServices());

        view()->share([
            'userConfig' => getConfig('user'),
            'main_menu' => 'MYPAGE',
        ]);
    }

    public function intro(Request $request)
    {
        view()->share(['sub_menu' => 'S1']);
        return view('mypage.intro', $this->mypageServices->indexService($request));
    }
    //회원정보수정
    public function pwCheck(Request $request)
    {
        view()->share(['sub_menu' => 'S2']);
        return view('mypage.pwCheck', $this->mypageServices->indexService($request));
    }
    public function modify(Request $request)
    {
        view()->share(['sub_menu' => 'S2']);
        return view('mypage.modify', $this->mypageServices->upsertService($request));
    }

    public function password(Request $request)
    {
        view()->share(['sub_menu' => 'S3']);
        return view('mypage.password', $this->mypageServices->indexService($request));
    }
    public function repassword(Request $request)
    {
        view()->share(['sub_menu' => 'S3']);
        return view('mypage.repassword', $this->mypageServices->indexService($request));
    }

    public function work_attend(Request $request)
    {
        view()->share(['sub_menu' => 'S5']);
        return view('mypage.workshop.attend_list');
    }

    //회원탈퇴
    public function withdraw(Request $request)
    {
        view()->share(['sub_menu' => 'S6']);
        return view('mypage.withdraw', $this->mypageServices->indexService($request));
    }


    public function MyPagedata(Request $request)
    {
        return $this->mypageServices->dataAction($request);
    }
}
