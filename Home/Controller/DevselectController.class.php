<?php
namespace Home\Controller;
use Tools\HomeController; 
use Think\Controller;
class DevselectController extends HomeController {
	
	public function select(){
		$tab = $_GET['tab'];
  	$uid= $_SESSION['userid'];
  	//var_dump($uid);
  	$psnSelect=M('psn')->where(array('userid'=>$uid))->select();
  	//var_dump($psnSelect);
		//dump($dev);
		$this->assign('psnSelect',$psnSelect);
		$this->display();
	}

	public function sickness(){
		$tab = $_GET['tab'];
  	$uid= $_SESSION['userid'];
  	//dump($uid);
  	$name=$_SESSION['name'];
  	$this->assign('name',$name);
  	
  	
  	$psnSelect=M('psn')->where(array('userid'=>$uid))->select();
  	$psnsize=count($psnSelect);
  	if(empty($psnSelect)){
  		dump($psnSelect);
  		exit;
  	}else{
  		for($i=0;$i<$psnsize;$i++){
  				if($i==0){
  					$where1='psn='.$psnSelect[$i]['sn'];
  					$where2='psnid='.$psnSelect[$i]['sn'];
  				}else{
	  				$sql1=' or psn='.$psnSelect[$i]['sn'];
	  				$sql2=' or psnid='.$psnSelect[$i]['sn'];
	  				$where1=$where1.$sql1;
	  				$where2=$where2.$sql2;
  				}
  		}
  	}
  	//dump($where);
  	//exit;
  	$devdount=M('device')->where($where1.' and flag > 0')->count();
		$devSelect1=M('sickness')->where($where2)->where(array('state'=>1))->order('devid asc')->select();
		$devSelect2=M('sickness')->where($where2)->where(array('flag'=>1))->order('devid asc')->select();
		$devSelect3=M('sickness')->where($where2)->where(array('state'=>2))->order('devid asc')->select();
		$this->assign('devcount',$devdount);
		$this->assign('devSelect1',$devSelect1);
		$this->assign('devSelect2',$devSelect2);
		$this->assign('devSelect3',$devSelect3);
      
		$this->display();
	}
	
	public function devlist(){
		$psnid = $_GET['psnid'];
		$devSelect=M('device')->where(array('flag'=>1,'dev_type'=>0,'psn'=>$psnid))->order('devid asc')->select();
		//dump($dev);
		$this->assign('devSelect',$devSelect);
		$this->display();
	}
	
	public function station(){
		$psnid = $_GET['psnid'];
		$devSelect=M('bdevice')->where(array('psnid'=>$psnid))->order('id asc')->select();
		//dump($dev);
		$this->assign('devSelect',$devSelect);
		$this->display();
	}
	
	public function querytoday(){
				$psnid=$_GET['psnid'];
				$now = time();
			  $time =date('Y-m-d ',$now).'00:00:00';
  			$time2 =date('Y-m-d ',$now).'24:00:00';
				$start_time = strtotime($time);
				$end_time = strtotime($time2)+86400;
				
				$accSelect=M('access')->group('devid')->where('time >='.$start_time.' and time <'.$end_time)->where(array('psn'=>$psnid))->order('devid asc')->limit(0,4)->select();
				
				foreach($accSelect as $acc){
					var_dump($acc['devid']);
				}
				exit;
				
	}
		
	public function querytemp(){
		if(empty($_POST['time'])||empty($_POST['time2'])){
			  $now = time();
			  $time =date('Y-m-d');
  			$time2 =$time;
		}else{
		  	$time =  $_POST['time'];
		  	$time2 =  $_POST['time2'];
		}
		
  	$start_time = strtotime($time);
  	$end_time = strtotime($time2)+86400;
  	$psn = $_GET['psnid'];
  	$id=$_GET['devid'];
		$psnid = $_GET['psnid'];
		$sql = 'devid ='.$id.' and psn= '.$psnid.' and time >= '.$start_time.' and time < '.$end_time;
		//var_dump($sql);
		$selectSql=M('access')->where($sql)->order('id desc')->select();
		if(empty($selectSql)){
				$date = date("Y-m-d");
        $this->assign('date',$date);
        $this->assign('date2',$date);
		}else{
			  $this->assign('date',$time);
        $this->assign('date2',$time2);
		}
		//var_dump($selectSql);
		$this->assign('selectSql',$selectSql);
		$this->display();
	}
	
	public function addfactory(){
		    $psnid= $_POST['psnid'];
      	
      	$product=M('product')->where('state=1')->select();
      	foreach($product as $v){
      		$snstr = $v['sn'];
      		if(strlen($snstr)!=5){
      			continue;
      		}else{
      			$psn = substr($snstr,0,1);
      			$sn = substr($snstr,1,4);
      			$pid=(int)$pid;
      			$sn=(int)$sn;
      			echo "psn:";
      			var_dump($psn);
      			//var_dump($sn);
      			$psnfind=M('psn')->where(array('sn'=>$psn,'tsn'=>1086756300))->find();
      			if(empty($psnfind)){
      				//var_dump($psnfind);
      				$psnfind=M('psn')->where(array('sn'=>$psn,'tsn'=>2086756300))->find();
      			}
      			$psnid=$psnfind['id'];
      			echo "psnid:";
      			var_dump($psnid);
      			var_dump($sn);
      			$devfind=M('factory')->where(array( 'psnid'=>$psnid,
																		      			'devid'=>$sn)
																		    )->find();
						var_dump($devfind);												    
      			if(empty($psnfind)){
      				$dev = array( 'psnid'=>$psnid,
								      			'devid'=>$sn,
														'state'=>$v['state'],
								      			'fsn'=>"ABC",
								      			'time'=>$v['time']);
							$ret=M('factory')->add($dev);      			
      			}
      		}		
      	}
      	
    $devSelect=M('device')->where(array('flag'=>1,'dev_type'=>0,'psn'=>$psnid))->order('devid asc')->select();
		//dump($dev);
		$this->assign('devSelect',$devSelect);
		$this->display();
      	
      	exit;
	}
	
	public function checkfactory(){
				$psnid=$_GET['psnid'];
				$delay = 4*3600;
				$delay_sub = 2*3600;
 
      	$now = time();
  			$start_time = strtotime(date('Y-m-d',$now).' 00:00:00');
      	//var_dump($start_time);
      	$yes_time = $start_time-86400;
      	$end_time = $start_time+86400;
      	$cur_time = $now - $start_time;
      	//var_dump($cur_time);
      	$cur_time = (int)($cur_time/$delay)*$delay;
      	$first_time = $cur_time-$delay+$start_time;
  			//var_dump($first_time);
				
      	$devlist=M('factory')->where(array('psnid'=>$psnid))->order('id asc')->select();

				foreach($devlist as $dev){
					$devid = $dev['devid'];
					$psnid = $dev['psnid'];
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
				}
				$devSelect=M('factory')->where(array('state'=>4))->where(array('psnid'=>$psnid))->order('devid asc')->select();
				$this->assign('devSelect',$devSelect);
				//var_dump($devSelect);
				//exit;
				$this->display();
	}

	public function factorypass(){
				$psnid=$_GET['psnid'];

				$devSelect=M('factory')->where(array('state'=>2))->where(array('psnid'=>$psnid))->order('devid asc')->select();
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
						if($temp1==-20||$temp2==0||$temp3==0)
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
				$devSelect=M('factory')->where(array('state'=>3))->where(array('psnid'=>$psnid))->order('devid asc')->select();
				
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
						if($temp1==-20||$temp2==0||$temp3==0)
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
	
	public function autoadd(){

			//$i=2760;
		  for($i=2;$i<=50;$i++){
					$dev=array(
						'psn'=>3,
						'shed'=>1,
						'fold'=>1,
						'flag'=>1,
						'state'=>1,
						's_count'=>0,
						'rid'=>$i,
						'age'=>1,
						'devid'=>$i,
					);
					//$saveSql=M('device')->add($dev);
  		}
  		echo "ok";
  		exit;
	}
	
	public function start(){
        	$psn= $_GET['psnid'];
        	$now = time();
    			$start_time = strtotime(date('Y-m-d',$now).'00:00:00');
        	dump($start_time);
        	$end_time = $start_time+86400;
        	$hlevl1=40;
        	$hlevl2=45;
        	$llevl1=10;
        	$llevl2=0;

        	$acss=M('access')->group('devid')->order('temp1 desc')->where('temp1 >= '.$hlevl1.' and time >'.$start_time.' and time <'.$end_time)->where(array('psn'=>$psn))->select();
        	
        	$sick=M('sickness')->where(array('psnid'=>$psn,'state'=>1))->select();
        	echo "sick:";
        	dump($sick);
					{
        		foreach($sick as $s){
        			$ret=M('access')->where('time >'.$start_time.' and time <'.$end_time)->where(array('psn'=>$psn,'devid'=>$s['devid']))->order('temp1 desc')->limit(0,1)->find();
        			if(!empty($ret)){        					
      					$date = date('Y-m-d H:i:s',$ret['time']);
      					$day1 = strtotime((date('Y-m-d',$ret['time'])));
      					$day2 = strtotime((date('Y-m-d',$s['time'])));
      					if($day1-$day2>=86400){
      						$days=$s['days']+1;
      					}else{
      						$days=$s['days'];
      					}
      					$temp = $ret['temp1'];
      					if($temp>=$hlevl2){
      						$level=2;
      					}else if($temp>=$hlevl1){
      						$level=1;
      					}else{
      						$level=0;
      					}

      					$sk=array(
							  		'temp1'=>$ret['temp1'],
										'time'=>$ret['time'],
										'date'=>$date,
										'level'=>$level,
										'state'=>1,
										'days'=>$days,
							  		);
							  $saveSql=D('sickness')->where(array('devid'=>$s['devid'],'psn'=>$s['psn']))->save($sk);
      					dump($sk);
        					
        			}
        		}
 
      			foreach($acss as $acs){
      				$find = false;
      				foreach($sick as $s){
      					if($acs['devid']==$s['devid']){
      						$find = true;
      						break;
      					}
      				}
      				if($find==false){
		        		$date = date('Y-m-d H:i:s',$acs['time']);
		        		$dev = M('device')->where(array('psn'=>$acs['psn'],'devid'=>$acs['devid']))->find();
		        		if(!empty($dev)){
	      					$temp = $acs['temp1'];
	      					if($temp>=$hlevl2){
	      						$level=2;
	      					}else if($temp>=$hlevl1){
	      						$level=1;
	      					}else{
	      						$level=0;
	      					}
				        	$sk=array(
							   	  'psnid'=>$acs['psn'],
							  		'devid'=>$acs['devid'],
							  		'shed'=>$dev['shed'],
							  		'fold'=>$dev['fold'],
							  		'temp1'=>$acs['temp1'],
										'time'=>$acs['time'],
										'level'=>$level,
										'date'=>$date,
										'state'=>1,
										'days'=>1,
							  		);
							  	echo "add:";
							  	dump($sk);
		  			  	 	$saveSql=D('sickness')->add($sk);
	  			  		}
	      			}
      			}	
        	}

        	$acss3=M('access')->group('devid')->order('temp1 desc')->where('temp1 < '.$llevl1.' and time >'.$start_time.' and time <'.$end_time)->where(array('psn'=>$psn))->select();
        	
        	$sick3=M('sickness')->where(array('psnid'=>$psn,'state'=>2))->select();
					echo "sick3:";
					dump($sick3);
					{
        		foreach($sick3 as $s){
        			$ret=M('access')->where('time >'.$start_time.' and time <'.$end_time)->where(array('psn'=>$psn,'devid'=>$s['devid']))->order('temp1 asc')->limit(0,1)->find();
        			if(!empty($ret)){
        					$date = date('Y-m-d H:i:s',$ret['time']);
        					$day1 = strtotime((date('Y-m-d',$ret['time'])));
        					$day2 = strtotime((date('Y-m-d',$s['time'])));
        					if($day1-$day2>=86400){
        						$days=$s['days']+1;
        					}else{
        						$days=$s['days'];
        					}
	      					$temp = $ret['temp1'];
	      					if($temp<=$llevl2){
	      						$level=2;
	      					}else if($temp<=$llevl1){
	      						$level=1;
	      					}else{
	      						$level=0;
	      					}
        					$sk=array(
								  		'temp1'=>$temp,
											'time'=>$ret['time'],
											'date'=>$date,
											'level'=>$level,
											'state'=>2,
											'days'=>$days,
								  		);
								  $saveSql=D('sickness')->where(array('devid'=>$s['devid'],'psn'=>$s['psn']))->save($sk);
        					dump($sk);
        				
        			}
        		}
 
      			foreach($acss3 as $acs){
      				$find = false;
      				foreach($sick3 as $s){
      					if($acs['devid']==$s['devid']){
      						$find = true;
      						break;
      					}
      				}
      				if($find==false){
		        		$date = date('Y-m-d H:i:s',$acs['time']);
		        		$dev = M('device')->where(array('psn'=>$acs['psn'],'devid'=>$acs['devid']))->find();
		        		if(!empty($dev)){
		        			$temp = $acs['temp1'];
	      					if($temp<=$llevl2){
	      						$level=2;
	      					}else if($temp<=$llevl1){
	      						$level=1;
	      					}else{
	      						$level=0;
	      					}
				        	$sk=array(
							   	  'psnid'=>$acs['psn'],
							  		'devid'=>$acs['devid'],
										'shed'=>$dev['shed'],
										'fold'=>$dev['fold'],
							  		'temp1'=>$temp,
										'time'=>$acs['time'],
										'date'=>$date,
										'level'=>$level,
										'state'=>2,
										'days'=>1,
							  		);
							  	echo "add:";
							  	dump($sk);
		  			  	 	$saveSql=D('sickness')->add($sk);
		  			  	}
	      			}
      			}	
        	}
					exit;
	}
	
	public function setting(){
		$devid = $_GET['devid'];
  	$psn= $_GET['psnid'];
		$temp = $_GET['temp1'];
  	$state = $_POST['radio1'];
    $msg = $_POST['msg'];
  	$name=$_SESSION['name'];
  	$this->assign('name',$name);
  	
    if(empty($state)||empty($msg)){
    		$this->display();
    		exit;
    }
    
    if($state==1){
    	/*
       $sk=array(
					'state'=>0,
		  		);
		   $saveSql=D('sickness')->where(array('devid'=>$devid,'psn'=>$psn))->save($sk);
		   */
    }else{
       $sk=array(
					'flag'=>1,
		  		);
		   $saveSql=D('sickness')->where(array('devid'=>$devid,'psn'=>$psn))->save($sk);
		   if(!empty($msg)){
		   	 $rec = array(
		   	 					'devid'=>$devid,
		   	 					'psnid'=>$psn,
		   	 					'temp1'=>$temp,
		   	 					'msg'=>$msg,
		   	 				);
		     $rd = D('sickrecord')->add($rec);	
		   }
    }
    
		$this ->redirect('Devselect/sickness',array(),0,'');
	}
	
	public function setting2(){
		$devid = $_GET['devid'];
  	$psn= $_GET['psnid'];
		$msg= $_POST['msg'];
		$temp = $_GET['temp1'];
  	$name=$_SESSION['name'];
  	$this->assign('name',$name);
  	
		if(!empty($msg)){
			$rec = array(
						'devid'=>$devid,
						'psnid'=>$psn,
						'temp1'=>$temp,
						'msg'=>$msg,
					);
			$rd = D('sickrecord')->add($rec);	
			$this ->redirect('Devselect/sickness',array('tab'=>2),0,'');
			exit;
		}
		
		$record=M('sickrecord')->where(array('devid'=>$devid ,'psn'=>$psn))->order('time desc')->select();
    $this->assign('sickrecord',$record);
    $this->display();
	}
	
	public function setting3(){
		$devid = $_GET['devid'];
  	$psn= $_GET['psnid'];
		$msg= $_POST['msg'];
		$temp = $_GET['temp1'];
  	$name=$_SESSION['name'];
  	$this->assign('name',$name);
  	
		if(!empty($msg)){
			$rec = array(
						'devid'=>$devid,
						'psnid'=>$psn,
						'temp1'=>$temp,
						'msg'=>$msg,
					);
			$rd = D('recovery')->add($rec);	
			$this ->redirect('Devselect/select',array(),0,'');
			exit;
		}
		
		$record=M('recovery')->where(array('devid'=>$devid ,'psn'=>$psn))->order('time desc')->select();
    $this->assign('recovery',$record);
    $this->display();
	}
}