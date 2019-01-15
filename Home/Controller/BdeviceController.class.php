<?php
namespace Home\Controller;
use Tools\HomeController; 
use Think\Controller;
class BdeviceController extends Controller {
	public function add(){
		if($_POST['id'] && isset($_POST['uptime']) && isset($_POST['shed'])){
			$id=(int)$_POST['id'];
			$uptime=(int)$_POST['uptime'];
			$shed=(int)$_POST['shed'];
			$add=M('bdevice')->add(
				array(
					'id'=>$id,
					'uptime'=>$uptime,
					'shed'=>$shed,
					)
				);
			if($add){
				echo 'OK';
			}else{
				echo 'ERROR';
			}
			exit;
		}

		if($_POST['id'] && isset($_POST['uptime']) && isset($_POST['shed']) && $_POST['aip']=='pc'){
			$id=(int)$_POST['id'];
			$uptime=(int)$_POST['uptime'];
			$shed=(int)$_POST['shed'];
			$add=M('bdevice')->add(
				array(
					'id'=>$id,
					'uptime'=>$uptime,
					'shed'=>$shed,
					)
				);
		}
	}


	public function find(){
		if($_POST['id']){
			$id=(int)$_POST['id'];
			$find=M('bdevice')->where(
				array(
					'id'=>$_POST['id']
					)
				)->order("autoid desc")->find();
			echo 'OK'.$find['uptime'];
			
			exit;
		}


	}


	public function pfind(){
		if($_POST['id'] && ($_POST['aip']=='ios' || $_POST['aip']=='an')){
			$id=(int)$_POST['id'];
			$find=M('bdevice')->where(
				array(
					'id'=>$_POST['id']
					)
				)->order("autoid desc")->find();
			$jarr=array('ret'=>array('ret_message'=>'OK','status_code'=>10001000,'date'=>$find,));
                echo json_encode(array('UserInfo'=>$jarr));
			
			exit;
		}
	}
	
		public function GetBDeviceState(){
			$id=(int)$_POST['id'];
			$find=M('bdevice')->where(
				array(
					'id'=>$_POST['id']
					)
				)->field(array('uptime','state'))->find();
			if(empty($find)){
				echo "OKE";
			}	else{
				$uptime=$find['uptime'];
				$state=$find['state'];
				if($state==0){
					$devset=M('bdevice')->where(
						array(
						'id'=>$_POST['id']
						)
					)->save(array('state'=>1));
					echo "OK1".$uptime;
				}else{
				    echo "OK".$state.$uptime;
			  }
			}
			exit;
	  }
	
			public function GetBDeviceStateAos(){
			$id=(int)$_POST['id'];
			$find=M('bdevice')->where(
				array(
					'id'=>$_POST['id']
					)
				)->field(array('uptime','state'))->find();
			if(empty($find)){
				echo "OKE";
			}	else{
				$uptime=$find['uptime'];
				$state=$find['state'];
			  echo "OK".$state.$uptime; 
			}
			exit;
	  }
	  
		public function SetBDeviceState(){
			$id=(int)$_POST['id'];
			$state=(int)$_POST['state'];
			$find=M('bdevice')->where(
				array(
					'id'=>$_POST['id']
					)
				)->field(array('uptime','state'))->find();
			if(empty($find)){
				echo "OKE";
			}	else{
				$uptime = $find['uptime'];
				$devset=M('bdevice')->where(
					array(
					'id'=>$_POST['id']
					)
				)->save(array('state'=>$state));
				echo "OK".$state.$uptime;
			}
			exit;
	  }
}












		/*if($_POST['id'] && $_POST['aip']=='pc'){
			$id=(int)$_POST['id'];
			$find=M('bdevice')->where(
				array(
					'id'=>$_POST['id']
					)
				)->select();
			var_dump($find);
			exit;
		}*/
		/*$jarr=array('ret'=>array('ret_message'=>'OK'.date('YmdHis',time()),'status_code'=>10001000,'date'=>$find,));
                echo json_encode(array('UserInfo'=>$jarr));*/