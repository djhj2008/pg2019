<?php
namespace Home\Controller;
use Think\Controller;
class DatapushV60Controller extends Controller {

	public function pushnewsubV30(){
		// $checksum=crc32("Thequickbrownfoxjumpedoverthelazydog.");
		// printf("%u\n",$checksum);
		
		$TIME_LEN = 18;//时间字符长度
		$DELAY_START = 10;
		$HOUR_DELAY_LEN = 2;
		$MIN_DELAY_LEN = 2;
		$FREQ_LEN = 1;
		
		$TIME_DIFF_LEN=3;
		
		$BTSN_LEN  = 10;//统编10位1类型,2-4国家编码,5-10区域编码
		$BDSN_LEN  = 4;//BS字符长度
		$BSN_LEN  = $BTSN_LEN+$BDSN_LEN;//BS字符长度
		$BVS_LEN  = 1; //B device version
		
    $BRSSI_MAX_LEN = 1;
    $BRSSI_COUNT = 10;
    $BRSSI_SN_LEN = 1;
    $BRSSI_SIGN_LEN = 1;
		$BRSSI_LEN = $BRSSI_MAX_LEN+$BRSSI_COUNT*($BRSSI_SN_LEN+$BRSSI_SIGN_LEN);
		
		$CDATA_START = $TIME_LEN+$BSN_LEN+$BVS_LEN+$BRSSI_LEN;
		
		$COUNT_LEN =2; //data的条数
		$CSN_LEN  =4;//设备字符长度
		$SIGN_LEN =1;//信号
		$CVS_LEN =1;//client version
		$STATE_LEN  =1;//state
		$DELAY_LEN  =1;//delay
		$VAILD_LEN  =1;//有效值个数
		
		$SENS_LEN  =1;//有效值个数
		
		$VALUE_LEN = 10;//data中每个长度
		$COUNT_VALUE = 4;

		$DATA_LEN = ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN+$SENS_LEN)*2+$VALUE_LEN*$COUNT_VALUE; //一条data长度
		
		//var_dump($DATA_LEN);
		
		$CRC_LEN  = 4;//校验码
		$post      =file_get_contents('php://input');//抓取内容
    $strarr    =unpack("H*", $post);//unpack() 函数从二进制字符串对数据进行解包。
    $str       =implode("", $strarr);

		//$str = "323032303037323331323030313034313239313038363735363330300000a00a05500000000000000000000c0000a0036173040104796227758227752227796227730228765227ffffff0000a0044c73040104766227766227766227766227764227735227ffffff0000a00538740701210491333800009023380000926338000092633800000000a0074573040104798227795227758227797227787227779227ffffff0000a00951740500310477522700007942270000797227000078522700000000a00a387407002a0472122700007212270000731227000073122700000000a00b607407013b0479622700047952270004796227000479422700040000a00c4b7407013d0470922600007092260000709226000070922600000000a00d3f740701320479622700007962270000797227000079722700000000a00e507407013d0479622700008012270000802227000077922600000000a00f427407003e0458722500005332250000534225000051122500000000a01037740702280483222800008212280000811228000083222800000000556b";
    $sndir = substr($str, ($TIME_LEN+$BTSN_LEN)*2,$BDSN_LEN*2);
    $sn_footer = hexdec($sndir)&0x1fff;
    $sn_header = hexdec($sndir)>>13;
    $logbase="lora_log/backupV6030/";
    $logerr="lora_log/errorV6030/";
    $logreq="lora_log/reqV6030/";
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
    $psninfo = D('psn')->where(array('sn'=>$psn))->find();
    //$psninfo = D('psn')->where(array('sn'=>$psn))->find();
    if($psninfo){
    	$psnid=$psninfo['id'];
    	$delay_up=$psninfo['delay_up'];
    	$delay_up=str_pad($delay_up,2,'0',STR_PAD_LEFT);
    	$retry_up=$psninfo['retry_up'];
    	$retry_up=str_pad($retry_up,1,'0',STR_PAD_LEFT);
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
	 	$saveRssi=D('brssi')->add($rssi);

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
			$step_sleeptime=$bdevinfo['step_sleeptime'];
			$step_sleeptime=str_pad($step_sleeptime,3,'0',STR_PAD_LEFT);
			
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
	    	//dump($snint);
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
	    		$tempstr	 =	substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1十六进制字符
					$sens=0-hexdec($sensstr);
					$step_list[$snint]['state']=$state;
	    	}else{
					$sens=0;
					$vaildstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
	    		$tempstr   = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1十六进制字符
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
	    		if($freq>1){
		    		$up_time = $start+$interval*$j+$interval*($freq-$vaild);
	    		}else{
	    			$up_time = $end+$interval*$j+$interval*($freq-$vaild);
	    		}
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
		
  	$mydb='access_base';
    $user=D($mydb);
		$access1=$user->addAll($accadd_list);
    		
    $user2=D('taccess');
		$access2=$user2->addAll($accadd_list2);

		//$user3=D('device');
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
						$dev1=M('device')->where(array('devid'=>$devid,'psn'=>$dev_psn))->save($mysave);
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
			
		$body=$header.$delay_time.$rate.$log_flag.$dump_rate.$step_rate.$step_setup.$step_sleeptime.$delay_up.$retry_up.$change_str.$footer.$stepres_str.$devres_str;
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


	public function pushnewsubV47()
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

	    
	    $BRSSI_MAX_LEN = 1;
	    $BRSSI_COUNT = 10;
	    $BRSSI_SN_LEN = 1;
	    $BRSSI_SIGN_LEN = 1;
			$BRSSI_LEN = $BRSSI_MAX_LEN+$BRSSI_COUNT*($BRSSI_SN_LEN+$BRSSI_SIGN_LEN);
			
	    $CDATA_START = $TIME_LEN + $BSN_LEN + $BVS_LEN + $BRSSI_LEN;

	    $COUNT_LEN = 2; //data的条数
	    $CSN_LEN = 4;//设备字符长度
	    $SIGN_LEN = 1;//信号
	    $CVS_LEN = 1;//client version
	    $STATE_LEN = 1;//state
	    $DELAY_LEN = 1;//delay
	    $VAILD_LEN = 1;//有效值个数

			$SENS_LEN  =1;//有效值个数
			
			$VALUE_LEN = 10;//data中每个长度
			$VALUE_LEN_NEW = 11;//data中每个长度
			$COUNT_VALUE = 4;

			$DATA_LEN = ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN+$SENS_LEN)*2+$VALUE_LEN*$COUNT_VALUE; //一条data长度
			
	    $CRC_LEN = 4;//校验码
	    $post = file_get_contents('php://input');//抓取内容
	    $strarr = unpack("H*", $post);//unpack() 函数从二进制字符串对数据进行解包。
	    $str = implode("", $strarr);


	    //$str = "323032303037323431353030313034313038363735363330300000a00a0550000000000000000000000000000000000000000000270225000047622400004312240000418223000000008007425405002b045522260000562226000056322600005522260000000080055d5405002d04363223000035122300003512230000348222000000008005595405002d04363223000035122300003512230000348222000000008005285405002d04363223000035122300003512230000348222000000008006415405413b04449223000045022400004492230000450224000000008007345405002e04552226000052222600005222260000490226000000010adb00ffffffffffff000080064554058000013562230000ffffffffffffffffffffffffffffff000080064354058000013562230000ffffffffffffffffffffffffffffff000080055a5445002b043622230000350223000033822200003492220000000080055a5445002b04362223000035022300003382220000349222000000008005285445002b04362223000035022300003382220000349222000000008005625445002b043622230000350223000033822200003492220000000080064654058000014682240000ffffffffffffffffffffffffffffff000080064754057f3b014682240000ffffffffffffffffffffffffffffff000080072f1455002c045112260000501226000050122600005002260000000080055954050026043262220000340223000033722200003382220000000080052554050026043262220000340223000033722200003382220000000080055b5405002604326222000034022300003372220000338222000000008005615405002604326222000034022300003372220000338222000000008006435405413b014762240000ffffffffffffffffffffffffffffff000080075e54050078044902260000500226000049022600005002260000000080073654050078044902260000500226000049022600005002260000000080055a5405002e043622230000362223000036322300003522230000000080055a5405002e04362223000036222300003632230000352223000000008005235405002e04362223000036222300003632230000352223000000008006415405003b04487224000050922400005102250000498224000000008007395405412b045412260000542226000054222600005322260000000080055b5405412804363223000037622300003632230000362223000000008005585405412804363223000037622300003632230000362223000000008005255405412804363223000037622300003632230000362223000000008005615405412804363223000037622300003632230000362223000000008006415405413a04500225000047622400004312240000418223000000008007425405002b045522260000562226000056322600005522260000000080055d5405002d04363223000035122300003512230000348222000000008005595405002d04363223000035122300003512230000348222000000008005285405002d04363223000035122300003512230000348222000000008006415405413b04449223000045022400004492230000450224000000008007345405002e0455222600005222260000522226000049022600000000e4f8";
	    $sndir = substr($str, ($TIME_LEN + $BTSN_LEN) * 2, $BDSN_LEN * 2);
	    $sn_footer = hexdec($sndir) & 0x1fff;
	    $sn_header = hexdec($sndir) >> 13;
	    $logbase = "lora_log/backupV6047/";
	    $logerror = "lora_log/errorV6047/";
	    $logreq = "lora_log/reqV6047/";
			ob_clean();
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
	        fwrite($newFile, $str);
	        fclose($newFile); //关闭文件
	    }

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
	        {
	            $logdir = $logerror;
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
	            fwrite($newFile, $bsnint . "," . $sid);
	            fclose($newFile); //关闭文件

	        }
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
	        {
	            $logdir = $logerror;
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
	            fwrite($newFile, $btsn . "," . $psn . ' not find.');
	            fclose($newFile); //关闭文件
	        }
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
	    $saveRssi=D('brssi')->add($rssi);

	    $count = substr($str, $CDATA_START * 2, $COUNT_LEN * 2);//2为解包后的倍数
	    $count = hexdec($count);//从十六进制转十进制
	    $data = substr($str, ($CDATA_START + $COUNT_LEN) * 2, $count * $DATA_LEN);//取出data
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

			if($crc==$sum){
		    for ($i = 0; $i < $count; $i++) {
		        $snstr = substr($data, $i * $DATA_LEN, $CSN_LEN * 2);
		        $snint = hexdec($snstr) & 0x1fff;    //从十六进制转十进制
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
			    		$tempstr	 =	substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$SENS_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1十六进制字符
							$sens=0-hexdec($sensstr);
							$step_list[$snint]['state']=$state;
			    	}else{
							$sens=0;
							$vaildstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
			    		$tempstr   = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1十六进制字符
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

		        //$tempstr = substr($data, $i * $DATA_LEN + ($CSN_LEN + $SIGN_LEN + $CVS_LEN + $STATE_LEN + $DELAY_LEN + $VAILD_LEN) * 2, $VALUE_LEN * $COUNT_VALUE);//temp1十六进制字符

						if($vaild>4){
							continue;
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

	    if(strlen($psn_err_log)>0)
	    {
	        $logdir = $logerror;
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
	        fwrite($newFile, $psn_err_log);
	        fclose($newFile); //关闭文件
	    }

	    foreach ($psn_list as $psn_buf){
	        $psn_buf_psnid=$psn_buf['psnid'];
	        $psn_buf_psn=$psn_buf['psn'];
	        if(count($psn_buf['devid'])>0){
	            $wheredev['devid']=array('in',$psn_buf['devid']);
	            $curdb1301='access1301_base';
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

    	$mydb='access_base';
	    $user=D($mydb);
	    $ret=$user->addAll($accadd_list);

			$mydb1301='access1301_base';
	    $user1301=D($mydb1301);
	    $ret=$user1301->addAll($acc1301addall);
			
	    $tuser=D('taccess');
	    $ret=$tuser->addAll($accadd_list2);
	  
	    $chuser=D('changeidlog');
	    $ret=$chuser->addAll($change_add);

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
      	$dev=M('changeidlog')->where($where_ch_dev)->save(array('flag'=>2));
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
	        if(!file_exists($logdir)) {
	            mkdir($logdir);
	        }
	        $logdir = $logdir.date('Y-m-d',time()).'/';
	        if(!file_exists($logdir)){
	            mkdir($logdir);
	        }

	        $filename = date("Ymd_His_").mt_rand(10, 99).".log"; //新图片名称
	        $newFilePath = $logdir.$filename;//图片存入路径
	        $newFile = fopen($newFilePath,"w"); //打开文件准备写入
	        fwrite($newFile,$header.$changedev_str.$blacklist_str);
	        fclose($newFile); //关闭文件

	    }
  
	    echo $header.$changedev_str.$blacklist_str;
	    exit;
	}  

	public function pushlog()
	{

	    $post = file_get_contents('php://input');//抓取内容
	    $sid = $_GET['sid'];
	    $sn_footer = (int)$sid & 0x1fff;
	    $sn_header = (int)$sid >> 13;
	    $logbase = "lora_log/syslogV60/";

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
	
}