/***************************/
//@Author: Adrian "yEnS" Mato Gondelle
//@license: Feel free to use it, but keep this credits please!					
/***************************/

//SETTING UP OUR POPUP
//0 means disabled; 1 means enabled;
var popupStatus = 0;
var jq = jQuery.noConflict();

//loading popup with jQuery magic!
function loadPopup(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		jq("#backgroundPopup").css({
			"opacity": "0.7"
		});
		jq("#backgroundPopup").fadeIn("slow");
		jq("#popupContact").fadeIn("slow");
		popupStatus = 1;
	}
}

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		jq("#backgroundPopup").fadeOut("slow");
		jq("#popupContact").fadeOut("slow");
		popupStatus = 0;
	}
}

function kaiserdisable(){
	//kaiser
	jq(".kaiser_jieshao").css("display","none");
}

function kaiserjieshaonext(n){
	//kaiser
	if(n < 7){
		var next = ".kaiser_jieshao_" + n;
		var next_m = ".kaiser_jieshao_" + (n+1);
		jq(next).css("display","none");
		jq(next_m).css("display","inline");
	}else{
		disablePopup();
		kaiserdisable();
	}
}

function kaiserjieshaoclose(){
	//kaiser
	disablePopup();
	kaiserdisable();
}

//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = jq("#popupContact").height();
	var popupWidth = jq("#popupContact").width();
	//centering
	jq("#popupContact").css({
		"position": "absolute",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6
	
	jq("#backgroundPopup").css({
		"height": windowHeight
	});
	
}


//CONTROLLING EVENTS IN jQuery
jq(document).ready(function(){
	
	//LOADING POPUP
	//Click the button event!

		//centering with css
		centerPopup();
		//load popup
		loadPopup();
				
	//CLOSING POPUP
	//Click the x event!
//	jq("#popupContactClose").click(function(){
//		disablePopup();
//		kaiserdisable();
//	});
	//Click out event!
	jq("#backgroundPopup").click(function(){
		disablePopup();
		kaiserdisable();
	});
	//Press Escape event!
	jq(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});

});