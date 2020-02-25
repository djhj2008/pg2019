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
			
			$dev = M('device')->field('psn,devid,psnid,rid,avg_temp')->where(array('rid'=>$rid))->find();
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