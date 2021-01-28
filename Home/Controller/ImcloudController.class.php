<?php
namespace Home\Controller;
use Think\Controller;
class ImcloudController extends Controller {
	
	public function meter(){

		$url = $_SERVER["REQUEST_URI"];
		if(strpos($url,'info')>0){
			//dump($_POST);
			$ret['status']='ok';
			$ret['i_channels']='1,2,3,4';
			$ret['v_channels']='1';
			$label = json_encode($ret);
	    echo $label;
		}
		if(strpos($url,'data')>0){
			dump($_POST);
			$ret['request']='none';
			$label = json_encode($ret);
	    echo $label;
		}
		exit;
	}

	public function testjson()
	{
			$ret['cmd']="master";
			$ret['time']="2014-07-02 22:05:00";
			
			$interval[]=4;
			$interval[]=0;
			$interval[]=1;
			$ret['interval']=$interval;
			
			$ret['freq']=1;
			$ret['log']=1;
			$ret['rate_flag']=1;//ÌøÆµ¿ª¹Ø
			$ret['sens']=100;
			
			$station['flag']=1;
			$station['new']="000300001";
			$station['freq']=1;
			$ret['station']=$station;
			
			$url['flag']=1;
			$url['url']="iot.xunrun.com.cn";
			$ret['url']=$url;
			
			$step['count']=5;
			for($i=0;$i<5;$i++){
				$dev['sn']=300000+$i;
				$dev['flag']=0;
				$row[]=$dev;
			}
			$step['data']=$row;
			$ret['step']=$step;
			
			$recover['count']=40;
			for($i=0;$i<40;$i++){
				$stop_list[]=300000+$i;
			}
			$recover['dev']=$stop_list;
			$ret['recover']=$recover;
			
			
			$label = json_encode($ret);
	    echo $label;
	    exit;
	}
	
	public function testjsondecode(){
		$json=http("http://iot.xunrun.com.cn/pg/djtest/testjson");
		$ret= json_decode($json,true);
		dump($ret['step']['data']);
		dump($ret['recover']['dev']);
		exit;
	}
	
  public function testcode(){
			$sn=$_GET['sn'];
      $dateArr = array();
      $temp1Arr = array();
      $temp2Arr = array();
      $temp3Arr = array();
			for($i=0;$i<12;$i++){
				$time=strtotime(date('Y-m-d',time()))-3600*16;
				$timestr=date('H:i',$time+3600*$i);
				$data=mt_rand(100,850);
				$temp=mt_rand(34,36);
				$temp2=mt_rand(4,5);
				$sub=mt_rand(10,99)/100;
				$sub2=mt_rand(10,99)/100;
				$temp=$temp+$sub;
				$temp2=$temp-$temp2-$sub2;
				array_push($dateArr,$timestr);
				array_push($temp1Arr,$data);
				array_push($temp2Arr,$temp);
				array_push($temp3Arr,$temp2);
			}
			//dump($temp3Arr);
			$this->assign('temp3Arr',json_encode(array_reverse($temp3Arr)));
			$this->assign('temp2Arr',json_encode(array_reverse($temp2Arr)));
			$this->assign('temp1Arr',json_encode(array_reverse($temp1Arr)));
			$this->assign('dateArr',json_encode(array_reverse($dateArr)));
			$this->assign('sn',$sn);
			$this->display();
  }
  
  public function dailyworklist(){
    	$sn=$_GET['sn'];
    	$this->assign('sn',$sn);
			$this->display();
  }
  
  public function appupdate(){
  	$post = file_get_contents('php://input');
    $sid = $_GET['sid'];
    $sn_footer = (int)$sid & 0x1fff;
    $sn_header = (int)$sid >> 13;
    $logbase = "lora_log/logjson/";
    {
        $logdir = $logbase;
        if (!file_exists($logdir)) {
            mkdir($logdir);
        }
        $logdir = $logdir . $sn_header . '/';
        if (!file_exists($logdir)) {
            mkdir($logdir);
        }
        $logdir = $logdir . $sn_footer . '/';
        if (!file_exists($logdir)) {
            mkdir($logdir);
        }
        $logdir = $logdir . date('Y-m-d', time()) . '/';
        if (!file_exists($logdir)) {
            mkdir($logdir);
        }

        $filename = date("Ymd_His_") . mt_rand(10, 99) . ".log"; //ÐÂÍ¼Æ¬Ãû³Æ
        $newFilePath = $logdir . $filename;//Í¼Æ¬´æÈëÂ·¾¶
        $newFile = fopen($newFilePath, "w"); //´ò¿ªÎÄ¼þ×¼±¸Ð´Èë
        fwrite($newFile, $post);
        fclose($newFile); //¹Ø±ÕÎÄ¼þ
    }
		$parm= json_decode($post,true);
	  $app_ver=(int)$parm['ver'];
	  $sn=$parm['sn'];
	  $appfile=M('appfiles')->where('ver>'.$app_ver)->order('ver desc')->find();
	  if($appfile){
  	  $ver=$appfile['ver']; 
			$ret['cmd']='update';
			$ret['ver']=$ver;
			$ret['sn']=$sn;
			$token=$appfile['md5']; 
			$ret['crc']=$token;
			$ret['url']="http://iot.xunrun.com.cn/".$appfile['path'];
			$ota['ota']=$ret;
			$label = json_encode($ota);
	    echo $label;
	  }
  }

  public function testapp(){
  	  
  	  $app_ver=(int)$parm['ver'];
  	  $sn=$parm['sn'];
  	  $appfile=M('appfiles')->where('ver>'.$app_ver)->order('ver desc')->find();
  	  if($appfile){
	  	  $ver=$appfile['ver']; 
				$ret['cmd']='update';
				$ret['ver']=$ver;
				$ret['sn']=$sn;
				$token=$appfile['md5']; 
				$ret['crc']=$token;
				$ota['ota']=$ret;
				$ota['url']="http://iot.xunrun.com.cn/".$appfile['path'];
				$label = json_encode($ota);
		    echo $label;
  	  }

	    exit;
  }
}