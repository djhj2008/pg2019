<?php
namespace Home\Controller;
use Think\Controller;
class StationController extends Controller {
  public function index(){
       ob_clean();
       echo 'test';
       exit;
  }
  
	public function devspnscan(){
		ini_set("memory_limit","1024M");
		$psnid=$_GET['psnid'];

		$check_count=24;
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
    	$first_time = $start_time;
			$last_time = $start_time-($check_count-1)*3600;
			
			$station=M('station_dev')->order('id desc')->find();

			if($station){
				if($station['last_time']==$first_time){
					echo 'already computed.';
					exit; 
				}
			}
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->order('id asc')->select();
    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
    	}
    	
    	$wheredev['devid']=array('in',$devidlist);
			$time1=time();
    	$mydb='access_'.$psn;
    	
			$start_index=M($mydb)->where('time='.$last_time)->field('id')->order('id asc')->find();
			$end_index=M($mydb)->where('time='.$first_time)->field('id')->order('id desc')->find();

			$range[]=$start_index['id'];
			$range[]=$end_index['id'];
			
			dump($range);

			$time1=time();
    	$mydb='access_'.$psn;
    	
    	$whereid['id']=array('between',$range);
    	$accSelect1=M($mydb)->where(array('psn'=>$psn))->where($whereid)->field('psn,devid,sid,psnid,time,sign')->select();
			
			$time2=time();
			for($i=30;$i<40;$i++){
				if($i!=$psn){
	    		$mydb='access1301_'.$i;
	    		$acc1301list1[$i]=M($mydb)->where(array('psn'=>$psn))->where('time<='.$first_time.' and time>='.$last_time)->field('psn,devid,sid,psnid,time,sign')->select();
				}
    	}
    	$time3=time();
    	
			foreach($accSelect1 as $acc){
				$devid=$acc['devid'];
				if($acc['psn']==$psn){
					$cdev[$devid][]=$acc;
				}
			}
			for($i=30;$i<40;$i++){
				foreach($acc1301list1[$i] as $acc){
					$devid=$acc['devid'];
					if($acc['psn']==$psn){
						$cdev[$devid][]=$acc;
					}
				}
    	}
    	$time4=time();
			echo "START SCAN...";
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$acc_size=0;
				$acc_low_size=0;
				unset($acc_list);
				$acc_list = array();
				foreach($cdev[$devid] as $acc){
					if($acc['time']>=$last_time&&$acc['time']<=$first_time){
					  $cp=$acc['psnid'];
					  $cd=$acc['sid'];
						$index=$cp*10000+$cd;
						$find_acc=false;

						foreach($acc_list[$index]['time'] as $v){
							if($v==$acc['time']){
								$find_acc=true;
								break;
							}
						}
						if($find_acc==false){
							if($acc['sign']> $acc_list[$index]['sign']){
								$acc_list[$index]['sign']=$acc['sign'];
							}
						  $acc_list[$index]['time'][]=$acc['time'];	
							$acc_list[$index]['count']+=1;	
						}
					}
				}
				if($acc_list){
					foreach($acc_list as $key=>$acc){
						$tmp['rid']=$psn*10000+$devid;
						$tmp['sid']=$key;
						$tmp['sign']=$acc['sign'];
						$tmp['count']=$acc['count'];
						$tmp['last_time']=$first_time;
						$savelist[]=$tmp;
					}
				}else{
						$tmp['rid']=$psn*10000+$devid;
						$tmp['sid']=0;
						$tmp['count']=0;
						$tmp['sign']=0;
						$tmp['last_time']=$first_time;
						$savelist[]=$tmp;
				}
			}
			$time5=time();
			dump($time2-$time1);
			dump($time3-$time2);
			dump($time4-$time3);
			dump($time5-$time4);
			//dump($savelist);
			
		}
		if($savelist){
			$ret=M('station_dev')->addAll($savelist);
		}
		dump($savelist);
		exit;
	}
	
	public function devpsnnow(){
		ini_set("memory_limit","512M");

		$station_list=M('station_dev')->order('id desc')->select();
		
		//dump($station_list);
		foreach($station_list as $station){
			$rid=$station['rid'];
			if($station['sid']>0){
				$devlist[$rid][]=$station;
			}
		}

		foreach($devlist as $key=>$devs){
			$max_count=0;			
			$psn=(int)($key/10000);
			$right_psnid=$psn;
			//dump($devs);
			foreach($devs as $dev){
				$sid=$dev['sid'];
				$count=$dev['count'];
				$psnid=(int)($sid/10000);
				if($count>=12&&$psn==$psnid){
					$right_psnid=$psn;
					break;
				}
				if($count>$max_count){
					$max_count=$count;
					$right_psnid=$psnid;
				}else if($count==$max_count){
					if($psn==$psnid){
						$max_count=$count;
						$right_psnid=$psnid;
					}
				}
			}
			if($right_psnid!=$psn){
				$psn_now=$right_psnid;
				$rid=$key;
				$psn_now_list[$rid]=$psn_now;
				$rid_list[]=$rid;
				//dump('rid:'.$key.' psn_now:'.$right_psnid);
			}
		}
		
		$whererid['rid']=array('in',$rid_list);
		$devlist=M('device')->where($whererid)->select();
		
		foreach($devlist as $dev){
			if($dev['flag']!=1){
				continue;
			}
			$rid=$dev['rid'];
			if($dev['psn_now']==0){
				dump('psn null:'.$rid.' new:'.$psn_now_list[$rid]);
				//$ret=M('device')->where(array('id'=>$dev['id']))->save(array('psn_now'=>$psn_now_list[$rid]));
			}else if($psn_now_list[$rid]!=$dev['psn_now']){
				dump('psn pre:'.$dev['psn_now'].' new:'.$psn_now_list[$rid]);
				//$ret=M('device')->where(array('id'=>$dev['id']))->save(array('psn_now'=>$psn_now_list[$rid]));
			}			
		}
		dump($psn_now_list);
	}	
	
	
	public function devspnscanonce(){
		ini_set("memory_limit","1024M");
		$psnid=$_GET['psnid'];

		$check_count=2;
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

    	$cur_time = $now - $start_time;
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time;
    	$pre_time = $cur_time-$delay+$start_time-$delay;
			$last_time = $cur_time-$delay+$start_time-($check_count-1)*3600;
			
			$start_rid=$psn*10000+30;
			$end_rid=$psn*10000+2760;
			$range_rid[]=$start_rid;
			$range_rid[]=$end_rid;
			

			$whererid['rid']=array('BETWEEN',$range_rid);
			//dump($whererid);
			$stations=M('station_dev')->where($whererid)->order('id desc')->select();

			foreach($stations as $st){
				$rid=$st['rid'];
				$station_list[$rid][]=$st;
				$sn_code_list[]=str_pad($rid,9,'0',STR_PAD_LEFT);
			}

			
			$mode=M('','','DB_CONFIG');
			$wherecow['sn_code']=array('in',$sn_code_list);
			$cows=$mode->table('cows')->where($wherecow)->select();
			foreach($cows as $cow){
				$sn_code=(int)$cow['sn_code'];
				$cow_list[$sn_code]=$cow['id'];
				$farmer_list[$sn_code]=$cow['farmer_id'];
			}
			
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->order('id asc')->select();
    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
    	}
    	
    	$wheredev['devid']=array('in',$devidlist);
			$time1=time();
    	$mydb='access_'.$psn;
    	
			$start_index=M($mydb)->where('time='.$last_time)->field('id')->order('id asc')->find();
			$end_index=M($mydb)->where('time='.$first_time)->field('id')->order('id desc')->find();

			$range[]=$start_index['id'];
			$range[]=$end_index['id'];
			
			dump($range);

			$time1=time();
    	$mydb='access_'.$psn;
    	
    	$whereid['id']=array('between',$range);
    	$accSelect1=M($mydb)->where(array('psn'=>$psn))->where($whereid)->field('psn,devid,sid,psnid,time,sign')->select();
			
			$time2=time();
			for($i=30;$i<40;$i++){
				if($i!=$psn){
	    		$mydb='access1301_'.$i;
	    		$acc1301list1[$i]=M($mydb)->where(array('psn'=>$psn))->where('time<='.$first_time.' and time>='.$last_time)->field('psn,devid,sid,psnid,time,sign')->select();
				}
    	}
    	$time3=time();
    	
			foreach($accSelect1 as $acc){
				$devid=$acc['devid'];
				if($acc['psn']==$psn){
					$cdev[$devid][]=$acc;
				}
			}
			for($i=30;$i<40;$i++){
				foreach($acc1301list1[$i] as $acc){
					$devid=$acc['devid'];
					if($acc['psn']==$psn){
						$cdev[$devid][]=$acc;
					}
				}
    	}
    	$time4=time();
			echo "START SCAN...";
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$rid = $dev['rid'];
				$acc_size=0;
				$acc_low_size=0;
				unset($acc_list);
				$acc_list = array();
				foreach($cdev[$devid] as $acc){
					if($acc['time']>=$last_time&&$acc['time']<=$first_time){
					  $cp=$acc['psnid'];
					  $cd=$acc['sid'];
						$index=$cp*10000+$cd;
						$find_acc=false;

						foreach($acc_list[$index]['time'] as $v){
							if($v==$acc['time']){
								$find_acc=true;
								break;
							}
						}
						if($find_acc==false){
							$find_station=false;
							$exit_flag=false;
							foreach($station_list[$rid] as $station){
								if($station['sid']==$index){
									if($acc['sign']>-100
										&&($station['sign']-$acc['sign'])<10){
											$exit_flag=true;//sign well jumb
											//dump('rid:'.$rid.' sign:'.$acc['sign'].' well sign:'.$station['sign']);
											break;
									}
									//dump('rid:'.$rid.' sign:'.$acc['sign']);
									$find_station=true;
									break;
								}
							}
							if($exit_flag==true){
								break;
							}
							if($find_station==false){
								$tmp['station_sn']=$index;
								$tmp['cow_sn']=$rid;
								$tmp['state']=1;//other cow
								$tmp['state_time']=date('Y-m-d H:i:s',$acc['time']);
								$tmp['sign']=$acc['sign'];
								$tmp['cow_id']=$cow_list[$rid];
								$tmp['farmer_id']=$farmer_list[$rid];
								$tmp['created_at']=date('Y-m-d H:i:s',time());
								//dump($tmp);
								$travel[]=$tmp;
								break;
							}
						  $acc_list[$index]['time'][]=$acc['time'];	
						}
					}
				}
			}
						
			$travel_list=$mode->table('travel')->select();
			if(empty($travel_list)){
				$ret=$mode->table('travel')->addAll($travel);
			}else{
				foreach($travel as $t1){
					foreach($travel_list as $t2){
						if($t1['cow_sn']==$t2['cow_sn']){
							$ret=$mode->table('travel')->where(array('id'=>$v2['id']))->save($t1);
						}else{
							$ret=$mode->table('travel')->add($t1);
							//$dump($mode->getLastsql());
						}
					}
				}
			}


			$time5=time();
			dump($time2-$time1);
			dump($time3-$time2);
			dump($time4-$time3);
			dump($time5-$time4);
			dump($travel);		
		}
		exit;
	}
	
	public function addtravellist(){
		$mode=M('','','DB_CONFIG');
		$travel_list=$mode->table('travel')->select();
		foreach($travel_list as $travel){
			$cow_sn=(int)$travel['cow_sn'];
			$dev=M('device')->where(array('rid'=>$cow_sn,'flag'=>1))->find();
			$start_time=strtotime($travel['state_time']);
			if($dev){
				$psn_now=$dev['psn_now'];
				$psn=$dev['psn'];
				$devid=$dev['devid'];
				if($psn_now>0){
					$mydb='access1301_'.$psn_now;
				}else{
					$mydb='access_'.$psn;
				}
				$acclist=M($mydb)->where(array('psn'=>$psn,'devid'=>$devid))->where('time <='.$start_time)->order('time desc')->limit(0,6)->select();
				foreach($acclist as $acc){
					$station_sn=(int)($acc['psnid']*10000)+(int)$acc['sid'];
					$tmp['station_sn']=$station_sn;
					$tmp['travel_id']=$travel['id'];
					$tmp['cow_sn']=$travel['cow_sn'];
					$tmp['cow_id']=$travel['cow_id'];
					$tmp['state_time']=date('Y-m-d H:i:s',$acc['time']);
					$tmp['temp']=$acc['temp1'];
					$tmp['sign']=$acc['sign'];
					$tmp['created_at']=date('Y-m-d H:i:s',time());
					$travellist_list[]=$tmp;
				}
				
			}
			
		}
		$ret=$mode->table('travellist')->addAll($travellist_list);
		dump($ret);
	}
}