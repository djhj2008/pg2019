<?php
namespace Home\Controller;
use Tools\HomeController;  
use Think\Controller;
class LongdxController extends HomeController {
    public function getstatesync(){
    	$token=(int)ldx_decode($_GET['token']);
    	$addr=ldx_decode($_GET['addr']);
    	$now=time();
      $sn=$_POST['sn'];
      if(empty($sn)){
      	$sn=$_GET['sn'];
      }
      
    	if(!$token||$token< $now-60*5||$token>$now){
    		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000201));
    		echo json_encode($jarr);
    		exit;
    	}
    	if(!empty($sn)){
					$data[0]['sn']=$sn;
	    		$data[0]['health']=3;
	    		$data[0]['survival']=2;
    	}else{
	    	for($i=0;$i<1;$i++){
	    		$data[$i]['sn']=200480+$i;
	    		$data[$i]['health']=3;
	    		$data[$i]['survival']=2;
	    	}
	    	
	    	for($i=8;$i<14;$i++){
	    		$data[$i]['sn']=200480+$i;
	    		$data[$i]['health']=2;
	    		$data[$i]['survival']=1;
	    	}
	    	
	    	for($i=14;$i<20;$i++){
	    		$data[$i]['sn']=200480+$i;
	    		$data[$i]['health']=1;
	    		$data[$i]['survival']=3;
	    	}
    	}
    	
    	
  		$jarr=array('ret'=>array('ret_message'=>'success','status_code'=>10000100,'data'=>$data));
  		echo json_encode($jarr);
  		exit;
    }
    
    public function gettempnow(){
    	$token=(int)ldx_decode($_GET['token']);
    	$addr=ldx_decode($_GET['addr']);
    	$now=time();
			//dump($token);
			//dump($now-$token);
    	if(!$token||$token< $now-60*5||$token>$now){
    		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000201));
    		echo json_encode($jarr);
    		//exit;
    	}
      $sn=$_POST['sn'];
      if(empty($sn)){
      	$sn=$_GET['sn'];
      }
      $rid=(int)$sn;
      $sn=str_pad($sn,9,'0',STR_PAD_LEFT);
      $psn=(int)substr($sn,0,5);
      $devid=(int)substr($sn,5,4);
			//dump($devid);
			//dump($psn);
			//dump($rid);
			$psnfind = M('psn')->where(array('id'=>$psn))->find();
			if(empty($psnfind)){
				echo "PSN NULL.";
				exit;
			}
			$btemp=38.75;//$psnfind['base_temp'];
			$hlevl1=$psnfind['htemplev1'];
			$hlevl2=$psnfind['htemplev2'];
			$llevl1=$psnfind['ltemplev1'];
			$llevl2=$psnfind['ltemplev2'];
			$temp_value=$psnfind['check_value'];
			//($temp_value);
			
			$dev = M('device')->field('psn,devid,psnid,rid,avg_temp')->where(array('rid'=>$rid))->order('time desc')->find();
			$psn=$dev['psn'];
			$devid=$dev['devid'];
			//dump($dev);
    	$avg=(float)$dev['avg_temp'];
    	//dump($avg);
			if(empty($dev)){
    		$jarr=array('ret'=>array('ret_message'=>'sn error','status_code'=>10000301));
    		echo json_encode($jarr);
    		exit;
			}
			
			$now = time();
			$time =date('Y-m-d',$now);
			$start_time = strtotime($time)-86400;
			$end_time = strtotime($time)+86400;	
			
			$mydb='access_'.$psn;
			$acclist=M($mydb)->field('temp1,temp2,time')->where(array('devid'=>$devid,'psn'=>$psn))->where('time >= '.$start_time.' and time <= '.$end_time)
													        ->group('time')
													        ->limit(0,48)
													        ->select();
			//dump($acclist);
			if($avg>0){
				for($i=0;$i<count($acclist);$i++){
	        		$temp1=$acclist[$i]['temp1'];
	        		$temp2=$acclist[$i]['temp2'];
	        		$temp3=$acclist[$i]['env_temp'];
							$a=array($temp1,$temp2);
							$t=max($a);
							$vt=(float)$t;
							if($vt < 32){
								$ntemp=$vt;
							}else{
								$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
							}
	        		//$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
	        		$acclist[$i]['temp1']=$ntemp;
				}
			}
  		$jarr=array('ret'=>array('ret_message'=>'success','status_code'=>10000100,'data'=>$acclist));
  		echo json_encode($jarr);
			exit;
    }
    
    public function sendsmscode(){
    	$token=(int)ldx_decode($_GET['token']);
    	$addr=ldx_decode($_GET['addr']);
    	$now=time();

    	if(!$token||$token< $now-60*5||$token>$now){
    		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000201));
    		echo json_encode($jarr);
    		exit;
    	}
      $phone=$_POST['phone']; 
      if(empty($phone)){
      	$phone=$_GET['phone'];
      }

    	$now=time();
    	$time_out=$now+60*10;
    	$ret=sendldxmsg($phone);
			if($ret['code']==200){
				$data['obj']=$ret['obj'];
				$data['timeout']=$time_out;
	  		$jarr=array('ret'=>array('ret_message'=>'sucess','status_code'=>10000100,'data'=>$data));
	  		echo json_encode($jarr);
			}else{
    		$jarr=array('ret'=>array('ret_message'=>'send error','status_code'=>10000301));
    		echo json_encode($jarr);
    		exit;
			}
  		exit;
    }

    public function sendsmsmsg(){
    	$token=(int)ldx_decode($_GET['token']);
    	$addr=ldx_decode($_GET['addr']);
    	$now=time();

    	if(!$token||$token< $now-60*5||$token>$now){
    		//$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000201));
    		//echo json_encode($jarr);
    		//exit;
    	}
			$post=file_get_contents('php://input');
	    $logbase="sms_backup/";
	    {
	        $logdir =$logbase;
	        if(!file_exists($logdir)){
	            mkdir($logdir);
	        }
	        $logdir = $logdir.date('Y-m-d',time()).'/';
	        if(!file_exists($logdir)){
	            mkdir($logdir);
	        }
	          			
	        $filename = date("Ymd_His_").mt_rand(10, 99).".bmp"; //新图片名称
	        $newFilePath = $logdir.$filename;//图片存入路径
	        $newFile = fopen($newFilePath,"w"); //打开文件准备写入
	        fwrite($newFile,$post);
	        fclose($newFile); //关闭文件
	           
	    }
    	
    	/*
    	$sn1[]='000310031';
    	
    	$sn2[]='000320031';
    	$sn2[]='000320032';
    	    	
    	$sn3[]='000330031';
    	$sn3[]='000330032';
    	$sn3[]='000330033';
    	$sn3[]='000330134';
    	$sn3[]='000331134';
    	$sn3[]='000332134';
    	$sn3[]='000333134';
    	$sn3[]='000334134';
    	$sn3[]='000335134';
    	
    	$phone1 = array('phone'=>'13311152676','name'=>iconv("GBK", "UTF-8", '测试1'),'sn'=>$sn1);
    	$phone2 = array('phone'=>'15010160170','name'=>iconv("GBK", "UTF-8", '测试2'),'sn'=>$sn2);
    	$phone3 = array('phone'=>'15010150766','name'=>iconv("GBK", "UTF-8", '测试3'),'sn'=>$sn3);
    
    	$msg[]=$phone1;
    	$msg[]=$phone2;
    	$msg[]=$phone3;
    	
    	$jarr = array('type'=>1,'msg'=>$msg);
    	$jarr=json_encode($jarr);
    	dump($jarr);
    	*/
    	
      $array = json_decode($post,TRUE);

			$type= $array['type'];
			$cmd= $array['cmd'];
			
			//dump($type);
			//dump($cmd);

			if($type==1){
				$tmp='14860115';
			}else if($type==2){
				$tmp='14854123';
			}else{
				$tmp='14860115';
			}
			
			foreach($array['msg'] as $msg){
				//dump($msg['phone']);
				unset($phone);
				unset($smsmsg);
				$phone[]=$msg['phone'];
				$other='SN:';
				for($i=0;$i<count($msg['sn']);$i++){
					$sn=$msg['sn'][$i];
					//dump($sn);
					//$psn=(int)substr($sn,0,5);
      		$devid=(int)substr($sn,5,4);
					if($i==0){
						$other=$other.$devid;
					}else{
						if(strlen($other)>=25){
							$other=$other.'...';
							break;
						}else{
							$other=$other.','.$devid;
						}
					}
				}
				$ohter=$other;
				$smsmsg[]=$msg['name'];
				$smsmsg[]=$ohter;
				//dump($smsmsg);
				send163msgtmp($phone,$smsmsg,$tmp);
				//send163msg($phone,$smsmsg);
			}
      $jarr=array('ret'=>array('ret_message'=>'sucess','status_code'=>10000100));
	  	echo json_encode($jarr);
	  	exit;
	  	
			if($ret['code']==200){
				$data['obj']=$ret['obj'];
	  		$jarr=array('ret'=>array('ret_message'=>'sucess','status_code'=>10000100,'data'=>$data));
	  		echo json_encode($jarr);
			}else{
    		$jarr=array('ret'=>array('ret_message'=>'send error','status_code'=>10000301));
    		echo json_encode($jarr);
    		exit;
			}
  		exit;
    }
       
    public function test_encode(){
    	$data=$_GET['data'];
    	$token=ldx_encode($data);
    	echo $token;
      exit;
    }
    
    public function test(){
    	$now=time();
    	$token=ldx_encode(time());
    	dump($now);
    	echo $token;
    	dump(ldx_encode('ldx'));
      exit;
    }
}