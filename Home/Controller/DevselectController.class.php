<?php
namespace Home\Controller;
use Tools\HomeController; 
use Think\Controller;
class DevselectController extends HomeController {
	
	public function select(){
		$tab = $_GET['tab'];
  	$uid= $_SESSION['userid'];
  	//var_dump($uid);
  	//$psnSelect=M('psn')->where(array('userid'=>$uid))->select();
  	if($uid==18){
  		$psnSelect=M('psn')->select();
 		}else{
 			$psnSelect=M('psn')->where(array('userid'=>$uid))->select();
 		}
  	//var_dump($psnSelect);
		//dump($dev);
		$this->assign('psnSelect',$psnSelect);
		$this->display();
	}

	public function sickness(){
		$tab = $_GET['tab'];
  	$uid = $_SESSION['userid'];
  	$user_autoid = $_SESSION['user_autoid'];
  	$aip = $_POST['aip'];
  	//dump($uid);
  	$name=$_SESSION['name'];
  	$tab= $_GET['tab'];
  	$this->assign('name',$name);
  	//dump($aip);
  	if(!empty($aip)){
  		$uid = $_POST['userid'];
  	}
  	
  	if(empty($tab)==false){
  		$this->assign('tab',$tab);
  	}

		if($uid==5){
			$wx_count=500;
		}else if($uid==2){
			$wx_count=50;
		}else{
			$wx_count=$devdount;
		}
			
		//dump($uid);
		if($uid==2){
			$uid=5;
			$rel_uid=2;
		}
		
  	$psnSelect=M('psn')->where(array('userid'=>$uid))->find();
  	if(empty($psnSelect)){
  		dump($psnSelect);
  		exit;
  	}else{
  		//dump($psnSelect);
  		$psnid=$psnSelect['id'];
  	}
  	//dump($psnSelect);
  	//exit;
  	$dev1=M('device')->where(array('psn'=>$psnid,'dev_type'=>1))->find();
  	
  	$dev2=M('device')->where(array('psn'=>$psnid,'dev_type'=>2))->find();
  	
  	$devid1=$dev1['devid'];
  	$devid2=$dev2['devid'];
  	
  	
  	$temp1 = M('taccess')->where(array('psn'=>$psnid,'devid'=>$devid1))->order('time desc')->find();
  	$temp2 = M('taccess')->where(array('psn'=>$psnid,'devid'=>$devid2))->order('time desc')->find();
  	//dump($temp1);
  	//dump($temp2);
		$this->assign('temp1',$temp1);
		$this->assign('temp2',$temp2);

		if($aip=='ios'){
			if($rel_uid==2){
				$devdount=M('device')->where('flag > 0  and dev_type=0')->where(array('psn'=>$psnid))->count();
				$devSelect1=M('sickness')->where(array('psnid'=>$psnid,'state'=>1,'flag'=>0))->limit(2,2)->order('devid asc')->select();
				$devSelect2=M('sickness')->where(array('psnid'=>$psnid,'flag'=>1))->limit(2,1)->order('devid asc')->select();
				$devSelect3=M('sickness')->where(array('psnid'=>$psnid,'state'=>2,'flag'=>0))->limit(2,1)->order('devid asc')->select();
			}else{
				$devdount=M('device')->where('flag > 0 and dev_type=0')->where(array('psn'=>$psnid))->count();
				$devSelect1=M('sickness')->where(array('psnid'=>$psnid,'state'=>1,'flag'=>0))->order('devid asc')->select();
				$devSelect2=M('sickness')->where(array('psnid'=>$psnid,'flag'=>1))->order('devid asc')->select();
				$devSelect3=M('sickness')->where(array('psnid'=>$psnid,'state'=>2,'flag'=>0))->limit(0,1)->order('devid asc')->select();
			}
			$temparr=array('temp1'=>$temp1,'temp2'=>$temp2);
			$devarr=array('dev1'=>$devSelect1,'dev2'=>$devSelect2,'dev3'=>$devSelect3);
			$wx_count=200;
			if($rel_uid==5){
				$wx_count=500;
			}else if($rel_uid==2){
				$wx_count=50;
			}else{
				$wx_count=500;
			}
			$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000200,'devcount'=>$wx_count,'temp'=>$temparr,'devs'=>$devarr));
			$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
			exit;
		}
		if($rel_uid==2){
			$devdount=M('device')->where('flag > 0 and dev_type=0')->where(array('psn'=>$psnid))->count();
			$devSelect1=M('sickness')->where(array('psnid'=>$psnid,'state'=>1,'flag'=>0))->limit(2,2)->order('devid asc')->select();
			$devSelect2=M('sickness')->where(array('psnid'=>$psnid,'flag'=>1))->limit(2,1)->order('devid asc')->select();
			$devSelect3=M('sickness')->where(array('psnid'=>$psnid,'state'=>2,'flag'=>0))->limit(2,1)->order('devid asc')->select();
		}else{
			$devdount=M('device')->where('flag > 0 and dev_type=0')->where(array('psn'=>$psnid))->count();
			$devSelect1=M('sickness')->where(array('psnid'=>$psnid,'state'=>1,'flag'=>0))->order('devid asc')->select();
			$devSelect2=M('sickness')->where(array('psnid'=>$psnid,'flag'=>1))->order('devid asc')->select();
			$devSelect3=M('sickness')->where(array('psnid'=>$psnid,'state'=>2,'flag'=>0))->limit(0,1)->order('devid asc')->select();
		}
		if($rel_uid==2){
			$this->assign('devcount',50);
		}else{
			$this->assign('devcount',500);
		}
		
		$this->assign('devSelect1',$devSelect1);
		$this->assign('devSelect2',$devSelect2);
		$this->assign('devSelect3',$devSelect3);
		$this->display();
	}
	
	public function checktmp(){
		$psn=$_GET['psnid'];
		$id=$_GET['devid'];
		$now = time();
	  $time =date('Y-m-d ',$now);
		$time2 =date('Y-m-d ',$now).'24:00:00';
		$start_time = strtotime($time)-86400;
		$end_time = strtotime($time2)+86400;

		$psnfind = M('psn')->where(array('id'=>$psn))->find();
		if(empty($psnfind)){
			echo "PSN NULL.";
			exit;
		}
		$btemp=$psnfind['base_temp'];
  	$hlevl1=$psnfind['htemplev1'];
  	$hlevl2=$psnfind['htemplev2'];
  	$llevl1=$psnfind['ltemplev1'];
  	$llevl2=$psnfind['ltemplev2'];
		$temp_value=$psnfind['check_value'];
			
    $dev=M('device')->where(array('devid'=>$id,'psn'=>$psn))->find();
    if($dev==NULL){
        //echo "<script type='text/javascript'>alert('No device.');distory.back();</script>";
        $this->display();
        exit;
    }
    $avg=(float)$dev['avg_temp'];
    //var_dump($avg);
    if($avg==0){
    		//echo "<script type='text/javascript'>alert('Start now.');distory.back();</script>";
    	  $this->display();
        exit;
    }
    //var_dump($dev);

    $devSelect=M('device')->where(array('dev_type'=>1,'psn'=>$psn))->find();
    if($devSelect!=NULL){
        $devid=$devSelect['devid'];
        //var_dump($devid);
    }
    
    $devSelect2=M('device')->where(array('dev_type'=>2,'psn'=>$psn))->find();
    if($devSelect2!=NULL){
        $devid2=$devSelect2['devid'];
        //var_dump($devid2);
    }

    $devSelect3=M('device')->where(array('dev_type'=>3,'psn'=>$psn))->find();
    if($devSelect3!=NULL){
        $devid3=$devSelect3['devid'];
        //var_dump($devid3);
    }

    if($devid!=NULL){
    	$tmpSql=M('taccess')->where('devid ='.$devid.' and psn= '.$psn.' and time <= '.$end_time)->order('id desc')->limit(0,8)->select();
    }
		if($devid2!=NULL){
    	$tmpSql2=M('taccess')->where('devid ='.$devid2.' and psn= '.$psn.' and time <= '.$end_time)->order('id desc')->limit(0,8)->select();
			//var_dump($tmpSql2);
		}

		if($devid3!=NULL){
    	$tmpSql3=M('taccess')->where('devid ='.$devid3.' and psn= '.$psn.' and time <= '.$end_time)->order('id desc')->limit(0,8)->select();
			//var_dump($tmpSql3);
		}
		
		
    if($selectSql=M('access')->where('devid ='.$id.' and psn= '.$psn.' and time <= '.$end_time)->group('time')->order('id desc')->limit(0,8)->select()){
        $this->assign('devid',$id);
        $this->assign('date',$time);
        $this->assign('date2',$time2);
        $this->assign('id',$id);
        for($i=0;$i<count($selectSql);$i++){
        		$temp1=$selectSql[$i]['temp1'];
        		$temp2=$selectSql[$i]['temp2'];
        		$temp3=$selectSql[$i]['env_temp'];
						$a=array($temp1,$temp2);
						$t=max($a);
						$vt=(float)$t;
						if($vt < 20){
							$ntemp=$vt;
						}else{
							$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
						}
        		//$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
        		$selectSql[$i]['ntemp']=$ntemp;
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
	  $this->display();
				
	}
	
	public function devlist(){
		$psnid = $_GET['psnid'];
		$devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psnid,'flag'=>1))->order('devid asc')->select();
		//dump($dev);
		$this->assign('devSelect',$devSelect);
		$this->display();
	}
	
	public function station(){
		$psnid = $_GET['psnid'];
		$devSelect=M('bdevice')->where(array('psnid'=>$psnid))->order('id asc')->select();
		//dump($dev);
		$this->assign('devSelect',$devSelect);
		$this->assign('psnid',$psnid);
		$this->display();
	}
	
	public function stopdevice(){
			$this->display();
			exit;
			/*
			$psn = $_GET['psnid'];
	  	$id=$_GET['devid'];
			$psnid = $_GET['psnid'];
			
			
			$where=array(
										'psn'=>$psn,
										'devid'=>$id,
									);
			$re_flag['re_flag']=1;
			$ret=M('device')->where($where)->save($re_flag);
			
			$this ->redirect('/Devselect/devlist',array('psnid'=>$psn),0,'');
			*/
			
	}
	
	public function querytempnew(){
		if(empty($_POST['time'])||empty($_POST['time2'])){
			  $now = time();
			  $v = strtotime(date('Y-m-d',$now))-86400;
			  $time =date('Y-m-d',$v);
  			$time2 =date('Y-m-d',$now);
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
		$selectSql=M('access')->where($sql)->group('time')->order('time desc')->select();
		if(empty($selectSql)){
				$now = time();
				$v = strtotime(date('Y-m-d',$now))-86400;
				$date = date("Y-m-d",$v);
				$date2 = date("Y-m-d",$now);
        $this->assign('date',$date);
        $this->assign('date2',$date2);
		}else{
			  $this->assign('date',$time);
        $this->assign('date2',$time2);
		}
		//var_dump($selectSql);
		$this->assign('selectSql',$selectSql);
		$this->display();
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
	  	$psn = $_GET['psnid'];
	  	$id=$_GET['devid'];
			$psnid = $_GET['psnid'];
    	
    	$start_time = strtotime($time);
    	$end_time = strtotime($time2)+86400;
        $dev=M('device')->where(array('devid'=>$id,'psn'=>$psn))->find();
        if($dev==NULL){
            $date = date("Y-m-d");
            $this->assign('date',$date);
            $this->assign('date2',$date);
            echo "<script type='text/javascript'>alert('设备不存在.');distory.back();</script>";
            $this->display();
            exit;
        }
        $psn = $dev['psn'];
        $shed = $dev['shed'];
        //var_dump($dev);

        $devSelect=M('device')->where(array('flag'=>1,'dev_type'=>1,'psn'=>$psn))->find();
        if($devSelect!=NULL){
            $devid=$devSelect['devid'];
            //var_dump($devid);
        }
        
        $devSelect2=M('device')->where(array('flag'=>1,'dev_type'=>2,'psn'=>$psn))->find();
        if($devSelect2!=NULL){
            $devid2=$devSelect2['devid'];
            //var_dump($devid2);
        }

        $devSelect3=M('device')->where(array('flag'=>1,'dev_type'=>3,'psn'=>$psn))->find();
        if($devSelect3!=NULL){
            $devid3=$devSelect3['devid'];
            //var_dump($devid3);
        }
        
        if($devid==NULL){
            if($selectSql=M('access')->group('time')->where('devid ='.$id.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id desc')->select()){
                $this->assign('devid',$id);
                $this->assign('date',$time);
                $this->assign('date2',$time2);
                $this->assign('id',$id);
                $this->assign('selectSql',$selectSql);
            }else{
                $date = date("Y-m-d");
                $this->assign('date',$date);
                $this->assign('date2',$date);
                 echo "<script type='text/javascript'>alert('没有查询到结果.');distory.back();</script>"; 
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
				
        if($selectSql=M('access')->group('time')->where('devid ='.$id.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id desc')->select()){
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

	public function addfactory2(){
		    $psnid= 12;

      	$product=M('device')->where(array('psn'=>12,'dev_type'=>0))->select();
      	dump($product);
      	foreach($product as $v){
      		$snstr = $v['devid'];
      		if($snstr<20){
      			continue;
      		}else{
      			$devfind=M('factory')->where(array( 'psnid'=>$psnid,
																		      			'devid'=>$snstr)
																		    )->find();
						var_dump($devfind);				    
      			if(empty($psnfind)){
      				$dev = array( 'psnid'=>$psnid,
								      			'devid'=>$snstr,
														'state'=>1,
								      			'fsn'=>"ABC");
							$ret=M('factory')->add($dev);      			
      			}
      		}		
      	}
      	
    //$devSelect=M('device')->where(array('flag'=>1,'dev_type'=>0,'psn'=>$psnid))->order('devid asc')->select();
		//dump($dev);
		//$this->assign('devSelect',$devSelect);
		//$this->display();
      	
    exit;
	}
	
	public function checkfactory(){
				$psnid=$_GET['psnid'];
				$delay = 4*3600;
				$delay_sub = 2*3600;
 
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
				
      	$devlist=M('factory')->where(array('psnid'=>$psnid))->order('id asc')->select();
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

	public function factoryfailout(){
				$psnid=$_GET['psnid'];
				$delay = 4*3600;
				$delay_sub = 2*3600;
 
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
				
      	$devlist=M('device')->where(array('psn'=>$psnid,'flag'=>1))->order('id asc')->select();

				$j=0;
				foreach($devlist as $dev){
					$devid = $dev['devid'];
					$psnid = $dev['psn'];
					//var_dump($dev);
					
					$accSelect=M('access')->group('time')->where('time >='.$yes_time.' and time <'.$end_time)->where(array('devid'=>$devid,'psn'=>$psnid))->order('time desc')->limit(0,4)->select();
					$accsize=count($accSelect);
					if($accsize == 0){
						continue;
					}
	
	      	for($i=0;$i < $accsize;$i++){
	      		$time=(int)$accSelect[$i]['time'];
	      		//$time = date('Y-m-d H:s:i',$time);
	      		$right_time=$first_time-$i*$delay_sub;
	      		//echo "devid:";
	      		//var_dump($devid);
	      		//var_dump($time);
	      		//var_dump($right_time);
	      		if($time!=$right_time){
	      			$devSelect[$j]['devid']=$devid;
							$devSelect[$j]['time']=$dev['time'];
							$devSelect[$j]['psnid']=$psnid;
							if(empty($dev['sn'])){
								$devSelect[$j]['fsn']=$psnid.str_pad($devid,4,'0',STR_PAD_LEFT);
							}else{
								$devSelect[$j]['fsn']=$dev['sn'];
							}
							$j++;
	      			break;
	      		}
	      	}
				}
				
				$this->assign('devSelect',$devSelect);
				$this->display();
	}
	
	public function factorynoneout(){
				$psnid=$_GET['psnid'];
				$delay = 4*3600;
				$delay_sub = 2*3600;
 
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
				
      	$devlist=M('factory')->where(array('psnid'=>$psnid))->order('id asc')->select();

				$j=0;
				foreach($devlist as $dev){
					$devid = $dev['devid'];
					$psnid = $dev['psnid'];
					$dev=M('device')->where(array('devid'=>$devid,'psn'=>$psnid,'flag'=>1))->order('id asc')->find();
					//var_dump($dev);
					if(!empty($dev)){
						$accSelect=M('access')->group('time')->where('time >='.$yes_time.' and time <'.$end_time)->where(array('devid'=>$devid,'psn'=>$psnid))->order('time desc')->limit(0,4)->select();
						$accsize=count($accSelect);
						if($accsize==0){
							$devSelect[$j]['devid']=$devid;
							$devSelect[$j]['fsn']=$dev['sn'];
							$devSelect[$j]['time']=$dev['time'];
							$devSelect[$j]['psnid']=$psnid;
							$j++;
	      		}
		      }
				}
				
				$this->assign('devSelect',$devSelect);
				$this->display();
	}
	
	public function factorynone(){
			$psnid=$_GET['psnid'];
			$devSelect=M('factory')->where(array('state'=>4))->where(array('psnid'=>$psnid))->order('devid asc')->select();
			
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
	
	public function factorylow(){
				$psnid=$_GET['psnid'];
				$delay = 4*3600;
				$delay_sub = 2*3600;
 
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
  			
				$devs=M('device')->where(array('flag'=>1))->where(array('psn'=>$psnid))->order('devid asc')->select();
				$devcount=count($devs);
				$j=0;
				for($i=0; $i< $devcount; $i++){
					$devid = $devs[$i]['devid'];
					$accSelect=M('access')->where('time >='.$yes_time.' and time <'.$end_time)->where(array('devid'=>$devid,'psn'=>$psnid))->order('time desc')->limit(0,4)->select();
					//echo "devid:";
					//var_dump($devid);
					$hcount=0;
					foreach($accSelect as $acc){
						$temp1=(float)$acc['temp1'];
						$temp2=(float)$acc['temp2'];
						$temp3=(float)$acc['env_temp'];
						//var_dump($temp1);
						//var_dump($temp2);
						//var_dump($temp3);
						if($temp1 < 20||$temp2 < 20)
						{
							$hcount++;
						}else{
							$hcount=0;
						}
						//var_dump($hcount);
						if($hcount==4){
							$devSelect[$j]['devid']=$devid;
							//$devSelect[$j]['fsn']=$devs[$i]['sn'];
							if(empty($devs[$i]['sn'])){
								$devSelect[$j]['fsn']=$psnid.str_pad($devid,4,'0',STR_PAD_LEFT);
							}else{
								$devSelect[$j]['fsn']=$devs[$i]['sn'];
							}
							$devSelect[$j]['time']=$devs[$i]['time'];
							$devSelect[$j]['psnid']=$devs[$i]['psn'];
							$j++;
							break;
						}
					}
				}
				//exit;
				//var_dump($devSelect);
				$this->assign('devSelect',$devSelect);
				$this->display();
	}

	public function factoryhigh(){
				$psnid=$_GET['psnid'];
				$delay = 4*3600;
				$delay_sub = 2*3600;
 
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
  			
				$devs=M('device')->where(array('flag'=>1))->where(array('psn'=>$psnid))->order('devid asc')->select();
				$devcount=count($devs);
				$j=0;
				for($i=0; $i< $devcount; $i++){
					$devid = $devs[$i]['devid'];
					$accSelect=M('access')->where('time >='.$yes_time.' and time <'.$end_time)->where(array('devid'=>$devid,'psn'=>$psnid))->order('time desc')->limit(0,4)->select();
					//echo "devid:";
					//var_dump($devid);
					$hcount=0;
					foreach($accSelect as $acc){
						$temp1=(float)$acc['temp1'];
						$temp2=(float)$acc['temp2'];
						$temp3=(float)$acc['env_temp'];
						//var_dump($temp1);
						//var_dump($temp2);
						//var_dump($temp3);
						if($temp1 >38 ||$temp2 > 38)
						{
							$hcount++;
						}else{
							$hcount=0;
						}
						if($hcount>0){
							$devSelect[$j]['devid']=$devid;
							//$devSelect[$j]['fsn']=$devs[$i]['sn'];
							if(empty($devs[$i]['sn'])){
								$devSelect[$j]['fsn']=$psnid.str_pad($devid,4,'0',STR_PAD_LEFT);
							}else{
								$devSelect[$j]['fsn']=$devs[$i]['sn'];
							}
							$devSelect[$j]['time']=$devs[$i]['time'];
							$devSelect[$j]['psnid']=$devs[$i]['psn'];
							$j++;
							break;
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
		  for($i=2;$i<=200;$i++){
					$dev=array(
						'psn'=>12,
						'shed'=>1,
						'fold'=>1,
						'flag'=>0,
						'state'=>1,
						's_count'=>0,
						'rid'=>$i,
						'age'=>1,
						'devid'=>$i,
					);
					//$saveSql=M('device')->add($dev);
					//dump($saveSql);
  		}
  		echo "ok";
  		exit;
	}
	
	public function avgtoday(){
			$psnid=$_GET['psnid'];
			$now = time();
		  $time =date('Y-m-d',$now);
		  dump($time);
			$start_time = strtotime($time)-86400+3600*18;
			$end_time = strtotime($time)+86400;
			$max_count=6;
			$env_check=0.4;
			
			$devs=M('device')->where(array('flag'=>1,'psn'=>$psnid,'dev_type'=>0))->select();
			
			foreach($devs as $dev){
				$devid=$dev['devid'];
				$avg = $dev['avg_temp'];
				if($avg>0){
					continue;
				}
				
				//var_dump($devid);
				$accss=M('access')->where('time >='.$start_time.' and time <='.$end_time)
														->where(array('devid'=>$devid,'psn'=>$psnid))->group('time')->order('time desc')->limit(0,$max_count)
														->select();
				$temp=NULL;
				$count = count($accss);
				if($count==0){
					var_dump($devid);
					var_dump(count($accss));
					//continue;
				}
				$sum=0;
				for($i=0;$i< $count;$i++){
					$acc=$accss[$i];
					$temp1=$acc['temp1'];
					$temp2=$acc['temp2'];
					$temp3=$acc['env_temp'];
					$time=date('Y-m-d H:s:i',$acc['time']);
					//var_dump($time);
					//var_dump($temp3);
					if($temp==NULL){
						$temp=$temp3;
						$a=array($temp1,$temp2);
						$t=max($a);
						$vt=(float)$t;
					}else{
						$v=($temp3-$temp)*$env_check;
						$a=array($temp1,$temp2);
						$m=max($a);
						$vt=$t+$v;
						$a=array($m,$vt);
						$vt=(float)max($a);
						$temp=$temp3;
					}
					$sum+=$vt;
					$accss[$i]['vtemp']=$vt;
					//var_dump($acc);
				}
				//dump($devid);
				$avg= round($sum/$count,2);
				//dump($avg);
				if($avg< 30){
					dump('avg:'.$avg);
					//$avg=0;
				}else{
			  	$devSave=M('device')->where(array('psn'=>$psnid,'devid'=>$devid))->save(array('avg_temp'=>$avg));
				}

			}
			exit;
	}
	
	public function startnew(){
    	$psn= $_GET['psnid'];
    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	dump($start_time);
    	$end_time = $start_time+86400;
			$com_count=8;
			$loweor_count=2;

			$psnfind = M('psn')->where(array('id'=>$psn))->find();
			if(empty($psnfind)){
				echo "PSN NULL.";
				exit;
			}
			$btemp=$psnfind['base_temp'];
    	$hlevl1=$psnfind['htemplev1'];
    	$hlevl2=$psnfind['htemplev2'];
    	$llevl1=$psnfind['ltemplev1'];
    	$llevl2=$psnfind['ltemplev2'];
			$temp_value=$psnfind['check_value'];

			dump($btemp);
			dump($hlevl1);
			dump($hlevl2);
			dump($llevl1);
			dump($llevl2);
			dump($temp_value);

			$devs = M('device')->where(array('psn'=>$psn,'flag'=>1,'dev_type'=>0))->select();
			
			if(empty($devs)){
				echo "DEV NULL.";
				exit;
			}
    	foreach($devs as $dev){
    		$devidlist[]=$dev['devid'];
    	}
    	dump($devidlist);
    	
    	$wheredev['devid']=array('in',$devidlist);
    	
    	$accSelect=M('access')->where($wheredev)->where('time >='.$start_time.' and time <='.$end_time)->where(array('psn'=>$psn))->order('time desc')->select();
    	
    	$sickSelect=D('sickness')->where($wheredev)->where(array('psnid'=>$psn))->select();
    	
    	dump($sickSelect);

			dump('high');
			foreach($devs as $dev){
				$devid=$dev['devid'];
				//$ret=M('access')->where('time <'.$end_time)->where(array('psn'=>$psn,'devid'=>$devid))->group('time')->order('time desc')->limit(0,$com_count)->select();
				$flag=$dev['flag'];
				$avg=$dev['avg_temp'];
				
				$acc_size=0;
				unset($acc_list);
				$acc_list = array();
				foreach($accSelect as $acc){
					if($acc['devid']==$devid){
						$acc_list[]=$acc;
					}
				}
				$acc_size=count($acc_list);
				
				//dump($devid);
				if($avg==0){
					dump('no avg:'.$devid);
					continue;
				}
				
				if($flag==1&&$acc_size>0){
					$ntemp = 255;
					$index =0;
					$hcount = 0;
					$pre_time=0;
  				for($i=0;$i< $acc_size;$i++){
  					$cur_time=$acc_list[$i]['time'];
  					if($pre_time==$cur_time){
  						//dump('same:'.$devid.' :'.$cur_time);
  						continue;
  					}

	    			$temp1=$acc_list[$i]['temp1'];
						$temp2=$acc_list[$i]['temp2'];
						$temp3=$acc_list[$i]['env_temp'];
						$a=array($temp1,$temp2);
						$t=max($a);
						$vt=(float)$t;
						$temp= round($btemp+($vt-$avg)*$temp_value,2);
						
						if($ntemp==255){
							$last_temp=$temp;
							$last_time=$cur_time;
							$ntemp=$temp;
						}else{
							if($temp>=$ntemp){
								$ntemp=$temp;
								$index=$i;
							}
						}
						if($temp>$hlevl1){
							//if(abs($temp1-$temp2)< 0.2&&$temp3 > 30)
							if($temp1> 38&&$temp2> 38)
							{
								//dump('devid:'.$devid.':'.$ntemp);
								//dump($cur_time);
								//dump($pre_time);
								$hcount++;
							}
						}else{
							if($hcount< 2){
								$hcount=0;
							}
						}
						$pre_time=$cur_time;
  				}
  				//var_dump($index);
  				//dump('devid:'.$devid.':'.$ntemp);
  				
					$temp1=$acc_list[$index]['temp1'];
					$temp2=$acc_list[$index]['temp2'];
					$temp3=$acc_list[$index]['env_temp'];
					$index_time =$acc_list[$index]['time']; 
					$date = date('Y-m-d H:i:s',$index_time);
					
					if($ntemp>$hlevl1){
							if($ntemp>$hlevl2){
								$level=2;
							}else{
								$level=1;
							}
					}else{
						$level=0;
					}
					//dump('devid:'.$devid.':'.$level);
					//dump('devid:'.$devid.':'.$hcount);
					$find_sick=false;
					$sick=NULL;
					foreach($sickSelect as $s){
						if($s['devid']==$devid){
							$find_sick=true;
							$sick=$s;
							dump('find sick.');
							dump($sick);
							break;
						}
					}

					if($sick==NULL){
						if($level>0&&$hcount>=2){
		        	$sk=array(
					   	  'psnid'=>$psn,
					  		'devid'=>$devid,
					  		'sn'=>$dev['sn'],
					  		'shed'=>$dev['shed'],
					  		'fold'=>$dev['fold'],
					  		'temp1'=>$ntemp,
								'time'=>$index_time,
								'level'=>$level,
								'date'=>$date,
								'state'=>1,
								'days'=>1,
					  		);
					  	echo "add:";
						  dump($devid);
						  dump($psn);
					  	dump($sk);
  			  	 	$addSql=D('sickness')->add($sk);
  			  	 	$adddev[]=$devid;
  			  	}
					}else{						
	  					if($level==0||$hcount< 2){
	  						if($sick['state']==1){
		  						if($sick['flag']==1){
			    					$sk=array(
									  		'temp1'=>$last_temp,
												'time'=>$last_time,
												'date'=>$date,
												'level'=>$level,
												'state'=>0,
									  		);
									  echo "save:";
								  	$saveSql=D('sickness')->where(array('devid'=>$devid,'psnid'=>$psn))->save($sk);
								  	$deldev[]=$devid;
								  }else{
								  	echo "del:";
								  	$delSql=D('sickness')->where(array('devid'=>$devid,'psnid'=>$psn))->delete();
								  	$deldev[]=$devid;
								  }
								  	//$saveSql=D('sickness')->where(array('devid'=>$devid,'psnid'=>$psn))->delete();
								  dump($devid);
								  dump($psn);
								  dump($sk);
								}
	  					}else{
	  							$day1 = strtotime((date('Y-m-d',$index_time)));
		  						$day2 = strtotime((date('Y-m-d',$sick['time'])));
		  						dump($sick['devid']);
		  						dump(date('Y-m-d H:s',$index_time));
		  						dump(date('Y-m-d H:s',$sick['time']));
		  						dump($ntemp);
		  						if($day1!=$day2){
		    						$days=(int)$sick['days'];
		    						if($day1-$day2>=86400){
		      						$days=$days+1;
		      					}
		      					$sk=array(
									  		'temp1'=>$ntemp,
												'time'=>$index_time,
												'date'=>$date,
												'level'=>$level,
												'state'=>1,
												'days'=>$days,
									  		);
									  echo "save2:";
								  	$saveSql=D('sickness')->where(array('devid'=>$devid,'psnid'=>$psn))->save($sk);
								  	$adddev[]=$devid;
									  dump($sk);
								  }
						  }
					}
				}
			}
			
			dump('low');
			foreach($devs as $dev){
				$devid=$dev['devid'];
				//$ret=M('access')->where('time <'.$end_time)->where(array('psn'=>$psn,'devid'=>$devid))->group('time')->order('time desc')->limit(0,$com_count)->select();
				$flag=$dev['flag'];
				$avg=$dev['avg_temp'];

				$acc_size=0;
				unset($acc_list);
				$acc_list = array();
				foreach($accSelect as $acc){
					if($acc['devid']==$devid){
						$acc_list[]=$acc;
					}
				}
				$acc_size=count($acc_list);
				//dump($devid);
				if($avg==0){
					dump('no avg:'.$devid);
					continue;
				}
				
				if($flag==1&&$acc_size>0){
					$ntemp = 255;
					$index =0;
					$hcount = 0;
					$pre_time=0;
  				for($i=0;$i< $acc_size;$i++){
  					$cur_time=$acc_list[$i]['time'];
  					if($pre_time==$cur_time){
  						//dump('same:'.$devid.' :'.$cur_time);
  						continue;
  					}
  					$date = date('Y-m-d H:i:s',$cur_time);
	    			$temp1=$acc_list[$i]['temp1'];
						$temp2=$acc_list[$i]['temp2'];
						$temp3=$acc_list[$i]['env_temp'];
						$a=array($temp1,$temp2);
						$t=max($a);
						$vt=(float)$t;
						if($vt < 30){
							$temp=$vt;
						}else{
							$temp= round($btemp+($vt-$avg)*$temp_value,2);
						}
						
						//dump($temp);
						
						if($ntemp==255){
							$last_temp=$temp;
							$last_time=$cur_time;
							$ntemp=$temp;
						}else{
							if($temp< $ntemp){
								$ntemp=$temp;
								$index=$i;
							}
						}
						if($temp<=$llevl1){
							//dump('low temp dev:'.$devid.':'.$ntemp);
							//dump($cur_time);
							//dump($pre_time);
							$lcount++;
						}else{
							if($lcount< $loweor_count){
								$lcount=0;
							}
						}
						$pre_time=$cur_time;
  				}
  				//var_dump($index);
  				//var_dump($ntemp);
  				
					$temp1=$acc_list[$index]['temp1'];
					$temp2=$acc_list[$index]['temp2'];
					$temp3=$acc_list[$index]['env_temp'];
					$index_time =$acc_list[$index]['time'];
					if($ntemp<=$llevl1){
							if($ntemp<=$llevl2){
								$level=2;
							}else{
								$level=1;
							}
					}else{
						$level=0;
					}
					
					$find_sick=false;
					$sick=NULL;
					foreach($sickSelect as $s){
						if($s['devid']==$devid){
							$find_sick=true;
							$sick=$s;
							dump('find sick.');
							dump($sick);
							break;
						}
					}

					if($sick==NULL){
						if($level>0&&$lcount>=$loweor_count){
		        	$sk=array(
					   	  'psnid'=>$psn,
					  		'devid'=>$devid,
					  		'sn'=>$dev['sn'],
					  		'shed'=>$dev['shed'],
					  		'fold'=>$dev['fold'],
					  		'temp1'=>$ntemp,
								'time'=>$index_time,
								'level'=>$level,
								'date'=>$date,
								'state'=>2,
								'days'=>1,
					  		);
					  	echo "addl:";
					  	dump($sk);
  			  	 	$saveSql=D('sickness')->add($sk);
  			  	}
					}else{
  					if($level==0||$lcount< $loweor_count){
  						if($sick['state']==2){
	  						if($sick['flag']==1){
		    					$sk=array(
								  		'temp1'=>$last_temp,
											'time'=>$last_time,
											'date'=>$date,
											'level'=>$level,
											'state'=>0,
								  		);
								  echo "savel1:";
								  $saveSql=D('sickness')->where(array('devid'=>$devid,'psnid'=>$psn))->save($sk);
								}else{
									 echo "dell1:";
									 $saveSql=D('sickness')->where(array('devid'=>$devid,'psnid'=>$psn))->delete();
								}
								
							  dump($devid);
							  //dump($lcount);
							  dump($psn);
							  //dump($sk);
							}
  					}else{
  						$day1 = strtotime((date('Y-m-d',$index_time)));
  						$day2 = strtotime((date('Y-m-d',$sick['time'])));
  						dump($sick['devid']);
  						//dump($lcount);
  						dump(date('Y-m-d H:s',$index_time));
  						dump(date('Y-m-d H:s',$sick['time']));
  						dump($ntemp);
  						if($day1!=$day2){
    						$days=(int)$sick['days'];
    						if($day1-$day2>=86400){
      						$days=$days+1;
      					}
      					$sk=array(
							  		'temp1'=>$ntemp,
										'time'=>$index_time,
										'date'=>$date,
										'level'=>$level,
										'state'=>2,
										'days'=>$days,
							  		);
							  echo "savel2:";
							  $saveSql=D('sickness')->where(array('devid'=>$devid,'psnid'=>$psn))->save($sk);
							  dump($devid);
							  //dump($lcount);
							  dump($psn);
							  //dump($sk);
						  }
						}
					}
				}
			}
			
			$userinfo=M('usermsginfo')->where(array('psnid'=>$psn))->select();
			$sub=',';
     	$other_head1='设备(';
     	$foot=')体温升高,';
     	$other_foot1=')体温已恢复正常,';
     	$other_head1=iconv("GBK", "UTF-8", $other_head1); 
     	$foot=iconv("GBK", "UTF-8", $foot); 
     	$other_foot1=iconv("GBK", "UTF-8", $other_foot1); 
		
			if($adddev){
				foreach($adddev as $dev){
     				if($devname!=NULL){
     					$devname=$devname.$sub;
     				}else{
     					$devname='ID:';
     				}
     				$devname=$devname.$dev;
				}
			}
			if($deldev){
				foreach($deldev as $dev){
     				if($devname2!=NULL){
     					$devname2=$devname2.$sub;
     				}else{
     					$devname2='ID:';
     				}
     				$devname2=$devname2.$dev;
				}
			}
			
   		foreach($userinfo as $user){
   			$phone[]=$user['phone'];
   			$name=$user['info'];
   		}
   		
     	if($devname){
     		$other=$other_head1.$devname.$foot;
     	} 	
     	
     	if($devname2){
     		$other=$other.$other_head1.$devname2.$other_foot1;
     	}
     					
     	$msg[]=$name;

     	if($other){
     		$msg[]=$other;
     	}else{
     		$msg[]='';
     	}
			//$phone[]='15010150766';
			dump($phone);
			dump($msg);
			if($phone&&$other){
				//send163msg($phone,$msg);
			}
	}
	
	public function setting(){
		$aip = $_POST['aip'];
		if($aip=='ios'){
			$devid = $_POST['devid'];
			$psnid= $_POST['psnid'];
			$temp = $_POST['temp1'];
		}else{
			$devid = $_GET['devid'];
	  	$psnid= $_GET['psnid'];
			$temp = $_GET['temp1'];
	  	$name=$_SESSION['name'];
	  	$this->assign('name',$name);
		}
		
  	$state =(int)$_POST['radio1'];
    $msg = $_POST['msg'];

    if(!empty($msg)){
	    if($state==1){
	    	 //var_dump($state);
	       $sk=array(
						'state'=>0,
			  		);
			   $saveSql=D('sickness')->where(array('devid'=>$devid,'psn'=>$psnid))->save($sk);
			   
	    }else{
	       $sk=array(
						'flag'=>1,
			  		);
			   $saveSql=D('sickness')->where(array('devid'=>$devid,'psn'=>$psnid))->save($sk);
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
    }else{
    	if($aip!='ios'){
	   		$this->display();
	   		exit;
    	}
    }
    
		if($aip=='ios'){
			$psnfind = M('psn')->where(array('id'=>$psnid))->find();
			if(empty($psnfind)){
				echo "PSN NULL.";
				exit;
			}
	  	$hlevl1=$psnfind['htemplev1'];
	  	$hlevl2=$psnfind['htemplev2'];
	  	$llevl1=$psnfind['ltemplev1'];
	  	$llevl2=$psnfind['ltemplev2'];
	  	
			$record=M('sickrecord')->where(array('devid'=>$devid ,'psnid'=>$psnid))->order('time desc')->select();
			//dump($record);
			for($i=0;$i< count($record);$i++){
				$value=$record[$i]['temp1'];
				$record[$i]['state']=0;
				$level=0;
				if($value >$hlevl1){
					$record[$i]['state']=1;
					$level=1;
					if($value>=$hlevl2){
						$level=2;
					}
				}
				if($value < $llevl1){
					$record[$i]['state']=2;
					$level=1;
					if($value<= $llevl2){
						$level=2;
					}
				}
				
				$record[$i]['level']=$level;
			}
			$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000200,'descs'=>$record));
			$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
			exit;
		}
		
		$this ->redirect('Devselect/sickness',array(),0,'');
	}
	
	public function setting2(){
		$aip = $_POST['aip'];
		if($aip=='ios'){
			$devid = $_POST['devid'];
			$psnid= $_POST['psnid'];
			$temp = $_POST['temp1'];
		}else{
			$devid = $_GET['devid'];
	  	$psnid= $_GET['psnid'];
			$temp = $_GET['temp1'];
	  	$name=$_SESSION['name'];
	  	$this->assign('name',$name);
		}
  	
		$msg= $_POST['msg'];
		$temp = $_GET['temp1'];

		$psnfind = M('psn')->where(array('id'=>$psnid))->find();
		if(empty($psnfind)){
			echo "PSN NULL.";
			exit;
		}

  	$hlevl1=$psnfind['htemplev1'];
  	$hlevl2=$psnfind['htemplev2'];
  	$llevl1=$psnfind['ltemplev1'];
  	$llevl2=$psnfind['ltemplev2'];
		
		//dump($hlevl1);
		//dump($hlevl2);
		//dump($llevl1);
		//dump($llevl2);
		
		if(!empty($msg)){
			$rec = array(
						'devid'=>$devid,
						'psnid'=>$psnid,
						'temp1'=>$temp,
						'msg'=>$msg,
					);
			$rd = D('sickrecord')->add($rec);	
			if($aip!='ios'){
				$this ->redirect('Devselect/sickness',array('tab'=>2),0,'');
				exit;
			}
			
		}
		
		$record=M('sickrecord')->where(array('devid'=>$devid ,'psnid'=>$psnid))->order('time desc')->select();
		//dump($record);
		for($i=0;$i< count($record);$i++){
			$value=$record[$i]['temp1'];
			$level=0;
			$record[$i]['state']=0;
			if($value >$hlevl1){
				$record[$i]['state']=1;
				$level=1;
				if($value>=$hlevl2){
					$level=2;
				}
			}
			if($value < $llevl1){
				$record[$i]['state']=2;
				$level=1;
				if($value<= $llevl2){
					$level=2;
				}
			}
			
			$record[$i]['level']=$level;
		}
		//dump($record);
		if($aip=='ios'){
			$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000200,'descs'=>$record));
			$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
			exit;
		}
		
    $this->assign('sickrecord',$record);
    $this->display();
	}
	
	public function setting3(){
		$aip = $_POST['aip'];
		if($aip=='ios'){
			$devid = $_POST['devid'];
			$psnid= $_POST['psnid'];
			$temp = $_POST['temp1'];
		}else{
			$devid = $_GET['devid'];
	  	$psnid= $_GET['psnid'];
			$temp = $_GET['temp1'];
	  	$name=$_SESSION['name'];
	  	$this->assign('name',$name);
		}
		$msg= $_POST['msg'];
		$temp = $_GET['temp1'];
  	$state =(int)$_POST['radio1'];

  	$this->assign('name',$name);
 		//var_dump($state);
		if(!empty($msg)){
			$rec = array(
						'devid'=>$devid,
						'psnid'=>$psnid,
						'temp1'=>$temp,
						'msg'=>$msg,
					);
			if($state==2){
				$sk=array(
						'flag'=>2,
			  		);
			  $saveSql=D('sickness')->where(array('devid'=>$devid,'psn'=>$psnid))->save($sk);
			  $rd = D('recovery')->add($rec);	
			}else{
				$sk=array(
						'flag'=>1,
			  		);
			  $saveSql=D('sickness')->where(array('devid'=>$devid,'psn'=>$psnid))->save($sk);
			  $rd = D('sickrecord')->add($rec);	
			}
			if($aip!='ios'){
				$this ->redirect('Devselect/sickness',array('tab'=>3),0,'');
				exit;
			}
		}
		
		if($aip=='ios'){
			$psnfind = M('psn')->where(array('id'=>$psnid))->find();
			if(empty($psnfind)){
				echo "PSN NULL.";
				exit;
			}
	  	$hlevl1=$psnfind['htemplev1'];
	  	$hlevl2=$psnfind['htemplev2'];
	  	$llevl1=$psnfind['ltemplev1'];
	  	$llevl2=$psnfind['ltemplev2'];
	  	
			$record=M('sickrecord')->where(array('devid'=>$devid ,'psnid'=>$psnid))->order('time desc')->select();
			//dump($record);
			for($i=0;$i< count($record);$i++){
				$value=$record[$i]['temp1'];
				$level=0;
				$record[$i]['state']=0;
				if($value >$hlevl1){
					$record[$i]['state']=1;
					$level=1;
					if($value>=$hlevl2){
						$level=2;
					}
				}
				if($value < $llevl1){
					$record[$i]['state']=2;
					$level=1;
					if($value<= $llevl2){
						$level=2;
					}
				}
				
				$record[$i]['level']=$level;
			}
			$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000200,'descs'=>$record));
			$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
			exit;
		}
    $this->display();
	}
	
	public function calendar(){
      $devid=$_GET["devid"];

      $postArr=array();
      $postArr['devid']=$devid;
      $accSelect=M('access')->where($postArr)->order('time asc')->select();
      $first = reset($accSelect);
      $end = end($accSelect);

      $firstDay = substr($first[cur_time],0,strpos($first[cur_time],' '));//开始日期
      $endDay = substr($end[cur_time],0,strpos($end[cur_time],' '));//最后日期

      $days = (strtotime($endDay)-strtotime($firstDay))/86400+1;//天数
      $startStr = $firstDay . '00:00:00';
      $endStr = $firstDay .  '23:59:59';

      $startTime = strtotime($startStr);
      $endTime = strtotime($endStr);

      $jarr = array();
      $jsArr = array();
      for($i=0;$i<$days;$i++){
            $start = $startTime+86400*$i;
            $end = $endTime+86400*$i;
            $postArr['time']=array('between',array($start,$end));
            if($selectSql=M('access')->where($postArr)->order('time desc')->select()){
                  $curtime=($selectSql[0][cur_time]);
                  $day= substr($curtime,0,strpos($curtime,' '));
                  $jarr= array('day'=>$day,"price"=>$selectSql[0][temp1]);
                  array_push($jsArr,$jarr);

            }

      };
      $this->assign('data',json_encode($jsArr));
      $this->assign('date',$date);
      $this->display();
	}
	
	public function todayValue(){
				$aip = $_POST['aip'];
				if($aip=='ios'){
					$devid = $_POST['devid'];
					$psnid= $_POST['psnid'];
				}else{
					$devid = $_GET["devid"];
					$psnid=$_GET["psnid"];
					$name=$_SESSION['name'];
					$this->assign('name',$name);
				}

        
        $now = time();
        $postArr = array();
        $postArr['devid'] = $devid;
        $postArr['psn'] = $psnid;
        
				$now = time();
				$time =date('Y-m-d',$now);
				
				$start_time = strtotime($time)-86400*6;
				$end_time = strtotime($time)+86400;
        //var_dump($start_time);
        //var_dump($end_time);
        //var_dump($psnid);
        
        $dateArr = array();
        $temp1Arr = array();
        $temp2Arr = array();
        
      	$psn = M('psn')->where(array('id'=>$psnid))->find();
				if(empty($psn)){
					echo "PSN NULL.";
					exit;
				}
				$btemp=$psn['base_temp'];
				$temp_value=$psn['check_value'];

				//dump($btemp);
				//dump($temp_value);
        
				$dev=M('device')->where(array('devid'=>$devid,'psn'=>$psnid))->find();
				if($dev==NULL){
					//echo "<script type='text/javascript'>alert('设备不存在.');distory.back();</script>";
					$this->display();
					exit;
				}
        $avg=(float)$dev['avg_temp'];
        //$postArr['time']=array('between',array($start,$end));
        //' and time >= '.$start_time.
        if($aip=='ios'){
        	$ios_order='id asc';
        }else{
        	$ios_order='id desc';
        }
        
        if($selectSql=M('access')->where('devid ='.$devid.' and psn= '.$psnid.' and time >= '.$start_time.' and time <= '.$end_time)
													        ->group('time')
													        ->order($ios_order)
													        ->limit(0,24*7)
													        ->select())
        {
           $todayData = array_slice($selectSql,0,24*7);
           $dataCount= count($todayData);
         	 //dump($selectSql);
           for($j=0;$j< $dataCount;$j++){
							$time=($todayData[$j][time]);
							$date=date('m-d H:i',$time);
							$temp1=$todayData[$j]['temp1'];
							$temp2=$todayData[$j]['temp2'];
							$temp3=$todayData[$j]['env_temp'];
							$a=array($temp1,$temp2);
							$t=max($a);
							$vt=(float)$t;
							if($vt< 20||$avg==0){
								$ntemp=$vt;
							}else{
								$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
							}
							//$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
							$selectSql[$j]['ntemp']=$ntemp;
							//var_dump($date);
							//var_dump($vt);
							//var_dump($avg);
							array_push($dateArr,$date);
							array_push($temp1Arr,$ntemp);
							array_push($temp2Arr,$todayData[$j]['env_temp']);
           }
           
				}

			if($aip=='ios'){
					$jarr=array('ret'=>array(
						"ret_message"=>'success',
						'status_code'=>10000200,
						'temp1Arr'=>$temp1Arr,
						'temp2Arr'=>$temp2Arr,
						'dateArr'=>$dateArr)
					);
					$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
					exit;
			}
      $this->assign('temp1Arr',json_encode(array_reverse($temp1Arr)));
      $this->assign('temp2Arr',json_encode(array_reverse($temp2Arr)));
      $this->assign('dateArr',json_encode(array_reverse($dateArr)));

      $this->display();

	}
	
	public function getpwd(){
		$pwd=$_GET['pwd'];
		dump(md5($pwd));
		exit;
	}
	
	public function test(){
		$num=6;
		$str=str_pad($num,2,'0',STR_PAD_LEFT);
		dump($str);
		exit;
	}
	
	public function devmove(){
		$psnid = $_GET['psnid'];
		$psn=M('psn')->where(array('id'=>$psnid))->find();
		$psn_sn=$psn['sn'];
		$devSelect=M('changeidlog')->where(array('psnid'=>$psnid))->order('time desc')->select();
		//dump($dev);
		$this->assign('devSelect',$devSelect);
		$this->assign('psn_sn',$psn_sn);
		$this->display();
	}
	
	public function startchkdrop(){
    	$psn= $_GET['psnid'];
    	$now = time();
			$start_time = strtotime(date('Y-m-d',$now));
    	dump($start_time);
    	$end_time = $start_time+86400;

			$psnfind = M('psn')->where(array('id'=>$psn))->find();
			if(empty($psnfind)){
				echo "PSN NULL.";
				exit;
			}
			$btemp=$psnfind['base_temp'];
    	$hlevl1=$psnfind['htemplev1'];
    	$hlevl2=$psnfind['htemplev2'];
    	$llevl1=$psnfind['ltemplev1'];
    	$llevl2=$psnfind['ltemplev2'];
			$temp_value=$psnfind['check_value'];

			//dump($btemp);
			//dump($hlevl1);
			//dump($hlevl2);
			//dump($llevl1);
			//dump($llevl2);
			//dump($temp_value);

			$devs = M('device')->where(array('psn'=>$psn,'flag'=>1,'dev_type'=>0))->select();
			
			if(empty($devs)){
				echo "DEV NULL.";
				exit;
			}
			dump('high');
			foreach($devs as $dev){
				$devid=$dev['devid'];
				$ret=M('access')->where('time <'.$end_time)->where(array('psn'=>$psn,'devid'=>$devid))->group('time')->order('time desc')->limit(0,1)->select();
				$flag=$dev['flag'];
				$avg=$dev['avg_temp'];
				$size = count($ret);
				//dump($devid);
				
				if($avg==0){
					dump('no avg:'.$devid);
					continue;
				}
				
				if($size>0){
					$ntemp = 255;
					$index =0;
					$hcount = 0;
  				for($i=0;$i< $size;$i++){
  					$date = date('Y-m-d H:i:s',$ret[$i]['time']);
  					$day1 = strtotime((date('Y-m-d',$ret[$i]['time'])));
  					$day2 = strtotime((date('Y-m-d',$s['time'])));
						
	    			$temp1=$ret[$i]['temp1'];
						$temp2=$ret[$i]['temp2'];
						$temp3=$ret[$i]['env_temp'];
						$a=array($temp1,$temp2);
						$t=max($a);
						$vt=(float)$t;
						if($vt>=30){
							$nor[]=$ret[$i];
							//dump('devid:'.$devid.':'.$vt);
						}else{
							$drop[]=$ret[$i];
						}
  				}
				}else{
					$eor[]=$dev;
				}
				
			}
			dump($nor);
			dump($drop);
			dump($eor);
			$devs2 = M('device')->where(array('psn'=>$psn,'flag'=>2,'dev_type'=>0))->select();
			dump($devs2);
			exit;
	}

	public function startnew0908(){
    	$psn= $_GET['psnid'];
    	$date=$_GET['date'];
    	$days=$_GET['days'];
    	
    	if(empty($date)||empty($days)){
    		echo "DATE or DAYS NULL.";
				exit;
    	}
    	
    	$now = time();
			$com_count=8;
			$loweor_count=2;

			$psnfind = M('psn')->where(array('id'=>$psn))->find();
			if(empty($psnfind)){
				echo "PSN NULL.";
				exit;
			}
			$btemp=$psnfind['base_temp'];
    	$hlevl1=$psnfind['htemplev1'];
    	$hlevl2=$psnfind['htemplev2'];
    	$llevl1=$psnfind['ltemplev1'];
    	$llevl2=$psnfind['ltemplev2'];
			$temp_value=$psnfind['check_value'];

			//dump($btemp);
			//dump($hlevl1);
			//dump($hlevl2);
			//dump($llevl1);
			//dump($llevl2);
			//dump($temp_value);

			for($d_index=0;$d_index< $days;$d_index++){
				$start_time = strtotime(date($date))+86400*$d_index;
	    	//dump($start_time);
	    	$end_time = $start_time+86400;
	    	
				$devs = M('device')->where(array('psn'=>$psn,'flag'=>1,'dev_type'=>0))->order('devid asc')->select();
				
				if(empty($devs)){
					echo "DEV NULL.";
					exit;
				}
	    	foreach($devs as $dev){
	    		$devidlist[]=$dev['devid'];
	    	}
	    	//dump($devidlist);
	    	
	    	$wheredev['devid']=array('in',$devidlist);
	    	
	    	$accSelect=M('access')->where($wheredev)->where('time >='.$start_time.' and time <'.$end_time)->where(array('psn'=>$psn))->order('time desc')->select();
	    	
	    	if(empty($accSelect)){
	    		continue;
	    	}
	    	
				//dump('high');
				foreach($devs as $dev){
					$devid=$dev['devid'];
					//$ret=M('access')->where('time <'.$end_time)->where(array('psn'=>$psn,'devid'=>$devid))->group('time')->order('time desc')->limit(0,$com_count)->select();
					$flag=$dev['flag'];
					$avg=$dev['avg_temp'];
					
					$acc_size=0;
					unset($acc_list);
					$acc_list = array();
					foreach($accSelect as $acc){
						if($acc['devid']==$devid){
							$acc_list[]=$acc;
						}
					}
					$acc_size=count($acc_list);
					
					//dump($devid);
					if($avg==0){
						//dump('no avg:'.$devid);
						continue;
					}
					
					if($flag==1&&$acc_size>0){
						$ntemp = 255;
						$index =0;
						$hcount = 0;
						$pre_time=0;
	  				for($i=0;$i< $acc_size;$i++){
	  					$cur_time=$acc_list[$i]['time'];
	  					if($pre_time==$cur_time){
	  						//dump('same:'.$devid.' :'.$cur_time);
	  						continue;
	  					}

		    			$temp1=$acc_list[$i]['temp1'];
							$temp2=$acc_list[$i]['temp2'];
							$temp3=$acc_list[$i]['env_temp'];
							$a=array($temp1,$temp2);
							$t=max($a);
							$vt=(float)$t;
							$temp= round($btemp+($vt-$avg)*$temp_value,2);
							
							if($ntemp==255){
								$last_temp=$temp;
								$last_time=$cur_time;
								$ntemp=$temp;
							}else{
								if($temp>=$ntemp){
									$ntemp=$temp;
									$index=$i;
								}
							}
							if($temp>$hlevl1){
								if($temp1> 38&&$temp2> 38)
								{
									$hcount++;
								}
							}else{
								if($hcount< 3){
									$hcount=0;
								}
							}
							$pre_time=$cur_time;
	  				}

	  				
						$temp1=$acc_list[$index]['temp1'];
						$temp2=$acc_list[$index]['temp2'];
						$temp3=$acc_list[$index]['env_temp'];
						$index_time =$acc_list[$index]['time']; 
						$cur_time = date('Y-m-d H:i:s',$index_time);
						$cur_date = date('Y-m-d',$index_time);
						if($ntemp>$hlevl1){
								if($ntemp>$hlevl2){
									$level=2;
								}else{
									$level=1;
								}
						}else{
							$level=0;
						}
						if($level>0&&$hcount>2){
							$sickinfo['devid']=$devid;
							$sickinfo['temp']=$ntemp;
							$sickinfo['time']=$cur_time;
							$sickinfo['date']=$cur_date;
							$sickinfo['avg']=$avg;
							$sicklist[]=$sickinfo;
							//dump($sickinfo);
						}
						
						
						//dump('devid:'.$devid.':'.$level);
						//dump('devid:'.$devid.':'.$hcount);
					}
				}
			}
			
			//dump($sicklist);
			$this->assign('sicklist',$sicklist);
			$this->display();
	}
	
	public function todayValue0911(){
				$aip = $_POST['aip'];

				$devid = 230;
				$psnid= 12;

        $now = time();
        $postArr = array();
        $postArr['devid'] = $devid;
        $postArr['psn'] = $psnid;
        
				$now = time();
				$time =date('2019-09-08');
				
				$start_time = strtotime($time);
				$end_time = strtotime($time)+86400*3;
        //var_dump($start_time);
        //var_dump($end_time);
        //var_dump($psnid);
        
        $dateArr = array();
        $temp1Arr = array();
        $temp2Arr = array();
        
      	$psn = M('psn')->where(array('id'=>$psnid))->find();
				if(empty($psn)){
					echo "PSN NULL.";
					exit;
				}
				
				$btemp=$psn['base_temp'];
				$temp_value=$psn['check_value'];

				$dev=M('device')->where(array('devid'=>$devid,'psn'=>$psnid))->find();
				if($dev==NULL){
					//echo "<script type='text/javascript'>alert('设备不存在.');distory.back();</script>";
					$this->display();
					exit;
				}
        $avg=(float)$dev['avg_temp'];
        //$postArr['time']=array('between',array($start,$end));
        //' and time >= '.$start_time.
        if($selectSql=M('access')->where('devid ='.$devid.' and psn= '.$psnid.' and time <= '.$end_time)
													        ->group('time')
													        ->order('id desc')
													        ->limit(0,24)
													        ->select())
        {
           //$todayData = array_slice($selectSql,0,24);
           $dataCount= count($todayData);
         	 //dump($selectSql);
           for($j=0;$j< $dataCount;$j++){
							$time=$todayData[$j][time];
							$date=date('m-d H:i',$time);
							$temp1=$todayData[$j]['temp1'];
							$temp2=$todayData[$j]['temp2'];
							$temp3=$todayData[$j]['env_temp'];
							$a=array($temp1,$temp2);
							$t=max($a);
							$vt=(float)$t;
							if($vt< 20||$avg==0){
								$ntemp=$vt;
							}else{
								$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
							}
							//$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
							$selectSql[$j]['ntemp']=$ntemp;
							array_push($dateArr,$date);
							array_push($temp1Arr,$ntemp);
							array_push($temp2Arr,$todayData[$j]['env_temp']);
           }
           
				}

      $this->assign('temp1Arr',json_encode(array_reverse($temp1Arr)));
      $this->assign('temp2Arr',json_encode(array_reverse($temp2Arr)));
      $this->assign('dateArr',json_encode(array_reverse($dateArr)));

      $this->display();

	}
}