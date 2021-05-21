<?php
namespace Home\Controller;
use Think\Controller;
class CollectController extends Controller {
    public function monthValue(){

				if(empty($_POST['time'])||empty($_POST['time2'])){
					  $now = time();
					  $v = strtotime(date('Y-m-d',$now))-86400*30;
					  $time =date('Y-m-d',$v);
		  			$time2 =date('Y-m-d',$now);
				}else{
				  	$time =  $_POST['time'];
				  	$time2 =  $_POST['time2'];
				}
		    
		  	$devid = $_GET['devid'];
				$psnid = $_GET['psnid'];
				$estrus = $_GET['estrus'];
				
	    	//dump($estrus+40*86400);
	    	if($estrus){
	    		$start_time = $estrus+40*86400;
	    	}else{
	    		$start_time = strtotime($time);
	    	}
	    	$start_time = strtotime($time);
	    	$end_time = strtotime($time2)+86400;

        $ios_order='time asc';
        
        $dateArr = array();
        $temp1Arr = array();
        $temp2Arr = array();
        
        $dev=M('device')->where(array('psn'=>$psnid,'devid'=>$devid))->find();
        
        if($dev){
        	$psn_now=$dev['psn_now'];
        	$avg_step=$dev['avg_step'];
        }

        $mydb='access_base';
        
        $selectSql=M($mydb)->where('devid ='.$devid.' and psn= '.$psnid.' and time >= '.$start_time.' and time <= '.$end_time)
													        ->group('time')
													        ->order($ios_order)
													        ->select();
        /*if($selectSql)
        {
        		$day_count=($end_time-$start_time)/86400;		
        	
        		for($i=0;$i< $day_count;$i++){
        			$today_start=$start_time+86400*$i;
        			$todaty_end=$start_time+86400*$i+86400;
        			$today_count=0;
        			$today_sum=0;
        			$today_sum2=0;
        			$today_time=date('m-d',$today_start);
	        		foreach($selectSql as $acc){
								if($acc['time']>=$today_start&&$acc['time']< $todaty_end){
									$temp1=$acc['temp1'];
									$temp2=$acc['temp2'];
									$temp3=$acc['env_temp'];
									$a=array($temp1,$temp2);
									$t=max($a);
									if($t>32){
										$vt=(float)$t;
										$today_sum+=$vt;
										$today_sum2+=$temp3;
										$today_count++;
									}
									if($today_count>=24){
										break;
									}
								}
							}
							$today_avg=$today_sum/$today_count;
							$today_avg2=$today_sum2/$today_count;
							//dump($today_start);
							if($estrus){
								$days=(int)($today_start-$estrus)/86400;
								$today_time=$today_time.'('.$days.')';
							}
							array_push($dateArr,$today_time);
							array_push($temp1Arr,number_format($today_avg, 2));
							array_push($temp2Arr,number_format($today_avg2, 2));
							//dump('date:'.$today_time.' temp:'.number_format($today_avg, 2));
        		}
        		//dump($dateArr);
        		//dump($temp1Arr);
        		//dump($temp2Arr);    		
        }*/
        
        $next_time=0;
				foreach($selectSql as $key=>$acc){
					if($key > 1){
						$step = (int)$acc['rssi2'];
						$next_step = 0;
						$cur_time =  (int)$acc['time'];
						//echo 'cmp:';
						//dump(date('Y-m-d H:i:s',$cur_time));
						//dump(date('Y-m-d H:i:s',$next_time));
						if($next_time>0&&$cur_time> $next_time){
							continue;
						}
						
						foreach($selectSql as $acc2){
							$next_time = (int)$acc2['time'];
							if($next_time-$cur_time==7200){
								$next_step =  (int)$acc2['rssi2'];	
								break;
							}
						}
						//dump($pre_step);
						if($next_step==0){
							continue;
						}
						if($next_step-$step>=0){
							$cur_step = $next_step-$step;
						}else{
							if(($acc['rssi3']&0x03)==0x01){
								$cur_step=0;
							}else{
								if($step< 50000){

								}else{
									$cur_step=65535-$step+$next_step;
								}
							}
						}
						$selectSql[$key]['step3']=$cur_step-$avg_step;
						$selectSql[$key]['step2']='+'.$cur_step;
						array_push($dateArr,date('m-d H:i:s',$cur_time));
						array_push($temp1Arr,$cur_step);
						array_push($temp2Arr,$cur_step-$avg_step);
					}
				}
        
	      $this->assign('temp1Arr',json_encode(array_reverse($temp1Arr)));
	      $this->assign('temp2Arr',json_encode(array_reverse($temp2Arr)));
	      $this->assign('dateArr',json_encode(array_reverse($dateArr)));
	      $this->display();
    }
    
    public function todayValue(){
    		$count_num=15;
				if(empty($_POST['time'])||empty($_POST['time2'])){
					  $now = time();
					  $v = strtotime(date('Y-m-d',$now))-86400*15;
					  $time =date('Y-m-d',$v);
		  			$time2 =date('Y-m-d',$now);
				}else{
				  	$time =  $_POST['time'];
				  	$time2 =  $_POST['time2'];
				}
		    
		  	$devid = $_GET['devid'];
				$psnid = $_GET['psnid'];
	    	
	    	$start_time = strtotime($time);
	    	$end_time = strtotime($time2)+86400;
       
        //dump($start_time);
        //dump($end_time);
        //dump($devid);
				//dump($psnid);
        $ios_order='time asc';
        
        $dateArr = array();
        $temp1Arr = array();
        $temp2Arr = array();
        
        $mydb='access_'.$psn;
        if($selectSql=M($mydb)->where('devid ='.$devid.' and psn= '.$psnid.' and time >= '.$start_time.' and time <= '.$end_time)
													        ->group('time')
													        ->order($ios_order)
													        ->select())
        {
        
        		$day_count=($end_time-$start_time)/86400;		
        	
        		for($i=0;$i< $day_count;$i++){
        			$today_start=$start_time+86400*$i;
        			$todaty_end=$start_time+86400*$i+86400;
        			$today_count=0;
        			$today_sum=0;
        			$today_sum2=0;
        			$today_time=date('Y-m-d',$today_start);
	        		foreach($selectSql as $acc){
								if($acc['time']>=$today_start&&$acc['time']< $todaty_end){
									$temp1=$acc['temp1'];
									$temp2=$acc['temp2'];
									$temp3=$acc['env_temp'];
									$a=array($temp1,$temp2);
									$t=max($a);
									$vt=(float)$t;
									$today_sum+=$vt;  
									$today_sum2+=$temp3;
									$today_count++;
									if($today_count>=24){
										break;
									}
								}
							}
							$today_avg=$today_sum/$today_count;
							$today_avg2=$today_sum2/$today_count;
							array_push($dateArr,$today_time);
							array_push($temp1Arr,number_format($today_avg, 2));
							array_push($temp2Arr,number_format($today_avg2, 2));
							//dump('date:'.$today_time.' temp:'.number_format($today_avg, 2));
        		}
        		//dump($dateArr);
        		//dump($temp1Arr);
        		//dump($temp2Arr);    		
        }
	      $this->assign('temp1Arr',json_encode(array_reverse($temp1Arr)));
	      $this->assign('temp2Arr',json_encode(array_reverse($temp2Arr)));
	      $this->assign('dateArr',json_encode(array_reverse($dateArr)));

	      $this->display();
    }

    public function downValuesync(){
				ini_set("memory_limit","2048M");
			  $now = time();
			  $v = strtotime(date('Y-m-d',$now));
				$hours = 10;
		    //$down_value= 35;//$_POST['temp'];
		    
				$psnid = $_GET['psnid'];
				
				$psninfo = M('psn')->where(array('id'=>$psnid))->find();
				$psn=$psninfo['sn'];
				$down_value= $psninfo['htemplev1'];//$_POST['temp'];
				$step_value= $psninfo['hstep'];
				$hstep_count= $psninfo['hstep_count'];
				echo 'PSN:';
				dump($psn);
				dump($down_value);
				dump($step_value);
	    	$start_time = $v+3600*6;
	    	$end_time = $start_time-3600*$hours;
				dump(date('Y-m-d H:i:s',$start_time));
				dump(date('Y-m-d H:i:s',$end_time));
				
        $mydb='access_base';
        
				$psn=$psnid;
				$devSelect=M('device')->where(array('psn'=>$psn))->order('devid desc')->select();
				
        $accs=M($mydb)->field('id,psn,devid,temp1,temp2,rssi2,rssi3,time')->where(['psn'=>$psn])->where('time <= '.$start_time.' and time >= '.$end_time)
									        ->order('time asc')
									        ->select();

				foreach($accs as $key=>$acc){
					$devid=$acc['devid'];
					$temp1=$acc['temp1'];
					$temp2=$acc['temp2'];
					$step=$acc['rssi2'];
					$temp=max($temp1,$temp2);
					if($temp>$down_value){
						$temp_list[$devid][]=$acc['time'];
					}
					$dev['step']=$step;
					$dev['time']=$acc['time'];
					$dev['rssi3']=$acc['rssi3'];
					$step_list[$devid][]=$dev;
				}
						
				//dump($temp_list);
				//dump($step_list);		
								
				foreach($devSelect as $key=>$dev){
					$devid=$dev['devid'];
					$high_flag=0;
					$step_flag=0;
					$pre_time=0;
					if(count($temp_list[$devid])>=5){
						//dump($devid);
						foreach($temp_list[$devid] as $time){
							if($pre_time>0){
								if($time-$pre_time==3600){
									$high_flag+=1;
								}else{
									$high_flag=0;
								}
							}
							$pre_time=$time;
							if($high_flag>=5){
								//dump($devid);
								$ids[]=$devid;
								break;
							}
						}
					}
					//dump($ids);
					$pre_time=0;
					if(count($step_list[$devid])>=5){
						foreach($step_list[$devid] as $key2=>$step_info){
							if($key2< count($step_list[$devid])-1){
								$step = $step_info['step'];
								$cur_time = $step_info['time'];
								$next_time = (int)$step_list[$devid][$key2+1]['time'];
								
								//dump(date('Y-m-d H:i:s',$cur_time));
								//dump($step);
								if($cur_time==$next_time){
									continue;
								}
								if($next_time-$cur_time==3600){
									$next_step = (int)$step_list[$devid][$key2+1]['step'];
									//dump($next_step);
									if($next_step-$step>=0){
										$cur_step = $next_step-$step;
									}else{
										if(($step_info['rssi3']&0x03)==0x01){
											$cur_step=0;
										}else{
											$cur_step=65535+$next_step-$step;
										}
										if($next_step==0){
											$cur_step=0;
										}
									}
								}else{
									$cur_step=0;
								}
							}

							if($cur_step>=$step_value){
								if($pre_time>0){
									if($cur_time-$pre_time==3600){
										$step_flag+=1;
									}else{
										$step_flag=0;
									}
								}else{
									$step_flag+=1;
								}
							}else{
								$step_flag=0;
							}
							$pre_time=$cur_time;
							if($step_flag>=$hstep_count){
								//dump($devid);
								$stepids[]=$devid;
								break;
							}
						}
					}
				}
				
				foreach($ids as $id){
					foreach($stepids as $stepid){
						if($id==$stepid){
							$msglist[]=$id;
							break;
						}
					}
				}
				
				//dump($ids);
				//dump($stepids);
				$step_time_start=$v-86400*20;
				$stepSelect=M('stepmsg')->where('step_time >'.$step_time_start)->select();
				
				dump($msglist);
				//dump($stepSelect);
				if($msglist){
					$wheremsg['devid']=['in',$msglist];
					$devs=M('device')->where(array('psn'=>$psn))->where($wheremsg)->select();
					foreach($devs as $dev){
						$rid=substr($dev['rid'],-8);
						$step_find=false;
						foreach($stepSelect as $stepsel){
							if($stepsel['rid']==$rid){
								$step_find=true;
								break;
							}
						}
						if($step_find==false){
							$stepmsg['rid']=$rid;
							$stepmsg['psn']=$dev['psn'];
							$stepmsg['devid']=$dev['devid'];
							$stepmsg['step_time']=$v;
							$stepmsg_list[]=$stepmsg;
						}
					}
				}
				
				dump($stepmsg_list);
				if($stepmsg_list){
					$ret = M('stepmsg')->addAll($stepmsg_list);
				}

				$ret=M('device')->where(array('psn'=>$psn,'step_state'=>1))->save(array('step_state'=>0));
				$ret=M('device')->where(array('psn'=>$psn,'high_state'=>1))->save(array('high_state'=>0));

				if(count($stepids)>0){
					$wherestep['devid']=['in',$stepids];
					$ret=M('device')->where(array('psn'=>$psn))->where($wherestep)->save(array('step_state'=>1));
				}
				
				if(count($ids)>0){
					$wheredev['devid']=['in',$ids];
					$ret=M('device')->where(array('psn'=>$psn))->where($wheredev)->save(array('high_state'=>1));
				}

				exit;
    }

		public function sendmsg(){

			$id=$_GET['id'];
			$mode=M('','','DB_CONFIG');
			$stepmsg=M('stepmsg')->where(array('flag'=>0))->select();

			foreach($stepmsg as $msg){
				$sn_list[]=$msg['rid'];
			}

			if($sn_list){
				$wherecode['sn_code']=array('in',$sn_list);
				$cows = $mode->table('cows')->where($wheresn)->select();
			}else{
				echo "SN NULL.";
				exit;
			}
			if($cows){
				foreach($cows as $cow){
					$cow_ids[]= $cow['id'];
					$farmers[]=$cow['farmer_id'];
				}
			}else{
				echo "COWS NULL.";
				exit;
			}
			
			$farmers=array_unique($farmers);
			$wherefarmers['fs.id']=array('in',$farmers);
			$farmerlist = $mode->table(array('farmers'=>'fs'))
									->field('fs.id as id,fs.name as name,fs.phone as phone,fs.village_id as village_id,fs.town_id as town_id')
									->where($wherefarmers)
									->select();
						
			foreach($farmerlist as $farmer){
				$farmer_sel[$farmer['id']]=$farmer['name'];
				$phone_sel[$farmer['id']]=$farmer['phone'];
				$villages[]=$farmer['village_id'];
				$towns[]=$farmer['town_id'];
			}
			
			$villages=array_unique($villages);
			$wherevillages['ss.id']=array('in',$villages);
			$villagelist = $mode->table(array('subareas'=>'ss'))
									->field('ss.id as id,ss.name as name')
									->where(array('type'=>'village_id'))
									->where($wherevillages)
									->select();
									
			foreach($villagelist as $village){
				$village_sel[$village['id']]=$village['name'];
			}		

			$towns=array_unique($towns);
			
			$wheretowns['ss.id']=array('in',$towns);
			$townlist = $mode->table(array('subareas'=>'ss'))
									->field('ss.id as id,ss.name as name')
									->where($wheretowns)
									->where(array('type'=>'town_id'))
									->select();
									
			foreach($townlist as $town){
				$town_sel[$town['id']]=$town['name'];
			}
		
		
		
		
			foreach($cows as $cow){
				$msg['farmer']=$farmer_sel[$cow['farmer_id']];
				$msg['phone']=$phone_sel[$cow['farmer_id']];
				$msg['village']=$village_sel[$cow['village_id']];
				$msg['town']=$town_sel[$cow['town_id']];
				$msg['sn_code']=$cow['sn_code'];
			}
			
			foreach($stepmsg as $msg){
				foreach($cows as $cow){
					if($cow['sn_code']==$msg['rid']){
						$savemsg['farmer']=$farmer_sel[$cow['farmer_id']];
						$savemsg['phone']=$phone_sel[$cow['farmer_id']];
						$savemsg['village']=$village_sel[$cow['village_id']];
						$savemsg['town']=$town_sel[$cow['town_id']];
						$savemsg['flag']=1;
						$ret=M('stepmsg')->where(array('id'=>$msg['id']))->save($savemsg);
						$tmp = '19482119';
						$smsmsg=array($savemsg['farmer'],$msg['rid']);
						$phone1=$savemsg['phone'];
						if($phone1){
							//$phone1="13311152676";
							$phone=array($phone1);
							//$ret=send163msgtmp($phone,$smsmsg,$tmp);
							//dump($phone);
							//dump($smsmsg);
							dump($ret);
						}
						break;
					}
				}
			}
		}
	
    public function downValue2(){
				ini_set("memory_limit","2048M");
			  $now = time();
			  $v = strtotime(date('Y-m-d H:i:s',$now));
				$hours = 12;
		    $down_value= 35;//$_POST['temp'];
		    
				$psnid = $_GET['psnid'];
				
				$psninfo = M('psn')->where(array('id'=>$psnid))->find();
				$psn=$psninfo['sn'];
				$down_value= $psninfo['htemplev1'];//$_POST['temp'];

	    	$start_time = $v;
	    	$end_time = $start_time-3600*$hours;

        $mydb='access_base';
        
				$psn=$psnid;
				$devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psn))->order('devid desc')->select();
				
        $accs=M($mydb)->where(['psn'=>$psn])->where('time <= '.$start_time.' and time >= '.$end_time)
									        ->order('time asc')
									        ->select();

				foreach($accs as $key=>$acc){
					$devid=$acc['devid'];
					$temp1=$acc['temp1'];
					$temp2=$acc['temp2'];
					$temp=max($temp1,$temp2);
					if($temp>$down_value){
						$temp_list[$devid][]=$acc['time'];
					}
				}
				//dump($temp_list);
				//exit;
				
				foreach($devSelect as $key=>$dev){
					$devid=$dev['devid'];
					$high_flag=0;
					$pre_time=0;
					if(count($temp_list)>=3){
						foreach($temp_list[$devid] as $time){
							if($pre_time>0){
								if($time-$pre_time==3600){
									$high_flag+=1;
								}else{
									$high_flag=0;
								}
							}
							$pre_time=$time;
						}
						if($high_flag>=3){
							//dump($devid);
							$ids[]=$devid;
						}
					}
				}
				
				
				if(count($ids)>0){
					$wheredev['devid']=['in',$ids];
					$ret=M('device')->where(array('psn'=>$psn))->where($wheredev)->save(array('high_state'=>1));
				}
				
				$devs=M('device')->where(array('psn'=>$psn,'high_state'=>1))->select();
        $this->assign('devSelect',$devs);
	      $this->display();
    }
         
    public function downValue(){
				$psnid = $_GET['psnid'];
				if($psnid==NULL){
					$devs=M('device')->where('high_state=1 or step_state=1')->select();
	        $this->assign('devSelect',$devs);
		      $this->display();
		      exit;
				}
				$psninfo = M('psn')->where(array('id'=>$psnid))->find();
				$psn=$psninfo['sn'];
				$devs=M('device')->where('high_state=1 or step_state=1')->where(['psn'=>$psn])->select();
        $this->assign('devSelect',$devs);
	      $this->display();
    }

		public function hightempset(){
			$temp=$_GET['temp'];
			if($temp>32){
				$ret=M('psn')->where('1')->save(array('htemplev1'=>$temp));
				$this->assign('temp',$temp);
			}
			dump($ret);
			dump($temp);
			//$this->display();
			exit;
		}

		public function hightempget(){


			$ret=M('psn')->find();
				
			dump($ret['htemplev1']);
			//$this->display();
			exit;
		}
		
		public function highstepset(){
			$step=$_GET['step'];
			if($step>100){
				$ret=M('psn')->where('1')->save(array('hstep'=>$step));
				//$this->assign('step',$step);
			}
			dump($ret);
			dump($step);
			//$this->display();
			exit;
		}

		public function highstepget(){


			$ret=M('psn')->find();
				
			dump($ret['hstep']);
			//$this->display();
			exit;
		}
		
		public function config(){
			$htemplev1=$_POST['htemplev1'];
			$hstep=$_POST['hstep'];
			$hstep_count=$_POST['hstep_count'];
			
			$psninfo=M('psn')->find();
			
			if($htemplev1==NULL&&$hstep==NULL&&$hstep_count==NULL){
				//dump($psninfo);
				$this->assign('psninfo',$psninfo);
				$this->display();
				exit;
			}
						

			if($psninfo['htemplev1']!=$htemplev1){
				$psnsave['htemplev1']=$htemplev1;
			}
			if($psninfo['hstep']!=$hstep){
				$psnsave['hstep']=$hstep;
			}
			if($psninfo['hstep_count']!=$hstep_count){
				$psnsave['hstep_count']=$hstep_count;
			}
			
			if(!empty($psnsave)){
				$ret=M('psn')->where('1')->save($psnsave);
				$psninfo=M('psn')->find();
				$this->assign('psninfo',$psninfo);
				$this->display();
			}else{
				$this->assign('psninfo',$psninfo);
				$this->display();
			}
		}	
			
    public function signdownValue(){
    		$count_num=15;
				if(empty($_POST['time'])||empty($_POST['time2'])){
					  $now = time();
					  $v = strtotime(date('Y-m-d',$now))-86400*15;
					  $time =date('Y-m-d',$v);
		  			$time2 =date('Y-m-d',$now);
				}else{
				  	$time =  $_POST['time'];
				  	$time2 =  $_POST['time2'];
				}
		    $down_value=-100;
		    
				$psnid=$_GET['psnid'];
				$productno=$_GET['productno'];
				$bdevinfo = M('bdevice')->where(array('psnid'=>$psnid))->find();

				$delay_str= $bdevinfo['uptime'];
				$count= $bdevinfo['count'];
				
				$psninfo= M('psn')->where(array('id'=>$psnid))->find();
				if($psninfo){
				  $psn=$psninfo['sn'];
				}else{
					echo 'PSN ERR.';
					exit;
				}
	    	
	    	$start_time = strtotime($time2)-86400;
	    	$end_time = strtotime($time2)+86400;

        $ios_order='time asc';
        
        $dateArr = array();
        $temp1Arr = array();
        $temp2Arr = array();
        
        $mydb='access_base';
        
				$psn=$psnid;
				//$devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psn,'flag'=>1))->order('devid desc')->select();
				$devSelect=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
				
				for($i=0;$i<count($devSelect);$i++)
				{
					$devSelect[$i]['down_count']=0;
				}
		
        $selectSql=M($mydb)->where('rssi1 <'.$down_value.' and time >= '.$start_time.' and time <= '.$end_time)->where(array('psn'=>$psn))
													        ->order($ios_order)
													        ->select();
				//dump($selectSql);
        if($selectSql)
        {
        		foreach($devSelect as $key=>$dev){
        			$devid=$dev['devid'];
        			foreach($selectSql as $acc){
        				$cur_time=$acc['time'];
        				if($devid==$acc['devid']){
        					if(in_array($cur_time,$devSelect[$key]['down_time'],true)){
        						//nothing
        					}else{
	        					$devSelect[$key]['down_count']=$devSelect[$key]['down_count']+1;
	        					$devSelect[$key]['down_time'][]=$cur_time;
        					}
        					//dump($devSelect[$key]);
        				}
        			}
        		}
        }

        $this->assign('devSelect',$devSelect);
	      //dump($devSelect);
	      $this->display();
    }
   
    public function signlastValue(){
    		$count_num=15;
				if(empty($_POST['time'])||empty($_POST['time2'])){
					  $now = time();
					  $v = strtotime(date('Y-m-d',$now))-86400*15;
					  $time =date('Y-m-d',$v);
		  			$time2 =date('Y-m-d',$now);
				}else{
				  	$time =  $_POST['time'];
				  	$time2 =  $_POST['time2'];
				}
		    $down_value=-100;
		    
				$psnid=$_GET['psnid'];
				$productno=$_GET['productno'];
				$bdevinfo = M('bdevice')->where(array('psnid'=>$psnid))->find();

				$delay_str= $bdevinfo['uptime'];
				$count= $bdevinfo['count'];
				
				$delay = substr($delay_str,0, 2);
				$delay = (int)$delay;

				$delay = 3600*$delay;
				$delay_sub = $delay/$count;
				
				$psninfo= M('psn')->where(array('id'=>$psnid))->find();
				if($psninfo){
				  $psn=$psninfo['sn'];
				}else{
					echo 'PSN ERR.';
					exit;
				}

	    	$now = time();
				$start_time = strtotime(date('Y-m-d',$now));
	    	$cur_time = $now - $start_time;
	    	$cur_time = (int)($cur_time/$delay)*$delay;
	    	$first_time = $cur_time-$delay+$start_time;
    	
	    	$start_time = strtotime($time);
	    	$end_time = strtotime($time2)+86400;

        $ios_order='time asc';
        
        $dateArr = array();
        $temp1Arr = array();
        $temp2Arr = array();
        
        $mydb='access_base';
        
				$psn=$psnid;

				$$devlist=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
				
				for($i=0;$i<count($devSelect);$i++)
				{
					$devSelect[$i]['down_count']=0;
				}
		
        $accSelect2=M($mydb)->where(array('psn'=>$psn,'time'=>$first_time))->order('devid asc')->select();
        
				foreach($devlist as $dev){
					$devid = $dev['devid'];
					$dev_find=false;
					foreach($accSelect2 as $acc){
						if($devid==$acc['devid']){
							$dev_find=true;
							$al_find=false;
							foreach($acclist2 as $al){
								if($devid==$al['devid']){
									$al_find=true;
									break;
								}
							}
							if($al_find==false){
								$acclist2[]=$acc;
							}
						}
					}
					if($dev_find==false){
						if($acc_lost){
							$acc_lost=$acc_lost.','.$devid;
						}else{
							$acc_lost=$devid;
						}
					}
				}

				$this->assign('acclist',$acclist2);
				$this->assign('devlost',$acc_lost);
	      //dump($devSelect);
	      $this->display();
    }
    
		public function avgtoday(){
			$psnid=$_GET['psnid'];
			$now = time();
		  $time =date('Y-m-d',$now);
		  dump($time);
			$start_time = strtotime($time)-86400;
			$end_time = strtotime($time)+86400;
			$max_count=24;
			$env_check=0.4;
			
			$devs=M('device')->where(array('flag'=>1,'psn'=>$psnid,'dev_type'=>0))->select();
			
			foreach($devs as $dev){
				$devid=$dev['devid'];
				$avg = $dev['avg_temp'];
				if($avg>0){
					//continue;
				}
				
				//var_dump($devid);
				$mydb='access_'.$psnid;
				$accss=M($mydb)->where('time >='.$start_time.' and time <='.$end_time)
														->where(array('devid'=>$devid,'psn'=>$psnid))->group('time')->order('time desc')->limit(0,$max_count)
														->select();
				$temp=NULL;
				$count = count($accss);
				if($count==0){
					dump('devid count 0 :'.$devid);
					//var_dump(count($accss));
					//continue;
				}
				$sum=0;
				$cur_count=0;
				for($i=0;$i< $count;$i++){
					$acc=$accss[$i];
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
			  	$devSave=M('device')->where(array('psn'=>$psnid,'devid'=>$devid))->save(array('avg_temp'=>$avg));
				}

			}
			exit;
		}
}