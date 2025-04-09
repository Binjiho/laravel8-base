<?php

namespace App\Services\Main;

use App\Models\Board;
use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class MainServices
 * @package App\Services
 */
class MainServices extends AppServices
{
    public function indexService(Request $request)
    {
        $exceptionBoardPopup = [];
        $allCookies = $request->cookies->all();

        foreach ($allCookies as $key => $val) {
            // 게시판 팝업 오늘하루 보지않기 있는지 체크
            if (strpos($key, 'board-popup-') !== false) {
                $boardSid = (int)str_replace('board-popup-', '', $key);
                $exceptionBoardPopup[] = $boardSid;
            }
        }

        // 게시판 팝업
        $this->data['boardPopupList'] = Board::withCount('files')
            ->where(['hide' => 'N', 'popup' => 'Y'])
            ->whereNotIn('sid', $exceptionBoardPopup)
            ->whereHas('popups', function ($q) {
                $q->where('popup_sDate', '<=', now()->format('Y-m-d'))
                    ->where('popup_eDate', '>=', now()->format('Y-m-d'));

            })
            ->get();

        // 학술대회 일정
        $query = Board::where(['code' => 'event-schedule', 'hide' => 'N'])->orderBy('event_sDate');
        if (!empty($request->year)) {
            $query->whereYear('event_sDate', $request->year);
        }
        if (!empty($request->month)) {
            $query->whereMonth('event_sDate', $request->month);
        }
        $this->data['event_list'] = $query->get();

        // 학회소식
        $query = Board::where(['hide' => 'N', 'main' => 'Y'])->whereIn('code', ['notice','eco','mems','monthly-magazine'])->orderByDesc('sid');
        if(!empty($request->bcode)){
            $query->where('code',$request->bcode);
        }
        $this->data['notice_list'] = $query->limit(5)->get();
        $this->data['photo'] = Board::where(['code' => 'photo', 'hide' => 'N', 'main' => 'Y'])->orderByDesc('event_sDate')->first(); // 포토갤러리

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            default:
                return notFoundRedirect();
        }
    }
}
