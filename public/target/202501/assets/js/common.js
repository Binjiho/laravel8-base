$(function (e) {
	$width = $(window).innerWidth(),
    wWidth = windowWidth();

	$(document).ready(function (e) {
		btnTop();
        datepicker();
        imgMap();
        fileUpload();
		popup();
        subMenu();
        tabMenu();
        slideTabMenu();
        popRolling();

        mainVisual();
        sponsorRolling();
        

		if(wWidth < 1025){		
		}else{
		}
		
		resEvt();
	});

	// resize#
	function resEvt() {	
		if (wWidth < 1025) {
			mGnb();
            if($('.js-dim').hasClass('mobile')){
				$('.js-dim').show();
				$('html, body').addClass('ovh');
			}

			subConHeight();
			mTabMenu();  
            linkTabMenu(30);
		} else {	
            gnb();
            if($('.js-dim').hasClass('mobile')){
				$('.js-dim').hide();
                $('.js-dim').removeClass('mobile');
				$('html, body').removeClass('ovh');
			}
            $('#gnb').removeAttr('style');
            $('.js-gnb > li > ul').removeAttr('style');
            $('.js-gnb > li').removeClass('on');

            linkTabMenu(50);
		}

		if(wWidth < 769){
            mTabDropDown() 
			touchHelp();
			$('.main-visual-menu').appendTo($('.main-visual'));
		}else{
			tabDropDown();
			$('.js-tab-drop-menu').removeAttr('style');
            
			$('.main-visual-con01').append($('.main-visual-menu'));
			$('.js-sub-menu-list ul').removeAttr('style');
			$('.js-btn-sub-menu').removeClass('on');
			$('.js-tab-menu, .js-tabcon-menu').removeAttr('style');		
			$('.js-btn-tab-menu, .js-btn-tabcon-menu').removeClass('on');
		}
	}

	$(window).resize(function (e) {
		$width = $(window).innerWidth(),
		wWidth = windowWidth();
		resEvt();
	});

	$(window).scroll(function(e){
		if($(this).scrollTop() > 200){
			$('.js-btn-top').addClass('on');
		}else{
			$('.js-btn-top').removeClass('on');
		}
	});
});

function Mobile() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

function windowWidth() {
	if ($(document).innerHeight() > $(window).innerHeight()) {
		if (Mobile()) {
			return $(window).innerWidth();
		} else {
			return $(window).innerWidth() + 17;
		}
	} else {
		return $(window).innerWidth();
	}
}

function subConHeight(){
    $(document).ready(function(e){
        var subConHeight = $(window).outerHeight() - $('.js-header').outerHeight() - $('#footer').outerHeight();
        setTimeout(function(e){
            $('.sub-contents').css('min-height',subConHeight);
        },100);
    });	
}

function gnb() {
	var max_h = 0;
	var gnb_h = 0;
    $('.js-gnb > li > ul').each(function (e) {
        $(this).height('');
        var h = parseInt($(this).css('height'));
        if (max_h < h) {
            max_h = h;
        }
        gnb_h = max_h + 210;
    });
	
    $('.js-gnb > li > a').off('click');
	$('.js-gnb > li').on('mouseenter',function(e){
		$('.js-header').addClass('active').stop().animate({'height':gnb_h},400);
    });
    $('.js-gnb').on('mouseleave', function(e){
		$('.js-header').removeClass('active').stop().animate({'height':'210px'},300);
    });
}

function mGnb(){
	$('.js-header').removeAttr('style');
    $('.js-gnb > li').off('mouseenter');
    $('.js-gnb').off('mouseleave');
    $('.js-gnb > li > a').off().on('click',function(e){
        if($(this).next('ul').length){
            $(this).parent('li').toggleClass('on');
            $('.js-gnb > li > a').not(this).parent('li').removeClass('on');
            $(this).next('ul').stop().slideToggle();
            $('.js-gnb > li > a').not(this).next('ul').stop().slideUp();
            return false;
        }
    });
    
    $('.js-btn-menu-open').on('click',function(e){
        $('html, body').addClass('ovh');
        $('.js-dim').addClass('mobile').stop().fadeIn(100);
        $('#gnb').stop().animate({'right':0},400); 
    });
    $('.js-btn-menu-close, .js-dim').on('click',function(e){
        $('html, body').removeClass('ovh');
        $('.js-dim').removeClass('mobile').stop().fadeOut(100);
        $('#gnb').stop().animate({'right':'-100%'},400);
    });
}


function subMenu(){
	$('.js-btn-sub-menu').off().on('click', function (e) {
		$(this).next('ul').stop().slideToggle();
		$(this).toggleClass('on');
		$('.js-btn-sub-menu').not(this).removeClass('on').next('ul').stop().slideUp();
		return false;
	});
	$('body').off().on('click', function (e) {
		if ($('.js-sub-menu-list').has(e.target).length == 0) {
			$('.js-btn-sub-menu').removeClass('on');
			$('.js-btn-sub-menu:visible +  ul').stop().slideUp();
		}
	});
}

function tabMenu(){
    $('.js-tab-menu').each(function(e){
        var cnt = $(this).children('li').length;
        $(this).addClass('n'+cnt+'');
    });

	$('.js-tab-menu > li').off('click');	
	$('.js-tab-menu > li').on('click',function(e){	
		var cnt = $(this).index();
		$(this).addClass('on');
		$(this).siblings().removeClass('on');
		$('.js-tab-con').hide().eq(cnt).stop().fadeIn();
        $('.slick-slider').slick('setPosition');
		return false;
	});
}

// sub-tab-drop-menu 2024-08-26
function mTabDropDown() {
	var activeTab = $('.js-tab-drop-menu li.on > a').html();
	$('.js-btn-tab-drop').html(activeTab);
	$('.js-btn-tab-drop').off().on('click',function(e){
		$(this).toggleClass('on');
		$(this).next('ul').stop().slideToggle();
		return false;
	});
	$('.js-tab-drop-menu li > a').off().on('click',function(e){
		var currentTab = $(this).html();
		$('.js-btn-tab-drop').html(currentTab);

		$(this).addClass('on');
		$(this).siblings().removeClass('on');
		$(this).parent().parent('ul').stop().slideUp();
		$('.js-btn-tab-drop').removeClass('on');
	});

	$('body').off().on('click', function (e) {
		if ($('.js-tab-drop-menu').has(e.target).length == 0) {
			$('.js-btn-tab-drop').removeClass('on');
			$('.js-btn-tab-drop:visible +  ul').stop().slideUp();
		}
	});

	tabDropDown();
}

function tabDropDown() {
	$('.js-tab-drop-menu li').on('click',function(e){	
		var cnt = $(this).index();

		$(this).addClass('on');
		$(this).siblings().removeClass('on');
		$('.js-tab-drop-con').hide().eq(cnt).stop().fadeIn();

		if($('.js-tab-drop-menu li.on > a').hasClass('all') == true) {
			$('.js-tab-drop-con').removeAttr('style');
		}
		
		return false;
	});
}

function mTabMenu(){
	var activeTab = $('.js-btn-tab-menu + .js-tab-menu > li.on > a').html();
	$('.js-btn-tab-menu').html(activeTab);
	$('.js-btn-tab-menu').off().on('click',function(e){
		$(this).toggleClass('on');
		$(this).next('ul').stop().slideToggle();
		return false;
	});
	$('.js-btn-tab-menu + .js-tab-menu > li').off().on('click',function(e){		
		var currentTab = $(this).html();
		$('.js-btn-tab-menu').html(currentTab);

		$(this).addClass('on');
		$(this).siblings().removeClass('on');

		$(this).parent('ul').stop().slideUp();
		$('.js-btn-tab-menu').removeClass('on');
	});
}

function slideTabMenu(){
    $('.js-tab-slide > li').on('click',function(e){        
        $(this).addClass('on');
        $('.js-tab-slide > li').not(this).removeClass('on');
        if(!$(this).hasClass('all')){
            var cnt = $(this).index() - 1;
            $('.js-tab-slidecon').stop().slideUp().eq(cnt).stop().slideDown();
        }else{
            $('.js-tab-slidecon').stop().slideDown();
        }
    });
}

function linkTabMenu(h){
    $('.js-tab-link > li > a').on('click',function(e){
        $(this).parent('li').addClass('on');
        $('.js-tab-link > li > a').not(this).parent('li').removeClass('on');
        if($(this).attr('href')){
            $('html, body').stop().animate({
                scrollTop: $(this.hash).offset().top - h
            }, 400);
        }
    });
}

function btnTop(){
	$('.js-btn-top').on('click',function(e){
        $('html, body').stop().animate({'scrollTop':0},400);
		return false;
	});
}

function touchHelp(){
	$('.scroll-x').each(function(e){
		if($(this).height() < 180){
			$(this).addClass('small');
		}
		$(this).scroll(function(e){
			$(this).removeClass('touch-help');
		});
	});
}

function fileUpload(option=null){
    $('.file-upload').each(function(e){
        $(this).parent().find('.upload-name').attr('readonly','readonly');
        $(this).on('change',function(){
            var fileName = $(this).val();
            $(this).parent().find('.upload-name').val(fileName);
        });
    });
}

function datepicker(){
	if($('.datepicker').length){
		$('.datepicker').datepicker({
			dateFormat : "yy-mm-dd",
			dayNamesMin : ["월", "화", "수", "목", "금", "토", "일"],
			monthNamesShort : ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"],
			showMonthAfterYear: true, 
			changeMonth : true,
			changeYear : true
		});
	}
}

function popup(){
    $('.js-pop-open').on('click',function(e){
        var popCnt = $(this).attr('href');
        $('html, body').addClass('ovh');
        $(popCnt).css('display','flex');
        return false;
    });
    $('.js-pop-close').on('click',function(e){
        $('html, body').removeClass('ovh');
        $(this).parents('.popup-wrap').css('display','none');
        return false;
    });
    // $('.popup-wrap').off().on('click', function (e){
    // 	if ($('.popup-contents').has(e.target).length == 0){
    // 		$('html, body').removeClass('ovh');
    // 		$('.popup-wrap').css('display','none');
    // 	}
    // });
}

function imgMap(){
    $('img[usemap]').each(function(e){
        $('img[usemap]').rwdImageMaps();
    });
}



function mainVisual(){
    $(".js-main-visual").each(function(index, element) {
        $(this).not('.slick-initialized').slick({
            dots: true,
            arrows: false,
            prevArrow: $('.main-visual .prev'),
            nextArrow: $('.main-visual .next'),
			autoplay: true,
			autoplaySpeed: 30000,
			speed: 1000,
			infinite: true,
            fade: true
		});
    });
}

function sponsorRolling(){
    $('.js-sponsor-rolling').each(function(e){
		$(this).not('.slick-initialized').slick({
			dots: false,
			arrows: true,
			autoplay: true,
			autoplaySpeed: 3000,
			speed: 1000,
			infinite: true,
            slidesToShow: 6,
			responsive: [
				{
					breakpoint: 1401,
					settings: {
                        slidesToShow: 5,
					}
				},
				{
					breakpoint: 1025,
					settings: {
                        slidesToShow: 4,
					}
				},
				{
					breakpoint: 769,
					settings: {
                        slidesToShow: 3,
                        arrows: false,
					}
				}
			]
		});
    });
}


function popRolling(){
    $('.js-popup-rolling').not('.slick-initialized').slick({
        dots: false,
        arrows: false,
        autoplay: false,
        infinite: false,
        adaptiveHeight: true,
        slidesToShow: 2,
        slidesToScroll: 1,
        responsive: [{
            breakpoint: 769,
            settings: {
                arrows: true,
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }]
    });
}
