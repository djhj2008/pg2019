<?php
namespace Home\Controller;
use Tools\HomeController;  
use Think\Controller;
class LongdxController extends HomeController {
    public function getstatesync(){
    	$token=(int)ldx_decode($_GET['token']);
    	$addr=ldx_decode($_GET['addr']);
    	$now=time();
    	//dump($token);
    	//dump($now);
    	//dump($addr);
    	if(!$token||$token< $now-60*5||$token>$now){
    		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000201));
    		echo json_encode($jarr);
    		exit;
    	}
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
    	
  		$jarr=array('ret'=>array('ret_message'=>'success','status_code'=>10000100,'data'=>$data));
  		echo json_encode($jarr);
  		exit;
    }
    
    public function gettempnow(){
    	$token=(int)ldx_decode($_GET['token']);
    	$addr=ldx_decode($_GET['addr']);
    	$now=time();
    	//dump($token);
    	//dump($now);
    	//dump($addr);
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
      $psn=(int)substr($sn,0,5);
      $devid=(int)substr($sn,5,4);
			//dump($devid);
			//dump($psn);
			//dump($rid);
			$dev = M('device')->field('psn,devid,psnid,rid')->where(array('rid'=>$rid))->find();
			//dump($dev);
			if(empty($dev)){
    		$jarr=array('ret'=>array('ret_message'=>'sn error','status_code'=>10000301));
    		echo json_encode($jarr);
    		exit;
			}
			
			$now = time();
			$time =date('Y-m-d',$now);
			$start_time = strtotime($time)-86400;
			$end_time = strtotime($time)+86400;	
			
			$acclist=M('access')->field('temp1,temp2,time')->where(array('devid'=>550,'psn'=>7))->where('time >= '.$start_time.' and time <= '.$end_time)
													        ->group('time')
													        ->limit(0,48)
													        ->select();
			//dump($acclist);
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
      if(empty($sn)){
      	$sn=$_GET['phone'];
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
    		$jarr=array('ret'=>array('ret_message'=>'send ','status_code'=>10000301));
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
    	$token=ldx_encode($now);
    	dump($now);
    	echo $token;
    	dump(ldx_encode('ldx'));
      exit;
    }
}