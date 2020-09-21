<?php
namespace Home\Controller;
use Tools\HomeController; 
use Think\Controller;
class ManagerController extends HomeController {
		public function register(){
			if($_POST['name'] && $_POST['pwd'] && ($_POST['aip']=='ios' || $_POST['aip']=='an')){
				$pwd   =trim($_POST['pwd']);
				$name  =trim($_POST['name']);
				$name  =htmlspecialchars($name);
				$pwdlen=strlen($pwd);
				$userfind=M('user')->where(array(
					'name'=>$name,
					))->find();
				if($userfind){
					$jarr=array('ret'=>array('ret_message'=>'name is existed','status_code'=>10000109));
                    echo json_encode(array('UserInfo'=>$jarr));
                    exit;
				}
				if($pwdlen<6){
					//echo 'password not is less than six';
					$jarr=array('ret'=>array('ret_message'=>'password not is less than six','status_code'=>10000101));
                    echo json_encode(array('UserInfo'=>$jarr));
                    exit;
				}
				$nameArr=array(
					'name'=>$name,
					'pwd' =>md5($pwd),
					);
				$userAdd=M('user')->add($nameArr);

				if($userAdd){
					$jarr=array('ret'=>array('ret_message'=>'register ok','status_code'=>10000104,'data'=>M('user')->where(array('id'=>$userAdd))->find()));
                    echo json_encode(array('UserInfo'=>$jarr));
				}else{
					$jarr=array('ret'=>array('ret_message'=>'register error','status_code'=>10000105));
                    echo json_encode(array('UserInfo'=>$jarr));
				}

				exit;
			}

			if($_POST['name'] && $_POST['pwd'] && $_POST['aip']=='pc'){
				$pwd   =trim($_POST['pwd']);
				$name  =trim($_POST['name']);
				$name  =htmlspecialchars($name);
				$pwdlen=strlen($pwd);

				$userfind=M('user')->where(array(
					'name'=>$name,
					))->find();
				if($userfind){
					echo "<script>alert('用户名已注册,请选择其他用户名!');history.go(-1);</script>";
					exit;
				}
				if($pwdlen<6){
					//echo 'password not is less than six';
					$jarr=array('ret'=>array('ret_message'=>'password not is less than six','status_code'=>10000101));
                    echo json_encode(array('UserInfo'=>$jarr));
				}

				$nameArr=array(
					'name'=>$name,
					'pwd' =>md5($pwd),
					);
				$userAdd=M('user')->add($nameArr);

				if($userAdd){
					$this ->redirect('login',array(),0,'');

				}else{
					$this ->redirect('',array(),0,'');
				}

				exit;
			}
			$this->display();
		}

	  public function valid(){
    	  $signature = $_GET["signature"];  
        $timestamp = $_GET["timestamp"];  
        $nonce = $_GET["nonce"];      
                  
        		$token = "weixin";  
            //logger("valid");
            $echoStr = $_GET["echostr"];
            //dump($echoStr);
            //valid signature , option
            if(checkSignature($signature,$timestamp,$nonce,$token)){
                //ob_clean();
                echo $echoStr;
                exit;
            }
    }
    
		public function login(){
			$code = $_GET['code'];
			$aip = $_POST['aip'];
			if($code){
				//var_dump($code);
				$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx4dba4ec159da3bf7&secret=bf6fac869e348f3454d68ef9956cd61b&code=".$code."&grant_type=authorization_code";
				$ret=http($url);
				$ret=json_decode($ret,true);
				//var_dump($ret);
				$openid=$ret['openid'];
				//var_dump($openid);
				$userFind=M('useropenid')->where(array('openid'=>$openid))->find();
			}
			//dump($userFind);
			if(empty($userFind)){
				if($_POST['pwd']!=NULL&&$_POST['name']!=NULL){
					$openid = $_POST['openid'];
					//var_dump($openid);
					$pwd   =trim($_POST['pwd']);
				  $name =trim($_POST['name']);
					$name  =htmlspecialchars($name);

					$nameArr=array(
						'name'=>$name,
						'pwd' =>md5($pwd)
					);
					$user=M('user')->where($nameArr)->find();
					if($user){
							if($openid){
								$userset=M('useropenid')->add(array('openid'=>$openid,'userid'=>$user['autoid']));
							}
							if($aip=='ios'){
								$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000101,'data'=>$user));
								$this ->redirect('',array(),1,json_encode(array('UserInfo'=>$jarr)));
								exit;
							}
							session('userid',	$user['id']);
							session('user_autoid',	$user['autoid']);
							session('name',	$user['info']);
            	$this ->redirect('/Devselect/sickness',array(),0,'');
            	exit;
              	
					}else{
							if($aip=='ios'){
								$jarr=array('ret'=>array("ret_message"=>'fail','status_code'=>10000102));
								$this ->redirect('',array(),1,json_encode(array('UserInfo'=>$jarr)));
								exit;
							}
							echo "<script type='text/javascript'>alert('用户名或密码错误.');distory.back();</script>";
							$this->display();
	          	exit;
					}
				}else{
					if($aip=='ios'){
						$jarr=array('ret'=>array("ret_message"=>'fail','status_code'=>10000103,'openid'=>$openid));
						$this ->redirect('',array(),1,json_encode(array('UserInfo'=>$jarr)));
						exit;
					}
				}
				$this->assign('openid',$openid);
				$this->display();
			}
			else{
				$user=M('user')->where(array('autoid'=>$userFind['userid']))->find();
				session('userid',	$userFind['userid']);
				session('name',	$user['info']);
				session('user_autoid',	$user['autoid']);
				if($aip=='ios'){
					$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000100,'data'=>$user));
					$this ->redirect('',array(),1,json_encode(array('UserInfo'=>$jarr)));
					exit;
				}
      	$this ->redirect('/Devselect/sickness',array(),0,'');
      	exit;
			}
		}

		public function adddev(){
			$code = $_GET['code'];
			if($code){
				//var_dump($code);
				$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx4dba4ec159da3bf7&secret=bf6fac869e348f3454d68ef9956cd61b&code=".$code."&grant_type=authorization_code";
				$ret=http($url);
				$ret=json_decode($ret,true);
				//var_dump($ret);
				$openid=$ret['openid'];
				//var_dump($openid);
				$userFind=M('useropenid')->where(array('openid'=>$openid))->find();
			}
			//var_dump($userFind);
			
			if(empty($userFind)){
				
				if($_POST['pwd']!=NULL&&$_POST['name']!=NULL){
					$openid = $_SESSION['openid'];
					//var_dump($openid);
					$pwd   =trim($_POST['pwd']);
				  $name =trim($_POST['name']);
					$name  =htmlspecialchars($name);

					$nameArr=array(
						'name'=>$name,
						'pwd' =>md5($pwd)
					);
					$userFind=M('user')->where($nameArr)->find();
					if($userFind){
							if($openid){
								$userset=M('useropenid')->add(array('openid'=>$openid,'userid'=>$userFind['id']));
							}
							session('userid',	$userFind['id']);
            	$this ->redirect('/Devselect/adddev',array(),0,'');
            	exit;
              	
					}else{
							echo "<script type='text/javascript'>alert('用户名或密码错误.');distory.back();</script>";
							$this->display();
	          	exit;
					}
				}else{
					session('openid', 	$openid);	
				}
				$this->display();
			}
			else{
				//$user=M('user')->where(array('id'=>$userFind['userid']))->find();
				session('userid',	$userFind['userid']);
      	$this ->redirect('/Devselect/adddev',array(),0,'');
      	exit;
			}
		}
		
		public function loginbase(){
				if($_POST['pwd']!=NULL&&$_POST['name']!=NULL){
					$openid = $_SESSION['openid'];
					//var_dump($openid);
					$pwd   =trim($_POST['pwd']);
				  $name =trim($_POST['name']);
					$name  =htmlspecialchars($name);

					$nameArr=array(
						'name'=>$name,
						'pwd' =>md5($pwd)
					);
					//dump($nameArr);
					//exit;
					$userFind=M('user')->where($nameArr)->find();
					if($userFind){
							//if($openid){
								//$userset=M('useropenid')->add(array('openid'=>$openid,'userid'=>$userFind['id']));
							//}
							//dump($userFind);
							//exit;
							session('admin_userid',	$userFind['id']);
            	$this ->redirect('/Devselect/select',array(),0,'');
            	exit;
              	
					}else{
							echo "<script type='text/javascript'>alert('用户名或密码错误.');distory.back();</script>";
							//$this->display();
	          	exit;
					}
				}
				$this->display();
      	exit;
		}
		
		public function relogin(){
			$code = $_GET['code'];
			if($code){
				//var_dump($code);
				$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx4dba4ec159da3bf7&secret=bf6fac869e348f3454d68ef9956cd61b&code=".$code."&grant_type=authorization_code";
				$ret=http($url);
				$ret=json_decode($ret,true);
				//var_dump($ret);
				$openid=$ret['openid'];
				//dump($openid);
				$userFind=M('useropenid')->where(array('openid'=>$openid))->find();
			}
			//var_dump($userFind);
					
			if($_POST['pwd']!=NULL&&$_POST['name']!=NULL){
				$openid = $_POST['openid'];//$_SESSION['openid'];
				//dump($openid);
				//exit;
				$pwd   =trim($_POST['pwd']);
			  $name =trim($_POST['name']);
				$name  =htmlspecialchars($name);

				$nameArr=array(
					'name'=>$name,
					'pwd' =>md5($pwd)
				);
				$user=M('user')->where($nameArr)->find();
				//dump($user);
				if($user){
						if($openid){
							$useropenid=M('useropenid')->where(array('openid'=>$openid))->find();
							//dump($useropenid);
							if($useropenid){
								$userset=M('useropenid')->where(array('id'=>$useropenid['id']))->save(array('userid'=>$user['autoid']));
							}else{
								$userset=M('useropenid')->add(array('openid'=>$openid,'userid'=>$user['autoid']));
							}
						}else{
							echo "<script type='text/javascript'>alert('授权失败.');distory.back();</script>";
							exit;
						}
						session('userid',	$user['autoid']);
						session('name',	$user['info']);
						session('user_autoid',	$user['autoid']);
          	$this ->redirect('/Devselect/sickness',array(),0,'');
          	exit;
            	
				}else{
						echo "<script type='text/javascript'>alert('用户名或密码错误.');distory.back();</script>";
				}
			}else{
				session('openid', 	$openid);	
			}
			
			$this->assign('openid',$openid);
			$this->display();
    
		}
		
		public function logout(){
				session('userid',	NULL);
				session('name',	NULL);
      	$this ->redirect('/manager/loginbase',array(),0,'');
      	exit;
		}
		
		public function devlist(){
			$group_id=$_GET['group'];
			if(empty($group_id)){
				$group_id=1;
			}
			
			$gds=M('group_dev')->where(array('group_id'=>$group_id))->select();
			foreach($gds as $gd){
				$devs[]=$gd['rid'];
			}
			//dump($devs);
			$whererid['rid']=array('in',$devs);
			$devSelect=M('device')->where(array('flag'=>1))->where($whererid)->order('devid asc')->select();

			$this->assign('devSelect',$devSelect);
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
	  	$psnid = $_GET['psnid'];
	  	$id=$_GET['devid'];
	  	
    	$start_time = strtotime($time);
    	$end_time = strtotime($time2)+86400;

			$psninfo = M('psn')->where(array('id'=>$psnid))->find();
			$psn=$psninfo['sn'];
      $shed = $dev['shed'];
			$tcc=substr($psninfo['tsn'],0,7);
			
			$devrid=M('device')->field('rid')->where(array('devid'=>$id,'psn'=>$psn))->find();
			$rid=$devrid['rid'];
			$this->assign('rid',$rid);
			
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

        $mydb='access_base';
        if($devid==NULL){
            if($selectSql=M($mydb)->where('devid ='.$id.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->group('time')->order('time desc')->select()){
                $this->assign('devid',$id);
                $this->assign('date',$time);
                $this->assign('date2',$time2);
                $this->assign('id',$id);
								foreach($selectSql as $key=>$acc){
									if($key<count($selectSql)-1){
										$step = (int)$acc['rssi2'];
										$pre_step = (int)$selectSql[$key+1]['rssi2'];
										if($step-$pre_step>=0){
											$cur_step = $step-$pre_step;
										}else{
											if(($acc['rssi3']&0x03)==0x01){
												$cur_step=0;
											}else{
												$cur_step=65535-$pre_step+$step;
											}
										}
										$selectSql[$key]['step2']='+'.$cur_step;
									}
								}
                $this->assign('selectSql',$selectSql);
            }else{
                $this->assign('devid',$id);
                $this->assign('id',$id);
                $this->assign('selectSql',$selectSql);
                $date = date("Y-m-d");
                $this->assign('date',$date);
                $this->assign('date2',$date);
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
}