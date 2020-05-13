<?php
namespace Home\Controller;
use Think\Controller;
class CowController extends Controller {
  public function index(){
       ob_clean();
       echo 'test';
       exit;
  }
  
  public function getdev(){
 
	  $now = time();
	  $v = strtotime(date('Y-m-d',$now))-1*86400;
		$end_time=$v;
		$wheretime='time >='.$end_time;

		$mode=M();
		$cowlist = $mode->table(array('cows'=>'cs'))
								->field('cs.sn_code,cs.farmer_id')
								->where('cs.farmer_id = 1005')
								->select();
		//dump($mode->getlastsql());
		//dump($cowlist);
		foreach($cowlist as $key=>$cow){
			$dev_sn[]=(int)$cow['sn_code'];
		}
		//dump($dev_sn);
		
		$whererid['rid']=array('in',$dev_sn);
		$devlist = M('device','','DB_CONFIG')->where($whererid)->select();
		
		foreach($devlist as $dev){
			if($dev['flag']==1&&$dev['cow_state']==0){
				$devid[]=$dev['devid'];
			}	
		}
		
		$wheredev['devid']=array('in',$devid);
		$mydb='access_32';
		$acclist=M($mydb,'','DB_CONFIG')->where(array('psn'=>32))->where($wheredev)->where($wheretime)->select();
		
		
		foreach($devid as $id){
			$sid_flag=false;
			foreach($acclist as $acc){
				if($acc['devid']==$id){
					$sid_flag=true;
					if($acc['sid']>2){
						$sid_flag=false;
						dump($acc);
						break;
					}
				}
			}
			if($sid_flag==false){
				$sn='00032'.str_pad($id,4,'0',STR_PAD_LEFT);
				dump($sn);
			}

		}
		

		//dump($acclist);
		exit;
	}
  
}