<?php
namespace Home\Controller;
use Think\Controller;
class AccmanagerController extends Controller {
		public function syncmonthacc(){
			ini_set('memory_limit','2048M');
			$psn=$_GET['psn'];
			$now = time();
			$start_time = strtotime('2020-08-01 00:00:00');
			$end_time = strtotime('2020-09-01 00:00:00');
			
			dump(date('Y-m-d H:i:s',$start_time));
			dump(date('Y-m-d H:i:s',$end_time));
			

			$ret=M('access_base')->where('time>='.$start_time.' and time<'.$end_time)->select();
			if(empty($ret)){
				/*
				$dbs =M()->query('show tables');
				foreach($dbs as $db){
					$table_name = $db['tables_in_pg'];
					if(strpos($table_name,"access_")!==false
						&&strpos($table_name,"old")===false
						&&strpos($table_name,"base")===false){
						dump($table_name);
						dump((int)(memory_get_usage()/(1024*1024)));
						$acclist=M($table_name)
											->field('temp1,temp2,env_temp,env_temp2,delay,sign,rssi1,rssi2,rssi3,rssi4,rssi5,cindex,lcount,time,devid,psn,psnid,sid,cur_time')
											->where('time>='.$start_time.' and time<'.$end_time)->select();
						dump(count($acclist));
						dump((int)(memory_get_usage()/(1024*1024)));
						$ret=M('access_base202008')->addAll($acclist);
						unset($acclist);
					}
				}
				*/
				$table_name = 'access_'.$psn;
				$acclist=M($table_name)
											->field('temp1,temp2,env_temp,env_temp2,delay,sign,rssi1,rssi2,rssi3,rssi4,rssi5,cindex,lcount,time,devid,psn,psnid,sid,cur_time')
											->where('time>='.$start_time.' and time<'.$end_time)->count();
				dump(count($acclist));						
			}
			
			exit;
		}
		
		public function syncnowacc($psn,$now,$db){
			ini_set('memory_limit','512M');
			
			$now = $now.' 00:00:00';
			$start_time = strtotime($now);
			$end_time = $start_time+86400;

			$psninfo = M('psn')->where(array('sn'=>$psn))->find();
			$psnid=$psninfo['id'];
			dump($psnid);
			dump(date('Y-m-d H:i:s',$start_time));
			//dump(date('Y-m-d H:i:s',$end_time));
			if(empty($db)){
				$mydb='access_base';
			}else{
				$mydb='access_base2020'.$db;
			}
			dump($mydb);

			$ret=M($mydb)->where(array('psnid'=>$psnid))->where('time>='.$start_time.' and time<'.$end_time)->select();
			echo 'db count:'.count($ret);
			if(count($ret)===0){
					$table_name = 'access_'.$psn;
					$acclist=M($table_name)
												->field('temp1,temp2,env_temp,delay,sign,rssi1,rssi2,rssi3,cindex,lcount,time,devid,psn,psnid,sid,cur_time')
												->where('time>='.$start_time.' and time<'.$end_time)->select();
					echo 'count:'.count($acclist);
					$als = array_chunk($acclist, 3000, true);
					//dump(date('Y-m-d H:i:s',time()));
					//$ret = M('access_base202008')->addAll($acclist);
					foreach($als as $al){
						unset($tl);
						dump(date('Y-m-d H:i:s',time()));
						foreach($al as $a){
							$tl[]=$a;
						}
						$user = M($mydb);
						$ret =$user->addAll($tl);
						//dump($user->getlastsql());
						dump($ret);
					}
			}
		}
		
		public function startsyncdb(){
			$now = $_GET['time'];
			$db=$_GET['db'];

			$psn_list=array(22,23,30,31,32,33,34,35,36,37,38,39);
			
			foreach($psn_list as $psn){
				$this->syncnowacc($psn,$now,$db);
			}

			
		}
	
}