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
    		  $rid=(int)$sn;
    			$dev=M('device')->where(array('rid'=>$rid,'flag'=>1))->order('time desc')->find();
    			if($dev){
						$cow['sn']=$sn;
						if($dev['cow_state']==4){
							$cow['survival']=3;
						}else{
							$cow['survival']=1;
						}
						if($dev['cow_state']==5){
							$cow['health']=3;
						}else{
							$cow['health']=1;
						}
		    		$data[]=$cow;
    			}else{
		    		$jarr=array('ret'=>array('ret_message'=>'dev not find','status_code'=>10000301));
		    		echo json_encode($jarr);
		    		exit;
    			}
    	}else{
					$devnone=M('device')->where('psnid>=30 and psnid<=39')->where(array('cow_state'=>4))->select();
					$devnlow=M('device')->where('psnid>=30 and psnid<=39')->where(array('cow_state'=>5))->select();
					
					foreach($devnone as $dev){
						$cow['sn']=str_pad($dev['rid'],9,'0',STR_PAD_LEFT);
						$cow['survival']=3;
						$cow['health']=1;
						$data[]=$cow;
						$sn_none[]=$cow['sn'];
					}
					foreach($devnlow as $dev){
						$cow['sn']=str_pad($dev['rid'],9,'0',STR_PAD_LEFT);
						$cow['survival']=1;
						$cow['health']=3;
						$data[]=$cow;
						$sn_low[]=$cow['sn'];
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
    		exit;
    	}
      $sn=$_POST['sn'];
      if(empty($sn)){
      	$sn=$_GET['sn'];
      }
      $rid=(int)$sn;
      $sn=str_pad($sn,9,'0',STR_PAD_LEFT);
			$dev = M('device')->field('psn,devid,psnid,rid,avg_temp')->where(array('rid'=>$rid,'flag'=>1))->order('time desc')->find();
			$psn=$dev['psn'];
			$devid=$dev['devid'];
			
			$psnfind = M('psn')->where(array('id'=>$psn))->find();
			if(empty($psnfind)){
				echo "PSN NULL.";
				exit;
			}
			$btemp=35.5;//$psnfind['base_temp'];
			$hlevl1=$psnfind['htemplev1'];
			$hlevl2=$psnfind['htemplev2'];
			$llevl1=$psnfind['ltemplev1'];
			$llevl2=$psnfind['ltemplev2'];
			$temp_value=$psnfind['check_value'];
			//($temp_value);
			
			//dump($dev);
    	$avg=(float)$dev['avg_temp'];
    	//dump($avg);
			if(empty($dev)){
    		$jarr=array('ret'=>array('ret_message'=>'sn error','status_code'=>10000301));
    		echo json_encode($jarr);
    		exit;
			}
			if($dev['cow_state']==4){
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
													        ->order('time asc')
													        ->select();
												        
			if(empty($acclist)){
	    	for($i=30;$i<40;$i++){
	    		$mydb='access1301_'.$i;
	    		$acclist=M($mydb)->where(array('psn'=>$psn,'devid'=>$devid))->where('time >= '.$start_time.' and time <= '.$end_time)
	    														->group('time')
													        ->limit(0,48)
													        ->order('time asc')
													        ->select();
					if($acclist){
						break;
					}
	    	}
			}
			
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
								if($ntemp>32){
									$ntemp=$ntemp;
								}else{
									$ntemp=$vt;
								}
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

      $array = json_decode($post,TRUE);
    	$token=(int)ldx_decode($array['token']);
    	$addr=ldx_decode($array['addr']);
    	$now=time();

			//dump($token);
			//dump($addr);

    	if(!$token||$token< $now-60*5||$token>$now){
    		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000201));
    		echo json_encode($jarr);
    		exit;
    	}
			//exit;
			$type= $array['type'];
			$cmd= $array['cmd'];
			
			//dump($type);
			//dump($cmd);

			if($type==1){
				$tmp='14860115';
			}else if($type==2){
				$tmp='14854123';
			}else if($type==11){
				$tmp='14867046';
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
				if($type==11){
					send163msgtmp($phone,$smsmsg,$tmp);
				}
				//send163msg($phone,$smsmsg);
			}
      $jarr=array('ret'=>array('ret_message'=>'sucess','status_code'=>10000100,'msg_type'=>$type));
	  	echo json_encode($jarr);
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