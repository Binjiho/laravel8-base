@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-contents">
        <div class="sub-tab-wrap">
            <ul class="sub-tab-menu cf">
                <li class="{{ empty($memberCase) ? 'on' : '' }}">
                    <a href="{{ route('member') }}">전체회원</a>
                </li>

                <li class="{{ request()->case == 'gubunN' ? 'on' : '' }}">
                    <a href="{{ route('member', ['case' => 'gubunN']) }}">일반회원</a>
                </li>

                <li class="{{ request()->case == 'gubunS' ? 'on' : '' }}">
                    <a href="{{ route('member', ['case' => 'gubunS']) }}">특별회원</a>
                </li>

                <li class="{{ request()->case == 'gubunG' ? 'on' : '' }}">
                    <a href="{{ route('member', ['case' => 'gubunG']) }}">단체회원</a>
                </li>

                <li class="{{ request()->case == 'withdraw' ? 'on' : '' }}">
                    <a href="{{ route('member', ['case' => 'withdraw']) }}">탈퇴회원</a>
                </li>

                <li class="{{ request()->case == 'elimination' ? 'on' : '' }}">
                    <a href="{{ route('member', ['case' => 'elimination']) }}">삭제회원</a>
                </li>
            </ul>
        </div>

        <form id="searchF" name="searchF" action="{{ route('member', $memberCase) }}" class="sch-form-wrap">
            <fieldset>
                <legend class="hide">검색</legend>
                <div class="table-wrap">
                    <table class="cst-table">
                        <colgroup>
                            <col style="width: 20%;">
                            <col style="width: 30%;">
                            <col style="width: 20%;">
                            <col style="width: 30%;">
                        </colgroup>

                        <tbody>
                        <tr>
                            <th scope="row">회원등급</th>
                            <td class="text-left">
                                <select name="level" class="form-item">
                                    <option value="">전체</option>

                                    @foreach($userConfig['level'] as $key => $val)
                                        <option value="{{ $key }}" {{ (request()->level ?? '') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <th scope="row">이름</th>
                            <td class="text-left">
                                <input type="text" name="name_kr" value="{{ request()->name_kr ?? '' }}" class="form-item">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">회원 ID</th>
                            <td class="text-left">
                                <input type="text" name="id" value="{{ request()->id ?? '' }}" class="form-item">
                            </td>

                            <th scope="row">이메일</th>
                            <td class="text-left">
                                <input type="text" name="email" value="{{ request()->email ?? '' }}" class="form-item">
                            </td>
                        </tr>

                        <tr>
{{--                            <th scope="row">면허번호</th>--}}
{{--                            <td class="text-left">--}}
{{--                                <input type="text" name="license_number" value="{{ request()->license_number ?? '' }}" class="form-item">--}}
{{--                            </td>--}}

                            <th scope="row">근무처 (직장명)</th>
                            <td class="text-left" colspan="3">
                                <input type="text" name="company" value="{{ request()->company ?? '' }}" class="form-item">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">휴대폰번호</th>
                            <td class="text-left">
                                <input type="text" name="phone" value="{{ request()->phone ?? '' }}" class="form-item">
                            </td>

                            <th scope="row">이메일 수신동의</th>
                            <td class="text-left">
                                <select name="emailReception" class="form-item">
                                    <option value="">전체</option>
                                    @foreach($userConfig['emailReception'] as $key => $val)
                                        <option value="{{ $key }}" {{ (request()->emailReception ?? '') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <input type="hidden" name="li_page" value="{{ $li_page }}" readonly>

                <div class="btn-wrap text-center">
                    <button type="submit" class="btn btn-type1 color-type17">검색</button>
                    <a href="{{ route('member', $memberCase) }}" class="btn btn-type1 color-type18">검색 초기화</a>
                    <a href="{{ route('member.excel', request()->except(['page']) + $memberCase) }}" class="btn btn-type1 color-type19">데이터 엑셀 백업</a>
                </div>
            </fieldset>
        </form>

        <div class="table-wrap mb-50">
            <table class="cst-table abs-info-table">
                <colgroup>
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">

                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                </colgroup>

                <thead>
                <tr>
                    <th>구분</th>
                    <th>전체회원</th>
                    @foreach($userConfig['level'] as $key => $val)
                        <th>{{ $val }}</th>
                    @endforeach
                    <th>탈퇴회원</th>
                    <th>삭제회원</th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td>인원수</td>
                    <td>
                        <a href="{{ route('member') }}">{{ number_format($levelCnt['total']) }}</a>
                    </td>

                    @foreach($userConfig['level'] as $key => $val)
                        <td>
                            <a href="{{ route('member', ['level' => $key]) }}">
                                {{ number_format($levelCnt[$key] ?? 0) }}
                            </a>
                        </td>
                    @endforeach

                    <td>
                        <a href="{{ route('member',['case'=>'withdraw']) }}">{{ number_format($levelCnt['withdraw']) }}</a>
                    </td>
                    <td>
                        <a href="{{ route('member',['case'=>'elimination']) }}">{{ number_format($levelCnt['elimination']) }}</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="list-contop text-right cf">
            <span class="cnt full-left">
                [총 <strong>{{ number_format($list->total()) }}</strong>명]
            </span>

            @include('admin.layouts.include.li_page')
        </div>

        @switch(request()->case)
            @case('withdraw' /* 탈퇴 회원 */)
                @include('admin.member.include.withdraw-list')
                @break

            @case('elimination' /* 삭제 회원 */)
                @include('admin.member.include.elimination-list')
                @break

            @default
                @include('admin.member.include.default-list')
                @break
        @endswitch

        {{ $list->links('pagination::custom') }}
    </div>
@endsection

@section('addScript')
    <script>
        const dataUrl = '{{ route('member.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        // $(document).on('change', '.select-confirm', function () {
        //     callAjax(dataUrl, {
        //         'case': 'db-change',
        //         'sid': getPK(this),
        //         'field': 'confirm',
        //         'value': $(this).val(),
        //     });
        // });

        //회원등급 변경
        $(document).on('change', '.select-level', function () {
            callAjax(dataUrl, {
                'case': 'change-level',
                'sid': getPK(this),
                'value': $(this).val(),
            });
        });

        //관리자지정 변경
        $(document).on('click', '.is_admin', function () {
            let value = $(this).is(':checked') ? $(this).val() : 'N';
            callAjax(dataUrl, {
                'case': 'change-isAdmin',
                'sid': getPK(this),
                'value': value,
            });
        });

        //비밀번호 초기화
        $(document).on('click', '.pw-reset', function() {
            callAjax(dataUrl, {
                'case': 'pw-reset',
                'sid': getPK(this),
            });
        });

        //해당 회원 로그인
        $(document).on('click', '.user-login', function() {
            callAjax(dataUrl, {
                'case': 'user-login',
                'sid': getPK(this),
            });
        });
    </script>

    @yield('memberScript')
@endsection
