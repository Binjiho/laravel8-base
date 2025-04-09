@extends('admin.layouts.popup-layout')

@section('addStyle')
@endsection

@section('contents').
<div class="sub-tit-wrap">
    <h3 class="sub-tit">회원 검색</h3>
</div>

<form id="searchF" name="searchF" class="sch-form-wrap">
    <fieldset>
        <legend class="hide">검색</legend>
        <div class="table-wrap">
            <table class="cst-table">
                <colgroup>
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col>
                    <col style="width: 10%;">
                </colgroup>

                <tbody>
                <tr>
                    <th scope="">검색</th>
                    <td class="text-left">
                        <select name="field" class="form-item">
                            @foreach($userConfig['popup-search-field'] as $key => $val)
                                <option value="{{ $key }}" {{ (request()->field ?? '') == $key ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td class="text-left">
                        <input type="text" name="keyword" value="{{ request()->keyword ?? '' }}" class="form-item">
                    </td>

                    <td>
                        <button type="submit" class="btn btn-small color-type5">검 색</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
</form>

<div class="table-wrap mt-50" style="margin-top: 10px;">
    <table class="cst-table list-table">
        <caption class="hide">목록</caption>

        <colgroup>
            <col style="width: 20%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col>
            <col style="width: 8%;">
        </colgroup>

        <thead>
        <tr>
            <th scope="col">회원아이디</th>
            <th scope="col">회원등급-세부등급</th>
            <th scope="col">이름</th>
            <th scope="col">직장명(기관명)</th>
            <th scope="col">선택</th>
        </tr>
        </thead>

        <tbody>
        @forelse($list ?? [] as $row)
            <tr data-sid="{{ $row->sid }}">
                <td>{{ $row->id }}</td>
                <td>{{ $userConfig['level'][$row->level ?? ''] ?? '' }}</td>
                <td>{{ $row->name_kr ?? '' }}</td>
                <td>{{ $row->company ?? '' }}</td>
                <td>
                    <a href="javascript:void(0);" class="btn btn-small color-type2 user-select">
                        선택
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">검색된 데이터가 없습니다.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@isset($list)
    {{ $list->links('pagination::custom') }}
@endisset
@endsection

@section('addScript')
    <script>
        const form = '#user-search-frm';

        $(document).on('click', '.user-select', function () {
            // 부모창에 회원 정보 전달
            if (window.opener && !window.opener.closed) {
                callbackAjax('{{ route('member.data') }}', {
                    'case': 'select-member-info',
                    'user_sid': $(this).closest('tr').data('sid'),
                }, function (data, error) {
                    if (error) {
                        ajaxErrorData(error);
                        return false;
                    }
                    window.opener.setUserInfo(data.user);
                    window.close();
                });
            } else {
                window.close();
            }
        });
    </script>
@endsection
