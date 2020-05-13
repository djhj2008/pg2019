<?php
namespace Home\Controller;
use Tools\HomeController; 
use Think\Controller;
class ProductController extends HomeController {
		public function rfidscan(){
			dump(date('Y-m-d H:i:s',time()));
			//$access=M('access')->where(array('psn'=>2,'psnid'=>0))->save(array('psnid'=>2));
			//$access=M('access')->where(array('psn'=>3,'psnid'=>0))->count();
			dump(date('Y-m-d H:i:s',time()));
			//$ret=M('access')->field('id')->where(array('psnid'=>0))->order('time desc')->count();
			dump('OK:'.$access);
			exit;
		}
	
		public function devpsn(){
			$devs=M('device')->field('id,psn,psnid,devid')->where(array('psnid'=>0))->order('id desc')->select();
			foreach($devs as $dev){
				$dev_psn=$dev['psn'];
				$dev_psnid=$dev['psnid'];
				$devid=$dev['devid'];
				if($psnid==0){
					//$ret=M('device')->where(array('id'=>$dev['id']))->save(array('psnid'=>$dev_psn));
					dump('sn:'.$devid.' dev_psn:'.$dev_psn);
				}
			}
			echo 'OK';
			exit;
		}
		
	  public function delrfid(){
			$devs=M('device')->field('id,psn,psnid,devid,rid')->where(array('psnid'=>11))->select();
			foreach($devs as $dev){
				$rid = $dev['rid'];
				foreach($devs as $dev2){
					if($dev2['rid']==$rid&&$dev['id']!=$dev2['id']){
						//dump($dev);
						dump($dev2['rid']);
						//$ret=M('device')->where(array('id'=>$dev2['id']))->delete();
						break;
					}
				}
			}
			echo 'OK';
			exit;
		}
		
		public function devlist(){
			$psnid = 12;//$_GET['psnid'];
			$devSelect=M('access')->where(array('time'=>1557604800,'psn'=>$psnid))->order('devid asc')->select();
			//dump($dev);
			$this->assign('devSelect',$devSelect);
			$this->display();
		}	
	
		public function productlist(){
			$psnid = $_GET['psnid'];
			$productlog=M('productlog')->where(array('psnid'=>$psnid))->select();
			$this->assign('productlog',$productlog);
			$this->display();
		}
		
		public function addfactory(){
			$productno=$_GET['productno'];
			$productlog=M('productlog')->where(array('productno'=>$productno))->find();
			$psnid=$productlog['psnid'];
			$mytab='product'.$productno;
			dump('psnid:'.$psnid);		
			if($productno==NULL){
				echo 'PRODUCTNO ERR.';
				exit;
			}
			dump('productno:'.$productno);		
			$product=M($mytab)->select();
			//dump($product);		
    	foreach($product as $v){
    		$snstr = $v['sn'];
				$sn_start=strlen($snstr)-4;
  			$psn = substr($snstr,0,$sn_start);
  			$sn = substr($snstr,$sn_start,4);
  			$psn=(int)$psn;
  			$sn=(int)$sn;
  			//dump($sn);
  			$psnfind=M('psn')->where(array('sn'=>$psn))->find();
  			if(!$psnfind){
  				echo 'PSN ERROR :'.$psn;
  				exit;
  			}
  			$psnid=$psnfind['id'];
  			//dump($psnid);
  			//dump($sn);
  			$devfind=M('factory')->where(array( 'psnid'=>$psnid,
																      			'devid'=>$sn))->select();
				$mytime=strtotime($v['time']);
	
  			if(empty($devfind)){
  				$devadd = array( 
  											'psnid'=>$psnid,
						      			'devid'=>$sn,
						      			'productno'=>$productno,
												'state'=>1,
						      			'fsn'=>"ABC",
						      			'time'=>$v['time']);
					dump('add dev:'.$sn);
					$ret=M('factory')->add($devadd); 
					continue;     			
  			}
  			if(count($devfind)<2){
  				//dump($devfind[0]['productno']);
	  			if($devfind[0]['productno']!=$productno){
	  				$devadd = array(
	  											'psnid'=>$psnid,
							      			'devid'=>$sn,
							      			'productno'=>$productno,
													'state'=>1,
							      			'fsn'=>"ABC",
							      			'time'=>$v['time']);
						dump('add dev 2:'.$sn);
						$ret=M('factory')->add($devadd); 
	  			}
	  			
  			}
    	}
    	dump('finish!');
			exit;
		}

		public function scanfactory(){
			$psnid=$_GET['psnid'];
			$productno=$_GET['productno'];
			$bdevinfo = M('bdevice')->where(array('psnid'=>$psnid))->find();
			$psn=$bdevinfo['psn'];
			$delay_str= $bdevinfo['uptime'];
			$count= $bdevinfo['count'];
			
			$delay = substr($delay_str,0, 2);
			$delay = (int)$delay;

			$delay = 3600*$delay;
			$delay_sub = $delay/$count;

			if($productno==NULL){
				echo 'PRODUCTNO ERR.';
				exit;
			}
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
			//dump(date('Y-m-d H:s:i',$first_time));
			//dump(date('Y-m-d H:s:i',$pre_time));
			//dump(date('Y-m-d H:s:i',$pre2_time));
			
    	$devlist=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
    	}
    	//dump($devlist);
    	$wheredev['devid']=array('in',$devidlist);

    	$mydb='access_'.$psn;
    	$accSelect1=M($mydb)->where(array('psn'=>$psn,'time'=>$first_time))->where($wheredev)->order('devid asc')->select();
			$accSelect2=M($mydb)->where(array('psn'=>$psn,'time'=>$pre_time))->where($wheredev)->order('devid asc')->select();
			$accSelect3=M($mydb)->where(array('psn'=>$psn,'time'=>$pre2_time))->where($wheredev)->order('devid asc')->select();
			
			for($i=30;$i<40;$i++){
    		$mydb='access1301_'.$i;
    		$acc1301list1[$i]=M($mydb)->where(array('psn'=>$psn,'time'=>$first_time))->order('devid asc')->select();
    	}
			for($i=30;$i<40;$i++){
    		$mydb='access1301_'.$i;
    		$acc1301list2[$i]=M($mydb)->where(array('psn'=>$psn,'time'=>$pre_time))->order('devid asc')->select();
    	}
			for($i=30;$i<40;$i++){
    		$mydb='access1301_'.$i;
    		$acc1301list3[$i]=M($mydb)->where(array('psn'=>$psn,'time'=>$pre2_time))->order('devid asc')->select();
    	}

			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$psnid = $dev['psnid'];
				$acc_size=0;
				unset($acc_list);
				$acc_list = array();
				foreach($accSelect1 as $acc){
					if($acc['devid']==$devid){

						$acc_list[]=$acc;
						break;
					}
				}
				foreach($accSelect2 as $acc){
					if($acc['devid']==$devid){

						$acc_list[]=$acc;
						break;
					}
				}
				foreach($accSelect3 as $acc){
					if($acc['devid']==$devid){

						$acc_list[]=$acc;
						break;
					}
				}
				
				$acc_size=count($acc_list);

				if($acc_size==0){
						for($i=30;$i<40;$i++){
							foreach($acc1301list1[$i] as $acc){
								if($devid==$acc['devid']){
									$acc_list[]=$acc;
									break;
								}
							}
							foreach($acc1301list2[$i] as $acc){
								if($devid==$acc['devid']){
									$acc_list[]=$acc;
									break;
								}
							}
							foreach($acc1301list3[$i] as $acc){
								if($devid==$acc['devid']){
									$acc_list[]=$acc;
									break;
								}
							}
						}
				}
				$acc_size=count($acc_list);
				
				if($acc_size< 3){
						if($acc_size==0){
      		  		$dev_none[]=$devid;
      			}else{
      				$dev_lost[]=$devid;
      			}
      			continue;
				}
				$dev_pass[]=$devid;
			}

			if($dev_lost){
				$wherelost['devid']=array('in',$dev_lost);
				$ret=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where($wherelost)->save(array('state'=>3));
			}
			if($dev_none){
				$wherenone['devid']=array('in',$dev_none);
				$ret=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where($wherenone)->save(array('state'=>4));
				//week
				//dump($dev_none);
				
				if(!empty($dev_none)){
					$mydb='access_'.$psn;
					$accweekSelect=M($mydb)->where($wherenone)->where('time >='.$week_time.' and time <='.$end_time)->where(array('psn'=>$psn))->order('time desc')->select();
					for($i=30;$i<40;$i++){
		    		$mydb='access1301_'.$i;
		    		$accweek1301list[$i]=M($mydb)->where($wherenone)->where('time >='.$week_time.' and time <='.$end_time)->where(array('psn'=>$psn))->order('time desc')->select();
		    	}
		    	
					foreach($dev_none as $dev_week_id){
						$acc_week_find=false;
						foreach($accweekSelect as $acc){
							if($acc['devid']==$dev_week_id){
								//echo 'access';
								//dump($dev_week_id);
								$acc_week_find=true;
								break;
							}
						}
						if($acc_week_find==false){
							for($i=30;$i<40;$i++){
								foreach($accweek1301list[$i] as $acc){
									if($dev_week_id==$acc['devid']){
										//echo 'access1301';
										//dump($dev_week_id);
										$acc_week_find=true;
										break;
									}
								}
							}
						}

						if($acc_week_find==false){
							$dev_week_none[]=$dev_week_id;
						}
					}
				}
				if(!empty($dev_week_none)){
					$whereweeknone['devid']=array('in',$dev_week_none);
					$ret=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where($whereweeknone)->save(array('state'=>5));
				}
				
				/*
				if(!empty($dev_week_none)){
					$whereweeknone['devid']=array('in',$dev_week_none);
					$ret=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where($whereweeknone)->save(array('state'=>5));
					
					//month
					$accnoneSelect=M('access')->where($whereweeknone)->where('time >='.$week_time.' and time <='.$end_time)->where(array('psnid'=>$psnid))->order('time desc')->select();
					foreach($dev_week_none as $dev_mon_id){
						$acc_mon_size=0;
						unset($acc_mon_list);
						$acc_mon_list = array();
						foreach($accmonSelect as $acc){
							if($acc['devid']==$dev_mon_id){
								$acc_mon_list[]=$acc;
							}
						}
						$acc_mon_size=count($acc_mon_list);
						if($acc_mon_size==0){
							$dev_mon_none[]=$dev_mon_id;
						}
					}
				}
				
				if(!empty($dev_week_none)){
					$wheremonnone['devid']=array('in',$dev_mon_none);
					$ret=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where($wheremonnone)->save(array('state'=>6));
				}
				*/
				
			}
			if($dev_pass){
				$wherepass['devid']=array('in',$dev_pass);
				$ret=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where($wherepass)->save(array('state'=>2));
			}

			$devSelect=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where('state>=4')->order('devid asc')->select();
			$this->assign('devSelect',$devSelect);
			$this->display();
		}

		public function factorytempeor(){
			$psnid=$_GET['psnid'];
			$productno=$_GET['productno'];
			$delay = 1*3600;
			$delay_sub = 1*3600;
			$bdevinfo = M('bdevice')->where(array('psnid'=>$psnid))->find();
			$psn=$bdevinfo['psn'];
			if($productno==NULL){
				echo 'PRODUCTNO ERR.';
				exit;
			}
    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	//var_dump($start_time);
    	$yes_time = $start_time;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;
    	//var_dump($cur_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time;
    	$last_time = $cur_time-$delay+$start_time-$delay-$delay_sub;	
				
			$devSelect=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('devid asc')->select();

			foreach($devSelect as $dev){
				$devlist[]=$dev['devid'];
			}
			
			$wheredev['devid']=array('in',$devlist);
			
			$mydb='access_'.$psn;
			$accSelect=M($mydb)->where('time >='.$last_time.' and time <='.$first_time)->where(array('psn'=>$psn))->where($wheredev)->group('devid')->select();

			foreach($accSelect as $acc){
				$temp1=(float)$acc['temp1'];
				$temp2=(float)$acc['temp2'];
				$temp3=(float)$acc['env_temp'];
				//dump($temp1);
				//var_dump($temp2);
				//var_dump($temp3);
				if($temp1 ==0||$temp2==0||$temp3==0||$temp1< 10||$temp2< 10||$temp3< 10||$temp1> 40||$temp2> 40||$temp3> 40)
				//if($temp1 > 0)
				{
					$acclist[]=$acc;
				}
			}
			//dump($devtempeor);
			//if($devtempeor){
			//	$wheretempeor['devid']=array('in',$devtempeor);
			//}
			//$devSelect=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where($wheretempeor)->select();
			//exit;
			//var_dump($devSelect);
			$this->assign('acclist',$acclist);
			$this->display();
		}
	
		public function devselect(){
			$psnid=$_GET['psnid'];
			$productno=$_GET['productno'];
			if($productno==NULL){
				echo 'PRODUCTNO ERR.';
				exit;
			}
			$devSelect=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('devid asc')->select();
			
			$this->assign('devSelect',$devSelect);
			$this->display();
		}
		
 		public function devtempnow(){
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
			
			$delay = substr($delay_str,0, 2);
			$delay = (int)$delay;
			$delay = 3600*$delay;
			$delay_sub = $delay/$count;

			if($productno==NULL){
				echo 'PRODUCTNO ERR.';
				exit;
		
			}
    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	$yes_time = $start_time;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;
    	//dump($cur_time);
    	//dump($start_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time;
			//dump(date('Y-m-d H:s:i',$first_time));
			//dump($psn);
    	$devlist=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
    	//dump(count($devlist));
    	$mydb='access_'.$psn;
			$accSelect2=M($mydb)->where(array('psn'=>$psn,'time'=>$first_time))->order('devid asc')->select();
			//dump(count($accSelect2));
			//dump(count($acclist));
    	for($i=30;$i<40;$i++){
    		$mydb='access1301_'.$i;
    		$acc1301list[$i]=M($mydb)->where(array('psn'=>$psn,'time'=>$first_time))->order('devid asc')->select();
    	}
    	
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
					for($i=30;$i<40;$i++){
						if(count($acc1301list[$i])>0){
							foreach($acc1301list[$i] as $acc){
								if($devid==$acc['devid']){
									$dev_find=true;
									$al_find=false;
									for($j=0;$j<count($acclist2);$j++){
										$al=$acclist2[$j];
										if($devid==$al['devid']){
											$acclist2[$j]['psnid']=$acclist2[$j]['psnid'].','.$acc['psnid'];
											$al_find=true;
											break;
										}
									}
									if($al_find==false){
										$acc['psnid']=$acc['psnid'];
										$acclist2[]=$acc;
										break;
									}
								}
							}
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
			$this->display();
		}

 		public function devtempnow1301(){
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
			
			$delay = substr($delay_str,0, 2);
			$delay = (int)$delay;
			$delay = 3600*$delay;
			$delay_sub = $delay/$count;

			if($productno==NULL){
				echo 'PRODUCTNO ERR.';
				exit;
		
			}
    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	$yes_time = $start_time;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;
    	//dump($cur_time);
    	//dump($start_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time;
			//dump(date('Y-m-d H:s:i',$first_time));
			//dump($psn);
    	$devlist=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
    	//dump(count($devlist));

    	for($i=30;$i<40;$i++){
    		$mydb='access1301_'.$i;
    		$accSelect[$i]=M($mydb)->where(array('psn'=>$psn,'time'=>$first_time))->order('devid asc')->select();
    	}

			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$dev_find=false;

				for($i=30;$i<40;$i++){
					if(count($accSelect[$i])>0){
						foreach($accSelect[$i] as $acc){
							if($devid==$acc['devid']){
								$dev_find=true;
								$al_find=false;
								for($j=0;$j<count($acclist2);$j++){
									$al=$acclist2[$j];
									if($devid==$al['devid']){
										$acclist2[$j]['psnid']=$acclist2[$j]['psnid'].','.$acc['psnid'];
										$al_find=true;
										break;
									}
								}
								if($al_find==false){
									$acc['psnid']=$acc['psnid'];
									$acclist2[]=$acc;
									break;
								}
							}
						}
					}
					if($dev_find==true){
						//break;
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
			//dump($acc_lost);
			//dump($acclist2);
			//exit;
			$this->assign('acclist',$acclist2);
			$this->assign('devlost',$acc_lost);
			$this->display();
		}
		
		public function devtempnext(){
			$psnid=$_GET['psnid'];
			$productno=$_GET['productno'];
			$bdevinfo = M('bdevice')->where(array('psnid'=>$psnid))->find();
			$delay_str= $bdevinfo['uptime'];
			$count= $bdevinfo['count'];
			$psn = $bdevinfo['psn'];
			
			$delay = substr($delay_str,0, 2);
			$delay = (int)$delay;

			$delay = 3600*$delay;
			$delay_sub = $delay/$count;

			if($productno==NULL){
				echo 'PRODUCTNO ERR.';
				exit;
			}
    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	//var_dump($start_time);
    	$yes_time = $start_time;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;
    	//dump($cur_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time;
    	$pre_time = $cur_time-$delay+$start_time-$delay;
			//dump($first_time);
			//dump($pre_time);
    	$devlist=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
    	//dump(count($devlist));
    	$mydb='access_'.$psn;
			$accSelect=M($mydb)->where(array('psn'=>$psn,'time'=>$pre_time))->order('devid asc')->select();
			//dump(count($accSelect));
			$accSelect2=M($mydb)->where(array('psn'=>$psn,'time'=>$first_time))->order('devid asc')->select();
			
			for($i=30;$i<40;$i++){
    		$mydb='access1301_'.$i;
    		$acc1301list1[$i]=M($mydb)->where(array('psn'=>$psn,'time'=>$pre_time))->order('devid asc')->select();
    	}
			for($i=30;$i<40;$i++){
    		$mydb='access1301_'.$i;
    		$acc1301list2[$i]=M($mydb)->where(array('psn'=>$psn,'time'=>$first_time))->order('devid asc')->select();
    	}
    	
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$psnid = $dev['psnid'];
				$dev_find=false;
				foreach($accSelect as $acc){
					if($devid==$acc['devid']){
						$dev_find=true;
						$al_find=false;
						foreach($acclist as $al){
							if($devid==$al['devid']){
								$al_find=true;
								break;
							}
						}
						if($al_find==false){
							$acclist[]=$acc;
						}
					}
				}
				if($dev_find==false){
					for($i=30;$i<40;$i++){
						if(count($acc1301list1[$i])>0){
							foreach($acc1301list1[$i] as $acc){
								if($devid==$acc['devid']){
									$dev_find=true;
									$al_find=false;
									for($j=0;$j<count($acclist);$j++){
										$al=$acclist[$j];
										if($devid==$al['devid']){
											$acclist[$j]['psnid']=$acclist[$j]['psnid'].','.$acc['psnid'];
											$al_find=true;
											break;
										}
									}
									if($al_find==false){
										$acc['psnid']=$acc['psnid'];
										$acclist[]=$acc;
										break;
									}
								}
							}
						}
					}		
				}
			}
			$precount=count($acclist);
			//dump(count($acclist));
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$psnid = $dev['psnid'];
				$dev_find=false;
				foreach($accSelect2 as $acc){
					if($devid==$acc['devid']){
						$al_find=false;
						$dev_find=true;
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
					for($i=30;$i<40;$i++){
						if(count($acc1301list2[$i])>0){
							foreach($acc1301list2[$i] as $acc){
								if($devid==$acc['devid']){
									$dev_find=true;
									$al_find=false;
									for($j=0;$j<count($acclist2);$j++){
										$al=$acclist2[$j];
										if($devid==$al['devid']){
											$acclist2[$j]['psnid']=$acclist2[$j]['psnid'].','.$acc['psnid'];
											$al_find=true;
											break;
										}
									}
									if($al_find==false){
										$acc['psnid']=$acc['psnid'];
										$acclist2[]=$acc;
										break;
									}
								}
							}
						}
					}		
				}
			}
			$nowcount=count($acclist2);

			foreach($devlist as $dev){
				$devid=$dev['devid'];
				$dev_find=false;
				foreach($acclist as $acc){
					if($devid==$acc['devid']){
						$dev_find=true;
						break;
					}
				}
				foreach($acclist2 as $acc){
					if($devid==$acc['devid']){
						$dev_find=true;
						break;
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

			$this->assign('precount',$precount);
			$this->assign('nowcount',$nowcount);
			$this->assign('acclist',$acclist);
			$this->assign('devlost',$acc_lost);
			$this->assign('time1',date('Y-m-d H:i:s',$first_time));
			$this->assign('time2',date('Y-m-d H:i:s',$pre_time));
			$this->display();
		}
		
		public function scanfactoryall(){
			$psnid=$_GET['psnid'];

			$bdevinfo = M('bdevice')->where(array('psnid'=>$psnid))->find();
			$delay_str= $bdevinfo['uptime'];
			$count= $bdevinfo['count'];
			$psn= $bdevinfop['psn'];
			$delay = substr($delay_str,0, 2);
			$delay = (int)$delay;

			$delay = 3600*$delay;
			$delay_sub = $delay/$count;
			$scan_count=4;
			
    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	//dump($start_time);
    	$yes_time = $start_time;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;
    	//dump($cur_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	//dump($cur_time);
    	$first_time = $cur_time-$delay+$start_time;
    	$last_time = $first_time-($delay/$delay_sub)*($scan_count-1)*$delay_sub;
    	//dump(date('Y-m-d H:s:i',$last_time));
    	//dump(date('Y-m-d  H:s:i',$first_time));
    	
    	$devlist=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
    	}
    	
    	$wheredev['devid']=array('in',$devidlist);
    	
    	
    	dump(count($devlist));
    	exit;
    	$mydb='access_'.$psn;
			$accSelect=M($mydb)->where(array('psnid'=>$psnid))->where($wheredev)->where('time >='.$last_time.' and time <='.$first_time)->order('time desc')->select();
			//dump(count($accSelect));
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$acc_size=0;
				unset($acc_list);
				$acc_list = array();
				foreach($accSelect as $acc){
					if($acc['devid']==$devid){
						$acc_list[]=$acc;
					}
				}
				$acc_size=count($acc_list);
				//dump('devid:'.$devid.' count:'.$acc_size);
				if($acc_size< $scan_count){
						if($acc_size==0){
      		  		$dev_none[]=$devid;
      			}else{
      				$dev_lost[]=$devid;
      			}
      			continue;
				}
				$dev_pass[]=$devid;
			}

			if($dev_lost){
				$wherelost['devid']=array('in',$dev_lost);
				$ret=M('factory')->where(array('psnid'=>$psnid))->where($wherelost)->save(array('state'=>3));
			}
			if($dev_none){
				$wherenone['devid']=array('in',$dev_none);
				$ret=M('factory')->where(array('psnid'=>$psnid))->where($wherenone)->save(array('state'=>4));
			}
			if($dev_pass){
				$wherepass['devid']=array('in',$dev_pass);
				$ret=M('factory')->where(array('psnid'=>$psnid))->where($wherepass)->save(array('state'=>2));
			}

			$devSelect=M('factory')->where('state > 1')->where(array('psnid'=>$psnid))->order('devid asc')->select();
			//dump($devSelect);
			//dump($psnid);
			$this->assign('devSelect',$devSelect);
			$this->display();
		}
		
		public function scandevlow(){
			$psnid=$_GET['psnid'];
			$delay = 1*3600;
			$delay_sub = 1*3600;

    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	//var_dump($start_time);
    	$yes_time = $start_time;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;
    	//var_dump($cur_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time;
    	$last_time = $first_time-$delay-$delay_sub;
    	//dump(date('Y-m-d H:s:i',$last_time));
    	//dump(date('Y-m-d  H:s:i',$first_time));
    	
    	$devlist=M('device')->where(array('psn'=>$psnid,'dev_type'=>0))->order('id asc')->select();
    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
    	}
    	
    	$wheredev['devid']=array('in',$devidlist);
    	//dump(count($devlist));
    	//exit;
			$accSelect=M('access')->where($wheredev)->where('time >='.$last_time.' and time <='.$first_time)->where(array('psn'=>$psnid))->order('time desc')->select();
			//dump(count($accSelect));
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$acc_size=0;
				unset($acc_list);
				$acc_list = array();
				foreach($accSelect as $acc){
					if($acc['devid']==$devid){
						$acc_list[]=$acc;
					}
				}
				$acc_size=count($acc_list);
				//dump('devid:'.$devid.' count:'.$acc_size);
				$lcount=0;
				foreach($acc_list as $acc){
					if($acc['temp1']<32||$acc['temp2']<32){
						$lcount++;
						if($lcount > 1){
							$dev['temp1']=$acc['temp1'];
							$dev['temp2']=$acc['temp2'];
							$dev['env_temp']=$acc['env_temp'];
							$devSelect[]=$dev;
							break;
						}
					}else{
						$lcount=0;
					}
				}
			}

			$this->assign('devSelect',$devSelect);
			$this->display();
		}
		
		public function addfactoryfornew(){
			//dump('add close');
			//exit;
			$psnid = $_GET['psnid'];
			$productno=$_GET['productno'];
			$count=$_GET['count'];
			$now=date('Y-m-d H:i:s',time());
			
			if(empty($psnid)||empty($productno)||empty($count)){
				dump('add null');
				exit;
			}
			dump($psnid);
			dump($productno);
			dump((231+$count));
			exit;
			for($i=231;$i<(231+$count);$i++){
				$cur_dev=array( 
  											'psnid'=>$psnid,
						      			'devid'=>$i,
						      			'productno'=>$productno,
												'state'=>1,
						      			'fsn'=>"ABC",
						      			'time'=>$now);
				$dev_add[]=	$cur_dev;	      			
			}
  				
			dump($dev_add);
			$ret=M('factory')->addAll($dev_add); 
    	dump('finish!');
			exit;
		}
	
	public function querytemp(){
		if(empty($_POST['time'])||empty($_POST['time2'])){
			  $now = time();
			  $v = strtotime(date('Y-m-d',$now))-86400;
			  $time =date('Y-m-d',$v);
  			$time2 =date('Y-m-d',$now);
		}else{
		  	$time =  $_POST['time'];
		  	$time2 =  $_POST['time2'];
		}
    {
	  	$psn = $_GET['psn'];
	  	$id=$_GET['devid'];
	  	
    	$start_time = strtotime($time);
    	$end_time = strtotime($time2)+86400;

        $devSelect=M('device')->field('devid')->where(array('flag'=>1,'dev_type'=>1,'psn'=>$psn))->find();
        if($devSelect!=NULL){
            $devid=$devSelect['devid'];

        }
        
        $devSelect2=M('device')->field('devid')->where(array('flag'=>1,'dev_type'=>2,'psn'=>$psn))->find();
        if($devSelect2!=NULL){
            $devid2=$devSelect2['devid'];

        }

        $devSelect3=M('device')->field('devid')->where(array('flag'=>1,'dev_type'=>3,'psn'=>$psn))->find();
        if($devSelect3!=NULL){
            $devid3=$devSelect3['devid'];

        }

        $mydb='access_'.$psn;
        if($devid==NULL){
            if($selectSql=M($mydb)->field('temp1,temp2,env_temp,delay,sign,cindex,lcount,time,devid,sid,state,psnid,psn')->where('devid ='.$id.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('time desc')->select()){
								for($i=30;$i<40;$i++){
					    		$mydb='access1301_'.$i;
					    		$acc1301list[$i]=M($mydb)->where('devid ='.$id.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('time desc')->select();
					    	}
					    	for($i=30;$i<40;$i++){
					    		//dump($acc1301list[$i]);
									if(count($acc1301list[$i])>0){
		            		foreach($acc1301list[$i] as $acc){
		            			$selectSql[]=$acc;
		            		}
		            	}
		            }
                $this->assign('devid',$id);
                $this->assign('date',$time);
                $this->assign('date2',$time2);
                $this->assign('id',$id);
                $this->assign('selectSql',$selectSql);
            }else{
								for($i=30;$i<40;$i++){
					    		$mydb='access1301_'.$i;
					    		$acc1301list[$i]=M($mydb)->where('devid ='.$id.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('time desc')->select();
					    	}
					    	for($i=30;$i<40;$i++){
					    		//dump($acc1301list[$i]);
									if(count($acc1301list[$i])>0){
		            		foreach($acc1301list[$i] as $acc){
		            			$selectSql[]=$acc;
		            		}
		            	}
		            }
                $this->assign('devid',$id);
                $this->assign('id',$id);
                $this->assign('selectSql',$selectSql);
                //$date = date("Y-m-d");
                $this->assign('date',$time);
                $this->assign('date2',$time2);
                //echo "<script type='text/javascript'>alert('NO DATA.');distory.back();</script>"; 
            }

            $this->display();
            exit;
        }

        $tmpSql=M('taccess')->where('devid ='.$devid.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id asc')->select();

				if($devid2!=NULL){
        	$tmpSql2=M('taccess')->where('devid ='.$devid2.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id asc')->select();
					//var_dump($tmpSql2);
				}
				
				if($devid3!=NULL){
        	$tmpSql3=M('taccess')->where('devid ='.$devid3.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id asc')->select();
					//var_dump($tmpSql3);
				}

        if($selectSql=M($mydb)->group('time')->where('devid ='.$id.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id desc')->select()){
            $this->assign('devid',$id);
            $this->assign('date',$time);
            $this->assign('date2',$time2);
            $this->assign('id',$id);

            for($i=0;$i<count($selectSql);$i++){
                if($tmpSql!=NULL){
                		$max=count($tmpSql)-1;
                    for($j=0;$j<count($tmpSql);$j++){
                        if($selectSql[$i]['time']==$tmpSql[$j]['time']){
                            $selectSql[$i]['env_temp1']=number_format($tmpSql[$j]['temp1'],2);
                            $selectSql[$i]['env_temp2']=number_format($tmpSql[$j]['temp2'],2);
                            break;
                        }
                        else if($selectSql[$i]['time'] > $tmpSql[$j]['time']){
                            $selectSql[$i]['env_temp1']=number_format($tmpSql[$max]['temp1'],2);
                            $selectSql[$i]['env_temp2']=number_format($tmpSql[$max]['temp2'],2);
                        }
                        else{
                        	  $selectSql[$i]['env_temp1']=255; 
                    				$selectSql[$i]['env_temp2']=255;
                        }                          
                    }
                }else{
                    $selectSql[$i]['env_temp1']=255; 
                    $selectSql[$i]['env_temp2']=255;
                }
                if($tmpSql2!=NULL){
                		$max=count($tmpSql2)-1;
                    for($j=0;$j<count($tmpSql2);$j++){
                        if($selectSql[$i]['time']==$tmpSql2[$j]['time']){
                            $selectSql[$i]['env_temp3']=number_format($tmpSql2[$j]['temp1'],2);
                            $selectSql[$i]['env_temp4']=number_format($tmpSql2[$j]['temp2'],2);
                            break;
                        }
                        else if($selectSql[$i]['time'] > $tmpSql2[$j]['time']){
                            $selectSql[$i]['env_temp3']=number_format($tmpSql2[$max]['temp1'],2);
                            $selectSql[$i]['env_temp4']=number_format($tmpSql2[$max]['temp2'],2);
                        }
                        else{
                        	  $selectSql[$i]['env_temp3']=255; 
                    				$selectSql[$i]['env_temp4']=255;
                        }   
                    }
                }else{
                    $selectSql[$i]['env_temp3']=255; 
                    $selectSql[$i]['env_temp4']=255;
                } 
                if($tmpSql3!=NULL){
                		$max=count($tmpSql3)-1;
                    for($j=0;$j<count($tmpSql3);$j++){
                        if($selectSql[$i]['time']==$tmpSql3[$j]['time']){
                            $selectSql[$i]['env_temp5']=number_format($tmpSql3[$j]['temp1'],2);
                            $selectSql[$i]['env_temp6']=number_format($tmpSql3[$j]['temp2'],2);
                            break;
                        }
                        else if($selectSql[$i]['time'] > $tmpSql3[$j]['time']){
                            $selectSql[$i]['env_temp5']=number_format($tmpSql3[$max]['temp1'],2);
                            $selectSql[$i]['env_temp6']=number_format($tmpSql3[$max]['temp2'],2);
                        }
                        else{
                        	  $selectSql[$i]['env_temp5']=255; 
                    				$selectSql[$i]['env_temp6']=255;
                        }   
                    }
                }else{
                    $selectSql[$i]['env_temp5']=255; 
                    $selectSql[$i]['env_temp6']=255;
                }                        
                                   
            }

            $this->assign('selectSql',$selectSql);
            //var_dump($selectSql);

        }else{
        		$date = date("Y-m-d");
	 	 				$this->assign('date',$date);
	 	 				$this->assign('date2',$date);
            echo "<script type='text/javascript'>alert('没有查询到结果.');distory.back();</script>";
        }
    }
		$this->display();
	}
	
	public function devnotinsall(){
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

		$delay = substr($delay_str,0, 2);
		$delay = (int)$delay;
		$delay = 3600*$delay;
		$delay_sub = $delay/$count;

		if($productno==NULL){
		echo 'PRODUCTNO ERR.';
		exit;

		}
		$now = time();
		$start_time = strtotime(date('Y-m-d',$now));
		$yes_time = $start_time;
		$end_time = $start_time+86400;
		$cur_time = $now - $start_time;
    	//dump($cur_time);
    	//dump($start_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time;
			//dump(date('Y-m-d H:s:i',$first_time));
			//dump($psn);
    	$devlist=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('devid asc')->select();
    	//dump(count($devlist));
    	$dev_max=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('devid desc')->find();

    	$devid_max=(int)$dev_max['devid'];
    	//dump($devid_max);

    	$cows=M('cows')->order('sn_code asc')->select();

    	//dump($cows);
		//echo 'dev not install:';
    	foreach($devlist as $dev){
    		$psn=$dev['psnid'];
    		$devid=$dev['devid'];
    		$dev_find=false;
			//dump($psn);
			//dump($devid);
    		foreach($cows as $cow){
    			$sn=$cow['sn_code'];
    			$cow_psn=(int)substr($sn,0,5);
      			$cow_devid=(int)substr($sn,5,4);
    			//dump($sn);
				//dump($cow_psn);
				//dump($cow_devid);
    			//exit;
      			if($psn==$cow_psn){
      				if($devid==$cow_devid){
      					$dev_find=true;
      					break;
      				}
      			}
    		}
    		if($dev_find==false){
    			$psn_not=str_pad($psn,5,'0',STR_PAD_LEFT);
    			$devid_not=str_pad($devid,4,'0',STR_PAD_LEFT);
    			$dev_not_install['sn']=$psn_not.$devid_not;
    			$dev_not_install['psn']=$psn;
    			$dev_not_install['devid']=$devid;
    			$devlist_not_install[]=$dev_not_install;
    			//dump($dev_not_install);
    		}

    	}
		//echo 'dev not install:';
    	//dump($dev_not_install);
    	//echo 'dev error:';
		foreach($cows as $cow){
			$sn=$cow['sn_code'];
			$cow_psn=(int)substr($sn,0,5);
  		$cow_devid=(int)substr($sn,5,4);
  			if($psn==$cow_psn){
  				if($cow_devid>$devid_max){
	    			$psn_err=str_pad($cow_psn,5,'0',STR_PAD_LEFT);
	    			$devid_err=str_pad($cow_devid,4,'0',STR_PAD_LEFT);
	    			$dev_err['sn']=$psn_err.$devid_err;
	    			$dev_err['psn']=$cow_psn;
	    			$dev_err['devid']=$cow_devid;
	    			$devlist_err[]=$dev_err;
  					//dump($dev_err);
  				}
  			}else if($cow_psn<30||$cow_psn>39){
  					$psn_err=str_pad($cow_psn,5,'0',STR_PAD_LEFT);
	    			$devid_err=str_pad($cow_devid,4,'0',STR_PAD_LEFT);
	    			$dev_err['sn']=$psn_err.$devid_err;
	    			$dev_err['psn']=$cow_psn;
	    			$dev_err['devid']=$cow_devid;
	    			$devlist_err[]=$dev_err;
	    			//dump($dev_err);
  			}
		}

		//echo 'dev error:';
		//dump($dev_err);
    	//exit;
    	$this->assign('dev_not_install',$devlist_not_install);
    	$this->assign('dev_err',$devlist_err);
		$this->display();
	}

 	public function get1301dev(){
			$psnid=$_GET['psnid'];
			$sid=$_GET['sid'];
			
			
			$bdevinfo = M('bdevice')->where(array('psnid'=>$psnid))->find();

			$delay_str= $bdevinfo['uptime'];
			$count= $bdevinfo['count'];
			
			$delay = substr($delay_str,0, 2);
			$delay = (int)$delay;
			$delay = 3600*$delay;
			$delay_sub = $delay/$count;

    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	$yes_time = $start_time-86400;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;

    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time;
    	
    	$mydb='access_'.$psnid;
    	$lasttimeSel=M($mydb)->where(array('psnid'=>$psnid,'sid'=>$sid))->order('time desc')->find();
    	$first_time=$lasttimeSel['time'];
    	$pretime=$lasttimeSel['time']-$delay;
    	$lasttime=$lasttimeSel['time']-$delay*2;
    	
			//dump(date('Y-m-d H:s:i',$first_time));
    	$accSelect1=M($mydb)->where(array('psnid'=>$psnid,'sid'=>$sid))->where('time <='.$end_time.' and time>='.$yes_time)->select();
			//$accSelect1=M($mydb)->where(array('psnid'=>$psnid,'time'=>$first_time,'sid'=>$sid))->order('devid asc')->select();
			//$accSelect2=M($mydb)->where(array('psnid'=>$psnid,'time'=>$pretime,'sid'=>$sid))->order('devid asc')->select();
			//$accSelect3=M($mydb)->where(array('psnid'=>$psnid,'time'=>$lasttime,'sid'=>$sid))->order('devid asc')->select();
			//dump(count($accSelect1));
			foreach($accSelect1 as $acc1){
				$acc1_devid=$acc1['devid'];
				$acc1_psn=$acc1['psn'];
				$acc_find=false;
				foreach($acclist as $v){
					if($v['psn']==$acc1_psn&&$acc1_devid==$v['devid']){
						$acc_find=true;
						break;
					}
				}
				if($acc_find==false){
					$acclist[]=$acc1;
				}
			}
			
			foreach($accSelect2 as $acc1){
				$acc1_devid=$acc1['devid'];
				$acc1_psn=$acc1['psn'];
				$acc_find=false;
				foreach($acclist as $v){
					if($v['psn']==$acc1_psn&&$acc1_devid==$v['devid']){
						$acc_find=true;
						break;
					}
				}
				if($acc_find==false){
					$acclist[]=$acc1;
				}
			}
			
			foreach($accSelect3 as $acc1){
				$acc1_devid=$acc1['devid'];
				$acc1_psn=$acc1['psn'];
				$acc_find=false;
				foreach($acclist as $v){
					if($v['psn']==$acc1_psn&&$acc1_devid==$v['devid']){
						$acc_find=true;
						break;
					}
				}
				if($acc_find==false){
					$acclist[]=$acc1;
				}
			}
			$this->assign('acclist',$acclist);
			$this->display();
	}
	
	public function test(){
		send163msg('13801394601',NULL);
	}
	
	public function querysntemp(){
		$sn=$_POST['sn'];
		if($sn){
			$sn=str_pad($sn,9,'0',STR_PAD_LEFT);
      $psn=(int)substr($sn,0,5);
      $devid=(int)substr($sn,5,4);
      $this ->redirect('/product/querytemp',array('psn'=>$psn,'devid'=>$devid),0,'');
			exit;
		}
		$this->display();
	}
	
	public function synccows(){
		//$vid=$_GET['village_id'];
		//dump($cows);
		//flag 1:active, 2:change sn,3:die or lose or sale.
		$cows=M('cows')->order('sn_code asc')->select();
		foreach($cows as $key=>$cow){
			$sn=$cow['sn_code'];
			//$sn=str_pad($sn,9,'0',STR_PAD_LEFT);
      //$psn=(int)substr($sn,0,5);
      //$devid=(int)substr($sn,5,4);
      //$cows[$key]['psn']=$psn;
      //$cows[$key]['devid']=$devid;
      $survival_state=$cow['survival_state'];
      if($survival_state==2||$survival_state==4){
      	$devnone[]=(int)$sn;
      }else{
      	$devlist[]=(int)$sn;
      }
		}
		//dump($devlist);
		$whereall['rid']=array('in',$devlist);
		$wherenone['rid']=array('in',$devnone);
		/*
		for($i=30;$i<40;$i++){
			dump($i);
			dump($devlist[$i]);
			$wherenone['rid']=array('in',$devlist[$i]);
			//$ret=M('device')->where(array('psnid'=>$i))->where($wherenone)->save(array('flag'=>1));
			//dump($ret);
		}
		*/
		//dump($devlist);
		$ret=M('device')->where($whereall)->save(array('flag'=>1));
		echo 'add all';
		dump($ret);
		$ret=M('device')->where($wherenone)->save(array('flag'=>3));
		echo 'add sale';
		dump($ret);
		$changeid=M('changeidlog')->where(array('flag'=>3))->where('old_psn >=30 and old_psn <=39')->select();
		
		foreach($changeid as $dev){
			//$ridlist[]=$dev['rfid'];
			$psn=$dev['old_psn'];
			$devid=$dev['old_devid'];
			$rid=$dev['rfid'];
			$ret=M('device')->where(array('rid'=>$rid,'psn'=>$psn,'devid'=>$devid))->save(array('flag'=>2));
			echo 'add change sn';
			dump($ret);
		}
		exit;
		//$this->assign('clist',$cows);
		//$this->display();
		
	}

	public function villagelist(){
		$villagelist=M('subareas')->where(array('type'=>'village_id'))->select();
		//dump($villagelist);
		//exit;
		foreach($villagelist as $key=>$v){
			$nc=M('cows')->where(array('village_id'=>$v['id']))->count();
			$hc=M('cows')->where(array('village_id'=>$v['id'],'health_state'=>3))->count();
			$lc=M('cows')->where(array('village_id'=>$v['id'],'survival_state'=>3))->count();
			$sc=M('cows')->where(array('village_id'=>$v['id'],'survival_state'=>4))->count();
			$villagelist[$key]['count']=$nc;
			$villagelist[$key]['hcount']=$hc;
			$villagelist[$key]['lcount']=$lc;
			$villagelist[$key]['scount']=$sc;
		}
		$this->assign('vlist',$villagelist);
		$this->display();
		
	}
	
	public function cowlist(){
		$vid=$_GET['village_id'];
		//dump($cows);
		$cows=M('cows')->where(array('village_id'=>$vid))->select();
		//dump($cows);
		//exit;
		$farmers=M('farmers')->where(array('village_id'=>$vid))->select();
		foreach($cows as $key=>$cow){
			$sn=$cow['sn_code'];
			$sn=str_pad($sn,9,'0',STR_PAD_LEFT);
      $psn=(int)substr($sn,0,5);
      $devid=(int)substr($sn,5,4);
      $cows[$key]['psn']=$psn;
      $cows[$key]['devid']=$devid;
      
			foreach($farmers as $farmer){
				if($farmer['id']==$cow['farmer_id']){
					$cows[$key]['farmer']=$farmer['name'];
					//dump($key);
					//dump($farmer['name']);
					break;
				}	
			}	
		}
		
		
		$this->assign('clist',$cows);
		$this->display();
		
	}
	
	public function scancows(){
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
					if($acc['temp1']<$low_temp&&$acc['temp2']<$low_temp){
						$low_count++;
					}else{
						break;
					}
				}
				if($low_count>0){
					dump($acc_list);
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
			
			$ret=$mode->where(array('psn'=>$psn))->save(array('cow_state'=>0));
			if($dev_pass){
				$wherenpass['devid']=array('in',$dev_pass);
				$ret=$mode->where(array('psn'=>$psn))->where($wherenpass)->save(array('cow_state'=>2));
			}
			if($dev_none){
				$wherenone['devid']=array('in',$dev_none);
				$ret=$mode->where(array('psn'=>$psn))->where($wherenone)->save(array('cow_state'=>4));
			}
			if($dev_low){
				$wherenlow['devid']=array('in',$dev_low);
				$ret=$mode->where(array('psn'=>$psn))->where($wherenlow)->save(array('cow_state'=>5));
			}
			//if($dev_pass){
			//	$wherepass['devid']=array('in',$dev_pass);
			//	$ret=M('device')->where(array('psn'=>$psn))->where($wherepass)->save(array('state'=>2));
			//}
			//echo 'pass:';
			//dump($dev_pass);
			//echo 'dev_lost:';
			//dump($dev_lost);
			echo 'none:';
			dump($dev_none);
			echo 'low:';
			dump($dev_low);
		}
		//dump(count($cows));
		exit;
	}

	public function scansync(){
		//$cows=M('cows')->order('sn_code asc')->select();
		$devnone=M('device')->where('psnid>=30 and psnid<=39')->where(array('cow_state'=>4))->select();
		$devnlow=M('device')->where('psnid>=30 and psnid<=39')->where(array('cow_state'=>5))->select();
		//$cowslist=M('cows')->select();
		$ret=M('cows')->where(array('survival_state'=>3))->save(array('survival_state'=>1));
		$ret=M('cows')->where('health_state>1')->save(array('health_state'=>1));
		dump($ret);
		
		foreach($devnone as $dev){
			$cow['sn']=str_pad($dev['rid'],9,'0',STR_PAD_LEFT);
			$cow['survival']=3;
			$cow['health']=1;
			$data[]=$cow;
			$sn_none[]=$cow['sn'];
		}
		foreach($devnlow as $dev){
			$cow['sn']=str_pad($dev['rid'],9,'0',STR_PAD_LEFT);
			$cow['survival']=1;
			$cow['health']=3;
			$data[]=$cow;
			$sn_low[]=$cow['sn'];
		}
		
		if($sn_none){
			$wherenone['sn_code']=array('in',$sn_none);
			$cowslist=M('cows')->where($wherenone)->select();
			foreach($cowslist as $cow){
				if($cow['survival_state']==1){
					$cow_none[]=$cow['sn_code'];
				}
			}
			echo 'cow none:';
			dump($cow_none);
			if($cow_none){
				$wherenonecow['sn_code']=array('in',$cow_none);
				$ret=M('cows')->where($wherenonecow)->save(array('survival_state'=>3));
				dump($ret);
			}
		}
		if($sn_low){
			$wherelow['sn_code']=array('in',$sn_low);
			$cowslist=M('cows')->where($wherelow)->select();
			//echo 'cow low:';
			//dump($cowslist);
			foreach($cowslist as $cow){
				if($cow['health_state']==1){
					$cow_low[]=$cow['sn_code'];
				}
			}
			echo 'cow low:';
			dump($cow_low);
			if($cow_low){
				$wherelowcow['sn_code']=array('in',$cow_low);
				$ret=M('cows')->where($wherelowcow)->save(array('health_state'=>3));
				dump($ret);
			}
		}
		//dump($data);
	}
	
}