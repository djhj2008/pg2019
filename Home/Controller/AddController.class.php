<?php
namespace Home\Controller;
use Tools\HomeController; 
use Think\Controller;
class AddController extends HomeController {
 	  public function devlist(){

	    	if(empty($_SESSION['userid'])||empty($_SESSION['name'])){
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
					if(empty($userFind)){
						echo "请先去个人中心绑定账号.";
						exit;
					}
			    	
		    	$uid = $userFind['userid'];
		    	$user=M('user')->where(array('autoid'=>$uid))->find();
					$name = $user['info'];
					$this->assign('name',$name);
					session('userid',	$uid);
					session('name',	$name);
				}else{
					$uid = $_SESSION['userid'];
					$name = $_SESSION['name'];
					$this->assign('name',$name);
				}
				if($uid==2){
					$user_autoid=$uid;
					$uid=5;
				}
				
				$user=M('user')->where(array('autoid'=>$uid))->find();
				
				$psnSelect=M('psn')->where(array('userid'=>$uid))->select();
				$psnsize=count($psnSelect);
				if($psnsize>1){
					$this->assign('psnSelect',$psnSelect);
					var_dump($psnSelect);
					exit;
				}
				$psnid=$psnSelect[0]['id'];
				
				$devid = $_POST['devid'];
	    	if(!empty($devid)){
	    		$postArr=array();
            $postArr['devid']=$devid;
            $postArr['psn']=$psnid;
            $postArr['dev_type']=0;
            $postArr2['sn'] = array('like','%'.$devid.'%');
            $postArr2['psn'] =$psnid;
            $postArr2['dev_type'] = 0;
            $where_main['_complex'] = array($postArr,
																				    $postArr2,
																				    '_logic' => 'or');
					  $device = M('device');
            $devSelect= $device->where($where_main)->order('devid asc')->select();
            //var_dump($device->getLastSql());
            //exit;
            if($devSelect){
                $this->assign('devSelect',$devSelect);
            }else{
                $devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psnid))->order('devid asc')->select();
                $this->assign('devSelect',$devSelect);
                $this->assign('ret',"2001");
            }
	    	}else{
	    		if($user_autoid==2){
	    			$devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psnid,'flag'=>1))->limit(20,50)->order('flag desc,devid asc')->select();
	    		}else{
	    			$devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psnid))->order('flag desc,devid asc')->select();
	    		}
	    	}

				$this->assign('devSelect',$devSelect);
				$this->display();
			
    }

	  public function devlistwx(){
		    $uid = $_POST['userid'];
		    $aip = $_POST['aip'];
		    
				$psnSelect=M('psn')->where(array('userid'=>$uid))->select();
				$psnsize=count($psnSelect);
				if($psnsize>1){
					$jarr=array('ret'=>array("ret_message"=>'fail','status_code'=>10000300));
					$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
					exit;
				}
				$psnid=$psnSelect[0]['id'];
				
				$devid = $_POST['devid'];
	    	if(!empty($devid)){
	    		$postArr=array();
            $postArr['devid']=$devid;
            $postArr['psn']=$psnid;
            $postArr['dev_type']=0;
            $postArr2['sn'] = array('like','%'.$devid.'%');
            $postArr2['psn'] =$psnid;
            $postArr2['dev_type'] = 0;
            $where_main['_complex'] = array($postArr,
																				    $postArr2,
																				    '_logic' => 'or');
					  $device = M('device');
            $devSelect= $device->where($where_main)->order('devid asc')->select();
            //var_dump($device->getLastSql());
            //exit;
            if($devSelect){
                $this->assign('devSelect',$devSelect);
								$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000200,'devlist'=>$devSelect));
								$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
            }else{
                $devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psnid))->order('devid asc')->select();
								$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000200,'devlist'=>$devSelect));
								$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
			
            }
	    	}else{
		    	$devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psnid))->order('flag desc,devid asc')->select();
					$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000200,'devlist'=>$devSelect));
					$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
				}
    }
    
    public function adddev(){
    	$uid = $_SESSION['userid'];
			$name = $_SESSION['name'];
			$this->assign('name',$name);
				
			$psnSelect=M('psn')->where(array('userid'=>$uid))->select();
			$psnsize=count($psnSelect);
			$this->assign('psnSelect',$psnSelect);
		
    	if($_POST && $_POST['devid']){
    		$devid = intval($_POST['devid']);
    		$psn = intval($_POST['psn']);
    		$sn = $_POST['sn'];
					
    		$postArr=array(
    		'psn' =>$psn,
    		'devid'=>$devid,
    		'sn'=>$sn,
    		'shed'=>1,
    		'fold'=>1,
    		'flag'=>1,
    		'state'=>1,
    		's_count'=>1,
    		'rid'=>$devid,
    		'age'=>1,
    		);

    		if($have=M('device')->where(array('devid'=>$devid,'psn'=>$psn))->find()){
					$this->assign('errcode',"1001");
				  $this->display();
				  exit;
    		}else{
    			M('device')->add($postArr);
    			$this ->redirect('add/devlist',array(),0,'');
    			exit;
    		}
    	}
      $this->display();
    }

    public function devedit(){

    	$uid = $_SESSION['userid'];
			$name = $_SESSION['name'];
			$this->assign('name',$name);
				
			$psn = $_GET['psnid'];
			$devid = $_GET['devid'];
			$dev =M('device')->where(array('devid'=>$devid,'psn'=>$psn))->find();
			$this->assign('devid',$devid);
			$this->assign('sn',$dev['sn']);
			$this->assign('psnid',$dev['psn']);
			$this->assign('flag',$dev['flag']);
			
			$psnSelect=M('psn')->where(array('id'=>$psn))->find();
			$this->assign('psninfo',$psnSelect['info']);
			
    	if(!empty($_POST['devid'])){
				$sn = $_POST['sn'];
	    	$flag = $_POST['flag'];
				if($flag=="on"){
					$devsave['flag']=1;
				}else{
					$devsave['flag']=0;
				}
    		if(!empty($sn)){
    			$devsave['sn']=$sn;
    		}
    		if($have=M('device')->where(array('devid'=>$devid,'psn'=>$psn))->save($devsave)){
				  $this ->redirect('add/devlist',array(),0,'');
				  exit;
    		}
    		$this->assign('errcode',"2001");
    	}
      $this->display();
    }
    
    public function deveditwx(){
 
			$psnid = $_POST['psnid'];
			$devid = $_POST['devid'];
			
			$dev =M('device')->where(array('devid'=>$devid,'psn'=>$psnid))->find();

			$psnSelect=M('psn')->where(array('id'=>$psnid))->find();
			$dev['psninfo']=$psnSelect['info'];
			
    	if(!empty($_POST['devid'])){
				$sn = $_POST['sn'];
	    	$flag = $_POST['flag'];
				if(empty($sn)&&empty($flag)){
					$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000200,'dev'=>$dev));
					$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
					exit;
				}
				if($flag){
					$devsave['flag']=1;
				}else{
					$devsave['flag']=0;
				}
    		if(!empty($sn)){
    			$devsave['sn']=$sn;
    		}
    		if($have=M('device')->where(array('devid'=>$devid,'psn'=>$psn))->save($devsave)){
    			$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000201));
					$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
    		}
    		$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000301));
				$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
    	}else{
    		$jarr=array('ret'=>array("ret_message"=>'fail','status_code'=>10000300));
				$this ->redirect('',array(),1,json_encode(array('Dev'=>$jarr)));
    	}
      
    }
        
    public function move(){
    	if($_POST['aip']=='ios' || $_POST['aip']=='an'){
    		if($_POST['devid'] && ($_POST['shed'] || $_POST['column'])){

    			if($_POST['devid'] && ($_POST['shed'] || $_POST['column'])){
	    			if($_POST['devid'] && !is_numeric($_POST['devid']) ){
		    			$jarr=array('ret'=>array('ret_message'=>'devid is number','status_code'=>10000019));
		                echo json_encode(array('UserInfo'=>$jarr));
		                exit;

	    			}

	    			if(is_numeric($_POST['devid']) ){
	    				$devIs=M('device')->
		    			$jarr=array('ret'=>array('ret_message'=>'devid is number','status_code'=>10000019));
		                echo json_encode(array('UserInfo'=>$jarr));
		                exit;

	    			}

	    			if($_POST['shed'] && !is_numeric($_POST['shed']) ){
		    			$jarr=array('ret'=>array('ret_message'=>'shed is number','status_code'=>10000019));
		                echo json_encode(array('UserInfo'=>$jarr));
		                exit;

	    			}

	    			if($_POST['column'] && !is_numeric($_POST['column']) ){
		    			$jarr=array('ret'=>array('ret_message'=>'column is number','status_code'=>10000019));
		                echo json_encode(array('UserInfo'=>$jarr));
		                exit;

	    			}


	            }


    		$devInfo=M('device')->where(array('devid'=>intval($_POST['devid']),'flag'=>1))->order('id desc')->find();
    		$devInfoId=$devInfo['id'];
    		array_splice($devInfo,0,1);
    		$devInfo['flag']=1;//新记录flag置为1
    		$devNext=M('device')->add($devInfo);
    		//把原有的记录flag置为2
    		$devLast=M('device')->where(array('id'=>intval($devInfoId)))->save(array('flag'=>2));
            //修改又名移动
            if($_POST['shed']){
            	$saveData['shed']=$_POST['shed'];
            }
            if($_POST['column']){
            	$saveData['column']=$_POST['column'];
            }
            $saveNextDev=M('device')->where(array('id'=>intval($devNext)))->save($saveData);

    		$devNextInfo=M('device')->where(array('id'=>intval($devNext)))->find();

    		//var_dump($devInfo);
    		//var_dump($devNext);
	    		if($devNext && $devLast){
	                    $jarr=array('ret'=>array('ret_message'=>'ok','status_code'=>10000004,'data'=>$devNextInfo));
	                    echo json_encode(array('UserInfo'=>$jarr));

	            }else{
	                   $jarr=array('ret'=>array('ret_message'=>'error','status_code'=>10000005));
	                    echo json_encode(array('UserInfo'=>$jarr));
	            } 
    		exit;
    	    }
    	}


    	if($_POST){
    		if($_POST['devid'] && ($_POST['shed'] || $_POST['column'])){
    		$devInfo=M('device')->where(array('devid'=>intval($_POST['devid']),'flag'=>1))->order('id desc')->find();
    		$devInfoId=$devInfo['id'];
    		array_splice($devInfo,0,1);
    		$devInfo['flag']=1;//新记录flag置为1
    		$devNext=M('device')->add($devInfo);
    		//把原有的记录flag置为2
    		$devLast=M('device')->where(array('id'=>intval($devInfoId)))->save(array('flag'=>2));
            //修改又名移动
            if($_POST['shed']){
            	$saveData['shed']=$_POST['shed'];
            }
            if($_POST['column']){
            	$saveData['column']=$_POST['column'];
            }
            $saveNextDev=M('device')->where(array('id'=>intval($devNext)))->save($saveData);
    		$devNextInfo=M('device')->where(array('id'=>intval($devNext)))->find();


	    		if($devNext && $devLast){
	                  echo "<script type='text/javascript'>alert('移动成功！');distory.back();</script>";
		    			 

	            }else{
	                  echo "<script type='text/javascript'>alert('错误！');distory.back();</script>";
		    			 
	            } 
    		
    	    }
    	}
    	$this->display();

    }

    public function saveInfo(){
    	if($_POST['aip']=='ios' || $_POST['aip']=='an'){
    		if($_POST['devid'] && ($_POST['state'] || $_POST['s_count'] || $_POST['rid'] || $_POST['age'])){

    			

    			if($_POST['devid'] && ($_POST['state'] || $_POST['s_count'] || $_POST['age'])){
	    			if($_POST['devid'] && !is_numeric($_POST['devid']) ){
		    			$jarr=array('ret'=>array('ret_message'=>'devid is number','status_code'=>10000019));
		                echo json_encode(array('UserInfo'=>$jarr));
		                exit;

	    			}



	    			if($_POST['state'] && !is_numeric($_POST['state']) ){
		    			$jarr=array('ret'=>array('ret_message'=>'state is number','status_code'=>10000019));
		                echo json_encode(array('UserInfo'=>$jarr));
		                exit;

	    			}

	    			if($_POST['s_count'] && !is_numeric($_POST['s_count']) ){
		    			$jarr=array('ret'=>array('ret_message'=>'s_count is number','status_code'=>10000019));
		                echo json_encode(array('UserInfo'=>$jarr));
		                exit;

	    			}

	    			if($_POST['age'] && !is_numeric($_POST['age']) ){
		    			$jarr=array('ret'=>array('ret_message'=>'age is number','status_code'=>10000019));
		                echo json_encode(array('UserInfo'=>$jarr));
		                exit;

	    			}


	            }    



    			$devInfo=M('device')->where(array('devid'=>intval($_POST['devid']),'flag'=>1))->order('id desc')->find();
    			//var_dump($devInfo);
    			$postArr=array();
	    		if($_POST['devid']){
	    			$postArr['devid']=intval($_POST['devid']);

	    		}
	    		
		    	if($_POST['state']){
		    		$postArr['state']=intval($_POST['state']);	
	    		}

	    		if($_POST['s_count']){
		    		$postArr['s_count']=intval($_POST['s_count']);	
	    		}

	    		if($_POST['rid']){
		    		$postArr['rid']=
			    		intval($_POST['rid']);	
	    		}

	    		if($_POST['age']){
		    		$postArr['age']=intval($_POST['age']);	
	    		}
	    		if($devInfo=M('device')->where(array('devid'=>intval($_POST['devid']),'flag'=>1))->order('id desc')->find()){
		    		$devInfoId=$devInfo['id'];
		    		array_splice($devInfo,0,1);
		    		$devInfo['flag']=1;//新记录flag置为1
		    		$devNext=M('device')->add($devInfo);
	    		}else{
	    			$jarr=array('ret'=>array('ret_message'=>'devid is no','status_code'=>10000029));
                	echo json_encode(array('UserInfo'=>$jarr));
	    			exit;
	    		}
	    		

	    		$devNextSave=M('device')->where(array('id'=>intval($devNext)))->save($postArr);

		    	if($devNextSave){
		    		$devInfoNew=M('device')->where(array('id'=>$devNext))->find();

		    		$jarr=array('ret'=>array('ret_message'=>'ok','status_code'=>10000010,'data'=>$devInfoNew));
                	echo json_encode(array('UserInfo'=>$jarr));
		    	}else{
		    		$jarr=array('ret'=>array('ret_message'=>'error','status_code'=>10000011));
                    echo json_encode(array('UserInfo'=>$jarr));
		    	}
    			exit;
    		}
    		exit;
    	}




    	if($_POST){
    		if($_POST['devid'] && ($_POST['state'] || $_POST['s_count'] || $_POST['rid'] || $_POST['age'])){

    			if($_POST['devid'] && ($_POST['state'] || $_POST['s_count'] || $_POST['age'])){
	    			if($_POST['devid'] && !is_numeric($_POST['devid']) ){
		    			 echo "<script type='text/javascript'>alert('devid is number');distory.back();</script>";
		    			 $this->display();
		    			 exit;


	    			}

	    			if($_POST['state'] && !is_numeric($_POST['state']) ){
		    			echo "<script type='text/javascript'>alert('state is number');distory.back();</script>";
		    			 $this->display();
		    			 exit;

	    			}

	    			if($_POST['s_count'] && !is_numeric($_POST['s_count']) ){
		    			echo "<script type='text/javascript'>alert('s_count is number');distory.back();</script>";
		    			 $this->display();
		    			 exit;
		   

	    			}

	    			if($_POST['age'] && !is_numeric($_POST['age']) ){
		    			echo "<script type='text/javascript'>alert('age is number');distory.back();</script>";
		    			 $this->display();
		    			 exit;
		           

	    			}


	            }    



    			$devInfo=M('device')->where(array('devid'=>intval($_POST['devid']),'flag'=>1))->order('id desc')->find();
    			//var_dump($devInfo);
    			$postArr=array();
	    		if($_POST['devid']){
	    			$postArr['devid']=intval($_POST['devid']);

	    		}
	    		
		    	if($_POST['state']){
		    		$postArr['state']=intval($_POST['state']);	
	    		}

	    		if($_POST['s_count']){
		    		$postArr['s_count']=intval($_POST['s_count']);	
	    		}

	    		if($_POST['rid']){
		    		$postArr['rid']=
			    		intval($_POST['rid']);	
	    		}

	    		if($_POST['age']){
		    		$postArr['age']=intval($_POST['age']);	
	    		}
	    		//var_dump($postArr);

		    	$devInfo=M('device')->where(array('devid'=>intval($_POST['devid']),'flag'=>1))->order('id desc')->find();
	    		$devInfoId=$devInfo['id'];
	    		array_splice($devInfo,0,1);
	    		$devInfo['flag']=1;//新记录flag置为1
	    		$devNext=M('device')->add($devInfo);

	    		$devNextSave=M('device')->where(array('id'=>intval($devNext)))->save($postArr);

		    	if($devNextSave){
		    		$devInfoNew=M('device')->where(array('id'=>$devNext))->find();

		    		echo "<script type='text/javascript'>alert('ok');distory.back();</script>";
		    			 $this->display();
		    			 exit;
		    	}else{
		    		echo "<script type='text/javascript'>alert('error');distory.back();</script>";
		    			 $this->display();
		    			 exit;
		    	}
    			
    		}
    	}

        $this->display();

    }

    public function select(){
    	if($_POST['aip']=='ios' || $_POST['aip']=='an'){
            if(is_numeric($_POST['devid']) || is_numeric($_POST['shed']) || is_numeric($_POST['column'])){

            }else{
            	$jarr=array('ret'=>array('ret_message'=>'shed column id is number','status_code'=>10000019));
                echo json_encode(array('UserInfo'=>$jarr));
                exit;
            }

        $postArr=array();//声明一个数组
				$postArr['flag']=1;
				
	    	if($_POST['devid']){
	    		$postArr['devid']=intval($_POST['devid']);
	    	}
	    		
		    if($_POST['shed']){
		    	$postArr['shed']=intval($_POST['shed']);	
	    	}

	    	if($_POST['column']){
		    	$postArr['column']=intval($_POST['column']);	
	    	}



	    	if($selectSql=M('device')->where($postArr)->select()){
	    		$jarr=array('ret'=>array('ret_message'=>'ok','status_code'=>10000006,'data'=>$selectSql));
          echo json_encode(array('UserInfo'=>$jarr));
	    	}else{
	    		$jarr=array('ret'=>array('ret_message'=>'error','status_code'=>10000007));
          echo json_encode(array('UserInfo'=>$jarr));
	    	}
            exit;
        }

    	if($_POST){
    		$postArr=array();//声明一个数组
    		$postArr['flag']=1;
	    	if($_POST['devid']){
	    		$postArr['devid']=intval($_POST['devid']);
	    	}
	    		
		    if($_POST['shed']){
		    	$postArr['shed']=intval($_POST['shed']);	
	    	}

	    	if($_POST['column']){
		    	$postArr['column']=intval($_POST['column']);	
	    	}
	    	//var_dump($postArr);
	    	if($selectSql=M('device')->where($postArr)->select()){
	    		//dump($selectSql);
	    		$this->assign('selectSql',$selectSql);
	    	}else{
	    		echo "<script type='text/javascript'>alert('查询的结果不存在');distory.back();</script>";
	    	}	
    	}    	
       $this->display();
    }
    
    public function addserver(){
    	$check_value=0.25;
    	$base_temp=39;
    	$htemplev1=39.5;
    	$htemplev2=40;
    	$ltemplev1=37.5;
    	$ltemplev2=30;
    	$userid=18;
    	$tsn=$_POST['tsn'];
    	$sn=$_POST['sn'];
    	$info=$_POST['info'];
    	if(empty($tsn)){
    		$this->display();
    		exit;
    	}
    	$psnsave['check_value']=$check_value;
    	$psnsave['base_temp']=$base_temp;
    	$psnsave['htemplev1']=$htemplev1;
    	$psnsave['htemplev2']=$htemplev2;
    	$psnsave['ltemplev1']=$ltemplev1;
    	$psnsave['ltemplev2']=$ltemplev2;
    	$psnsave['userid']=$userid;
    	$psnsave['tsn']=$tsn;
    	$psnsave['sn']=$sn;
    	if(!empty($info)){
    		$psnsave['info']=$info;
    	}
    	if($have=M('psn')->where(array('tsn'=>$tsn,'sn'=>$sn))->find()){
					$this->assign('errcode',"1001");
				  $this->display();
				  exit;
    	}else{
    		$ret=M('psn')->add($psnsave);
				$this ->redirect('Devselect/select',NULL,0,'');
    	}
    	$this->display();
    }
    
    public function addstation(){
    	$psnid=$_GET['psnid'];
    	$id=$_POST['id'];
    	$uptime=$_POST['uptime'];
    	$count=$_POST['count'];
    	$rate_id=$_POST['rate_id'];
    	$url="pg.51elook.com";
    	
    	$psn=M('psn')->where(array('id'=>$psnid))->find();
    	if(empty($psn)){
    		$this->assign('ret',"1001");
    		$this ->redirect('Devselect/select',NULL,0,'');
    		exit;
    	}
    	$sn=$psn['sn'];
    	if(empty($id)){
    		$rate=M('rate')->select();
    		$this->assign('sn',$sn);
    		$this->assign('rate',$rate);
    		$this->display();
    		exit;
    	}
    	$devsave['psnid']=$psnid;
    	$devsave['tsn']=$psn['tsn'];
    	$devsave['psn']=$psn['sn'];
    	$devsave['id']=$id;
    	$devsave['psn']=$sn;
    	$devsave['rate_id']=$rate_id;
    	$devsave['uptime']=$uptime;
    	$devsave['count']=$count;
    	$devsave['sn']=$sn;
			$devsave['url']=$url;
    	if($have=M('bdevice')->where(array('psnid'=>$psnid,'id'=>$id))->find()){
					$this->assign('errcode',"1001");
				  $this->display();
				  exit;
    	}else{
    		$ret=M('bdevice')->add($devsave);
				$this->redirect('Devselect/station',array('psnid'=>$psnid),0,'');
    	}
    }
    
    public function delstation(){
    	$autoid=$_GET['autoid'];
    	$have=M('bdevice')->where(array('autoid'=>$autoid))->find();
    	if($have){
    		$ret=M('bdevice')->where(array('autoid'=>$autoid))->delete();
    		$this->redirect('Devselect/station',array('psnid'=>$have['psnid']),0,'');
    	}
    }

    public function editstation(){
    	$autoid=$_GET['autoid'];
			$id=$_POST['id'];
			$uptime=$_POST['uptime'];
			$count=$_POST['count'];
			$rate_id=$_POST['rate_id'];
			$have=M('bdevice')->where(array('autoid'=>$autoid))->find();
			$psnid=$have['psnid'];
			
    	$psn=M('psn')->where(array('id'=>$psnid))->find();
    	if(empty($psn)){
    		$this->assign('ret',"1001");
    		$this ->redirect('Devselect/select',NULL,0,'');
    		exit;
    	}
    	$sn=$psn['sn'];
    	if(empty($id)){
    		$rate=M('rate')->select();
    		$this->assign('sn',$sn);
    		$this->assign('rate',$rate);
    		$this->assign('station',$have);
    		$this->display();
    		exit;
    	}
    	
    	if($have){
				if($id!=$have['id']){
					$savedev['id']=$id;
				}
				if($uptime!=$have['uptime']){
					$savedev['uptime']=$uptime;
				}
				if($count!=$have['count']){
					$savedev['count']=$count;
				}
				if($rate_id!=$have['rate_id']){
					$savedev['rate_id']=$rate_id;
				}
				if(empty($savedev)){
					$this->assign('station',$have);
				  $this->display();
				  exit;
				}else{
					$ret=M('bdevice')->where(array('autoid'=>$autoid))->save($savedev);
					$this->redirect('Devselect/station',array('psnid'=>$have['psnid']),0,'');
					exit;
				}
    	}
    }
    
    public function movedev(){
    	$old_psn=$_POST['old_psn'];
    	$old_devid=$_POST['old_devid'];
    	$new_devid=$_POST['new_devid'];
    	
    	$id=$_GET['changeid'];
    	$dev=M('changeidlog')->where(array('id'=>$id))->find();
    	$psnid=$dev['psnid'];
	    	
    	if(empty($old_psn)||empty($old_devid)||empty($new_devid)){
	    	$old_psn=$dev['old_psn'];
	    	$old_devid=$dev['old_devid'];
	    	
	    	$devlist=M('device')->where(array('psn'=>$psnid))->order('devid asc')->select();
	    	
	    		for($i=2;$i<2000;$i++){
	    			$finddev=0;
	    			foreach($devlist as $v){
	    				if($i==$v['devid']){
	    					$finddev=1;
	    					break;
	    				}
	    			}
	    			if($finddev==0){
	    				$devids[]=$i;
	    			}
	    			if(count($devids)>19){
	    				$this->assign('devids',$devids);
	    				break;
	    			}
	    		}
	    	
	    	$this->assign('dev',$dev);
	    	$this->display();
	    	exit;
    	}
			
			$newdev=array('new_devid'=>$new_devid,
										'flag'=>1);
			$dev=M('changeidlog')->where(array('old_psn'=>$old_psn,'old_devid'=>$old_devid))->save($newdev);

			$this ->redirect('devselect/devmove',array('psnid'=>$psnid),0,'');
    }
    
    public function quitmovedev(){

    	$id=$_GET['changeid'];
			$newdev=array('flag'=>0);
    	$id=$_GET['changeid'];
    	$dev=M('changeidlog')->where(array('id'=>$id))->find();
    	$psnid=$dev['psnid'];
    	
			if($dev){
				$ret=M('changeidlog')->where(array('id'=>$id))->save($newdev);
			}

			$this ->redirect('devselect/devmove',array('psnid'=>$psnid),0,'');
    }
}