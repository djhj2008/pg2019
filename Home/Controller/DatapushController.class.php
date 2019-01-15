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
				if(($brssisign&0x80)==0x80){
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

		$str = "32303139303131353033303430303231303836373536333030000020030150000000000000000000fd000020025621040102266222266220065220ffffffffffffffffff000020034a21040002264222257220056220ffffffffffffffffff000020044721041e033672233832222342220342200402ffffffff000020054421040102266222263220033220ffffffffffffffffff000020084c21040202243222234220044220ffffffffffffffffff0000200a3e210c0002857116004017500100ffffffffffffffffff0000200b4d21040302246222254220065220ffffffffffffffffff0000200c7a21040302277222275220055220ffffffffffffffffff0000201476210c0102894115001017310100ffffffffffffffffff000020154e21040302833118832117733117ffffffffffffffffff000020164b21040002844118846117766117ffffffffffffffffff000020174e21040102855118879117791118ffffffffffffffffff000020184c21040102844118830118798117ffffffffffffffffff000020194b21040102865118862118812118ffffffffffffffffff0000201a4a21040102862118842118780118ffffffffffffffffff0000201b4b21040102843118860118791118ffffffffffffffffff0000201c4921040102854118879117792118ffffffffffffffffff0000201d4821040002856118878117790118ffffffffffffffffff0000201e4621040102845118854117745117ffffffffffffffffff000020204e21040102844118863117745117ffffffffffffffffff000020214d21040102867118878117799117ffffffffffffffffff000020224c21040102856118880118802118ffffffffffffffffff000020234b21040302855118860118801118ffffffffffffffffff000020244a21040102877118853118831118ffffffffffffffffff000020254a21040102865118862118812118ffffffffffffffffff000020264c21040102845118869117801118ffffffffffffffffff000020284821040102855118868117789117ffffffffffffffffff000020294821040102844118874117747117ffffffffffffffffff0000202a4c21041d02835118861117734117ffffffffffffffffff0000202b4421040102008517840150585116ffffffffffffffffff0000202c4a21040102887118861118810118ffffffffffffffffff0000202d4a21040102876118861118810118ffffffffffffffffff0000202e4b21040102875118842118809117ffffffffffffffffff0000202f4a21040102866118872118813118ffffffffffffffffff000020304a21040002887118853118820118ffffffffffffffffff000020314821040102866118870118801118ffffffffffffffffff000020334721040102898118891118802118ffffffffffffffffff000020344a21040102855118865117755117ffffffffffffffffff000020354d21040102846118850117731117ffffffffffffffffff000020364b21040102867118876117787117ffffffffffffffffff000020374821040102877118879117798117ffffffffffffffffff000020384821040002855118898117783118ffffffffffffffffff000020394c210401039111199361188681188001188201ffffffff0000203a4a21040102878118851118819117ffffffffffffffffff0000203c4a21141d02876118860118799117ffffffffffffffffff0000203d4921040102867118879117800118ffffffffffffffffff0000203e4b21040102867118867117777117ffffffffffffffffff0000203f4d21040102867118874117756117ffffffffffffffffff000020404c21040102814118836116698116ffffffffffffffffff000020414e21040102843118841117702117ffffffffffffffffff000020424a21040102856118873117746117ffffffffffffffffff000020434a21040102855118854117744117ffffffffffffffffff000020444b21040102855118005517750150ffffffffffffffffff000020454a21040302845118854117755117ffffffffffffffffff000020464c21040102843118874117737117ffffffffffffffffff000020474c21040102844118894117738117ffffffffffffffffff000020484d21040102848104811117378016ffffffffffffffffff000020495121040102813118828116699116ffffffffffffffffff0000204a4d21040102901119908117798117ffffffffffffffffff0000204b4921040102911119912118833118ffffffffffffffffff0000204c4921040102911119925118866118ffffffffffffffffff0000204d4721040302934119949118899118ffffffffffffffffff0000204e4a21040102931119939118879118ffffffffffffffffff0000204f4921040102911119927118879118ffffffffffffffffff000020504721040102933119959118891119ffffffffffffffffff000020514721040102954119930119887118ffffffffffffffffff000020524721040102922119944118846118ffffffffffffffffff000020534221040102920119900118788117ffffffffffffffffff000020544d21040102929118929117770118ffffffffffffffffff000020554a21040102935119924118874118ffffffffffffffffff000020564921040102942119938118878118ffffffffffffffffff000020574821040102954119941119900119ffffffffffffffffff000020584921040102933119920119898118ffffffffffffffffff000020594921040102923119938118890119ffffffffffffffffff0000205a4a21040102955119921119918118ffffffffffffffffff0000205b4a21040302956119950119909118ffffffffffffffffff0000205d4621040302914119954118867118ffffffffffffffffff0000205e4621040102909118920118781118ffffffffffffffffff0000205f4d21040002923119918117808117ffffffffffffffffff000020604921040102956119976118878118ffffffffffffffffff000020614921040102966119969118899118ffffffffffffffffff000020624721040102953119940119889118ffffffffffffffffff000020634921040102951119961119872119ffffffffffffffffff000020644821040102966119961119911119ffffffffffffffffff000020654921040102945119949118909118ffffffffffffffffff000020664821040102955119960119890119ffffffffffffffffff000020674721040102945119946118876118ffffffffffffffffff000020684821040102942119944118813118ffffffffffffffffff0000206a4c21040102898118915117746117ffffffffffffffffff0000206b4821040102932119921118821118ffffffffffffffffff0000206d4821040102947119955118886118ffffffffffffffffff0000206e4721040102934119935118866118ffffffffffffffffff0000206f4a21040102943119937118866118ffffffffffffffffff000020704921040102934119926118875118ffffffffffffffffff000020714821040102933119926118865118ffffffffffffffffff000020724821040102933119945118846118ffffffffffffffffff000020734a21040102953119945118834118ffffffffffffffffff000020744721040102911119919117788117ffffffffffffffffff000020754e21040102866118899116702117ffffffffffffffffff000020764921040102880119934117769117ffffffffffffffffff000020774921040102922119929117790118ffffffffffffffffff000020784921040102913119930118812118ffffffffffffffffff000020794921040102911119900118809117ffffffffffffffffff0000207a4821040102909118909117789117ffffffffffffffffff0000207b4921040102912119910118810118ffffffffffffffffff0000207c4a21040102929118929117760118ffffffffffffffffff0000207d4a21040102888118915117747117ffffffffffffffffff0000207e4a21040302888118873117721117ffffffffffffffffff0000207f4921040102943119971118804118ffffffffffffffffff000020804821040102966119968118887118ffffffffffffffffff000020814621040302989119982119942119ffffffffffffffffff000020824721040102009219996119955119ffffffffffffffffff000020834621040202990120986119975119ffffffffffffffffff000020854521040102008219996119944119ffffffffffffffffff000020864221040102989119983119933119ffffffffffffffffff000020874221040102009219972119908118ffffffffffffffffff000020884221040102921119910118788117ffffffffffffffffff000020894621040102956119941118841118ffffffffffffffffff0000208a4621040102990120990119920119ffffffffffffffffff0000208b4521040302998119993119934119ffffffffffffffffff0000208c4621040102019219987119954119ffffffffffffffffff0000208e4521040102009219017219957119ffffffffffffffffff0000208f4821040102990120995119955119ffffffffffffffffff000020904721040102999119994119933119ffffffffffffffffff000020914721040102986119000219881119ffffffffffffffffff000020934221040102954119953118813118ffffffffffffffffff000020944821040102936119948117820118ffffffffffffffffff000020954821040102990120989118908118ffffffffffffffffff000020964721040102990120012219944119ffffffffffffffffff000020974521040102011220025219966119ffffffffffffffffff000020994621040102987119983119924119ffffffffffffffffff0000209a4521040102011220036219978119ffffffffffffffffff0000209b4621040102001220995119963119ffffffffffffffffff0000209c4721040102000220004219933119ffffffffffffffffff0000209e4521040102019219982119909118ffffffffffffffffff0000209f45210401030432200162199431198421188101ffffffff000020a04b210403030112200402199141197351177801ffffffff000020a14721040102977119965118854118ffffffffffffffffff000020a24521040102999119990119909118ffffffffffffffffff000020a34621040102998119000219902119ffffffffffffffffff000020a44621040102988119000219913119ffffffffffffffffff000020a54721040202009219982119910119ffffffffffffffffff000020a64921040102990120981119919118ffffffffffffffffff000020a74821040102968119977118888118ffffffffffffffffff000020a84221040102003a189001a0662117ffffffffffffffffff000020af4821040102953119921118798117ffffffffffffffffff000020b04c21040102867118868116698116ffffffffffffffffff000020b14821040102939118937117748117ffffffffffffffffff000020b24721040302933119959117791118ffffffffffffffffff000020b34821040102945119951118832118ffffffffffffffffff000020b54721040102933119961118814118ffffffffffffffffff000020b64721040102944119932118811118ffffffffffffffffff000020b74a21040102944119952118813118ffffffffffffffffff000020b84921040102944119930118809117ffffffffffffffffff000020b94c21040302943119949117789117ffffffffffffffffff000020ba4821141d02899118913117735117ffffffffffffffffff000020bb4921040102969119983118865118ffffffffffffffffff000020bc4821040102033220034219955119ffffffffffffffffff000020bd4421040102044220048219998119ffffffffffffffffff000020be4521040102044220020220997119ffffffffffffffffff000020bf4421040102032220059219981120ffffffffffffffffff000020c04521040102033220029219997119ffffffffffffffffff000020c14721040202913120056218989119ffffffffffffffffff000020c24421040102033220057219967119ffffffffffffffffff000020c34221040102009219980119888118ffffffffffffffffff000020c44421040102932119950118781118ffffffffffffffffff000020c54721040102988119995118856118ffffffffffffffffff000020c64a21040102012214002219342119ffffffffffffffffff000020c74221040102054220048219987119ffffffffffffffffff000020c84621040102064220051220990120ffffffffffffffffff000020c94221040102055220031220008219ffffffffffffffffff000020ca4621040302065220061220001220ffffffffffffffffff000020cb4621040202043220049219978119ffffffffffffffffff000020cc4221040102054220058219978119ffffffffffffffffff000020cd4021040102031220033219923119ffffffffffffffffff000020ce4121040102943119971118803118ffffffffffffffffff000020cf4821040102988119014218846118ffffffffffffffffff000020d04621040102010220030219913119ffffffffffffffffff000020d14421040102055220037219976119ffffffffffffffffff000020d24621040102033220066219979119ffffffffffffffffff000020d346210401030552200452200542209991199801ffffffff000020d44621040102056220079219001220ffffffffffffffffff000020d54821040302023219046219878119ffffffffffffffffff000020d64821040302044220056219967119ffffffffffffffffff000020d74421040102033220023219932119ffffffffffffffffff000020d84421040202996119996118836118ffffffffffffffffff000020da4721040102999119996118877118ffffffffffffffffff000020db4621040102022220041219913119ffffffffffffffffff000020dc4521040102023220033219944119ffffffffffffffffff000020dd4621040102033220044219945119ffffffffffffffffff000020de4721040302011220042219925119ffffffffffffffffff000020df4821040102021220032219923119ffffffffffffffffff000020e04b21040102001220040219904119ffffffffffffffffff000020e14b21040102011220038218880119ffffffffffffffffff000020e24821040202953119950118780118ffffffffffffffffff000020e34b21040102888118899117800118ffffffffffffffffff000020e44721040102008219988118877118ffffffffffffffffff000020e54421040102955119950118801118ffffffffffffffffff000020e64021040102008219966118853118ffffffffffffffffff000020e75121040102899118898117788117ffffffffffffffffff000020e84d21040102898118878117776117ffffffffffffffffff000020e94121040102880119907117789117ffffffffffffffffff000020ea4a21040102902119893117752117ffffffffffffffffff000020ec4621040102932119937117767117ffffffffffffffffff000020ed4721040102956119940118818117ffffffffffffffffff000020ee4721040102957119931118828117ffffffffffffffffff000020ef4721040102967119972118832118ffffffffffffffffff000020f04721040102956119952118820118ffffffffffffffffff000020f14721040202978119944118840118ffffffffffffffffff000020f24621040102955119952118821118ffffffffffffffffff000020f34221040102977119983118834118ffffffffffffffffff000020f44621040102957119982118835118ffffffffffffffffff000020f54521040102968119972118843118ffffffffffffffffff000020f64421040102945119940118810118ffffffffffffffffff000020f74421040102946119970118823118ffffffffffffffffff000020f84721040202935119969117802118ffffffffffffffffff000020f94621040102976119942118819117ffffffffffffffffff000020fa4621040102945119979117801118ffffffffffffffffff000020fb4521040102924119936117787117ffffffffffffffffff000020fc4921040102912119914117765117ffffffffffffffffff000020fd4521040102878118910117703117ffffffffffffffffff000020fe4621040302845118886116669116ffffffffffffffffff000020ff4a21040102931118907117653117ffffffffffffffffff000021004221040102945119959117809117ffffffffffffffffff000021014521141d02009219976118852118ffffffffffffffffff000021024721040302991120006218877118ffffffffffffffffff000021034621040302012220979118894118ffffffffffffffffff000021044521040102981120006218897118ffffffffffffffffff000021054521040102012220999118907118ffffffffffffffffff0000210645210401030902210912200222208901199001ffffffff000021074221040102992120007218908118ffffffffffffffffff000021094521040102002220018218909118ffffffffffffffffff0000210a4221040102011220999118897118ffffffffffffffffff0000210b4421040102994119987118826118ffffffffffffffffff0000210d4521040102989119985118866118ffffffffffffffffff0000210e4721040202999119006218867118ffffffffffffffffff0000210f4221040102990120006218876118ffffffffffffffffff000021104721040102999119985118854118ffffffffffffffffff000021114521040202965119992118804118ffffffffffffffffff000021124521040102944119938117788117ffffffffffffffffff000021134721040102900119923117735117ffffffffffffffffff000021144121040102886118880117680117ffffffffffffffffff000021154721040202921119885117741117ffffffffffffffffff000021164121040102965119960118799117ffffffffffffffffff000021174521040102956119950118810118ffffffffffffffffff000021184621040302009219996118855118ffffffffffffffffff000021194421040102979119004218857118ffffffffffffffffff0000211a4221040102989119975118864118ffffffffffffffffff0000211b4521040202000220017218878118ffffffffffffffffff0000211c4521040102000220007218877118ffffffffffffffffff0000211d5221041e02900339900339900339ffffffffffffffffff0000211e4b21040102879338907338899338ffffffffffffffffff0000211f5b21040102900339909338900339ffffffffffffffffff00004b4d";
		
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
				$bsign[$i] = 0-$brssisign;
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
		    	var_dump('time:'.$up_time);	
		    	$acc_value=D('access2')->where(array('time'=>$up_time,'psn'=>$psnid,'devid'=>$snint))->find();
		    	//var_dump($acc_value);
		    	//sleep(1);
		    	if(empty($acc_value)){
		    			$access=D('access2')->add(array(
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
					  	if(!empty($access)){
		    				var_dump($access);
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
		    
					//$tacc_value=D('taccess')->where(array('time'=>$up_time,'psn'=>$psnid,'devid'=>$snint))->find();
		    	//var_dump($tacc_value);

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