<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

	public function pushnewsubV30(){
		// $checksum=crc32("Thequickbrownfoxjumpedoverthelazydog.");
		// printf("%u\n",$checksum);
		
		$TIME_LEN = 18;//����??��?��?3��?��
		$DELAY_START = 10;
		$HOUR_DELAY_LEN = 2;
		$MIN_DELAY_LEN = 2;
		$FREQ_LEN = 1;
		
		$TIME_DIFF_LEN=3;
		
		$BTSN_LEN  = 10;//��3����10??1����D��,2-41��?������??,5-10??��������??
		$BDSN_LEN  = 4;//BS��?��?3��?��
		$BSN_LEN  = $BTSN_LEN+$BDSN_LEN;//BS��?��?3��?��
		$BVS_LEN  = 1; //B device version
		
    $BRSSI_MAX_LEN = 1;
    $BRSSI_COUNT = 10;
    $BRSSI_SN_LEN = 1;
    $BRSSI_SIGN_LEN = 1;
		$BRSSI_LEN = $BRSSI_MAX_LEN+$BRSSI_COUNT*($BRSSI_SN_LEN+$BRSSI_SIGN_LEN);
		
		$CDATA_START = $TIME_LEN+$BSN_LEN+$BVS_LEN+$BRSSI_LEN;
		
		$COUNT_LEN =2; //data��?��?��y
		$CSN_LEN  =4;//������?��?��?3��?��
		$SIGN_LEN =1;//D?o?
		$CVS_LEN =1;//client version
		$STATE_LEN  =1;//state
		$DELAY_LEN  =1;//delay
		$VAILD_LEN  =1;//��DD��?��??��y
		
		$SENS_LEN  =1;//��DD��?��??��y
		
		$VALUE_LEN = 10;//data?D????3��?��
		$COUNT_VALUE = 4;

		$DATA_LEN = ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN+$SENS_LEN)*2+$VALUE_LEN*$COUNT_VALUE; //��?��?data3��?��
		
		//var_dump($DATA_LEN);
		
		$CRC_LEN  = 4;//D��?��??
		$post      =file_get_contents('php://input');//������??����Y
    $strarr    =unpack("H*", $post);//unpack() o����y�䨮?t????��?��?��???��y?Y??DD?a�㨹?��
    $str       =implode("", $strarr);

		$str = "323032303031303130313030303231303030313634303432333030300000c00105500000000000000000000000000000000000000000000000000674";
    $sndir = substr($str, ($TIME_LEN+$BTSN_LEN)*2,$BDSN_LEN*2);
    $sn_footer = hexdec($sndir)&0x1fff;
    $sn_header = hexdec($sndir)>>13;
    $logbase="lora_log/backupV5030/";
    $logerr="lora_log/errorV5030/";
    $logreq="lora_log/reqV5030/";
    ob_clean();

		if(strlen($str) < $CDATA_START){
    	echo "OKF";
    	exit;
		}

    $hour_delay =substr($str,$DELAY_START*2,$HOUR_DELAY_LEN*2);
    $hour_delay =(int)pack("H*",$hour_delay);
    $min_delay =substr($str,($DELAY_START+$HOUR_DELAY_LEN)*2,$HOUR_DELAY_LEN*2);
    $min_delay =(int)pack("H*",$min_delay);
    $freq = substr($str,($DELAY_START+$HOUR_DELAY_LEN+$MIN_DELAY_LEN)*2,$FREQ_LEN*2);
    $freq = (int)pack("H*",$freq);
    $time_diff = substr($str,($DELAY_START+$HOUR_DELAY_LEN+$MIN_DELAY_LEN+$FREQ_LEN)*2,$TIME_DIFF_LEN*2);
    $time_diff = (int)pack("H*",$time_diff);
    //var_dump($hour_delay);
    //var_dump($min_delay);

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
    	exit;
    }
    $psninfo = D('psn')->where(array('tsn'=>$btsn,'sn'=>$psn))->find();
    //$psninfo = D('psn')->where(array('sn'=>$psn))->find();
    if($psninfo){
    	$psnid=$psninfo['id'];
    	$delay_up=$psninfo['delay_up'];
    	$delay_up=str_pad($delay_up,2,'0',STR_PAD_LEFT);
    	$retry_up=$psninfo['retry_up'];
    	$retry_up=str_pad($retry_up,1,'0',STR_PAD_LEFT);
    }else{
    	echo "OKE";
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
			if($brssisn[$i]>0){
				$brssisignstr= substr($brssistr, $i*($BRSSI_SN_LEN+$BRSSI_SIGN_LEN)*2+$BRSSI_SN_LEN*2,$BRSSI_SIGN_LEN*2);
				$brssisign = hexdec($brssisignstr);
				//var_dump($brssisign);
				if(($brssisign&0x80)==0x80){
					$bsign[$i] = 0-($brssisign&0x7f);
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
						'delay'=>$time_diff,
						'rssi'=>$brssimax,
						'sn1'=>$brssisn[0],
						'rssi1'=>$bsign[0],
						'sn2'=>$brssisn[1],
						'rssi2'=>$bsign[1],
						'sn3'=>$brssisn[2],
						'rssi3'=>$bsign[2],
						'sn4'=>$brssisn[3],
						'rssi4'=>$bsign[3],
						'sn5'=>$brssisn[4],
						'rssi5'=>$bsign[4],
						'sn6'=>$brssisn[5],
						'rssi6'=>$bsign[5],
						'sn7'=>$brssisn[6],
						'rssi7'=>$bsign[6],
						'sn8'=>$brssisn[7],
						'rssi8'=>$bsign[7],
						'sn9'=>$brssisn[8],
						'rssi9'=>$bsign[8],
						'sn10'=>$brssisn[9],
						'rssi10'=>$bsign[9],	
						'time'=>time(),
						);
	 	//$saveRssi=D('brssi')->add($rssi);

    $bdevinfo    =D('bdevice')->where(array('id'=>$bsnint,'psnid'=>$psnid))->find();

    if($bdevinfo){
    	$uptime=$bdevinfo['uptime'];
    	//var_dump($uptime);
    	$rate = $bdevinfo['rate_id'];
    	$dev_freq = $bdevinfo['count'];
    	$delay_time  = str_pad($uptime,4,'0',STR_PAD_LEFT).$dev_freq;
    	
			$log_flag= $bdevinfo['log_flag'];//0 self log 1 sys log
			$dump_rate=$bdevinfo['dump_rate'];
			$step_rate=$bdevinfo['step_rate'];
			$step_rate=str_pad($step_rate,3,'0',STR_PAD_LEFT);
			$step_setup=$bdevinfo['step_setup'];
			$step_setup=str_pad($step_setup,3,'0',STR_PAD_LEFT);
			
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

    $count     =substr($str,$CDATA_START*2,$COUNT_LEN*2);//2?a?a�㨹o����?��?��y
    $count	   =hexdec($count);//�䨮��?����????��a��?????
    $data      =substr($str,($CDATA_START+$COUNT_LEN)*2,$count*$DATA_LEN);//��?3?data
    $env_temp = 0;
    $snint = 0;
    $battery = 0;
    //dump($count);
    
    $day_begin = strtotime(date('Y-m-d',time()));
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
   	
		foreach($cur_devs as $cur_dev){
			$step_list[$cur_dev['devid']]['state']=(int)$cur_dev['dev_state'];
			$step_list[$cur_dev['devid']]['switch']=(int)$cur_dev['step_switch'];
		}
   	dump($cur_devs);
    //?��?a�㨹����??
    $len=strlen($str);
    $crc=substr($str,$len-$CRC_LEN*2);//��?��?���騤���?crc
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
	    	$snint = hexdec($snstr)&0x1fff;;	//�䨮��?����????��a��?????
	    	$dev_psn = hexdec($snstr) >> 13;
	    	
	    	$rfid = $dev_psn*10000+$snint;
	    	echo "dev_psn:";
	    	dump($dev_psn);
	    	if($dev_psn!=$psn)
	    	{
	    		continue;
	    	}
	    	$signstr = substr($data, $i*$DATA_LEN+($CSN_LEN)*2,$SIGN_LEN*2);
	    	$cvsstr =  substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN)*2,$CVS_LEN*2);
	    	$stastr =  substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN)*2,$STATE_LEN*2);
	    	$destr  =  substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN)*2,$DELAY_LEN*2);
	    	
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

				//echo "cvs:";
				//dump($cvs);

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
					$sensstr 	 =  substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$SENS_LEN*2);
					$vaildstr  =  substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN)*2,$VAILD_LEN*2);
	    		$tempstr	 =	substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1��?����????��?��?
					$sens=0-hexdec($sensstr);
					$step_list[$snint]['state']=$state;
	    	}else{
					$sens=0;
					$vaildstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
	    		$tempstr   = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1��?����????��?��?
	    	}
	    	$vaild = hexdec($vaildstr);
	    	if($type>0){
	    		$type=1;
	    	}
	    	//echo "type:";
	    	//dump($type);
	    	if($snint>0)
	    	{
	    		$rfid_find=false;
		    	foreach($cur_devs as $cur_dev){
		    		if(($cur_dev['devid']==$snint)&&($cur_dev['psn']==$dev_psn)){
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
		    		  				//$ret=M('changeidlog')->where(array('id'=>$ch_dev['id']))->save(array('flag'=>3));
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
	    	}else{
	    		continue;
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
		    		 	$tempstr_tmp = substr($tempstr,0+$j*$VALUE_LEN,$VALUE_LEN);
				    	//echo "tempstr_tmp:";
				    	//dump($tempstr_tmp);
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
				$dev_save=array(
									'devid'=>$snint,
									'psn'=>$dev_psn,
									'psnid'=>$psnid,
									'battery'=>$battery,
						  	 	'dev_state'=>$state,
						  	 	'version'=>$cvs);
				$devsave_list[]=$dev_save;
	    }
  	}

		//dump($accadd_list);
		//dump($accadd_list2);
		//dump($rfid_list);
		
  	$mydb='access_'.$psn;
    $user=D($mydb);
		//$access1=$user->addAll($accadd_list);
    		
    $user2=D('taccess');
		//$access2=$user2->addAll($accadd_list2);

		$user3=D('device');
		//$ret=$user3->addAll($rfid_list);

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
		$devres_count=str_pad($devres_count,2,'0',STR_PAD_LEFT);
		$devres_str=$devres_count.'';
		foreach($re_devs as $re_dev){
				$devre_id = $redev['devid'];
				//$string=base_convert($devre_id, 10, 16);
				$devre_id=str_pad($devre_id,4,'0',STR_PAD_LEFT);
				$devres_str=$devres_str.$devre_id;
		}

		if(!empty($devres)){
			$whereredev['devid']=array('in',$devres);
			//$dev1=D('device')->where($whereredev)->where(array('psn'=>$psn))->save(array(re_flag=>2));
		}

		if(!empty($devres2)){ 
			$whereredev2['devid']=array('in',$devres2);
			//$dev1=D('device')->where($whereredev2)->where(array('psn'=>$psn))->save(array(re_flag=>3));
		}
		
		$stepres_count=0;
		foreach($step_list as $key=>$step_dev){
			if(($step_dev['state']&0x03)==0x03){
				if($step_dev['switch']==0){
					$step_dev_str=str_pad($key,4,'0',STR_PAD_LEFT);
					$step_flag='0';
					$stepres_str=$stepres_str.$step_dev_str.$step_flag;
					$stepres_count++;
				}
			}else if(($step_dev['state']&0x03)==0x01){
				if($step_list[$key]['switch']==1){
					$step_dev_str=str_pad($key,4,'0',STR_PAD_LEFT);
					$step_flag='1';
					$stepres_str=$stepres_str.$step_dev_str.$step_flag;
					$stepres_count++;
				}
			}
		}
		$stepres_count=str_pad($stepres_count,4,'0',STR_PAD_LEFT);
		$stepres_str=$stepres_count.$stepres_str;
		
		if($crc==$sum){
			$header="OK1".date('YmdHis');
		}else{
			$header="OK2".date('YmdHis'); 
		}
		dump($step_list);

		$body=$header.$delay_time.$rate.$log_flag.$dump_rate.$step_rate.$step_setup.$delay_up.$retry_up.$change_str.$footer.$stepres_str.$devres_str;

		echo $body;
		exit;
	}

	public function pushnewsubV47()
	{
	    $TIME_LEN = 15;//����??��?��?3��?��
	    $DELAY_START = 10;
	    $HOUR_DELAY_LEN = 2;
	    $MIN_DELAY_LEN = 2;
	    $FREQ_LEN = 1;
	    
	    $BTSN_LEN = 10;//��3����10??1����D��,2-41��?������??,5-10??��������??
	    $BDSN_LEN = 4;//BS��?��?3��?��
	    $BSN_LEN = $BTSN_LEN + $BDSN_LEN;//BS��?��?3��?��
	    $BVS_LEN = 1; //B device version

	    
	    $BRSSI_MAX_LEN = 1;
	    $BRSSI_COUNT = 10;
	    $BRSSI_SN_LEN = 1;
	    $BRSSI_SIGN_LEN = 1;
			$BRSSI_LEN = $BRSSI_MAX_LEN+$BRSSI_COUNT*($BRSSI_SN_LEN+$BRSSI_SIGN_LEN);
			
	    $CDATA_START = $TIME_LEN + $BSN_LEN + $BVS_LEN + $BRSSI_LEN;

	    $COUNT_LEN = 2; //data��?��?��y
	    $CSN_LEN = 4;//������?��?��?3��?��
	    $SIGN_LEN = 1;//D?o?
	    $CVS_LEN = 1;//client version
	    $STATE_LEN = 1;//state
	    $DELAY_LEN = 1;//delay
	    $VAILD_LEN = 1;//��DD��?��??��y

			$SENS_LEN  =1;//��DD��?��??��y
			
			$VALUE_LEN = 10;//data?D????3��?��
			$VALUE_LEN_NEW = 11;//data?D????3��?��
			$COUNT_VALUE = 4;

			$DATA_LEN = ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN+$SENS_LEN)*2+$VALUE_LEN*$COUNT_VALUE; //��?��?data3��?��
			
	    $CRC_LEN = 4;//D��?��??
	    $post = file_get_contents('php://input');//������??����Y
	    $strarr = unpack("H*", $post);//unpack() o����y�䨮?t????��?��?��???��y?Y??DD?a�㨹?��
	    $str = implode("", $strarr);


	    $str = "323032303038313032303031303031313634303432333030300002e003055001a5000000000000000000000000000000000000001b0002c0234f2433814a015123282a88ffffffffffffffffffffffffffffff0002c0254f24330048014673292008ffffffffffffffffffffffffffffff0002c026412433003f016843322a2fffffffffffffffffffffffffffffff0002c0294b24338146015343291fd4ffffffffffffffffffffffffffffff0002c02d522433814a0160632823f1ffffffffffffffffffffffffffffff0002c0364624338151023263292cec37333033dbffffffffffffffffffff0002c03c47243381550138632923f9ffffffffffffffffffffffffffffff0002c0455124330052016933302410ffffffffffffffffffffffffffffff0002c04a4f24338147018853381e9cffffffffffffffffffffffffffffff0002c04c5424330052015693240ab4ffffffffffffffffffffffffffffff0002c05d4b2433004801342327240effffffffffffffffffffffffffffff0002c0614d2433005c015643290fa1ffffffffffffffffffffffffffffff0002c0654724330147014523291b23ffffffffffffffffffffffffffffff0002c06b57243300600121022200d2ffffffffffffffffffffffffffffff0002c06f54243300560141132516edffffffffffffffffffffffffffffff0002c0724d24330041015503292ccbffffffffffffffffffffffffffffff0002c0744e24338145015653302486ffffffffffffffffffffffffffffff0002c0795d2433815a0156133108f0ffffffffffffffffffffffffffffff0002c07d4d24338142015403291eb2ffffffffffffffffffffffffffffff0002c07e5824330047013662230791ffffffffffffffffffffffffffffff0002c081482433004d015643291dd7ffffffffffffffffffffffffffffff0002c0865124338150016263282865ffffffffffffffffffffffffffffff0002c08753243300540163332823eaffffffffffffffffffffffffffffff0002c08a612433005f016043300e42ffffffffffffffffffffffffffffff0002c08c5a2433005f015323290c1effffffffffffffffffffffffffffff0004e0c46b73748102642336119335569329ffffffffffffffffffffffff0000e1e266030400015953353503ffffffffffffffffffffffffffffffff0001fa99";
	    $sndir = substr($str, ($TIME_LEN + $BTSN_LEN) * 2, $BDSN_LEN * 2);
	    $sn_footer = hexdec($sndir) & 0x1fff;
	    $sn_header = hexdec($sndir) >> 13;
	    $logbase = "lora_log/backupV5047/";
	    $logerror = "lora_log/errorV5047/";
	    $logreq = "lora_log/reqV5047/";
			ob_clean();

	    if (strlen($str) < $CDATA_START) {
	        echo "OKF";
	        exit;
	    }

	    $sid = (int)$_GET['sid'] & 0x1fff;
	    
	    $hour_delay = substr($str, $DELAY_START * 2, $HOUR_DELAY_LEN * 2);
	    $hour_delay = (int)pack("H*", $hour_delay);
	    $min_delay = substr($str, ($DELAY_START + $HOUR_DELAY_LEN) * 2, $HOUR_DELAY_LEN * 2);
	    $min_delay = (int)pack("H*", $min_delay);
	    $freq = substr($str, ($DELAY_START + $HOUR_DELAY_LEN + $MIN_DELAY_LEN) * 2, $FREQ_LEN * 2);
	    $freq = (int)pack("H*", $freq);
    
	    $bsnstr = substr($str, $TIME_LEN * 2, $BSN_LEN * 2);
	    $btsnstr = substr($str, $TIME_LEN * 2, $BTSN_LEN * 2);
	    $bdsnstr = substr($str, ($TIME_LEN + $BTSN_LEN) * 2, $BDSN_LEN * 2);
	    $btsn = hex2bin($btsnstr);
	    $bsnint = hexdec($bdsnstr) & 0x1fff;
	    $psn = hexdec($bdsnstr) >> 13;

	    if ($bsnint != $sid) {
	        echo "OKE";
	        exit;
	    }

	    $psnallinfo = D('psn')->select();
	    //dump($psnallinfo);
	    $dev_psnid_find = false;
	    $psn_index = 0;
	    $tpsn_index = 0;
	    foreach ($psnallinfo as $psninfo) {
	        if ($psn == $psninfo['sn']) {
	            $dev_psnid_find = true;
	            $psnid = $psninfo['id'];
	            break;
	        }
	    }

	    if ($dev_psnid_find == false) {
	        echo "OKE";
	        exit;
	    } else {
	        $psn_list[$psn_index]['psnid'] = $psnid;
	        $psn_list[$psn_index]['psn'] = $psn;
	        $tpsn_list[$tpsn_index]['psnid'] = $psnid;
	        $tpsn_list[$tpsn_index]['psn'] = $psn;
	    }

	    $bversion = substr($str, ($TIME_LEN + $BSN_LEN) * 2, $BVS_LEN * 2);
	    $brssimaxstr = substr($str, ($TIME_LEN + $BSN_LEN + $BVS_LEN) * 2, $BRSSI_MAX_LEN * 2);
	    $brssistr = substr($str, ($TIME_LEN + $BSN_LEN + $BVS_LEN + $BRSSI_MAX_LEN) * 2, $BRSSI_LEN * 2);

	    $bvs = hexdec($bversion);
	    $brssimax = hexdec($brssimaxstr);
	    $brssimax = 0 - $brssimax;
	    for ($i = 0; $i < $BRSSI_COUNT; $i++) {
	        $brssisnstr = substr($brssistr, $i * ($BRSSI_SN_LEN + $BRSSI_SIGN_LEN) * 2, $BRSSI_SN_LEN * 2);

	        $brssisn[$i] = hexdec($brssisnstr);
	        if ($brssisn > 0) {
	            $brssisignstr = substr($brssistr, $i * ($BRSSI_SN_LEN + $BRSSI_SIGN_LEN) * 2 + $BRSSI_SN_LEN * 2, $BRSSI_SIGN_LEN * 2);
	            $brssisign = hexdec($brssisignstr);

	            if (($brssisign & 0x08) == 0x08) {
	                $bsign[$i] = 0 - ($brssisign & 0x07);
	            } else {
	                $bsign[$i] = $brssisign;
	            }
	        } else {
	            $bsign[$i] = 0;
	        }
	    }
			$rssi = array(
							'psnid'=>$psnid,
							'bsn'=>$bsnint,
							'station'=>1301,
							'rssi'=>$brssimax,
							'sn1'=>$brssisn[0],
							'rssi1'=>$bsign[0],
							'sn2'=>$brssisn[1],
							'rssi2'=>$bsign[1],
							'sn3'=>$brssisn[2],
							'rssi3'=>$bsign[2],
							'sn4'=>$brssisn[3],
							'rssi4'=>$bsign[3],
							'sn5'=>$brssisn[4],
							'rssi5'=>$bsign[4],
							'sn6'=>$brssisn[5],
							'rssi6'=>$bsign[5],
							'sn7'=>$brssisn[6],
							'rssi7'=>$bsign[6],
							'sn8'=>$brssisn[7],
							'rssi8'=>$bsign[7],
							'sn9'=>$brssisn[8],
							'rssi9'=>$bsign[8],
							'sn10'=>$brssisn[9],
							'rssi10'=>$bsign[9],	
							'time'=>time(),
							);
	    //$saveRssi=D('brssi')->add($rssi);

	    $count = substr($str, $CDATA_START * 2, $COUNT_LEN * 2);//2?a?a�㨹o����?��?��y
	    $count = hexdec($count);//�䨮��?����????��a��?????
	    $data = substr($str, ($CDATA_START + $COUNT_LEN) * 2, $count * $DATA_LEN);//��?3?data
	    $env_temp = 0;
	    $snint = 0;
	    $battery = 0;

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

	    //dump(date('Y-m-d H:i', $start));
	    //dump(date('Y-m-d H:i', $end));

	    //dump('count:' . $count);
	    //dump('psn:' . $psn . ' psnid:' . $psnid);
			$cur_devs =D('device')->where(array('psnid'=>$psnid))->order('devid asc')->select();
			
      $change_devs = D('changeidlog')->where(array('psnid' => $psnid))->select();

	    //?��?a�㨹����??
	    $len=strlen($str);
	    $crc=substr($str,$len-$CRC_LEN*2);//��?��?���騤���?crc
	    $crc=hexdec($crc);

	    $sum=0;
	    $len = strlen($str);
	    for($i=0 ; $i < $len/2-$CRC_LEN;$i++)
	    {
	        $value = hexdec(substr($str, $i*2,2));
	        $sum+=$value;
	    }
	    $sum=$sum&0xffffffff;

			if($crc==$sum){
		    for ($i = 0; $i < $count; $i++) {
		        $snstr = substr($data, $i * $DATA_LEN, $CSN_LEN * 2);
		        $snint = hexdec($snstr) & 0x1fff;    //�䨮��?����????��a��?????
		        $dev_psn = hexdec($snstr) >> 13;
		        $dev_sn = hexdec($snstr);

		        $dev_psnid = $psnid;
						
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
		                $psn_err_log = $psn_err_log . $btsn . ',' . $dev_psn . ',' . $snint . ' other not find.';
		                continue;
		            }
		        }
		        //dump('psnid:'.$dev_psnid.' sn:'.$snint);
		        $psn_list_find = false;

		        for ($j = 0; $j < $psn_index + 1; $j++) {
		            if ($psn_list[$j]['psnid'] == $dev_psnid) {
		                $psn_list_find = true;
		                if(empty($psn_list[$j]['devid'])){
		                	$psn_list[$j]['devid'][] = $snint;
		                }else{
		                	$psn_list_devid_find=false;
		                	foreach($psn_list[$j]['devid'] as $psn_list_devid)
		                	{
		                		if($snint==$psn_list_devid){
		                			$psn_list_devid_find=true;
		                			break;
		                		}
		                	}
		                	if($psn_list_devid_find==false){
	                			$psn_list[$j]['devid'][] = $snint;
	                		}
		                }
		                break;
		            }
		        }

		        if ($psn_list_find == false) {
		            $psn_index = $psn_index + 1;
		            $psn_list[$psn_index]['psnid'] = $dev_psnid;
		            $psn_list[$psn_index]['psn'] = $dev_psn;
		            $psn_list[$psn_index]['devid'][] = $snint;
		        }

		        $signstr = substr($data, $i*$DATA_LEN+($CSN_LEN)*2,$SIGN_LEN*2);
		        $cvsstr = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN)*2,$CVS_LEN * 2);
		        $stastr = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN)*2, $STATE_LEN * 2);
		        $destr = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN + $STATE_LEN) * 2, $DELAY_LEN * 2);
			    	$sensstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$SENS_LEN*2);

		        $sign = 0 - hexdec($signstr);
		        $cvst = hexdec($cvsstr);
		        $cvs = (int)($cvst&0x07);
		        $step_update =(int)($cvst&0x08);
		        $cindex = hexdec($cvsstr);
		        $cindex = ($cindex & 0xf0) >> 4;
		        $state = hexdec($stastr);
		        $delay = hexdec($destr);
		        $vaild = hexdec($vaildstr);

		        $changeid_find = false;
		        foreach ($change_devs as $ch_dev) {
		            if ($ch_dev['old_psn'] == $dev_psn
		                && $ch_dev['old_devid'] == $snint) {
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
			        			 	$chadd['old_devid']==$snint){
			        			 		$change_add_find=true;
			        			 	}
		        			 }
		        			 if($change_add_find==false){
		                	$dev_info=D('device')->field('rid')->where(array('devid'=>$snint,'psn'=>$dev_psn))->find();
		                	if($dev_info){
		                		$rfid=$dev_info['rid'];
				                $change_dev = array('psnid' => $psnid,
				                    'old_psn' => $dev_psn,
				                    'old_devid' => $snint,
				                    'sid' => $sid,
				                    'rfid'=> $rfid,
				                );
					             $change_add[]= $change_dev; 
		                	}
		        			 }
		        		}
		        }

		        $stmp = 0x07;
		        $stmp2 = 0x80;
		        $stmp3 = 0x08;
		        if (($state & $stmp2) == $stmp2) {
		            $battery = 1;
		        } else {
		            $battery = 0;
		        }
		        $lcount = $state & 0x70;
		        $lcount = ($lcount) >> 4;
		        $type = $state & $stmp3;
		        $state = $state & $stmp;
						$step_update = $step_update|$state;
						
			    	if($cvs>3){
							$sensstr 	 =  substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$SENS_LEN*2);
							$vaildstr  =  substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN)*2,$VAILD_LEN*2);
			    		$tempstr	 =	substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1��?����????��?��?
							$sens=0-hexdec($sensstr);
							$step_list[$snint]['state']=$state;
			    	}else{
							$sens=0;
							$vaildstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
			    		$tempstr   = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1��?����????��?��?
			    	}

			    	$vaild = hexdec($vaildstr);
			    	
		        if ($type > 0) {
		            $type = 1;
		        }

		        $dev_save = array(
		            'devid' => $snint,
		            'psn' => $dev_psn,
		            'battery' => $battery,
		            'dev_state' => $state,
		            'version' => $cvs);
		        $devsave_list[] = $dev_save;

		        //$tempstr = substr($data, $i * $DATA_LEN + ($CSN_LEN + $SIGN_LEN + $CVS_LEN + $STATE_LEN + $DELAY_LEN + $VAILD_LEN) * 2, $VALUE_LEN * $COUNT_VALUE);//temp1��?����????��?��?

						if($vaild>4){
							continue;
						}

			    	for($j=0;$j < $vaild;$j++){
				    	$up_time = $real_time-$interval*$freq+$interval*($j+1)+$interval*($freq-$vaild);
					    $up_time = strtotime(date('Y-m-d H:i',$up_time).':00');
				    	if($cvs>3){
				    		 	$tempstr_tmp = substr($tempstr,0+$j*$VALUE_LEN,$VALUE_LEN);
						    	//echo "tempstr_tmp:";
						    	//dump($tempstr_tmp);
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
									$acc1301_list_find = false;
			            /*
			            foreach ($acc1301add_list as $acc1301add) {
			                if ($acc1301add['time'] == $up_time &&
			                    $acc1301add['psnid'] == $psnid &&
			                    $acc1301add['psn'] == $dev_psn &&
			                    $acc1301add['devid'] == $snint) {
			                    $acc1301_list_find = true;
			                    break;
			                }
			            }
			            */
			            if ($acc1301_list_find == false) {
			                $acc1301_add = array(
			                    'psn' => $dev_psn,
			                    'psnid' => $psnid,
			                    'devid' => $snint,
			                    'temp1' => $temp1,
			                    'temp2' => $temp1,
			                    'env_temp' => $temp2,
			                    'sign' => $sign,
										  		'rssi1'=>$sens,
										  		'rssi2'=>$stepint,
										  		'rssi3'=>$step_update,
			                    'cindex' => $cindex,
			                    'lcount' => $lcount,
			                    'delay' => $delay,
			                    'sid' => $sid,
			                    'time' => $up_time,
			                );
			                $acc1301add_list[] = $acc1301_add;
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
									$acc1301_list_find = false;

			            if ($acc1301_list_find == false) {
			                $acc1301_add = array(
			                    'psn' => $dev_psn,
			                    'psnid' => $psnid,
			                    'devid' => $snint,
			                    'temp1' => $temp1,
			                    'temp2' => $temp2,
			                    'env_temp' => $temp3,
			                    'sign' => $sign,
										  		'rssi1'=>0,
										  		'rssi2'=>0,
										  		'rssi3'=>0,
			                    'cindex' => $cindex,
			                    'lcount' => $lcount,
			                    'delay' => $delay,
			                    'sid' => $sid,
			                    'time' => $up_time,
			                );
			                $acc1301add_list[] = $acc1301_add;
			            }
				    	}
						}
		    }
	  	}


	    foreach ($psn_list as $psn_buf){
	        $psn_buf_psnid=$psn_buf['psnid'];
	        $psn_buf_psn=$psn_buf['psn'];
	        if(count($psn_buf['devid'])>0){
	            $wheredev['devid']=array('in',$psn_buf['devid']);
	            $curdb1301='access1301_'.$psn_buf_psn;
	            $acc1301_values=D($curdb1301)->where(array('psn'=>$psn_buf_psn))->where($wheredev)->where('time >='.$start.' and time<='.$end)->select();
	            //dump($acc1301_values);
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

    	$mydb='access_'.$psn;
	    $user=D($mydb);
	    //$ret=$user->addAll($accadd_list);

			$mydb1301='access1301_'.$psn;
	    $user1301=D($mydb1301);
	    //$ret=$user1301->addAll($acc1301addall);
			
	    $tuser=D('taccess');
	    //$ret=$tuser->addAll($accadd_list2);
	  
	    $chuser=D('changeidlog');
	    $ret=$chuser->addAll($change_add);
			dump($change_add);
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
							//$dev1=D('device')->save($mysave);
							//dump($mysave);
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
	    $changedev_count=str_pad($changedev_count,4,'0',STR_PAD_LEFT);
	    $changedev_str=$changedev_count.'';
	    foreach($change_buf as $chdev){
	        $ch_psn = $chdev['old_psn'];
	        $ch_devid=$chdev['old_devid'];
	        $new_devid=$chdev['new_devid'];
	        $olddev_str=str_pad($ch_psn,5,'0',STR_PAD_LEFT).str_pad($ch_devid,4,'0',STR_PAD_LEFT);
	        $newdev_str=str_pad($psn,5,'0',STR_PAD_LEFT).str_pad($new_devid,4,'0',STR_PAD_LEFT);
	        $changedev_str=$changedev_str.$olddev_str.$newdev_str;
	    }
	    $blacklist_str=str_pad(count($blacklist),4,'0',STR_PAD_LEFT);
	    foreach($blacklist as $name){
	        $blacklist_str=$blacklist_str.$name;
	    }

	    if($crc==$sum){
	        $header="OK1";
	    }else{
	        $header="OK2";
	    }

  
	    echo $header.$changedev_str.$blacklist_str;
	    exit;
	}  

	public function rfidconnect(){
		$psn=23;
		//$psn=23;
		$devs=M('device')->where(array('psn'=>$psn))->select();
		//dump($devs);
		$mydb='rfid'.$psn;
		$rfids=M($mydb)->select();
		foreach($rfids as $rfid){
			$devid=(int)substr($rfid['sbi_sn'],-4);
			//dump($rfid['SBI_SN']);
			dump($devid);
			$rid=substr($rfid['sbi_fym'],0,15);
			dump($rid);
			$ret=M('device')->where(array('psn'=>$psn,'devid'=>$devid))->save(array('rid'=>$rid));
		}
		//dump($rfids);
		
		
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
			
  		$ivl[0]=$hour;
  		$ivl[1]=$min;
  		$ivl[2]=$ivl_count;
  		$ret['interval']=$ivl;
    	  	
    	$ota_flag = (int)$bdevinfo['ota_flag'];

			$ret['freq'] = (int)$bdevinfo['rate_id'];
    	if($bdevinfo['version']!=$app_ver){
    		$saveSql=M('bdevice')->where(array('id'=>$bsnint,'psnid'=>$psnid))->save(array('version'=>$app_ver));	
    	}
    	dump($os_ver);

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

			$rfdev_ret=$this->parsedata($data,$psnid,$bsnint,$interval);
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
				dump($dev_save);
				
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
		    		  	if($rfid_dev['devid']==$devid){
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
	  	$mydb='access_'.$psn;
	    $user=D($mydb);
	    //dump($psn);
	    //dump($accadd_list);
			//$user->addAll($accadd_list);
		}
    
    if($accadd_list2){
	    $user2=D('taccess');
			//$user2->addAll($accadd_list2);
    }		

		if($rfid_list){
			$user3=D('device');
			dump($rfid_list);
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
						//$dev1=M('device')->where(array('devid'=>$devid,'psn'=>$dev_psn))->save($mysave);
					}
				}
			}
		}
		dump($re_devs);
  	foreach($re_devs as $redev){
  			$devid_tmp=$redev['devid'];
  			foreach($devbuf as $devre){
  				if($devre==$devid_tmp){
						$devres[]=$devre;
						break;
  				}
  			}
  	}
		dump($devres);
		dump($re_devs2);
  	foreach($re_devs2 as $redev){
  			$devid_tmp=$redev['devid'];
  			foreach($devbuf as $devre){
  				if($devre==$devid_tmp){
						$devres2[]=$devre;
						break;
  				}
  			}
  	}
		dump($devres2);
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
			//$dev1=D('device')->where($whereredev)->where(array('psn'=>$psn))->save(array(re_flag=>2));
		}

		if(!empty($devres2)){
			$whereredev2['devid']=array('in',$devres2);
			//$dev1=D('device')->where($whereredev2)->where(array('psn'=>$psn))->save(array(re_flag=>3));
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
  
  public function parsedata($data,$psnid,$sid,$interval){
			$CSN_LEN  =4;//������?��?��?3��?��
			$SIGN_LEN =1;//D?o?
			$CVS_LEN =1;//client version
			$STATE_LEN  =1;//state
			$DELAY_LEN  =1;//delay
			$VAILD_LEN  =1;//��DD��?��??��y
			
			$SENS_LEN  =1;//��DD��?��??��y
			
			$VALUE_LEN = 10;//data?D????3��?��
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
    	$snint = hexdec($snstr)&0x1fff;;	//�䨮��?����????��a��?????
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
    		$tempstr	 =	substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1��?����????��?��?
				$sens=0-hexdec($sensstr);
    	}else{
				$sens=0;
				$vaildstr  = substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
    		$tempstr   = substr($data, ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1��?����????��?��?
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
				continue;
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
		$bigdiff = $parm['bigdiff'];
		
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
		
		if($rssi){
			//$saveRssi=D('brssi')->addAll($rssi);
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
			$rfdev_ret=$this->parsedata($data,$psnid,$sid,$interval);
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
		                    'sid' => $sid,
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
            $curdb1301='access1301_'.$psn_buf_psn;
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
    	$mydb='access_'.$psn;
	    $user=D($mydb);
	    //$user->addAll($accadd_list);
		}
    
    if($acc1301addall){
			$mydb1301='access1301_'.$psn;
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
    	dump($where_ch_dev);
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
     
    for($i=0;$i<10;$i++){
    	$blpsn_str=str_pad($psn,5,'0',STR_PAD_LEFT).str_pad(30+$i,4,'0',STR_PAD_LEFT);
    	$blacklist[]=$blpsn_str;
    } 
     
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
      //$newFilePath = $logdir . $filename;//��???��?��??��??
      //$newFile = fopen($newFilePath, "w"); //�䨰?a???t��?��?D�䨨?
      //fwrite($newFile, $post);
      //fclose($newFile); //1?��????t
  }
  
  public function testmd5(){
  	$file = 'app/app4097';
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
}