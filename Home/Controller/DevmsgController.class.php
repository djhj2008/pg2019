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

		$devmsg=M('devmsg')->select();
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
			$foot=iconv("GBK", "UTF-8", $foot); 
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
			$foot=iconv("GBK", "UTF-8", $foot); 
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
}