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
          $imgDir = "lora_backup2/";
          if(!file_exists("lora_backup2")){
                 mkdir("lora_backup2");
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
          $imgDir = "lora_backup2/";
          if(!file_exists("lora_backup2")){
                 mkdir("lora_backup2");
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
					$bsign[$i] = 0-$brssisign;
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
		//var_dump($sum);
		if($crc==$sum){
			echo "OK1".date('YmdHis').$delay_time.$rate.$footer;
		}else{
			echo "OK2".date('YmdHis').$delay_time.$rate.$footer;
		}
		exit;
	}
	
	public function pushnewsubV20Test(){
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

		$str = "32303139303131383037303430303231303836373536333030000020010150000000000000000000b20000200a28210c8202115895001082470900ffffffffffffffffff000020146a210c8202549878008086330700ffffffffffffffffff000020155121040002052332102230213321ffffffffffffffffff000020164b21148202190332040232219319ffffffffffffffffff000020175221048202201332022231126318ffffffffffffffffff000020185e21148202212332017231145321ffffffffffffffffff000020195221048402436334133233382320ffffffffffffffffff0000201a5a21048202419332135233221318ffffffffffffffffff0000201b4e21048202258333117231326319ffffffffffffffffff0000201c5121048202333333117232259318ffffffffffffffffff0000201d5821048202332333067232263318ffffffffffffffffff0000201e5121048202454333309233263320ffffffffffffffffff000020205721048202237333023231289317ffffffffffffffffff000020215721048202259331904132187318ffffffffffffffffff000020225521048302430333149233285320ffffffffffffffffff000020254e21148102335333204232255320ffffffffffffffffff000020265921048202420334278233354320ffffffffffffffffff000020284b21048202196331091231065316ffffffffffffffffff000020295221048202021332232229142321ffffffffffffffffff0000202a5821048202302333045232265319ffffffffffffffffff0000202c5221048402087332909130273320ffffffffffffffffff0000202d5421048202439333297233320322ffffffffffffffffff0000202e4e21048202328333279231290321ffffffffffffffffff0000202f5421048202446333399232170320ffffffffffffffffff000020304e21048202067332864129182317ffffffffffffffffff000020315d21048202141331965130023317ffffffffffffffffff000020336421048202111333905130273318ffffffffffffffffff000020345421048202297331004232078317ffffffffffffffffff000020355b21048302056332144229182318ffffffffffffffffff000020365421048302126332497228072317ffffffffffffffffff000020375c21048202317332143232187317ffffffffffffffffff000020384b21048202091333043229219317ffffffffffffffffff0000203951210482038572309051302503238382308601ffffffff0000203a5d21048202199333089230301320ffffffffffffffffff0000203c5a21048102251333061232303319ffffffffffffffffff0000203d5e21048102094333110232436323ffffffffffffffffff0000203e5621048202207333012231316318ffffffffffffffffff0000203f5221048202307332351232161320ffffffffffffffffff000020405521048202178333046230296317ffffffffffffffffff000020415821148202266333227231289318ffffffffffffffffff000020424e21048202132333083229177318ffffffffffffffffff000020436221048202128331869130153316ffffffffffffffffff000020445e21048202459332006534300350ffffffffffffffffff000020455721040002391335500234523324ffffffffffffffffff000020465a21048202265333472232342324ffffffffffffffffff000020475e21148202373333472234375325ffffffffffffffffff000020486721048102339316066232601118ffffffffffffffffff000020495b21048202485334463233285320ffffffffffffffffff0000204a5b21048202350335574233510325ffffffffffffffffff0000204b5521048202121330025231027319ffffffffffffffffff0000204d5521040102350333515233317324ffffffffffffffffff0000204e5421048202636334712236456326ffffffffffffffffff0000204f5b21048202368333513234431326ffffffffffffffffff000020506421048202443335611234511326ffffffffffffffffff000020515621048102145332116229108317ffffffffffffffffff000020525521048202111332896129072316ffffffffffffffffff000020535221048102773230025225876214ffffffffffffffffff000020545121048202232333158232370321ffffffffffffffffff000020555921048202199330914131027317ffffffffffffffffff000020564d21148202060332754129103313ffffffffffffffffff000020576821048102290334345232366322ffffffffffffffffff000020585a21048402367334555233460325ffffffffffffffffff000020595921048202065332400231241323ffffffffffffffffff0000205a5c21048402475334977233370325ffffffffffffffffff0000205b5c21040002363333379233354322ffffffffffffffffff0000205d5d21040002373334407233448323ffffffffffffffffff000020605821048102241334446232426324ffffffffffffffffff000020614921048202278332128231181318ffffffffffffffffff000020623f21048102478333381231993216ffffffffffffffffff000020634a21048202145330114230961218ffffffffffffffffff000020644121148102279331005231075317ffffffffffffffffff000020654a21048202572335314332163321ffffffffffffffffff000020664521048202376334568231291319ffffffffffffffffff000020673c21048202448226134121485207ffffffffffffffffff000020684d21048202264332186231158318ffffffffffffffffff0000206a4521048202301334080232346317ffffffffffffffffff0000206b4621048402181333082231277319ffffffffffffffffff0000206d5021048202130331958130044319ffffffffffffffffff0000206e4621048202264332027231115318ffffffffffffffffff0000206f4e21048202202331377230994218ffffffffffffffffff000020704621048202257332157233420324ffffffffffffffffff000020714721148202159332051227888214ffffffffffffffffff000020724821048202082332024229109317ffffffffffffffffff000020734f21048202249331119231149318ffffffffffffffffff000020745521048202348332108234445322ffffffffffffffffff000020764721048402124331048229986217ffffffffffffffffff000020774621048402145333981131346318ffffffffffffffffff000020784c21048102227333153231304317ffffffffffffffffff000020794821048202016332112229175318ffffffffffffffffff0000207a3b21048202073330027230051317ffffffffffffffffff000020815121740102358334493232378321ffffffffffffffffff0000207b4921148202925232781133566328ffffffffffffffffff0000207c4c21048202109331236231246322ffffffffffffffffff0000207e5721040002313332283233264323ffffffffffffffffff0000207f4421048102320334307232366321ffffffffffffffffff000020805821048302344334384233441323ffffffffffffffffff000020825221048202158333300234582329ffffffffffffffffff000020835b21048102184332051232263322ffffffffffffffffff000020845221048202137332127231318322ffffffffffffffffff000020865021048202377333400234360325ffffffffffffffffff000020875921048102303333256234502327ffffffffffffffffff000020885721048302417334377233428321ffffffffffffffffff000020895921048202314334438232419323ffffffffffffffffff0000208a5921048202347332282233253322ffffffffffffffffff0000208b5a21040002488333326234373323ffffffffffffffffff0000208c4e21048202071331040231129321ffffffffffffffffff0000208e4f21048202256333423232344323ffffffffffffffffff0000208f5921048202310333238232269321ffffffffffffffffff000020905421148102260334380232348322ffffffffffffffffff000020915a21048202343332366234329327ffffffffffffffffff000020935821148202264332298233392324ffffffffffffffffff000020945921048202127332327232388324ffffffffffffffffff000020955721048202344333336234484324ffffffffffffffffff000020965121048202227331262232176322ffffffffffffffffff000020975821048202255333422231232322ffffffffffffffffff000020995221048402138332262231266321ffffffffffffffffff0000209a5421048202323333318232313322ffffffffffffffffff0000209b5b21048102293333409232340324ffffffffffffffffff0000209c5b21048202259332188232315321ffffffffffffffffff0000209e5421048302355332279233306322ffffffffffffffffff0000209f57210482032623342762335213262843345002ffffffff000020a051210400034153344042312983221373322102ffffffff000020a14f21148202238223475131371321ffffffffffffffffff000020a24a21048202245330006232098320ffffffffffffffffff000020a35421048202277332324232221322ffffffffffffffffff000020a45421048302237331272234326329ffffffffffffffffff000020a56921048202305333295232287321ffffffffffffffffff000020a65421148202278332426232272323ffffffffffffffffff000020a75921048202183331265231101321ffffffffffffffffff000020af4e21148102292332143233264322ffffffffffffffffff000020b04f21048202608226692113620109ffffffffffffffffff000020b15d21048102394334223234474322ffffffffffffffffff000020b25021040002308333376232336321ffffffffffffffffff000020b35821048202294334185232448320ffffffffffffffffff000020b55821048202312332379232238322ffffffffffffffffff000020b65a21048202919233030229368319ffffffffffffffffff000020b76121048202347332129234465323ffffffffffffffffff000020b85c21048202478334523234421323ffffffffffffffffff000020b95a21040002338332235233267321ffffffffffffffffff000020ba5121048302282334352232341321ffffffffffffffffff000020bb5b21048202060330869128762216ffffffffffffffffff000020bc5021048202025328904130855219ffffffffffffffffff000020bd5521048202661228580126759216ffffffffffffffffff000020be5e21048202021330934130141320ffffffffffffffffff000020bf4a21148102911228850127626216ffffffffffffffffff000020c05221148202116331436227864213ffffffffffffffffff000020c14c21048202945231820129118317ffffffffffffffffff000020c24f21048202633229756126982219ffffffffffffffffff000020c35521048202336334198233504323ffffffffffffffffff000020c45021048302238332394229087317ffffffffffffffffff000020c55521048202730229721126812215ffffffffffffffffff000020c65521048202001324871128200215ffffffffffffffffff000020c75c21048202029331850129132316ffffffffffffffffff000020c85221048102994230959128967216ffffffffffffffffff000020c94b21048202070330008226601213ffffffffffffffffff000020cb4d21040002855228492127723213ffffffffffffffffff000020cc4921048102005328774128684215ffffffffffffffffff000020cd5021048202118331940130118316ffffffffffffffffff000020ce4e21048202010331862129018316ffffffffffffffffff000020cf5121048202022330306228871218ffffffffffffffffff000020d04a21048202874229784130135320ffffffffffffffffff000020d14c21148102985229774128809214ffffffffffffffffff000020d25421048202115330842130987216ffffffffffffffffff000020d352210482039232308721290253170563300502ffffffff000020d44b21048202004329860129853213ffffffffffffffffff000020d55121040002096331701130069315ffffffffffffffffff000020d64f21040002903229712128894215ffffffffffffffffff000020d94b21048202118331904130112317ffffffffffffffffff000020db5421048202116330896130979215ffffffffffffffffff000020dc4c21048102157330908130969216ffffffffffffffffff000020dd4d21048202036330919129014318ffffffffffffffffff000020de5121040002782229703126799214ffffffffffffffffff000020df4f21048202366332038233289321ffffffffffffffffff000020e04d21048102258333298231316321ffffffffffffffffff000020e15021048402115331098230136319ffffffffffffffffff000020e25b21048202303333209234473322ffffffffffffffffff000020e34b21048102140333067230235318ffffffffffffffffff000020e45421048202223333358231272323ffffffffffffffffff0000ab39";
		
		if(strlen($str) < $CDATA_START){
    	echo "OKF".date('YmdHis')."00101";
    	//exit;
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
	 	//$saveRssi=D('brssi')->add($rssi);
	 	var_dump($rssi);
	 	var_dump($bsign);
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
    	echo "sn:";
    	var_dump($snint);
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

			  var_dump('temp1:'.$temp1);
				if($temp2str1 == 0){
			    $temp2 = $temp2str2.".".$temp2str3;
		 	 	}else{
		 	 	 	$temp2 = $temp2str1.$temp2str2.".".$temp2str3;
		 	 	}
		    var_dump('temp2:'.$temp2);
		    if($temp3str1 == 0){
			    $temp3 = $temp3str2.".".$temp3str3;
		 	 	}else{
		 	 	 	$temp3 = $temp3str1.$temp3str2.".".$temp3str3;
		 	 	}
		    var_dump('temp3:'.$temp3);
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
}