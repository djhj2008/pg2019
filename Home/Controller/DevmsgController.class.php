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

		$devmsg=M('devmsg')->where(array('state'=>0))->select();
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
			$devmsg[$key]['survival_state']=$cow_sel[$dev['sn_code']];
		}
		
		
		$this->assign('devmsg',$devmsg);
		$this->display();
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
			$viliage=$_POST['viliage'];
			$farmer=$_POST['farmer'];
			$sn=$_POST['sn'];
			
			$tmp = '14867046';
			$phone=array($phone);
			$foot='·ÀÒßÂë:'.substr($sn,-8);
			$foot=iconv("GBK", "UTF-8", $foot); 
			send163msgtmp($phone,$smsmsg,$tmp);
			//$smsmsg[]=$town.$viliage.$farmer;
			$smsmsg=array($town.$viliage.$farmer,$foot);
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