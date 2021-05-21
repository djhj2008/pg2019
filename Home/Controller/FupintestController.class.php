<?php
namespace Home\Controller;
use Think\Controller;
class FupintestController extends Controller {

  public function getdataall(){
		$post = file_get_contents('php://input');
		$ret= json_decode($post,true);
		//dump($ret);
		$time=(int)$ret['time'];
		$token=$ret['token'];
		$cmd=$ret['cmd'];
		$now=time();
		$sn=$ret['sn'];
		
		//dump($now-$time);
		
		if($now-$time>5*60||$time>$now+5*60){
    		$jarr=array('ret'=>array('ret_message'=>'timestamp error','status_code'=>10000101));
    		echo json_encode($jarr);
    		exit;
		}
		$data=$sn.':'.$time;
		$hmac=sha256($data);
		//dump($hmac);
		//dump($token);
		if($token!=$hmac){
    		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000102));
    		echo json_encode($jarr);
    		exit;
		}
    
    $fym='2640423';
    $rid=(int)$sn;
    //dump($rid);
    if($rid < 300030){
    	$rid=$fym.str_pad($sn,8,'0',STR_PAD_LEFT);
    }
    //dump($rid);
    //dump($rfid);
		$dev = M('device')->field('psn,devid,psnid,rid,avg_temp,psn_now,cow_state,dev_state')->where(array('rid'=>$rid))->where('flag!=2')->order('time desc')->find();
						
		//dump($dev);
		$psn=$dev['psn'];
		$devid=$dev['devid'];
		$psn_now=$dev['psn_now'];
		$hw_step = ((int)$dev['dev_state'])&0x01;

		$psnfind = M('psn')->where(array('id'=>$psn))->find();
		if(empty($psnfind)){
			echo "PSN NULL.";
			exit;
		}
		
		$memcache = new \Memcache;
		$mem_ret=$memcache->connect('localhost', 11211);
	
		$get_result=false;
		
		if($mem_ret===true){
			$key_sn=$rid;
			//$get_result = $memcache->get($key_sn);
		}
		
		if($get_result===false){
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

			$now = time();
			$time =date('Y-m-d',$now);
			$start_time = strtotime($time)-86400*6;
			
			$delay=3600;
	  	$cur_time = $now - strtotime($time);;
	  	$cur_time = (int)($cur_time/$delay)*$delay;
	  	$first_time = $start_time;
	  	
	  	$data_count = $cur_time/$delay;
	  	$data_count = $data_count+(86400/$delay)*6;
			
			//dump($data_count);
			$acclist=array();
			for($i=0;$i< $data_count;$i++){
					$acclist[$i]['step']=rand(0,4000);
					$acclist[$i]['rumi']=rand(0,50);
					$acclist[$i]['cur_time']=date('Y-m-d H:i:s',$first_time+$i*$delay);
			}

			$jarr=array('ret'=>array('ret_message'=>'success','status_code'=>10000100,'sn'=>$sn,'data'=>$acclist));
			$ret=json_encode($jarr);
			if($mem_ret===true){
				$key_sn=$rid;
				$now=time();
				//var_dump($now);
				$time_out=$now%3600;
				$expire_time=3600-$time_out-1;
				//var_dump($expire_time);
				$get_result=$memcache->set($key_sn, $ret, false, $expire_time);
				//var_dump($time_out);
				$memcache->close();
			}
			echo $ret;
			exit;
		}else{
			echo 'memcache.';
			echo $get_result;
			$memcache->close();
			exit;
		}
  }


  public function getsteptempruminow(){
		$post = file_get_contents('php://input');
		$ret= json_decode($post,true);

		$time=(int)$ret['time'];
		$token=$ret['token'];
		$cmd=$ret['cmd'];
		$now=time();
		$sn=$ret['sn'];
		
		if($now-$time>5*60||$time>$now+5*60){
    		$jarr=array('ret'=>array('ret_message'=>'timestamp error','status_code'=>10000101));
    		echo json_encode($jarr);
    		//exit;
		}
		$data=$sn.':'.$time;
		$hmac=sha256($data);
		//dump($hmac);
		//dump($token);
		if($token!=$hmac){
    		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000102));
    		echo json_encode($jarr);
    		//exit;
		}
    
    $fym='2640423';
    $rid=(int)$sn;
    //dump($rid);
    if($rid < 300030){
    	$rid=$fym.str_pad($sn,8,'0',STR_PAD_LEFT);
    }
    //dump($rid);
    //dump($rfid);
		$dev = M('device')->field('psn,devid,psnid,rid,avg_temp,psn_now,cow_state,dev_state')->where(array('rid'=>$rid))->where('flag!=2')->order('time desc')->find();
						
		//dump($dev);
		$psn=$dev['psn'];
		$devid=$dev['devid'];
		$psn_now=$dev['psn_now'];
		$hw_step = ((int)$dev['dev_state'])&0x01;

		$psnfind = M('psn')->where(array('id'=>$psn))->find();
		if(empty($psnfind)){
			echo "PSN NULL.";
			exit;
		}
		
		$memcache = new \Memcache;
		$mem_ret=$memcache->connect('localhost', 11211);
	
		$get_result=false;
		
		if($mem_ret===true){
			$key_sn=$cmd.$rid;
			//$get_result = $memcache->get($key_sn);
		}
		
		if($get_result===false){
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

			$now = time();
			$time =date('Y-m-d',$now);
			$start_time = strtotime($time)-86400*6;
			
			$delay=3600;
	  	$cur_time = $now - strtotime($time);;
	  	$cur_time = (int)($cur_time/$delay)*$delay;
	  	$first_time = $start_time;
	  	
	  	$data_count = $cur_time/$delay;
	  	$data_count = $data_count+(86400/$delay)*6;
			
			//dump($data_count);
			$acclist=array();
			for($i=0;$i< $data_count;$i++){
					if($cmd=='getstep'){
						$acclist[$i]['step']=rand(0,4000);
						$acclist[$i]['offset']=rand(-500,500);
						$acclist[$i]['offset_hours']=rand(-100,1000);
					}
					if($cmd=='getrumi'){
						$acclist[$i]['rumi']=rand(0,50);
						$acclist[$i]['offset']=rand(-20,20);
						$acclist[$i]['offset_hours']=rand(-10,30);
					}
					if($cmd=='gettemp'){
						$temp=rand(25,42);
						$acclist[$i]['temp1']=$temp.'.'.rand(10,99);
						$acclist[$i]['temp2']=$temp.'.'.rand(10,99);
					}
					$acclist[$i]['cur_time']=date('Y-m-d H:i:s',$first_time+$i*$delay);
			}

			$jarr=array('ret'=>array('ret_message'=>'success','status_code'=>10000100,'sn'=>$sn,'delay'=>1,'data'=>$acclist));
			
			$ret=json_encode($jarr);
			if($mem_ret===true){
				$now=time();
				//var_dump($now);
				$time_out=$now%3600;
				$expire_time=3600-$time_out-1;
				//var_dump($expire_time);
				$get_result=$memcache->set($key_sn, $ret, false, $expire_time);
				//var_dump($time_out);
				$memcache->close();
			}
			echo $ret;
			exit;
		}else{
			echo 'memcache.';
			echo $get_result;
			$memcache->close();
			exit;
		}
		
  }
  
  
  public function getmiruqi(){
		$post = file_get_contents('php://input');
		$ret= json_decode($post,true);
		
		$time=(int)$ret['time'];
		$token=$ret['token'];
		$start_time=$ret['start_time'];
		$end_time=$ret['end_time'];
		$now=time();
		$sn=$ret['sn'];
		
		if($now-$time>5*60||$time>$now+5*60){
    		$jarr=array('ret'=>array('ret_message'=>'timestamp error','status_code'=>10000101));
    		echo json_encode($jarr);
    		exit;
		}
		$data=$sn.':'.$time;
		$hmac=sha256($data);
		//dump($hmac);
		//dump($token);
		if($token!=$hmac){
    		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000102));
    		echo json_encode($jarr);
    		exit;
		}
    
    $fym='2640423';
    $rid=(int)$sn;
    //dump($rid);
    if($rid < 300030){
    	$rid=$fym.str_pad($sn,8,'0',STR_PAD_LEFT);
    }
    //dump($rid);
    //dump($rfid);
		$dev = M('device')->field('psn,devid,psnid,rid,avg_temp,psn_now,cow_state,dev_state')->where(array('rid'=>$rid))->where('flag!=2')->order('time desc')->find();
						
		//dump($dev);
		$psn=$dev['psn'];
		$devid=$dev['devid'];
		$psn_now=$dev['psn_now'];
		$hw_step = ((int)$dev['dev_state'])&0x01;

		$psnfind = M('psn')->where(array('id'=>$psn))->find();
		if(empty($psnfind)){
			echo "PSN NULL.";
			exit;
		}
		
		$memcache = new \Memcache;
		$mem_ret=$memcache->connect('localhost', 11211);
	
		$get_result=false;
		
		if($mem_ret===true){
			$key_sn='getmiruqi'.$rid;
			//$get_result = $memcache->get($key_sn);
		}
		
		if($get_result===false){
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

			$now = time();
			$time =date('Y-m-d',$now);

	  	$data_count = ($end_time-$start_time)/86400+1;
			
			//dump($data_count);
			$acclist=array();
			for($i=0;$i< $data_count;$i++){
					$acclist[$i]['rumi']=rand(0,50)*24;
					$acclist[$i]['step']=rand(0,500)*24;
					$acclist[$i]['cur_time']=date('Y-m-d H:i:s',$start_time+$i*86400);
			}

			$jarr=array('ret'=>array('ret_message'=>'success','status_code'=>10000100,'sn'=>$sn,'data'=>$acclist));
			$ret=json_encode($jarr);
			if($mem_ret===true){
				$now=time();
				//var_dump($now);
				$time_out=$now%3600;
				$expire_time=3600-$time_out-1;
				//var_dump($expire_time);
				$get_result=$memcache->set($key_sn, $ret, false, $expire_time);
				//var_dump($time_out);
				$memcache->close();
			}
			echo $ret;
			exit;
		}else{
			echo 'memcache.';
			echo $get_result;
			$memcache->close();
			exit;
		}
		
  }
  
	function hmactest(){
		$data=$_GET['data'];
		$hmac=sha256($data);
		echo $hmac;
		exit;
	}	

	function test(){
		$sn='00058304';
		$now=time();
		$data=$sn.':'.$now;
		$hmac=sha256($data);
		$json['cmd']='getdataall';
		$json['time']=$now;
		$json['token']=$hmac;
		$json['addr']='ldx';
		$json['sn']=$sn;
		$json=json_encode($json);
		dump($json);
		$ret=http('http://engine.mjiangtech.cn/pg/fupintest/getdataall',$json,'POST');
		dump($ret);
		exit;
	} 
}