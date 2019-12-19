<?php
namespace Home\Controller;
use Think\Controller;
class DatapushController extends Controller {
	public function pushnewsub(){
		// $checksum=crc32("Thequickbrownfoxjumpedoverthelazydog.");
		// printf("%u\n",$checksum);
		
		$TIME_LEN = 14;//时间字符长度
		$DELAY_START = 10;
		$HOUR_DELAY_LEN = 2;
		$MIN_DELAY_LEN = 2;
		
		$BSN_LEN  = 4;//BS字符长度
		$BVS_LEN  = 1; //B device version
		
		$CDATA_START = $TIME_LEN+$BSN_LEN+$BVS_LEN;
		
		$COUNT_LEN =2; //data的条数
		$CSN_LEN  =2;//设备字符长度
		$CVS_LEN =1;//client SN
		$STATE_LEN  =1;//设备字符长度
		
		
		$DELAY_LEN  =1;//设备字符长度
		$VALUE_LEN = 4;//data中每个长度
		$COUNT_VALUE = 2;
				
		$DATA_LEN = $CSN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VALUE_LEN*$COUNT_VALUE; //一条data长度
		
		$CRC_LEN  = 4;//校验码
		$post      =file_get_contents('php://input');//抓取内容
    $strarr    =unpack("H*", $post);//unpack() 函数从二进制字符串对数据进行解包。
    $str       =implode("", $strarr);
    {
          $imgDir = "lora_backup/";
          if(!file_exists("lora_backup")){
                 mkdir("lora_backup");
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
          fwrite($newFile,$str);
          fclose($newFile); //关闭文件
    }
    
    //$str ="323031363034303830393033313000000002060000000002ce";
    //$str= "32303138313131333137303330300000200204000600050204840000810900009df7000602048300007ec200007bbe00090204830000b3740000b3b9000a0414846666824100e08842000b04048200007b3e000079a9000d0404830000812700007807000013d0";
    //print_r($str);
    //var_dump($str);
    $sid =  (int)$_GET['sid']&0x1fff;
    //var_dump($sid);
    $bsnstr   =substr($str, $TIME_LEN*2,$BSN_LEN*2);
    $bsnint = hexdec($bsnstr)&0x1fff;
    $psn = hexdec($bsnstr)>>13;
    //var_dump($bsnint);
    //var_dump($psn);
    
    //$psn = $bdevinfo['psn'];
    //var_dump($bsnint);
    if($bsnint!=$sid){
    	echo "OKE".date('YmdHis')."0010";
    	//$bsnint = $sid;
    	exit;
    }
		$bversion   =substr($str, ($TIME_LEN+$BSN_LEN)*2,$BVS_LEN*2);
		$bvs = hexdec($bversion);
		//var_dump($bvs);
    $bdevinfo    =D('bdevice')->where(array('id'=>$bsnint,'psn'=>$psn,'dev_type'=>$type))->find();
    if($bdevinfo){
    	$uptime=$bdevinfo['uptime'];
    	//var_dump($uptime);
    	$rate = $bdevinfo['rate'];
    	$ast_flag = $bdevinfo['assert_flag'];
    	$delay_time  = str_pad($uptime,4,'0',STR_PAD_LEFT);
			$url_flag = $bdevinfo['url_flag'];
			$url = $bdevinfo['url'];
			
    	if($bdevinfo['version']!=$bvs){
    		//var_dump($bdevinfo['version']);
    		$saveSql=M('bdevice')->where(array('id'=>$bsnint,'psn'=>$psn))->save(array('version'=>$bvs));	
    	}
    }else{
    	$delay_time = "0010";
    }
    $count     =substr($str,$CDATA_START*2,$COUNT_LEN*2);//2为解包后的倍数
    $count	   =hexdec($count);//从十六进制转十进制
    $data      =substr($str,($CDATA_START+$COUNT_LEN)*2,$count*$DATA_LEN*2);//取出data
    $env_temp = 0;
    $snint = 0;
    $battery = 0;
    //var_dump($count);
    
    $hour_delay =substr($str,$DELAY_START*2,$HOUR_DELAY_LEN*2);
    $hour_delay =(int)pack("H*",$hour_delay);
    $min_delay =substr($str,($DELAY_START+$HOUR_DELAY_LEN)*2,$HOUR_DELAY_LEN*2);
    $min_delay =(int)pack("H*",$min_delay);
    //var_dump($hour_delay);
    //var_dump($min_delay);
    $day_begin = strtotime(date('Y-m-d',time()));
    //var_dump(date('Y-m-d',time()));
    $hour_time = 60*60;
    $pre_time =3*60;
    $pre_hour =15*60;
    
    $now = time();
    $today = strtotime(date('Y-m-d',$now).'00:00:00');
    $now = $now-$today;
    
  	if($min_delay == 0){
      $real_time = ((int)(($now+$pre_hour)/($hour_delay*$hour_time))-1)*$hour_delay*$hour_time;
      $real_time = $today+$real_time;
      $real_time = strtotime(date('Y-m-d H',$real_time).':00:00');
    }else{
    	$real_time = ((int)(($now+$pre_time)/($min_delay*60)-1))*$min_delay*60;
    	$real_time = $today+$real_time;
      $real_time = strtotime(date('Y-m-d H:i',$real_time).':00');
    }
    //var_dump($real_time);
    //exit;
    
    for($i=0 ; $i < $count ; $i++){
    	$snstr   =substr($data, $i*$DATA_LEN*2,$CSN_LEN*2);
    	//var_dump($snstr);
    	$snint = hexdec($snstr);	//从十六进制转十进制
    	//var_dump($snint);
    	$cvsstr = substr($data, $i*$DATA_LEN*2+($CSN_LEN)*2,$CVS_LEN*2);
    	$stastr = substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN)*2,$STATE_LEN*2);
    	$destr  = substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN+$STATE_LEN)*2,$DELAY_LEN*2);
    	$cvs = hexdec($cvsstr);
    	$state =  hexdec($stastr);
    	$delay =  hexdec($destr);
    	//var_dump($cvs);
    	//var_dump($state);
    	//var_dump($delay);
    	$stmp = 0x07;
    	$stmp2 = 0x80;
    	$stmp3 = 0x10;
    	if(($state & $stmp2) == $stmp2){
    		$battery=1;
    	}
    	else{
    		$battery=0;
    	}
    	$ast = $state & 0x08;
    	$type = $state & $stmp3;
    	$state=$state & $stmp;
    		
    	//var_dump($ast);
    	//var_dump($state);
    	//var_dump($type);
    	if($type>0){
    		$type=1;
    	}
    	//var_dump($type);
    	$snint=$snint;
    	$info    =D('device')->where(array('devid'=>$snint,'psn'=>$psn))->find();//查询devce是否存在
    	//var_dump($info);
    	
    	if(!$info){
    		continue;
    	}
    	
    	$temp1str=substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VALUE_LEN*2);//temp1十六进制字符
    	$temp2str=substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VALUE_LEN)*2,$VALUE_LEN*2);
   	
    	if($type==0){
    		$temp1int=hexdec($temp1str);
    		$temp2int=hexdec($temp2str);
    		
	    	$temp1 = gettemp3($temp1int);
	    	$temp2 = gettemp3($temp2int);
	  		$access=D('access')->add(array(
	  				'psn'=>$psn,
			  		'devid'=>$snint,
			  		'temp1'=>$temp1,
			  		'temp2'=>$temp2,
			  		'delay'=>$delay,
			  		'env_temp'=>$env_temp,
			  		'time' =>$real_time,
			  	));
		  	 	$saveSql=M('device')->where(array('devid'=>$snint,'psn'=>$psn))->save(array(
		  	 																																	'battery'=>$battery,
																																		  	 	'dev_state'=>$state,
																																		  	 	'dev_assert'=>$ast,
																																		  	 	'version'=>$cvs)
																																		  	 	);
				}else{
			    	$temp1str=substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VALUE_LEN*2);//temp1十六进制字符
			    	$temp2str=substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VALUE_LEN)*2,$VALUE_LEN*2);
			    	//var_dump($temp1str);
			    	//var_dump($temp2str);
			   
			    	$temp1 = unpack("f*",pack('H*',$temp1str))[1];
			    	$temp2 = unpack("f*",pack('H*',$temp2str))[1];
			    	//var_dump($temp1);
			    	//var_dump($temp2);
			    	
			   $access=D('taccess')->add(array(
			   	  'psn'=>$psn,
			  		'devid'=>$snint,
			  		'temp1'=>$temp1,
			  		'temp2'=>$temp2,
			  		'delay'=>$delay,
			  		'env_temp'=>$env_temp,
			  		'time' =>$real_time,
			  	));
		  	 	$saveSql=M('device')->where(array('devid'=>$snint,'psn'=>$psn))->save(array(
		  	 																																	'battery'=>$battery,
																																		  	 	'dev_state'=>$state,
																																		  	 	'dev_assert'=>$ast,
																																		  	 	'version'=>$cvs)
																																		  	 	);
			    	
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
		//var_dump($sum);
		if($crc==$sum){
			echo "OK1".date('YmdHis').$delay_time.$ast_flag.$rate;
		}else{
			echo "OK2".date('YmdHis').$delay_time.$ast_flag.$rate;
		}
		exit;
	}
	
	public function pushnewsub5(){
		// $checksum=crc32("Thequickbrownfoxjumpedoverthelazydog.");
		// printf("%u\n",$checksum);
		
		$TIME_LEN = 15;//时间字符长度
		$DELAY_START = 10;
		$HOUR_DELAY_LEN = 2;
		$MIN_DELAY_LEN = 2;
		$FREQ_LEN = 1;
		
		$BSN_LEN  = 4;//BS字符长度
		$BVS_LEN  = 1; //B device version
		
		$CDATA_START = $TIME_LEN+$BSN_LEN+$BVS_LEN;
		
		$COUNT_LEN =2; //data的条数
		$CSN_LEN  =2;//设备字符长度
		$CVS_LEN =1;//client version
		$STATE_LEN  =1;//state
		$DELAY_LEN  =1;//delay
		$VAILD_LEN  =1;//有效值个数
		
		$VALUE_LEN = 5;//data中每个长度
		$COUNT_VALUE = 4;
		$DATA_LEN = $CSN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN+$VALUE_LEN*$COUNT_VALUE; //一条data长度
		
		//var_dump($DATA_LEN);
		
		$CRC_LEN  = 4;//校验码
		$post      =file_get_contents('php://input');//抓取内容
    $strarr    =unpack("H*", $post);//unpack() 函数从二进制字符串对数据进行解包。
    $str       =implode("", $strarr);
    {
          $imgDir = "lora_backup5/";
          if(!file_exists("lora_backup5")){
                 mkdir("lora_backup5");
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
          fwrite($newFile,$str);
          fclose($newFile); //关闭文件
    }
    //$str ="323031363034303830393033313000000002060000000002ce";
    //$str= "32303138313031383131303031303400006001030001000403047f020cfb40cfe50d1000d127ffffffffffffffffffff000011e3";
    //$str= "323031383130313931373031303034000060010500020004050400010ca500ca58ffffffffffffffffffffffffffffff000a05140001172a2157ffffffffffffffffffffffffffffffff000024fa";
    //print_r($str);
    //var_dump($str);
    $sid =  (int)$_GET['sid']&0x1fff;
    //var_dump($sid);
    $bsnstr   =substr($str, $TIME_LEN*2,$BSN_LEN*2);
    //var_dump($bsnstr);
    $bsnint = hexdec($bsnstr)&0x1fff;
    $psn = hexdec($bsnstr)>>13;
    //var_dump($bsnint);
    //var_dump($psn);
    
    //$psn = $bdevinfo['psn'];
    //var_dump($bsnint);
    if($bsnint!=$sid){
    	echo "OKE".date('YmdHis')."00101";
    	//$bsnint = $sid;
    	exit;
    }
		$bversion   =substr($str, ($TIME_LEN+$BSN_LEN)*2,$BVS_LEN*2);
		$bvs = hexdec($bversion);
		//var_dump($bvs);
    $bdevinfo    =D('bdevice')->where(array('id'=>$bsnint,'psn'=>$psn,'dev_type'=>$type))->find();
    if($bdevinfo){
    	$uptime=$bdevinfo['uptime'];
    	//var_dump($uptime);
    	$rate = $bdevinfo['rate'];
    	$ast_flag = $bdevinfo['assert_flag'];
    	$dev_freq = $bdevinfo['count'];
    	$delay_time  = str_pad($uptime,4,'0',STR_PAD_LEFT).$dev_freq;
    	if($bdevinfo['version']!=$bvs){
    		//var_dump($bdevinfo['version']);
    		$saveSql=M('bdevice')->where(array('id'=>$bsnint,'psn'=>$psn))->save(array('version'=>$bvs));	
    	}
    }else{
    	$dev_freq = 1;
    	$delay_time = "00101".$dev_freq;
    	
    }
    //echo "delay_time:";
		//var_dump($delay_time);
    $count     =substr($str,$CDATA_START*2,$COUNT_LEN*2);//2为解包后的倍数
    $count	   =hexdec($count);//从十六进制转十进制
    $data      =substr($str,($CDATA_START+$COUNT_LEN)*2,$count*$DATA_LEN*2);//取出data
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
   	
    for($i=0 ; $i < $count ; $i++){
    	$snstr   =substr($data, $i*$DATA_LEN*2,$CSN_LEN*2);
    	//var_dump($snstr);
    	$snint = hexdec($snstr);	//从十六进制转十进制
    	//echo "sn:";
    	//var_dump($snint);
    	$cvsstr = substr($data, $i*$DATA_LEN*2+($CSN_LEN)*2,$CVS_LEN*2);
    	$stastr = substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN)*2,$STATE_LEN*2);
    	$destr  = substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN+$STATE_LEN)*2,$DELAY_LEN*2);
    	$vaildstr  = substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
    	$cvs = hexdec($cvsstr);
    	$state =  hexdec($stastr);
    	$delay =  hexdec($destr);
    	$vaild = hexdec($vaildstr);
    	
    	//var_dump($cvs);
    	//var_dump($state);
    	//echo "vaild:";
    	//var_dump($vaild);
    	$stmp = 0x07;
    	$stmp2 = 0x80;
    	$stmp3 = 0x10;
    	if(($state & $stmp2) == $stmp2){
    		$battery=1;
    	}
    	else{
    		$battery=0;
    	}
    	$ast = $state & 0x08;
    	$type = $state & $stmp3;
    	$state=$state & $stmp;
    	
    	//var_dump($ast);
    	//var_dump($state);
    	//var_dump($type);
    	
    	if($type>0){
    		$type=1;
    	}
    	//echo "type:";
    	//var_dump($type);
    	$snint=$snint;
    	$info =D('device')->where(array('devid'=>$snint,'psn'=>$psn))->find();//查询devce是否存在
    	//var_dump($info);
    	
    	if(!$info){
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
	    
	    //echo "interval:";
	    //var_dump($interval);
    	for($j=0;$j < $vaild;$j++){
	    	$tempstr=substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2+$VALUE_LEN*2*$j,$VALUE_LEN*2);//temp1十六进制字符
	    	$temp1str = substr($tempstr,0,$VALUE_LEN);
	    	$temp2str = substr($tempstr,$VALUE_LEN,$VALUE_LEN);
	    	//$temp2str=substr($data, $i*$DATA_LEN*2+($CSN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VALUE_LEN)*2,$VALUE_LEN*2);
	   		//var_dump($temp1str);
	   		//var_dump($temp2str);
	    	//$real_time = strtotime(date('Y-m-d H:i',$real_time).':00');
	    	$up_time = $real_time-$interval*$freq+$interval*($j+1)+$interval*($freq-$vaild);
		    $up_time = strtotime(date('Y-m-d H:i',$up_time).':00');
		    
	    	if($type==0){
	    		$temp1int=hexdec($temp1str);
	    		$temp2int=hexdec($temp2str);
	    		
	    		//var_dump($temp1int);
		   		//var_dump($temp2int);
	    		
		    	$temp1 = gettemp3($temp1int);
		    	$temp2 = gettemp3($temp2int);
		    	
		    	$acc_value=D('access')->where(array('time'=>$up_time,'psn'=>$psn,'devid'=>$snint))->find();
		    	
		    	if(empty($acc_value)){
			  		$access=D('access')->add(array(
			  				'psn'=>$psn,
					  		'devid'=>$snint,
					  		'temp1'=>$temp1,
					  		'temp2'=>$temp2,
					  		'env_temp'=>255,
					  		'delay'=>$delay,
					  		'time' =>$up_time,
					  	));
				  	 	$saveSql=M('device')->where(array('devid'=>$snint,'psn'=>$psn))->save(array(
				  	 																																	'battery'=>$battery,
																																				  	 	'dev_state'=>$state,
																																				  	 	'dev_assert'=>$ast,
																																				  	 	'version'=>$cvs)
																																				  	 	);
					}
	
		
				}else{
			    	$temp1str1 = substr($tempstr,0,2);
			    	$temp1str2 = substr($tempstr,2,2);
			    	$temp2str1 = substr($tempstr,4,2);
						$temp2str2 = substr($tempstr,6,2);
			    	//var_dump($temp1str);
			    	//var_dump($temp2str);
			   		$temp1 = unpack("c*",pack('H*',$temp1str1))[1].".".hexdec($temp1str2);
			   		$temp2 = unpack("c*",pack('H*',$temp2str1))[1].".".hexdec($temp2str2);
			    	//$temp1 = unpack("f*",pack('H*',$temp1str))[1];
			    	//$temp2 = unpack("f*",pack('H*',$temp2str))[1];
			      //var_dump($temp1);
		   		  //var_dump($temp2);
					$tacc_value=D('taccess')->where(array('time'=>$up_time,'psn'=>$psn,'devid'=>$snint))->find();
		
		
					if(empty($tacc_value)){
					   $access=D('taccess')->add(array(
					   	  'psn'=>$psn,
					  		'devid'=>$snint,
					  		'temp1'=>$temp1,
					  		'temp2'=>$temp2,
				  			'env_temp'=>255,
					  		'delay'=>$delay,
					  		'time' =>$up_time,
					  	));
				  	 	$saveSql=M('device')->where(array('devid'=>$snint,'psn'=>$psn))->save(array(
				  	 																																	'battery'=>$battery,
																																				  	 	'dev_state'=>$state,
																																				  	 	'dev_assert'=>$ast,
																																				  	 	'version'=>$cvs)
																																				  	 	);
					}
				}
			}
			//var_dump($temp1);
    	//var_dump($temp2);
				
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
		//var_dump($sum);
		if($crc==$sum){
			echo "OK1".date('YmdHis').$delay_time.$ast_flag.$rate;
		}else{
			echo "OK2".date('YmdHis').$delay_time.$ast_flag.$rate;
		}
		exit;
	}
	
	public function pushnewsub10(){
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
		
		$CDATA_START = $TIME_LEN+$BSN_LEN+$BVS_LEN;
		
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
		//$str = "32303139303130343135303031303131303836373536333030000020030100060000200256217400014662244602ffffffffffffffffffffffffff0000200356217400014862244602ffffffffffffffffffffffffff0000200644217400014763345103ffffffffffffffffffffffffff0000200551217400014772244602ffffffffffffffffffffffffff0000200857217400014662244602ffffffffffffffffffffffffff0000200745217400015193344903ffffffffffffffffffffffffff00006002";
    {
          $imgDir = "lora_backup10/";
          if(!file_exists("lora_backup10")){
                 mkdir("lora_backup10");
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
          fwrite($newFile,$str);
          fclose($newFile); //关闭文件
           
    }
		if(strlen($str) < $CDATA_START){
    	echo "OK1".date('YmdHis')."00101";
    	//$bsnint = $sid;
    	exit;
		}
    $sid =  (int)$_GET['sid']&0x1fff;
    //var_dump($sid);
    $bsnstr   =substr($str, $TIME_LEN*2,$BSN_LEN*2);
    $btsnstr =substr($str, $TIME_LEN*2,$BTSN_LEN*2);
    $bdsnstr =substr($str, ($TIME_LEN+$BTSN_LEN)*2,$BDSN_LEN*2);
    $btsnstr=hex2bin($btsnstr);
    //var_dump($bsnstr);
    $bsnint = hexdec($bdsnstr)&0x1fff;
    $psn = hexdec($bdsnstr)>>13;
    //var_dump($bsnint);
    //var_dump($psn);
    
    //$psn = $bdevinfo['psn'];
    //var_dump($bsnint);
    //if($bsnint!=$sid){
    //	echo "OK1".date('YmdHis')."00101";
    //	$bsnint = $sid;
    //	exit;
    //}
		$bversion   =substr($str, ($TIME_LEN+$BSN_LEN)*2,$BVS_LEN*2);
		$bvs = hexdec($bversion);
		//var_dump($bvs);
    $bdevinfo    =D('bdevice')->where(array('id'=>$bsnint,'psn'=>$psn,'dev_type'=>$type))->find();
    if($bdevinfo){
    	$uptime=$bdevinfo['uptime'];
    	//var_dump($uptime);
    	$rate = $bdevinfo['rate'];
    	$dev_freq = $bdevinfo['count'];
    	$delay_time  = str_pad($uptime,4,'0',STR_PAD_LEFT).$dev_freq;
    	if($bdevinfo['version']!=$bvs){
    		//var_dump($bdevinfo['version']);
    		$saveSql=M('bdevice')->where(array('id'=>$bsnint,'psn'=>$psn))->save(array('version'=>$bvs));	
    	}
    	$url_flag = $bdevinfo['url_flag'];
			$url = $bdevinfo['url'];
			if($url_flag==1){
				$footer=$url_flag.$url;
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
    	$info    =D('device')->where(array('devid'=>$snint,'psn'=>$psn))->find();//查询devce是否存在
    	//var_dump($info);
    	
    	if(!$info){
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
		    		if($type==1&&$j==1){
			    		if($temp1str1==0){
			    			$temp1 = $temp1str2.".".$temp1str3;
			    		}else{
			    			$temp1 = $temp1str1.$temp1str2.".".$temp1str3;
			    		}	
		    		}else{
			    		$temp1str1=$temp1int&0x07;
			    		if($temp1str1==0){
			    			$temp1 = '-'.$temp1str2.".".$temp1str3;
			    		}else{
			    			$temp1 =  '-'.$temp1str1.$temp1str2.".".$temp1str3;
			    		}
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
		    	
		    	$acc_value=D('access')->where(array('time'=>$up_time,'psn'=>$psn,'devid'=>$snint))->find();
		    	
		    	if(empty($acc_value)){
			  		$access=D('access')->add(array(
			  				'psn'=>$psn,
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
				  	 	$saveSql=M('device')->where(array('devid'=>$snint,'psn'=>$psn))->save(array(
				  	 																																	'battery'=>$battery,
																																				  	 	'dev_state'=>$state,
																																				  	 	'version'=>$cvs)
																																				  	 	);
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
		    		if($type==1&&$j==1){
			    		if($temp1str1==0){
			    			$temp1 = $temp1str2.".".$temp1str3;
			    		}else{
			    			$temp1 = $temp1str1.$temp1str2.".".$temp1str3;
			    		}	
		    		}else{
			    		$temp1str1=$temp1int&0x07;
			    		if($temp1str1==0){
			    			$temp1 = '-'.$temp1str2.".".$temp1str3;
			    		}else{
			    			$temp1 =  '-'.$temp1str1.$temp1str2.".".$temp1str3;
			    		}
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
		    
					$tacc_value=D('taccess')->where(array('time'=>$up_time,'psn'=>$psn,'devid'=>$snint))->find();
		
		
					if(empty($tacc_value)){
					   $access=D('taccess')->add(array(
					   	  'psn'=>$psn,
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
				  	 	$saveSql=M('device')->where(array('devid'=>$snint,'psn'=>$psn))->save(array(
				  	 																																	'battery'=>$battery,
																																				  	 	'dev_state'=>$state,
																																				  	 	'version'=>$cvs)
																																				  	 	);
					}
				}
			}
			//var_dump($temp1);
    	//var_dump($temp2);
				
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
		//var_dump($sum);
		if($crc==$sum){
			echo "OK1".date('YmdHis').$delay_time.$rate.$footer;
		}else{
			echo "OK2".date('YmdHis').$delay_time.$rate.$footer;
		}
		exit;
	}
	
	public function pushnewsubV20(){
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
		//$str = "32303139303130343135303031303131303836373536333030000020030140014102420343044400060000200256217400014662244602ffffffffffffffffffffffffff0000200356217400014862244602ffffffffffffffffffffffffff0000200644217400014763345103ffffffffffffffffffffffffff0000200551217400014772244602ffffffffffffffffffffffffff0000200857217400014662244602ffffffffffffffffffffffffff0000200745217400015193344903ffffffffffffffffffffffffff00006002";
    {
          $imgDir = "lora_backup20/";
          if(!file_exists("lora_backup20")){
                 mkdir("lora_backup20");
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
          fwrite($newFile,$str);
          fclose($newFile); //关闭文件
           
    }
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
    	{
          $imgDir = "lora_error/";
          if(!file_exists("lora_error")){
                 mkdir("lora_error");
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
          fwrite($newFile,$bsnint.",".$sid);
          fclose($newFile); //关闭文件
           
    	}    	
    	exit;
    }
    $psninfo = D('psn')->where(array('tsn'=>$btsn,'sn'=>$psn))->find();
    if($psninfo){
    	$psnid=$psninfo['id'];
    }else{
    	echo "OKE".date('YmdHis')."00101";
    	{
          $imgDir = "lora_error/";
          if(!file_exists("lora_error")){
                 mkdir("lora_error");
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
			if($url_flag==1){
				$footer=$url_flag.$url;
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
    	
    	if(empty($info)){
					$savedev=array(
						'psn'=>$psnid,
						'shed'=>1,
						'fold'=>1,
						'flag'=>0,
						'state'=>0,
						's_count'=>0,
						'rid'=>$snint,
						'age'=>1,
						'devid'=>$snint,
					);
					$saveSql=M('device')->add($savedev);
    			//continue;
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
		    	
		    	$acc_value=D('access')->where(array('time'=>$up_time,'psn'=>$psnid,'devid'=>$snint))->find();
		    	
		    	if(empty($acc_value)){
			  		$access=D('access')->add(array(
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
				}
			}
			//var_dump($temp1);
    	//var_dump($temp2);
				
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
		$sum=$sum&0xffff;
		//var_dump($sum);
		if($crc==$sum){
			echo "OK1".date('YmdHis').$delay_time.$rate.$footer;
		}else{
			echo "OK2".date('YmdHis').$delay_time.$rate.$footer;
		}
		exit;
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
		
		$VALUE_LEN = 9;//data中每个长度
		$COUNT_VALUE = 4;

		$DATA_LEN = ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2+$VALUE_LEN*$COUNT_VALUE; //一条data长度
		
		//var_dump($DATA_LEN);
		
		$CRC_LEN  = 4;//校验码
		$post      =file_get_contents('php://input');//抓取内容
    $strarr    =unpack("H*", $post);//unpack() 函数从二进制字符串对数据进行解包。
    $str       =implode("", $strarr);

		//$str = "32303139303130343135303031303132303836373536333030000040010140014102420343044400060000200256217400014662244602ffffffffffffffffffffffffff0000200356217400014862244602ffffffffffffffffffffffffff0000200644217400014763345103ffffffffffffffffffffffffff0000200551217400014772244602ffffffffffffffffffffffffff0000200857217400014662244602ffffffffffffffffffffffffff0000200745217400015193344903ffffffffffffffffffffffffff00006002";
    $sndir = substr($str, ($TIME_LEN+$BTSN_LEN)*2,$BDSN_LEN*2);
    $sn_footer = hexdec($sndir)&0x1fff;
    $sn_header = hexdec($sndir)>>13;
    $logbase="lora_backup30/";
    $logerr="lora_error30/";
    $logreq="lora_req30/";
    
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
          			
        $filename = date("Ymd_His_").mt_rand(10, 99).".bmp"; //新图片名称
        $newFilePath = $logdir.$filename;//图片存入路径
        $newFile = fopen($newFilePath,"w"); //打开文件准备写入
        fwrite($newFile,$str);
        fclose($newFile); //关闭文件
           
    }

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
          			
          $filename = date("Ymd_His_").mt_rand(10, 99).".bmp"; //新图片名称
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
    	echo "OKE".date('YmdHis')."00101";
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
          			
          $filename = date("Ymd_His_").mt_rand(10, 99).".bmp"; //新图片名称
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
	    		  foreach($change_devs as $ch_dev){
	    		  	if($ch_dev['psnid']==$psnid&&
	    		  		$ch_dev['new_devid']==$snint){
	    		  			if($ch_dev['flag']==2){
	    		  				$change_dev_find=false;
	    		  				$ret=M('changeidlog')->where(array('id'=>$ch_dev['id']))->save(array('flag'=>3));
	    		  				break;
	    		  			}
	    		  		}
	    		  }
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
        $filename = date("Ymd_His_").mt_rand(10, 99).".bmp"; //新图片名称
        $newFilePath = $logdir.$filename;//图片存入路径
        $newFile = fopen($newFilePath,"w"); //打开文件准备写入
        fwrite($newFile,$header.$delay_time.$rate.$footer.$devres_str);
        fclose($newFile); //关闭文件 
  	}

		echo $header.$delay_time.$rate.$footer.$devres_str;
		exit;
	}

	public function pushnewsubV36()
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


	    //$str = "32303139313230323135303430303431303836373536333030000280010350000000000000000000220000a00e22430401043121132861144941146461166171178121170000a01019032401043291122891144761146851166431188061170000a011184304a0017751178201ffffffffffffffffffffffffff0000a0121b730400043381123231154911157071166871187961180000a0132a13040004300113308114478114632116626118855118000280972f03140c04099110079110098110110111093111132111000280b73183140204032110046110057110108110102111113111000280d62933140004186111167111165111176111168111177111000280e15123140104200112200112200112210112202112211112000280ef3b83140004078110060111110111145111147111187111000280f12e53140004011110013110023110066110069110099110000280f63123140104088110078110098110111111103111143111000281062e731401040781100981100891100991101121111231110002810f29831402040761100591100771101201111051111331110002811224731400041671111561111751111781111681111981110002811c35231402040231100371100781101111111241111451110002811e5373140104042110029110078110131111126111155111000281232a53340204021110015110045110109110093111123111000281222b231400049990090131100361100881101021111241110002812f2b53140204077110079110099110122111125111145111000281372a33140404033110046110057110109110113111124111000281392d031401040971100811110911111421111461111461110002815057831400043651133471133661133871133791133881130002815031831400043651133471133661133871133791133881130000e00405030400019903380004ffffffffffffffffffffffffff0000e0044d030400010623281002ffffffffffffffffffffffffff0000e0040c030400010623281002ffffffffffffffffffffffffff0002816533831404043771133561133651133661133571133761130000e00411030400010623281002ffffffffffffffffffffffffff0002816f30331402043331133231133321133441133341133441130002817c2a83140204266112266112267112288112289112299112000281ae2d63140204233112224112243112267112268112288112000281ef5d73140204132111119111188111222112215112244112000281ef30731402041321111191111881112221122151122441120000d876";
	    $sndir = substr($str, ($TIME_LEN + $BTSN_LEN) * 2, $BDSN_LEN * 2);
	    $sn_footer = hexdec($sndir) & 0x1fff;
	    $sn_header = hexdec($sndir) >> 13;
	    $logbase = "lora_backup36/";
	    $logerror = "lora_error36/";
	    $logreq = "lora_req36/";

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

	            $filename = date("Ymd_His_") . mt_rand(10, 99) . ".bmp"; //新图片名称
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

	            $filename = date("Ymd_His_") . mt_rand(10, 99) . ".bmp"; //新图片名称
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
	    $saveRssi=D('brssi')->add($rssi);

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
	                    $tmp_dev = array(
	                    		'id'=>$ch_dev['id'],
	                        'old_psn' => $dev_psn,
	                        'old_devid' => $snint,
	                        'new_devid' => $ch_dev['new_devid']
	                    );
	                    $change_buf[] = $tmp_dev;
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
	                	}
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
	                        $taccadd['psn'] == $dev_psn &&
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
	                    $acc1301add['psn'] == $dev_psn &&
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

	        $filename = date("Ymd_His_") . mt_rand(10, 99) . ".bmp"; //新图片名称
	        $newFilePath = $logdir . $filename;//图片存入路径
	        $newFile = fopen($newFilePath, "w"); //打开文件准备写入
	        fwrite($newFile, $psn_err_log);
	        fclose($newFile); //关闭文件
	    }


	    //dump($psn_list);
	    //dump($tpsn_list);
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
	        if(count($psn_buf['devid'])>0){
	            $wheredev['devid']=array('in',$psn_buf['devid']);
	            $acc_values=D('access')->where(array('psn'=>$psn_buf_psn))->where($wheredev)->where('time >='.$start.' and time<='.$end)->select();
	            //dump($acc_values);
	            foreach($accadd_list as $accadd){
	                if($accadd['psn']==$psn_buf_psn){
	                    $accadd_find=false;
	                    foreach($acc_values as $acc_value){
	                        if($acc_value['time']==$accadd['time']&&
	                            $acc_value['psnid']==$accadd['psnid']&&
	                            $acc_value['devid']==$accadd['devid'])
	                        {
	                            $accadd_find=true;
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
	    $user=D('access');
	    $ret=$user->addAll($accaddall);
	    //dump('accaddall list:');
	    //dump($accaddall);

	    $user1301=D('access1301');
	    $ret=$user1301->addAll($acc1301addall);
	    //dump('access1301 list:');
	    //dump($acc1301addall);

	    $tuser=D('taccess');
	    $ret=$tuser->addAll($taccadd_list);
	    //dump('taccess list:');
	    //dump($taccadd_list);
	    
	    $chuser=D('changeidlog');
	    $ret=$chuser->addAll($change_add);
	    
	    $changedev_count=count($change_buf);
	    $changedev_count=str_pad($changedev_count,2,'0',STR_PAD_LEFT);
	    $changedev_str=$changedev_count.'';
	    foreach($change_buf as $chdev){
	        $ch_psn = $chdev['old_psn'];
	        $ch_devid=$chdev['old_devid'];
	        $new_devid=$chdev['new_devid'];
	        $dev=M('changeidlog')->where(array('id'=>$chdev['id']))->save(array('flag'=>2));
	        $olddev_str=str_pad($ch_psn,5,'0',STR_PAD_LEFT).str_pad($ch_devid,4,'0',STR_PAD_LEFT);
	        $newdev_str=str_pad($psn,5,'0',STR_PAD_LEFT).str_pad($new_devid,4,'0',STR_PAD_LEFT);
	        $changedev_str=$changedev_str.$olddev_str.$newdev_str;
	    }
	    $blacklist_str=str_pad(count($blacklist),2,'0',STR_PAD_LEFT);
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

	        $filename = date("Ymd_His_").mt_rand(10, 99).".bmp"; //新图片名称
	        $newFilePath = $logdir.$filename;//图片存入路径
	        $newFile = fopen($newFilePath,"w"); //打开文件准备写入
	        fwrite($newFile,$header.$changedev_str.$blacklist_str);
	        fclose($newFile); //关闭文件

	    }

	    echo $header.$changedev_str.$blacklist_str;
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


	    //$str = "32303139313230323135303430303431303836373536333030000280010350000000000000000000220000a00e22430401043121132861144941146461166171178121170000a01019032401043291122891144761146851166431188061170000a011184304a0017751178201ffffffffffffffffffffffffff0000a0121b730400043381123231154911157071166871187961180000a0132a13040004300113308114478114632116626118855118000280972f03140c04099110079110098110110111093111132111000280b73183140204032110046110057110108110102111113111000280d62933140004186111167111165111176111168111177111000280e15123140104200112200112200112210112202112211112000280ef3b83140004078110060111110111145111147111187111000280f12e53140004011110013110023110066110069110099110000280f63123140104088110078110098110111111103111143111000281062e731401040781100981100891100991101121111231110002810f29831402040761100591100771101201111051111331110002811224731400041671111561111751111781111681111981110002811c35231402040231100371100781101111111241111451110002811e5373140104042110029110078110131111126111155111000281232a53340204021110015110045110109110093111123111000281222b231400049990090131100361100881101021111241110002812f2b53140204077110079110099110122111125111145111000281372a33140404033110046110057110109110113111124111000281392d031401040971100811110911111421111461111461110002815057831400043651133471133661133871133791133881130002815031831400043651133471133661133871133791133881130000e00405030400019903380004ffffffffffffffffffffffffff0000e0044d030400010623281002ffffffffffffffffffffffffff0000e0040c030400010623281002ffffffffffffffffffffffffff0002816533831404043771133561133651133661133571133761130000e00411030400010623281002ffffffffffffffffffffffffff0002816f30331402043331133231133321133441133341133441130002817c2a83140204266112266112267112288112289112299112000281ae2d63140204233112224112243112267112268112288112000281ef5d73140204132111119111188111222112215112244112000281ef30731402041321111191111881112221122151122441120000d876";
	    $sndir = substr($str, ($TIME_LEN + $BTSN_LEN) * 2, $BDSN_LEN * 2);
	    $sn_footer = hexdec($sndir) & 0x1fff;
	    $sn_header = hexdec($sndir) >> 13;
	    $logbase = "lora_backup46/";
	    $logerror = "lora_error46/";
	    $logreq = "lora_req46/";

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

	            $filename = date("Ymd_His_") . mt_rand(10, 99) . ".bmp"; //新图片名称
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

	            $filename = date("Ymd_His_") . mt_rand(10, 99) . ".bmp"; //新图片名称
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
	    $saveRssi=D('brssi')->add($rssi);

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
	                    /*
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
							        */
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
	                	}
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
	                        $taccadd['psn'] == $dev_psn &&
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
	                    $acc1301add['psn'] == $dev_psn &&
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

	        $filename = date("Ymd_His_") . mt_rand(10, 99) . ".bmp"; //新图片名称
	        $newFilePath = $logdir . $filename;//图片存入路径
	        $newFile = fopen($newFilePath, "w"); //打开文件准备写入
	        fwrite($newFile, $psn_err_log);
	        fclose($newFile); //关闭文件
	    }


	    //dump($psn_list);
	    //dump($tpsn_list);
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
	        if(count($psn_buf['devid'])>0){
	            $wheredev['devid']=array('in',$psn_buf['devid']);
	            $acc_values=D('access')->where(array('psn'=>$psn_buf_psn))->where($wheredev)->where('time >='.$start.' and time<='.$end)->select();
	            //dump($acc_values);
	            foreach($accadd_list as $accadd){
	                if($accadd['psn']==$psn_buf_psn){
	                    $accadd_find=false;
	                    foreach($acc_values as $acc_value){
	                        if($acc_value['time']==$accadd['time']&&
	                            $acc_value['psnid']==$accadd['psnid']&&
	                            $acc_value['devid']==$accadd['devid'])
	                        {
	                            $accadd_find=true;
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
	    $user=D('access');
	    $ret=$user->addAll($accaddall);
	    //dump('accaddall list:');
	    //dump($accaddall);

	    $user1301=D('access1301');
	    $ret=$user1301->addAll($acc1301addall);
	    //dump('access1301 list:');
	    //dump($acc1301addall);

	    $tuser=D('taccess');
	    $ret=$tuser->addAll($taccadd_list);
	    //dump('taccess list:');
	    //dump($taccadd_list);
	    
	    $chuser=D('changeidlog');
	    $ret=$chuser->addAll($change_add);
	     
      foreach ($change_devs as $ch_dev) {
          if ($ch_dev['flag'] == 1 || $ch_dev['flag'] == 2) {
								if($ch_dev['flag'] == 1){
									$ch_list_buf[]=$chdev['id'];
								}
                $tmp_dev = array(
                		'id'=>$ch_dev['id'],
                    'old_psn' => $dev_psn,
                    'old_devid' => $snint,
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
	    $changedev_count=str_pad($changedev_count,2,'0',STR_PAD_LEFT);
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

	        $filename = date("Ymd_His_").mt_rand(10, 99).".bmp"; //新图片名称
	        $newFilePath = $logdir.$filename;//图片存入路径
	        $newFile = fopen($newFilePath,"w"); //打开文件准备写入
	        fwrite($newFile,$header.$changedev_str.$blacklist_str);
	        fclose($newFile); //关闭文件

	    }

	    echo $header.$changedev_str.$blacklist_str;
	    exit;
	}
	
	public function test(){
		echo 'OK:'.date("Y-m-d_H:i:s");
		exit;
	}
	
}