<?php
namespace Home\Controller;
use Think\Controller;
class DatapushTestController extends Controller {
	

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
		
		$VALUE_LEN = 9;//data中每个长度
		$COUNT_VALUE = 4;

		$DATA_LEN = ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2+$VALUE_LEN*$COUNT_VALUE; //一条data长度
		
		//var_dump($DATA_LEN);
		
		$CRC_LEN  = 4;//校验码
		$post      =file_get_contents('php://input');//抓取内容
    $strarr    =unpack("H*", $post);//unpack() 函数从二进制字符串对数据进行解包。
    $str       =implode("", $strarr);

		$str = "323031393132303631313031303031313038363735363330300000a0010350000000000000000000050000a01e3b730400017731197601ffffffffffffffffffffffffff0000a0103c730401019631199101ffffffffffffffffffffffffff0000a01140730400019541199501ffffffffffffffffffffffffff0000a0124a730400019131199601ffffffffffffffffffffffffff0000a0133b730400010232200202ffffffffffffffffffffffffff000053a7";
    $sndir = substr($str, ($TIME_LEN+$BTSN_LEN)*2,$BDSN_LEN*2);
    $sn_footer = hexdec($sndir)&0x1fff;
    $sn_header = hexdec($sndir)>>13;
    $logbase="lora_backup30/";
    $logerr="lora_error30/";
    $logreq="lora_req30/";
    
		if(strlen($str) < $CDATA_START){
    	echo "OKF".date('YmdHis')."00101";
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
    	echo "OKE".date('YmdHis')."00101";  	
    	exit;
    }
    $psninfo = D('psn')->where(array('tsn'=>$btsn,'sn'=>$psn))->find();
    if($psninfo){
    	$psnid=$psninfo['id'];
    }else{
    	echo "OKE".date('YmdHis')."00101";
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
	 	//$saveRssi=D('brssi')->add($rssi);
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
			if($url_flag==1){
				$urllen=str_pad(strlen($url),2,'0',STR_PAD_LEFT);
				$footer=$url_flag.$urllen.$url;
			}else{
				$footer="0";
			}
    }else{
    	$dev_freq = 1;
    	$url_flag = 0;
    	$delay_time = "00101".$dev_freq;
    	
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
    for($i=0 ; $i < $count ; $i++){
    	$snstr   =substr($data, $i*$DATA_LEN,$CSN_LEN*2);
    	//var_dump($snstr);
    	$snint = hexdec($snstr)&0x1fff;;	//从十六进制转十进制
    	$dev_psn = hexdec($snstr) >> 13;
    	
    	$rfid = $dev_psn*10000+$snint;
    	//echo "sn:";
    	//var_dump($snint);
    	$signstr = substr($data, $i*$DATA_LEN+($CSN_LEN)*2,$SIGN_LEN*2);
    	$cvsstr = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN)*2,$CVS_LEN*2);
    	$stastr = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN)*2,$STATE_LEN*2);
    	$destr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN)*2,$DELAY_LEN*2);
    	$vaildstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
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
	    
	    $tempstr=substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1十六进制字符
    	for($j=0;$j < $vaild;$j++){

	    	$up_time = $real_time-$interval*$freq+$interval*($j+1)+$interval*($freq-$vaild);
		    $up_time = strtotime(date('Y-m-d H:i',$up_time).':00');
		    
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
		    	//$acc_value=D('access')->where(array('time'=>$up_time,'psn'=>$psnid,'devid'=>$snint))->find();
					$acc_add=array(
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
					$dev_save=array(
								'devid'=>$snint,
								'psn'=>$psn,
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
    
    $user=D('access');
		//$access1=$user->addAll($accadd_list);
		
    $user2=D('taccess');
		//$access2=$user2->addAll($accadd_list2);
		//dump($user->getlastsql());
		//dump("acc add 1:");
		dump('acc list:');
		dump($accadd_list);
		dump($accadd_list2);

		$user3=D('device');
		//$ret=$user3->addAll($rfid_list);
		dump('rf list:');
		dump($rfid_list);
		
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
						//$dev1=D('device')->where(array('devid'=>$devid,'psn'=>$dev_psn))->save($mysave);
						//$dev1=D('device')->save($mysave);
						dump('devid:'.$devid.' psn:'.$dev_psn);
						dump($mysave);
					}
				}
			}
		}

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

		$devres_count=count($devres);
		$devres_count=str_pad($devres_count,2,'0',STR_PAD_LEFT);
		$devres_str=$devres_count.'';
		foreach($devres as $devre_id){
				//$string=base_convert($devre_id, 10, 16);
				$devre_id=str_pad($devre_id,4,'0',STR_PAD_LEFT);
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

		echo $header.$delay_time.$rate.$footer.$devres_str;
		exit;
	}
	
	public function pushnewsubV46()
	{
	    $TIME_LEN = 15;//时间字符长度
	    $DELAY_START = 10;
	    $HOUR_DELAY_LEN = 2;
	    $MIN_DELAY_LEN = 2;
	    $FREQ_LEN = 1;

	    $BTSN_LEN = 10;//统编10位1类型,2-4国家编码,5-10区域编码
	    $BDSN_LEN = 4;//BS字符长度
	    $BSN_LEN = $BTSN_LEN + $BDSN_LEN;//BS字符长度
	    $BVS_LEN = 1; //B device version

	    $BRSSI_LEN = 9;
	    $BRSSI_MAX_LEN = 1;
	    $BRSSI_COUNT = 4;
	    $BRSSI_SN_LEN = 1;
	    $BRSSI_SIGN_LEN = 1;

	    $CDATA_START = $TIME_LEN + $BSN_LEN + $BVS_LEN + $BRSSI_LEN;

	    $COUNT_LEN = 2; //data的条数
	    $CSN_LEN = 4;//设备字符长度
	    $SIGN_LEN = 1;//信号
	    $CVS_LEN = 1;//client version
	    $STATE_LEN = 1;//state
	    $DELAY_LEN = 1;//delay
	    $VAILD_LEN = 1;//有效值个数

	    $VALUE_LEN = 9;//data中每个长度
	    $COUNT_VALUE = 4;

	    $DATA_LEN = ($CSN_LEN + $SIGN_LEN + $CVS_LEN + $STATE_LEN + $DELAY_LEN + $VAILD_LEN) * 2 + $VALUE_LEN * $COUNT_VALUE; //一条data长度

	    $CRC_LEN = 4;//校验码
	    $post = file_get_contents('php://input');//抓取内容
	    $strarr = unpack("H*", $post);//unpack() 函数从二进制字符串对数据进行解包。
	    $str = implode("", $strarr);


	    $str = "323031393132303631323031303031313038363735363330300000a00103500000000000000000001a000281e02213040204978119985118875118890119928119991120000281e15513040104976119975118844118889118888119988119000281e12b13040104976119975118844118889118888119988119000281e22313040204988119988118864118888118866119965119000281e32f13040104987119980119907118887118855119942119000281e42d13040204965119976118857118900119908119988119000281e51e13040104977119002518820150898118008519970150000281e55713040104977119002518820150898118008519970150000281e65513040104967119997118886118867118863119954119000281e62f13040104967119997118886118867118863119954119000281e72d13040204966119975118857118866118875119956119000281e82613040204975119978118868118965119932120019219000281e85713040204975119978118868118965119932120019219000281e92b13040204967119979118899118880119896119987119000281ea2b13040204967119960119900119923119909119017219000281eb5413040204956119957118887118879118884119966119000281eb2913040204956119957118887118879118884119966119000281ec3013040104966119971118822118845118862119944119000281ed2d13040004977119978118887118900119938119971120000281ee2313040304966119968118888118934119919119997119000281ef2513040204977119972118822118899118918119980120000281f04313040204967119978117819117856118863119944119000281f12513040204976119988118887118865118853119922119000281f15713040204976119988118887118865118853119922119000281f22113040104977119967117787117899118878119976119000281f328130402049751199661188331188861188661199341190000cdbc";
	    $sndir = substr($str, ($TIME_LEN + $BTSN_LEN) * 2, $BDSN_LEN * 2);
	    $sn_footer = hexdec($sndir) & 0x1fff;
	    $sn_header = hexdec($sndir) >> 13;
	    $logbase = "lora_backup46/";
	    $logerror = "lora_error46/";
	    $logreq = "lora_req46/";


	    if (strlen($str) < $CDATA_START) {
	        echo "OKF";
	        exit;
	    }

	    $sid = (int)$_GET['sid'] & 0x1fff;
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

	    $psnallinfo = D('psn')->where(array('tsn' => $btsn))->select();
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
	        'psnid' => $psnid,
	        'station' => 1301,
	        'bsn' => $bsnint,
	        'rssi' => $brssimax,
	        'sn1' => $brssisn[0],
	        'rssi1' => $bsign[0],
	        'sn2' => $brssisn[1],
	        'rssi2' => $bsign[1],
	        'sn3' => $brssisn[2],
	        'rssi3' => $bsign[2],
	        'sn4' => $brssisn[3],
	        'rssi4' => $bsign[3],
	        'time' => time(),
	    );
	    //$saveRssi=D('brssi')->add($rssi);

	    $count = substr($str, $CDATA_START * 2, $COUNT_LEN * 2);//2为解包后的倍数
	    $count = hexdec($count);//从十六进制转十进制
	    $data = substr($str, ($CDATA_START + $COUNT_LEN) * 2, $count * $DATA_LEN);//取出data
	    $env_temp = 0;
	    $snint = 0;
	    $battery = 0;

	    $hour_delay = substr($str, $DELAY_START * 2, $HOUR_DELAY_LEN * 2);
	    $hour_delay = (int)pack("H*", $hour_delay);
	    $min_delay = substr($str, ($DELAY_START + $HOUR_DELAY_LEN) * 2, $HOUR_DELAY_LEN * 2);
	    $min_delay = (int)pack("H*", $min_delay);
	    $freq = substr($str, ($DELAY_START + $HOUR_DELAY_LEN + $MIN_DELAY_LEN) * 2, $FREQ_LEN * 2);
	    $freq = (int)pack("H*", $freq);

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
      $change_devs = D('changeidlog')->where(array('psnid' => $psnid))->select();
      
	    for ($i = 0; $i < $count; $i++) {
	        $snstr = substr($data, $i * $DATA_LEN, $CSN_LEN * 2);
	        $snint = hexdec($snstr) & 0x1fff;    //从十六进制转十进制
	        $dev_psn = hexdec($snstr) >> 13;
	        $dev_sn = hexdec($snstr);

	        $dev_psnid = $psnid;
					$rfid = $dev_psn*10000+$snint;
					
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

	        $signstr = substr($data, $i * $DATA_LEN + ($CSN_LEN) * 2, $SIGN_LEN * 2);
	        $cvsstr = substr($data, $i * $DATA_LEN + ($CSN_LEN + $SIGN_LEN) * 2, $CVS_LEN * 2);
	        $stastr = substr($data, $i * $DATA_LEN + ($CSN_LEN + $SIGN_LEN + $CVS_LEN) * 2, $STATE_LEN * 2);
	        $destr = substr($data, $i * $DATA_LEN + ($CSN_LEN + $SIGN_LEN + $CVS_LEN + $STATE_LEN) * 2, $DELAY_LEN * 2);
	        $vaildstr = substr($data, $i * $DATA_LEN + ($CSN_LEN + $SIGN_LEN + $CVS_LEN + $STATE_LEN + $DELAY_LEN) * 2, $VAILD_LEN * 2);
	        $sign = 0 - hexdec($signstr);
	        $cvs = hexdec($cvsstr);
	        $cvs = $cvs & 0x0f;
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
							        foreach($change_buf as $ch_tmp)
							        {
				        			 	if($ch_tmp['id']==$ch_dev['id']){
				        			 		$change_buf_find=true;
				        			 		break;
				        			 	}
							        }
							        if($change_buf_find==false)
							        {
		                    $tmp_dev = array(
		                    		'id'=>$ch_dev['id'],
		                        'old_psn' => $dev_psn,
		                        'old_devid' => $snint,
		                        'new_devid' => $ch_dev['new_devid']
		                    );
		                    $change_buf[] = $tmp_dev;
							        }

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

	        $tempstr = substr($data, $i * $DATA_LEN + ($CSN_LEN + $SIGN_LEN + $CVS_LEN + $STATE_LEN + $DELAY_LEN + $VAILD_LEN) * 2, $VALUE_LEN * $COUNT_VALUE);//temp1十六进制字符
	        for ($j = 0; $j < $vaild; $j++) {

	            $up_time = $real_time - $interval * $freq + $interval * ($j + 1) + $interval * ($freq - $vaild);
	            $up_time = strtotime(date('Y-m-d H:i', $up_time) . ':00');

	            if ($type == 0) {
	                if ($j == 0) {
	                    $temp1str1 = substr($tempstr, 3, 1);
	                    $temp1str2 = substr($tempstr, 0, 1);
	                    $temp1str3 = substr($tempstr, 1, 1);
	                    $temp1int = base_convert($temp1str1, 16, 10);

	                    $temp2str1 = substr($tempstr, 4, 1);
	                    $temp2str2 = substr($tempstr, 5, 1);
	                    $temp2str3 = substr($tempstr, 2, 1);
	                    $temp2int = base_convert($temp2str1, 16, 10);

	                    $temp3str1 = substr($tempstr, 9, 1);
	                    $temp3str2 = substr($tempstr, 6, 1);
	                    $temp3str3 = substr($tempstr, 7, 1);
	                    $temp3int = base_convert($temp3str1, 16, 10);
	                } else if ($j == 1) {
	                    $temp1str1 = substr($tempstr, 10, 1);
	                    $temp1str2 = substr($tempstr, 11, 1);
	                    $temp1str3 = substr($tempstr, 8, 1);
	                    $temp1int = base_convert($temp1str1, 16, 10);

	                    $temp2str1 = substr($tempstr, 15, 1);
	                    $temp2str2 = substr($tempstr, 12, 1);
	                    $temp2str3 = substr($tempstr, 13, 1);
	                    $temp2int = base_convert($temp2str1, 16, 10);

	                    $temp3str1 = substr($tempstr, 16, 1);
	                    $temp3str2 = substr($tempstr, 17, 1);
	                    $temp3str3 = substr($tempstr, 14, 1);
	                    $temp3int = base_convert($temp3str1, 16, 10);
	                } else if ($j == 2) {
	                    $temp1str1 = substr($tempstr, 21, 1);
	                    $temp1str2 = substr($tempstr, 18, 1);
	                    $temp1str3 = substr($tempstr, 19, 1);
	                    $temp1int = base_convert($temp1str1, 16, 10);

	                    $temp2str1 = substr($tempstr, 22, 1);
	                    $temp2str2 = substr($tempstr, 23, 1);
	                    $temp2str3 = substr($tempstr, 20, 1);
	                    $temp2int = base_convert($temp2str1, 16, 10);

	                    $temp3str1 = substr($tempstr, 27, 1);
	                    $temp3str2 = substr($tempstr, 24, 1);
	                    $temp3str3 = substr($tempstr, 25, 1);
	                    $temp3int = base_convert($temp3str1, 16, 10);
	                } else if ($j == 3) {
	                    $temp1str1 = substr($tempstr, 28, 1);
	                    $temp1str2 = substr($tempstr, 29, 1);
	                    $temp1str3 = substr($tempstr, 26, 1);
	                    $temp1int = base_convert($temp1str1, 16, 10);

	                    $temp2str1 = substr($tempstr, 33, 1);
	                    $temp2str2 = substr($tempstr, 30, 1);
	                    $temp2str3 = substr($tempstr, 31, 1);
	                    $temp2int = base_convert($temp2str1, 16, 10);

	                    $temp3str1 = substr($tempstr, 34, 1);
	                    $temp3str2 = substr($tempstr, 35, 1);
	                    $temp3str3 = substr($tempstr, 32, 1);
	                    $temp3int = base_convert($temp3str1, 16, 10);
	                }

	                if (($temp1int & 0x08) == 0x08) {
	                    $temp1str1 = $temp1int & 0x07;
	                    if ($temp1str1 == 0) {
	                        $temp1 = '-' . $temp1str2 . "." . $temp1str3;
	                    } else {
	                        $temp1 = '-' . $temp1str1 . $temp1str2 . "." . $temp1str3;
	                    }
	                } else {
	                    if ($temp1str1 == 0) {
	                        $temp1 = $temp1str2 . "." . $temp1str3;
	                    } else {
	                        $temp1 = $temp1str1 . $temp1str2 . "." . $temp1str3;
	                    }
	                }

	                //var_dump('temp1:'.$temp1);
	                if (($temp2int & 0x08) == 0x08) {
	                    $temp2str1 = $temp2int & 0x07;
	                    if ($temp2str1 == 0) {
	                        $temp2 = '-' . $temp2str2 . "." . $temp2str3;
	                    } else {
	                        $temp2 = '-' . $temp2str1 . $temp2str2 . "." . $temp2str3;
	                    }
	                } else {
	                    if ($temp2str1 == 0) {
	                        $temp2 = $temp2str2 . "." . $temp2str3;
	                    } else {
	                        $temp2 = $temp2str1 . $temp2str2 . "." . $temp2str3;
	                    }

	                }

	                //var_dump('temp2:'.$temp2);
	                if (($temp3int & 0x08) == 0x08) {
	                    $temp3str1 = $temp3int & 0x07;
	                    if ($temp3str1 == 0) {
	                        $temp3 = '-' . $temp3str2 . "." . $temp3str3;
	                    } else {
	                        $temp3 = '-' . $temp3str1 . $temp3str2 . "." . $temp3str3;
	                    }
	                } else {
	                    if ($temp3str1 == 0) {
	                        $temp3 = $temp3str2 . "." . $temp3str3;
	                    } else {
	                        $temp3 = $temp3str1 . $temp3str2 . "." . $temp3str3;
	                    }
	                }

	                $acc_list_find = false;
	                foreach ($accadd_list as $accadd) {
	                    if ($accadd['time'] == $up_time &&
	                        $accadd['psn'] == $dev_psn &&
	                        $accadd['devid'] == $snint) {
	                        $acc_list_find = true;
	                        break;
	                    }
	                }
	                if ($acc_list_find == false) {
	                    $acc_add = array(
	                        'psn' => $dev_psn,
	                        'psnid' => $psnid,
	                        'devid' => $snint,
	                        'temp1' => $temp1,
	                        'temp2' => $temp2,
	                        'env_temp' => $temp3,
	                        'sign' => $sign,
	                        'cindex' => $cindex,
	                        'lcount' => $lcount,
	                        'delay' => $delay,
	                        'time' => $up_time,
	                        'sid' => $sid,
	                    );
	                    $accadd_list[] = $acc_add;
	                }
	            } else {
	                if ($j == 0) {
	                    $temp1str1 = substr($tempstr, 3, 1);
	                    $temp1str2 = substr($tempstr, 0, 1);
	                    $temp1str3 = substr($tempstr, 1, 1);
	                    $temp1int = base_convert($temp1str1, 16, 10);

	                    $temp2str1 = substr($tempstr, 4, 1);
	                    $temp2str2 = substr($tempstr, 5, 1);
	                    $temp2str3 = substr($tempstr, 2, 1);

	                    $temp3str1 = substr($tempstr, 9, 1);
	                    $temp3str2 = substr($tempstr, 6, 1);
	                    $temp3str3 = substr($tempstr, 7, 1);
	                } else if ($j == 1) {
	                    $temp1str1 = substr($tempstr, 10, 1);
	                    $temp1str2 = substr($tempstr, 11, 1);
	                    $temp1str3 = substr($tempstr, 8, 1);
	                    $temp1int = base_convert($temp1str1, 16, 10);

	                    $temp2str1 = substr($tempstr, 15, 1);
	                    $temp2str2 = substr($tempstr, 12, 1);
	                    $temp2str3 = substr($tempstr, 13, 1);

	                    $temp3str1 = substr($tempstr, 16, 1);
	                    $temp3str2 = substr($tempstr, 17, 1);
	                    $temp3str3 = substr($tempstr, 14, 1);
	                } else if ($j == 2) {
	                    $temp1str1 = substr($tempstr, 21, 1);
	                    $temp1str2 = substr($tempstr, 18, 1);
	                    $temp1str3 = substr($tempstr, 19, 1);
	                    $temp1int = base_convert($temp1str1, 16, 10);

	                    $temp2str1 = substr($tempstr, 22, 1);
	                    $temp2str2 = substr($tempstr, 23, 1);
	                    $temp2str3 = substr($tempstr, 20, 1);

	                    $temp3str1 = substr($tempstr, 27, 1);
	                    $temp3str2 = substr($tempstr, 24, 1);
	                    $temp3str3 = substr($tempstr, 25, 1);
	                } else if ($j == 3) {
	                    $temp1str1 = substr($tempstr, 28, 1);
	                    $temp1str2 = substr($tempstr, 29, 1);
	                    $temp1str3 = substr($tempstr, 26, 1);
	                    $temp1int = base_convert($temp1str1, 16, 10);

	                    $temp2str1 = substr($tempstr, 33, 1);
	                    $temp2str2 = substr($tempstr, 30, 1);
	                    $temp2str3 = substr($tempstr, 31, 1);

	                    $temp3str1 = substr($tempstr, 34, 1);
	                    $temp3str2 = substr($tempstr, 35, 1);
	                    $temp3str3 = substr($tempstr, 32, 1);
	                }

	                if (($temp1int & 0x08) == 0x08) {
	                    $temp1str1 = $temp1int & 0x07;
	                    if ($temp1str1 == 0) {
	                        $temp1 = '-' . $temp1str2 . "." . $temp1str3;
	                    } else {
	                        $temp1 = '-' . $temp1str1 . $temp1str2 . "." . $temp1str3;
	                    }
	                } else {
	                    if ($temp1str1 == 0) {
	                        $temp1 = $temp1str2 . "." . $temp1str3;
	                    } else {
	                        $temp1 = $temp1str1 . $temp1str2 . "." . $temp1str3;
	                    }
	                }
	                if ($temp2str1 == 0) {
	                    $temp2 = $temp2str2 . "." . $temp2str3;
	                } else {
	                    $temp2 = $temp2str1 . $temp2str2 . "." . $temp2str3;
	                }
	                if ($temp3str1 == 0) {
	                    $temp3 = $temp3str2 . "." . $temp3str3;
	                } else {
	                    $temp3 = $temp3str1 . $temp3str2 . "." . $temp3str3;
	                }

	                $tacc_list_find = false;
	                foreach ($taccadd_list as $taccadd) {
	                    if ($taccadd['time'] == $up_time &&
	                        $taccadd['psn'] == $dev_psnid &&
	                        $taccadd['devid'] == $snint) {
	                        $tacc_list_find = true;
	                        break;
	                    }
	                }
	                if ($tacc_list_find == false) {
	                    $tacc_add = array(
	                        'psn' => $dev_psn,
	                        'psnid' => $psnid,
	                        'devid' => $snint,
	                        'temp1' => $temp1,
	                        'temp2' => $temp2,
	                        'env_temp' => $temp3,
	                        'sign' => $sign,
	                        'cindex' => $cindex,
	                        'lcount' => $lcount,
	                        'delay' => $delay,
	                        'time' => $up_time,
	                        'sid' => $sid,
	                    );
	                    $taccadd_list[] = $tacc_add;
	                }
	            }

	            $acc1301_list_find = false;
	            foreach ($acc1301add_list as $acc1301add) {
	                if ($acc1301add['time'] == $up_time &&
	                    $acc1301add['psnid'] == $psnid &&
	                    $acc1301add['psn'] == $dev_psnid &&
	                    $acc1301add['devid'] == $snint) {
	                    $acc1301_list_find = true;
	                    break;
	                }
	            }
	            if ($acc1301_list_find == false) {
	                $acc1301_add = array(
	                    'psn' => $dev_psn,
	                    'psnid' => $psnid,
	                    'devid' => $snint,
	                    'temp1' => $temp1,
	                    'temp2' => $temp2,
	                    'env_temp' => $temp3,
	                    'sign' => $sign,
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


	    dump($psn_list);
	    dump($tpsn_list);
	    //未解包比对
	    $len=strlen($str);
	    $crc=substr($str,$len-$CRC_LEN*2);//收到发来的crc
	    $crc=hexdec($crc);

	    $sum=0;
	    $len = strlen($str);
	    for($i=0 ; $i < $len/2-$CRC_LEN;$i++)
	    {
	        $value = hexdec(substr($str, $i*2,2));
	        $sum+=$value;
	    }
	    $sum=$sum&0xffffffff;

	    //dump($accadd_list);
	    //dump($acc1301add);

	    foreach ($psn_list as $psn_buf){
	        $psn_buf_psnid=$psn_buf['psnid'];
	        $psn_buf_psn=$psn_buf['psn'];
	        dump($psn_buf_psnid);
	        if(count($psn_buf['devid'])>0){
	            $wheredev['devid']=array('in',$psn_buf['devid']);
	            $acc_values=D('access')->where(array('psn'=>$psn_buf_psn))->where($wheredev)->where('time >='.$start.' and time<='.$end)->select();
	            //dump($acc_values);
	            dump(count($accadd_list));
	            foreach($accadd_list as $accadd){
	                if($accadd['psn']==$psn_buf_psn){
	                    $accadd_find=false;
	                    foreach($acc_values as $acc_value){
	                        if($acc_value['time']==$accadd['time']&&
	                            $acc_value['psnid']==$accadd['psnid']&&
	                            $acc_value['devid']==$accadd['devid'])
	                        {
	                            $accadd_find=true;
	                            $accaddall_not[]=$accadd;
	                            $accaddall_not[]=$acc_value;
	                            break;
	                            //Nothing
	                        }
	                    }
	                    if( $accadd_find==false)
	                    {
	                        $accaddall[]=$accadd;
	                    }
	                }
	            }


	            $acc1301_values=D('access1301')->where(array('psn'=>$psn_buf_psn))->where($wheredev)->where('time >='.$start.' and time<='.$end)->select();
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
	                            $acc1301_value['devid']==$acc1301add['devid'])
	                        {
	                            if(count($blacklist)<512){
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
	    
	    dump('accaddall_not list:');
	    dump($accaddall_not);

	    $user=D('access');
	    //$ret=$user->addAll($accaddall);
	    dump('accaddall list:');
	    dump($accaddall);

	    $user1301=D('access1301');
	    //$ret=$user1301->addAll($acc1301addall);
	    dump('access1301 list:');
	    dump($acc1301addall);

	    $tuser=D('taccess');
	    //$ret=$tuser->addAll($taccadd_list);
	    dump('taccess list:');
	    dump($taccadd_list);

	    $chuser=D('changeidlog');
	    //$ret=$chuser->addAll($change_add);
	    dump('change_add list:');
	    dump($change_add);
	    
	    dump('change_buf list:');
	    dump($change_buf);
	    $changedev_count=count($change_buf);
	    $changedev_count=str_pad($changedev_count,2,'0',STR_PAD_LEFT);
	    $changedev_str=$changedev_count.'';
	    foreach($change_buf as $chdev){
	        $ch_psn = $chdev['old_psn'];
	        $ch_devid=$chdev['old_devid'];
	        $new_devid=$chdev['new_devid'];
	        //$dev=M('changeidlog')->where(array('id'=>$chdev['id']))->save(array('flag'=>2));
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
}