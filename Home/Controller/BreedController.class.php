<?php
namespace Home\Controller;
use Think\Controller;
class BreedController extends Controller {
    public function breedlist_old(){

		  	$devid = $_GET['devid'];
				$psnid = $_GET['psnid'];
	    	
				$devSelect=M('breed')->where(array('psnid'=>$psnid,'devid'=>$devid))->order('time asc')->select();
				$this->assign('devSelect',$devSelect);
				$this->display();
    }

    public function addbreed(){
    	$psnid=$_GET['psnid'];
    	$devid=$_GET['devid'];
    	$uptime=$_POST['uptime'];
    	
    	if(empty($uptime)){
    		$this->assign('psnid',$psnid);
    		$this->assign('devid',$devid);
    		$this->assign('uptime',$uptime);
    		$this->display();
    		exit;
    	}
    	$devsave['psnid']=$psnid;
    	$devsave['tsn']=$psn['tsn'];
    	$devsave['psn']=$psn['sn'];
    	$devsave['id']=$id;
    	$devsave['psn']=$sn;
    	$devsave['rate_id']=$rate_id;
    	$devsave['uptime']=$uptime;
    	$devsave['count']=$count;
    	$devsave['sn']=$sn;
			$devsave['url']=$url;
    	if($have=M('bdevice')->where(array('psnid'=>$psnid,'id'=>$id))->find()){
					$this->assign('errcode',"1001");
				  $this->display();
				  exit;
    	}else{
    		$ret=M('bdevice')->add($devsave);
				$this->redirect('Devselect/station',array('psnid'=>$psnid),0,'');
    	}
    }
    
    public function delbreed(){
    	$autoid=$_GET['autoid'];
    	$have=M('bdevice')->where(array('autoid'=>$autoid))->find();
    	if($have){
    		$ret=M('bdevice')->where(array('autoid'=>$autoid))->delete();
    		$this->redirect('Devselect/station',array('psnid'=>$have['psnid']),0,'');
    	}
    }
    
	  public function breedlist(){
	  	$alarm_b=$_POST['count'];

			if($alarm_b==NULL){
				$alarm_b=20;
			}
			
		  $now = time();
		  $v = strtotime(date('Y-m-d',$now))-$alarm_b*86400;
			$end_time=$v;
			$wheretime='bs.breeding_time <='.$end_time;

			$mode=M('','','DB_CONFIG');
			$breedlist = $mode->table(array('breedings'=>'bs','cows'=>'cs'))
									->field('cs.sn_code,cs.farmer_id,cs.village_id,bs.breeding_time,bs.admin_id,bs.breeding_tel')
									->where('bs.cows_id = cs.id and '.$wheretime)
									->group('cs.sn_code')
									->order('bs.breeding_time desc')
									->select();
			//dump($mode->getlastsql());
			foreach($breedlist as $key=>$breed){
				$farmers[]=$breed['farmer_id'];
			}

			$farmers=array_unique($farmers);
			$wherefarmers['fs.id']=array('in',$farmers);
			$farmerlist = $mode->table(array('farmers'=>'fs'))
									->field('fs.id as id,fs.name as name ,fs.village_id as village_id')
									->where($wherefarmers)
									->select();
						
			foreach($farmerlist as $farmer){
				$farmer_sel[$farmer['id']]=$farmer['name'];
				$villages[]=$farmer['village_id'];
			}
			
			$villages=array_unique($villages);
			$wherevillages['ss.id']=array('in',$villages);
			$villagelist = $mode->table(array('subareas'=>'ss'))
									->field('ss.id as id,ss.name as name')
									->where($wherevillages)
									->select();
									
			foreach($villagelist as $village){
				$village_sel[$village['id']]=$village['name'];
			}

		  $today = strtotime(date('Y-m-d',time()));
			foreach($breedlist as $key=>$breed){
				$breedlist[$key]['farmer']=$farmer_sel[$breed['farmer_id']];
				$breedlist[$key]['village']=$village_sel[$breed['village_id']];
				$breedlist[$key]['days']=($today-$breed['breeding_time'])/86400;
			}
			//dump($breedlist);

			$this->assign('breedlist',$breedlist);
	    $this->assign('date',$time);
	    $this->assign('date2',$time2);
	    $this->assign('count',$alarm_b);
			$this->display();
		}
	  
	  public function childlist(){
	  	$mode=M('','','DB_CONFIG');
			if($alarm_b==NULL){
				$alarm_info=$mode->table('alarm_config')->where(array('type'=>'child'))->find();
				$alarm_b=$alarm_info['days_min'];
			}
			
			$childlist = $mode->table('alarm_dev')->where(array('type'=>$alarm_info['id']))->select();
			$today = strtotime(date('Y-m-d',time()));
			foreach($childlist as $key=>$child){
				$childlist[$key]['days']=($today-$child['alarm_time'])/86400;
			}


			$this->assign('childlist',$childlist);
	    $this->assign('date',$time);
	    $this->assign('date2',$time2);
	    $this->assign('count',$alarm_b);
			$this->display();
		}
		
	  public function childsync(){

	  	$mode=M('','','DB_CONFIG');
			$alarm_info=$mode->table('alarm_config')->where(array('type'=>'child'))->find();
			if($alarm_info==NULL){
				echo 'no alarm_config.';
				exit;
			}
			$alarm_b_min=$alarm_info['days_min'];
			$alarm_b_max=$alarm_info['days_max'];
			
			$alarm_list=$mode->table('alarm_dev')->where(array('alarm_type'=>2))->select();
			
			
		  $now = time();
		  $v = strtotime(date('Y-m-d',$now));
		  $start_time=$v-$alarm_b_max*86400;
			$end_time=$v-$alarm_b_min*86400;
			
			$wheretime='UNIX_TIMESTAMP(cbs.time) >='.$start_time.' and UNIX_TIMESTAMP(cbs.time) <='.$end_time;
					
			$childlist = $mode->table(array('childbirths'=>'cbs','cows'=>'cs'))
									->field('cs.sn_code,cs.farmer_id,cs.village_id,cbs.time,cbs.num,UNIX_TIMESTAMP(time) as addtime')
									->where('cbs.cow_id=cs.id and '.$wheretime)
									->group('cs.sn_code')
									->order('cbs.time desc')
									->select();

			if($childlist){
					foreach($childlist as $key=>$child){
						$farmers[]=$child['farmer_id'];
					}
					
					$farmers=array_unique($farmers);
					$wherefarmers['fs.id']=array('in',$farmers);
					$farmerlist = $mode->table(array('farmers'=>'fs'))
											->field('fs.id as id,fs.name as name,fs.phone as phone,fs.village_id as village_id')
											->where($wherefarmers)
											->select();
								
					foreach($farmerlist as $farmer){
						$farmer_sel[$farmer['id']]=$farmer['name'];
						$phone_sel[$farmer['id']]=$farmer['phone'];
						$villages[]=$farmer['village_id'];
					}
					
					$villages=array_unique($villages);
					$wherevillages['ss.id']=array('in',$villages);
					$villagelist = $mode->table(array('subareas'=>'ss'))
											->field('ss.id as id,ss.name as name')
											->where($wherevillages)
											->select();
											
					foreach($villagelist as $village){
						$village_sel[$village['id']]=$village['name'];
					}


					foreach($childlist as $key=>$child){
						$alarm_find=false;
						//dump($child);
						foreach($alarm_list as $alarm){
							//dump($alarm);
							if($alarm['alarm_time']-$v >$alarm_b_max*86400){
								$alarm_del[]=$alarm['id'];
								echo 'del alarm:';
								dump($alarm);
							}
							if($alarm['sn_code']==$child['sn_code']
								&&$alarm['alarm_time']==strtotime($child['time'])
								&&$alarm['type']==$alarm_info['id']
							){
								//dump($child);
								$alarm_find=true;
								break;
							}
						}
						if($alarm_find==false){
							$alarm_dev=array(
								'sn_code'=>$child['sn_code'],
								'type'=>$alarm_info['id'],
								'sms_type'=>$alarm_info['sms_type'],
								'alarm_time'=>strtotime($child['time']),
								'farmer_id'=>$child['farmer_id'],
								'farmer_name'=>$farmer_sel[$child['farmer_id']],
								'farmer_phone'=>$phone_sel[$child['farmer_id']],
								'farmer_village'=>$village_sel[$child['village_id']]
							);
							$alarm_dev_list[]=$alarm_dev;
						}
					}
					echo 'ALARM DEV LIST:';
					dump($alarm_dev_list);
					if($alarm_dev_list){
						$ret=$mode->table('alarm_dev')->addAll($alarm_dev_list);
					}
					if($alarm_del){
						$wheredel['id']=array('in',$alarm_del);
						$ret=$mode->table('alarm_dev')->where($wheredel)->del();
					}
			}


		}	
		
	  public function childsync2(){
	  	$mode=M('','','DB_CONFIG');
	  	$temp_count=6;
	  	$alarm_info=$mode->table('alarm_config')->where(array('type'=>'child'))->find();
			if($alarm_info==NULL){
				echo 'no alarm_config.';
				exit;
			}
			
			$alarm_b_min=$alarm_info['days_min'];
			$alarm_b_max=$alarm_info['days_max'];
			
			$childlist = $mode->table('alarm_dev')->where(array('sms_state'=>0))->select();
			
		  $now = time();
		  $v = strtotime(date('Y-m-d',$now));
		  $start_time = $v-86400;
		  
		  dump(date('Y-m-d',$start_time));
		  
		  $breed_start_time = $v-$alarm_b_max*86400;
		  
			$wherebreedtime='UNIX_TIMESTAMP(bs.breeding_time) >='.$breed_start_time;
			
			$breedlist = $mode->table(array('breedings'=>'bs','cows'=>'cs'))
									->field('cs.sn_code,bs.breeding_time')
									->where('bs.cows_id = cs.id and '.$wherebreedtime)
									->group('cs.sn_code')
									->order('bs.breeding_time desc')
									->select();
									
			$pg=M();
			foreach($childlist as $key=>$child){
					$auto_id=$child['id'];
					$sn=$child['sn_code'];
					if($child['temp_time']-$start_time>0){
						continue;
					}
					$breed_find=false;
					foreach($breedlist as $breed){
						if($breed['sn_code']==$sn){
							$breed_find=true;
							if($breed['breeding_time']-$child['alarm_time']>=$alarm_b_min*86400){
								$alarm_del[]=$auto_id;	
								echo 'del alarm sn:'.$sn;
							}else{
								echo 'del alarm err alarm:'.$sn;
								dump($child);
								dump($breed);
							}
							break;
						}
					}
					if($breed_find==true){
						continue;
					}
					$rid=(int)$sn;
					$sn=str_pad($sn,9,'0',STR_PAD_LEFT);
			    $psn=(int)substr($sn,0,5);
			    $devid=(int)substr($sn,5,4);
					$dev=$pg->table('device')->field('psn_now,avg_temp')->where(array('rid'=>$rid))->find();
					if($dev){
						$temp_avg=(float)$dev['avg_temp'];
						if($dev['psn_now']==0){
							$mytable='access_'.$psn;
						}else{
							$mytable='access1301_'.$dev['psn_now'];
						}
						dump($mytable);
						$acc_list=$pg->table($mytable)->field('temp1,temp2,time')
													->where(array('psn'=>$psn,'devid'=>$devid))
													->where('time>'.$start_time)
													->where('temp1>32')
													->where('temp2>32')
													->group('time')
													->order('time desc')
													->limit(0,$temp_count)
													->select();
						//dump($acc_list);
						$today = strtotime(date('H:i:s',time()));
						//dump($sn);
						dump($today);
						$sum_temp=0;
						$v=0;
						$temp_time=0;
						$cur_count=0;
						foreach($acc_list as $acc){
							$a=array($acc['temp1'],$acc['temp2']);
							$t=max($a);
							if($v < $t){
								$v=$t;
								$temp_time= $acc['time'];
							}
							//dump($t);
							$sum_temp+=$t;
							$cur_count++;
						}
						$temp_now=number_format($sum_temp/$cur_count,2);

						//dump($temp_avg);
						//dump($temp_now);
						if($temp_now-$temp_avg>1){
							$sms_state=1;
						}else{
							$sms_state=0;
						}
						$alarm_dev=array(
							'temp_avg'=>$temp_avg,
							'temp_now'=>$temp_now,
							'temp_time'=>$temp_time,
							'sms_state'=>$sms_state,
						);
						dump($sn);
						dump($alarm_dev);
						$ret = $mode->table('alarm_dev')->where(array('id'=>$auto_id))->save($alarm_dev);
					}

			}
			if($alarm_del){
				$wheredel['id']=array('in',$alarm_del);
				$ret=$mode->table('alarm_dev')->where($wheredel)->del();
			}

		}
		
	  public function allsmspush(){
	  	$mode=M('','','DB_CONFIG');
			$childlist = $mode->table('alarm_dev')->where(array('sms_state'=>2))->select();
			
		  $now = time();
		  $start_time = strtotime(date('Y-m-d',$now))-86400;
		  
		  
		  echo 'Push ALL Alarm.';
			foreach($childlist as $key=>$child){
					$auto_id=$child['id'];
					$sn=$child['sn_code'];
					$rid=(int)$sn;
					$sn=str_pad($sn,9,'0',STR_PAD_LEFT);
			    $psn=(int)substr($sn,0,5);
			    $devid=(int)substr($sn,5,4);
					$phone[]=$child['farmer_phone'];
					$other='SN:'.$sn;
					$smsmsg[]=$child['farmer_name'];
					$smsmsg[]=$other;
					dump($smsmsg);
					if($child['alarm_type']==1){
						$tmp='14854123';
					}else if($child['alarm_type']==2){
						$tmp='14854123';
					}else{
						$tmp='14854123';
					}   
					//send163msgtmp($phone,$smsmsg,$tmp);
					$ret=true;
					if($ret){
						$ret=$mode->table('alarm_dev')->where(array('id'=>$auto_id))->save(array('sms_state'=>3));
					}
			}



		}
		
	  public function child_alarm_sms_set(){
	  	$mode=M('','','DB_CONFIG');
	  	$id=$_GET['id'];
			$childlist = $mode->table('alarm_dev')->where(array('id'=>$id))->save(array('sms_state'=>2));
			$this->redirect('/breed/childlist');
			exit;
		}
		
}