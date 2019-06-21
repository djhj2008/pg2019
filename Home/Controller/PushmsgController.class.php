<?php
namespace Home\Controller;
use Think\Controller;
class PushmsgController extends Controller {
    public function highmsg(){
     	$psnid=$_GET['psnid'];
     	$psn=M('usermsginfo')->where(array('psnid'=>$psnid))->select();
     	$sub=',';
     	if($psn){
     		$devlist=M('sickness')->where(array('psnid'=>$psnid,'state'=>1))->order('devid asc')->select();
     		if($devlist){
     			foreach($devlist as $dev){
     				if($devname!=NULL){
     					$devname=$devname.$sub;
     				}else{
     					$devname='ID:';
     				}
     				$devname=$devname.$dev['devid'];
     			}
     		}
     		foreach($psn as $user){
     			//$phone[]=$user['phone'];
     			$name=$user['info'];
     		}
     	}
     	
     	//dump($name);
     	//dump($devname);
     	$other='设备(ID:30)体温已恢复正常,';
     	$other=iconv("GBK", "UTF-8", $other); 
     	//print_r($test);
     	$msg[]=$name;
     	$msg[]=$devname;
     	$msg[]=$other;
     	$phone[]='13311152676';
     	//dump(iconv_get_encoding());
     	dump($msg);
     	dump($phone);
     	//exit;
     	send163msg($phone,$msg);
     	exit;
    }
}