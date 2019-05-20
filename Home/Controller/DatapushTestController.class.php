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

		$str = "323031393035313331353034303032323038363735363330300000e0020250000000000000000000560000e02a5b02040002433224436224466224ffffffffffffffffff0000e02b4e02040202444224447224477224ffffffffffffffffff0000e03b4f02048102434224446224477224ffffffffffffffffff0000e03c4202040102443224418224464224ffffffffffffffffff0000e0584a02048102434224437224486224ffffffffffffffffff0000e0594602040002453224438224477224ffffffffffffffffff0000e0614802040002423224456224479224ffffffffffffffffff0000e0934102048102454224437224476224ffffffffffffffffff0000e0945002040102443224417224465224ffffffffffffffffff0000e09d5402040002434224446224477224ffffffffffffffffff0000e09e4f02048102423224436224467224ffffffffffffffffff0000e0a04f020481034392234062244232244842244602ffffffff0000e0a15602048102442224447224458224ffffffffffffffffff0000e0a24202040002455224447224477224ffffffffffffffffff0000e0a34a02048102444224437224486224ffffffffffffffffff0000e0a44d02048102443224436224466224ffffffffffffffffff0000e0a55002048102443224447224467224ffffffffffffffffff0000e0a65102040002446224457224487224ffffffffffffffffff0000e0a74902040002434224446224477224ffffffffffffffffff0000e0a84d02040102443224437224466224ffffffffffffffffff0000e0a94a02040002432224426224455224ffffffffffffffffff0000e0aa6002040002445224447224487224ffffffffffffffffff0000e0ac4b02040102433224446224457224ffffffffffffffffff0000e0af4f02040002424224455224467224ffffffffffffffffff0000e0b04802048102416224434224496224ffffffffffffffffff0000e0b14e02040102445224446224477224ffffffffffffffffff0000e0b24b02040002433224436224466224ffffffffffffffffff0000e0d14402048102432224436224456224ffffffffffffffffff0000e0d24402048202434224436224476224ffffffffffffffffff0000e0d345020400034212243842244302244652244202ffffffff0000e0d44602048102422224414224443224ffffffffffffffffff0000e0d54c02040002431224425224434224ffffffffffffffffff0000e0d65902040102391224422224434224ffffffffffffffffff0000e0d74d02040002409223392224422224ffffffffffffffffff0000e0d84802040102419223414224424224ffffffffffffffffff0000e0d94502048102432224436224466224ffffffffffffffffff0000e0da4502040002422224405224452224ffffffffffffffffff0000e0dd5a02040002401224402224432224ffffffffffffffffff0000e0ea4702040102390224412224424224ffffffffffffffffff0000e0ec4702048102422224445224457224ffffffffffffffffff0000e0ed4702048102393224422224464224ffffffffffffffffff0000e10a4602040002420224404224423224ffffffffffffffffff0000e10c5a02040002401224412224433224ffffffffffffffffff0000e10e4602040002380224401224422224ffffffffffffffffff0000e10f4502048102400224373224430224ffffffffffffffffff0000e1114602040002422224436224456224ffffffffffffffffff0000e1145102048102429223434224425224ffffffffffffffffff0000e1154e02040002419223433224425224ffffffffffffffffff0000e1174a02040002392224411224454224ffffffffffffffffff0000e1184902048102401224403224453224ffffffffffffffffff0000e1194a02048102400224384224442224ffffffffffffffffff0000e11a4502048102432224437224468224ffffffffffffffffff0000e11b4402040002423224445224456224ffffffffffffffffff0000e11c4502040002434224445224465224ffffffffffffffffff0000e11d4602040002433224444224445224ffffffffffffffffff0000e11e4a02040102454224446224455224ffffffffffffffffff0000e11f5002048102444224435224454224ffffffffffffffffff0000e1214b02040002443224425224453224ffffffffffffffffff0000e1224402040002423224426224476224ffffffffffffffffff0000e1234502040202434224436224466224ffffffffffffffffff0000e12b4702048102445224435224475224ffffffffffffffffff0000e12c4a02048102433224454224446224ffffffffffffffffff0000e1314c02048102414224002a244502a0ffffffffffffffffff0000e13d4c02048102414224423224463224ffffffffffffffffff0000e1454802048102422224434224445224ffffffffffffffffff0000e1464402048102444224438224487224ffffffffffffffffff0000e1474602040002442224417224453224ffffffffffffffffff0000e1494d02040102433224414224453224ffffffffffffffffff0000e14b5002048102433224455224446224ffffffffffffffffff0000e14c4a02048102423224424224454224ffffffffffffffffff0000e14d4702048102423224436224477224ffffffffffffffffff0000e14e4802048102433224446224467224ffffffffffffffffff0000e14f4b02048102431224436224435224ffffffffffffffffff0000e1505202040102432224435224445224ffffffffffffffffff0000e1515002048102433224455224456224ffffffffffffffffff0000e1524c02048102441224436224434224ffffffffffffffffff0000e1534902048102413224433224455224ffffffffffffffffff0000e1554902040102420224414224434224ffffffffffffffffff0000e1584d02048102421224424224434224ffffffffffffffffff0000e1594a02048102422224424224444224ffffffffffffffffff0000e15a4602048102401224413224444224ffffffffffffffffff0000e15d4802048102442224406224453224ffffffffffffffffff0000e15e4402040102430224406224433224ffffffffffffffffff0000e15f4a02048102410224424224435224ffffffffffffffffff0000e1604a02040002423224415224464224ffffffffffffffffff0000e1704c02048102400224423224435224ffffffffffffffffff00045201";

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
    		//$saveSql=M('bdevice')->where(array('id'=>$bsnint,'psnid'=>$psnid))->save(array('version'=>$bvs));	
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
   	
   	$re_devs =D('device')->where(array('psn'=>$psnid,'re_flag'=>1))->limit(0,64)->select();
   	
   	echo "count:";
   	var_dump($count);
    for($i=0 ; $i < $count ; $i++){
    	$snstr   =substr($data, $i*$DATA_LEN,$CSN_LEN*2);
    	//var_dump($snstr);
    	$snint = hexdec($snstr)&0x1fff;;	//从十六进制转十进制
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
    	$snint=$snint;
    	$info    =D('device')->where(array('devid'=>$snint,'psn'=>$psnid))->find();//查询devce是否存在
    	//var_dump($info);
    	
    
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
		    
				}
			}
			//var_dump($temp1);
    	//var_dump($temp2);
    }

    //未解包比对
    
    $len=strlen($str);
    $crc=substr($str,$len-$CRC_LEN*2);//收到发来的crc
    var_dump($crc);
    $crc=hexdec($crc);
    var_dump($crc);

    $sum=0;
    $len = strlen($str);
		for($i=0 ; $i < $len/2-$CRC_LEN;$i++)
		{
			$value = hexdec(substr($str, $i*2,2));
			//var_dump($value);
			$sum+=$value;
		}
		var_dump($sum);
		$sum=$sum&0xffffffff;
		var_dump($sum);

  	foreach($re_devs as $redev){
  			$devid_tmp=$redev['devid'];
  			foreach($devbuf as $devre){
  				if($devre==$devid_tmp){
						$devres[]=$devre;
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

		//var_dump($rate);
		if($crc==$sum){
			echo "OK1".date('YmdHis').$delay_time.$rate.$footer.$devres_str;
		}else{
			echo "OK2".date('YmdHis').$delay_time.$rate.$footer.$devres_str;
		}
		exit;
	}
	
	public function pushnewsubV36(){
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



		//$str = "323031393035313331353034303032323038363735363330300000e0020250000000000000000000090000c00625020482034352244722255032256202266302ffffffff0000c0092502048102510225502226612226ffffffffffffffffff0000e0db3322140102422224424224455224ffffffffffffffffff0000e0e93712140102411224403224432224ffffffffffffffffff0000e10b3962140102423224414224453224ffffffffffffffffff0000e10d3d32140302410224393224421224ffffffffffffffffff0000e1103722140102411224435224456224ffffffffffffffffff0000e1133922140102402224413224444224ffffffffffffffffff0000e14a4792140202442224445224435224ffffffffffffffffff00007239";

		$changedev_str="99";
		for($i=0;$i<99;$i++){
			$changedev_str=$changedev_str."000040001"."000050108";
		}
		$blacklist_str="99";
		for($i=0;$i<99;$i++){
			$blacklist_str=$blacklist_str."000060005";
		}

		$header='OK1';
		//var_dump($rate);
  	{
        $imgDir = "lora_req36Test/";
        if(!file_exists("lora_req36Test")){
               mkdir("lora_req36Test");
        }
        if(!file_exists($imgDir)){
           mkdir($imgDir);
        }
        //要生成的图片名字
        $ctime = date("Ymd_His_").mt_rand(10, 99);
        $lnewFilePath = $imgDir.$ctime."/";//图片存入路径
        if(!file_exists($lnewFilePath)){
        	mkdir($lnewFilePath);
        }
        			
        $filename = date("Ymd_His_").mt_rand(10, 99).".bmp"; //新图片名称
        $newFilePath = $lnewFilePath.$filename;//图片存入路径
        $newFile = fopen($newFilePath,"w"); //打开文件准备写入
        fwrite($newFile,$header.$changedev_str.$blacklist_str);
        fclose($newFile); //关闭文件
         
  	}
    			
		echo $header.$changedev_str.$blacklist_str;

		exit;
		
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
    //var_dump($btsn);
    //var_dump($psn);
    
    //$psn = $bdevinfo['psn'];
    //var_dump($bsnint);

    $psninfo = D('psn')->where(array('tsn'=>$btsn,'sn'=>$psn))->find();
    if($psninfo){
    	$psnid=$psninfo['id'];
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
						'station'=>1301,
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
   	
   	$change_devs =D('changeidlog')->where(array('psnid'=>$psnid))->limit(0,32)->select();

   	//var_dump($change_devs);
    for($i=0 ; $i < $count ; $i++){
    	$snstr   =substr($data, $i*$DATA_LEN,$CSN_LEN*2);
    	//var_dump($snstr);
    	$snint = hexdec($snstr)&0x1fff;	//从十六进制转十进制
    	$dev_psn = hexdec($snstr)>>13;
    	$dev_sn = hexdec($snstr);
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

			$changeid_find=false;
			foreach($change_devs as $ch_dev){
				if($ch_dev['old_psn']==$dev_psn
						&&$ch_dev['old_devid']==$snint){
					$changeid_find=true;
					if($ch_dev['flag']==1||$ch_dev['flag']==2){
						$tmp_dev=array('old_psn'=>$dev_psn,
													'old_devid'=>$snint,
													'new_devid'=>$ch_dev['new_devid']
												);
						//var_dump($tmp_dev);						
						$change_buf[]=$tmp_dev;
					}
				}
			}
			if($changeid_find==false)
			{
					$havedev =D('changeidlog')->where(array('old_psn'=>$dev_psn,'old_devid'=>$snint))->find();
					if(empty($havedev)){
						$tmp_dev=array('psnid'=>$psnid,
								'old_psn'=>$dev_psn,
								'old_devid'=>$snint,
								'sid'=>$sid,
							);
						//$ret =D('changeidlog')->add($tmp_dev);
					}	
					//var_dump($ret);
				}
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
    	$snint=$snint;
    	//$info    =D('device')->where(array('devid'=>$snint,'psn'=>$psnid))->find();//查询devce是否存在
    	//var_dump($info);
    	
    	//if(empty($info)){
    			//continue;
    	//}
    
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
		    	
		    	$acc_value=D('access')->where(array('time'=>$up_time,'psn'=>$psnid,'devid'=>$snint))->find();
		    	if(empty($acc_value)){
					}
					$acc1301=D('access1301')->where(array('time'=>$up_time,'psn'=>$dev_psn,'devid'=>$snint))->find();
					var_dump(date('Y-m-d H:i:s',$up_time));
					//var_dump($acc1301);
					if(empty($acc1301)){
			  		$access=D('access1301')->add(array(
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
					  		'sid'=>$sid,
					  		'time' =>$up_time,
					  	));
					}else{
						if($j==($vaild-1)&&count($blacklist)<64){
							//var_dump($acc1301);
							$blpsn_str=str_pad($dev_psn,5,'0',STR_PAD_LEFT).str_pad($snint,4,'0',STR_PAD_LEFT);
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
					}
					
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
		    
		    	
					$tacc_value=D('taccess')->where(array('time'=>$up_time,'psn'=>$psnid,'devid'=>$snint))->find();

					if(empty($tacc_value)){
					   $access=D('taccess')->add(array(
					   	  'psn'=>$psnid,
					  		'devid'=>$snint,
					  		'temp1'=>$temp1,
					  		'temp2'=>$temp2,
				  			'env_temp'=>$temp3,
				  			'sign'=>$sign,
					  		'cindex'=>$cindex,
					  		'lcount'=>$lcount,
					  		'delay'=>$delay,
					  		'time' =>$up_time,
					  	));

				  	 	$saveSql=M('device')->where(array('devid'=>$snint,'psn'=>$psnid))->save(array(
				  	 																																	'battery'=>$battery,
																																				  	 	'dev_state'=>$state,
																																				  	 	'version'=>$cvs)
																																				  	 	);
					}
					$acc1301=D('access1301')->where(array('time'=>$up_time,'psn'=>$dev_psn,'devid'=>$snint))->find();
					if(empty($acc1301)){
			  		$access=D('access1301')->add(array(
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
					  		'sid'=>$sid,
					  		'time' =>$up_time,
					  	));
					}else{
						if($j==0&&count($blacklist)<64){
							$blpsn_str=str_pad($dev_psn,5,'0',STR_PAD_LEFT).str_pad($snint,4,'0',STR_PAD_LEFT);
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
					}
					
				}
			}
			//var_dump($temp1);
    	//var_dump($temp2);
    }

    //未解包比对
    
    $len=strlen($str);
    $crc=substr($str,$len-$CRC_LEN*2);//收到发来的crc
    var_dump($crc);
    $crc=hexdec($crc);
    var_dump($crc);

    $sum=0;
    $len = strlen($str);
		for($i=0 ; $i < $len/2-$CRC_LEN;$i++)
		{
			$value = hexdec(substr($str, $i*2,2));
			//var_dump($value);
			$sum+=$value;
		}
		var_dump($sum);
		$sum=$sum&0xffff;
		var_dump($sum);
		$changedev_count=count($change_buf);
		$changedev_count=str_pad($changedev_count,2,'0',STR_PAD_LEFT);
		$changedev_str=$changedev_count.'';
		foreach($change_buf as $chdev){
				$ch_psn = $chdev['old_psn'];
				$ch_devid=$chdev['old_devid'];
				$new_devid=$chdev['new_devid'];
				$dev=M('changeidlog')->where(array('old_psn'=>$ch_psn,'old_devid'=>$ch_devid,'new_devid'=>$new_devid))->save(array('flag'=>2));
				//var_dump($chdev);
				//var_dump($psn);
				//$olddev=($ch_psn<<13)|($ch_devid);
				//$newdev=($psn<<13)|($new_devid);

				//$olddev=base_convert($olddev, 10, 16);
				//$newdev=base_convert($newdev, 10, 16);
				//var_dump($olddev_str);
				//var_dump($newdev);
				$olddev_str=str_pad($ch_psn,5,'0',STR_PAD_LEFT).str_pad($ch_devid,4,'0',STR_PAD_LEFT);
				$newdev_str=str_pad($psn,5,'0',STR_PAD_LEFT).str_pad($new_devid,4,'0',STR_PAD_LEFT);
				$changedev_str=$changedev_str.$olddev_str.$newdev_str;
		}
		$blacklist_str=str_pad(count($blacklist),2,'0',STR_PAD_LEFT);
		foreach($blacklist as $name){
			$blacklist_str=$blacklist_str.$name;
		}
		exit;
	}
}