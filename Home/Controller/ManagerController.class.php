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
								$userset=M('useropenid')->add(array('openid'=>$openid,'userid'=>$user['id']));
							}
							if($aip=='ios'){
								$jarr=array('ret'=>array("ret_message"=>'success','status_code'=>10000101,'data'=>$user));
								$this ->redirect('',array(),1,json_encode(array('UserInfo'=>$jarr)));
								exit;
							}
							session('userid',	$user['id']);
							session('name',	$user['info']);
            	$this ->redirect('/Devselect/sickness',array(),0,'');
            	exit;
              	
					}else{
							if($aip=='ios'){
								$jarr=array('ret'=>array("ret_message"=>'fail','status_code'=>10000102));
								$this ->redirect('',array(),1,json_encode(array('UserInfo'=>$jarr)));
								exit;
							}
							echo "<script type='text/javascript'>alert('用户不存在.');distory.back();</script>";
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
				$user=M('user')->where(array('id'=>$userFind['userid']))->find();
				session('userid',	$userFind['userid']);
				session('name',	$user['info']);
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
							echo "<script type='text/javascript'>alert('用户不存在.');distory.back();</script>";
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
            	$this ->redirect('/Devselect/select',array(),0,'');
            	exit;
              	
					}else{
							echo "<script type='text/javascript'>alert('用户不存在.');distory.back();</script>";
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
      	$this ->redirect('/Devselect/select',array(),0,'');
      	exit;
			}
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
							dump($useropenid);
							if($useropenid){
								$userset=M('useropenid')->where(array('id'=>$useropenid['id']))->save(array('userid'=>$user['id']));
							}else{
								$userset=M('useropenid')->add(array('openid'=>$openid,'userid'=>$user['id']));
							}
						}else{
							echo "<script type='text/javascript'>alert('授权失败.');distory.back();</script>";
							exit;
						}
						session('userid',	$user['id']);
						session('name',	$user['info']);
          	$this ->redirect('/Devselect/sickness',array(),0,'');
          	exit;
            	
				}else{
						echo "<script type='text/javascript'>alert('用户不存在.');distory.back();</script>";
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
}