<?php
namespace Home\Controller;
use Think\Controller;
use Think\Db;
class CowsController extends Controller {
    public function index(){
			//$mode=M('','','DB_CONFIG');
			$mode = M('');
			$psn=33;
			$mydb='access_33';
			$a = $mode->table($mydb.' a1')->field('temp1,temp2,env_temp,time,psn,psnid,devid')->where(['psn'=>$psn,'devid'=>70])->buildSql();
			$mydb='access_34';
			$b = $mode->table($mydb.' b1')->field('temp1,temp2,env_temp,time,psn,psnid,devid')->where(['psn'=>$psn,'devid'=>70])->buildSql();
			$mydb='access_35';
		  	$c = $mode->table($mydb.' c1')->field('temp1,temp2,env_temp,time,psn,psnid,devid')->where(['psn'=>$psn,'devid'=>70])->union([$a,$b])->buildSql();
			dump($a);
			dump($b);
			dump($c);
			$mydb='access_35';
			$list = $mode->table($c. ' d')->field('temp1,temp2,env_temp,time,psn,psnid,devid')->order('time desc')->select();
			dump($list);
			exit;
    }

    public function getdevoff(){
			$mode=M('','','DB_CONFIG');
			$cows=$mode->table('cows')->where('survival_state!=1 or health_state!=1')->select();

			$devs=M('device')->where(['flag'=>4])->select();

			foreach($devs as $dev){
				$rid=(int)$dev['rid'];
				foreach($cows as $cow){
					$sn=(int)$cow['sn_code'];
					if($sn==$rid){
						dump($cow);
						break;
					}
				}
			}

			//dump($list);
			exit;
    }
    
    public function addgives(){
    	$town_id=85;
    	$village_id=86;//298/322
    	$doctor_id=0;
    	$first_time=strtotime('2020-02-18 09:00:00');
    	$num=80;
			$mode=M('','','DB_CONFIG');
			$cows=$mode->table('cows')->where(['town_id'=>$town_id,'village_id'=>$village_id])->order('farmer_id desc')->select();
			$doctors=M('doctors')->where(['town_id'=>$town_id])->select();
			$doctor=$doctors[$doctor_id];
			//$doctor2=$doctors[$doctor_id+1];
			dump($doctor);
			dump($doctor2);
			$vaccin=M('vaccins')->find();
			$count=0;
			foreach($cows as $key=>$cow){
				$farmer_id=$cow['farmer_id'];
				$cow_id=$cow['id'];
				if($count>=$num){
					if($farmer_id!=$cows[$key-1]['farmer_id']){
						$count=0;
					}
				}				
				$index=(int)($count/60);
				//dump($index);
				$cur_time=$index*86400+$first_time;

				$tmp['cows_id']=$cow['id'];
				if($index==0){
					$tmp['give_name']=$doctor['name'];
				}else{
					//$tmp['give_name']=$doctor2['name'];
				}
				$tmp['give_time']=$cur_time;
				$tmp['give_title']=$vaccin['name'];
				$tmp['content']=$vaccin['info'];
				$tmp['created_at']=date('Y-m-d H:i:s',time());
				$tmp['updated_at']=date('Y-m-d H:i:s',time());
				$count+=1;
				
				$gives[]=$tmp;
				//dump(date('Y-m-d H:i:s',$cur_time));
				//dump($tmp);
				
			}
			dump($gives);
			//$ret=$mode->table('gives')->addAll($gives);
			//$farmer_list=array_unique($farmer_list);

			//dump($farmer_list);
			//dump($cow_list);
			exit;
    }
}