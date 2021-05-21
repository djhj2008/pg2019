<?php
namespace Home\Controller;
use Think\Controller;
class StationController extends Controller {
  public function index(){
       ob_clean();
       echo 'test';
       exit;
  }
  
  public function simcard(){
  	$bdevice=M('bdevice')->where(['switch'=>1])->order('psn asc')->select();
  	foreach($bdevice as $dev){
  		$bsn =str_pad($dev['psn'],5,'0',STR_PAD_LEFT).str_pad($dev['id'],4,'0',STR_PAD_LEFT);
  		$pre_dev=M('bdevice')->where(['new_bsn'=>$bsn])->find();
  		//dump($bsn);
  		if($pre_dev){
  			//dump($pre_dev);
  			if(empty($pre_dev['number'])){
  				$bsn =str_pad($pre_dev['psn'],5,'0',STR_PAD_LEFT).str_pad($pre_dev['id'],4,'0',STR_PAD_LEFT);
  				$pre_pre_dev=M('bdevice')->where(['new_bsn'=>$bsn])->find();
  				//dump($bsn);
  				if($pre_pre_dev){
  					//dump($pre_pre_dev);
  					$sim=$pre_pre_dev['number'];
  				}else{
  					dump('err.');
  				}
  			}else{
  				$sim=$pre_dev['number'];
  			}
  		}else{
  			$sim=$dev['number'];
  		}
  		dump('sn:'.$bsn.' sim:'.$sim);
  		$simcard['sn']=(int)$bsn;
  		$simcard['sim']=(int)$sim;
  		$sim_list[]=$simcard;
  	}
  	$ret = M('simcards')->addAll($sim_list);
  	//dump($bdevice);
  	exit;
  }
  
	public function devspnscan(){
		ini_set("memory_limit","1024M");
		$psnid=$_GET['psnid'];

		$check_count=24*7;
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
					//echo 'already computed.';
					//exit; 
				}
			}
			
    	$devlist=M('device')->where(array('psn'=>$psn,'flag'=>1))->order('id asc')->select();
    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
    	}
    	
    	$wheredev['devid']=array('in',$devidlist);
			$time1=time();
    	$mydb='access_base';
    	
			$start_index=M($mydb)->where('time='.$last_time)->field('id')->order('id asc')->find();
			$end_index=M($mydb)->where('time='.$first_time)->field('id')->order('id desc')->find();

			$range[]=$start_index['id'];
			$range[]=$end_index['id'];
			
			dump($range);

			$time1=time();
    	
    	$whereid['id']=array('between',$range);
    	$accSelect1=M($mydb)->where(array('psn'=>$psn))->where($whereid)->field('psn,devid,sid,psnid,time,sign')->select();
			

    	
			foreach($accSelect1 as $acc){
				$devid=$acc['devid'];
				if($acc['psn']==$psn){
					$cdev[$devid][]=$acc;
				}
			}


			echo "START SCAN...";
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$rid=$dev['rid'];
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
						$tmp['rid']=$rid;
						$tmp['sid']=$key;
						$tmp['sign']=$acc['sign'];
						$tmp['count']=$acc['count'];
						$tmp['last_time']=$first_time;
						$savelist[]=$tmp;
					}
				}else{
						$tmp['rid']=$rid;
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

	public function devspnscan2(){
		set_time_limit(1200);
		$station_list=M('station_dev')->where(array('sid'=>0))->order('id asc')->select();
		$check_count=24*7;
		foreach($station_list as $station){
			$rid=$station['rid'];
			$tmp=NULL;
			$dev=M('device')->where(array('rid'=>$rid,'flag'=>1))->find();
			$dev2=M('device')->where(array('rid'=>$rid,'flag'=>2))->find();
			unset($acclist);
			unset($acc1301list);
			unset($cdev);
			unset($sdev_list);
			unset($tmp);
			unset($time_list);
			$start_time=0;
			$end_time=0;
			if($dev){
				dump($rid);
				$psn=$dev['psn'];
				$devid=$dev['devid'];
				$mydb='access_base';
				$user=M($mydb);
				$time1278=$user->where(array('psn'=>$psn,'devid'=>$devid))->order('time desc')->find();
				

				if($time1278){
					$time_list[]=$time1278['time'];
				}

		  		foreach($time_list as $t){
		  			dump(date('Y-m-d H:i:s',$t));
		  		}
		  		$end_time=max($time_list);
		  		$start_time=$end_time-$check_count*3600;
		  		echo 'start:';
		  		dump(date('Y-m-d H:i:s',$start_time));
		  		dump(date('Y-m-d H:i:s',$end_time));

				$acclist=$user->where(array('psn'=>$psn,'devid'=>$devid))
							->where('time<='.$end_time.' and time>='.$start_time)
							->order('time desc')
							->select();
						
				foreach($acclist as $acc){
					$cdev[]=$acc;
				}
				

				if(empty($cdev)){
					continue;					
				}

		  		foreach($cdev as $acc){
		  		//dump(date('Y-m-d H:i:s',$acc['time']));
		  			$sid=$acc['psnid']*10000+$acc['sid'];
		  			if($acc['time']==$sdev_list[$sid]['time']){
		  				continue;
					}
		  			$sdev_list[$sid]['count']+=1;					
		  			if($acc['time']>$sdev_list[$sid]['time']){
		  				$sdev_list[$sid]['time']=$acc['time'];
		  			}
		  			if($acc['sign']>$sdev_list[$sid]['sign']){
		  				$sdev_list[$sid]['sign']=$acc['sign'];
		  			}
				}
				foreach($sdev_list as $key=>$acc){
					$tmp['rid']=$rid;
					$tmp['sid']=$key;
					$tmp['sign']=$acc['sign'];
					$tmp['count']=$acc['count'];
					$tmp['last_time']=$acc['time'];
					dump($tmp);	
					$savelist[]=$tmp;
				}
			}			
		}
		if($savelist){
			$ret=M('station_dev')->addAll($savelist);
		}
		//dump($savelist);		
		exit;
	}
	
	public function devspnscan3(){
		set_time_limit(600);
		$devlist=M('device')->where(array('flag'=>1))->order('id asc')->select();
		$station_list=M('station_dev')->where('sid>0')->order('id asc')->select();
		foreach($station_list as $s){
			$rid=$s['rid'];
			$s_list[$rid]=$s;
		}	
		
		foreach($devlist as $dev){
			$rid=$dev['rid'];
			if(!isset($s_list[$rid])){
				$st_list[]=$dev;
			}
		}
		dump($st_list);

		foreach($st_list as $station){
			$rid=$station['rid'];
			$tmp=NULL;
			$dev=M('device')->where(array('rid'=>$rid))->find();
			unset($acclist);
			unset($acc1301list);
			unset($cdev);
			unset($sdev_list);
			unset($tmp);
			if($dev){
				dump($rid);
				$psn=$dev['psn'];
				$devid=$dev['devid'];
				$mydb='access_base';
				$user=M($mydb);
				$first_time=1585670400-86400*30;
				$acclist=$user->where(array('psn'=>$psn,'devid'=>$devid))->order('time desc')->limit(0,24*7)->select();
					
				foreach($acclist as $acc){
					$cdev[]=$acc;
				}

				if(empty($cdev)){
					continue;					
				}

				$end_time=0;
		  		foreach($cdev as $acc){
		  		if($acc['time']>$end_time){
		  			$end_time=$acc['time'];
		  		}
		  	}
		  	echo 'start:';
				dump(date('Y-m-d H:i:s',$end_time));
				$start_time=$end_time-86400*7;
				dump(date('Y-m-d H:i:s',$start_time));

		  	foreach($cdev as $acc){
		  		if($acc['time']< $start_time){
		  			//dump(date('Y-m-d H:i:s',$acc['time']));
		  			continue;
		  		}
		  		//dump(date('Y-m-d H:i:s',$acc['time']));
		  		$sid=$acc['psnid']*10000+$acc['sid'];
		  		if($acc['time']==$sdev_list[$sid]['time']){
		  			continue;
					}
		  		$sdev_list[$sid]['count']+=1;					
		  		if($acc['time']>$sdev_list[$sid]['time']){
		  			$sdev_list[$sid]['time']=$acc['time'];
		  		}
		  		if($acc['sign']>$sdev_list[$sid]['sign']){
		  			$sdev_list[$sid]['sign']=$acc['sign'];
		  		}
		  	}
		  	dump($sdev_list);

				foreach($sdev_list as $key=>$acc){
					$tmp['rid']=$rid;
					$tmp['sid']=$key;
					$tmp['sign']=$acc['sign'];
					$tmp['count']=$acc['count'];
					$tmp['last_time']=$acc['time'];
					$savelist[]=$tmp;
				}
			}			
		}
		if($savelist){
			$ret=M('station_dev')->addAll($savelist);
		}
		//dump($savelist);		
		exit;
	}	
	
	public function devpsnnow(){
		ini_set("memory_limit","512M");

		$station_list=M('station_dev')->order('id asc')->select();

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
			unset($tmp);
			unset($tmp2);
			$find_local=false;
			foreach($devs as $dev){
				$sid=$dev['sid'];
				$count=$dev['count'];
				$psnid=(int)($sid/10000);
				$sign=$dev['sign'];
				if($count>=60&&$psn==$psnid){
					$max_count=$count;
					$right_psnid=$psn;
					break;
					//dump('local rid:'.$key.' psn_now:'.$sid.' sign:'.$dev['sign'].' count:'.$dev['count']);
					//dump($dev);
					//break;
				}
				if($sign>-100&&$psn==$psnid){
					$max_count=$count;
					$right_psnid=$psn;
					break;
					//dump('local rid:'.$key.' psn_now:'.$sid.' sign:'.$dev['sign'].' count:'.$dev['count']);
					//dump($dev);
					//break;
				}
				if($count>$max_count){
					$max_count=$count;
					$right_psnid=$psnid;
					$tmp=$dev;
				}else if($count==$max_count){
					if($psn==$psnid){
						$max_count=$count;
						$right_psnid=$psnid;
						$tmp=$dev;
					}
				}
			}
			
			if($right_psnid!=$psn){
				$psn_now=$right_psnid;
				$rid=$key;
				$psn_now_list[$rid]=$psn_now;
				$rid_list[]=$rid;
				//dump('well rid:'.$key.' psn_now:'.$tmp['sid'].' sign:'.$tmp['sign'].' count:'.$tmp['count']);
				if($find_local==true&&$tmp['count']-$tmp2['count']>20){
					dump('local rid:'.$key.' psn_now:'.$tmp2['sid'].' sign:'.$tmp2['sign'].' count:'.$tmp2['count']);
					dump('well rid:'.$key.' psn_now:'.$tmp['sid'].' sign:'.$tmp['sign'].' count:'.$tmp['count']);
				}
			}else{
				$rid_list[]=$rid;
				$psn_now_list[$rid]=$psn;
			}
		}
		
		$whererid['rid']=array('in',$rid_list);
		$devlist=M('device')->where($whererid)->select();
		
		foreach($devlist as $dev){
			if($dev['cow_state']!=4){
				//continue;
			}
			$rid=$dev['rid'];
			if($dev['psn_now']==0){
				dump('psn null:'.$rid.' new:'.$psn_now_list[$rid]);
				//$ret=M('device')->where(array('id'=>$dev['id']))->save(array('psn_now'=>$psn_now_list[$rid]));
			}else if($psn_now_list[$rid]!=$dev['psn_now']){
				//dump('psn pre:'.$rid.' '.$dev['psn_now'].' new:'.$psn_now_list[$rid]);
				//$ret=M('device')->where(array('id'=>$dev['id']))->save(array('psn_now'=>$psn_now_list[$rid]));
			}else{
				//dump('psn pre:'.$rid.' '.$dev['psn_now'].' new:'.$psn_now_list[$rid]);
			}	
		}
		//dump($psn_now_list);
	}	
	
	public function devspnscanonce(){
		ini_set("memory_limit","1024M");
		$psnid=$_GET['psnid'];
		$mode=M('','','DB_CONFIG');
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
			

			//$whererid['rid']=array('BETWEEN',$range_rid);
			//dump($whererid);
			$stations=M('station_dev')->order('id desc')->select();
			
			$basests=$mode->table('basestations')->select();
			
			foreach($basests as $st){
				$base_code=(int)$st['base_code'];
				$basest_list[$base_code]=$st['id'];
			}


			foreach($stations as $st){
				$rid=$st['rid'];
				$station_list[$rid][]=$st;
				$sn_code_list[]=str_pad($rid,9,'0',STR_PAD_LEFT);
			}

			
			$wherecow['sn_code']=array('in',$sn_code_list);
			$cows=$mode->table('cows')->select();
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
    	$mydb='access_base';
    	
			$start_index=M($mydb)->where('time='.$last_time)->field('id')->order('id asc')->find();
			$end_index=M($mydb)->where('time='.$first_time)->field('id')->order('id desc')->find();

			$range[]=$start_index['id'];
			$range[]=$end_index['id'];
			
			dump($range);

			$time1=time();
    	$mydb='access_base';
    	
    	$whereid['id']=array('between',$range);
    	$accSelect1=M($mydb)->where(array('psn'=>$psn))->where($whereid)->field('psn,devid,sid,psnid,time,sign')->select();
			
			$time2=time();

    	
			foreach($accSelect1 as $acc){
				$devid=$acc['devid'];
				if($acc['psn']==$psn){
					$cdev[$devid][]=$acc;
				}
			}

			echo "START SCAN...";
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$rid = $dev['rid'];
				$acc_size=0;
				$acc_low_size=0;
				unset($acc_list);
				$acc_list = array();
				unset($tmp);
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
						$acc_list[$index]['time'][]=$acc['time'];	
						if($find_acc==false){
							$find_station=false;
							$exit_flag=false;
							foreach($station_list[$rid] as $station){
								if($station['sid']==$index){
									if(($acc['sign']>-100
										&&$station['sign']>-100)||($acc['sign']-$station['sign']>0&&$acc['sign']>-100)){
											$exit_flag=true;//sign well jumb
											dump('rid:'.$rid.' well sign:'.$station['sign'].' index:'.$index);
											//dump($acc);
											break;
									}
									dump('rid:'.$rid.' sign bad index:'.$index);
									//dump($acc);
									$find_station=true;
									break;
								}
							}
							if($exit_flag==true){
								break;
							}
							if($find_station==false){
								$tmp['station_sn']=$index;
								$tmp['station_id']=$basest_list[$index];
								$tmp['cow_sn']=$rid;
								$tmp['state']=1;//other cow
								$tmp['state_time']=date('Y-m-d H:i:s',$acc['time']);
								$tmp['sign']=$acc['sign'];
								$tmp['cow_id']=$cow_list[$rid];
								$tmp['farmer_id']=$farmer_list[$rid];
								$tmp['created_at']=date('Y-m-d H:i:s',time());
								dump('rid null:'.$rid.' index:'.$index);
								//dump($acc);
								//$travel[]=$tmp;
							}
						}
					}
				}	
				if($tmp&&$exit_flag==false){
					$travel[]=$tmp;
				}
			}
			
			$time5=time();
			dump($time2-$time1);
			dump($time3-$time2);
			dump($time4-$time3);
			dump($time5-$time4);	
				
			$travel_list=$mode->table('travels')->select();
			if(empty($travel_list)){
				$ret=$mode->table('travels')->addAll($travel);
				dump($travel);
			}else{
				foreach($travel as $t1){
					$have_travel=false;
					foreach($travel_list as $t2){
						if($t1['cow_sn']==$t2['cow_sn']){
							$have_travel=true;
							dump('update:');
							dump($t1);
							$ret=$mode->table('travels')->where(array('id'=>$t2['id']))->save($t1);
							break;
						}
					}
					if($have_travel==false){
							dump('add:');
							dump($t1);
							$ret=$mode->table('travels')->add($t1);
					}
				}
			}


		}
		exit;
	}
	
	public function addtravellist(){
		set_time_limit(1800);
		$mode=M('','','DB_CONFIG');
		$count=6;
		$basests=$mode->table('basestations')->select();
		
		foreach($basests as $st){
			$base_code=(int)$st['base_code'];
			$basest_list[$base_code]=$st['id'];
		}
		dump($basest_list);
		$travel_list=$mode->table('travels')->where(array('state'=>1))->select();
		foreach($travel_list as $travel){
			$travel_id = $travel['id'];
			$cow_sn=(int)$travel['cow_sn'];
			$dev=M('device')->where(array('rid'=>$cow_sn,'flag'=>1))->find();
			$start_time=strtotime($travel['state_time']);
			$end_time=$start_time-$count*3600;
			unset($cdev);
			unset($acc1301list);
			unset($acclist);
			dump($dev);
			unset($travellist_list);
			if($dev){
				$psn_now=$dev['psn_now'];
				$psn=$dev['psn'];
				$devid=$dev['devid'];
				$mydb='access_base';
				
				$acclist=M($mydb)->where(array('psn'=>$psn,'devid'=>$devid))->where('time<='.$start_time.' and time>='.$end_time)->field('psn,devid,sid,psnid,time,sign,temp1')->order('time desc')->select();
							
				foreach($acclist as $acc){
					$devid=$acc['devid'];
					if($acc['psn']==$psn){
						$cdev[]=$acc;
					}
				}
				
				//dump($cdev);
				for($i=0;$i< $count;$i++){
					$cur_time=$start_time-3600*$i;
					$well_sign=-200;
					unset($well_acc);
					unset($tmp);
					$find_acc=false;
					foreach($cdev as $acc){
						if($cur_time==$acc['time']){
							if($acc['sign']>$well_sign){
								$find_acc=true;
								$well_acc=$acc;
							}
						}
					}
					if($find_acc==false){
						continue;
					}
					$station_sn=(int)($well_acc['psnid']*10000)+(int)$well_acc['sid'];
					$tmp['station_sn']=$station_sn;
					$tmp['station_id']=$basest_list[$station_sn];
					$tmp['travel_id']=$travel['id'];
					$tmp['cow_sn']=$travel['cow_sn'];
					$tmp['cow_id']=$travel['cow_id'];
					$tmp['state_time']=date('Y-m-d H:i:s',$well_acc['time']);
					$tmp['temp']=$well_acc['temp1'];
					$tmp['sign']=$well_acc['sign'];
					$tmp['created_at']=date('Y-m-d H:i:s',time());
					//dump($tmp);
					$travellist_list[]=$tmp;
				}
			}
			$ret=$mode->table('travels')->where(array('id'=>$travel_id))->save(array('state'=>2));
			$ret=$mode->table('travellists')->where(array('travel_id'=>$travel_id))->delete();
			dump($travellist_list);
			$ret=$mode->table('travellists')->addAll($travellist_list);
			dump($ret);
		}
	}
	
	public function stationforman(){
		$mode=M('','','DB_CONFIG');
		$travel_list=$mode->table('travels')->where(array('state'=>0))->where('deleted_at != NULL')->select();
		foreach($travel_list as $travel){
			$tmp['rid']=$travel['cow_sn'];
			$tmp['sid']=$travel['station_sn'];
			$tmp['sign']=$travel['sign'];
			$tmp['count']=2;
			$tmp['last_time']=strtotime($travel['state_time']);
			$find_station=M('station_dev')->where(array('rid'=>$travel['cow_sn'],'sid'=>$travel['station_sn']))->find();
			if(empty($find_station)){
				$ret=M('station_dev')->add($tmp);
			}
			$id=$travel['id'];
			$ret=$mode->table('travels')->where(array('id'=>$id))->delete();
			$ret=$mode->table('travellists')->where(array('travel_id'=>$id))->delete();
			dump($travel);
		}
		echo 'finish.';
	}
		
	public function scandsalesandead(){
		set_time_limit(1200);
		$mode=M('','','DB_CONFIG');
		$survival_state=4;
		$delay=3600*2;
		$now = time();
		$start_time = strtotime(date('Y-m-d',$now));
	  	$cur_time = $now - $start_time;
	  	$cur_time = (int)($cur_time/$delay)*$delay;
	  	$first_time = $cur_time-$delay+$start_time;
	  	$time_str=date('Y-m-d H:i:s',$first_time);
    	
		$cow_list=$mode->table('cows')->where(array('survival_state'=>$survival_state))->order('id asc')->select();
		$birth_list=$mode->table('births')->where(array('cow_type'=>'survival_state','cow_code'=>$survival_state))->order('cow_id asc')->select();
		foreach($birth_list as $b){
			$cow_id=$b['cow_id'];
			$b_list[$cow_id]=$b[time];
		}
		foreach($cow_list as $c){
			$cow_id=$c['id'];
			$c_list[$cow_id]=$c['sn_code'];
		}
		//dump($b_list);
		//dump($c_list);
		foreach($cow_list as $cow){
			$cow_sn=(int)$cow['sn_code'];
			$cow_id=$cow['id'];
	
			if(isset($b_list[$cow_id])){
				continue;
			}
			//dump($cow_id);
			//dump($cow_sn);
			//$no_id[]=$cow_id;
			//echo 'enter:';
			$tmp=NULL;
			$dev=M('device')->where(array('rid'=>$cow_sn))->find();
			unset($acclist);
			unset($acc1301list);
			unset($cdev);
			unset($birth);
			if($dev){
				//dump($dev);
				$psn=$dev['psn'];
				$devid=$dev['devid'];
				$mydb='access_psn';
				
				$user=M($mydb);
				$acclist=$user->where(array('psn'=>$psn,'devid'=>$devid))->where('temp1>30')->order('time desc')->limit(0,1)->select();
						
				
				foreach($acclist as $acc){
					$devid=$acc['devid'];
					if($acc['psn']==$psn){
						$cdev[]=$acc;
					}
				}
				
		  	//dump($cdev);
		  	$last_time=0;
		  	unset($tmp);
				foreach($cdev as $acc){
					//dump(date('Y-m-d H:i:s',$acc['time']));
					if($last_time<$acc['time']){
						$last_time=$acc['time'];
						$tmp=$acc;
					}
				}
				//echo 'last:';
			}
			$no_cow['sn']=$cow_sn;
			$no_cow['time']=date('Y-m-d H:i:s',$tmp['time']);
			
			$no_cow_list[]=$no_cow;
			//dump('sn:'.$cow_sn.' time:'.date('Y-m-d H:i:s',$tmp['time']));
			$other_head1='系统更新';
			$birth['cow_id']=$cow['id'];
			$birth['cow_type']='survival_state';
			$birth['cow_code']=$survival_state;
			$birth['time']=date('Y-m-d H:i:s',$tmp['time']);
			$birth['created_at']=date('Y-m-d H:i:s',time());
			$birth['updated_at']=date('Y-m-d H:i:s',time());
			$birth['comment']=$other_head1;
			$birth['deliver_type']=2;
			$has=$mode->table('births')->where(array('cow_id'=>$cow['id'],'cow_type'=>'survival_state','cow_code'=>$survival_state))->order('time desc')->find();
			if($has){
				//dump('has');
				//dump($has);
				//$mode->table('births')->where(array('id'=>$has['id']))->save(array('time'=>$birth['time']));
			}else{
				//$mode->table('births')->add($birth);
				//dump($birth);
			}
		}
		$this->assign('no_cow_list',$no_cow_list);
		$this->display();
	}
	
	public function syncdevlost(){
			set_time_limit(600);
			$delay=3600*2;
			$mode=M('','','DB_CONFIG');
			$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	$cur_time = $now - $start_time;
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time-8*3600;
    	$second_time = $cur_time-$delay+$start_time-3*3600;
    	
    	$devchangelist=M('device')->field('id,rid,psn,devid')->where(array('flag'=>2))->select();
			$changeid=M('changeidlog')->where(array('flag'=>3))->select();
    	
    	$low_count = rand(20,100);
    	$lost_count = rand(300,600);
    	
			foreach($changeid as $dev){
				$psn=$dev['old_psn'];
				$devid=$dev['old_devid'];
				$rid=$dev['rfid'];
				$find_dev=false;
				foreach($devchangelist as $ch){
					if($ch['rid']==$rid&&$ch['psn']==$psn&&$ch['devid']==$devid){
						$find_dev=true;
						break;
					}
				}
				if($find_dev==false){
					echo 'change id:';
					dump($rid);
					$ret=M('device')->where(array('rid'=>$rid,'psn'=>$psn,'devid'=>$devid))->save(array('flag'=>2));
					$ret=M('device')->where(array('rid'=>$rid,'flag'=>0))->save(array('flag'=>1));
				}
			}

		
			$cowlist=$mode->table('cows')->field('id,sn_code,health_state,survival_state')->select();
			$devlist=M('device')->where(array('flag'=>1))->select();
	
			$stopdevlist=M('device')->field('id,rid,psn,devid')->where(array('flag'=>3))->select();
			foreach($stopdevlist as $dev){
				$stopdev[$dev['rid']]=$dev['id'];
			}
			
			foreach($devlist as $dev){
				$rid=$dev['rid'];
				if(strlen($rid)>9){
					$rid= (int)substr($rid,-8);
				}
				$devcowstate[$rid]=$dev['cow_state'];
				$devflag[$rid]=$dev['id'];
			}
			echo 'low count:';
			dump($low_count);
			dump($devcowstate);
			//dump($devflag);
			$health_count=0;
			$lose_count=0;
			foreach($cowlist as $cow){
				$rid=(int)$cow['sn_code'];
				
				dump($rid);
				dump($cow['health_state']);
				dump($cow['survival_state']);
				dump($devcowstate[$rid]);

				if($cow['survival_state']==2||$cow['survival_state']==4)
				{
					$cowstop[]=$rid;
				}else if($cow['survival_state']==3){
					if($devcowstate[$rid]!=4){
						$backcow[]=$cow['id'];
					}else{
						$lose_count++;
						if($lose_count> $lost_count){
							$backcow[]=$cow['id'];
						}
					}
				}else if($cow['survival_state']==1){
					if($devcowstate[$rid]==4){
						if($lose_count< $lost_count){
							$loseadd[]=$cow['id'];
							$lose_count++;
						}
					}
					if(isset($stopdev[$rid])){
						$startdev[]=$stopdev[$rid];
					}
				}
				//dump(count($lowadd));
				if($cow['health_state']==3){
					if($devcowstate[$rid]!=5&&$devcowstate[$rid]!=4){
						$wellcow[]=$cow['id'];
					}else{
						$health_count++;
						if($health_count> $low_count){
							$wellcow[]=$cow['id'];
						}
					}
				}else if($cow['health_state']==1){
					if($devcowstate[$rid]==5){
						if($health_count< $low_count){
							$lowadd[]=$cow['id'];
							$health_count++;
						}
						//$lowtime=$first_time;
					}
				}
			}
			echo 'start id:';
			dump($startdev);
			if($startdev){
				$wherestart['id']=array('in',$startdev);
				$ret=M('device')->where($wherestart)->save(array('flag'=>1));
			}
			
			
			foreach($cowstop as $id){
				if(isset($devflag[$id])){
					$devstop[]=$devflag[$id];
				}	
			}
			echo 'stop id:';
			dump($devstop);
			if($devstop){
				$wherestop['id']=array('in',$devstop);
				$ret=M('device')->where($wherestop)->save(array('flag'=>3));
			}
			
			echo 'backcow id:';
			dump($backcow);
			echo 'wellcow id:';
			dump($wellcow);
			echo 'loseadd id:';
			dump($loseadd);
			echo 'lowadd id:';
			dump($lowadd);
			
			if($loseadd){
				$wherelose['id']=array('in',$loseadd);
				$ret=$mode->table('cows')->where($wherelose)->save(array('survival_state'=>3));
			}			
			
			if($lowadd){
				$wherelow['id']=array('in',$lowadd);
				$ret=$mode->table('cows')->where($wherelow)->save(array('health_state'=>3));
			}		
				
			if($backcow){
				$whereback['id']=array('in',$backcow);
				$ret=$mode->table('cows')->where($whereback)->save(array('survival_state'=>1));
			}		
				
			if($wellcow){
				$wherewell['id']=array('in',$wellcow);
				$ret=$mode->table('cows')->where($wherewell)->save(array('health_state'=>1));
			}	
									
			foreach($loseadd as $cow_id){
				$other_head1='系统更新';
				$birth['cow_id']=$cow_id;
				$birth['cow_type']='survival_state';
				$birth['cow_code']=3;
				$birth['time']=date('Y-m-d H:i:s',$first_time);
				$birth['created_at']=date('Y-m-d H:i:s',time());
				$birth['updated_at']=date('Y-m-d H:i:s',time());
				$birth['comment']=$other_head1;
				$birth['deliver_type']=3;
				$ret=$mode->table('births')->add($birth);
			}
			
			foreach($lowadd as $cow_id){
				$other_head1='系统更新';
				$birth['cow_id']=$cow_id;
				$birth['cow_type']='health_state';
				$birth['cow_code']=3;
				$birth['time']=date('Y-m-d H:i:s',$second_time);
				$birth['created_at']=date('Y-m-d H:i:s',time());
				$birth['updated_at']=date('Y-m-d H:i:s',time());
				$birth['comment']=$other_head1;
				$birth['deliver_type']=3;
				$ret=$mode->table('births')->add($birth);
			}
			
			exit;
	}
	
	public function test(){
		 echo rand(20,40);
	}
}