<?php
namespace Home\Controller;
use Think\Controller;
class DevLostALowmsgController extends Controller {
  public function index(){
       ob_clean();
       echo 'test';
       exit;
  }
  
	public function lostdevlist(){
		$mode=M('','','DB_CONFIG');
		$now = time();
		$cows=$mode->table('cows')->where(array('survival_state'=>3))->select();
		foreach($cows as $cow){
			$cow_ids[]= $cow['id'];
			$rids[]=(int)$cow['sn_code'];
			$sn_codes[]=$cow['sn_code'];
			$farmers[]=$cow['farmer_id'];
		}
		$whererid['rid']=array('in',$rids);
		//dump($whererid);
		$devs = M('device')->where($whererid)->where(array('flag'=>1))->select();
		
		$bdevs = M('bdevice')->field('psn,id,switch')->where(array('switch'=>1,'state'=>2))->select();
		
		//dump(count($devs));
		dump($bdevs);
		
		foreach($devs as $dev){
			$psn_find=false;
			$psn_now=$dev['psn_now'];
			$rid = (int)$dev['rid'];
			if($psn_now==0){
				$psn_now=$dev['psn'];
				//dump($psn_now);
			}
			foreach($bdevs as $bdev){
				if($psn_now==$bdev['psn']){
					echo 'remove dev:';
					dump($rid);
					$index = (int) array_search($rid,$rids,TRUE);
					dump($cow_ids[$index]);
					unset($cow_ids[$index]);
				}
			}
		}

		//dump($cow_ids);
		//exit;
		$farmers=array_unique($farmers);
		$wherefarmers['fs.id']=array('in',$farmers);
		$farmerlist = $mode->table(array('farmers'=>'fs'))
								->field('fs.id as id,fs.name as name,fs.phone as phone,fs.village_id as village_id,fs.town_id as town_id')
								->where($wherefarmers)
								->select();
					
		foreach($farmerlist as $farmer){
			$farmer_sel[$farmer['id']]=$farmer['name'];
			$phone_sel[$farmer['id']]=$farmer['phone'];
			$villages[]=$farmer['village_id'];
			$towns[]=$farmer['town_id'];
		}
		
		$villages=array_unique($villages);
		$wherevillages['ss.id']=array('in',$villages);
		$villagelist = $mode->table(array('subareas'=>'ss'))
								->field('ss.id as id,ss.name as name')
								->where(array('type'=>'village_id'))
								->where($wherevillages)
								->select();
								
		foreach($villagelist as $village){
			$village_sel[$village['id']]=$village['name'];
		}		

		$towns=array_unique($towns);
		
		$wheretowns['ss.id']=array('in',$towns);
		$townlist = $mode->table(array('subareas'=>'ss'))
								->field('ss.id as id,ss.name as name')
								->where($wheretowns)
								->where(array('type'=>'town_id'))
								->select();
								
		foreach($townlist as $town){
			$town_sel[$town['id']]=$town['name'];
		}
		//$cow_ids[]=4398;		
  	$now = time();
		$today_time = strtotime(date('Y-m-d',$now));
		$end_time = $today_time+86400;
		$start_time = $today_time-86400;
		
		
		$wherecid['cow_id']=array('in',$cow_ids);
		$cowlist=$mode->table('births')->field('cow_id,UNIX_TIMESTAMP(time) as addtime,time')
																	->where(array('cow_type'=>'survival_state','cow_code'=>3))
																	->where('UNIX_TIMESTAMP(time) >'.$start_time.' and UNIX_TIMESTAMP(time)<='.$end_time)
																	->where($wherecid)->order('addtime desc')
																	->select();
		
		dump($start_time);
		dump($end_time);
		foreach($cowlist as $key=>$cow){
			//dump($cow['cow_id']);
			//dump($cow['addtime']);
			//dump($cow_sel[$cow['cow_id']]);
			if(!empty($cow_sel[$cow['cow_id']])){
				if($cow['addtime']>$cow_sel[$cow['cow_id']]){
					$cow_sel[$cow['cow_id']]=(int)$cow['addtime'];
				}
			}else{
				$cow_sel[$cow['cow_id']]=(int)$cow['addtime'];
			}
		}

		$wheresn['sn_code']=array('in',$sn_codes);
		//dump($sn_codes);
		$devmsglist = M('devmsg')->where($wheresn)->select();
				
		foreach($cows as $key=>$cow){
			if(!empty($cow_sel[$cow['id']])){
				$dev_msg_find=false;
				foreach($devmsglist as $devmsg){
					if($cow['sn_code']==$devmsg['sn_code']&&$cow_sel[$cow['id']]==$devmsg['s_time']){
						$dev_msg_find=true;
						break;
					}
				}
				if($dev_msg_find==false){
					$msg['s_time']=$cow_sel[$cow['id']];
					$msg['farmer_name']=$farmer_sel[$cow['farmer_id']];
					$msg['phone']=$phone_sel[$cow['farmer_id']];
					$msg['viliage']=$village_sel[$cow['village_id']];
					$msg['town']=$town_sel[$cow['town_id']];
					$msg['sn_code']=$cow['sn_code'];
					$msg_list[]=$msg;
				}
			}
		}

	
		if(count($msg_list)>0){
			echo 'add dev:';
			dump($msg_list);
			$ret = M('devmsg')->addAll($msg_list);
			dump($ret);
		}
		exit;
		//$devmsg=M('devmsg')->where(array('state'=>0))->select();
		//$this->assign('devmsg',$devmsg);
		//$this->display();
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