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

		$str = "323031393039313831313031303031323038363735363330300000e0010250000000000000000000cb0000e02e67020401015893359902ffffffffffffffffffffffffff0000e02f6e020401015333358702ffffffffffffffffffffffffff0000e0307e020400014543349602ffffffffffffffffffffffffff0000e03875020401014143335202ffffffffffffffffffffffffff0000e03986020401016143363303ffffffffffffffffffffffffff0000e03a6c020401015021155101ffffffffffffffffffffffffff0000e03c71020481015803363603ffffffffffffffffffffffffff0000e03f7c020401016433371403ffffffffffffffffffffffffff0000e0407d020403016143358502ffffffffffffffffffffffffff0000e04267020401017003374703ffffffffffffffffffffffffff0000e0437102040101610336000affffffffffffffffffffffffff0000e04469020401015613361003ffffffffffffffffffffffffff0000e04567020481016253364503ffffffffffffffffffffffffff0000e04a69020401016693358902ffffffffffffffffffffffffff0000e04d69020401016203368902ffffffffffffffffffffffffff0000e0566e020401016323369502ffffffffffffffffffffffffff0000e05864020401016901176901ffffffffffffffffffffffffff0000e05972020401015933363803ffffffffffffffffffffffffff0000e05a76020402016253361703ffffffffffffffffffffffffff0000e05b68020401013011133401ffffffffffffffffffffffffff0000e05c65020401016781166901ffffffffffffffffffffffffff0000e05d80020401015463358402ffffffffffffffffffffffffff0000e05e86020400015503368702ffffffffffffffffffffffffff0000e06472020402015253358702ffffffffffffffffffffffffff0000e0654c020401014651144501ffffffffffffffffffffffffff0000e06672020400015973350103ffffffffffffffffffffffffff0000e06875020401013653344903ffffffffffffffffffffffffff0000e0696e02040102425335885234568328ffffffffffffffffff0000e06a75020403015253357902ffffffffffffffffffffffffff0000e06b76020400016493354903ffffffffffffffffffffffffff0000e06c7d020481010792211602ffffffffffffffffffffffffff0000e06d71020481013703348002ffffffffffffffffffffffffff0000e06f5b020401017151177201ffffffffffffffffffffffffff0000e07076020401016453363903ffffffffffffffffffffffffff0000e07374020481017213371003ffffffffffffffffffffffffff0000e07472020400015653359102ffffffffffffffffffffffffff0000e07870020481015003350003ffffffffffffffffffffffffff0000e07967020401016871155901ffffffffffffffffffffffffff0000e07a6b020402019501a09601ffffffffffffffffffffffffff0000e07b6b020403012733338002ffffffffffffffffffffffffff0000e07c68020401014853358402ffffffffffffffffffffffffff0000e07d6e020481015093348202ffffffffffffffffffffffffff0000e07e78020481014403358402ffffffffffffffffffffffffff0000e07f76020481013443285702ffffffffffffffffffffffffff0000e08051020401014223347102ffffffffffffffffffffffffff0000e0826c020401016573359902ffffffffffffffffffffffffff0000e08577020402010232200302ffffffffffffffffffffffffff0000e08682020481016473352103ffffffffffffffffffffffffff0000e08889020400014901155001ffffffffffffffffffffffffff0000e08970020401013193321503ffffffffffffffffffffffffff0000e08b47020400014341143901ffffffffffffffffffffffffff0000e08c51020400018311188701ffffffffffffffffffffffffff0000e08e71020481016552275602ffffffffffffffffffffffffff0000e09072020400015973350203ffffffffffffffffffffffffff0000e09268020400015803369502ffffffffffffffffffffffffff0000e09376020400015913368802ffffffffffffffffffffffffff0000e09464020401016103361503ffffffffffffffffffffffffff0000e09876020400011661111601ffffffffffffffffffffffffff0000e09971020401014112244002ffffffffffffffffffffffffff0000e09a48020401013131133901ffffffffffffffffffffffffff0000e09b67020481015993350803ffffffffffffffffffffffffff0000e09d71020401013803341303ffffffffffffffffffffffffff0000e09e7e020400017303374803ffffffffffffffffffffffffff0000e09f71020402016693353503ffffffffffffffffffffffffff0000e0a08a020402014533307302ffffffffffffffffffffffffff0000e0a14c020400014651144501ffffffffffffffffffffffffff0000e0a571020401016503367102ffffffffffffffffffffffffff0000e0a671020481016003360503ffffffffffffffffffffffffff0000e0a76e020402014503348502ffffffffffffffffffffffffff0000e0a87e020400013283327402ffffffffffffffffffffffffff0000e0aa76020400014991144901ffffffffffffffffffffffffff0000e0ac85020401017733371003ffffffffffffffffffffffffff0000e0af5f020400016461166701ffffffffffffffffffffffffff0000e0b081020401016823370403ffffffffffffffffffffffffff0000e0b283020481016723373303ffffffffffffffffffffffffff0000e0cc6f020401017073361603ffffffffffffffffffffffffff0000e0d181020400015933358602ffffffffffffffffffffffffff0000e0d571020400017283361203ffffffffffffffffffffffffff0000e0d670020400014823353203ffffffffffffffffffffffffff0000e0d756020401014171144701ffffffffffffffffffffffffff0000e0d87b020401017263360103ffffffffffffffffffffffffff0000e0db6e020400017013371503ffffffffffffffffffffffffff0000e0e066020481015593359002ffffffffffffffffffffffffff0000e0e15f020481016243354603ffffffffffffffffffffffffff0000e0e46a020402014143343603ffffffffffffffffffffffffff0000e0e66b020401012881122901ffffffffffffffffffffffffff0000e0e866020402016203360903ffffffffffffffffffffffffff0000e0eb6c020401015663357302ffffffffffffffffffffffffff0000e0ec64020401016403364703ffffffffffffffffffffffffff0000e0ed71020401013773307902ffffffffffffffffffffffffff0000e10b70020400014143346802ffffffffffffffffffffffffff0000e10c68020401015343358402ffffffffffffffffffffffffff0000e10d58020402013561134201ffffffffffffffffffffffffff0000e10e65020400014893340203ffffffffffffffffffffffffff0000e10f52020400013941144001ffffffffffffffffffffffffff0000e1125e020402015963358402ffffffffffffffffffffffffff0000e11375020401016983361603ffffffffffffffffffffffffff0000e1184b020481014331143201ffffffffffffffffffffffffff0000e11958020400014611155401ffffffffffffffffffffffffff0000e11e70020402015923350103ffffffffffffffffffffffffff0000e11f88020401016813373803ffffffffffffffffffffffffff0000e1236a020402017383371703ffffffffffffffffffffffffff0000e13d59020400013781133601ffffffffffffffffffffffffff0000e14566020400015331155201ffffffffffffffffffffffffff0000e1466d020400019942253802ffffffffffffffffffffffffff0000e1474f020400013711133801ffffffffffffffffffffffffff0000e14a77020402015443359102ffffffffffffffffffffffffff0000e14c60020401014593344003ffffffffffffffffffffffffff0000e14d3f020400017561178201ffffffffffffffffffffffffff0000e14e76020400016733369902ffffffffffffffffffffffffff0000e14f52020400013321133101ffffffffffffffffffffffffff0000e15282020481016663361303ffffffffffffffffffffffffff0000e1538a020400016643363003ffffffffffffffffffffffffff0000e1557f020401015993359302ffffffffffffffffffffffffff0000e1586b020481016513365502ffffffffffffffffffffffffff0000e15970020400010223338202ffffffffffffffffffffffffff0000e15f3d020400017881178301ffffffffffffffffffffffffff0000e16077020401016133369702ffffffffffffffffffffffffff0000e0146d027c0102447145002014570400ffffffffffffffffff0000e19176030400014893348502ffffffffffffffffffffffffff0000e19556030481015263348802ffffffffffffffffffffffffff0000e19672030401017213375703ffffffffffffffffffffffffff0000e19761030481014103346902ffffffffffffffffffffffffff0000e19861030400016083359502ffffffffffffffffffffffffff0000e19b65030400016813363303ffffffffffffffffffffffffff0000e19c7103040002676336158336670332ffffffffffffffffff0000e19e62030481016673365303ffffffffffffffffffffffffff0000e19f67030400016083359602ffffffffffffffffffffffffff0000e1a069030400015883351103ffffffffffffffffffffffffff0000e1a174030401016253351303ffffffffffffffffffffffffff0000e1a275030481016913364703ffffffffffffffffffffffffff0000e1a372030481016543366203ffffffffffffffffffffffffff0000e1a476030400014493349902ffffffffffffffffffffffffff0000e1a572030401016183359702ffffffffffffffffffffffffff0000e1a675030401017223375703ffffffffffffffffffffffffff0000e1a770030481015373348402ffffffffffffffffffffffffff0000e1aa6f030400014273338202ffffffffffffffffffffffffff0000e1ac6d030401016043369902ffffffffffffffffffffffffff0000e1ad7d030481016033359502ffffffffffffffffffffffffff0000e1ae75030400015483352803ffffffffffffffffffffffffff0000e1b085030400014843350603ffffffffffffffffffffffffff0000e1b16f030400016053351103ffffffffffffffffffffffffff0000e1b272030401016483363503ffffffffffffffffffffffffff0000e1b47e030400015273350703ffffffffffffffffffffffffff0000e1b583030401015783350803ffffffffffffffffffffffffff0000e1b764030400014383348102ffffffffffffffffffffffffff0000e1b85b030401016923375603ffffffffffffffffffffffffff0000e1b971030400016953361103ffffffffffffffffffffffffff0000e1be7d030401017583376603ffffffffffffffffffffffffff0000e1bf80030401016803373303ffffffffffffffffffffffffff0000e1c17d030401016873361503ffffffffffffffffffffffffff0000e1c275030481017343374003ffffffffffffffffffffffffff0000e1c36d030400015663359702ffffffffffffffffffffffffff0000e1c67c030401016283359802ffffffffffffffffffffffffff0000e1c789030402017513372503ffffffffffffffffffffffffff0000e1c876030481016533361203ffffffffffffffffffffffffff0000e1ce6f030400016803373203ffffffffffffffffffffffffff0000e1cf6e03040002582335877235510329ffffffffffffffffff0000e1d277030481016353360903ffffffffffffffffffffffffff0000e1d376030400017313372603ffffffffffffffffffffffffff0000e1d583030401016013364303ffffffffffffffffffffffffff0000e1db70030400016323360903ffffffffffffffffffffffffff0000e1dc70030400015013348102ffffffffffffffffffffffffff0000e1e375030401015353359202ffffffffffffffffffffffffff0000e1e577030400015553350703ffffffffffffffffffffffffff0000e1e683030400016123369002ffffffffffffffffffffffffff0000e1ea7d030400014833358502ffffffffffffffffffffffffff0000e1eb70030401016893350603ffffffffffffffffffffffffff0000e1ee81030401015413356502ffffffffffffffffffffffffff0000e1f188030401016703372103ffffffffffffffffffffffffff0000e1f275030481016413363903ffffffffffffffffffffffffff0000e1f37b030400016223362503ffffffffffffffffffffffffff0000e1f57d030401015363358802ffffffffffffffffffffffffff0000e1f677030400016193364003ffffffffffffffffffffffffff0000e1f881030400017553377003ffffffffffffffffffffffffff0000e1f975030481015963359602ffffffffffffffffffffffffff0000e1fa7b030400016133359102ffffffffffffffffffffffffff0000e1fb70030400015293348802ffffffffffffffffffffffffff0000e1fd88030401017573362903ffffffffffffffffffffffffff0000e1fe77030401017093364303ffffffffffffffffffffffffff0000e2016d030400017993376603ffffffffffffffffffffffffff0000e1e280030400015543359902ffffffffffffffffffffffffff0000e2037603040102425334841234444328ffffffffffffffffff0000e2046e030481016893360803ffffffffffffffffffffffffff0000e20565030400017033373403ffffffffffffffffffffffffff0000e2066a030400016793363203ffffffffffffffffffffffffff0000e20770030400016163350103ffffffffffffffffffffffffff0000e2097c030401015603358602ffffffffffffffffffffffffff0000e20b85030400014883346502ffffffffffffffffffffffffff0000e20d85030400015453358602ffffffffffffffffffffffffff0000e20e74030401015263348902ffffffffffffffffffffffffff0000e20f76030400016373361303ffffffffffffffffffffffffff0000e21182030401016813361503ffffffffffffffffffffffffff0000e2127e030400016093359402ffffffffffffffffffffffffff0000e21571030401013733333302ffffffffffffffffffffffffff0000e21776030401015543347902ffffffffffffffffffffffffff0000e21876030401015673359602ffffffffffffffffffffffffff0000e21b6f030402015263358302ffffffffffffffffffffffffff0000e21c7e030401015283348502ffffffffffffffffffffffffff0000e21e82030401014873337002ffffffffffffffffffffffffff0000e21f7d030401015413358702ffffffffffffffffffffffffff0000e2206e030401015943359002ffffffffffffffffffffffffff0000e2257c030400015543359402ffffffffffffffffffffffffff000caf1f";

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

    $psninfo = D('psn')->where(array('tsn'=>$btsn,'sn'=>$psn))->find();
    if($psninfo){
    	$psnid=$psninfo['id'];
    }else{
    	echo "OKE".date('YmdHis')."00101";
    	{

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
   	
   	$re_devs =D('device')->where(array('psn'=>$psnid,'re_flag'=>1))->limit(0,64)->select();
   	
   	$cur_devs =D('device')->where(array('psn'=>$psnid))->select();
   	
   	//var_dump($re_devs);
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
		    	//$acc_value=D('access')->where(array('time'=>$up_time,'psn'=>$psnid,'devid'=>$snint))->find();
					$acc_add=array(
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
					  		'sid' =>$sid,
					  	);
					$dev_save=array(
								'state'=>1,
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
			  		'sid' =>$sid,
			  	);

					$dev_save2=array(
													'state'=>1,
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
		
		foreach($cur_devs as $dev){
			$devid = $dev['devid'];
			$state= $dev['state'];
			$battery= $dev['battery'];
			$dev_state= $dev['dev_state'];
			$version= $dev['version'];
			foreach($devsave_list as $devsave){
				if($devid==$devsave['devid']){
					if($state!=$devsave['state']){
						$mysave['state']=$devsave['state'];
					}
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
						$dev1=D('device')->save($mysave);
						//dump($mysave);
					}
				}
			}
		}
		 
		//dump("acc add 2:");
		//dump($accadd_list2);
		//dump("dev save 2:");
		//dump($devsave_list2);
		//$access2=D('taccess_test')->add($acc_add2);
		//var_dump($access2);
    
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

		$devres_count=count($devres);
		$devres_count=str_pad($devres_count,2,'0',STR_PAD_LEFT);
		$devres_str=$devres_count.'';
		foreach($devres as $devre_id){
				//$string=base_convert($devre_id, 10, 16);
				$devre_id=str_pad($devre_id,4,'0',STR_PAD_LEFT);
				$devres_str=$devres_str.$devre_id;
		}

		if($crc==$sum){
			$header="OK1".date('YmdHis');
		}else{
			$header="OK2".date('YmdHis');
		}
  	{
        $imgDir = "lora_req30/";
        if(!file_exists("lora_req30")){
               mkdir("lora_req30");
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
        fwrite($newFile,$header.$delay_time.$rate.$footer.$devres_str);
        fclose($newFile); //关闭文件
         
  	}

		echo $header.$delay_time.$rate.$footer.$devres_str;

		exit;
	}
}