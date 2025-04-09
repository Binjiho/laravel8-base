var idCheck          = false; // 회원가입 중복확인 체크를 위한 변수
var passwordCheck    = false; // 비밀번호 조건 성립 변수
var repasswordCheck = false; // 비밀번호 확인 변수

$(document).ready(function(){

	//회원가입 아이디 중복확인
	$("#idCheck").click(function(){
		
		var id = $("#id").val();
		var rule = /^[0-9a-z]{6,12}$/;
		
		if( id == "" ){
			swalAlert("아이디를 입력해주세요.", "", "warning", "id");
			idCheck = false;
			return false;
		}else{
			if( rule.test(id) == false ){
				swalAlert("아이디는 6~12자리의 영문소문자, 숫자만 사용이 가능합니다.", "", "warning", "id");
				idCheck = false;
				return false;
			}
		}
		
		$.post( "/member/join/checkId", { id : id }, function(data){
			const res = data.result;

			switch (res) {
				case 1:
					idCheck = true;
					swalAlert("사용가능한 아이디 입니다.", "", "warning", "id");
					break;

				case 2:
					idCheck = false;
					swalAlert("사용중인 ID입니다. 다른 ID 입력해주세요.", "", "warning", "id");
					break;
			}
		});

		return false;

	});

	$("#joinForm #password").on("change",function(){
		
		var password = $(this).val();
		var id = $("#id").val();

		var num = password.search(/[0-9]/g);
		var eng = password.search(/[a-z]/g);
		
		if( password != "" ){
			if( password.length < 8 || password.length > 16 ){
				swalAlert("비밀번호는 8~16자 영문, 숫자를 조합하여 사용 가능합니다.", "", "warning", "password");
				passwordCheck = false;
				return false;
			}
			if( num < 0 || eng < 0 ){
				swalAlert("비밀번호는 8~16자 영문, 숫자를 조합하여 사용 가능합니다.", "", "warning", "password");
				passwordCheck = false;
				return false;
			}
			if( id == password  ){
				swalAlert("비밀번호가 아이디와 같습니다.", "", "warning", "password");
				passwordCheck = false;
				return false;
			}
			if( password.search(/\s/g) > -1 ){
				swalAlert("비밀번호에 공백이 있습니다.", "", "warning", "password");
				$(this).val('');
				passwordCheck = false;
				return false;
			}
			if( checkPasswordSame(password) == true ){
				swalAlert("비밀번호에 연속된 동일한 숫자나 문자가 있습니다.", "", "warning", "password");
				passwordCheck = false;
				return false;
			}
			passwordCheck = true;
		}
		
	});

	// 회원가입 비밀번호 확인
	$("#repassword").change(function(){ //비밀번호 확인을 변경했을때

		var repassword = $(this).val();

		if( repassword.search(/\s/g) > -1 ){
			swalAlert("비밀번호에 공백이 있습니다.", "", "warning", "repassword");
			$(this).val('');
			repasswordCheck = false;
		}

		if( $("#password").val() != $(this).val() ){
			swalAlert("비밀번호와 일치하지 않습니다.", "", "warning", "repassword");
			repasswordCheck = false;
		}

		repasswordCheck = true;
		
	});

	$("#job").change(function(){

		var value = $(this).val();

		switch(value){
			case "" : $(".positionBox").hide(); $(".positionBox").find("select, input").val("").hide(); break;
			case "A" : $(".positionBox, #position").show(); $("#position_etc").val("").hide(); break;
			default : $(".positionBox, #position_etc").show(); $("#position").val("").hide(); break;
		}

	});

});

// 비밀번호 연속된 동일 문자 숫자 체크
function checkPasswordSame(value) {

	var temp = "";
	var intCnt = 0;

	for( var i = 0; i < value.length; i++ ){
	    temp = value.charAt(i);
	    if( temp == value.charAt(i+1) && temp == value.charAt(i+2) && temp == value.charAt(i+3) ) {
    		intCnt = intCnt + 1;
	    }
	}

	if( intCnt > 0 ){
		return true;
	}else{
		return false;
	}

}

// 로그인 유효성 검사
function loginCheck(f){

	if( $(f.id).val() == "" ){
		swalAlert("아이디를 입력해주세요", "", "warning", "id");
		$(f.id).focus();
		return false;
	}

	if( $(f.password).val() == "" ){
		swalAlert("비밀번호를 입력해주세요", "", "warning", "password");
		$(f.password).focus();
		return false;
	}

	return true;

}

function joinStep01()
{
	if( !$("#agree").is(":checked") ){
		alert("회원가입 약관에 동의해주세요.");
		focus("#agree");
		return false;
	}
}

function joinStepN02(f)
{
	// if( !$("input:radio[name='grade']").is(":checked") ){
	// 	swalAlert("회원 세부 등급을 선택해주세요.", "", "warning", "gradeA");
	// 	return false;
	// }

	if( $(f.sid).val() == null ){

		if( !$(f.id).val() ){
			swalAlert("아이디를 입력해주세요.", "", "warning", "id");
			return false;
		}
		if( idCheck == false ){
			swalAlert("아이디 중복확인을 해주세요.", "", "warning", "id");
			return false;
		}
		if( !$(f.password).val() ){
			swalAlert("비밀번호를 입력해주세요.", "", "warning", "password");
			return false;
		}else{
			if( passwordCheck == false ){
				swalAlert("비밀번호는 8~16자 영문, 숫자를 조합하여 사용 가능합니다.", "", "warning", "password");
				return false;
			}
		}

		if( !$(f.repassword).val() ){
			swalAlert("비밀번호 확인을 입력해주세요.", "", "warning", "repassword");
			return false;
		}else{
			if( repasswordCheck == false ){
				swalAlert("비밀번호가 일치 하지 않습니다.", "", "warning", "repassword");
				return false;
			}
		}

	}

	if( !$(f.name_kr).val() ){
		swalAlert("성명(한글)을 입력해주세요.", "", "warning", "name_kr");
		return false;
	}
	if( !$(f.name_en).val() ){
		swalAlert("성명(영문)을 입력해주세요.", "", "warning", "name_en");
		return false;
	}
	if( !$(f.birth).val() ){
		swalAlert("생년월일을 입력해주세요.", "", "warning", "birth");
		return false;
	}
	if( !$("input:radio[name='sex']").is(":checked") ){
		swalAlert("성별을 선택해주세요.", "", "warning", "sexM");
		return false;
	}
	if( !$(f.email).val() ){
		swalAlert("이메일을 입력해주세요.", "", "warning", "email");
		return false;
	}
	if( !$("input:radio[name='emailReception']").is(":checked") ){
		swalAlert("이메일 수신을 선택해주세요.", "", "warning", "emailReceptionY");
		return false;
	}
	if( !$("#phone1").val() || !$("#phone2").val() || !$("#phone3").val() ){
		swalAlert("휴대폰번호를 입력해주세요.", "", "warning", "phone1");
		return false;
	}
	if( !$("input:radio[name='smsReception']").is(":checked") ){
		swalAlert("SMS 수신을 선택해주세요.", "", "warning", "smsReceptionY");
		return false;
	}
	if( !$("input:radio[name='post']").is(":checked") ){
		swalAlert("우편물 수령지를 선택해주세요.", "", "warning", "postH");
		return false;
	}
	if( !$(f.company).val() ){
		swalAlert("직장명을 입력해주세요.", "", "warning", "company");
		return false;
	}
	if( !$(f.department).val() ){
		swalAlert("부서를 입력해주세요.", "", "warning", "department");
		return false;
	}
	if( !$(f.job).val() ){
		swalAlert("직종을 선택해주세요.", "", "warning", "job");
		return false;
	}else{
		if( $(f.job).val() == "A" ){
			if( !$(f.position).val() ){
				swalAlert("직위를 선택해주세요.", "", "warning", "position");
				return false;
			}	
		}else{
			if( !$(f.position_etc).val() ){
				swalAlert("직위를 입력해주세요.", "", "warning", "position_etc");
				return false;
			}
		}
	}
	if( !$(f.company_zipcode).val() || !$(f.company_address).val() || !$(f.company_address2).val() ){
		swalAlert("직장주소를 입력해주세요.", "", "warning", "company_zipcode");
		return false;
	}
	if( !$(f.home_zipcode).val() || !$(f.home_address).val() || !$(f.home_address2).val() ){
		swalAlert("자택주소를 입력해주세요.", "", "warning", "home_zipcode");
		return false;
	}
	// if( !$("input:radio[name='payMethod']").is(":checked") ){
	// 	swalAlert("결제방법을 선택해주세요.", "", "warning", "payMethodCARD");
	// 	return false;
	// }

	var captcha = $("#captcha").val();
    var captchaCheck = true;

    if( !captcha ){
        swalAlert("자동화 프로그램 입력 방지 번호를 입력해주세요.", "", "warning", "captcha");
        return false;
    }

    $.ajax({
        type: 'POST',
        url: '/common/captcha-check',
        data: { captcha : captcha },
        async: false,
        success: function(data) {
			if( $.trim(data) == "fail" ){
                captchaCheck = false;
            }
        }
    });
    
    if( !captchaCheck ){
        swalAlert("자동화 프로그램 입력방지 인증에 실패하였습니다.", "", "warning", "captcha");
        return false;
    }
}

function joinStepS02(f)
{
	if( !$("input:radio[name='grade']").is(":checked") ){
		swalAlert("회원 세부 등급을 선택해주세요.", "", "warning", "gradeA");
		return false;
	}

	if( $(f.sid).val() == null ){

		if( !$(f.id).val() ){
			swalAlert("아이디를 입력해주세요.", "", "warning", "id");
			return false;
		}
		if( idCheck == false ){
			swalAlert("아이디 중복확인을 해주세요.", "", "warning", "id");
			return false;
		}
		if( !$(f.password).val() ){
			swalAlert("비밀번호를 입력해주세요.", "", "warning", "password");
			return false;
		}else{
			if( passwordCheck == false ){
				swalAlert("비밀번호는 8~16자 영문, 숫자를 조합하여 사용 가능합니다.", "", "warning", "password");
				return false;
			}
		}

		if( !$(f.repassword).val() ){
			swalAlert("비밀번호 확인을 입력해주세요.", "", "warning", "repassword");
			return false;
		}else{
			if( repasswordCheck == false ){
				swalAlert("비밀번호가 일치 하지 않습니다.", "", "warning", "repassword");
				return false;
			}
		}

	}

	if( !$(f.company).val() ){
		swalAlert("기관명을 입력해주세요.", "", "warning", "company");
		return false;
	}
	if( !$(f.ceo).val() ){
		swalAlert("대표이사명을 입력해주세요.", "", "warning", "ceo");
		return false;
	}
	if( !$(f.company_zipcode).val() || !$(f.company_address).val() || !$(f.company_address2).val() ){
		swalAlert("기관 주소를 입력해주세요.", "", "warning", "company_zipcode");
		return false;
	}
	if( !$(f.business).val() ){
		swalAlert("업태, 종목을 입력해주세요.", "", "warning", "business");
		return false;
	}
	if( !$(f.manager).val() ){
		swalAlert("담당자 성명을 입력해주세요.", "", "warning", "manager");
		return false;
	}
	if( !$("#managerTel2").val() || !$("#managerTel3").val() ){
		swalAlert("담당자 전화번호를 입력해주세요.", "", "warning", "managerTel2");
		return false;
	}
	if( !$("input:radio[name='smsReception']").is(":checked") ){
		swalAlert("SMS 수신을 선택해주세요.", "", "warning", "smsReceptionY");
		return false;
	}
	if( !$(f.managerEmail).val() ){
		swalAlert("담당자 이메일을 입력해주세요.", "", "warning", "managerEmail");
		return false;
	}
	if( !$("input:radio[name='emailReception']").is(":checked") ){
		swalAlert("이메일 수신을 선택해주세요.", "", "warning", "emailReceptionY");
		return false;
	}
	// if( !$("input:radio[name='payMethod']").is(":checked") ){
	// 	swalAlert("결제방법을 선택해주세요.", "", "warning", "payMethodCARD");
	// 	return false;
	// }

	var captcha = $("#captcha").val();
    var captchaCheck = true;

    if( !captcha ){
        swalAlert("자동화 프로그램 입력 방지 번호를 입력해주세요.", "", "warning", "captcha");
        return false;
    }

    $.ajax({
        type: 'POST',
        url: '/common/captcha-check',
        data: { captcha : captcha },
        async: false,
        success: function(data) {
			if( $.trim(data) == "fail" ){
                captchaCheck = false;
            }
        }
    });
    
    if( !captchaCheck ){
        swalAlert("자동화 프로그램 입력방지 인증에 실패하였습니다.", "", "warning", "captcha");
        return false;
    }
}

function joinStepG02(f)
{
	if( $(f.sid).val() == null ){

		if( !$(f.id).val() ){
			swalAlert("아이디를 입력해주세요.", "", "warning", "id");
			return false;
		}
		if( idCheck == false ){
			swalAlert("아이디 중복확인을 해주세요.", "", "warning", "id");
			return false;
		}
		if( !$(f.password).val() ){
			swalAlert("비밀번호를 입력해주세요.", "", "warning", "password");
			return false;
		}else{
			if( passwordCheck == false ){
				swalAlert("비밀번호는 8~16자 영문, 숫자를 조합하여 사용 가능합니다.", "", "warning", "password");
				return false;
			}
		}

		if( !$(f.repassword).val() ){
			swalAlert("비밀번호 확인을 입력해주세요.", "", "warning", "repassword");
			return false;
		}else{
			if( repasswordCheck == false ){
				swalAlert("비밀번호가 일치 하지 않습니다.", "", "warning", "repassword");
				return false;
			}
		}

	}

	if( !$(f.company).val() ){
		swalAlert("기관명을 입력해주세요.", "", "warning", "company");
		return false;
	}
	if( !$(f.ceo).val() ){
		swalAlert("대표이사명을 입력해주세요.", "", "warning", "ceo");
		return false;
	}
	if( !$(f.company_zipcode).val() || !$(f.company_address).val() || !$(f.company_address2).val() ){
		swalAlert("기관 주소를 입력해주세요.", "", "warning", "company_zipcode");
		return false;
	}
	if( !$(f.business).val() ){
		swalAlert("업태, 종목을 입력해주세요.", "", "warning", "business");
		return false;
	}
	if( !$(f.manager).val() ){
		swalAlert("담당자 성명을 입력해주세요.", "", "warning", "manager");
		return false;
	}
	if( !$("#managerTel2").val() || !$("#managerTel3").val() ){
		swalAlert("담당자 전화번호를 입력해주세요.", "", "warning", "managerTel2");
		return false;
	}
	if( !$("input:radio[name='smsReception']").is(":checked") ){
		swalAlert("SMS 수신을 선택해주세요.", "", "warning", "smsReceptionY");
		return false;
	}
	if( !$(f.managerEmail).val() ){
		swalAlert("담당자 이메일을 입력해주세요.", "", "warning", "managerEmail");
		return false;
	}
	if( !$("input:radio[name='emailReception']").is(":checked") ){
		swalAlert("이메일 수신을 선택해주세요.", "", "warning", "emailReceptionY");
		return false;
	}
	// if( !$("input:radio[name='payMethod']").is(":checked") ){
	// 	swalAlert("결제방법을 선택해주세요.", "", "warning", "payMethodCARD");
	// 	return false;
	// }

	var captcha = $("#captcha").val();
    var captchaCheck = true;

    if( !captcha ){
        swalAlert("자동화 프로그램 입력 방지 번호를 입력해주세요.", "", "warning", "captcha");
        return false;
    }

    $.ajax({
        type: 'POST',
        url: '/common/captcha-check',
        data: { captcha : captcha },
        async: false,
        success: function(data) {
			if( $.trim(data) == "fail" ){
                captchaCheck = false;
            }
        }
    });
    
    if( !captchaCheck ){
        swalAlert("자동화 프로그램 입력방지 인증에 실패하였습니다.", "", "warning", "captcha");
        return false;
    }
}

function makeFee(gubun)
{
	if( $("input:hidden[name='sid']").val() != null ){ return false; }

	var gubun = gubun;
	var grade = $("input:radio[name='grade']:checked").val();
	var birth = $("#birth").val();
	var birthCheck = "N";
	let birth_pattern = /^\d{4}\d{2}\d{2}$/

	if( birth && birth_pattern.test(birth) ){
		var age = calculateAge(birth);
		if( age >= 55 ){
			birthCheck = "Y";
		}
    }

	$.post( "/member/join/makeFee", { gubun : gubun, grade : grade, birth : birthCheck }, function(data){

		const res = data;

		$(".feePayText").html(comma(res.price)+"원");
		$("#feePay").val(res.price);
		$(".feePayTable").html(res.build);
		
	});
}

function calculateAge(birthday)
{
	const year = Number(birthday.substr(0, 4)); // 입력한 값의 0~4자리까지 (연)
   
    let today = new Date(); // 오늘 날짜를 가져옴
    let yearNow = Number(today.getFullYear()); // Date 객체의 년도를 가져옵니다.
    let age = Number(yearNow - year + 1);  // 소수점 버림
  
	return age;
}