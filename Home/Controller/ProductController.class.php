<?php
namespace Home\Controller;
use Tools\HomeController; 
use Think\Controller;
class ProductController extends HomeController {
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
																      			'devid'=>$sn))->find();
				$mytime=strtotime($v['time']);
				//dump($mytime);						      			
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
  			}	    		
    	}
    	dump('finish!');
			exit;
		}

		public function scanfactory(){
			$psnid=$_GET['psnid'];
			$productno=$_GET['productno'];
			$delay = 1*3600;
			$delay_sub = 1*3600;

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
    	
    	dump(date('Y-m-d H:s:i',$first_time));
    	dump(date('Y-m-d H:s:i',$last_time));
			//var_dump($first_time);
			
    	$devlist=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
    	//dump(count($devlist));
			$accSelect=M('access')->where('time >='.$last_time.' and time <='.$first_time)->where(array('psn'=>$psnid))->order('time desc')->select();
			//dump(count($accSelect));
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$psnid = $dev['psnid'];
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
				if($acc_size<2){
						if($acc_size==0){
      		  		$dev_none[]=$devid;
      			}else{
      				$dev_lost[]=$devid;
      			}
      			continue;
				}
				$dev_pass[]=$devid;
			}
			//dump($dev_lost);
			//dump($dev_none);
			//dump($dev_pass);
			//$wherelost['devid']=array('in',$dev_lost);
			//$wherenone['devid']=array('in',$dev_none);
			//$wherepass['devid']=array('in',$dev_pass);
			
			if($dev_lost){
				$wherelost['devid']=array('in',$dev_lost);
				$ret=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where($wherelost)->save(array('state'=>3));
			}
			if($dev_none){
				$wherenone['devid']=array('in',$dev_none);
				$ret=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where($wherenone)->save(array('state'=>4));
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
			
			$accSelect=M('access')->where('time >='.$last_time.' and time <='.$first_time)->where(array('psn'=>$psnid))->where($wheredev)->group('devid')->select();

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
			$delay = 3600;
			$delay_sub = 3600;

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
			//dump($first_time);
			
    	$devlist=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
    	//dump(count($devlist));
			$accSelect2=M('access')->where(array('psn'=>$psnid,'time'=>$first_time))->order('devid asc')->select();
			//dump(count($accSelect2));
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$psnid = $dev['psnid'];
				foreach($accSelect as $acc){
					if($devid==$acc['devid']){
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
			//dump(count($acclist));
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$psnid = $dev['psnid'];
				foreach($accSelect2 as $acc){
					if($devid==$acc['devid']){
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
			}

			$this->assign('acclist',$acclist2);
			$this->display();
		}
		
		public function devtempnext(){
			$psnid=$_GET['psnid'];
			$productno=$_GET['productno'];
			$delay = 1*3600;
			$delay_sub = 1*3600;

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
			$accSelect=M('access')->where(array('psn'=>$psnid,'time'=>$pre_time))->order('devid asc')->select();
			//dump(count($accSelect));
			$accSelect2=M('access')->where(array('psn'=>$psnid,'time'=>$first_time))->order('devid asc')->select();
			//dump(count($accSelect2));
			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$psnid = $dev['psnid'];
				foreach($accSelect as $acc){
					if($devid==$acc['devid']){
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
				foreach($accSelect2 as $acc){
					if($devid==$acc['devid']){
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
			}
			$nowcount=count($acclist2);
			//dump(count($acclist2));
			if($precount>$nowcount){
				foreach($acclist as $dev){
					$devid = $dev['devid'];
					$psnid = $dev['psnid'];
					$find=false;
					foreach($accSelect2 as $acc){
						if($devid==$acc['devid']){
							$find=true;
							break;
						}
					}
					if($find==false){
						$nolist[]=$dev;
					}
				}	
			}
			else if($precount< $nowcount){
				foreach($acclist2 as $dev){
					$devid = $dev['devid'];
					$psnid = $dev['psnid'];
					$find=false;
					foreach($accSelect as $acc){
						if($devid==$acc['devid']){
							$find=true;
							break;
						}
					}
					if($find==false){
						$nolist[]=$dev;
					}
				}	
			}
			//dump($nolist);
			//dump($acclist);
			//exit;
			$this->assign('precount',$precount);
			$this->assign('nowcount',$nowcount);
			$this->assign('acclist',$nolist);
			$this->display();
		}
		
		public function scanfactoryall(){
			$psnid=$_GET['psnid'];
			$delay = 1*3600;
			$delay_sub = 1*3600;
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
		
		public function factorytempeorall(){
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
    	$last_time = $cur_time-$delay+$start_time-$delay-$delay_sub;	
				
			$devSelect=M('factory')->where(array('psnid'=>$psnid))->order('devid asc')->select();

			foreach($devSelect as $dev){
				$devlist[]=$dev['devid'];
			}
			
			$wheredev['devid']=array('in',$devlist);
			
			$accSelect=M('access')->where('time >='.$last_time.' and time <='.$first_time)->where(array('psn'=>$psnid))->where($wheredev)->group('devid')->select();

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
		
		public function deveorall(){
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
    	$last_time = $cur_time-$delay+$start_time-$delay-$delay_sub;	
				
			$devSelect=M('factory')->where(array('psnid'=>$psnid,'flag'=>1))->order('devid asc')->select();

			foreach($devSelect as $dev){
				$devlist[]=$dev['devid'];
			}
			dump($devlist);
			$wheredev['devid']=array('in',$devlist);
			
			$accSelect=M('access')->where('time >='.$last_time.' and time <='.$first_time)->where(array('psn'=>$psnid))->where($wheredev)->limit(0,8)->select();

			foreach($accSelect as $acc){
				$temp1=(float)$acc['temp1'];
				$temp2=(float)$acc['temp2'];
				$temp3=(float)$acc['env_temp'];
				//dump($temp1);
				//var_dump($temp2);
				//var_dump($temp3);
				if($temp1 < 30||$temp2< 30||$temp3< 10||$temp3>36)
				//if($temp1 > 0)
				{
					$acclist[]=$acc;
				}
			}
			dump($acclist);
			exit;
			//if($devtempeor){
			//	$wheretempeor['devid']=array('in',$devtempeor);
			//}
			//$devSelect=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->where($wheretempeor)->select();
			//exit;
			//var_dump($devSelect);
			//$this->assign('acclist',$acclist);
			//$this->display();
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