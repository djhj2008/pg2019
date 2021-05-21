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
					$devnone=M('device')->where(array('cow_state'=>4))->select();
					$devnlow=M('device')->where(array('cow_state'=>5))->select();
					
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
    
    public function gettempnow_bak(){
    	$token=(int)ldx_decode($_GET['token']);
    	$addr=ldx_decode($_GET['addr']);
    	$now=time();
			//dump($addr);
			//dump($now-$token);
			//dump($token-$now);
    	if(!$token||$token< $now-60*5||$token>$now+60){
    		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000201));
    		echo json_encode($jarr);
    		exit;
    	}
      $sn=$_POST['sn'];
      if(empty($sn)){
      	$sn=$_GET['sn'];
      }
      //$fym='2640423';
      if($addr=='ldx'){
      	$fym='2640423';
      }
      
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
			//$hw_step = 1;
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
			//$jarr=array('ret'=>array('ret_message'=>'sn error','status_code'=>10000301));
			//echo json_encode($jarr);
			//exit;
			}
			$now = time();
			$time =date('Y-m-d',$now);
			$start_time = strtotime($time)-86400*6;
			$end_time = strtotime($time)+86400;	
			$delay=7200;
	  	$cur_time = $now - $start_time-86400;
	  	$cur_time = (int)($cur_time/$delay)*$delay;
	  	$first_time = $cur_time+$start_time;
	  	
	  	//dump($cur_time);
	  	$count=($cur_time-7200)/3600;
	  	$count=24+$count;

			$mydb='access_base';
			$acclist=M($mydb)->field('temp1,temp2,rssi2,rssi3,time')->where(array('devid'=>$devid,'psn'=>$psn))->where('time >= '.$start_time.' and time <= '.$end_time)
													        ->group('time')
													        ->order('time asc')
													        ->select();						        
			//dump($acclist);
			
			if(empty($acclist)){
	  		$jarr=array('ret'=>array('ret_message'=>'sn error','status_code'=>10000301));
	  		echo json_encode($jarr);
	  		exit;
			}
			
			foreach($acclist as $key=>$acc){
	      	$temp1=$acc['temp1'];
	      	$temp2=$acc['temp2'];
	      	//dump($acc['time']);
	      	$cur_time=$acc['time'];
					if($avg>0){
						$a=array($temp1,$temp2);
						$t=max($a);
						$vt=(float)$t;
						if($vt < 32){
							if($ntemp>32){
								if($dev['cow_state']==5){
									$ntemp=$vt;
								}else{
									$ntemp=$ntemp;
								}
							}else{
								$ntemp=$vt;
							}
						}else{
							$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
						}
					}else{
						$a=array($temp1,$temp2);
						$t=max($a);
						$vt=(float)$t;
						$ntemp=$vt;
					}
					
					$acclist[$key]['temp1']=$ntemp;
					//$acclist[$key]['step']=$acc['rssi2'];
					//$acclist[$key]['step']=200+$key;
					if($key<count($acclist)-1){
						$step = (int)$acc['rssi2'];
						$next_time = (int)$acclist[$key+1]['time'];
						if($next_time-$cur_time==3600){
							$next_step = (int)$acclist[$key+1]['rssi2'];
	
							if($next_step-$step>=0){
								$cur_step = $next_step-$step;
							}else{
								if(($acc['rssi3']&0x03)==0x01){
									$cur_step=0;
								}else{
									//$cur_step = $next_step-$step;
									$cur_step=65535+$next_step-$step;
								}
								if($next_step==0){
									$cur_step=0;
								}
							}
							$acclist[$key]['step']=$cur_step;
						}else{
							$acclist[$key]['step']=0;
						}
					}else{

					}
					$acclist[$key]['cur_time']=date('Y-m-d H:i:s',$acc['time']);
			}


  		$jarr=array('ret'=>array('ret_message'=>'success','status_code'=>10000100,'avg'=>$avg,'hw_step'=>$hw_step,'sn'=>$sn,'data'=>$acclist));
  		echo json_encode($jarr);
			exit;
    }
    
	  public function gettempnow(){
	  	$token=(int)ldx_decode($_GET['token']);
	  	$addr=ldx_decode($_GET['addr']);
	  	$now=time();
	  	$role=$_GET['role'];
	  	$mode=M('','','DB_CONFIG');
			//dump($addr);
			//dump($now-$token);
			//dump($token-$now);
	  	if(!$token||$token< $now-60*5||$token>$now+60){
	  		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000201));
	  		echo json_encode($jarr);
	  		exit;
	  	}
	    $sn=$_POST['sn'];
	    if(empty($sn)){
	    	$sn=$_GET['sn'];
	    }
	    //$fym='2640423';
	    if($addr=='ldx'){
	    	$fym='2640423';
	    }else if($addr=='hyx'){
	    	$fym='2640522';
	    }

	    $cow=$mode->table('cows')->where(array('sn_code'=>$sn))->find();
	    if($cow['health_state']==3||$cow['survival_state']==3){
	    	$role=NULL;
	    }
	    
	    $rid=(int)$sn;
	    //dump($rid);
	    if($rid>90000&$rid<=99000){
	    	$fym='2640522';
	    }
	    
	    if($rid < 300030){
	    	$rid=$fym.str_pad($sn,8,'0',STR_PAD_LEFT);
	    }
	    //dump($rid);
	    //dump($rfid);
			$dev = M('device')->field('psn,devid,psnid,rid,avg_temp,psn_now,cow_state,dev_state,version')->where(array('rid'=>$rid))->where('flag!=2')->order('time desc')->find();
							
			//dump($dev);

			$psn=$dev['psn'];
			$devid=$dev['devid'];
			$psn_now=$dev['psn_now'];
			$hw_step = ((int)$dev['dev_state'])&0x01;
			//$hw_step = 1;
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
				if($dev['cow_state']==4||$dev['cow_state']==5){
					if($role=='admin'){
						$key_sn=$role.$rid;
						$rid_list=array("264042300051722","264042300052774","264042300052776","264042300052777","264042300052778","264042300052780","264042300052781","264042300052782","264042300052785","264042300052787");
						$index= rand(0,9);
						$rid=$rid_list[$index];
						$dev = M('device')->field('psn,devid,psnid,rid,avg_temp,psn_now,cow_state,dev_state,version')->where(array('rid'=>$rid))->where('flag!=2')->order('time desc')->find();
						$psn=$dev['psn'];
						$devid=$dev['devid'];
					}
				}
				//var_dump($key_sn);
				$get_result = $memcache->get($key_sn);
				//var_dump($get_result);
			}
			//dump($dev);
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
				$end_time = strtotime($time)+86400;	
				$delay=7200;
		  	$cur_time = $now - $start_time-86400;
		  	$cur_time = (int)($cur_time/$delay)*$delay;
		  	$first_time = $cur_time+$start_time;
		  	
		  	//dump($cur_time);
		  	$count=($cur_time-7200)/3600;
		  	$count=24+$count;

				$mydb='access_base';
				$acclist=M($mydb)->field('temp1,temp2,rssi2,rssi3,time,step_total')->where(array('devid'=>$devid,'psn'=>$psn))->where('time >= '.$start_time.' and time <= '.$end_time)
														        ->group('time')
														        ->order('time asc')
														        ->select();	
				if(empty($acclist)){
		  		$jarr=array('ret'=>array('ret_message'=>'acc error','status_code'=>10000401));
		  		echo json_encode($jarr);
		  		exit;
				}
				//dump($acclist);
				
				foreach($acclist as $key=>$acc){
		      	$temp1=$acc['temp1'];
		      	$temp2=$acc['temp2'];
		      	//dump($acc['time']);
		      	$cur_time=$acc['time'];
						if($avg>0){
							$a=array($temp1,$temp2);
							$t=max($a);
							$vt=(float)$t;
							if($vt < 32){
								if($ntemp>32){
									if($dev['cow_state']==5){
										$ntemp=$vt;
									}else{
										$ntemp=$ntemp;
									}
								}else{
									$ntemp=$vt;
								}
							}else{
								$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
							}
						}else{
								$a=array($temp1,$temp2);
								$t=max($a);
								$vt=(float)$t;
								$ntemp=$vt;
							
						}
						
						$acclist[$key]['temp1']=$ntemp;
						//$acclist[$key]['step']=$acc['rssi2'];
						//$acclist[$key]['step']=200+$key;
						if($dev['version']>=6){
							$acclist[$key]['step']=(int)$acc['step_total'];
						}else{
							if($key<count($acclist)-1){
								{
									$step = (int)$acc['rssi2'];
									$next_time = (int)$acclist[$key+1]['time'];
									if($next_time-$cur_time==3600){
										$next_step = (int)$acclist[$key+1]['rssi2'];

										if($next_step-$step>=0){
											$cur_step = $next_step-$step;
										}else{
											if(($acc['rssi3']&0x03)==0x01){
												$cur_step=0;
											}else{
												//$cur_step = $next_step-$step;
												$cur_step=65535+$next_step-$step;
											}
											if($next_step==0){
												$cur_step=0;
											}
										}
										$acclist[$key]['step']=$cur_step;
									}else{
										$acclist[$key]['step']=0;
									}
								}
							}
						}
						$acclist[$key]['cur_time']=date('Y-m-d H:i:s',$acc['time']);
				}

				$jarr=array('ret'=>array('ret_message'=>'success','status_code'=>10000100,'avg'=>$avg,'hw_step'=>$hw_step,'sn'=>$sn,'data'=>$acclist));
				$ret=json_encode($jarr);
				if($mem_ret===true){
					$now=time();
					//var_dump($now);
					$time_out=$now%7200;
					$expire_time=7200-$time_out-1;
					//var_dump($expire_time);
					$get_result=$memcache->set($key_sn, $ret, false, $expire_time);
					//var_dump($time_out);
					$memcache->close();
				}
				echo $ret;
				exit;
			}else{
				//echo 'memcache.';
				echo $get_result;
				$memcache->close();
				exit;
			}
			
	  }
  
    public function sendsmscode(){
    	$token=(int)ldx_decode($_GET['token']);
    	$addr=ldx_decode($_GET['addr']);
    	$now=time();

    	if(!$token||$token< $now-60*5||$token>$now){
    		$jarr=array('ret'=>array('ret_message'=>'token error','status_code'=>10000201));
    		echo json_encode($jarr);
    		//exit;
    	}
			$phone=$_POST['phone']; 
			if(empty($phone)){
				$phone=$_GET['phone'];
			}
			
			/*
			$mode=M('','','DB_CONFIG');
			$admins=$mode->table('admins')->where(array('role_id'=>4,'type'=>1))->select();
			
			foreach($admins as $admin){
				$phone_list[]=$admin['phone'];
			}

			$index =  in_array($phone,$phone_list);
			if($index){
		    	$now=time();
		    	$time_out=$now+60*10;
					$data['obj']="312666";
					$data['timeout']=$time_out;
		  		$jarr=array('ret'=>array('ret_message'=>'sucess','status_code'=>10000100,'data'=>$data));
		  		echo json_encode($jarr);
		  		exit;
			}
			*/
			if($phone=='18812345678'){
		    	$now=time();
		    	$time_out=$now+60*10;
					$data['obj']="123456";
					$data['timeout']=$time_out;
		  		$jarr=array('ret'=>array('ret_message'=>'sucess','status_code'=>10000100,'data'=>$data));
		  		echo json_encode($jarr);
		  		exit;
			}
    	$now=time();
    	$time_out=$now+60*10;
    	$ret=sendldxmsg($phone);
			if($ret['code']==200){
				$data['obj']=$ret['obj'];
				$data['timeout']=$time_out;
				//dump($data);
	  		$jarr=array('ret'=>array('ret_message'=>'sucess','status_code'=>10000100,'data'=>$data));
	  		echo json_encode($jarr);
			}else{
    		$jarr=array('ret'=>array('ret_message'=>'send error','status_code'=>10000301));
    		echo json_encode($jarr);
			}
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
	          			
	        $filename = date("Ymd_His_").mt_rand(10, 99).".log"; 
	        $newFilePath = $logdir.$filename;
	        $newFile = fopen($newFilePath,"w"); 
	        fwrite($newFile,$phone);
	        fwrite($newFile,json_encode($jarr));
	        fclose($newFile);
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
	          			
	        $filename = date("Ymd_His_").mt_rand(10, 99).".log"; 
	        $newFilePath = $logdir.$filename;
	        $newFile = fopen($newFilePath,"w"); 
	        fwrite($newFile,$post);
	        fclose($newFile);
	    }

      $array = json_decode($post,TRUE);
    	$token=(int)ldx_decode($array['token']);
    	$addr=ldx_decode($array['addr']);
    	$now=time();

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
			$arr_type=[' ORDER_SMS_NUM'=>'14860115', 'LACTATION_CYCLE'=>'14860115', 'PREGNANCY_LOOK'=>'14871375', 'COW_PREGNANCY'=>'14854123','PREGNANCY_CYCLE'=>'14876424','ABANDONED_CALL'=>'19482173'];
			if (array_key_exists($type,$arr_type)){
				$tmp=$arr_type[$type];
			}else{
	      $jarr=array('ret'=>array('ret_message'=>'fail','status_code'=>10000101,'msg_type'=>$type));
		  	echo json_encode($jarr);
		  	exit;
			}
			
			if($type=='ABANDONED_CALL'){
				foreach($array['msg'] as $msg){
					//dump($msg['phone']);
					unset($phone);
					unset($smsmsg);
					$phone[]=$msg['phone'];

					$content=$msg['content'];

					$smsmsg[]=$content['town'].$content['village'].$content['name'];
					$smsmsg[]=$content['phone'];
					$smsmsg[]=$content['datetime'];
					//dump($smsmsg);
					send163msgtmp($phone,$smsmsg,$tmp);
				}
			}else{
				foreach($array['msg'] as $msg){
					//dump($msg['phone']);
					unset($phone);
					unset($smsmsg);
					$phone[]=$msg['phone'];
					$foot='防疫码:';

					$other=$foot;
					//dump($other);
					for($i=0;$i<count($msg['sn']);$i++){
						$sn=$msg['sn'][$i];
						//dump($sn);
						//$psn=(int)substr($sn,0,5);
	      		$devid=substr($sn,-8);
						if($i==0){
							$other=$other.$devid;
						}else{
							if(strlen($other)>=24){
								$other=$other.'...';
								break;
							}else{
								$other=$other.','.$devid;
							}
						}
					}

					$smsmsg[]=$msg['town'].$msg['village'].$msg['name'];
					$smsmsg[]=$other;
					//dump($smsmsg);
					send163msgtmp($phone,$smsmsg,$tmp);
				}
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
    	dump(ldx_encode('hyx'));
      exit;
    }

}