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
       
        //dump($start_time);
        //dump($end_time);
        //dump($devid);
				//dump($psnid);
        $ios_order='time asc';
        
        $dateArr = array();
        $temp1Arr = array();
        $temp2Arr = array();
        
        if($selectSql=M('access')->where('devid ='.$devid.' and psn= '.$psnid.' and time >= '.$start_time.' and time <= '.$end_time)
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
        
        if($selectSql=M('access')->where('devid ='.$devid.' and psn= '.$psnid.' and time >= '.$start_time.' and time <= '.$end_time)
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
}