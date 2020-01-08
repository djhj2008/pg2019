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
    	$week_time = $start_time-86400*6;
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
    	
    	$wheredev['devid']=array('in',$devidlist);

    	$mydb='access_'.$psn;
    	$accSelect1=M($mydb)->where(array('psn'=>$psn,'time'=>$first_time))->where($wheredev)->order('devid asc')->select();
			$accSelect2=M($mydb)->where(array('psn'=>$psn,'time'=>$pre_time))->where($wheredev)->order('devid asc')->select();
			$accSelect3=M($mydb)->where(array('psn'=>$psn,'time'=>$pre2_time))->where($wheredev)->order('devid asc')->select();
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$psnid = $dev['psnid'];
				$acc_size=0;
				unset($acc_list);
				$acc_list = array();
				foreach($accSelect1 as $acc){
					if($acc['devid']==$devid){
						$acc_list[]=$acc;
					}
				}
				foreach($accSelect2 as $acc){
					if($acc['devid']==$devid){
						$acc_list[]=$acc;
					}
				}
				foreach($accSelect3 as $acc){
					if($acc['devid']==$devid){
						$acc_list[]=$acc;
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
				/*
				if(!empty($dev_none)){
					$accweekSelect=M('access')->where($wherenone)->where('time >='.$week_time.' and time <='.$end_time)->where(array('psnid'=>$psnid))->order('time desc')->select();
					foreach($dev_none as $dev_week_id){
						$acc_week_size=0;
						unset($acc_week_list);
						$acc_week_list = array();
						foreach($accweekSelect as $acc){
							if($acc['devid']==$dev_week_id){
								$acc_week_list[]=$acc;
							}
						}
						$acc_week_size=count($acc_week_list);
						if($acc_week_size==0){
							$dev_week_none[]=$dev_week_id;
						}
					}
				}

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

			$devSelect=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('devid asc')->select();
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
			//dump(count($accSelect2));
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
    	
    	$devlist=M('device')->where(array('psn'=>$psnid,'flag'=>1,'dev_type'=>0))->order('id asc')->select();
    	foreach($devlist as $dev){
    		$devidlist[]=$dev['devid'];
    	}
    	
    	$wheredev['devid']=array('in',$devidlist);
    	//dump(count($devlist));
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
		
		public function test(){
			
			send163msg('13801394601',NULL);
		}
}