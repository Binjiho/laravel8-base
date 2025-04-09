@extends('layouts.web-layout')

@section('addStyle')
    <link rel="stylesheet" href="{{ asset('html/bbs/notice/assets/css/board.css') }}">
@endsection

@section('contents')
    @include('layouts.include.sub-menu-wrap')

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            <div id="board" class="board-wrap">
                <div class="sch-wrap type2 font-pre">
                    <form id="bbsSearch" action="{{ route('board', ['code' => $code]) }}" method="get">
                        <fieldset>
                            <legend class="hide">검색</legend>
                            <div class="form-group">
                                <select name="search" id="search" class="form-item sch-cate">
                                    @foreach($boardConfig['search'] as $key => $val)
                                        <option value="{{ $key }}" {{ ((request()->search ?? '') == $key) ? 'selected'  : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="keyword" id="keyword" class="form-item sch-key" placeholder="검색어를 입력하세요." value="{{ request()->keyword ?? '' }}">
                                <button type="submit" class="btn btn-sch"><span class="hide">검색</span></button>
                                <a href="{{ route('board', ['code' => $code]) }}"  class="btn btn-reset" style="align-content:center;">검색 초기화</a>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <ul class="board-list">
                    <!-- 공지 -->
                    @foreach($notice_list ?? [] as $row)
                    <li class="ef01 active" data-sid="{{ $row->sid }}">
                        <div class="list-con">
                            <div class="bbs-tit">
                                <div class="bbs-cate">공지</div><br>
                                <a href="{{ route('board.view', ['code' => $code, 'sid' => $row->sid]) }}" class="ellipsis2">
                                    {{ $row->subject }}
                                </a>
                                {!! $row->isNew() !!}
                            </div>
                            <span class="bbs-date">{{ $row->created_at->format('Y-m-d') }}</span>
                            <span class="bbs-name">작성자</span>
                            <span class="bbs-hit">{{ number_format($row->ref ?? 0) }} Hit</span>
                            @if($row->files_count > 0)
                            <span><img src="/html/bbs/notice/assets/image/ic_attach_file.png" alt=""></span>
                            @endif
                        </div>

                        @if(isAdmin())
                        <div>
                            <div class="bbs-admin">
                                <select name="hide" class="form-item">
                                    @foreach($boardConfig['options']['hide'] as $key => $val)
                                        <option value="{{ $key }}" {{ $key == $row->hide ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('board.upsert', ['code' => $row->code, 'sid' => $row->sid]) }}" class="btn btn-modify"><span class="hide">수정</span></a>
                                <a href="javascript:void(0);" class="btn btn-delete"><span class="hide">삭제</span></a>
                            </div>
                        </div>
                        @endif
                    </li>
                    @endforeach

                    @forelse($list as $row)
                    <li class="ef01" data-sid="{{ $row->sid }}">
                        <div class="list-con">
                            <div class="bbs-tit">
                                <a href="{{ route('board.view', ['code' => $code, 'sid' => $row->sid]) }}" class="ellipsis2">
                                    {{ $row->subject ?? '' }}
                                </a>

                                {!! $row->isNew() !!}
                            </div>
                            <span class="bbs-date">{{ $row->created_at->format('Y-m-d') }}</span>
                            <span class="bbs-name">작성자</span>
                            <span class="bbs-hit">{{ number_format($row->ref ?? 0) }} Hit</span>
                            @if($row->files_count > 0)
                                <span><img src="/html/bbs/notice/assets/image/ic_attach_file.png" alt=""></span>
                                {{--
                                <a href="{{ $row->plDownloadUrl() }}">
                                    <img src="/html/bbs/notice/assets/image/ic_attach_file.png" alt="">
                                </a>
                                --}}
                            @endif

                        </div>

                        @if(isAdmin())
                        <div>
                            <div class="bbs-admin">
                                <select name="hide" class="form-item">
                                        @foreach($boardConfig['options']['hide'] as $key => $val)
                                            <option value="{{ $key }}" {{ $key == $row->hide ? 'selected' : '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                <a href="{{ route('board.upsert', ['code' => $row->code, 'sid' => $row->sid]) }}" class="btn btn-modify"><span class="hide">수정</span></a>
                                <a href="javascript:void(0);" class="btn btn-delete"><span class="hide">삭제</span></a>
                            </div>
                        </div>
                        @endif
                    </li>
                    @empty
                    <!-- no data -->
                    <li class="no-data text-center">
                        <img src="/html/bbs/notice/assets/image/ic_nodata.png" alt=""> <br>
                        등록된 게시글이 없습니다.
                    </li>
                    @endforelse
                </ul>

                @if(isAdmin())
                <div class="btn-wrap text-right">
                    <a href="{{ route('board.upsert', ['code' => $code]) }}" class="btn btn-type1 color-type8">등록</a>
                </div>
                @endif

                {{ $list->links('pagination::custom') }}
            </div>
            <!-- //e:board -->

        </div>
    </article>
@endsection

@section('addScript')
    @include("board.default-script")

    @if(isAdmin())
        <script>
            $(document).on('change', 'select[name=hide]', function() {
                const ajaxData = {
                    case: 'db-change',
                    sid: getPK(this),
                    column: 'hide',
                    value: $(this).val(),
                }

                callAjax(dataUrl, ajaxData);
            });

            $(document).on('click', '.btn-delete', function() {
                const ajaxData = {
                    case: 'board-delete',
                    sid: getPK(this),
                }

                if (confirm('삭제 하시겠습니까?')) {
                    callAjax(dataUrl, ajaxData);
                }
            });
        </script>
    @endif
@endsection
