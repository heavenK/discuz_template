/*
 *	add by zh use vertify msg
 */
function errormessage(id, msg) {
	if($(id)) {
		showInputTip();
		msg = !msg ? '' : msg;
		if($('tip_' + id)) {
			if(msg == 'succeed') {
				msg = '';
                $('tip_' + id).className = $('tip_' + id).className.replace(/ p_right/, '');
				$('tip_' + id).className += ' p_right';
				$('tip_' + id).style.display="block";
			} else if(msg !== '') {
				$('tip_' + id).className = $('tip_' + id).className.replace(/ p_right/, '');
				$('tip_' + id).style.display="none";
			}
		}
		if($('chk_' + id)) {
			$('chk_' + id).innerHTML = msg;
		}
		$(id).className = $(id).className.replace(/ er/, '');
		$(id).className += !msg ? '' : ' er';
	}
}

function trim(str) {
	return str.replace(/^\s*(.*?)[\s\n]*$/g, '$1');
}

function subvertifycode(formid,id,url){
	var verifycode = trim($('verifycode').value);
	var flag=false;
	var ajaxframeid = 'ajaxframe';
	var ajaxframe = $(ajaxframeid);
	var formtarget = $(formid).target;
	if(verifycode == '') {
		$(id).innerHTML = "<img src='template/we54/common/images/register_no.png' />验证码不能为空";
		$('verifycode').focus();
	} else {
		$('flag').value = "1";
		var mobile=trim($('newmobile').value);
		var vertifycode=trim($('verifycode').value);
		var formhash=trim($('formhash').value);
		jQuery.post("member.php?mod=sms&action=bindmobile",{flag:1,newmobile:mobile,verifycode:verifycode,bindmobilesubmit:true,formhash:formhash},function(data){
			if (data!="")
			{
				if(data!="success"){
					$(id).innerHTML = data;
				}else{
					showDialog('绑定手机成功','right','','location.href="member.php?mod=regend&o='+url+'"',null,null,null,null,null,null,2);
				}
			}
		});
	}
	return flag;
} 