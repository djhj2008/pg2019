<?php
namespace Home\Controller;
use Think\Controller;
class DataV100Controller extends Controller {
  public function index(){
       ob_clean();
       echo 'test';
       exit;
  }
  
  public function simcard(){
  	$bdevice=M('bdevice')->where(['switch'=>1])->order('psn asc')->select();
  	foreach($bdevice as $dev){
  		$bsn =str_pad($dev['psn'],5,'0',STR_PAD_LEFT).str_pad($dev['id'],4,'0',STR_PAD_LEFT);
  		$pre_dev=M('bdevice')->where(['new_bsn'=>$bsn])->find();
  		//dump($bsn);
  		if($pre_dev){
  			//dump($pre_dev);
  			if(empty($pre_dev['number'])){
  				$bsn =str_pad($pre_dev['psn'],5,'0',STR_PAD_LEFT).str_pad($pre_dev['id'],4,'0',STR_PAD_LEFT);
  				$pre_pre_dev=M('bdevice')->where(['new_bsn'=>$bsn])->find();
  				//dump($bsn);
  				if($pre_pre_dev){
  					//dump($pre_pre_dev);
  					$sim=$pre_pre_dev['number'];
  				}else{
  					dump('err.');
  				}
  			}else{
  				$sim=$pre_dev['number'];
  			}
  		}else{
  			$sim=$dev['number'];
  		}
  		dump('sn:'.$bsn.' sim:'.$sim);
  		$simcard['sn']=(int)$bsn;
  		$simcard['sim']=(int)$sim;
  		$sim_list[]=$simcard;
  	}
  	$ret = M('simcards')->addAll($sim_list);
  	//dump($bdevice);
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
  
  public function masterV10(){
  	$post = file_get_contents('php://input');
    $sid = $_GET['sid'];
    $sn_footer = (int)$sid & 0x1fff;
    $sn_header = (int)$sid >> 13;
    $logbase="lora_json/V10/";
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
    	$ret['delay_up']=$delay_up;
    	$ret['retry_up']=$retry_up;
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
    	
			$ret['log']=$bdevinfo['log_flag'];
			$ret['rate_flag']=$bdevinfo['dump_rate'];
			$step['rate']=$bdevinfo['step_rate'];
			$step['config']=$bdevinfo['step_setup'];
			$step['sleeptime']=$bdevinfo['step_sleeptime'];

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
				  $appfile=M('appfiles')->where(array('type'=>'app'))->where('ver>'.$app_ver)->order('ver desc')->find();
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
			$rfdev_ret=$this->parsedata($data,$psn,$psnid,$bsnint,$interval);
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


	    	if($devid>0&&$devid<2800)
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
		    		  			$change_dev_find=true;
		    		  			$rfid=$ch_dev['rfid'];
		    		  			if($ch_dev['flag']==2){
		    		  				$change_dev_find=false;
		    		  				$ret=M('changeidlog')->where(array('id'=>$ch_dev['id']))->save(array('flag'=>3));
		    		  			}
		    		  		}
		    		  }
		    		  foreach($rfid_list as $rfid_dev){
		    		  	if($rfid_dev['devid']==$devid&&$dev_psn==$rfid_dev['psn']){
		    		  		$change_dev_find=true;
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
		
		if($accadd_list){
	  	$mydb='access_base';
	    $user=D($mydb);
			$user->addAll($accadd_list);
		}
    
    if($accadd_list2){
	    $user2=D('taccess');
			$user2->addAll($accadd_list2);
    }		

		if($rfid_list){
			$user3=D('device');
			$user3->addAll($rfid_list);
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
		
		$ret['time']=date('Y-m-d H:i:s', time());

  	$ret['ret']='success';
  	$ret['msg']='SUCCESS.';
		$label = json_encode($ret);
    echo $label;
    $this->savelog($sn_header,$sn_footer,$logbase,$req_file,$label);
  	exit;
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

		$hour_delay = $interval[0];
		$min_delay	= $interval[1];
		$freq				= $interval[2];

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
            $acc1301_values=D($curdb1301)->where(array('psn'=>$psn_buf_psn))->where($wheredev)->where('time >='.$start.' and time<='.$end)->select();

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
	    $user->addAll($accadd_list);
		}
		
    if($acc1301addall){
			$mydb1301='access1301_base';
	    $user1301=D($mydb1301);
	    $user1301->addAll($acc1301addall);
    }		

		if($change_add){
	    $chuser=D('changeidlog');
	    $chuser->addAll($change_add);
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
    	$dev=M('changeidlog')->where($where_ch_dev)->save(array('flag'=>2));
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
  
  public function parsedata($data,$psn,$psnid,$sid,$interval){
			$CSN_LEN  =4;//设备字符长度
			$SIGN_LEN =1;//信号
			$CVS_LEN =1;//client version
			$STATE_LEN  =1;//state
			$DELAY_LEN  =1;//delay
			$VAILD_LEN  =1;//有效值个数
			
			$SENS_LEN  =1;//有效值个数
			
			$VALUE_LEN = 10;//data中每个长度
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
    	$snint = hexdec($snstr)&0x1fff;;	//从十六进制转十进制
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
    		$tempstr	 =	substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1十六进制字符
				$sens=0-hexdec($sensstr);
    	}else{
				$sens=0;
				$vaildstr  = substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
    		$tempstr   = substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1十六进制字符
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
			}  	

    	for($j=0;$j < $vaild;$j++){
    		if($freq>1){
	    		$up_time = $start+$interval*$j+$interval*($freq-$vaild);
    		}else{
    			$up_time = $end+$interval*$j+$interval*($freq-$vaild);
    		}
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
  
	public function pushlog()
	{
	    $post = file_get_contents('php://input');//抓取内容
	    $sid = $_GET['sid'];
	    $sn_footer = (int)$sid & 0x1fff;
	    $sn_header = (int)$sid >> 13;
	    $logbase = "lora_log/syslogV100/";

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

	        $filename = date("Ymd_His_") . mt_rand(10, 99) . ".log"; //新图片名称
	        $newFilePath = $logdir . $filename;//图片存入路径
	        $newFile = fopen($newFilePath, "w"); //打开文件准备写入
	        fwrite($newFile, $post);
	        fclose($newFile); //关闭文件
	    }
	    echo "OK1";
	    exit;
	}
	
	public function test(){
		echo 'OK:'.date("Y-m-d_H:i:s");
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
			$ret['rate_flag']=1;//跳频开关
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
		dump($json);
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

        $filename = date("Ymd_His_") . mt_rand(10, 99) . ".log"; //新图片名称
        $newFilePath = $logdir . $filename;//图片存入路径
        $newFile = fopen($newFilePath, "w"); //打开文件准备写入
        fwrite($newFile, $post);
        fclose($newFile); //关闭文件
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