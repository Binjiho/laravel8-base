<div class="table-wrap" style="margin-top: 10px;">
    <table class="cst-table list-table">
        <caption class="hide">목록</caption>

        <colgroup>
            <col style="width: 3%;">
            <col style="width: 10%;">
            <col style="width: 6%;">
            <col style="width: 6%;">
            <col>

            <col style="width: 8%;">
            <col style="width: 10%;">
            <col style="width: 8%;">
            <col style="width: 7%;">
            <col style="width: 6%;">

            <col style="width: 5%;">
            <col style="width: 5%;">
            <col style="width: 6%;">
        </colgroup>

        <thead>
        <tr>
            <th scope="col">번호</th>
            <th scope="col">회원등급-세부등급</th>
            <th scope="col">회원번호</th>
            <th scope="col">이름</th>
            <th scope="col">아이디</th>

            <th scope="col">직장명<br>(기관명)</th>
            <th scope="col">이메일</th>
            <th scope="col">휴대폰번호</th>
            <th scope="col">회비내역</th>
            <th scope="col">가입일</th>

            <th scope="col">최종수정일</th>
            <th scope="col">탈퇴요청일</th>
            <th scope="col">관리</th>
        </tr>
        </thead>

        <tbody>
        @forelse($list as $row)
            <tr data-sid="{{ $row->sid }}">
                <td>{{ $row->seq }}</td>
                <td>
                    {{ $userConfig['level'][$row->level ?? ''] ?? '' }}
                </td>
                <td>
                    {{ $row->sid }}
                </td>
                <td>
                    {{ $row->name_kr }}
                </td>
                <td>
                    {{ $row->id }}
                </td>

                <td>{{ $row->company ?? '' }}</td>
                <td>{{ !empty($row->managerEmail) ? $row->managerEmail : ($row->email ?? '') }}</td>
                <td>{{ !empty($row->managerTel) ? $row->managerTel : ($row->phone ?? '') }}</td>
                <td>
                    <a href="{{ route('fee.popup.all-list', ['user_sid' => $row->sid]) }}" class="btn btn-small color-type1 call-popup" data-width="1400" data-height="700" data-name="fee-all-list">
                        전체보기
                    </a>
                </td>
                <td>{{ $row->created_at ?? '' }}</td>

                <td>{{ $row->updated_at ?? '' }}</td>
                <td>{{ $row->del_request_at ?? '' }}</td>
                <td>
                    <a href="javascript:void(0);" class="btn-admin btn-restore">
                        <img src="/assets/image/admin/refresh.png" alt="복원">
                    </a>
                    <a href="javascript:void(0);" class="btn-admin btn-del">
                        <img src="/assets/image/admin/ic_del.png" alt="삭제">
                    </a>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="13">회원정보가 없습니다.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).on('click', '.btn-restore', function () {
        if (confirm('회원 정보를 복원 하시겠습니까?')) {
            callAjax(dataUrl, {
                'case': 'user-restore',
                'sid': getPK(this),
            });
        }
    });

    $(document).on('click', '.btn-del', function () {
        if (confirm('탈퇴 처리를 승인 하시겠습니까?')) {
            callAjax(dataUrl, {
                'case': 'user-eliminationDelete',
                'sid': getPK(this),
            });
        }
    });
</script>