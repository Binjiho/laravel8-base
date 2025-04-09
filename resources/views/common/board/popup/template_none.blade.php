{{--@php--}}
{{--    dd($popup);--}}
{{--@endphp--}}
{{--    <div class="win-popup-wrap popup-wrap type0" style="top: {{ $popup->position_y }}px; left: {{ $popup->position_x }}px; width: {{ $popup->width }}px; height: {{ $popup->height }}px; display: block;" id="popup_{{ $popup->sid }}">--}}

@php
    $main_pop = strpos(request()->url(),'/main_popup');
    $workshop_pop = strpos(request()->url(),'/workshop');
@endphp
@if($main_pop !== false)
    <script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="/assets/js/jquery-ui.min.js"></script>
    <script src="{{ asset('plugins/plupload/2.3.6/plupload.full.min.js') }}"></script>
    <script src="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/jquery.plupload.queue.min.js') }}"></script>
@endif
<!-- popup css -->
@if($workshop_pop !== false)

@else
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sub.css') }}">
@endif

<div class="win-popup-wrap popup-wrap type0" style="display: block;" id="popup_{{ $board->sid }}">
    <div class="popup-contents" style="background-color:white; width: auto; min-width:{{ $popup->width }}px; max-width:{{ $popup->width }}px; min-height:{{ $popup->height }}px; max-height:{{ $popup->height }}px; margin-top:{{ $popup->position_y }}px; margin-left:{{ $popup->position_x }}px;">
        <div class="popup-conbox">

            <div class="popup-tit-wrap">
                <h3 class="popup-tit">
                    {!! $board->subject !!}
                </h3>
            </div>

            <!-- content -->
            <div class="popup-con">
                {!! $popup->popup_contents !!}
            </div>
            <!-- //content -->

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
    </div>

    <div class="popup-footer btn-pop-today-close" style="cursor: pointer;">
        [오늘하루 그만보기]
    </div>

    <button type="button" class="btn btn-pop-close">
        <span class="hide">팝업 닫기</span>
    </button>
</div>


<script>
    $(document).on('click', '.popup_close_btn', function () {
        self.close();
    });

    @if($main_pop !== false)
    $(document).on('click', '.btn-pop-today-close', function () {
        const layer = $(this).closest('.win-popup-wrap');

        setCookie24(layer.attr('id'), 'done', 1);

        self.close();
    });
    @endif

    function setCookie24(name, value, expiredays) {
        var todayDate = new Date();

        todayDate.setDate(todayDate.getDate() + expiredays);

        document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";";
    }
</script>