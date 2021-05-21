<?php
namespace Home\Controller;
use Think\Controller;
class DjtestController extends Controller {
	
function makeCurlFile(string $file)
{
    /**
     * .xls mime为 application/vnd.ms-excel
     * .xlsx mime为 application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
     * 可参考 https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Complete_list_of_MIME_types
     * 
     *  注意：也可以使用 finfo类动态获取，但需要装fileinfo扩展
     *  demo:
        $result = new finfo();
        if (is_resource($result) === true) {
            return $result->file($filename, FILEINFO_MIME_TYPE);
        }
        return false;
     */
    $mime = "image/jpeg";
    $info = pathinfo($file);
    $name = $info['basename'];
    $output = curl_file_create($file, $mime, $name);
    return $output;
}
    
function httpfile($url, $data,$file, $method='POST'){
    $curl = curl_init(); // 启动一个CURL会话  
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址  
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查  
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在  
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器  
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转  
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer  
    if($method=='POST'){  
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求  
        if ($file != ''){
        	$CurlFile = $this->makeCurlFile($file);
        	$filedata = array('upfile' => $CurlFile,'degree'=>$data['degree'],'filename'=>$data['filename'],'meter-id'=>$data['meter-id'],'meter-value'=>$data['meter-value']);
        	//dump($filedata);
        	curl_setopt($curl, CURLOPT_POSTFIELDS, $filedata);
        }  
    }
    curl_setopt($curl, CURLOPT_TIMEOUT, 60); // 设置超时限制防止死循环  
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回  
    $tmpInfo = curl_exec($curl); // 执行操作  
    //dump(curl_errno($curl));
    curl_close($curl); // 关闭CURL会话  
    return $tmpInfo; // 返回数据  
}
	
  public function index(){
  	//dump($_FILES);
  	//dump($_POST);
  	exit;
  }

  function testfile(){
  	$root = $_SERVER['DOCUMENT_ROOT'];
  	$data['degree']='0';
  	$data['filename']='normalup/110000109/20200910_164756_25.jpg';
  	$data['meter-id']='110000109';
  	$data['meter-value']='00000';
  	$file=$root.'/'.$data['filename'];
  	$url='http://iot.xidima.com:6080/upload?';
  	//$url='http://iot.xunrun.com.cn/pg/djtest/index';
  	$ret = $this->httpfile($url,$data,$file);
  	if(strpos($ret,'x') !== false||strpos($ret,'y') !== false){
  		echo 'error.';
  	}
  	dump($ret);
  }
  
	public function scancows_avg(){
		ini_set("memory_limit","1024M");
		$psnid=$_GET['psnid'];

		$max_count=12;
		$env_check=0.4;
		//for($psnid=30;$psnid<40;$psnid++)
		if($psnid)
		{
			$bdevinfo = M('bdevice')->where(array('psnid'=>$psnid))->find();
			$psn=$bdevinfo['psn'];
			$delay_str= $bdevinfo['uptime'];
			$count= $bdevinfo['count'];
			echo "PSN:";
			dump($psn);
			
			$delay = substr($delay_str,0, 2);
			$delay = (int)$delay;

			$delay = 3600*$delay;
			$delay_sub = $delay/$count;

    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	//var_dump($start_time);
    	$month_time = $start_time-86400*30;
    	$week_time = $start_time-86400*2;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;
    	//var_dump($cur_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	//$first_time = $cur_time-$delay+$start_time;
    	//$last_time = $cur_time-$delay+$start_time-($max_count-1)*3600;
    	$first_time = $start_time;
    	$last_time = $start_time-($max_count-1)*3600;
    	
			dump(date('Y-m-d H:i:s',$first_time));
			dump(date('Y-m-d H:i:s',$last_time));
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->where('avg_temp=0 or avg_temp>36.5 or avg_temp<35')->order('id asc')->select();

    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];	
    	}
			$wheredev['devid']=array('in',$devidlist);
		
			dump($wheredev);
		
    	$mydb='access_'.$psn;
    	$accSelect1=M($mydb)->where(array('psn'=>$psn))->where('time<='.$first_time.' and time>='.$last_time)->where($wheredev)->field('devid,temp1,temp2,time')->order('time desc')->select();

			foreach($accSelect1 as $acc){
				$devid=$acc['devid'];
				$cdev[$devid][]=$acc;
			}

			echo "START SCAN...";
			dump(count($accSelect1));
			foreach($devlist as $dev){
				$devid=$dev['devid'];
				$avg = $dev['avg_temp'];

				echo 'devid:';
				dump($devid);

				$acc_size=0;
				$acc_low_size=0;
				unset($acc_list);
				foreach($cdev[$devid] as $acc){
					if($acc['devid']==$devid){
						for($ai=0;$ai<$max_count;$ai++){
							if($acc['time']==$first_time-$ai*3600){
								$acc_list[$ai]=$acc;
								break;
							}
						}
					}
				}
				$acc_size=count($acc_list);
				//dump($cdev[$devid]);

				$sum=0;
				$cur_count=0;
				for($i=0;$i< $acc_size;$i++){
					$acc=$acc_list[$i];
					$temp1=$acc['temp1'];
					$temp2=$acc['temp2'];
					$temp3=$acc['env_temp'];
					$time=date('Y-m-d H:s:i',$acc['time']);

					$a=array($temp1,$temp2);
					$t=max($a);
					$vt=(float)$t;
					if($vt>32){
						$sum+=$vt;
						$cur_count++;
					}

					$accss[$i]['vtemp']=$vt;
					//var_dump($acc);
				}
				//dump($devid);
				$avg= round($sum/$cur_count,2);
				//dump($avg);
				if($avg< 30){
					//dump('devid:'.$devid.' avg:'.$avg);
					//$avg=0;
				}else{
					dump('devid:'.$devid.' avg:'.$avg.' count:'.$cur_count);
			  	$devSave=M('device')->where(array('psn'=>$psn,'devid'=>$devid))->save(array('avg_temp'=>$avg));
				}

			}
			
		}
		//dump(count($cows));
		exit;
	}
	
	public function scancows_avg_next(){
		ini_set("memory_limit","1024M");
		$psnid=$_GET['psnid'];

		$max_count=24;
		$env_check=0.4;
		//for($psnid=30;$psnid<40;$psnid++)
		if($psnid)
		{
			$bdevinfo = M('bdevice')->where(array('psnid'=>$psnid))->find();
			$psn=$bdevinfo['psn'];
			$delay_str= $bdevinfo['uptime'];
			$count= $bdevinfo['count'];
			echo "PSN:";
			dump($psn);
			
			$delay = substr($delay_str,0, 2);
			$delay = (int)$delay;

			$delay = 3600*$delay;
			$delay_sub = $delay/$count;

    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	//var_dump($start_time);
    	$month_time = $start_time-86400*30;
    	$week_time = $start_time-86400*2;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;
    	//var_dump($cur_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	//$first_time = $cur_time-$delay+$start_time;
    	//$last_time = $cur_time-$delay+$start_time-($max_count-1)*3600;
    	$first_time = $start_time;
    	$last_time = $start_time-($max_count-1)*3600;
    	
			dump(date('Y-m-d H:i:s',$first_time));
			dump(date('Y-m-d H:i:s',$last_time));
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->where('avg_temp=0 or avg_temp>36.5 or avg_temp<35')->order('id asc')->select();

    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
    	}
			$wheredev['devid']=array('in',$devidlist);
		
			//dump($wheredev);
		
			for($i=30;$i<40;$i++){
    		$mydb='access1301_'.$i;
    		$acc1301list1[$i]=M($mydb)->where(array('psn'=>$psn))->where('time<='.$first_time.' and time>='.$last_time)->where($wheredev)->field('devid,temp1,temp2,time')->order('time desc')->select();
    	}

			for($i=30;$i<40;$i++){
				foreach($acc1301list1[$i] as $acc){
					$devid=$acc['devid'];
					$cdev[$devid][]=$acc;
				}
    	}
    	
			echo "START SCAN...";
			//dump($cdev);
			foreach($devlist as $dev){
				$devid=$dev['devid'];
				$avg = $dev['avg_temp'];

				echo 'devid:';
				dump($devid);
				$acc_size=0;
				$acc_low_size=0;
				unset($acc_list);
				foreach($cdev[$devid] as $acc){
					if($acc['devid']==$devid){
						for($ai=0;$ai<$max_count;$ai++){
							if($acc['time']==$first_time-$ai*3600){
								$acc_list[$ai]=$acc;
								break;
							}
						}
					}
				}
				$acc_size=count($acc_list);
	
				$sum=0;
				$cur_count=0;
				for($i=0;$i< $acc_size;$i++){
					$acc=$acc_list[$i];
					$temp1=$acc['temp1'];
					$temp2=$acc['temp2'];
					$temp3=$acc['env_temp'];
					$time=date('Y-m-d H:s:i',$acc['time']);

					$a=array($temp1,$temp2);
					$t=max($a);
					$vt=(float)$t;
					if($vt>32){
						$sum+=$vt;
						$cur_count++;
					}

					$accss[$i]['vtemp']=$vt;
					//dump($acc);
				}
				//dump($devid);
				$avg= round($sum/$cur_count,2);
				//dump($avg);
				if($avg< 30){
					//dump('devid:'.$devid.' avg:'.$avg);
					//$avg=0;
				}else{
					dump('devid:'.$devid.' avg:'.$avg.' count:'.$cur_count);
			  	$devSave=M('device')->where(array('psn'=>$psn,'devid'=>$devid))->save(array('avg_temp'=>$avg));
				}

			}
			
		}
		//dump(count($cows));
		exit;
	}
	
	public function getlasttime(){

		$cows=M('cows')->where(array('survival_state'=>2))->order('sn_code asc')->select();
	
		foreach($cows as $key=>$cow){
			$sn=$cow['sn_code'];
			$sn=str_pad($sn,9,'0',STR_PAD_LEFT);
      $psn=(int)substr($sn,0,5);
      $devid=(int)substr($sn,5,4);
    	$mydb='access_'.$psn;
    	$curtime=0;
    	$curtemp=0;
    	$accSelect1=M($mydb)->where(array('psn'=>$psn,'devid'=>$devid))->field('temp1,temp2,time')->order('time desc')->find();
			if($accSelect1){
				$curtime=$accSelect1['time'];
				$curtemp=$accSelect1['temp1'];
			}
			
			for($i=30;$i<40;$i++){
    		$mydb1301='access1301_'.$i;
    		$acc1301list1=M($mydb1301)->where(array('psn'=>$psn,'devid'=>$devid))->field('temp1,temp2,time')->order('time desc')->find();
				if($curtime<$acc1301list1['time']){
					$curtime=$acc1301list1['time'];
					$curtemp=$acc1301list1['temp1'];
				}
    	}
    	$cows[$key]['last_time']=date('Y-m-d H:i:s',$curtime);
    	$cows[$key]['temp']=$curtemp;
		}
		dump($cows);
		exit;
		//dump($data);
	}
	
	public function getlastspn(){
		ini_set("memory_limit","1024M");
		$psnid=$_GET['psnid'];

		$low_temp=25;
		$check_count=8;
		//for($psnid=30;$psnid<40;$psnid++)
		if($psnid)
		{
			$bdevinfo = M('bdevice')->where(array('psnid'=>$psnid))->find();
			$psn=$bdevinfo['psn'];
			$delay_str= $bdevinfo['uptime'];
			$count= $bdevinfo['count'];
			echo "PSN:";
			dump($psn);
			
			$delay = substr($delay_str,0, 2);
			$delay = (int)$delay;

			$delay = 3600*$delay;
			$delay_sub = $delay/$count;

    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	//var_dump($start_time);
    	$month_time = $start_time-86400*30;
    	$week_time = $start_time-86400*2;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;
    	//var_dump($cur_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time;
    	$pre_time = $cur_time-$delay+$start_time-$delay;
    	$pre2_time = $cur_time-$delay+$start_time-$delay*2;
    	$pre3_time = $cur_time-$delay+$start_time-$delay*3;
			$last_time = $cur_time-$delay+$start_time-($check_count-1)*3600;
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->order('id asc')->select();
    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
    	}
    	//dump($devlist);
    	$wheredev['devid']=array('in',$devidlist);

    	$mydb='access_'.$psn;
    	$accSelect1=M($mydb)->where(array('psn'=>$psn))->where('time<='.$first_time.' and time>='.$last_time)->where($wheredev)->field('devid,temp1,temp2,time,psnid,sign')->order('time desc')->select();
			
			for($i=30;$i<40;$i++){
    		$mydb='access1301_'.$i;
    		$acc1301list1[$i]=M($mydb)->where(array('psn'=>$psn))->where('time<='.$first_time.' and time>='.$last_time)->where($wheredev)->field('devid,temp1,temp2,time,psnid,sign')->order('time desc')->select();
    	}
    	
			foreach($accSelect1 as $acc){
				$devid=$acc['devid'];
				$cdev[$devid][]=$acc;
			}
			for($i=30;$i<40;$i++){
				foreach($acc1301list1[$i] as $acc){
					$devid=$acc['devid'];
					$cdev[$devid][]=$acc;
				}
    	}

			$mode=M('device');
			echo "START SCAN...";
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				//$psnid = $dev['psnid'];
				//$rid = $dev['rid'];
				//dump($devid);
				$acc_size=0;
				$acc_low_size=0;
				unset($acc_list);
				unset($acc_low_list);
				$acc_list = array();
				$acc_low_list = array();
				foreach($cdev[$devid] as $acc){
					if($acc['devid']==$devid){
						for($ai=0;$ai<$check_count;$ai++){
							if($acc['time']==$first_time-$ai*3600){
								$acc_list[$ai]=$acc;
								break;
							}
						}
						$acc_size=count($acc_list);
					}
				}
				$psn_flag=false;
				$psn_now=0;
				$sign=-200;
				unset($psnid_count);
				foreach($cdev[$devid] as $acc){
					if($acc['psnid']==$psnid){
						$psn_flag=true;
						break;
					}else{
						if($sign < $acc['sign']){
							$psn_now=$acc['psnid'];
							$sign=$acc['sign'];
						}
						$psnid_count[$acc['psnid']]=$psnid_count[$acc['psnid']]+1;
					}
				}
				
				if($psn_flag==false){
					if($psn_now>0){
						if($sign>-100){
							//dump($psn_now);
						}else{
							//dump($psnid_count);
							$psn_now = array_search(max($psnid_count), $psnid_count);
						}
						if($dev['psn_now']!=$psn_now){
							$ret = $mode->where(array('id'=>$dev['id']))->save(array('psn_now'=>$psn_now));
							echo 'PSN NOW:';
							dump($psn_now);
						}
					}
				}else{
						if($dev['psn_now']!=0){
							$ret = $mode->where(array('id'=>$dev['id']))->save(array('psn_now'=>0));
							echo 'PSN NOW:0';
						}
				}

				$low_count=0;
				foreach($acc_list as $key=>$acc){
					if($acc['temp1']< $low_temp&&$acc['temp2']< $low_temp){
						$low_count++;
					}else{
						break;
					}
				}
				if($low_count>0){
					//dump($acc_list);
				}

				if($acc_size>=6){
					if($low_count>=6)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}
				}else if($acc_size>=3&$acc_size<6){
					if($low_count>=3)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}					
				}else if($acc_size==2){
					if($low_count==2)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}					
				}else if($acc_size==2){
					if($low_count==2)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}					
				}else if($acc_size==2){
					if($low_count==2)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}					
				}else if($acc_size==1){
					if($low_count==1)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}
				}else if($acc_size==0){
					$dev_none[]=$devid;
				}
			}
			
			//$ret=$mode->where(array('psn'=>$psn))->save(array('cow_state'=>0));
			if($dev_pass){
				$wherenpass['devid']=array('in',$dev_pass);
				//$ret=$mode->where(array('psn'=>$psn))->where($wherenpass)->save(array('cow_state'=>2));
			}
			if($dev_none){
				$wherenone['devid']=array('in',$dev_none);
				//$ret=$mode->where(array('psn'=>$psn))->where($wherenone)->save(array('cow_state'=>4));
			}
			if($dev_low){
				$wherenlow['devid']=array('in',$dev_low);
				//$ret=$mode->where(array('psn'=>$psn))->where($wherenlow)->save(array('cow_state'=>5));
			}
			//if($dev_pass){
			//	$wherepass['devid']=array('in',$dev_pass);
			//	$ret=$mode->where(array('psn'=>$psn))->where($wherepass)->save(array('state'=>2));
			//}
			//echo 'pass:';
			//dump($dev_pass);
			//echo 'dev_lost:';
			//dump($dev_lost);
			echo 'none:';
			//dump($dev_none);
			echo 'low:';
			//dump($dev_low);
		}
		//dump(count($cows));
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
		$json=http("http://engine.mjiangtech.cn/pg/djtest/testjson");
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
				$ota['url']="http://engine.mjiangtech.cn/".$appfile['path'];
				$label = json_encode($ota);
		    echo $label;
  	  }

	    exit;
  }
  
  public function savelog($sn_header,$sn_footer,$logbase,$res_file,$post){
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

      $filename = $res_file.date("His_") . mt_rand(100, 999) . ".log"; //新图片名称
      $newFilePath = $logdir . $filename;//图片存入路径
      $newFile = fopen($newFilePath, "w"); //打开文件准备写入
      fwrite($newFile, $post);
      fclose($newFile); //关闭文件
  }
  
  public function uploadxyz(){
  	$version_interface='Djtest';
    $sid = $_GET['sid'];
    $sn_footer = (int)$sid & 0x1fff;
    $sn_header = (int)$sid >> 13;
  	$post = file_get_contents('php://input');
    $logbase="lora_json/".$version_interface."/";
    $res_file="uploadxyz_res";
    $req_file="uploadxyz_req";
    $log_flag=true;
    if($log_flag)
    {
			$this->savelog($sn_header,$sn_footer,$logbase,$res_file,$post);
    }
    
		$parm = json_decode($post,true);
		$count=$parm['count'];
		$stepxyz=$parm['stepxyz'];
		$psn = ((int)$parm['sn'])>>13;
		$bsnint = ((int)$parm['sn'])& 0x1fff;
		
  	$psninfo = D('psn')->where(array('sn'=>$psn))->find();
    if($psn){
    	$psnid = $psninfo['id'];
			$delay_up = $psninfo['delay_up']; 
			$retry_up = $psninfo['retry_up'];
    	$body['delay_up']=(int)$delay_up;
    	$body['retry_up']=(int)$retry_up;
    }else{
    	$body['ret']='fail';
    	$body['msg']='PSN NULL.';
			$label = json_encode($body);
	    echo $label;
    	exit;
    }
    
    $bdevinfo = M('bdevice')->where(array('id'=>$bsnint,'psnid'=>$psnid))->find();
     if($bdevinfo){
    	$uptime=$bdevinfo['uptime'];
    	$hour=(int)substr($uptime,0,2);
    	$min=(int)substr($uptime,2,2);
    	$ivl_count = (int)$bdevinfo['count'];
    	
			$body['log']=(int)$bdevinfo['log_flag'];
			$body['rate_flag']=(int)$bdevinfo['dump_rate'];
			$step['rate']=(int)$bdevinfo['step_rate'];
			$step['config']=(int)$bdevinfo['step_setup'];
			$step['sleeptime']=(int)$bdevinfo['step_sleeptime'];

			$step['rate2']=(int)$bdevinfo['step_rate2'];
			$step['config2']=(int)$bdevinfo['step_setup2'];
			$step['config3']=(int)$bdevinfo['step_setup3'];
			$step['sleeptime2']=(int)$bdevinfo['step_sleeptime2'];

			$bt_ota_flag = (int)$bdevinfo['bt_ota_flag'];
			$bt['state']=(int)$bdevinfo['bt_state'];
			if($bt_ota_flag){
				$bt_fw=M('appfiles')->where(array('type'=>'bt'))->order('ver desc')->find();
				if($bt_fw){
					$bt['fw']=$bt_fw['ver'];
					$token=$osfile['md5']; 
					$ota['crc']=$token;
					$ota['url']="http://".$osfile['url'].$osfile['path'];
				}
			}
			$body['bt']=$bt;
			
  		$ivl[0]=$hour;
  		$ivl[1]=$min;
  		$ivl[2]=$ivl_count;
  		$body['interval']=$ivl;
	
    	$ota_flag = (int)$bdevinfo['ota_flag'];
    	$os_ota_flag = (int)$bdevinfo['os_ota_flag'];
			$body['freq'] = (int)$bdevinfo['rate_id'];
    	if($bdevinfo['version']!=$app_ver){
    		$savebd['version']=$app_ver;
    	}
    	if($bdevinfo['slaver_stop']!=$slaver_stop){
    		$savebd['slaver_stop']=$slaver_stop;
    	}
    	if($bdevinfo['number']!=$ccid){
    		$savebd['number']=$ccid;
    	}
    	
    	if(!empty($savebd)){
    		$saveSql=M('bdevice')->where(array('id'=>$bsnint,'psnid'=>$psnid))->save($savebd);	
    	}

    	if($ota_flag){
			  $osfile=M('appfiles')->where(array('type'=>'os','ver'=>$os_ver))->order('time desc')->find();
			  if($osfile){
					$ota['cmd']='os';
					$ota['ver']=$osfile['ver']; 
					$ota['sn']=str_pad($psn,5,'0',STR_PAD_LEFT).str_pad($bsnint,4,'0',STR_PAD_LEFT);
					$token=$osfile['md5']; 
					$ota['crc']=$token;
					$ota['url']="http://".$osfile['url'].$osfile['path'];
					$body['ota']=$ota;
			  }else{
			  	if($app_ver< 8193){
				  	$appfile=M('appfiles')->where(array('type'=>'app'))->where('ver>'.$app_ver)->order('ver desc')->find();
			  	}else{
				  	$appfile=M('appfiles')->where(array('type'=>'btapp'))->where('ver>'.$app_ver)->order('ver desc')->find();
			  	}
				  if($appfile){
						$ota['cmd']='app';
						$ota['ver']=$appfile['ver']; 
						$ota['sn']=str_pad($psn,5,'0',STR_PAD_LEFT).str_pad($bsnint,4,'0',STR_PAD_LEFT);
						$token=$appfile['md5']; 
						$ota['crc']=$token;
						$ota['url']="http://".$appfile['url'].$appfile['path'];
						$body['ota']=$ota;
				  }
			  }
    	}

    	$url_flag = (int)$bdevinfo['url_flag'];
			$url_url = $bdevinfo['url'];
			$change_flag= (int)$bdevinfo['change_flag'];
			$new_bsn=$bdevinfo['new_bsn'];
			$url['flag']=$url_flag;
			$station['flag']=$change_flag;
			if($url_flag==1){
				$url['url']=$url_url;
				$body['url']=$url;
			}
			if($change_flag==1){
				$station['new']=$new_bsn;
				$ch_psnint=(int)substr($new_bsn,0,5);
				$ch_bsnint=(int)substr($new_bsn,5,4); 
				$ch_bdevinfo=D('bdevice')->where(array('id'=>$ch_bsnint,'psn'=>$ch_psnint))->find();
				if($ch_bdevinfo)
				{
					$body['freq'] = $ch_bdevinfo['rate_id'];
					$body['station']=$station;
				}
			}
    }else{
    	$body['ret']='fail';
    	$body['msg']='PSN ID NULL.';
			$label = json_encode($body);
	    echo $label;
	    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
    	exit;
    }		
		

		$value_len=2;
		if(count($stepxyz)!=$count){

		}
		//dump($parm);
		//"sn":65538,"serial":1,"item":48,"data":
		foreach($stepxyz as $step){
			$dev_psn = ((int)$step['sn'])>>13;
			$dev_id = ((int)$step['sn'])& 0x1fff;
			$acc['sn']=$dev_psn*10000+$dev_id;
			$acc['serial']=$step['serial'];
			$item=(int)$step['item'];
			$data=$step['data'];
			for($i=0;$i< $item;$i++){
				$value=substr($data,$i*3*$value_len,$value_len*3);
				$x=hexdec(substr($value,0,2));
				$y=hexdec(substr($value,2,2));
				$z=hexdec(substr($value,4,2));
				$acc['x']=$this->getbyte($x);
				$acc['y']=$this->getbyte($y);
				$acc['z']=$this->getbyte($z);
				$acc_list[]=$acc;
				//dump($acc);
			}
		}
		//dump($acc_list);
		$als = array_chunk($acc_list, 3000, true);
		foreach($als as $al){
			unset($tl);
			foreach($al as $a){
				$tl[]=$a;
			}
			$user = M('stepxyz');
			$ret =$user->addAll($tl);
		}
		$body['cmd']='stepxyz';
		$body['ret']='success';
		$body['msg']='SUCCESS';
		$body['time']=date('Y-m-d H:i:s', time());
		$label=json_encode($body);
		echo $label;
    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
		exit;
  }
  
  public function getbyte($x){
  	if(($x&0x80)>>7==1){
  		$v=$x&0x7f;
  		$v=128-$v;
  		$ret=0-$v;
  	}else{
  		$ret=$x;
  	}
  	return $ret;
  }
  
  public function steplist(){
  	$sn=$_GET['sn'];
  	$count=$_GET['count'];
		if($count==NULL){
			$mx=100;
		}else{
			$mx=(int)$count;
		}

  	if($mx==0){
  		$devmsg=M('stepxyz')->where(array('sn'=>$sn))->order('id desc')->select();
  	}else{
  		$devmsg=M('stepxyz')->where(array('sn'=>$sn))->order('id desc')->limit(0,$mx)->select();
  	}

		$this->assign('devmsg',$devmsg);
		$this->display();
  	
  }
  
}