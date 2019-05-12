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
			
			if(!$productno){
				echo 'PRODUCTNO ERR.';
				exit;
			}
			dump('productno:'.$productno);		
			$product=M($mytab)->select();
			//dump($product);
			//$devs = M('factory')->where(array('psnid'=>$psnid))->select();
    	foreach($product as $v){
    		$snstr = $v['sn'];
				$sn_start=strlen($snstr)-4;
  			$psn = substr($snstr,0,$sn_start);
  			$sn = substr($snstr,$sn_start,4);
  			$psn=(int)$psn;
  			$sn=(int)$sn;
  			//var_dump($sn);
  			$psnfind=M('psn')->where(array('sn'=>$psn))->find();
  			if(!$psnfind){
  				echo 'PSN ERROR :'.$psn;
  				exit;
  			}
  			$psnid=$psnfind['id'];

  			$devfind=M('factory')->where(array( 'psnid'=>$psnid,
																      			'devid'=>$sn)
																    )->find();
				//var_dump($devfind);
  			if(empty($psnfind)){
  				$devadd = array( 
  											'psnid'=>$psnid,
						      			'devid'=>$sn,
						      			'productno'=>$productno,
												'state'=>1,
						      			'fsn'=>"ABC",
						      			'time'=>$v['time']);
					dump('add dev:'.$devadd);		
					$ret=M('factory')->add($devadd);      			
  			}	    		
    	}
			exit;
		}

		public function scanfactory(){
			$psnid=$_GET['psnid'];
			$productno=$_GET['productno'];
			$delay = 4*3600;
			$delay_sub = 2*3600;

			if(!$productno){
				echo 'PRODUCTNO ERR.';
				exit;
			}
    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	//var_dump($start_time);
    	$yes_time = $start_time-86400;
    	$end_time = $start_time+86400;
    	$cur_time = $now - $start_time;
    	//var_dump($cur_time);
    	$cur_time = (int)($cur_time/$delay)*$delay;
    	$first_time = $cur_time-$delay+$start_time;
			//var_dump($first_time);
			
    	$devlist=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
    	dump(count($devlist));

			foreach($devlist as $dev){
				$devid = $dev['devid'];
				$psnid = $dev['psnid'];
				$dev=M('device')->where(array('devid'=>$devid,'psn'=>$psnid))->order('id asc')->find();
				//var_dump($dev);
				if(!empty($dev)){
					$accSelect=M('access')->group('time')->where('time >='.$yes_time.' and time <'.$end_time)->where(array('devid'=>$devid,'psn'=>$psnid))->order('time desc')->limit(0,4)->select();
					$accsize=count($accSelect);
					if($accsize<4){
						$stateSave=array('state'=>3);
						if($accsize==0){
      		  		$stateSave=array('state'=>4);
      			}
      			$ret=M('factory')->where(array('devid'=>$devid,'psnid'=>$psnid))->save($stateSave);
      		  //var_dump($devid);
      		  //var_dump($psnid);
      			continue;
      		}
	      	for($i=0;$i < 4;$i++){
	      		$time=(int)$accSelect[$i]['time'];
	      		//$time = date('Y-m-d H:s:i',$time);
	      		$right_time=$first_time-$i*$delay_sub;
	      		//echo "devid:";
	      		//var_dump($devid);
	      		//var_dump($time);
	      		//var_dump($right_time);
	      		if($time!=$right_time){
	      			$ret=M('factory')->where(array('devid'=>$devid,'psnid'=>$psnid))->save(array('state'=>3));
	      			break;
	      		}
	      		$ret=M('factory')->where(array('devid'=>$devid,'psnid'=>$psnid))->save(array('state'=>2));
	      	}
	      }else{
	      	//echo "devid:";
	      	//var_dump($devid);
	      	$ret=M('factory')->where(array('devid'=>$devid,'psnid'=>$psnid))->save(array('state'=>0));
	      }
			}
			$devSelect=M('factory')->where(array('state'=>2))->where(array('psnid'=>$psnid,'flag'=>1))->order('devid asc')->select();
			$this->assign('devSelect',$devSelect);
			$this->display();
		}

		public function factorypass(){
			$psnid=$_GET['psnid'];
			$productno=$_GET['productno'];
				
			if(!$productno){
				echo 'PRODUCTNO ERR.';
				exit;
			}				
				
			$devSelect=M('factory')->where(array('state'=>2))->where(array('psnid'=>$psnid,'productno'=>$productno))->order('devid asc')->select();
			$devcount=count($devSelect);
			for($i=0; $i< $devcount; $i++){
				$devid = $devSelect[$i]['devid'];
				$accSelect=M('access')->where(array('devid'=>$devid,'psn'=>$psnid))->order('time desc')->limit(0,1)->select();
				//echo "devid:";
				//var_dump($devid);
				foreach($accSelect as $acc){
					$temp1=(float)$acc['temp1'];
					$temp2=(float)$acc['temp2'];
					$temp3=(float)$acc['env_temp'];
					//var_dump($temp1);
					//var_dump($temp2);
					//var_dump($temp3);
					if($temp1 ==0||$temp2==0||$temp3==0||$temp1< 10||$temp2< 10||$temp3< 10||$temp1> 40||$temp2> 40||$temp3> 40)
					{
						$devSelect[$i]['err']=1;
					}else{
						$devSelect[$i]['err']=0;
					}
				}
			}
			//exit;
			//var_dump($devSelect);
			$this->assign('devSelect',$devSelect);
			$this->display();
		}
	
		public function factoryfail(){
			$psnid=$_GET['psnid'];
			$productno=$_GET['productno'];
			if(!$productno){
				echo 'PRODUCTNO ERR.';
				exit;
			}		
			$devSelect=M('factory')->where(array('state'=>3))->where(array('psnid'=>$psnid,'productno'=>$productno))->order('devid asc')->select();

			$devcount=count($devSelect);
			for($i=0; $i< $devcount; $i++){
				$devid = $devSelect[$i]['devid'];
				$accSelect=M('access')->where(array('devid'=>$devid,'psn'=>$psnid))->order('time desc')->limit(0,1)->select();
				//echo "devid:";
				//var_dump($devid);
				foreach($accSelect as $acc){
					$temp1=(float)$acc['temp1'];
					$temp2=(float)$acc['temp2'];
					$temp3=(float)$acc['env_temp'];
					//var_dump($temp1);
					//var_dump($temp2);
					//var_dump($temp3);
					if($temp1 ==0||$temp2==0||$temp3==0||$temp1< 10||$temp2< 10||$temp3< 10||$temp1> 40||$temp2> 40||$temp3> 40)
					{
						$devSelect[$i]['err']=1;
					}else{
						$devSelect[$i]['err']=0;
					}
				}
			}
			//exit;
			//var_dump($devSelect);
			$this->assign('devSelect',$devSelect);
			$this->display();
		}
		
		public function factorynone(){
			$psnid=$_GET['psnid'];
			$productno=$_GET['productno'];
			if(!$productno){
				echo 'PRODUCTNO ERR.';
				exit;
			}
			$devSelect=M('factory')->where(array('state'=>4))->where(array('psnid'=>$psnid,'productno'=>$productno))->order('devid asc')->select();
			
			$devcount=count($devSelect);
			for($i=0; $i< $devcount; $i++){
				$devid = $devSelect[$i]['devid'];
				$accSelect=M('access')->where(array('devid'=>$devid,'psn'=>$psnid))->order('time desc')->limit(0,1)->select();
				//echo "devid:";
				//var_dump($devid);
				foreach($accSelect as $acc){
					$temp1=(float)$acc['temp1'];
					$temp2=(float)$acc['temp2'];
					$temp3=(float)$acc['env_temp'];
					//var_dump($temp1);
					//var_dump($temp2);
					//var_dump($temp3);
					if($temp1< 20||$temp2==0||$temp3==0)
					{
						$devSelect[$i]['err']=1;
					}else{
						$devSelect[$i]['err']=0;
					}
				}
			}
			//exit;
			//var_dump($devSelect);
			$this->assign('devSelect',$devSelect);
			$this->display();
		}
}