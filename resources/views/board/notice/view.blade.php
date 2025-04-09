@extends('layouts.web-layout')

@section('addStyle')
    <link rel="stylesheet" href="{{ asset('html/bbs/notice/assets/css/board.css') }}">
@endsection

@section('contents')
    @include('layouts.include.sub-menu-wrap')

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            <!-- s:board -->
            <div id="board" class="board-wrap">
                <div class="board-view">
                    <div class="view-contop">
                        <h4 class="view-tit">
                            <strong>{{ $board->subject ?? '' }}</strong>
                        </h4>
                        <div class="view-info">
                            <span><strong>작성자 : </strong>{{ env('APP_NAME') }}</span>
                            <span><strong>작성일 : </strong>{{ $board->created_at->format('Y-m-d') }}</span>
                            <span><strong>조회수 : </strong>{{ number_format($board->ref >> 0) }}</span>
                        </div>
                    </div>

                    @if($boardConfig['use']['link'] && !empty($board->link_url))
                        <div class="view-link text-right">
                            <a href="{{ $board->link_url }}" target="_blank">{{ $board->link_url }}</a>
                        </div>
                    @endif

                    <div class="view-contents editor-contents">
                        @if($boardConfig['use']['contents'] && !empty($board->contents))
                            {!! $board->contents !!}
                        @endif
                    </div>

                    @if($boardConfig['use']['plupload'] && $board->files_count > 0)
                        <div class="view-attach">
                            <div class="view-attach-con">
                                <div class="con">
                                    @foreach($board->files as $file)
                                        <a href="{{ $file->downloadUrl() }}">
                                            {{ $file->filename }}  (다운로드 : {{ number_format($file->download) }}회)
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="btn-wrap text-right">
                        <a href="{{ route('board', ['code' => $code]) }}" class="btn btn-type1 color-type7">목록</a>

                        @if(isAdmin() || thisPk() == $board->user_sid)
                            <a href="{{ route('board.upsert', ['code' => $code, 'sid' => $board->sid]) }}" class="btn btn-type1 color-type6">수정</a>
                            <a href="javascript:void(0);" class="btn-delete btn btn-type1 color-type7">삭제</a>
                        @endif
                    </div>
                </div>
            </div>
            <!-- //e:board -->

        </div>
    </article>
@endsection

@section('addScript')
    @include("board.default-script")

    @if(isAdmin() || thisPK() == $board->user_sid)
        <script>
            $(document).on('click', '.btn-delete', function() {
                if (confirm('삭제 하시겠습니까?')) {
                    callAjax(dataUrl, { case: 'board-delete', sid: {{ $board->sid }} });
                }
            });
        </script>
    @endif
@endsection
