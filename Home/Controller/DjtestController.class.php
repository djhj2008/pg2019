<?php
namespace Home\Controller;
use Think\Controller;
class DjtestController extends Controller {
  public function index(){
       ob_clean();
       echo 'test';
       exit;
  }
  
	public function scancows_avg(){
		ini_set("memory_limit","1024M");
		$psnid=$_GET['psnid'];

		$max_count=12;
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
    	//$first_time = $cur_time-$delay+$start_time;
    	//$last_time = $cur_time-$delay+$start_time-($max_count-1)*3600;
    	$first_time = $start_time;
    	$last_time = $start_time-($max_count-1)*3600;
    	
			dump(date('Y-m-d H:i:s',$first_time));
			dump(date('Y-m-d H:i:s',$last_time));
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->where('avg_temp=0 or avg_temp>36.5 or avg_temp<35')->order('id asc')->select();

    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];	
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

				echo 'devid:';
				dump($devid);

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
    	//$first_time = $cur_time-$delay+$start_time;
    	//$last_time = $cur_time-$delay+$start_time-($max_count-1)*3600;
    	$first_time = $start_time;
    	$last_time = $start_time-($max_count-1)*3600;
    	
			dump(date('Y-m-d H:i:s',$first_time));
			dump(date('Y-m-d H:i:s',$last_time));
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->where('avg_temp=0 or avg_temp>36.5 or avg_temp<35')->order('id asc')->select();

    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
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

				echo 'devid:';
				dump($devid);
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
					//dump($acc);
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
	
	public function getlastspn(){
		ini_set("memory_limit","1024M");
		$psnid=$_GET['psnid'];

		$low_temp=25;
		$check_count=8;
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
    	$pre_time = $cur_time-$delay+$start_time-$delay;
    	$pre2_time = $cur_time-$delay+$start_time-$delay*2;
    	$pre3_time = $cur_time-$delay+$start_time-$delay*3;
			$last_time = $cur_time-$delay+$start_time-($check_count-1)*3600;
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->order('id asc')->select();
    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
    	}
    	//dump($devlist);
    	$wheredev['devid']=array('in',$devidlist);

    	$mydb='access_'.$psn;
    	$accSelect1=M($mydb)->where(array('psn'=>$psn))->where('time<='.$first_time.' and time>='.$last_time)->where($wheredev)->field('devid,temp1,temp2,time,psnid,sign')->order('time desc')->select();
			
			for($i=30;$i<40;$i++){
    		$mydb='access1301_'.$i;
    		$acc1301list1[$i]=M($mydb)->where(array('psn'=>$psn))->where('time<='.$first_time.' and time>='.$last_time)->where($wheredev)->field('devid,temp1,temp2,time,psnid,sign')->order('time desc')->select();
    	}
    	
			foreach($accSelect1 as $acc){
				$devid=$acc['devid'];
				$cdev[$devid][]=$acc;
			}
			for($i=30;$i<40;$i++){
				foreach($acc1301list1[$i] as $acc){
					$devid=$acc['devid'];
					$cdev[$devid][]=$acc;
				}
    	}

			$mode=M('device');
			echo "START SCAN...";
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				//$psnid = $dev['psnid'];
				//$rid = $dev['rid'];
				//dump($devid);
				$acc_size=0;
				$acc_low_size=0;
				unset($acc_list);
				unset($acc_low_list);
				$acc_list = array();
				$acc_low_list = array();
				foreach($cdev[$devid] as $acc){
					if($acc['devid']==$devid){
						for($ai=0;$ai<$check_count;$ai++){
							if($acc['time']==$first_time-$ai*3600){
								$acc_list[$ai]=$acc;
								break;
							}
						}
						$acc_size=count($acc_list);
					}
				}
				$psn_flag=false;
				$psn_now=0;
				$sign=-200;
				unset($psnid_count);
				foreach($cdev[$devid] as $acc){
					if($acc['psnid']==$psnid){
						$psn_flag=true;
						break;
					}else{
						if($sign < $acc['sign']){
							$psn_now=$acc['psnid'];
							$sign=$acc['sign'];
						}
						$psnid_count[$acc['psnid']]=$psnid_count[$acc['psnid']]+1;
					}
				}
				
				if($psn_flag==false){
					if($psn_now>0){
						if($sign>-100){
							//dump($psn_now);
						}else{
							//dump($psnid_count);
							$psn_now = array_search(max($psnid_count), $psnid_count);
						}
						if($dev['psn_now']!=$psn_now){
							$ret = $mode->where(array('id'=>$dev['id']))->save(array('psn_now'=>$psn_now));
							echo 'PSN NOW:';
							dump($psn_now);
						}
					}
				}else{
						if($dev['psn_now']!=0){
							$ret = $mode->where(array('id'=>$dev['id']))->save(array('psn_now'=>0));
							echo 'PSN NOW:0';
						}
				}

				$low_count=0;
				foreach($acc_list as $key=>$acc){
					if($acc['temp1']< $low_temp&&$acc['temp2']< $low_temp){
						$low_count++;
					}else{
						break;
					}
				}
				if($low_count>0){
					//dump($acc_list);
				}

				if($acc_size>=6){
					if($low_count>=6)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}
				}else if($acc_size>=3&$acc_size<6){
					if($low_count>=3)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}					
				}else if($acc_size==2){
					if($low_count==2)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}					
				}else if($acc_size==2){
					if($low_count==2)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}					
				}else if($acc_size==2){
					if($low_count==2)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}					
				}else if($acc_size==1){
					if($low_count==1)
					{
						//dump($acc_list);
						$dev_low[]=$devid;
					}
				}else if($acc_size==0){
					$dev_none[]=$devid;
				}
			}
			
			//$ret=$mode->where(array('psn'=>$psn))->save(array('cow_state'=>0));
			if($dev_pass){
				$wherenpass['devid']=array('in',$dev_pass);
				//$ret=$mode->where(array('psn'=>$psn))->where($wherenpass)->save(array('cow_state'=>2));
			}
			if($dev_none){
				$wherenone['devid']=array('in',$dev_none);
				//$ret=$mode->where(array('psn'=>$psn))->where($wherenone)->save(array('cow_state'=>4));
			}
			if($dev_low){
				$wherenlow['devid']=array('in',$dev_low);
				//$ret=$mode->where(array('psn'=>$psn))->where($wherenlow)->save(array('cow_state'=>5));
			}
			//if($dev_pass){
			//	$wherepass['devid']=array('in',$dev_pass);
			//	$ret=$mode->where(array('psn'=>$psn))->where($wherepass)->save(array('state'=>2));
			//}
			//echo 'pass:';
			//dump($dev_pass);
			//echo 'dev_lost:';
			//dump($dev_lost);
			echo 'none:';
			//dump($dev_none);
			echo 'low:';
			//dump($dev_low);
		}
		//dump(count($cows));
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
			$ret['rate_flag']=1;//ÌøÆµ¿ª¹Ø
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
		$ret= json_decode($json,true);
		dump($ret['step']['data']);
		dump($ret['recover']['dev']);
		
		exit;
	}
}