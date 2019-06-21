<?php
use JPush\Client as JPush;  
function SendMail($to, $title, $content,$path,$name) {

	 Vendor('PHPMailer.PHPMailerAutoload');
	 $mail = new PHPMailer(); //实例化
	 $mail->IsSMTP(); // 启用SMTP
	 $mail->Host=C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
	 $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
	 $mail->Username = C('MAIL_USERNAME'); //发件人邮箱名
	 $mail->Password = C('MAIL_PASSWORD') ; //163邮箱发件人授权密码
	 $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
	 $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
	 $mail->AddAddress($to,"尊敬的客户");
	 $mail->WordWrap = 31; //设置每行字符长度
	 $mail->AddAttachment($path,$name);//附件的路径和附件名称
	 $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
	 $mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
	 $mail->Subject =$title; //邮件主题
	 $mail->Body = $content; //邮件内容
	 $mail->AltBody = "这是一个纯文本的在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
	 return($mail->Send());
}

function send_sms_code($phone,$code){
    //请求地址，格式如下，不需要写https://
    $serverIP='app.cloopen.com';
    //请求端口
    $serverPort='8883';
    //REST版本号
    $softVersion='2013-12-26';
    //主帐号
    $accountSid=C('RONGLIAN_ACCOUNT_SID');
    //主帐号Token
    $accountToken=C('RONGLIAN_ACCOUNT_TOKEN');
    //应用Id
    $appId=C('RONGLIAN_APPID');

    $rest = new Org\Xb\Rest($serverIP,$serverPort,$softVersion);
    $rest->setAccount($accountSid,$accountToken);
    $rest->setAppId($appId);
    // 发送模板短信
    $result=$rest->sendTemplateSMS($phone,array($code,5),1);
    if($result==NULL) {
    	return false;
    }
    if($result->statusCode!=0) {
    	return  false;
    }else{
    	return true;
    }

}




function voiceVerify($verifyCode,$playTimes,$to,$displayNum,$respUrl,$lang,$userData,$welcomePrompt,$playVerifyCode)
{

	  	//请求地址，格式如下，不需要写https://
	    $serverIP='sandboxapp.cloopen.com';
	    //请求端口
	    $serverPort='8883';
	    //REST版本号
	    $softVersion='2013-12-26';
	    //主帐号
	    $accountSid=C('RONGLIAN_ACCOUNT_SID');
	    //主帐号Token
	    $accountToken=C('RONGLIAN_ACCOUNT_TOKEN');
	    //应用Id
	    $appId=C('RONGLIAN_APPID');

        // 初始化REST SDK

        $rest =  new Org\Xb\Rest($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);

        //调用语音验证码接口
        echo "Try to make a voiceverify,called is $to <br/>";
        $result = $rest->voiceVerify($verifyCode,$playTimes,$to,$displayNum,$respUrl,$lang,$userData,$welcomePrompt,$playVerifyCode);
         if($result == NULL ) {
            echo "result error!";
            break;
        }

        if($result->statusCode!=0) {
            echo "error code :" . $result->statusCode . "<br>";
            echo "error msg :" . $result->statusMsg . "<br>";
            //TODO 添加错误处理逻辑
        } else{
            echo "voiceverify success!<br>";
            // 获取返回信息
            $voiceVerify = $result->VoiceVerify;
            echo "callSid:".$voiceVerify->callSid."<br/>";
            echo "dateCreated:".$voiceVerify->dateCreated."<br/>";
           //TODO 添加成功处理逻辑
        }
}





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
  
/** 
 * 向所有设备推送消息 
 * @param string $message 需要推送的消息 
 */  
function sendNotifyAll($message){  
   require_once "JPush/autoload.php";  
   $app_key = 'bf74ed9e18f0f2771d1e959e';                //填入你的app_key  
   $master_secret = '230e49626a5987a514b11c93';    //填入你的master_secret  
   $client = new JPush($app_key,$master_secret);  
   $result = $client->push()->setPlatform('all')->addAllAudience()->setNotificationAlert($message)
   ->iosNotification('Hello IOS', array(
                'sound' => 'hello jpush',
                'badge' => 1,
                'content-available' => true,
                'category' => 'jiguang',
                'extras' => array(
                    'key' => 'value',
                    'jiguang'
                ),
            ))
   ->send();  
   
   return json_array($result);  
}  
  
  
/** 
 * 向特定设备推送消息 
 * @param array $regid 特定设备的设备标识 
 * @param string $message 需要推送的消息 
 */  
function sendNotifySpecial($regid,$message){ 
   require_once "JPush/autoload.php";  
   $app_key = 'bf74ed9e18f0f2771d1e959e';                //填入你的app_key  
   $master_secret = '230e49626a5987a514b11c93';    //填入你的master_secret  
   $client = new JPush($app_key,$master_secret);  
   $result = $client->push()->setPlatform('all')->addRegistrationId($regid)->setNotificationAlert($message)->send();  
   return json_array($result);  
}  


 /** 
 * 向指定设备推送自定义消息 
 * @param string $message 发送消息内容 
 * @param array $regid 特定设备的id 
 * @param int $did 状态值1 
 * @param int $mid 状态值2 
 */  
  
function sendSpecialMsg($regid,$message,$did,$mid){  
   require_once "JPush/autoload.php";  
   $app_key = 'bf74ed9e18f0f2771d1e959e';                //填入你的app_key  
   $master_secret = '230e49626a5987a514b11c93';    //填入你的master_secret  
   $client = new JPush($app_key,$master_secret);  
   $result = $client->push()->setPlatform('all')->addRegistrationId($regid)  
      ->addAndroidNotification($message,'',1,array('did'=>$did,'mid'=>$mid))  
      ->addIosNotification($message,'','+1',true,'',array('did'=>$did,'mid'=>$mid))->send();  
  
   return json_array($result);  
}  
  
/** 
 * 得到各类统计数据 
 * @param array $msgIds 推送消息返回的msg_id列表 
 */  
function reportNotify($msgIds){  
   require_once "JPush/autoload.php";  
   $app_key = 'bf74ed9e18f0f2771d1e959e';                //填入你的app_key  
   $master_secret = '230e49626a5987a514b11c93';    //填入你的master_secret  
   $client = new JPush($app_key,$master_secret);  
   $response = $client->report()->getReceived($msgIds);  
   return json_array($response);  
} 

function send163msg($phone,$msg){
	//网易云信分配的账号，请替换你在管理后台应用下申请的Appkey
	$AppKey = '6973948f02cb204c13ad868f636660b9';
	//网易云信分配的账号，请替换你在管理后台应用下申请的appSecret
	$AppSecret = '2b6bc24f75a6';
	$p = new Org\Xb\ServerAPI($AppKey,$AppSecret,'curl');     //fsockopen伪造请求

	//发送短信验证码
	//print_r( $p->sendSmsCode('14809235',$phone,'','6') );

	//发送模板短信
	print_r( $p->sendSMSTemplate('14799317',$phone,$msg));
}

//下载
// function query(){
//     $file_name = "EASYLOOK.ipa";
//     $file_dir = "www.easylook.com";


// } 


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

function gettemp($adc){
	//var_dump($adc);
	$temp = -1;
	$temp_table=array(
				array('base_adc'=>70030,'temp'=>-20),
				array('base_adc'=>66660,'temp'=>-19),
				array('base_adc'=>63470,'temp'=>-18),
              	array('base_adc'=>60450,'temp'=>-17),
              	array('base_adc'=>57600,'temp'=>-16),
              	array('base_adc'=>54890,'temp'=>-15),
              	array('base_adc'=>52330,'temp'=>-14),
              	array('base_adc'=>49900,'temp'=>-13),
				array('base_adc'=>47600,'temp'=>-12),
				array('base_adc'=>45420,'temp'=>-11),
				array('base_adc'=>43350,'temp'=>-10),
				array('base_adc'=>41380,'temp'=>-9),
				array('base_adc'=>39520,'temp'=>-8),
				array('base_adc'=>37750,'temp'=>-7),
				array('base_adc'=>36070,'temp'=>-6),
              	array('base_adc'=>34480,'temp'=>-5),
              	array('base_adc'=>32960,'temp'=>-4),
              	array('base_adc'=>31520,'temp'=>-3),
              	array('base_adc'=>30160,'temp'=>-2),
              	array('base_adc'=>28850,'temp'=>-1),
              	array('base_adc'=>27620,'temp'=>0),
              	array('base_adc'=>26440,'temp'=>1),
				array('base_adc'=>25320,'temp'=>2),
                array('base_adc'=>24250,'temp'=>3),
              	array('base_adc'=>23230,'temp'=>4),
              	array('base_adc'=>22270,'temp'=>5),
             	array('base_adc'=>21340,'temp'=>6),
              	array('base_adc'=>20460,'temp'=>7),
              	array('base_adc'=>19630,'temp'=>8),
              	array('base_adc'=>18830,'temp'=>9),
              array('base_adc'=>18070,'temp'=>10),
              array('base_adc'=>17340,'temp'=>11),
              array('base_adc'=>16650,'temp'=>12),
              array('base_adc'=>15980,'temp'=>13),
									array('base_adc'=>15350,'temp'=>14),
                  array('base_adc'=>14750,'temp'=>15),
                  array('base_adc'=>14170,'temp'=>16),
                  array('base_adc'=>13620,'temp'=>17), 
                  array('base_adc'=>13090,'temp'=>18),
                  array('base_adc'=>12590,'temp'=>19),
                  array('base_adc'=>12110,'temp'=>20),
									array('base_adc'=>11650,'temp'=>21),
                  array('base_adc'=>11210,'temp'=>22),
                  array('base_adc'=>10790,'temp'=>23),
                  array('base_adc'=>10390,'temp'=>24),
                  array('base_adc'=>10000,'temp'=>25),
                  array('base_adc'=>9631,'temp'=>26),
                  array('base_adc'=>9277,'temp'=>27),
				  array('base_adc'=>8938,'temp'=>28),
                  array('base_adc'=>8613,'temp'=>29),
                  array('base_adc'=>8302,'temp'=>30),
                  array('base_adc'=>8004,'temp'=>31),
                  array('base_adc'=>7718,'temp'=>32),
                  array('base_adc'=>7444,'temp'=>33),
                  array('base_adc'=>7180,'temp'=>34),                  
				  array('base_adc'=>6928,'temp'=>35),
                  array('base_adc'=>6686,'temp'=>36),
                  array('base_adc'=>6454,'temp'=>37),
                  array('base_adc'=>6230,'temp'=>38),
                  array('base_adc'=>6016,'temp'=>39),
                  array('base_adc'=>5810,'temp'=>40),
                  array('base_adc'=>5613,'temp'=>41),
                  array('base_adc'=>5423,'temp'=>42),
				  array('base_adc'=>5240,'temp'=>43),
                  array('base_adc'=>5065,'temp'=>44),
                  array('base_adc'=>4897,'temp'=>45),
                  array('base_adc'=>4734,'temp'=>46),
                  array('base_adc'=>4578,'temp'=>47),
                  array('base_adc'=>4429,'temp'=>48),
                  array('base_adc'=>4284,'temp'=>49),
                  array('base_adc'=>4145,'temp'=>50),
				  array('base_adc'=>4012,'temp'=>51),
                  array('base_adc'=>3883,'temp'=>52),
                  array('base_adc'=>3759,'temp'=>53),
                  array('base_adc'=>3640,'temp'=>54),
                  array('base_adc'=>3525,'temp'=>55),
                  array('base_adc'=>3415,'temp'=>56),
                  array('base_adc'=>3308,'temp'=>57),
                  array('base_adc'=>3205,'temp'=>58),                                      
                  );
  $size=count($temp_table);
  for($i=0;$i<$size;$i++){
  	if($adc >= $temp_table[$i]['base_adc']){
  		//var_dump( $temp_table[$i]['base_adc']);
  		//var_dump($i);
  		if($i>0){
  			$step_adc = ($temp_table[$i-1]['base_adc'] - $temp_table[$i]['base_adc'])/10;
  			//var_dump($step_adc);
  			$step_count=($adc- $temp_table[$i]['base_adc'])/ $step_adc;
				//var_dump($step_count);
				//var_dump($temp_table[$i-1]['step_adc']);
  			$temp= $temp_table[$i]['temp']-$step_count*0.1;
  			break;
  		}else{
  		  break;
  		}
  	}
  }
  

	return round($temp,2);
};

function gettemp2($adc){
	//var_dump($adc);
	$temp = -1;
	$temp_table=array(
									array('max_adc'=>36928, 'min_adc'=>36814,'temp'=>32.0),
									array('max_adc'=>36771, 'min_adc'=>36660,'temp'=>32.1),
									array('max_adc'=>36616, 'min_adc'=>36506,'temp'=>32.2),
									array('max_adc'=>36461, 'min_adc'=>36353,'temp'=>32.3),
									array('max_adc'=>36307, 'min_adc'=>36201,'temp'=>32.4),
									array('max_adc'=>36153, 'min_adc'=>36050,'temp'=>32.5),
									array('max_adc'=>36001, 'min_adc'=>35899,'temp'=>32.6),
									array('max_adc'=>35849, 'min_adc'=>35749,'temp'=>32.7),
									array('max_adc'=>35698, 'min_adc'=>35600,'temp'=>32.8),
									array('max_adc'=>35547, 'min_adc'=>35451,'temp'=>32.9),						
									array('max_adc'=>35397, 'min_adc'=>35303,'temp'=>33.0),
									array('max_adc'=>35248, 'min_adc'=>35156,'temp'=>33.1),
									array('max_adc'=>35100, 'min_adc'=>35010,'temp'=>33.2),
									array('max_adc'=>34952, 'min_adc'=>34864,'temp'=>33.3),
									array('max_adc'=>34806, 'min_adc'=>34719,'temp'=>33.4),
									array('max_adc'=>34659, 'min_adc'=>34574,'temp'=>33.5),
									array('max_adc'=>34514, 'min_adc'=>34431,'temp'=>33.6),
									array('max_adc'=>34369, 'min_adc'=>34288,'temp'=>33.7),
									array('max_adc'=>34225, 'min_adc'=>34145,'temp'=>33.8),
									array('max_adc'=>34081, 'min_adc'=>34004,'temp'=>33.9),
									array('max_adc'=>33939, 'min_adc'=>33862,'temp'=>34.0),
									array('max_adc'=>33796, 'min_adc'=>33722,'temp'=>34.1),
									array('max_adc'=>33655, 'min_adc'=>33582,'temp'=>34.2),
									array('max_adc'=>33514, 'min_adc'=>33443,'temp'=>34.3),
									array('max_adc'=>33374, 'min_adc'=>33305,'temp'=>34.4),
									array('max_adc'=>33235, 'min_adc'=>33167,'temp'=>34.5),
									array('max_adc'=>33096, 'min_adc'=>33030,'temp'=>34.6),
									array('max_adc'=>32958, 'min_adc'=>32893,'temp'=>34.7),
									array('max_adc'=>32820, 'min_adc'=>32758,'temp'=>34.8),
									array('max_adc'=>32683, 'min_adc'=>32622,'temp'=>34.9),								
									array('max_adc'=>32547, 'min_adc'=>32488,'temp'=>35.0),
									array('max_adc'=>32412, 'min_adc'=>32354,'temp'=>35.1),
									array('max_adc'=>32277, 'min_adc'=>32221,'temp'=>35.2),
									array('max_adc'=>32142, 'min_adc'=>32088,'temp'=>35.3),
									array('max_adc'=>32009, 'min_adc'=>31956,'temp'=>35.4),
									array('max_adc'=>31876, 'min_adc'=>31824,'temp'=>35.5),
									array('max_adc'=>31743, 'min_adc'=>31693,'temp'=>35.6),
									array('max_adc'=>31612, 'min_adc'=>31563,'temp'=>35.7),
									array('max_adc'=>31481, 'min_adc'=>31434,'temp'=>35.8),
									array('max_adc'=>31350, 'min_adc'=>31305,'temp'=>35.9),									
									array('max_adc'=>31220, 'min_adc'=>31176,'temp'=>36.0),
									array('max_adc'=>31091, 'min_adc'=>31048,'temp'=>36.1),
									array('max_adc'=>30962, 'min_adc'=>30921,'temp'=>36.2),
									array('max_adc'=>30834, 'min_adc'=>30794,'temp'=>36.3),
									array('max_adc'=>30707, 'min_adc'=>30668,'temp'=>36.4),
									array('max_adc'=>30580, 'min_adc'=>30543,'temp'=>36.5),
									array('max_adc'=>30453, 'min_adc'=>30418,'temp'=>36.6),
									array('max_adc'=>30328, 'min_adc'=>30294,'temp'=>36.7),
									array('max_adc'=>30202, 'min_adc'=>30170,'temp'=>36.8),
									array('max_adc'=>30078, 'min_adc'=>30047,'temp'=>36.9),
									array('max_adc'=>29954, 'min_adc'=>29924,'temp'=>37.0),
									array('max_adc'=>29832, 'min_adc'=>29801,'temp'=>37.1),
									array('max_adc'=>29710, 'min_adc'=>29678,'temp'=>37.2),
									array('max_adc'=>29589, 'min_adc'=>29556,'temp'=>37.3),
									array('max_adc'=>29469, 'min_adc'=>29434,'temp'=>37.4),
									array('max_adc'=>29349, 'min_adc'=>29313,'temp'=>37.5),
									array('max_adc'=>29229, 'min_adc'=>29193,'temp'=>37.6),
									array('max_adc'=>29111, 'min_adc'=>29073,'temp'=>37.7),
									array('max_adc'=>28992, 'min_adc'=>28954,'temp'=>37.8),
									array('max_adc'=>28875, 'min_adc'=>28835,'temp'=>37.9),				
									array('max_adc'=>28757, 'min_adc'=>28717,'temp'=>38.0),
									array('max_adc'=>28641, 'min_adc'=>28599,'temp'=>38.1),
									array('max_adc'=>28525, 'min_adc'=>28482,'temp'=>38.2),
									array('max_adc'=>28409, 'min_adc'=>28366,'temp'=>38.3),
									array('max_adc'=>28294, 'min_adc'=>28250,'temp'=>38.4),
									array('max_adc'=>28180, 'min_adc'=>28134,'temp'=>38.5),
									array('max_adc'=>28066, 'min_adc'=>28019,'temp'=>38.6),
									array('max_adc'=>27952, 'min_adc'=>27905,'temp'=>38.7),
									array('max_adc'=>27839, 'min_adc'=>27791,'temp'=>38.8),
									array('max_adc'=>27727, 'min_adc'=>27678,'temp'=>38.9),														
									array('max_adc'=>27615, 'min_adc'=>27565,'temp'=>39.0),
									array('max_adc'=>27503, 'min_adc'=>27452,'temp'=>39.1),
									array('max_adc'=>27393, 'min_adc'=>27341,'temp'=>39.2),
									array('max_adc'=>27282, 'min_adc'=>27229,'temp'=>39.3),
									array('max_adc'=>27172, 'min_adc'=>27119,'temp'=>39.4),
									array('max_adc'=>27063, 'min_adc'=>27008,'temp'=>39.5),
									array('max_adc'=>26954, 'min_adc'=>26899,'temp'=>39.6),
									array('max_adc'=>26846, 'min_adc'=>26789,'temp'=>39.7),
									array('max_adc'=>26738, 'min_adc'=>26681,'temp'=>39.8),
									array('max_adc'=>26630, 'min_adc'=>26572,'temp'=>39.9),					
									array('max_adc'=>26524, 'min_adc'=>26465,'temp'=>40.0),
									array('max_adc'=>26417, 'min_adc'=>26357,'temp'=>40.1),
									array('max_adc'=>26311, 'min_adc'=>26251,'temp'=>40.2),
									array('max_adc'=>26206, 'min_adc'=>26144,'temp'=>40.3),
									array('max_adc'=>26101, 'min_adc'=>26039,'temp'=>40.4),
									array('max_adc'=>25996, 'min_adc'=>25933,'temp'=>40.5),
									array('max_adc'=>25892, 'min_adc'=>25829,'temp'=>40.6),
									array('max_adc'=>25789, 'min_adc'=>25724,'temp'=>40.7),
									array('max_adc'=>25686, 'min_adc'=>25620,'temp'=>40.8),
									array('max_adc'=>25583, 'min_adc'=>25517,'temp'=>40.9),	
									array('max_adc'=>25481, 'min_adc'=>25414,'temp'=>41.0),
									array('max_adc'=>25414, 'min_adc'=>25414,'temp'=>41.1),																
                  );
  $size=count($temp_table);
  for($i=0;$i<$size;$i++){
  	if($adc >= $temp_table[$i]['max_adc']){
  		if($i>0){
  			if($adc >= $temp_table[$i-1]['min_adc']){
  				$temp= $temp_table[$i-1]['temp'];
  			}else{
  				$step=($temp_table[$i-1]['min_adc']-$temp_table[$i]['max_adc'])/2;
  				//var_dump($step);
  				if($adc-$temp_table[$i]['max_adc']<=$step)
  				{
  					$temp= $temp_table[$i]['temp'];
  				}else{
  					$temp= $temp_table[$i-1]['temp'];
  				}
  			}
  			break;
  		}else{
  		  break;
  		}
  	}
  }
	return round($temp,2);
};

function gettemp3($adc){
	//var_dump($adc);
	$temp = -255;
	$temp_table=array(
					array('base_adc'=>482038,'temp'=>-20.0),
					array('base_adc'=>470586,'temp'=>-20.0),
					array('base_adc'=>444857,'temp'=>-19.0),
					array('base_adc'=>420664,'temp'=>-18.0),
					array('base_adc'=>397908,'temp'=>-17.0),
					array('base_adc'=>376497,'temp'=>-16.0),
					array('base_adc'=>356347,'temp'=>-15.0),
					array('base_adc'=>337377,'temp'=>-14.0),
					array('base_adc'=>319513,'temp'=>-13.0),
					array('base_adc'=>302685,'temp'=>-12.0),
					array('base_adc'=>286830,'temp'=>-11.0),
					
					array('base_adc'=>271885,'temp'=>-10.0),
					array('base_adc'=>257796,'temp'=>-9.0),
					array('base_adc'=>244508,'temp'=>-8.0),
					array('base_adc'=>231974,'temp'=>-7.0),
					array('base_adc'=>220146,'temp'=>-6.0),
					array('base_adc'=>208983,'temp'=>-5.0),
					array('base_adc'=>198442,'temp'=>-4.0),
					array('base_adc'=>188488,'temp'=>-3.0),
					array('base_adc'=>179084,'temp'=>-2.0),
					array('base_adc'=>170198,'temp'=>-1.0),
					
					array('base_adc'=>161798,'temp'=>0.0),					
					array('base_adc'=>153857,'temp'=>1.0),
					array('base_adc'=>146346,'temp'=>2.0),
					array('base_adc'=>139241,'temp'=>3.0),
					array('base_adc'=>132518,'temp'=>4.0),
					array('base_adc'=>126154,'temp'=>5.0),
					array('base_adc'=>120128,'temp'=>6.0),
					array('base_adc'=>114422,'temp'=>7.0),
					array('base_adc'=>109016,'temp'=>8.0),
					array('base_adc'=>103894,'temp'=>9.0),
					
					array('base_adc'=>99039,'temp'=>10.0),					
					array('base_adc'=>94436,'temp'=>11.0),
					array('base_adc'=>90071,'temp'=>12.0),
					array('base_adc'=>85930,'temp'=>13.0),
					array('base_adc'=>82001,'temp'=>14.0),
					array('base_adc'=>78273,'temp'=>15.0),
					array('base_adc'=>74733,'temp'=>16.0),
					array('base_adc'=>71371,'temp'=>17.0),
					array('base_adc'=>68179,'temp'=>18.0),
					array('base_adc'=>65145,'temp'=>19.0),
					
					array('base_adc'=>62263,'temp'=>20.0),
					array('base_adc'=>59522,'temp'=>21.0),
					array('base_adc'=>56917,'temp'=>22.0),
					array('base_adc'=>54439,'temp'=>23.0),
					array('base_adc'=>52082,'temp'=>24.0),
					
					array('base_adc'=>49839,'temp'=>25.0),
					array('base_adc'=>49621,'temp'=>25.1),
					array('base_adc'=>49404,'temp'=>25.2),
					array('base_adc'=>49188,'temp'=>25.3),
					array('base_adc'=>48973,'temp'=>25.4),
					array('base_adc'=>48759,'temp'=>25.5),
					array('base_adc'=>48546,'temp'=>25.6),
					array('base_adc'=>48334,'temp'=>25.7),
					array('base_adc'=>48123,'temp'=>25.8),
					array('base_adc'=>47914,'temp'=>25.9),
					
					array('base_adc'=>47705,'temp'=>26.0),
					array('base_adc'=>47497,'temp'=>26.1),
					array('base_adc'=>47290,'temp'=>26.2),
					array('base_adc'=>47085,'temp'=>26.3),
					array('base_adc'=>46880,'temp'=>26.4),
					array('base_adc'=>46676,'temp'=>26.5),
					array('base_adc'=>46474,'temp'=>26.6),
					array('base_adc'=>46272,'temp'=>26.7),
					array('base_adc'=>46071,'temp'=>26.8),
					array('base_adc'=>45872,'temp'=>26.9),
					
					array('base_adc'=>45673,'temp'=>27.0),
					array('base_adc'=>45475,'temp'=>27.1),
					array('base_adc'=>45278,'temp'=>27.2),
					array('base_adc'=>45082,'temp'=>27.3),
					array('base_adc'=>44887,'temp'=>27.4),
					array('base_adc'=>44693,'temp'=>27.5),
					array('base_adc'=>44500,'temp'=>27.6),
					array('base_adc'=>44308,'temp'=>27.7),
					array('base_adc'=>44117,'temp'=>27.8),
					array('base_adc'=>43927,'temp'=>27.9),
					
					array('base_adc'=>43738,'temp'=>28.0),
					array('base_adc'=>43549,'temp'=>28.1),
					array('base_adc'=>43362,'temp'=>28.2),
					array('base_adc'=>43175,'temp'=>28.3),
					array('base_adc'=>42990,'temp'=>28.4),
					array('base_adc'=>42805,'temp'=>28.5),
					array('base_adc'=>42621,'temp'=>28.6),
					array('base_adc'=>42438,'temp'=>28.7),
					array('base_adc'=>42256,'temp'=>28.8),
					array('base_adc'=>42075,'temp'=>28.9),
					
					array('base_adc'=>41895,'temp'=>29.0),
					array('base_adc'=>41715,'temp'=>29.1),
					array('base_adc'=>41537,'temp'=>29.2),
					array('base_adc'=>41359,'temp'=>29.3),
					array('base_adc'=>41182,'temp'=>29.4),
					array('base_adc'=>41006,'temp'=>29.5),
					array('base_adc'=>40831,'temp'=>29.6),
					array('base_adc'=>40657,'temp'=>29.7),
					array('base_adc'=>40483,'temp'=>29.8),
					array('base_adc'=>40311,'temp'=>29.9),

					array('base_adc'=>40139,'temp'=>30.0),
					array('base_adc'=>39968,'temp'=>30.1),
					array('base_adc'=>39798,'temp'=>30.2),
					array('base_adc'=>39628,'temp'=>30.3),
					array('base_adc'=>39460,'temp'=>30.4),
					array('base_adc'=>39292,'temp'=>30.5),
					array('base_adc'=>39125,'temp'=>30.6),
					array('base_adc'=>38959,'temp'=>30.7),
					array('base_adc'=>38794,'temp'=>30.8),
					array('base_adc'=>38629,'temp'=>30.9),
					
					array('base_adc'=>38466,'temp'=>31.0),
					array('base_adc'=>38303,'temp'=>31.1),
					array('base_adc'=>38141,'temp'=>31.2),
					array('base_adc'=>37979,'temp'=>31.3),
					array('base_adc'=>37819,'temp'=>31.4),
					array('base_adc'=>37659,'temp'=>31.5),
					array('base_adc'=>37500,'temp'=>31.6),
					array('base_adc'=>37341,'temp'=>31.7),
					array('base_adc'=>37184,'temp'=>31.8),
					array('base_adc'=>37027,'temp'=>31.9),
					
					array('base_adc'=>36871,'temp'=>32.0),
					array('base_adc'=>36716,'temp'=>32.1),
					array('base_adc'=>36561,'temp'=>32.2),
					array('base_adc'=>36407,'temp'=>32.3),
					array('base_adc'=>36254,'temp'=>32.4),
					array('base_adc'=>36102,'temp'=>32.5),
					array('base_adc'=>35950,'temp'=>32.6),
					array('base_adc'=>35799,'temp'=>32.7),
					array('base_adc'=>35649,'temp'=>32.8),
					array('base_adc'=>35499,'temp'=>32.9),
					
					array('base_adc'=>35350,'temp'=>33.0),
					array('base_adc'=>35202,'temp'=>33.1),
					array('base_adc'=>35055,'temp'=>33.2),
					array('base_adc'=>34908,'temp'=>33.3),
					array('base_adc'=>34762,'temp'=>33.4),
					array('base_adc'=>34617,'temp'=>33.5),
					array('base_adc'=>34472,'temp'=>33.6),
					array('base_adc'=>34328,'temp'=>33.7),
					array('base_adc'=>34185,'temp'=>33.8),
					array('base_adc'=>34042,'temp'=>33.9),
					
					array('base_adc'=>33900,'temp'=>34.0),
					array('base_adc'=>33759,'temp'=>34.1),
					array('base_adc'=>33619,'temp'=>34.2),
					array('base_adc'=>33479,'temp'=>34.3),
					array('base_adc'=>33339,'temp'=>34.4),
					array('base_adc'=>33201,'temp'=>34.5),
					array('base_adc'=>33063,'temp'=>34.6),
					array('base_adc'=>32926,'temp'=>34.7),
					array('base_adc'=>32789,'temp'=>34.8),
					array('base_adc'=>32653,'temp'=>34.9),
					
					array('base_adc'=>32518,'temp'=>35.0),
					array('base_adc'=>32383,'temp'=>35.1),
					array('base_adc'=>32249,'temp'=>35.2),
					array('base_adc'=>32115,'temp'=>35.3),
					array('base_adc'=>31982,'temp'=>35.4),
					array('base_adc'=>31850,'temp'=>35.5),
					array('base_adc'=>31718,'temp'=>35.6),
					array('base_adc'=>31587,'temp'=>35.7),
					array('base_adc'=>31457,'temp'=>35.8),
					array('base_adc'=>31327,'temp'=>35.9),
					
					array('base_adc'=>31198,'temp'=>36.0),
					array('base_adc'=>31070,'temp'=>36.1),
					array('base_adc'=>30942,'temp'=>36.2),
					array('base_adc'=>30814,'temp'=>36.3),
					array('base_adc'=>30687,'temp'=>36.4),
					array('base_adc'=>30561,'temp'=>36.5),
					array('base_adc'=>30436,'temp'=>36.6),
					array('base_adc'=>30311,'temp'=>36.7),
					array('base_adc'=>30186,'temp'=>36.8),
					array('base_adc'=>30062,'temp'=>36.9),
					
					array('base_adc'=>29939,'temp'=>37.0),
					array('base_adc'=>29816,'temp'=>37.1),
					array('base_adc'=>29694,'temp'=>37.2),
					array('base_adc'=>29573,'temp'=>37.3),
					array('base_adc'=>29452,'temp'=>37.4),
					array('base_adc'=>29331,'temp'=>37.5),
					array('base_adc'=>29211,'temp'=>37.6),
					array('base_adc'=>29092,'temp'=>37.7),
					array('base_adc'=>28973,'temp'=>37.8),
					array('base_adc'=>28855,'temp'=>37.9),
					
					array('base_adc'=>28737,'temp'=>38.0),
					array('base_adc'=>28620,'temp'=>38.1),
					array('base_adc'=>28503,'temp'=>38.2),
					array('base_adc'=>28387,'temp'=>38.3),
					array('base_adc'=>28272,'temp'=>38.4),
					array('base_adc'=>28157,'temp'=>38.5),
					array('base_adc'=>28042,'temp'=>38.6),
					array('base_adc'=>27928,'temp'=>38.7),
					array('base_adc'=>27815,'temp'=>38.8),
					array('base_adc'=>27702,'temp'=>38.9),
					
					array('base_adc'=>27590,'temp'=>39.0),
					array('base_adc'=>27478,'temp'=>39.1),
					array('base_adc'=>27367,'temp'=>39.2),
					array('base_adc'=>27256,'temp'=>39.3),
					array('base_adc'=>27145,'temp'=>39.4),
					array('base_adc'=>27036,'temp'=>39.5),
					array('base_adc'=>26926,'temp'=>39.6),
					array('base_adc'=>26818,'temp'=>39.7),
					array('base_adc'=>26709,'temp'=>39.8),
					array('base_adc'=>26601,'temp'=>39.9),
					
					array('base_adc'=>26494,'temp'=>40.0),
					array('base_adc'=>26387,'temp'=>40.1),
					array('base_adc'=>26281,'temp'=>40.2),
					array('base_adc'=>26175,'temp'=>40.3),
					array('base_adc'=>26070,'temp'=>40.4),
					array('base_adc'=>25965,'temp'=>40.5),
					array('base_adc'=>25860,'temp'=>40.6),
					array('base_adc'=>25757,'temp'=>40.7),
					array('base_adc'=>25653,'temp'=>40.8),
					array('base_adc'=>25550,'temp'=>40.9),
					
					array('base_adc'=>25448,'temp'=>41.0),
					array('base_adc'=>25346,'temp'=>41.1),
					array('base_adc'=>25244,'temp'=>41.2),
					array('base_adc'=>25143,'temp'=>41.3),
					array('base_adc'=>25042,'temp'=>41.4),
					array('base_adc'=>24942,'temp'=>41.5),
					array('base_adc'=>24842,'temp'=>41.6),
					array('base_adc'=>24743,'temp'=>41.7),
					array('base_adc'=>24644,'temp'=>41.8),
					array('base_adc'=>24546,'temp'=>41.9),
					
					array('base_adc'=>24448,'temp'=>42.0),
					array('base_adc'=>24350,'temp'=>42.1),
					array('base_adc'=>24253,'temp'=>42.2),
					array('base_adc'=>24157,'temp'=>42.3),
					array('base_adc'=>24060,'temp'=>42.4),
					array('base_adc'=>23965,'temp'=>42.5),
					array('base_adc'=>23869,'temp'=>42.6),
					array('base_adc'=>23774,'temp'=>42.7),
					array('base_adc'=>23680,'temp'=>42.8),
					array('base_adc'=>23586,'temp'=>42.9),
					
					array('base_adc'=>23492,'temp'=>43.0),
					array('base_adc'=>23399,'temp'=>43.1),
					array('base_adc'=>23306,'temp'=>43.2),
					array('base_adc'=>23214,'temp'=>43.3),
					array('base_adc'=>23122,'temp'=>43.4),
					array('base_adc'=>23031,'temp'=>43.5),
					array('base_adc'=>22939,'temp'=>43.6),
					array('base_adc'=>22849,'temp'=>43.7),
					array('base_adc'=>22758,'temp'=>43.8),
					array('base_adc'=>22669,'temp'=>43.9),
					
					array('base_adc'=>22579,'temp'=>44.0),
					array('base_adc'=>22490,'temp'=>44.1),
					array('base_adc'=>22401,'temp'=>44.2),
					array('base_adc'=>22313,'temp'=>44.3),
					array('base_adc'=>22225,'temp'=>44.4),
					array('base_adc'=>22138,'temp'=>44.5),
					array('base_adc'=>22051,'temp'=>44.6),
					array('base_adc'=>21964,'temp'=>44.7),
					array('base_adc'=>21878,'temp'=>44.8),
					array('base_adc'=>21792,'temp'=>44.9),
					
					array('base_adc'=>21706,'temp'=>45.0),
					array('base_adc'=>21621,'temp'=>45.1),
					array('base_adc'=>21536,'temp'=>45.2),
					array('base_adc'=>21452,'temp'=>45.3),
					array('base_adc'=>21368,'temp'=>45.4),
					array('base_adc'=>21284,'temp'=>45.5),
					array('base_adc'=>21201,'temp'=>45.6),
					array('base_adc'=>21118,'temp'=>45.7),
					array('base_adc'=>21035,'temp'=>45.8),
					array('base_adc'=>20953,'temp'=>45.9),
					
					array('base_adc'=>20871,'temp'=>46.0),
					array('base_adc'=>20790,'temp'=>46.1),
					array('base_adc'=>20709,'temp'=>46.2),
					array('base_adc'=>20628,'temp'=>46.3),
					array('base_adc'=>20548,'temp'=>46.4),
					array('base_adc'=>20468,'temp'=>46.5),
					array('base_adc'=>20388,'temp'=>46.6),
					array('base_adc'=>20309,'temp'=>46.7),
					array('base_adc'=>20230,'temp'=>46.8),
					array('base_adc'=>20151,'temp'=>46.9),
					
					array('base_adc'=>20073,'temp'=>47.0),
					array('base_adc'=>19995,'temp'=>47.1),
					array('base_adc'=>19917,'temp'=>47.2),
					array('base_adc'=>19840,'temp'=>47.3),
					array('base_adc'=>19763,'temp'=>47.4),
					array('base_adc'=>19687,'temp'=>47.5),
					array('base_adc'=>19610,'temp'=>47.6),
					array('base_adc'=>19535,'temp'=>47.7),
					array('base_adc'=>19459,'temp'=>47.8),
					array('base_adc'=>19384,'temp'=>47.9),
					
					array('base_adc'=>19309,'temp'=>48.0),
					array('base_adc'=>19234,'temp'=>48.1),
					array('base_adc'=>19160,'temp'=>48.2), 
					array('base_adc'=>19086,'temp'=>48.3),
					array('base_adc'=>19013,'temp'=>48.4),
					array('base_adc'=>18939,'temp'=>48.5),
					array('base_adc'=>18867,'temp'=>48.6),
					array('base_adc'=>18794,'temp'=>48.7),
					array('base_adc'=>18722,'temp'=>48.8),
					array('base_adc'=>18650,'temp'=>48.9),
					
					array('base_adc'=>18578,'temp'=>49.0),
					array('base_adc'=>18507,'temp'=>49.1),
					array('base_adc'=>18436,'temp'=>49.2),
					array('base_adc'=>18365,'temp'=>49.3),
					array('base_adc'=>18295,'temp'=>49.4),
					array('base_adc'=>18224,'temp'=>49.5),
					array('base_adc'=>18155,'temp'=>49.6),
					array('base_adc'=>18085,'temp'=>49.7),
					array('base_adc'=>18016,'temp'=>49.8),
					array('base_adc'=>17947,'temp'=>49.9),
					array('base_adc'=>17879,'temp'=>50.0),
 );
  $size=count($temp_table);
  for($i=0;$i<$size;$i++){
  	if($adc >= $temp_table[$i]['base_adc']){
  		if($i>0){
  			$step_adc = ($temp_table[$i-1]['base_adc'] - $temp_table[$i]['base_adc'])/10;
  			$step_count=($adc- $temp_table[$i]['base_adc'])/ $step_adc;
			$step_temp = ($temp_table[$i]['temp'] - $temp_table[$i-1]['temp'])/10;	
  			$temp= $temp_table[$i]['temp']-$step_count*$step_temp;	
  			break;
  		}else{
  		  $temp= $temp_table[$i]['temp'];
  		  break;
  		}
  	}
  }

	return round($temp,2);
};
?>