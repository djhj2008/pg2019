<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function rfidconnect(){
		//$psn=23;
		//$psn=23;
		//$devs=M('device')->where(array('psn'=>$psn))->select();
		//dump($devs);
		$psnid=$_GET['psnid'];
		$mydb='rfid'.$psnid;
		$rfids=M($mydb)->select();
		echo 'count:';
		dump(count($rfids));
		//exit;
		foreach($rfids as $rfid){
			$devid=(int)substr($rfid['sbi_sn'],-4);
			$psn=(int)substr($rfid['sbi_sn'],0,5);
			//dump($rfid['SBI_SN']);
			$dev = M('device')->where(array('psn'=>$psn,'devid'=>$devid))->find();

		if($dev){
					if(strlen($dev['rid'])!=15){
						dump(strlen($dev['rid']));
						dump($psn);
						dump($devid);
						$rid=substr($rfid['sbi_fym'],0,15);
						dump($rid);
						$ret=M('device')->where(array('psn'=>$psn,'devid'=>$devid))->save(array('rid'=>$rid));
					}
		}

		}
		//dump($rfids);
		
		
	}
	public function devscan(){
		$psn=$_GET['psn'];
		$devs=M('device')->where(array('psn'=>$psn))->order('devid desc')->select();
		
		foreach($devs as $dev){
					$devid=$dev['devid'];
					if(strlen($dev['rid'])!=15){
						//dump($dev['devid']);
						foreach($devs as $dev2){
							if($dev2['devid']==$devid&&$dev['id']!=$dev2['id']){
								if(strlen($dev2['rid'])==15){
									dump($dev['rid']);
									dump($dev2['rid']);
									dump($dev['id']);
									$ret=M('device')->where(array('id'=>$dev['id']))->delete();
									//dump($ret);
								}
								break;
							}
							
						}
					}
		}
	}
	
	public function adddev(){
		$psn=(int)$_GET['psn'];
		$devid=(int)$_GET['devid'];
		$count=(int)$_GET['count'];
		$devs=M('device')->where(array('psn'=>$psn))->order('devid desc')->select();
		
		for($i=$devid;$i< $devid+$count;$i++){
			$dev_find=false;
			$rfid = $psn*10000+$i;
			foreach($devs as $dev){
				if($dev['devid']==$i){
					$dev_find=true;
					break;
				}
			}
			if($dev_find===false){
					$rfdev=array(
					'psn'=>$psn,
					'psnid'=>$psn,
					'devid'=>$i,
					'rid'=>$rfid,
					'flag'=>1,
				);
				$rfid_list[]=$rfdev;
			}
		}
		if($rfid_list){
			dump($rfid_list);
			$user3=D('device');
			$user3->addAll($rfid_list);
		}else{
			echo "null";
		}
		exit;
	}	

	public function addfactory(){
		$psn=(int)$_GET['psn'];
		$devid=(int)$_GET['devid'];
		$count=(int)$_GET['count'];
		$productno=$_GET['productno'];
		$devs=M('factory')->where(array('psnid'=>$psn))->order('devid desc')->select();
		
		for($i=$devid;$i< $devid+$count;$i++){
			$dev_find=false;
			$rfid = $psn*10000+$i;
			foreach($devs as $dev){
				if($dev['devid']==$i){
					$dev_find=true;
					break;
				}
			}
			if($dev_find===false){
					$rfdev=array(
					'psnid'=>$psn,
					'devid'=>$i,
    			'productno'=>$productno,
					'state'=>1,
    			'fsn'=>"ABC",
					'time'=>time()
				);
				$rfid_list[]=$rfdev;
			}
		}
		if($rfid_list){
			dump($rfid_list);
			$user3=D('factory');
			$user3->addAll($rfid_list);
		}else{
			echo "null";
		}
		exit;
	}	
	
  public function masterV60(){
		$version_interface='V300';
  	$post = file_get_contents('php://input');
    $sid = $_GET['sid'];
    $sn_footer = (int)$sid & 0x1fff;
    $sn_header = (int)$sid >> 13;
    $logbase="lora_json/".$version_interface."/";
    $res_file="master_res";
    $err_file="master_err";
    $req_file="master_req";
    $log_flag=true;
		$ret['cmd']="master";
  	$ret['ret']='success';
  	$ret['msg']='SUCCESS.';
  	
    if($log_flag)
    {
			$this->savelog($sn_header,$sn_footer,$logbase,$res_file,$post);
    }
		$parm= json_decode($post,true);
		if($parm==false){
    	$ret['ret']='fail';
    	$ret['msg']='PSN NULL.';
			$label = json_encode($ret);
	    echo $label;
	    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
    	exit;
		}
		$btsn = $parm['tcc'];
		$psn = ((int)$parm['sn'])>>13;
		$bsnint = ((int)$parm['sn'])& 0x1fff;
		$app_ver = $parm['version'];
		$os_ver = $parm['os_version'];
		$count = $parm['count'];
		$small = $parm['small'];
		$bigdiff = $parm['bigdiff'];
		$bigtemp = $parm['bigtemp'];
		$interval = $parm['interval'];
		$slaver_stop = (int)$parm['no1301'];
		$ccid = $parm['ccid'];
				
 		$psninfo = D('psn')->where(array('sn'=>$psn))->find();
    if($psn){
    	$psnid = $psninfo['id'];
			$delay_up = $psninfo['delay_up']; 
			$retry_up = $psninfo['retry_up'];
    	$ret['delay_up']=(int)$delay_up;
    	$ret['retry_up']=(int)$retry_up;
    }else{
    	$ret['ret']='fail';
    	$ret['msg']='PSN NULL.';
			$label = json_encode($ret);
	    echo $label;
	    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
    	exit;
    }
    
    $bdevinfo = M('bdevice')->where(array('id'=>$bsnint,'psnid'=>$psnid))->find();
    
    if($bdevinfo){
    	$uptime=$bdevinfo['uptime'];
    	$hour=(int)substr($uptime,0,2);
    	$min=(int)substr($uptime,2,2);
    	$ivl_count = (int)$bdevinfo['count'];
    	
			$ret['log']=(int)$bdevinfo['log_flag'];
			$ret['rate_flag']=(int)$bdevinfo['dump_rate'];
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
			$ret['bt']=$bt;
			
  		$ivl[0]=$hour;
  		$ivl[1]=$min;
  		$ivl[2]=$ivl_count;
  		$ret['interval']=$ivl;
	
    	$ota_flag = (int)$bdevinfo['ota_flag'];
    	$os_ota_flag = (int)$bdevinfo['os_ota_flag'];
			$ret['freq'] = (int)$bdevinfo['rate_id'];
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
					$ret['ota']=$ota;
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
						$ret['ota']=$ota;
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
				$ret['url']=$url;
			}
			if($change_flag==1){
				$station['new']=$new_bsn;
				$ch_psnint=(int)substr($new_bsn,0,5);
				$ch_bsnint=(int)substr($new_bsn,5,4); 
				$ch_bdevinfo=D('bdevice')->where(array('id'=>$ch_bsnint,'psn'=>$ch_psnint))->find();
				if($ch_bdevinfo)
				{
					$ret['freq'] = $ch_bdevinfo['rate_id'];
					$ret['station']=$station;
				}
			}
    }else{
    	$ret['ret']='fail';
    	$ret['msg']='PSN ID NULL.';
			$label = json_encode($ret);
	    echo $label;
	    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
    	exit;
    }		
		
		$brssimax=0-(int)$parm['maxrssi'];
		$bigsync=$parm['bigsync'];
		foreach($bigsync as $key=>$v){
			$rssi['sn'.$key]=(int)$v['serial'];
			$sign=(int)$v['rssi'];
			if(($sign&0x80)==0x80){
					$rssi['rssi'.$key] = 0-($sign&0x7f);
			}else{
					$rssi['rssi'.$key] = $sign;
			}
		}
		$rssi['psnid']=$psnid;
		$rssi['bsn']=$bsnint;
		$rssi['rssi']=$brssimax;
		$rssi['delay']=$bigdiff;
		$rssi['temp']=$bigtemp;
		$rssi['time']=time();
		
		if($rssi){
			$saveRssi=D('brssi')->add($rssi);
		}

		if($count!=count($small)){
    	$ret['ret']='fail';
    	$ret['msg']='Count err.';
			$label = json_encode($ret);
	    echo $label;
	    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
    	exit;
		}
		
   	$re_devs =D('device')->where(array('psnid'=>$psnid,'re_flag'=>1))->order('devid asc')->limit(0,64)->select();
   	
   	$re_devs2 =D('device')->where(array('psnid'=>$psnid,'re_flag'=>2))->order('devid asc')->limit(0,64)->select();
   	
   	$cur_devs =D('device')->where(array('psnid'=>$psnid))->order('devid asc')->select();
   	
   	$change_devs = D('changeidlog')->where(array('psnid' => $psnid))->select();
   	
   	foreach($cur_devs as $cur_dev){
			$step_list[$cur_dev['devid']]['state']=(int)$cur_dev['dev_state'];
			$step_list[$cur_dev['devid']]['switch']=(int)$cur_dev['step_switch'];
		}
		   	
		foreach($small as $data){
			//dump($data);
			$rfdev_ret=$this->parsedata($data,$psn,$psnid,$bsnint,$interval,$app_ver);
			if($rfdev_ret['ret']=='success'){
				$dev_save = $rfdev_ret['rfdev'];
				$devid = (int)$dev_save['devid'];
				$dev_psn = (int)$dev_save['psn'];
				$battery = (int)$dev_save['battery'];
				$dev_state = (int)$dev_save['dev_state'];
				$cvs = (int)$dev_save['version'];
				$devsave_list[]=$dev_save;
				$devbuf[]=$devid;
				$rfid = $dev_psn*10000+$devid;
				
				$list1 = $rfdev_ret['list1'];
				
				$step_list[$dev_save['devid']]['state']=$dev_save['dev_state'];
				
				foreach($list1 as $acc_add){
					$accadd_list[]= $acc_add;
				}


	    	if($devid>=30&&$devid<2800)
	    	{
	    		$rfid_find=false;
		    	foreach($cur_devs as $cur_dev){
		    		if(($cur_dev['devid']==$devid)&&($cur_dev['psn']==$dev_psn)){
		    			$rfid_find=true;
		    			break;
		    		}
		    	}
		    	if($rfid_find==false){
		    		  $change_dev_find=false;
		    		  foreach($change_devs as $ch_dev){
		    		  	if($ch_dev['psnid']==$psnid&&
		    		  		$ch_dev['new_devid']==$devid){
		    		  			$change_dev_find=false;
		    		  			$rfid=$ch_dev['rfid'];
		    		  			if($ch_dev['flag']==2){
		    		  				$change_dev_find=true;
		    		  				$ret=M('changeidlog')->where(array('id'=>$ch_dev['id']))->save(array('flag'=>3));
		    		  			}
		    		  		}
		    		  }
		    		  foreach($rfid_list as $rfid_dev){
		    		  	if($rfid_dev['devid']==$devid&&$dev_psn==$rfid_dev['psn']){
		    		  		$change_dev_find=false;
		    		  		break;
		    		  	}
		    		  }
		    		  if($change_dev_find==false){
								$addrfdev=array(
									'psn'=>$dev_psn,
									'psnid'=>$psnid,
									'shed'=>1,
									'fold'=>1,
									'flag'=>0,
									'state'=>0,
									'battery'=>$battery,
						  	 	'dev_state'=>$dev_state,
						  	 	'version'=>$cvs,
									'rid'=>$rfid,
									'age'=>1,
									'devid'=>$devid,
								);
								$rfid_list[]=$addrfdev;
		    		  }else{
		    		  	//dump('NEVER.');
		    		  	//NEVER HAPPEN.
		    		  }
		    	}
		    	$devsave_list[]=$dev_save;
	    	}else{
	    		continue;
	    	}
			}
		
		}
		//dump($accadd_list);
		if($accadd_list){
	  	$mydb='access_v6';
	    $user=D($mydb);
			$user->addAll($accadd_list);
		}

/*    
    if($accadd_list2){
	    $user2=D('taccess');
			$user2->addAll($accadd_list2);
    }		
*/

		if($rfid_list){
			//$user3=D('device');
			//$user3->addAll($rfid_list);
		}
		
		foreach($cur_devs as $dev){
			$devid = $dev['devid'];
			$dev_psn =$dev['psn'];
			$battery= $dev['battery'];
			$dev_state= $dev['dev_state'];
			$version= $dev['version'];
			foreach($devsave_list as $devsave){
				unset($mysave);
				if($devid==$devsave['devid']&&$dev_psn==$devsave['psn']){
					if($battery!=$devsave['battery']){
						$mysave['battery']=$devsave['battery'];
					}
					if($dev_state!=$devsave['dev_state']){
						$mysave['dev_state']=$devsave['dev_state'];
					}
					if($version!=$devsave['version']){
						$mysave['version']=$devsave['version'];
					}
					if(!empty($mysave)){
						$dev1=M('device')->where(array('devid'=>$devid,'psn'=>$dev_psn))->save($mysave);
					}
				}
			}
		}

  	foreach($re_devs as $redev){
  			$devid_tmp=$redev['devid'];
  			foreach($devbuf as $devre){
  				if($devre==$devid_tmp){
						$devres[]=$devre;
						break;
  				}
  			}
  	}

  	foreach($re_devs2 as $redev){
  			$devid_tmp=$redev['devid'];
  			foreach($devbuf as $devre){
  				if($devre==$devid_tmp){
						$devres2[]=$devre;
						break;
  				}
  			}
  	}

		$devres_count=count($re_devs);
		foreach($re_devs as $re_dev){
				$devre_id = $redev['devid'];
				$devres_list[]=(int)$devre_id;
		}

		$recover['count'] = $devres_count;
		$recover['dev'] = $devres_list;
		$ret['recover'] = $recover;

		if(!empty($devres)){
			$whereredev['devid']=array('in',$devres);
			$dev1=D('device')->where($whereredev)->where(array('psn'=>$psn))->save(array(re_flag=>2));
		}

		if(!empty($devres2)){ 
			$whereredev2['devid']=array('in',$devres2);
			$dev1=D('device')->where($whereredev2)->where(array('psn'=>$psn))->save(array(re_flag=>3));
		}
		
		$stepres_count=0;
		foreach($step_list as $key=>$step_dev){
			if(($step_dev['state']&0x03)==0x03){
				if($step_dev['switch']==0){
					$stepres['sn']=(int)$key;
					$stepres['flag']=0;
					$stepres_list[]=$stepres;
					$stepres_count++;
				}
			}else if(($step_dev['state']&0x03)==0x01){
				if($step_list[$key]['switch']==1){
					$stepres['sn']=(int)$key;
					$stepres['flag']=1;
					$stepres_list[]=$stepres;
					$stepres_count++;
				}
			}
		}
			
		$step['count'] = $stepres_count;
		$step['data'] = $stepres_list;
		$ret['step']=$step;
		$ret['psn']=$psn;
		$ret['time']=date('Y-m-d H:i:s', time());

  	$ret['ret']='success';
  	$ret['msg']='SUCCESS.';
		$label = json_encode($ret);
    echo $label;
    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
  	exit;
  }
  
  public function parsedata($data,$psnid,$sid,$interval){
			$CSN_LEN  =4;//����������?3�濨�
			$SIGN_LEN =1;//D?o?
			$CVS_LEN =1;//client version
			$STATE_LEN  =1;//state
			$DELAY_LEN  =1;//delay
			$VAILD_LEN  =1;//��DD�꿦ʿ?��y
			
			$SENS_LEN  =1;//��DD�꿦ʿ?��y
			
			$VALUE_LEN = 10;//data?D????3�濨�
			$COUNT_VALUE = 4;
			
			
			$hour_delay = $interval[0];
			$min_delay	= $interval[1];
			$freq				= $interval[2];
		
	    $day_begin = strtotime(date('Y-m-d',time()));
	    $hour_time = 60*60;
	    $pre_time =5*60;
	    $hour_pre_time=15*60;
	    $now = time();
	    $today = strtotime(date('Y-m-d',$now));
	   	$now = $now-$today;
	   	
			$snstr   =substr($data, 0,$CSN_LEN*2);
    	$snint = hexdec($snstr)&0x1fff;;	//�䨮��?����????�!��?????
    	$dev_psn = hexdec($snstr) >> 13;
			$rfid = $dev_psn*10000+$snint;
			
    	if($dev_psn!=$psn)
    	{
    		$ret['ret']='fail';
    	}
    	
    	$signstr = substr($data,($CSN_LEN)*2,$SIGN_LEN*2);
    	$cvsstr  = substr($data,($CSN_LEN+$SIGN_LEN)*2,$CVS_LEN*2);
    	$stastr  = substr($data,($CSN_LEN+$SIGN_LEN+$CVS_LEN)*2,$STATE_LEN*2);
    	$destr   = substr($data,($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN)*2,$DELAY_LEN*2);

    	$sign= 0-hexdec($signstr);
    	$cvst = hexdec($cvsstr);
    	$cvs = (int)($cvst&0x07);
			$step_update =(int)($cvst&0x08);
    	$cindex = hexdec($cvsstr);
    	$cindex = ($cindex&0xf0)>>4;
    	$state =  hexdec($stastr);
    	$delay =  hexdec($destr);
    	$vaild = hexdec($vaildstr);
    	$devbuf[]=$snint;
    	
    	$stmp = 0x07;
    	$stmp2 = 0x80;
    	$stmp3 = 0x08;
    	if(($state & $stmp2) == $stmp2){
    		$battery=1;
    	}
    	else{
    		$battery=0;
    	}
    	$lcount = $state&0x70;
    	$lcount = ($lcount)>>4;
    	$type = $state&$stmp3;
    	$state=$state&$stmp;
			$step_update = $step_update|$state;
			
    	if($cvs>3){
				$sensstr 	 =  substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$SENS_LEN*2);
				$vaildstr  =  substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN)*2,$VAILD_LEN*2);
    		$tempstr	 =	substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1��?����????����?
				$sens=0-hexdec($sensstr);
    	}else{
				$sens=0;
				$vaildstr  = substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
    		$tempstr   = substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1��?����????����?
    	}
    	$vaild = hexdec($vaildstr);
    	if($type>0){
    		$type=1;
    	}
			
			$rfdev=array(
				'psn'=>$dev_psn,
				'devid'=>$snint,
				'battery'=>$battery,
	  	 	'dev_state'=>$state,
	  	 	'version'=>$cvs,
				'rid'=>$rfid,
			);
    	
	    if($min_delay == 0){
	      $real_time = ((int)(($now+$hour_pre_time)/($hour_delay*$hour_time))-1)*$hour_delay*$hour_time;
	      $real_time = $today+$real_time;
	      $interval = ($hour_delay/$freq)*$hour_time;
	    }else{
	    	$real_time = ((int)(($now+$pre_time)/($min_delay*60)-1))*$min_delay*60;
	    	$real_time = $today+$real_time;
	    	$interval = ($min_delay/$freq)*60;
	    }
	    
	    $start = $real_time-$interval*$freq;
	    $end = $real_time;
	    
			if($vaild>4){
				$vaild=4;
				return;
			}  	

    	for($j=0;$j < $vaild;$j++){
	    	$up_time = $real_time-$interval*$freq+$interval*($j+1)+$interval*($freq-$vaild);
		    $up_time = strtotime(date('Y-m-d H:i',$up_time).':00');
	    	if($cvs>3){
	    		 	$tempstr_tmp = substr($tempstr,0+$j*$VALUE_LEN,$VALUE_LEN);
		    		if($type==0){
					    $temp1str1 = substr($tempstr_tmp,3,1);
				  		$temp1str2 = substr($tempstr_tmp,0,1);
				    	$temp1str3 = substr($tempstr_tmp,1,1);
				    	$temp1int =base_convert($temp1str1,16,10);

				    	$temp2str1 = substr($tempstr_tmp,4,1);
				  		$temp2str2 = substr($tempstr_tmp,5,1);
				    	$temp2str3 = substr($tempstr_tmp,2,1);
				    	$temp2int =base_convert($temp2str1,16,10);
		    		
		    			$stepstr = substr($tempstr_tmp,-4);
		    			$stepint = (int)base_convert($stepstr,16,10);
		    			
					    if(($temp1int&0x08)==0x08){
				    		$temp1str1=$temp1int&0x07;
				    		if($temp1str1==0){
				    			$temp1 = '-'.$temp1str2.".".$temp1str3;
				    		}else{
				    			$temp1 =  '-'.$temp1str1.$temp1str2.".".$temp1str3;
				    		}
				    	}else{
					    		if($temp1str1==0){
					    			$temp1 = $temp1str2.".".$temp1str3;
					    		}else{
					    			$temp1 = $temp1str1.$temp1str2.".".$temp1str3;
					    		}
				    	}

						  //var_dump('temp1:'.$temp1);
						  if(($temp2int&0x08)==0x08){
						  	$temp2str1=$temp2int&0x07;
								if($temp2str1 == 0){
							    $temp2 = '-'.$temp2str2.".".$temp2str3;
						 	 	}else{
						 	 	 	$temp2 = '-'.$temp2str1.$temp2str2.".".$temp2str3;
						 	 	}
					 		}else{
					 			if($temp2str1 == 0){
							    $temp2 = $temp2str2.".".$temp2str3;
						 	 	}else{
						 	 	 	$temp2 = $temp2str1.$temp2str2.".".$temp2str3;
						 	 	}
					 			
					 		}
					 	
							$acc_add=array(
					  				'psn'=>$dev_psn,
					  				'psnid'=>$psnid,
							  		'devid'=>$snint,
							  		'temp1'=>$temp1,
							  		'temp2'=>$temp1,
							  		'env_temp'=>$temp2,
							  		'sign'=>$sign,
							  		'rssi1'=>$sens,
							  		'rssi2'=>$stepint,
							  		'rssi3'=>$step_update,
							  		'cindex'=>$cindex,
							  		'lcount'=>$lcount,
							  		'delay'=>$delay,
							  		'time' =>$up_time,
							  		'sid' =>$sid,
							  	);

							$accadd_list[]=$acc_add;
						}else{
							//nothing
						}
	    	}else{
		    		if($type==0){
							if($j==0){
						    $temp1str1 = substr($tempstr,3,1);
					  		$temp1str2 = substr($tempstr,0,1);
					    	$temp1str3 = substr($tempstr,1,1);
					    	$temp1int =base_convert($temp1str1,16,10);

					    	$temp2str1 = substr($tempstr,4,1);
					  		$temp2str2 = substr($tempstr,5,1);
					    	$temp2str3 = substr($tempstr,2,1);
					    	$temp2int =base_convert($temp2str1,16,10);
					    	
					    	$temp3str1 = substr($tempstr,9,1);
					  		$temp3str2 = substr($tempstr,6,1);
					    	$temp3str3 = substr($tempstr,7,1);
					    	$temp3int =base_convert($temp3str1,16,10);
				    	}else if($j==1){
				    		$temp1str1 = substr($tempstr,10,1);
					  		$temp1str2 = substr($tempstr,11,1);
					    	$temp1str3 = substr($tempstr,8,1);
					    	$temp1int =base_convert($temp1str1,16,10);
					    	
					    	$temp2str1 = substr($tempstr,15,1);
					  		$temp2str2 = substr($tempstr,12,1);
					    	$temp2str3 = substr($tempstr,13,1);
					    	$temp2int =base_convert($temp2str1,16,10);
					    	
					    	$temp3str1 = substr($tempstr,16,1);
					  		$temp3str2 = substr($tempstr,17,1);
					    	$temp3str3 = substr($tempstr,14,1);
					    	$temp3int =base_convert($temp3str1,16,10);
				    	}else if($j==2){
						    $temp1str1 = substr($tempstr,21,1);
					  		$temp1str2 = substr($tempstr,18,1);
					    	$temp1str3 = substr($tempstr,19,1);
					    	$temp1int =base_convert($temp1str1,16,10);
					    	
					    	$temp2str1 = substr($tempstr,22,1);
					  		$temp2str2 = substr($tempstr,23,1);
					    	$temp2str3 = substr($tempstr,20,1);
					    	$temp2int =base_convert($temp2str1,16,10);
					    	
					    	$temp3str1 = substr($tempstr,27,1);
					  		$temp3str2 = substr($tempstr,24,1);
					    	$temp3str3 = substr($tempstr,25,1);
					    	$temp3int =base_convert($temp3str1,16,10);			    	
				    	}else if($j==3){
				    		$temp1str1 = substr($tempstr,28,1);
					  		$temp1str2 = substr($tempstr,29,1);
					    	$temp1str3 = substr($tempstr,26,1);
					    	$temp1int =base_convert($temp1str1,16,10);

					    	$temp2str1 = substr($tempstr,33,1);
					  		$temp2str2 = substr($tempstr,30,1);
					    	$temp2str3 = substr($tempstr,31,1);
					    	$temp2int =base_convert($temp2str1,16,10);

					    	$temp3str1 = substr($tempstr,34,1);
					  		$temp3str2 = substr($tempstr,35,1);
					    	$temp3str3 = substr($tempstr,32,1);
					    	$temp3int =base_convert($temp3str1,16,10);					    	
				    	}
		    		
					    if(($temp1int&0x08)==0x08){
				    		$temp1str1=$temp1int&0x07;
				    		if($temp1str1==0){
				    			$temp1 = '-'.$temp1str2.".".$temp1str3;
				    		}else{
				    			$temp1 =  '-'.$temp1str1.$temp1str2.".".$temp1str3;
				    		}
				    	}else{
					    		if($temp1str1==0){
					    			$temp1 = $temp1str2.".".$temp1str3;
					    		}else{
					    			$temp1 = $temp1str1.$temp1str2.".".$temp1str3;
					    		}
				    	}

						  //var_dump('temp1:'.$temp1);
						  if(($temp2int&0x08)==0x08){
						  	$temp2str1=$temp2int&0x07;
								if($temp2str1 == 0){
							    $temp2 = '-'.$temp2str2.".".$temp2str3;
						 	 	}else{
						 	 	 	$temp2 = '-'.$temp2str1.$temp2str2.".".$temp2str3;
						 	 	}
					 		}else{
					 			if($temp2str1 == 0){
							    $temp2 = $temp2str2.".".$temp2str3;
						 	 	}else{
						 	 	 	$temp2 = $temp2str1.$temp2str2.".".$temp2str3;
						 	 	}
					 			
					 		}
					 	
					    //var_dump('temp2:'.$temp2);
					    if(($temp3int&0x08)==0x08){
					    	$temp3str1=$temp3int&0x07;
						    if($temp3str1 == 0){
							    $temp3 = '-'.$temp3str2.".".$temp3str3;
						 	 	}else{
						 	 	 	$temp3 = '-'.$temp3str1.$temp3str2.".".$temp3str3;
						 	 	}
					 		}else{
					 			if($temp3str1 == 0){
							    $temp3 = $temp3str2.".".$temp3str3;
						 	 	}else{
						 	 	 	$temp3 = $temp3str1.$temp3str2.".".$temp3str3;
						 	 	}
							}

							$acc_add=array(
					  				'psn'=>$dev_psn,
					  				'psnid'=>$psnid,
							  		'devid'=>$snint,
							  		'temp1'=>$temp1,
							  		'temp2'=>$temp2,
							  		'env_temp'=>$temp3,
							  		'sign'=>$sign,
							  		'rssi1'=>0,
							  		'rssi2'=>0,
							  		'rssi3'=>0,
							  		'cindex'=>$cindex,
							  		'lcount'=>$lcount,
							  		'delay'=>$delay,
							  		'time' =>$up_time,
							  		'sid' =>$sid,
							  	);

							$accadd_list[]=$acc_add;
						}else{
							if($j==0){
						    $temp1str1 = substr($tempstr,3,1);
					  		$temp1str2 = substr($tempstr,0,1);
					    	$temp1str3 = substr($tempstr,1,1);
					    	$temp1int =base_convert($temp1str1,16,10);

					    	$temp2str1 = substr($tempstr,4,1);
					  		$temp2str2 = substr($tempstr,5,1);
					    	$temp2str3 = substr($tempstr,2,1);
					    	
					    	$temp3str1 = substr($tempstr,9,1);
					  		$temp3str2 = substr($tempstr,6,1);
					    	$temp3str3 = substr($tempstr,7,1);
				    	}else if($j==1){
				    		$temp1str1 = substr($tempstr,10,1);
					  		$temp1str2 = substr($tempstr,11,1);
					    	$temp1str3 = substr($tempstr,8,1);
					    	$temp1int =base_convert($temp1str1,16,10);
					    	
					    	$temp2str1 = substr($tempstr,15,1);
					  		$temp2str2 = substr($tempstr,12,1);
					    	$temp2str3 = substr($tempstr,13,1);	
					    	
					    	$temp3str1 = substr($tempstr,16,1);
					  		$temp3str2 = substr($tempstr,17,1);
					    	$temp3str3 = substr($tempstr,14,1);	
				    	}else if($j==2){
						    $temp1str1 = substr($tempstr,21,1);
					  		$temp1str2 = substr($tempstr,18,1);
					    	$temp1str3 = substr($tempstr,19,1);
					    	$temp1int =base_convert($temp1str1,16,10);
					    	
					    	$temp2str1 = substr($tempstr,22,1);
					  		$temp2str2 = substr($tempstr,23,1);
					    	$temp2str3 = substr($tempstr,20,1);
					    	
					    	$temp3str1 = substr($tempstr,27,1);
					  		$temp3str2 = substr($tempstr,24,1);
					    	$temp3str3 = substr($tempstr,25,1);					    	
				    	}else if($j==3){
				    		$temp1str1 = substr($tempstr,28,1);
					  		$temp1str2 = substr($tempstr,29,1);
					    	$temp1str3 = substr($tempstr,26,1);
					    	$temp1int =base_convert($temp1str1,16,10);

					    	$temp2str1 = substr($tempstr,33,1);
					  		$temp2str2 = substr($tempstr,30,1);
					    	$temp2str3 = substr($tempstr,31,1);	

					    	$temp3str1 = substr($tempstr,34,1);
					  		$temp3str2 = substr($tempstr,35,1);
					    	$temp3str3 = substr($tempstr,32,1);						    	
				    	}

					    if(($temp1int&0x08)==0x08){
				    		$temp1str1=$temp1int&0x07;
				    		if($temp1str1==0){
				    			$temp1 = '-'.$temp1str2.".".$temp1str3;
				    		}else{
				    			$temp1 =  '-'.$temp1str1.$temp1str2.".".$temp1str3;
				    		}
				    	}else{
					    		if($temp1str1==0){
					    			$temp1 = $temp1str2.".".$temp1str3;
					    		}else{
					    			$temp1 = $temp1str1.$temp1str2.".".$temp1str3;
					    		}
				    	}

						  //var_dump('temp1:'.$temp1);
							if($temp2str1 == 0){
						    $temp2 = $temp2str2.".".$temp2str3;
					 	 	}else{
					 	 	 	$temp2 = $temp2str1.$temp2str2.".".$temp2str3;
					 	 	}
					    //var_dump('temp2:'.$temp2);
					    if($temp3str1 == 0){
						    $temp3 = $temp3str2.".".$temp3str3;
					 	 	}else{
					 	 	 	$temp3 = $temp3str1.$temp3str2.".".$temp3str3;
					 	 	}
		    
					  	$acc_add2=array(
					   	  'psn'=>$dev_psn,
					   	  'psnid'=>$psnid,
					  		'devid'=>$snint,
					  		'temp1'=>$temp1,
					  		'temp2'=>$temp2,
				  			'env_temp'=>$temp3,
				  			'sign'=>$sign,
					  		'cindex'=>$cindex,
					  		'lcount'=>$lcount,
					  		'delay'=>$delay,
					  		'time' =>$up_time,
					  		'sid' =>$sid,
					  	);

							$accadd_list2[]=$acc_add2;
						}
	    	}
			}
			
			$ret['list1']=$accadd_list;
			$ret['list2']=$accadd_list2;
			
			$ret['ret']='success';
			$ret['rfdev']=$rfdev;
			return	$ret;
  }
  
  public function slaverV10(){
  	$post = file_get_contents('php://input');
    $sid = $_GET['sid'];
    $sn_footer = (int)$sid & 0x1fff;
    $sn_header = (int)$sid >> 13;
    $logbase="lora_json/V10/";
    $res_file="slaver_res";
    $err_file="slaver_err";
    $req_file="slaver_req";
    $log_flag=true;
		$ret['cmd']="slaver";
  	$ret['ret']='success';
  	$ret['msg']='SUCCESS.';
  	
    if($log_flag)
    {
			$this->savelog($sn_header,$sn_footer,$logbase,$res_file,$post);
    }
		$parm= json_decode($post,true);
		if($parm==false){
    	$ret['ret']='fail';
    	$ret['msg']='PSN NULL.';
			$label = json_encode($ret);
	    echo $label;
	    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
    	exit;
		}
		
		$psn = ((int)$parm['sn'])>>13;
		$bsnint = ((int)$parm['sn'])& 0x1fff;
		$count = $parm['count'];
		$small = $parm['small'];
		$bigdiff = 0;
		$interval = $parm['interval'];

    $psnallinfo = D('psn')->select();
    //dump($psnallinfo);
    $dev_psnid_find = false;
    $psn_index = 0;

    foreach ($psnallinfo as $psninfo) {
        if ($psn == $psninfo['sn']) {
            $dev_psnid_find = true;
            $psnid = $psninfo['id'];
            break;
        }
    }

    if ($dev_psnid_find == false) {
    	$ret['ret']='fail';
    	$ret['msg']='PSN ID NULL.';
			$label = json_encode($ret);
			echo $label;
	    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
	    exit;
    } else {
      $psn_list[$psn_index]['psnid'] = $psnid;
      $psn_list[$psn_index]['psn'] = $psn;
    }
		
		$brssimax=0-(int)$parm['maxrssi'];
		$bigsync=$parm['bigsync'];
		foreach($bigsync as $key=>$v){
			$rssi['sn'.$key]=(int)$v['serial'];
			$sign=(int)$v['rssi'];
			if(($sign&0x80)==0x80){
					$rssi['rssi'.$key] = 0-($sign&0x7f);
			}else{
					$rssi['rssi'.$key] = $sign;
			}
			
		}
		$rssi['psnid']=$psnid;
		$rssi['bsn']=$bsnint;
		$rssi['rssi']=$brssimax;
		$rssi['delay']=$bigdiff;
		$rssi['temp']=$bigtemp;
		$rssi['station']=1301;
		$rssi['time']=time();
		
		if($rssi){
			//$saveRssi=D('brssi')->add($rssi);
		}

		if($count!=count($small)){
    	$ret['ret']='fail';
    	$ret['msg']='Count err.';
			$label = json_encode($ret);
	    echo $label;
	    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
    	exit;
		}
		
		$cur_devs =D('device')->where(array('psnid'=>$psnid))->order('devid asc')->select();
		
    $change_devs = D('changeidlog')->where(array('psnid' => $psnid))->select();
   			   	
		foreach($small as $data){
			//dump($data);
			$rfdev_ret=$this->parsedata($data,$psn,$psnid,$bsnint,$interval);
			if($rfdev_ret['ret']=='success'){
				$dev_save = $rfdev_ret['rfdev'];
				$devid = $dev_save['devid'];
				$dev_psn = $dev_save['psn'];
				$devsave_list[]=$dev_save;
				
				$list1 = $rfdev_ret['list1'];
				
				foreach($list1 as $acc_add){
					$accadd_list[]= $acc_add;
					$acc1301add_list[] = $acc_add;
				}
				
        if ($dev_psn != $psn) {
            $dev_psnid_find = false;
            foreach ($psnallinfo as $psninfo) {
                if ($dev_psn == $psninfo['sn']) {
                    $dev_psnid_find = true;
                    $dev_psnid = $psninfo['id'];
                    break;
                }
            }
            if ($dev_psnid_find == false) {
                $psn_err_log = $psn_err_log . $btsn . ',' . $dev_psn . ',' . $devid . ' other not find.';
                continue;
            }
        }
        $psn_list_find = false;

        for ($j = 0; $j < $psn_index + 1; $j++) {
            if ($psn_list[$j]['psnid'] == $dev_psnid) {
                $psn_list_find = true;
                if(empty($psn_list[$j]['devid'])){
                	$psn_list[$j]['devid'][] = $devid;
                }else{
                	$psn_list_devid_find=false;
                	foreach($psn_list[$j]['devid'] as $psn_list_devid)
                	{
                		if($devid==$psn_list_devid){
                			$psn_list_devid_find=true;
                			break;
                		}
                	}
                	if($psn_list_devid_find==false){
              			$psn_list[$j]['devid'][] = $devid;
              		}
                }
                break;
            }
        }

        if ($psn_list_find == false) {
            $psn_index = $psn_index + 1;
            $psn_list[$psn_index]['psnid'] = $dev_psnid;
            $psn_list[$psn_index]['psn'] = $dev_psn;
            $psn_list[$psn_index]['devid'][] = $devid;
        }
        
        $changeid_find = false;
        foreach ($change_devs as $ch_dev) {
            if ($ch_dev['old_psn'] == $dev_psn
                && $ch_dev['old_devid'] == $devid) {
                $changeid_find = true;
                if ($ch_dev['flag'] == 1 || $ch_dev['flag'] == 2) {
                    $change_buf_find=false;
                }else if($ch_dev['flag'] == 3){
                	$changeid_find = false;
                }
            }
        }
        if ($changeid_find == false) {
        		if($dev_psn!=$psn){
        			 $change_add_find=false;
        			 foreach($change_add as $chadd)
        			 {
	        			 	if($chadd['psnid']==$psnid&&
	        			 	$chadd['old_psn']==$dev_psn&&
	        			 	$chadd['old_devid']==$devid){
	        			 		$change_add_find=true;
	        			 	}
        			 }
        			 if($change_add_find==false){
                	$dev_info=D('device')->field('rid')->where(array('devid'=>$devid,'psn'=>$dev_psn))->find();
                	if($dev_info){
                		$rfid=$dev_info['rid'];
		                $change_dev = array('psnid' => $psnid,
		                    'old_psn' => $dev_psn,
		                    'old_devid' => $devid,
		                    'sid' => $bsnint,
		                    'rfid'=> $rfid,
		                );
			             $change_add[]= $change_dev; 
                	}
        			 }
        		}
        }
			}
		
		}

    $day_begin = strtotime(date('Y-m-d', time()));
    $hour_time = 60 * 60;
    $pre_time = 5 * 60;
    $hour_pre_time = 15 * 60;
    $now = time();
    $today = strtotime(date('Y-m-d', $now) . '00:00:00');
    $now = $now - $today;
    
		$hour_delay = $interval[0];
		$min_delay	= $interval[1];
		$freq				= $interval[2];

    if ($min_delay == 0) {
        $real_time = ((int)(($now + $hour_pre_time) / ($hour_delay * $hour_time)) - 1) * $hour_delay * $hour_time;
        $real_time = $today + $real_time;
        $interval = ($hour_delay / $freq) * $hour_time;
    } else {
        $real_time = ((int)(($now + $pre_time) / ($min_delay * 60) - 1)) * $min_delay * 60;
        $real_time = $today + $real_time;
        $interval = ($min_delay / $freq) * 60;
    }



    $start = $real_time - $interval * $freq;
    $end = $real_time;
	    
	    
    foreach ($psn_list as $psn_buf){
        $psn_buf_psnid=$psn_buf['psnid'];
        $psn_buf_psn=$psn_buf['psn'];
        if(count($psn_buf['devid'])>0){
            $wheredev['devid']=array('in',$psn_buf['devid']);
            $curdb1301='access1301_base';
            $acc1301_values=D($curdb1301)->where(array('psn'=>$psn_buf_psn))->where($wheredev)->where('time >='.$start.' and time<'.$end)->select();

            foreach($psnallinfo as $psninfo){
                if($psn_buf_psnid==$psninfo['id']){
                    $blacklist_psn=$psninfo['sn'];
                    break;
                }
            }
            foreach($acc1301add_list as $acc1301add){
                if($acc1301add['psn']==$psn_buf_psn){
                    $acc1301add_find=false;
                    foreach($acc1301_values as $acc1301_value){
                        if($acc1301_value['time']==$acc1301add['time']&&
                            $acc1301_value['psnid']==$acc1301add['psnid']&&
                            $acc1301_value['psn']==$acc1301add['psn']&&
                            $acc1301_value['devid']==$acc1301add['devid'])
                        {
                            if(count($blacklist)<64){
                                $blpsn_str=str_pad($blacklist_psn,5,'0',STR_PAD_LEFT).str_pad($acc1301add['devid'],4,'0',STR_PAD_LEFT);
                                $inlist=false;
                                foreach($blacklist as $black){
                                    if($black==$blpsn_str){
                                        $inlist=true;
                                        break;
                                    }
                                }
                                if($inlist==false){
                                    $blacklist[]=$blpsn_str;
                                }
                            }
                            $acc1301add_find=true;

                            break;
                        }
                    }
                    if($acc1301add_find==false){
                        $acc1301addall[]=$acc1301add;
                    }
                }
            }

        }
    }
    
		if($accadd_list){
    	$mydb='access_base';
	    $user=D($mydb);
	    //$user->addAll($accadd_list);
		}
		
    if($acc1301addall){
			$mydb1301='access1301_base';
	    $user1301=D($mydb1301);
	    //$user1301->addAll($acc1301addall);
    }		

		if($change_add){
	    $chuser=D('changeidlog');
	    //$chuser->addAll($change_add);
		}
		
		foreach($cur_devs as $dev){
			$devid = $dev['devid'];
			$dev_psn =$dev['psn'];
			$battery= $dev['battery'];
			$dev_state= $dev['dev_state'];
			$version= $dev['version'];
			foreach($devsave_list as $devsave){
				unset($mysave);
				if($devid==$devsave['devid']&&$dev_psn==$devsave['psn']){
					if($battery!=$devsave['battery']){
						$mysave['battery']=$devsave['battery'];
					}
					if($dev_state!=$devsave['dev_state']){
						$mysave['dev_state']=$devsave['dev_state'];
					}
					if($version!=$devsave['version']){
						$mysave['version']=$devsave['version'];
					}
					if(!empty($mysave)){
						//$dev1=M('device')->where(array('devid'=>$devid,'psn'=>$dev_psn))->save($mysave);
					}
				}
			}
		}
	
    foreach ($change_devs as $ch_dev) {
        if ($ch_dev['flag'] == 1 || $ch_dev['flag'] == 2) {
							if($ch_dev['flag'] == 1){
								$ch_list_buf[]=$ch_dev['id'];
							}
              $tmp_dev = array(
              		'id'=>$ch_dev['id'],
                  'old_psn' => $ch_dev['old_psn'],
                  'old_devid' => $ch_dev['old_devid'],
                  'new_devid' => $ch_dev['new_devid']
              );
              $change_buf[] = $tmp_dev;
							if(count($change_buf)>=128){
								break;
							}
        } 
    }
    if(count($ch_list_buf)>0){
    	$where_ch_dev['id']=array('in',$ch_list_buf);
    	//$dev=M('changeidlog')->where($where_ch_dev)->save(array('flag'=>2));
    }
	
    $changedev_count=count($change_buf);

    foreach($change_buf as $chdev){
        $ch_psn = $chdev['old_psn'];
        $ch_devid=$chdev['old_devid'];
        $new_devid=$chdev['new_devid'];
        $olddev_str=str_pad($ch_psn,5,'0',STR_PAD_LEFT).str_pad($ch_devid,4,'0',STR_PAD_LEFT);
        $newdev_str=str_pad($psn,5,'0',STR_PAD_LEFT).str_pad($new_devid,4,'0',STR_PAD_LEFT);
        $changedev['o']=(int)$olddev_str;
        $changedev['n']=(int)$newdev_str;
        $changedev_list[]=$changedev;
    }
    $change_res['count']=$changedev_count;
    $change_res['dev']=$changedev_list;
    $ret['change'] = $change_res;

    foreach($blacklist as $name){
        $blacklist_list[]=(int)$name;
    }
    $blacklist_res['count']=count($blacklist);
    $blacklist_res['dev']=$blacklist_list;
		$ret['blacklist'] = $blacklist_res;
		
		$ret['time']=date('Y-m-d H:i:s', time());

  	$ret['ret']='success';
  	$ret['msg']='SUCCESS.';
		$label = json_encode($ret);
    echo $label;
    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
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

      //$filename = $res_file.date("His_") . mt_rand(100, 999) . ".log"; //D?��?????3?
      //$newFilePath = $logdir . $filename;//��???�⿨�??��??
      //$newFile = fopen($newFilePath, "w"); //�䨰?a???t����D�䨨?
      //fwrite($newFile, $post);
      //fclose($newFile); //1?��???t
  }
  
  public function testmd5(){
  	$file = '../ota/mj_flash_8194.img';
  	
  	echo md5_file($file);
  	exit;
  }
  
  public function addgroup(){
    $psn=23;
		$devs=M('device')->where(array('psn'=>$psn))->select();
		foreach($devs as $dev){
			$gd['psn']=$psn;
			$gd['devid']=$dev['devid'];
			$gd['rid']=$dev['rid'];
			$gd['flag']=$dev['flag'];
			$gd['group_id']=0;
			$gd_list[]=$gd;
		}
		dump($gd_list);
		//$ret=M('group_dev')->addAll($gd_list);
  }
  
	public function changenum(){
		$changeid=M('changeidlog')->where(array('flag'=>3))->where('old_psn = 22')->select();
		//dump($changeid);
		
		foreach($changeid as $dev){
			//$ridlist[]=$dev['rfid'];
			$psnid=$dev['psnid'];
			$psninfo = M('psn')->where(array('id'=>$psnid))->find();
			//dump($psninfo);
			$psn=$dev['old_psn'];
			$devid=$dev['old_devid'];
			$rid=$dev['rfid'];
			$new_psn=$psninfo['sn'];
			$new_devid=$dev['new_devid'];
			$ret=M('device')->where(array('rid'=>$rid,'psn'=>$psn,'devid'=>$devid))->save(array('flag'=>2));
			$ret=M('device')->where(array('rid'=>$rid,'psn'=>$new_psn,'devid'=>$new_devid))->save(array('flag'=>1));
			echo 'add change sn';
			dump($new_psn);
			dump($new_devid);
			dump($rid);
		}
		exit;
	}
	
	
	public function getlost(){
		$mode=M('','','DB_CONFIG');
		$rids=array(380153,310137,310134,310132,310138310139,310133,330453,330451,330456,330458,330457,330455,330450,330452,340612,320430,320427,320424);
		$whererid['rid']=array('in',$rids);
		$ret = M('device')->where($whererid)->where(array('flag'=>1))->select();
		foreach($ret as $dev){
			$ids[]=$dev['id'];
		}
		if(empty($ids)===false){
			$whereid['id']=array('in',$ids);
			$ret = M('device')->where($whereid)->save(array('flag'=>4));
			dump($ret);

		}

		exit;
		
	}
	
	public function getstationstate(){
			$bdevice = M('bdevice')->field('autoid,psn,id,uptime,version')->where(array('switch'=>1))->select();
			$count = 48;
			$delay = 3600;
    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));

    	$cur_time = $now - $start_time;
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	
    	$first_time = $cur_time+$start_time;
    	$end_time = $cur_time+$start_time-$count*$delay;	
    	$timeall[]=$end_time;
    	$timeall[]=$first_time;
    	
    	dump(count($bdevice));
    	dump(date('Y-m-d H:i:s',$first_time));
    	dump(date('Y-m-d H:i:s',$end_time));
    	
    	$brssi = M('brssi')->where(array('station'=>1278))->where('time>='.$end_time.' and time<='.$first_time)->order('time desc')->select();
			echo 'lost:<br>';
    	foreach($bdevice as $s){
    		$psn=$s['psn'];
    		$sid=$s['id'];
    		$uptime=(int)substr($s['uptime'],0,2);
    		$times=$count/$uptime;
    		$v=0;
				foreach($brssi as $r){
					if($r['psnid']==$psn&&$r['bsn']==$sid){
						$v++;
					}
				}

				$s['times']=$v;
    		if($v>$times){
    			//echo 'assert';
					$sn=str_pad($s['psn'],5,'0',STR_PAD_LEFT).str_pad($s['id'],4,'0',STR_PAD_LEFT);
    			//dump($sn);
    		}else if($v< $times){
    			if($v==0){
    				$bdev_lost[]=$s['autoid'];
	    			unset($phone);
	    			unset($smsmsg);
						$sn=str_pad($s['psn'],5,'0',STR_PAD_LEFT).str_pad($s['id'],4,'0',STR_PAD_LEFT);
						echo $sn."<br>";
						//dump($sn);
	    			//dump('lose:'.($times-$v));
	    		}
    		}else{
    			$bdev_normal[]=$s['autoid'];
    		}
    	}
 
			exit;
	}
	
	public function test(){
		ini_set('memory_limit','4096M');
		$lastweek_start = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y")));
		$lastweek_end = date("Y-m-d H:i:s",mktime(24,0,0,date("m"),date("d")-date("w")+7-7,date("Y")));
		$lastweek_starttime=strtotime($lastweek_start);
		$lastweek_endtime=strtotime($lastweek_end);
		$psn=$_GET['psn'];
		echo 'PSN:'.$psn;
		dump($lastweek_start);	
		dump($lastweek_starttime);
		dump($lastweek_end);
		dump($lastweek_endtime);

		$devs=M('device')->where(['psn'=>$psn])->where('dev_state=3 or dev_state=7')->select();
		
		foreach($devs as $dev){
			$ids[]=$dev['devid'];
		}
		if(count($ids)>0){
			$whereinids['devid']=array('in',$ids);
		}
		$time1=time();
		$mydb='access_base';
		$accs=M($mydb)->field('psn,devid,rssi1,rssi2,rssi3,time')->where($whereinids)->where(['psn'=>$psn])->where('time >='.$lastweek_starttime.' and time <='.$lastweek_endtime)->select();
		$time2=time();
		dump($time2-$time1);
		dump(count($accs));
		foreach($accs as $acc){
			foreach($devs as $dev){
				if($dev['devid']===$acc['devid']){
					$acc_list[$dev['id']][$acc['time']]=$acc;
				}
			}
		}

		foreach($devs as $dev){
			unset($step_list);
			unset($selectSql);
			$id=$dev['id'];
			$step_list=$acc_list[$id];
			
			if(count($step_list)< 2){
				continue;
			}
			$cur_time=$lastweek_starttime;
			$next_time=$cur_time+7200;
			$step_sum=0;
			$step_count=0;
			while($next_time<= $lastweek_endtime){
				//dump(date("Y-m-d H:i:s",$cur_time));
				//dump(date("Y-m-d H:i:s",$next_time));
				$cur_step=$step_list[$cur_time];
				$next_step=$step_list[$next_time];
				$cur_time=$cur_time+7200;
				$next_time=$cur_time+7200;
				//dump($cur_step);
				//dump($next_step);
				//dump($cur_step['rssi3']);
				if($cur_step&&$next_step){
					$step=$next_step['rssi2']-$cur_step['rssi2'];
					if($step< 0){
						if(($cur_step['rssi3']&0x03)==0x01){
							$step=0;
						}else{
							if($cur_step['rssi2'] > 50000){
								$step=65535+$next_step['rssi2']-$cur_step['rssi2'];
							}
						}
					}
					//dump($step);
					if($step>0){
						$step_sum+=$step;
						$step_count+=1;
					}
				}
			}
			if($step_count>0){
				$avg_step=(int)($step_sum/$step_count);
				echo 'DEV:';
				dump($dev['devid']);
				//dump($step_sum);
				dump($step_count);
				dump($avg_step);
				$ret = M('device')->where(['id'=>$id])->save(['avg_step'=>$avg_step,'step_count'=>$step_count]);
				dump($ret);
			}

		}
		
		
	}
	
	public function addstep(){
				$devs = M('device')->field('psn,devid,rid,avg_step,step_count')->where('avg_step>0')->select();
				$start_time = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7-7,date("Y")));
				//$start_time=strtotime($start_time);
				foreach($devs as $key=>$dev){
					$devs[$key]['time']=$start_time;
				}
				dump($start_time);
				$ret = M('device_step')->addAll($devs);
				dump($ret);
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
	  		//exit;
	  	}
	    $sn=$_POST['sn'];
	    if(empty($sn)){
	    	$sn=$_GET['sn'];
	    }
	    //$fym='2640423';
	    if($addr=='ldx'){
	    	$fym='2640423';
	    }

	    $cow=$mode->table('cows')->where(array('sn_code'=>$sn))->find();
	    //dump($cow['health_state']);
	    //dump($cow['survival_state']);
	    if($cow['health_state']==3||$cow['survival_state']==3){
	    	$role=NULL;
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
			
			$memcache = new \Memcache;
			$mem_ret=$memcache->connect('localhost', 11211);
		
			$get_result=false;

			if($mem_ret===true){
				$key_sn=$rid;
				if($dev['cow_state']==4||$dev['cow_state']==5){
					if($role=='admin'){
						$key_sn=$role.$rid;
						$rid_list=array("264042300052773","264042300052774","264042300052776","264042300052777","264042300052778","264042300052779","264042300052780","264042300052781","264042300052782","264042300052783");
						$index= rand(0,9);
						$rid=$rid_list[$index];
						$dev = M('device')->field('psn,devid,psnid,rid,avg_temp,psn_now,cow_state,dev_state')->where(array('rid'=>$rid))->where('flag!=2')->order('time desc')->find();
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
				$acclist=M($mydb)->field('temp1,temp2,rssi2,rssi3,time')->where(array('devid'=>$devid,'psn'=>$psn))->where('time >= '.$start_time.' and time <= '.$end_time)
														        ->group('time')
														        ->order('time asc')
														        ->select();	
				if(empty($acclist)){
		  		$jarr=array('ret'=>array('ret_message'=>'acc error','status_code'=>10000401));
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
  
  
  public function weuipushmsg(){
  	$mode=M('','','DB_CONFIG');
  	$template_id = '-HzOtD2zosdFQYPhtu5NZg8hHm-X2UcNIo00dcVM4C4';	
		$appid = 'wx4dba4ec159da3bf7';
		$secret = 'bf6fac869e348f3454d68ef9956cd61b';
  	$now = time();
  	
  	$post=file_get_contents('php://input');
    $array = json_decode($post,TRUE);
    
  	$now=time();
		$msg= $array['wxmsg'];
		$lost_list= $array['data'];

		dump($lost_list);
		
		$start_time = strtotime(date('Y-m-d',$now));
  	$cur_time = ($now - $start_time)/3600;
  	
  	if($cur_time<6||$cur_time>10){
  		//return;
  	}
  	
  	$tokens=M('weui_token')->where('exprie_time >'.$now)->order('time desc')->find();

  	if($tokens){
  		$acc_token=$tokens['access_token'];
  	}else{
  		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
  		$ret=http($url);
			$ret=json_decode($ret,true);
			if(!empty($ret['access_token'])){
				$savetoken['exprie_time']=$now+(int)$ret['expires_in'];
				$savetoken['access_token']=$ret['access_token'];
				$token=M('weui_token')->add($savetoken);
				$acc_token=$ret['access_token'];
			}
  	}
  	
  	{
  		$url='https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.$acc_token;
  		$ret=http($url);
  		$tags=json_decode($ret,true);
  		foreach($tags['tags'] as $tag){
				if($tag['name']=='station'){
					$tagid=$tag['id'];
					break;
				}
  		}
  	}
  	
  	{
  		$url = 'https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token='.$acc_token;
  		$data['tagid']=$tagid;
  		$data['next_openid']='';
  		$data=json_encode($data,true);
  		$ret=http($url,$data,'POST');
  		$users=json_decode($ret,true);
  		$ids=$users['data']['openid'];
  	}
  	
  	foreach($lost_list as $lost){
  			foreach($ids as $id){
  				$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$acc_token;
  				$sn=$lost['sn'];
  				$count = $lost['expire_days'];
  				dump($sn);
					$station = $mode->table('basestations')->where(array('base_code'=>$sn))->find();
					//dump($station);
					$jwmsg='';
					unset($wmsg);
    			$loc = $station['title'];
		    	$wmsg['touser']=$id;
		    	$wmsg['template_id']=$template_id;
		    	$msg_data['first']['value']='您有一条新的基站资费信息';
		    	$msg_data['first']['color']="#173177";
		    	$msg_data['keyword1']['value']=$sn;
		    	$msg_data['keyword1']['color']="#173177";
		    	$msg_data['keyword2']['value']=$loc;
		    	$msg_data['keyword2']['color']="#173177";
		    	$msg_data['keyword3']['value']=$count."天后欠费";
		    	$msg_data['keyword3']['color']="#173177";
		    	$msg_data['keyword4']['value']="资费到期提醒";
		    	$msg_data['keyword4']['color']="#173177";
		    	$msg_data['remark']['value']="请相关工作人员及时处理";
		    	$msg_data['remark']['color']="#173177";
		    	$wmsg['data']=$msg_data;
		    	$jwmsg=json_encode($wmsg,true);
	    		$ret=http($url,$jwmsg,'POST');
	    		$ret=json_encode($ret,true);
  			}
  	}	
  	exit;
  }
  
}