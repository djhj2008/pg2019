function lover(x){
	if(x==2){topu1l2.className="l2 l2h";umenu2.style.display="";}
	else if(x==3){topu1l3.className="l3 l3h";umenu3.style.display="";}
}
function lout(x){
	if(x==2){topu1l2.className="l2";umenu2.style.display="none";}
	else if(x==3){topu1l3.className="l3";umenu3.style.display="none";}
}


function pxover(){
document.getElementById("px").style.display="";
}

function pxout(){
document.getElementById("px").style.display="none";
}






var iflogin;

var xmlHttpses = false;
try {
  xmlHttpses = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
  try {
    xmlHttpses = new ActiveXObject("Microsoft.XMLHTTP");
  } catch (e2) {
    xmlHttpses = false;
  }
}
if (!xmlHttpses && typeof XMLHttpRequest != 'undefined') {
  xmlHttpses = new XMLHttpRequest();
}
function userCheckses(){
    url ="/tem/sesCheck.php";
    xmlHttpses.open("get", url, true);
    xmlHttpses.onreadystatechange = updatePageses;
    xmlHttpses.send(null);
	}
function updatePageses() {
  if (xmlHttpses.readyState == 4) {
   response = xmlHttpses.responseText;
   response=response.replace(/[\r\n]/g,'');
   if(response=="0"){notlogin.style.display="";yeslogin.style.display="none";iflogin=0;return false;}
   else{yeslogin.style.display="";notlogin.style.display="none";document.getElementById("yesuid").innerHTML=response;iflogin=1;return false;}
  }
}

function yhmenuover(x){
document.getElementById("yhmenu"+x).className="menu2";	
document.getElementById("rmenu"+x).style.display="";	
}

function yhmenuout(x){
document.getElementById("yhmenu"+x).className="menu1";	
document.getElementById("rmenu"+x).style.display="none";	
}

function leftmenuover(){
document.getElementById("leftmenu").style.display="";	
}

function leftmenuout(){
if(!document.getElementById("leftnone")){
document.getElementById("leftmenu").style.display="none";	
}
}

function qqclose(){
document.getElementById("floatTips").style.display="none";	
}

        var tips; var theTop = 200/*这是默认高度,越大越往下*/; var old = theTop;
        function initFloatTips() {
            tips = document.getElementById('floatTips');
            moveTips();
        };
        function moveTips() {
            var tt = 50;
            if (window.innerHeight) {
                pos = window.pageYOffset
            }
            else if (document.documentElement && document.documentElement.scrollTop) {
                pos = document.documentElement.scrollTop
            }
            else if (document.body) {
                pos = document.body.scrollTop;
            }
            pos = pos - tips.offsetTop + theTop;
            pos = tips.offsetTop + pos / 10;
            if (pos < theTop) pos = theTop;
            if (pos != old) {
                tips.style.top = pos + "px";
                tt = 10;
            }
            old = pos;
            setTimeout(moveTips, tt);
        }

window.onerror =function(){return false;}