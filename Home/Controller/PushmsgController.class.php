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

     	dump($msg);
     	dump($phone);
     	if($phone&&$other){
     		  send163msg($phone,$msg);
     	}
     	exit;
    }
    
    public function weuipushmsg(){
    	$now = time();
    	$tokens=M('weui_token')->where('exprie_time >'.$now)->order('time desc')->find();
    	dump($now);
    	dump($tokens);
    	if($tokens){
    		$acc_token=$tokens['access_token'];
    	}else{
    		$appid = 'wx4dba4ec159da3bf7';
    		$secret = 'bf6fac869e348f3454d68ef9956cd61b';
    		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
    		$ret=http($url);
    		dump($ret);
				$ret=json_decode($ret,true);
				if(!empty($ret['access_token'])){
					$savetoken['exprie_time']=$now+(int)$ret['expires_in'];
					$savetoken['access_token']=$ret['access_token'];
					$token=M('weui_token')->add($savetoken);
					$acc_token=$ret['access_token'];
				}
    	}
    	
    	dump($acc_token);
    	{
    		$url='https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.$acc_token;
    		$ret=http($url);
    		$tags=json_decode($ret,true);
    		dump($tags);
    		foreach($tags['tags'] as $tag){
    			dump($tag['name']);
					if($tag['name']=='station'){
						$tagid=$tag['id'];
						break;
					}
    		}
    	}
    	
    	{
    		dump($tagid);
    		$url = 'https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token='.$acc_token;
    		$data['tagid']=$tagid;
    		$data['next_openid']='';
    		$data=json_encode($data,true);
    		$ret=http($url,$data,'POST');
    		$users=json_decode($ret,true);
    		$ids=$users['data']['openid']
    		dump($ids);
    	}
    	
    	$template_id = '-HzOtD2zosdFQYPhtu5NZg8hHm-X2UcNIo00dcVM4C4';
    	
    	$msg['touser']=$id;
    	$msg['template_id']=$template_id;
    	$msg['url']='http://weixin.qq.com/download';
    	$miniprogram['appid']=$appid;
    	$miniprogram['pagepath']='index?foo=bar';
    	$msg['miniprogram']=$miniprogram;
    	$msg_data['first']=
    	$msg_data['keyword1']=
    	$msg_data['keyword2']=
    	$msg_data['keyword3']=
    	$msg_data['keyword4']=
    	$msg_data['remark']=
    	
    	exit;
    	
    	
    }
}