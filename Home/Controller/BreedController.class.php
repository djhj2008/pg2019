<?php
namespace Home\Controller;
use Think\Controller;
class BreedController extends Controller {
    public function breedlist(){

		  	$devid = $_GET['devid'];
				$psnid = $_GET['psnid'];
	    	
				$devSelect=M('breed')->where(array('psnid'=>$psnid,'devid'=>$devid))->order('time asc')->select();
				$this->assign('devSelect',$devSelect);
				$this->display();
    }

    public function addbreed(){
    	$psnid=$_GET['psnid'];
    	$devid=$_GET['devid'];
    	$uptime=$_POST['uptime'];
    	
    	if(empty($uptime)){
    		$this->assign('psnid',$psnid);
    		$this->assign('devid',$devid);
    		$this->assign('uptime',$uptime);
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
    
    public function delbreed(){
    	$autoid=$_GET['autoid'];
    	$have=M('bdevice')->where(array('autoid'=>$autoid))->find();
    	if($have){
    		$ret=M('bdevice')->where(array('autoid'=>$autoid))->delete();
    		$this->redirect('Devselect/station',array('psnid'=>$have['psnid']),0,'');
    	}
    }
}