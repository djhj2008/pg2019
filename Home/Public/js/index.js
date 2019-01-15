var uidyz;
var lxryz;
uidyz=0;
lxryz=0;
function tblur(x){
 if(x==1){
	 if((document.f1.t1.value).replace(/\s/,"")=="" || !isEmail(document.f1.t1.value)){document.getElementById("ts1").style.display="none";document.getElementById("err1").style.display="";document.getElementById("ul1").className="u1 u3";document.getElementById("err1").innerHTML="请输入有效的邮箱";}
	 else{document.getElementById("ts1").style.display="";document.getElementById("err1").style.display="none";document.getElementById("ul1").className="u1 u2";}
 
 }else if(x==2){ //密码
     tblur(3);
     if((document.f1.t2.value).replace(/\s/,"")==""){document.getElementById("ts2").style.display="none";document.getElementById("err2").style.display="";document.getElementById("ul2").className="u1 u3";}
	 else{document.getElementById("ts2").style.display="";document.getElementById("err2").style.display="none";document.getElementById("ul2").className="u1";}
 
 }else if(x==3){ //重复密码
     if((document.f1.t3.value).replace(/\s/,"")==""){document.getElementById("ts3").style.display="none";document.getElementById("err3").style.display="";document.getElementById("ul3").className="u1 u3";}
	 else if(document.f1.t3.value!=document.f1.t2.value){document.getElementById("ts3").style.display="none";document.getElementById("err3").style.display="";document.getElementById("ul3").className="u1 u3";document.getElementById("err3").innerHTML="两次密码输入不一致！";}
	 else{document.getElementById("ts3").style.display="";document.getElementById("err3").style.display="none";document.getElementById("ul3").className="u1";}
 
 }else if(x==4){ //QQ号码
     if((document.f1.t4.value).replace(/\s/,"")=="" || isNaN(document.f1.t4.value)){document.getElementById("ts4").style.display="none";document.getElementById("err4").style.display="";document.getElementById("ul4").className="u1 u3";}
	 else{document.getElementById("ts4").style.display="";document.getElementById("err4").style.display="none";document.getElementById("ul4").className="u1";}
 
 }else if(x==5){ //联系人
     if((document.f1.t5.value).replace(/\s/,"")==""){document.getElementById("ts5").style.display="none";document.getElementById("err5").style.display="";document.getElementById("ul5").className="u1 u3";}
	 else{document.getElementById("ts5").style.display="";document.getElementById("err5").style.display="none";document.getElementById("ul5").className="u1";}
 
 }else if(x==6){ //验证码
     if((document.f1.t6.value).replace(/\s/,"")==""){document.getElementById("ts6").style.display="none";document.getElementById("err6").style.display="";document.getElementById("ul6").className="u1 u3";}
	 else{document.getElementById("ts6").style.display="";document.getElementById("err6").style.display="none";document.getElementById("ul6").className="u1";}
 
 }else if(x==9){ //手机号码
     if((document.f1.t9.value).replace(/\s/,"")=="" || isNaN(document.f1.t9.value)){document.getElementById("ts9").style.display="none";document.getElementById("err9").style.display="";document.getElementById("ul9").className="u1 u3";}
	 else{document.getElementById("ts9").style.display="";document.getElementById("err9").style.display="none";document.getElementById("ul9").className="u1";}
	 }
}
function mover(x){
 if(x==1){if(document.getElementById("ul1").className!="u1 u3"){document.getElementById("ul1").className="u1 u2";}}	
 else if(x==2){if(document.getElementById("ul2").className!="u1 u3"){document.getElementById("ul2").className="u1 u2";}}	
 else if(x==3){if(document.getElementById("ul3").className!="u1 u3"){document.getElementById("ul3").className="u1 u2";}}	
 else if(x==4){if(document.getElementById("ul4").className!="u1 u3"){document.getElementById("ul4").className="u1 u2";}}	
 else if(x==5){if(document.getElementById("ul5").className!="u1 u3"){document.getElementById("ul5").className="u1 u2";}}	
 else if(x==6){if(document.getElementById("ul6").className!="u1 u3"){document.getElementById("ul6").className="u1 u2";}}	
 else if(x==9){if(document.getElementById("ul9").className!="u1 u3"){document.getElementById("ul9").className="u1 u2";}}	
}
function mout(x){
 if(x==1){if(document.getElementById("ul1").className!="u1 u3"){document.getElementById("ul1").className="u1";}}	
 else if(x==2){if(document.getElementById("ul2").className!="u1 u3"){document.getElementById("ul2").className="u1";}}	
 else if(x==3){if(document.getElementById("ul3").className!="u1 u3"){document.getElementById("ul3").className="u1";}}	
 else if(x==4){if(document.getElementById("ul4").className!="u1 u3"){document.getElementById("ul4").className="u1";}}	
 else if(x==5){if(document.getElementById("ul5").className!="u1 u3"){document.getElementById("ul5").className="u1";}}	
 else if(x==6){if(document.getElementById("ul6").className!="u1 u3"){document.getElementById("ul6").className="u1";}}	
 else if(x==9){if(document.getElementById("ul9").className!="u1 u3"){document.getElementById("ul9").className="u1";}}	
}
function regtj(){
	ifok=1;
	if((document.f1.t1.value).replace(/\s/,"")=="" || !isEmail(document.f1.t1.value)){ifok=0;tblur(1);}
	if((document.f1.t2.value).replace(/\s/,"")==""){ifok=0;tblur(2);}
	if((document.f1.t3.value).replace(/\s/,"")==""){ifok=0;tblur(3);}
	if(document.f1.t3.value!=document.f1.t2.value){ifok=0;tblur(3);}
	if((document.f1.t4.value).replace(/\s/,"")=="" || isNaN(document.f1.t4.value)){ifok=0;tblur(4);}
	if((document.f1.t5.value).replace(/\s/,"")==""){ifok=0;tblur(5);}
	if((document.f1.t6.value).replace(/\s/,"")==""){ifok=0;tblur(6);}
	if((document.f1.t9.value).replace(/\s/,"")=="" || isNaN(document.f1.t9.value)){ifok=0;tblur(9);}
	if(ifok==0 || uidyz==0 || lxryz==0){return false;}
    document.getElementById("tjbtn").style.display="none";
    document.getElementById("tjing").style.display="";
	f1.action="index.php";
}

function isEmail(str){//判断邮箱
var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
return reg.test(str);
}

var xmlHttp = false;
try {
  xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
  try {
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  } catch (e2) {
    xmlHttp = false;
  }
}
if (!xmlHttp && typeof XMLHttpRequest != 'undefined') {
  xmlHttp = new XMLHttpRequest();
}


function updatePage() {
  if (xmlHttp.readyState == 4) {
    var response = xmlHttp.responseText;
	response=response.replace(/[\r\n]/g,'');
	if(response=="True"){document.getElementById("err1").innerHTML="很遗憾，该邮箱已被本站其他用户使用，请更换";uidyz=0;document.getElementById("ts1").style.display="none";document.getElementById("err1").style.display="";document.getElementById("ul1").className="u1 u3";}
   else if(response=="False"){uidyz=1;document.getElementById("ts1").style.display="";document.getElementById("err1").style.display="none";document.getElementById("ul1").className="u1 u2";document.getElementById("ts1").innerHTML="邮箱可以使用";}
  }
}

function userCheck(){
	tblur(1);
    var u_name =document.f1.t1.value;
	if(u_name.replace(/\s/,"")=="" || !isEmail(document.f1.t1.value)){return false;}
	document.getElementById("ts1").innerHTML="用户名正在检测……";
    var url = "userCheck.php?uid="+u_name;
    xmlHttp.open("post", url, true);
    xmlHttp.onreadystatechange = updatePage;
    xmlHttp.send(null);
	}



var xmlHttplxr = false;
try {
  xmlHttplxr = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
  try {
    xmlHttplxr = new ActiveXObject("Microsoft.XMLHTTP");
  } catch (e2) {
    xmlHttplxr = false;
  }
}
if (!xmlHttplxr && typeof XMLHttpRequest != 'undefined') {
  xmlHttplxr = new XMLHttpRequest();
}


function updatePagelxr() {
  if (xmlHttplxr.readyState == 4) {
    var responselxr = xmlHttplxr.responseText;
	responselxr=responselxr.replace(/[\r\n]/g,'');
	if(responselxr=="True"){document.getElementById("err5").innerHTML="很遗憾，该昵称已存在";lxryz=0;document.getElementById("ts5").style.display="none";document.getElementById("err5").style.display="";document.getElementById("ul5").className="u1 u3";}
   else if(responselxr=="False"){lxryz=1;document.getElementById("ts5").style.display="";document.getElementById("err5").style.display="none";document.getElementById("ul5").className="u1 u2";document.getElementById("ts5").innerHTML="昵称可以使用";}
  }
}

function lxrCheck(){
	tblur(5);
    var lxr =document.f1.t5.value;
	if(lxr.replace(/\s/,"")==""){return false;}
	document.getElementById("ts5").innerHTML="昵称正在检测……";
    var url = "lxrCheck.php?lxr="+lxr;
    xmlHttplxr.open("post", url, true);
    xmlHttplxr.onreadystatechange = updatePagelxr;
    xmlHttplxr.send(null);
	}





function logintj(){
	if((document.f1.t1.value).replace(/\s/,"")=="" || !isEmail(document.f1.t1.value)){document.getElementById("err").innerHTML="请输入有效的邮箱邮箱";document.getElementById("err").style.display="";return false;}
	if((document.f1.t2.value).replace(/\s/,"")==""){document.getElementById("err").innerHTML="请输入登录密码";document.getElementById("err").style.display="";return false;}
    document.getElementById("tjbtn").style.display="none";
    document.getElementById("tjing").style.display="";
	f1.action="login.php";
}


function getpwdtj(){
	if((document.f1.t1.value).replace(/\s/,"")=="" || !isEmail(document.f1.t1.value)){alert("请输入有效的邮箱!");document.f1.t1.focus();return false;}
	if((document.f1.t2.value).replace(/\s/,"")==""){alert("请输入验证码!");document.f1.t2.focus();return false;}
	f1.action="getpasswd.php"
}


function bdover(x){
 if(x==1){
 document.getElementById("bd1").className="l1";	
 document.getElementById("bd2").className="l2";	
 document.getElementById("bdmain1").style.display="";
 document.getElementById("bdmain2").style.display="none";
 }else if(x==2){
 document.getElementById("bd1").className="l2";	
 document.getElementById("bd2").className="l1";	
 document.getElementById("bdmain1").style.display="none";
 document.getElementById("bdmain2").style.display="";
 }
}

function bdtj(){
 if((document.f2.bt1.value).replace(/\s/,"")==""){alert("请输入有效的帐号!");document.f2.bt1.focus();return false;}
 if((document.f2.bt2.value).replace(/\s/,"")==""){alert("请输入密码!");document.f2.bt2.focus();document.f2.bt2.focus();return false;}
 if((document.f2.bt6.value).replace(/\s/,"")==""){alert("请输入验证码!");document.f2.bt6.focus();document.f2.bt6.focus();return false;}
 f2.action="index.php";	
}














