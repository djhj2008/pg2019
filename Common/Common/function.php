<?php
use JPush\Client as JPush;  

function more(){
    $fopentext=fopen('visitlog.txt',"r");
    $freadtext=fread($fopentext,filesize("visitlog.txt"));

    $pattern[]="/login/i";
    $pattern[]="/reg/i";
    $pattern[]="/index\/index/i";
    $pattern[]="/manager\/adduserinfo/i";
    // $pattern[]="/index\/index/i";
    // $pattern[]="/index\/index/i";
    // $pattern[]="/index\/index/i";
    // $pattern[]="/index\/index/i";
    // $pattern[]="/index\/index/i";

    for($i=0;$i<count($pattern);$i++){
        $more=preg_match_all($pattern[$i], $freadtext, $matches);
        echo $pattern[$i]."&nbsp"."&nbsp"."&nbsp"."&nbsp"."&nbsp"."&nbsp".$more;
        echo "<br/>";
    }
}




//推送消息
 /**     
     * 将数据先转换成json,然后转成array 
     */  
function json_array($result){  
   $result_json = json_encode($result);  
   return json_decode($result_json,true);  
}  
  


function send163msg($phone,$msg){
	//网易云信分配的账号，请替换你在管理后台应用下申请的Appkey
	$AppKey = '6973948f02cb204c13ad868f636660b9';
	//网易云信分配的账号，请替换你在管理后台应用下申请的appSecret
	$AppSecret = '2b6bc24f75a6';
	$p = new Org\Xb\ServerAPI($AppKey,$AppSecret,'curl');     //fsockopen伪造请求

	//发送短信验证码
	//print_r( $p->sendSmsCode('14809235','15010150766','','6') );

	//发送模板短信
	print_r( $p->sendSMSTemplate('14799317',$phone,$msg));
}

function sendldxmsg($phone){
	//网易云信分配的账号，请替换你在管理后台应用下申请的Appkey
	$AppKey = '6973948f02cb204c13ad868f636660b9';
	//网易云信分配的账号，请替换你在管理后台应用下申请的appSecret
	$AppSecret = '2b6bc24f75a6';
	$p = new Org\Xb\ServerAPI($AppKey,$AppSecret,'curl');     //fsockopen伪造请求

	//发送短信验证码
	//print_r( $p->sendSmsCode('14809235','15010150766','','6') );

	//发送模板短信
	return $p->sendSmsCode('14809235',$phone,'','6');
}
//下载
// function query(){
//     $file_name = "EASYLOOK.ipa";
//     $file_dir = "www.easylook.com";


// }

/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 */
function encode($string = '', $skey = 'mjiangkey') {
 $strArr = str_split(base64_encode($string));
 $strCount = count($strArr);
 foreach (str_split($skey) as $key => $value)
  $key < $strCount && $strArr[$key].=$value;
 return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}
/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 */
function decode($string = '', $skey = 'mjiangkey') {
 $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
 $strCount = count($strArr);
 foreach (str_split($skey) as $key => $value)
  $key <= $strCount && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
 return base64_decode(join('', $strArr));
}

/*key:vjZYJ6f7LvPPr6zitbmh*/
function ldx_encode($string = '', $skey = 'vjZYJ6f7LvPPr6zitbmh') {
 $strArr = str_split(base64_encode($string));
 $strCount = count($strArr);
 foreach (str_split($skey) as $key => $value)
  $key < $strCount && $strArr[$key].=$value;
 return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

function ldx_decode($string = '', $skey = 'vjZYJ6f7LvPPr6zitbmh') {
 $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
 $strCount = count($strArr);
 foreach (str_split($skey) as $key => $value)
  $key <= $strCount && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
 return base64_decode(join('', $strArr));
}

function http($url, $data='', $method='GET'){
    $curl = curl_init(); // 启动一个CURL会话  
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址  
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查  
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在  
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器  
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转  
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer  
    if($method=='POST'){  
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求  
        if ($data != ''){  
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包  
        }  
    }  
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环  
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回  
    $tmpInfo = curl_exec($curl); // 执行操作  
    curl_close($curl); // 关闭CURL会话  
    return $tmpInfo; // 返回数据  
}

?>