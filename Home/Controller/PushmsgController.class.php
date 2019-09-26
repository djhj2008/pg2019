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
     				if(strlen($devname)>12){
     					$devname=$devname.'...';
     					break;
     				}else{
	     				if($devname!=NULL){
	     					$devname=$devname.$sub;
	     				}else{
	     					$devname='ID:';
	     				}
     					$devname=$devname.$dev['devid'];
     				}
     			}
     		}
     		foreach($psn as $user){
     			$phone[]=$user['phone'];
     			$name=$user['info'];
     		}
     	}
     	
     	//$devname = 'ID:388,389,390,400,401';
			//$sub=',';
			
     	$other_head1='设备(';
     	$foot=')体温升高,';
     	$other_foot1=')体温已恢复正常,';
     	$other_head1=iconv("GBK", "UTF-8", $other_head1);
     	$foot=iconv("GBK", "UTF-8", $foot); 
     	$other_foot1=iconv("GBK", "UTF-8", $other_foot1); 
     	
     	if($devname){
     		$other=$other_head1.$devname.$foot;
     	} 
     	//print_r($test);
     	$msg[]=$name;
     	if($other){
     		$msg[]=$other;
     	}else{
     		$msg[]='';
     	}
     	//$phone[]='15010150766';
     	//dump(iconv_get_encoding());
     	dump($msg);
     	dump($phone);
     	if($phone&&$other){
     		  send163msg($phone,$msg);
     	}
     	exit;
    }
}