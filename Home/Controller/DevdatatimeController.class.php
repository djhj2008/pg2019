<?php
namespace Home\Controller;
use Tools\HomeController; 
use Think\Controller;
class DevdatatimeController extends HomeController {
	public function data(){
		if($_POST['startTime'] && $_POST['endTime'] && isset($_POST['devid']) && ($_POST['aip']=='ios' || $_POST['aip']=='an')){
			$startTime=(int)$_POST['startTime'];
			$endTime=(int)$_POST['endTime'];
			$devid=(int)$_POST['devid'];
			$data=M('access')->where("`devid` = $devid and `time` >= $startTime and `time` <= $endTime")->order('time')->select();
			$jarr=array('ret'=>array('ret_message'=>'access time select','status_code'=>10000110,'data'=>$data));
        	echo json_encode(array('UserInfo'=>$jarr));
		exit;
		}

		/*if($_POST['startTime'] && $_POST['endTime'] && isset($_POST['devid']) && $_POST['aip']=='pc'){
			$startTime=(int)$_POST['startTime'];
			$endTime=(int)$_POST['endTime'];
			$devid=(int)$_POST['devid'];
			$data=M('access')->where("`devid` = $devid and `time` >= $startTime and `time` <= $endTime")->order('time')->select();
			$this->assign('data',$data);
		
		}
		$this->display();*/
		
	}
}