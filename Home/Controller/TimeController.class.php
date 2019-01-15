<?php
namespace Home\Controller;
use Think\Controller;
class TimeController extends Controller {
    public function activate(){
     	 $time = date("YmdHis",time());
     	 $header = getallheaders();
     	 echo json_encode(array('status'=>"ok"
     	 	,'access_key'=>'1234567890123456789012345678901234567890123456789012345678901234'
     	 	,'time'=>$time));
     	 exit;
    }
    
    public function info(){
     	 echo json_encode(array('status'=>"ok"
     	 	,'i_channels'=>"1,2,3,4"
     	 	,'v_channels'=>"1"));
     	 exit;
    }
    
    public function data(){
    	$header = getallheaders();
    	//var_dump($header);
    	$post      =file_get_contents('php://input');
    	{
          $imgDir = "imcloud/";
          if(!file_exists("imcloud")){
                 mkdir("imcloud");
          }
          if(!file_exists($imgDir)){
             mkdir($imgDir);
          }

          $lnewFilePath = $imgDir."/";
          			
          $filename = date("Ymd_His_").mt_rand(10, 99).".bmp"; //ÐÂÍ¼Æ¬Ãû³Æ
          $newFilePath = $lnewFilePath.$filename;//Í¼Æ¬´æÈëÂ·¾¶
          $newFile = fopen($newFilePath,"w"); //´ò¿ªÎÄ¼þ×¼±¸Ð´Èë
          fwrite($newFile,$post);
          fclose($newFile); //¹Ø±ÕÎÄ¼þ
    	}
    	//sleep(1);
     	 $time = date("YmdHis",time());
     	 echo json_encode(array('request'=>"interval_change",'interval'=>'30','time'=>$time));
     	 //echo json_encode(array('request'=>"reboot"));
     	 //echo json_encode(array('request'=>"fw_update",'fw_version'=>4098,'domain'=>"iot.xunrun.com.cn",'checksum'=>"01020304",'size'=>9953280));
     	 exit;
    }

    public function fw(){

	   $file = fopen("app/fw_4098.tar","r");  
	   //返回的文件类型  
	   if($file!=NULL){
		   Header("Content-type: application/octet-stream");  
		   //按照字节大小返回  
		   Header("Accept-Ranges: bytes");  
		   //返回文件的大小  
		   Header("Accept-Length: ".filesize("app/fw_4098.tar"));  
		   //这里对客户端的弹出对话框，对应的文件名  
		   Header("Content-Disposition: attachment; filename="."fw_4098.tar");  
		   //修改之前，一次性将数据传输给客户端  
		   echo fread($file, filesize("app/fw_4098.tar"));  
		   //修改之后，一次只传输1024个字节的数据给客户端  
		   //向客户端回送数据  
		   $buffer=1024*1000;
		   //判断文件是否读完  
		   while (!feof($file)) {
		    //将文件读入内存  
		    $file_data=fread($file,$buffer);  
		    //每次向客户端回送1024个字节的数据  
		    echo $file_data;  
		   }
		}
	   fclose($file);
    }
}