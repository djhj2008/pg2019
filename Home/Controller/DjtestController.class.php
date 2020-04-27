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
		
			//dump($wheredev);
		
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
	
}