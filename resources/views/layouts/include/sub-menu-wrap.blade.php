@php
    $main_name = $menu['main'][$main_menu]['name'] ?? '';
    if(!empty($sub_menu)){
        $sub_name = $menu['sub'][$main_menu][$sub_menu]['name'] ?? '';
    }
@endphp

<article class="sub-visual">
    <div class="sub-visual-con inner-layer">
        <h2 class="sub-visual-text">{{ $main_name }}</h2>
        <ul class="breadcrumb">
            <li><img src="/assets/image/sub/img_breadcrumb.png" alt=""></li>
            <li>{{ $main_name }}</li>
            <li>&gt;</li>
            <li>{{ $sub_name }}</li>
        </ul>

    </div>
</article>

<article class="sub-menu-wrap">
    <div class="sub-menu inner-layer cf">
        <ul class="sub-menu-list js-sub-menu-list cf">
            <li class="sub-menu-depth01">
                <a href="javascript:;" class="btn-sub-menu js-btn-sub-menu">{{ $main_name }}</a>
                <ul>
                    @foreach($menu['main'] ?? [] as $key => $val)
                        @if($val['continue']) @continue @endif
                        <li class="{{ ($main_menu ?? '') == $key ? 'on':'' }}"><a href="{{ empty($val['url']) ? route($val['route'], $val['param']) : $val['url'] }}" >{{ $val['name'] }}</a></li>
                    @endforeach
                </ul>
            </li>

            @if($menu['sub'][$main_menu][$sub_menu])
                <li class="sub-menu-depth02">
                    <a href="javascript:;" class="btn-sub-menu js-btn-sub-menu">{{ $sub_name }}</a>
                    <ul>
                        @foreach($menu['sub'][$main_menu] ?? [] as $sKey => $sVal)
                            @if($sVal['continue']) @continue @endif
                            <li class="{{ ($sub_menu ?? '') == $sKey ? 'on':'' }}"><a href="{{ empty($sVal['url']) ? route($sVal['route'], $sVal['param']) : $sVal['url'] }}" {{ $sVal['blank'] === true ? "target=_blank" : '' }} >{{ $sVal['name'] }}</a></li>
                        @endforeach
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</article>