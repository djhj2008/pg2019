<?php
namespace Home\Controller;
use Think\Controller;
class DjtestController extends Controller {
  public function index(){
   
  }
  
	public function scancows_avg(){
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
    	$first_time = $cur_time-$delay+$start_time;
    	$last_time = $cur_time-$delay+$start_time-($max_count-1)*3600;
    	
			dump(date('Y-m-d H:i:s',$first_time));
			dump(date('Y-m-d H:i:s',$last_time));
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->order('id asc')->select();

    	foreach($devlist as $dev){
    		if($dev['avg_temp']==0){
    			$devidlist[]=$dev['devid'];
    		}
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
				if($avg>0){
					continue;
				}
				
				echo 'devid:';
				dump($devid);
				/*
				$mydb='access_'.$psnid;
				$accSelect1=M($mydb)->where('time <='.$first_time.' and time >='.$last_time)
														->where(array('devid'=>$devid,'psn'=>$psn))
										    		->field('devid,temp1,temp2,time')
										    		->group('time')
										    		->order('time desc')
										    		->limit(0,$max_count)
										    		->select();
				$temp=NULL;
				$count = count($accSelect1);
				*/
				/*
				if($count==0){
					for($i=30;$i<40;$i++){
		    		$mydb='access1301_'.$i;
		    		$acc1301list1[$i]=M($mydb)->where(array('devid'=>$devid,'psn'=>$psn))
															    		->where('time<='.$first_time)
															    		->field('devid,temp1,temp2,time')
															    		->group('time')
															    		->order('time desc')
															    		->limit(0,$max_count)
															    		->select();
		    	}
				}
				*/
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
/*
				for($i=30;$i<40;$i++){
					if($acc1301list1[$i]){
						foreach($acc1301list1[$i] as $acc){
							$acc_find=false;
							foreach($acc_list as $al){
								if($al['time']==$acc['time']){
									$acc_find=true;
									break;
								}
							}
							if($acc_find==false){
								$acc_list[]=$acc;
							}
						}
					}
					$acc_size=count($acc_list);
					if($acc_size>=$max_count){
						break;
					}
				}
*/				
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
    	$first_time = $cur_time-$delay+$start_time;
    	$last_time = $cur_time-$delay+$start_time-($max_count-1)*3600;
    	
			dump(date('Y-m-d H:i:s',$first_time));
			dump(date('Y-m-d H:i:s',$last_time));
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->order('id asc')->select();

    	foreach($devlist as $dev){
    		if($dev['avg_temp']==0){
    			$devidlist[]=$dev['devid'];
    		}
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
				if($avg>0){
					continue;
				}
				
				echo 'devid:';
				dump($devid);
				dump($cdev[$devid]);
				/*
				$mydb='access_'.$psnid;
				$accSelect1=M($mydb)->where('time <='.$first_time.' and time >='.$last_time)
														->where(array('devid'=>$devid,'psn'=>$psn))
										    		->field('devid,temp1,temp2,time')
										    		->group('time')
										    		->order('time desc')
										    		->limit(0,$max_count)
										    		->select();
				$temp=NULL;
				$count = count($accSelect1);
				*/
				/*
				if($count==0){
					for($i=30;$i<40;$i++){
		    		$mydb='access1301_'.$i;
		    		$acc1301list1[$i]=M($mydb)->where(array('devid'=>$devid,'psn'=>$psn))
															    		->where('time<='.$first_time)
															    		->field('devid,temp1,temp2,time')
															    		->group('time')
															    		->order('time desc')
															    		->limit(0,$max_count)
															    		->select();
		    	}
				}
				*/
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
/*
				for($i=30;$i<40;$i++){
					if($acc1301list1[$i]){
						foreach($acc1301list1[$i] as $acc){
							$acc_find=false;
							foreach($acc_list as $al){
								if($al['time']==$acc['time']){
									$acc_find=true;
									break;
								}
							}
							if($acc_find==false){
								$acc_list[]=$acc;
							}
						}
					}
					$acc_size=count($acc_list);
					if($acc_size>=$max_count){
						break;
					}
				}
*/				
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
	
	public function pushnewsubV30(){
		// $checksum=crc32("Thequickbrownfoxjumpedoverthelazydog.");
		// printf("%u\n",$checksum);
		
		$TIME_LEN = 15;//时间字符长度
		$DELAY_START = 10;
		$HOUR_DELAY_LEN = 2;
		$MIN_DELAY_LEN = 2;
		$FREQ_LEN = 1;
		
		$BTSN_LEN  = 10;//统编10位1类型,2-4国家编码,5-10区域编码
		$BDSN_LEN  = 4;//BS字符长度
		$BSN_LEN  = $BTSN_LEN+$BDSN_LEN;//BS字符长度
		$BVS_LEN  = 1; //B device version
		
		$BRSSI_LEN = 9;
		$BRSSI_MAX_LEN = 1;
		$BRSSI_COUNT = 4;
		$BRSSI_SN_LEN = 1;
		$BRSSI_SIGN_LEN =1;
		
		$CDATA_START = $TIME_LEN+$BSN_LEN+$BVS_LEN+$BRSSI_LEN;
		
		$COUNT_LEN =2; //data的条数
		$CSN_LEN  =4;//设备字符长度
		$SIGN_LEN =1;//信号
		$CVS_LEN =1;//client version
		$STATE_LEN  =1;//state
		$DELAY_LEN  =1;//delay
		$VAILD_LEN  =1;//有效值个数
		
		$SENS_LEN  =1;//有效值个数
		$STEP_LEN  =2;//有效值个数
		
		$VALUE_LEN = 9;//data中每个长度
		$COUNT_VALUE = 4;

		$DATA_LEN = ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN+$SENS_LEN+$STEP_LEN)*2+$VALUE_LEN*$COUNT_VALUE; //一条data长度
		
		//var_dump($DATA_LEN);
		
		$CRC_LEN  = 4;//校验码
		$post      =file_get_contents('php://input');//抓取内容
    $strarr    =unpack("H*", $post);//unpack() 函数从二进制字符串对数据进行解包。
    $str       =implode("", $strarr);

		//$str = "32303139303130343135303031303132303836373536333030000040010140014102420343044400060000200256217400014662244602ffffffffffffffffffffffffff0000200356217400014862244602ffffffffffffffffffffffffff0000200644217400014763345103ffffffffffffffffffffffffff0000200551217400014772244602ffffffffffffffffffffffffff0000200857217400014662244602ffffffffffffffffffffffffff0000200745217400015193344903ffffffffffffffffffffffffff00006002";
    $sndir = substr($str, ($TIME_LEN+$BTSN_LEN)*2,$BDSN_LEN*2);
    $sn_footer = hexdec($sndir)&0x1fff;
    $sn_header = hexdec($sndir)>>13;
    $logbase="lora_log/backupV4030/";
    $logerr="lora_log/errorV4030/";
    $logreq="lora_log/reqV4030/";
    ob_clean();
    {
        $logdir =$logbase;
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.$sn_header.'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.$sn_footer.'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.date('Y-m-d',time()).'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
          			
        $filename = date("Ymd_His_").mt_rand(10, 99).".log"; //新图片名称
        $newFilePath = $logdir.$filename;//图片存入路径
        $newFile = fopen($newFilePath,"w"); //打开文件准备写入
        fwrite($newFile,$str);
        fclose($newFile); //关闭文件
           
    }

		if(strlen($str) < $CDATA_START){
    	echo "OKF";
    	exit;
		}

    $sid =  (int)$_GET['sid']&0x1fff;
    //var_dump($sid);
    $bsnstr   =substr($str, $TIME_LEN*2,$BSN_LEN*2);
    $btsnstr =substr($str, $TIME_LEN*2,$BTSN_LEN*2);
    $bdsnstr =substr($str, ($TIME_LEN+$BTSN_LEN)*2,$BDSN_LEN*2);
    $btsn=hex2bin($btsnstr);
    $bsnint = hexdec($bdsnstr)&0x1fff;
    $psn = hexdec($bdsnstr)>>13;
    //var_dump($bsnint);
    //var_dump($psn);
    
    //$psn = $bdevinfo['psn'];
    //var_dump($bsnint);
    if($bsnint!=$sid){
    	echo "OKE";
    	{
        $logdir =$logerr;
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.$sn_header.'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.$sn_footer.'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.date('Y-m-d',time()).'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
          			
          $filename = date("Ymd_His_").mt_rand(10, 99).".log"; //新图片名称
          $newFilePath = $lnewFilePath.$filename;//图片存入路径
          $newFile = fopen($logdir,"w"); //打开文件准备写入
          fwrite($newFile,$bsnint.",".$sid);
          fclose($newFile); //关闭文件
           
    	}    	
    	exit;
    }
    $psninfo = D('psn')->where(array('tsn'=>$btsn,'sn'=>$psn))->find();
    if($psninfo){
    	$psnid=$psninfo['id'];
    }else{
    	echo "OKE";
    	{
        $logdir =$logerr;
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.$sn_header.'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.$sn_footer.'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.date('Y-m-d',time()).'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
          			
          $filename = date("Ymd_His_").mt_rand(10, 99).".log"; //新图片名称
          $newFilePath = $logdir.$filename;//图片存入路径
          $newFile = fopen($newFilePath,"w"); //打开文件准备写入
          fwrite($newFile,$btsn.",".$psn);
          fclose($newFile); //关闭文件
           
    	}
    	exit;
    }

		$bversion = substr($str, ($TIME_LEN+$BSN_LEN)*2,$BVS_LEN*2);
		$brssimaxstr = substr($str, ($TIME_LEN+$BSN_LEN+$BVS_LEN)*2,$BRSSI_MAX_LEN*2);
		$brssistr = substr($str, ($TIME_LEN+$BSN_LEN+$BVS_LEN+$BRSSI_MAX_LEN)*2,$BRSSI_LEN*2);

		$bvs = hexdec($bversion);
		$brssimax = hexdec($brssimaxstr);
		$brssimax = 0-$brssimax;
		for($i=0;$i < $BRSSI_COUNT;$i++){
			$brssisnstr= substr($brssistr, $i*($BRSSI_SN_LEN+$BRSSI_SIGN_LEN)*2,$BRSSI_SN_LEN*2);
			//var_dump($brssisnstr);
			$brssisn[$i] = hexdec($brssisnstr);
			if($brssisn>0){
				$brssisignstr= substr($brssistr, $i*($BRSSI_SN_LEN+$BRSSI_SIGN_LEN)*2+$BRSSI_SN_LEN*2,$BRSSI_SIGN_LEN*2);
				$brssisign = hexdec($brssisignstr);
				//var_dump($brssisign);
				if(($brssisign&0x08)==0x08){
					$bsign[$i] = 0-($brssisign&0x07);
				}else{
					$bsign[$i] = $brssisign;
				}
			}else{
				$bsign[$i]=0;
			}
		}
		$rssi = array(
						'psnid'=>$psnid,
						'bsn'=>$bsnint,
						'rssi'=>$brssimax,
						'sn1'=>$brssisn[0],
						'rssi1'=>$bsign[0],
						'sn2'=>$brssisn[1],
						'rssi2'=>$bsign[1],
						'sn3'=>$brssisn[2],
						'rssi3'=>$bsign[2],
						'sn4'=>$brssisn[3],
						'rssi4'=>$bsign[3],
						'time'=>time(),
						);
	 	$saveRssi=D('brssi')->add($rssi);
	 	//var_dump($rssi);
	 	//var_dump($bsign);
	 	//exit;
		//var_dump($bvs);
    $bdevinfo    =D('bdevice')->where(array('id'=>$bsnint,'psnid'=>$psnid))->find();

    if($bdevinfo){
    	$uptime=$bdevinfo['uptime'];
    	//var_dump($uptime);
    	$rate = $bdevinfo['rate_id'];
    	$dev_freq = $bdevinfo['count'];
    	$delay_time  = str_pad($uptime,4,'0',STR_PAD_LEFT).$dev_freq;
    	if($bdevinfo['version']!=$bvs){
    		//var_dump($bdevinfo['version']);
    		$saveSql=M('bdevice')->where(array('id'=>$bsnint,'psnid'=>$psnid))->save(array('version'=>$bvs));	
    	}
    	$url_flag = $bdevinfo['url_flag'];
			$url = $bdevinfo['url'];
			$change_flag=$bdevinfo['change_flag'];
			$new_bsn=$bdevinfo['new_bsn'];
			if($url_flag==1){
				$urllen=str_pad(strlen($url),2,'0',STR_PAD_LEFT);
				$footer=$url_flag.$urllen.$url;
			}else{
				$footer="0";
			}
			if($change_flag==1){
				$change_str=$change_flag.$new_bsn;
				$ch_psnint=(int)substr($new_bsn,0,5);
				$ch_bsnint=(int)substr($new_bsn,5,4);
				$ch_bdevinfo=D('bdevice')->where(array('id'=>$ch_bsnint,'psnid'=>$ch_psnint))->find();
				if($ch_bdevinfo)
				{
					$rate = $ch_bdevinfo['rate_id'];
				}
				
			}else{
				$change_str="0";
			}
    }else{
    	$dev_freq = 1;
    	$url_flag = 0;
    	//$delay_time = "00101".$dev_freq;
    	echo "OKE";
    	exit;
    }
    
    //echo "delay_time:";
		//var_dump($delay_time);

    $count     =substr($str,$CDATA_START*2,$COUNT_LEN*2);//2为解包后的倍数
    $count	   =hexdec($count);//从十六进制转十进制
    $data      =substr($str,($CDATA_START+$COUNT_LEN)*2,$count*$DATA_LEN);//取出data
    $env_temp = 0;
    $snint = 0;
    $battery = 0;
    //var_dump($count);
    
    $hour_delay =substr($str,$DELAY_START*2,$HOUR_DELAY_LEN*2);
    $hour_delay =(int)pack("H*",$hour_delay);
    $min_delay =substr($str,($DELAY_START+$HOUR_DELAY_LEN)*2,$HOUR_DELAY_LEN*2);
    $min_delay =(int)pack("H*",$min_delay);
    $freq = substr($str,($DELAY_START+$HOUR_DELAY_LEN+$MIN_DELAY_LEN)*2,$FREQ_LEN*2);
    $freq = (int)pack("H*",$freq);
    
    //var_dump($hour_delay);
    //var_dump($min_delay);
    //echo "freq:";
    //var_dump($freq);
    
    $day_begin = strtotime(date('Y-m-d',time()));
    //var_dump(date('Y-m-d',time()));
    $hour_time = 60*60;
    $pre_time =5*60;
    $hour_pre_time=15*60;
    $now = time();
    $today = strtotime(date('Y-m-d',$now).'00:00:00');
   	$now = $now-$today;
   	
   	$re_devs =D('device')->where(array('psnid'=>$psnid,'re_flag'=>1))->order('devid asc')->limit(0,64)->select();
   	
   	$re_devs2 =D('device')->where(array('psnid'=>$psnid,'re_flag'=>2))->order('devid asc')->limit(0,64)->select();
   	
   	$cur_devs =D('device')->where(array('psnid'=>$psnid))->order('devid asc')->select();
   	
   	$change_devs = D('changeidlog')->where(array('psnid' => $psnid))->select();
   	//var_dump($re_devs);
   	
    //未解包比对
    $len=strlen($str);
    $crc=substr($str,$len-$CRC_LEN*2);//收到发来的crc
    //var_dump($crc);
    $crc=hexdec($crc);
    //var_dump($crc);

    $sum=0;
    $len = strlen($str);
		for($i=0 ; $i < $len/2-$CRC_LEN;$i++)
		{
			$value = hexdec(substr($str, $i*2,2));
			//var_dump($value);
			$sum+=$value;
		}
		$sum=$sum&0xffffffff;
		if($crc==$sum){
	    for($i=0 ; $i < $count ; $i++){
	    	$snstr   =substr($data, $i*$DATA_LEN,$CSN_LEN*2);
	    	//var_dump($snstr);
	    	$snint = hexdec($snstr)&0x1fff;;	//从十六进制转十进制
	    	$dev_psn = hexdec($snstr) >> 13;
	    	
	    	$rfid = $dev_psn*10000+$snint;
	    	//echo "sn:";
	    	//var_dump($snint);
	    	if($dev_psn!=$psn)
	    	{
	    		continue;
	    	}
	    	$signstr = substr($data, $i*$DATA_LEN+($CSN_LEN)*2,$SIGN_LEN*2);
	    	$cvsstr = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN)*2,$CVS_LEN*2);
	    	$stastr = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN)*2,$STATE_LEN*2);
	    	$destr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN)*2,$DELAY_LEN*2);

	    	$sensstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$SENS_LEN*2);
	    	$stepstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN)*2,$STEP_LEN*2);
	    	$sign= 0-hexdec($signstr);
	    	$cvs = hexdec($cvsstr);
	    	$cvs = $cvs&0x0f;
	    	$cindex = hexdec($cvsstr);
	    	$cindex = ($cindex&0xf0)>>4;
	    	$state =  hexdec($stastr);
	    	$delay =  hexdec($destr);
	    	$vaild = hexdec($vaildstr);
	    	//echo "devid:";
	    	//var_dump($snint);
	    	$devbuf[]=$snint;

	    	//var_dump($cvs);
	    	//var_dump($state);
	    	//echo "vaild:";
	    	//var_dump($vaild);
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
	    	
	    	if($cvs>3){
	    		$sens=0-hexdec($sensstr);
	    		$pre_step=hexdec($stepstr);
	    		$tempstr=substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN+$SENS_LEN+$STEP_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1十六进制字符
					$vaildstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN+$STEP_LEN)*2,$VAILD_LEN*2);
	    	}else{
					$sens=0;
	    		$step=0;
	    		$tempstr=substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1十六进制字符
					$vaildstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
	    	}
	    	$vaild = hexdec($vaildstr);
	    	//var_dump($ast);
	    	//var_dump($state);
	    	//var_dump($type);
	    	
	    	if($type>0){
	    		$type=1;
	    	}
	    	//echo "type:";
	    	//var_dump($type);
	    	if($snint>0)
	    	{
	    		$rfid_find=false;
		    	foreach($cur_devs as $cur_dev){
		    		if($cur_dev['rid']==$rfid){
		    			$rfid_find=true;
		    			break;
		    		}
		    	}
		    	//var_dump($info);
		    	if($rfid_find==false){
		    		  $change_dev_find=false;
		    		  foreach($change_devs as $ch_dev){
		    		  	if($ch_dev['psnid']==$psnid&&
		    		  		$ch_dev['new_devid']==$snint){
		    		  			$change_dev_find=true;
		    		  			$rfid=$ch_dev['rfid'];
		    		  			if($ch_dev['flag']==2){
		    		  				$change_dev_find=false;
		    		  				$ret=M('changeidlog')->where(array('id'=>$ch_dev['id']))->save(array('flag'=>3));
		    		  			}
		    		  		}
		    		  }
		    		  foreach($rfid_list as $rfid_dev){
		    		  	if($rfid_dev['devid']==$snint){
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
						  	 	'dev_state'=>$state,
						  	 	'version'=>$cvs,
									's_count'=>0,
									'rid'=>$rfid,
									'age'=>1,
									'devid'=>$snint,
								);
								$rfid_list[]=$addrfdev;
		    		  }else{
		    		  	//dump('NEVER.');
		    		  	//NEVER HAPPEN.
		    		  }
		    	}
	    	}
	    	
		    if($min_delay == 0){
		      $real_time = ((int)(($now+$hour_pre_time)/($hour_delay*$hour_time))-1)*$hour_delay*$hour_time;
		      $real_time = $today+$real_time;
		      $interval = ($hour_delay/$freq)*$hour_time;
		      //$real_time = strtotime(date('Y-m-d H',$real_time).':00:00');
		    }else{
		    	$real_time = ((int)(($now+$pre_time)/($min_delay*60)-1))*$min_delay*60;
		    	$real_time = $today+$real_time;
		    	$interval = ($min_delay/$freq)*60;
		      //$real_time = strtotime(date('Y-m-d H:i',$real_time).':00');
		    }
		    
		    $start = $real_time-$interval*$freq;
		    $end = $real_time;
		    
				if($vaild>4){
					continue;
				}
				
	    	for($j=0;$j < $vaild;$j++){

		    	$up_time = $real_time-$interval*$freq+$interval*($j+1)+$interval*($freq-$vaild);
			    $up_time = strtotime(date('Y-m-d H:i',$up_time).':00');
		    	if($cvs>3){
		    		if($j==$vaild-1){
		    			$step=$pre_step;
		    		}else{
		    			$step=0;
		    		}
		    	}
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
			    	//var_dump('temp3:'.$temp3);
						$acc_add=array(
				  				'psn'=>$dev_psn,
				  				'psnid'=>$psnid,
						  		'devid'=>$snint,
						  		'temp1'=>$temp1,
						  		'temp2'=>$temp2,
						  		'env_temp'=>$temp3,
						  		'sign'=>$sign,
						  		'rssi1'=>$sens,
						  		'rssi2'=>$step,
						  		'rssi3'=>$battery,
						  		'cindex'=>$cindex,
						  		'lcount'=>$lcount,
						  		'delay'=>$delay,
						  		'time' =>$up_time,
						  		'sid' =>$sid,
						  	);
						$dev_save=array(
									'devid'=>$snint,
									'psn'=>$dev_psn,
									'psnid'=>$psnid,
									'battery'=>$battery,
						  	 	'dev_state'=>$state,
						  	 	'version'=>$cvs);
						  	 	
						$accadd_list[]=$acc_add;
						$devsave_list[]=$dev_save;
		
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
			    //var_dump('temp3:'.$temp3);
			    
				  	$acc_add2=array(
				   	  'psn'=>$dev_psn,
				   	  'psnid'=>$psnid,
				  		'devid'=>$snint,
				  		'temp1'=>$temp1,
				  		'temp2'=>$temp2,
			  			'env_temp'=>$temp3,
			  			'sign'=>$sign,
							'rssi1'=>$sens,
							'rssi2'=>$step,
							'rssi3'=>$battery,
				  		'cindex'=>$cindex,
				  		'lcount'=>$lcount,
				  		'delay'=>$delay,
				  		'time' =>$up_time,
				  		'sid' =>$sid,
				  	);

						$dev_save2=array(
														'devid'=>$snint,
														'psn'=>$dev_psn,
														'psnid'=>$psnid,
														'battery'=>$battery,
											  	 	'dev_state'=>$state,
											  	 	'version'=>$cvs);

						$accadd_list2[]=$acc_add2;
						$devsave_list[]=$dev_save2;
					}
				}
				//var_dump($temp1);
	    	//var_dump($temp2);
	    }
  	}

  	$mydb='access_'.$psn;
    $user=D($mydb);
		$access1=$user->addAll($accadd_list);
    		
    $user2=D('taccess');
		$access2=$user2->addAll($accadd_list2);
		//dump($user->getlastsql());
		//dump("acc add 1:");
		//dump($access1);

		$user3=D('device');
		$ret=$user3->addAll($rfid_list);
		//dump($rfid_list);
		
		foreach($cur_devs as $dev){
			$devid = $dev['devid'];
			$dev_psn =$dev['psn'];
			$battery= $dev['battery'];
			$dev_state= $dev['dev_state'];
			$version= $dev['version'];
			foreach($devsave_list as $devsave){
				if($devid==$devsave['devid']){
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
						$dev1=D('device')->where(array('devid'=>$devid,'psn'=>$dev_psn))->save($mysave);
						//$dev1=D('device')->save($mysave);
						//dump($mysave);
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
/*
		$devres_count=count($devres);
		$devres_count=str_pad($devres_count,2,'0',STR_PAD_LEFT);
		$devres_str=$devres_count.'';
		foreach($devres as $devre_id){
				//$string=base_convert($devre_id, 10, 16);
				$devre_id=str_pad($devre_id,4,'0',STR_PAD_LEFT);
				$devres_str=$devres_str.$devre_id;
		}
*/
		$devres_count=count($devres);
		$devres_count=str_pad($devres_count,2,'0',STR_PAD_LEFT);
		$devres_str=$devres_count.'';
		foreach($devres as $devre_id){
				//$string=base_convert($devre_id, 10, 16);
				$devre_id=str_pad($devre_id,4,'0',STR_PAD_LEFT);
				$devres_str=$devres_str.$devre_id;
		}
		$devres_count=99;
		$devres_count=str_pad($devres_count,2,'0',STR_PAD_LEFT);
		$devres_str=$devres_count.'';
		for($i=0;$i<99;$i++){
				$devre_id=str_pad($i+30,4,'0',STR_PAD_LEFT);
				$devres_str=$devres_str.$devre_id;
		}

		if(!empty($devres)){
			$whereredev['devid']=array('in',$devres);
			$dev1=D('device')->where($whereredev)->where(array('psn'=>$psn))->save(array(re_flag=>2));
		}

		if(!empty($devres2)){ 
			$whereredev2['devid']=array('in',$devres2);
			$dev1=D('device')->where($whereredev2)->where(array('psn'=>$psn))->save(array(re_flag=>3));
		}
		
		if($crc==$sum){
			$header="OK1".date('YmdHis');
		}else{
			$header="OK2".date('YmdHis'); 
		}
		$log_flag=1;//0 self log 1 sys log
		$dump_rate=1;
		$step_rate=115;
		
		$stepres_count=512;
		$stepres_count=str_pad($stepres_count,4,'0',STR_PAD_LEFT);
		$stepres_str=$stepres_count.'';
		for($i=0;$i<100;$i++){
			$step_dev=str_pad(1+$i,4,'0',STR_PAD_LEFT);
			$step_flag=1;
			$stepres_str=$stepres_str.$step_psn.$step_dev.$step_flag;
		}
		for($i=100;$i<512;$i++){
			$step_dev=str_pad(1+$i,4,'0',STR_PAD_LEFT);
			$step_flag=0;
			$stepres_str=$stepres_str.$step_psn.$step_dev.$step_flag;
		}
		$body=$header.$delay_time.$rate.$log_flag.$dump_rate.$step_rate.$change_str.$footer.$stepres_str.$devres_str;
  	{
        $logdir =$logreq;
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.$sn_header.'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.$sn_footer.'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $logdir = $logdir.date('Y-m-d',time()).'/';
        if(!file_exists($logdir)){
            mkdir($logdir);
        }
        $filename = date("Ymd_His_").mt_rand(10, 99).".log"; //新图片名称
        $newFilePath = $logdir.$filename;//图片存入路径
        $newFile = fopen($newFilePath,"w"); //打开文件准备写入
        fwrite($newFile,$body);
        fclose($newFile); //关闭文件 
  	}

		echo $body;
		exit;
	}
}