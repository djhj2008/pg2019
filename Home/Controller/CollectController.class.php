<?php
namespace Home\Controller;
use Think\Controller;
class CollectController extends Controller {
    public function monthValue(){
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

        $ios_order='time asc';
        
        $dateArr = array();
        $temp1Arr = array();
        $temp2Arr = array();
        
        $mydb='access_'.$psnid;
        
        $selectSql=M($mydb)->where('devid ='.$devid.' and psn= '.$psnid.' and time >= '.$start_time.' and time <= '.$end_time)
													        ->group('time')
													        ->order($ios_order)
													        ->select();
        if($selectSql)
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
    
    public function downValue(){
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
		    $down_value=32;
		    
				$psnid = $_GET['psnid'];
	    	
	    	$start_time = strtotime($time);
	    	$end_time = strtotime($time2)+86400;

        $ios_order='time asc';
        
        $dateArr = array();
        $temp1Arr = array();
        $temp2Arr = array();
        
        $mydb='access_'.$psnid;
        
				$psn=$psnid;
				$devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psn,'flag'=>1))->order('devid desc')->select();
		
				for($i=0;$i<count($devSelect);$i++)
				{
					$devSelect[$i]['down_count']=0;
				}
		
        $selectSql=M($mydb)->where('temp1 <'.$down_value.' and time >= '.$start_time.' and time <= '.$end_time)
													        ->order($ios_order)
													        ->select();
        if($selectSql)
        {
        		foreach($selectSql as $acc){
        			$devid=$acc['devid'];
        			$cur_time=$acc['time'];
        			for($i=0;$i<count($devSelect);$i++)
        			{
        				if($devid==$devSelect[$i]['devid']){
        					$devSelect[$i]['down_count']=$devSelect[$i]['down_count']+1;
        					$devSelect[$i]['down_time'][]=$cur_time;
        					break;
        				}
        			}
						}
        }
				/*		        
        foreach($devSelect as $dev){
        	 $devid=$dev['devid'];
        	 $start_time=$end_time-86400;
        	 $today_avg=0;
        	 $today_avg2=0;
        	 if($dev['down_count']>0){
	  	        $todayvalue=M($mydb)->where('devid ='.$devid.' and psn= '.$psnid.' and time >= '.$start_time.' and time <= '.$end_time)
													        ->group('time')
													        ->order($ios_order)
													        ->select();

        			$today_sum=0;
        			$today_sum2=0;
        			$today_count=0;
        			$today_time=date('Y-m-d',$today_start);
	        		foreach($todayvalue as $acc){
									$temp1=$acc['temp1'];
									$temp2=$acc['temp2'];
									$temp3=$acc['env_temp'];
									$a=array($temp1,$temp2);
									$t=max($a);
									$vt=(float)$t;
									$today_sum+=$vt;
									$today_sum2+=$temp3;
									$today_count++;
							}
							$today_avg=$today_sum/$today_count;
							$today_avg2=$today_sum2/$today_count;
        		}
        		if($today_avg>32){
	        		for($i=0;$i<count($devSelect);$i++)
	      			{
	      				if($devid==$devSelect[$i]['devid']){
	      					$devSelect[$i]['down_count']=0;
	      					break;
	      				}
	      			}
        		}
        }
        */
        $this->assign('devSelect',$devSelect);
	      //dump($devSelect);
	      $this->display();
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
	    	
	    	$start_time = strtotime($time);
	    	$end_time = strtotime($time2)+86400;

        $ios_order='time asc';
        
        $dateArr = array();
        $temp1Arr = array();
        $temp2Arr = array();
        
        $mydb='access_'.$psnid;
        
				$psn=$psnid;
				//$devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psn,'flag'=>1))->order('devid desc')->select();
				$devSelect=M('factory')->where(array('psnid'=>$psnid,'productno'=>$productno))->order('id asc')->select();
				
				for($i=0;$i<count($devSelect);$i++)
				{
					$devSelect[$i]['down_count']=0;
				}
		
        $selectSql=M($mydb)->where('sign <'.$down_value.' and time >= '.$start_time.' and time <= '.$end_time)
													        ->order($ios_order)
													        ->select();
        if($selectSql)
        {
        		foreach($selectSql as $acc){
        			$devid=$acc['devid'];
        			$cur_time=$acc['time'];
        			for($i=0;$i<count($devSelect);$i++)
        			{
        				if($devid==$devSelect[$i]['devid']){
        					$devSelect[$i]['down_count']=$devSelect[$i]['down_count']+1;
        					$devSelect[$i]['down_time'][]=$cur_time;
        					break;
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
        
        $mydb='access_'.$psnid;
        
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