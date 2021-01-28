<?php
namespace Home\Controller;
use Think\Controller;
class DevmsgController extends Controller {
  public function index(){
       ob_clean();
       echo 'test';
       exit;
  }
  
	public function lostdevlist(){
		$mode=M('','','DB_CONFIG');
		$time = time();
		$start_time=date('Y-m-d H:i:s',$time-86400*7);
		dump($start_time);
		$table=M('devmsg');
		$devmsg=$table->where('time>="'.$start_time.'"')->select();

		foreach($devmsg as $dev){
			$sncode_list[]= $dev['sn_code'];
		}
		$wheresn['sn_code']=array('in',$sncode_list);
		$cows = $mode->table('cows')->where($wheresn)->select();

		foreach($cows as $cow){
			$cow_sel[$cow['sn_code']]=$cow['survival_state'];
		}
		//dump($cow_sel);
		//exit;
		foreach($devmsg as $key=>$dev){
			if($cow_sel[$dev['sn_code']]!=3){
				unset($devmsg[$key]);
				continue;
			}
			$devmsg[$key]['survival_state']=$cow_sel[$dev['sn_code']];
		}
		
		
		$this->assign('devmsg',$devmsg);
		$this->display();
	}
	
	public function sendmsg(){

		$id=$_GET['id'];
		$msg=M('devmsg')->where(array('id'=>$id))->find();

		if($msg){
			$tmp = '14867046';
			$phone = $msg['phone'];
			$town = $msg['town'];
			$village = $msg['town'];
			$farmer = $msg['farmer_name'];
			$village = $msg['village'];
			$sn=$msg['sn_code'];
			$phone=array($phone);
			$foot='防疫码:'.substr($sn,-8);
			//$smsmsg[]=$town.$viliage.$farmer;
			$smsmsg=array($town.$village.$farmer,$foot);
			$ret=send163msgtmp($phone,$smsmsg,$tmp);
			if($ret['code']==200){
				M('devmsg')->where(array('id'=>$id))->save(array('state'=>1));
			}else{
				M('devmsg')->where(array('id'=>$id))->save(array('state'=>2));
			}

		}
		$this ->redirect('/Devmsg/lostdevlist',NULL,0,'');
	}
	
	public function setflag(){

		$id=$_GET['id'];
		$msg=M('devmsg')->where(array('id'=>$id))->find();
		$flag = $msg['flag'];
		if($flag==0){
			M('devmsg')->where(array('id'=>$id))->save(array('flag'=>1));
		}else{
			M('devmsg')->where(array('id'=>$id))->save(array('flag'=>0));
		}
		
		$this ->redirect('/Devmsg/lostdevlist',NULL,0,'');
	}
	
	
	public function addmsg(){
		$phone=$_POST['phone'];
		if(empty($phone)){
			$id=$_GET['id'];
			$errcode=$_GET['errcode'];
			$msg=M('devmsg')->where(array('id'=>$id))->find();
		}else{
			$id=$_GET['id'];
			$town=$_POST['town'];
			$village=$_POST['village'];
			$farmer=$_POST['farmer'];
			$sn=$_POST['sn'];
			
			$tmp = '14867046';
			$phone=array($phone);
			$foot='防疫码:'.substr($sn,-8);
			send163msgtmp($phone,$smsmsg,$tmp);
			//$smsmsg[]=$town.$viliage.$farmer;
			$smsmsg=array($town.$village.$farmer,$foot);
			$ret=send163msgtmp($phone,$smsmsg,$tmp);
			if($ret['code']==200){
				$this ->redirect('/Devmsg/addmsg',array('id'=>$id,'errcode'=>'1001'),0,'');
				exit;
			}
			$this ->redirect('/Devmsg/addmsg',array('id'=>$id,'errcode'=>'1002'),0,'');
			exit;
		}
		$this->assign('errcode',$errcode);
		$this->assign('msg',$msg);
		$this->display();
	}
	public function pushmsg(){
		$phone1=$_POST['phone'];
		$phone2=$_POST['phone2'];
		$phone3=$_POST['phone3'];
		$phone4=$_POST['phone4'];
		if(empty($phone1)){
			$errcode=$_GET['errcode'];
		}else{
			$town=$_POST['town'];
			$village=$_POST['village'];
			$farmer=$_POST['farmer'];
			$sn=$_POST['sn'];
			$type=$_POST['type'];
			if($type==0){
				$tmp='14867045';
			}else if($type==1){
				$tmp='14860115';
			}else if($type==2){
				$tmp='14867046';
			}

			if($phone1){
				$phone[]=$phone1;
			}
			if($phone2){
				$phone[]=$phone2;
			}
			if($phone3){
				$phone[]=$phone3;
			}
			if($phone4){
				$phone[]=$phone4;
			}
			$foot='防疫码:'.$sn;

			$smsmsg[]=$town.$village.$farmer;
			$smsmsg[]=$foot;

			$ret=send163msgtmp($phone,$smsmsg,$tmp);
			//dump($phone);
			//exit;
			if($ret['code']==200){
				$this ->redirect('/Devmsg/pushmsg',array('id'=>$id,'errcode'=>'1001'),0,'');
				exit;
			}
			$this ->redirect('/Devmsg/pushmsg',array('id'=>$id,'errcode'=>'1002'),0,'');
			exit;
		}
		$this->assign('errcode',$errcode);
		$this->assign('msg',$msg);
		$this->display();
	}
}