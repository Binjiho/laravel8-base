@php
    /*

    팝업 세팅시 추가되는 css

    크기조절 : width:auto; min-width:팝업넓이; max-width:팝업넓이; min-height:팝업높이; max-height:팝업높이;
    위치조절 : margin:0; margin-top:팝업위치위에서; margin-left:팝업위치왼쪽에서;

    ex) 크기조절
    <div class="popup-contents" style="width:auto; min-width:600px; max-width:600px; min-height:600px; max-height:600px;">

    ex) 위치조절
    <div class="popup-contents" style="margin:0; margin-top:100px; margin-left:100px;">

    ex) 크기 + 위치조절
    <div class="popup-contents" style="width:auto; min-width:600px; max-width:600px; min-height:600px; max-height:600px; margin:0; margin-top:100px; margin-left:100px;">

    */
@endphp


<div @if(empty($preview)) id="board-popup-{{ $board->sid }}" @endif class="popup-wrap type3" style="display: block;">
    <div class="popup-contents" style="width: auto; min-width:{{ $popup->width }}px; max-width:{{ $popup->width }}px; min-height:{{ $popup->height }}px; max-height:{{ $popup->height }}px; margin-top:{{ $popup->position_y }}px; margin-left:{{ $popup->position_x }}px;">
        <div class="popup-tit-wrap text-center">
            <img src="/assets/image/common/h1_logo.png" alt="">
        </div>

        <div class="popup-conbox">
            <div class="popup-contit-wrap">
                <h2 class="popup-contit">{{ $board->subject }}</h2>
            </div>

            <div class="popup-con">
                {!! $popup->popup_contents !!}
            </div>

            @if(($board->files_count ?? 0) > 0)
                <div class="popup-attach-con">
                    @foreach($board->files as $key => $file)
                        <a href="{{ empty($preview) ? $file->downloadUrl() : "javascript:void(0);" }}">
                            {{ $file->filename }} (다운로드 : {{ number_format($file->download) }}회)
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="btn-wrap text-center">
                @if(!empty($popup->popup_link))
                    <a href="{{ $popup->popup_link }}" class="btn btn-pop-more">자세히보기</a>
                @endif

                @if(!empty($board->link_url))
                    <a href="{{ $board->link_url }}" class="btn btn-pop-link">바로가기</a>
                @endif
            </div>
        </div>

        <div class="popup-footer btn-pop-today-close" style="cursor: pointer;">
            [오늘하루 그만보기]
        </div>

        <button type="button" class="btn btn-pop-close">
            <span class="hide">팝업 닫기</span>
        </button>
    </div>
</div>