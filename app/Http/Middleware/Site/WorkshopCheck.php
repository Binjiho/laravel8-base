<?php

namespace App\Http\Middleware\Site;

use Closure;
use Illuminate\Http\Request;

class WorkshopCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $work_code = $request->work_code;
        $config = getConfig('workshop')[$work_code] ?? null;

        if (is_null($config)) {
            return notFoundRedirect();
        }

        // 로그인 사용시 로그인 안되어 있다면
        if($config['use']['login'] && !thisAuth()->check()) {
            return authRedirect();
        }

        // 관리자 계정이 아닐경우 권한체크
        if (!isAdmin()) {
            $userLevel = thisLevel(); // 회원 권한

            // 접근 권한이 설정되어있을 경우 해당 level 체크
            switch ($request->route()->getName()) {
                default:
                    break;
            }
        }

        return $next($request);
    }
}
