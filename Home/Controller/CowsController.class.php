<?php
namespace Home\Controller;
use Think\Controller;
use Think\Db;
class CowsController extends Controller {
    public function index(){
			$mode = M('','','DB_CONFIG');
			//$mode = M('');
			$cows=$mode->table('cows')->select();
			$devs=M('device')->select();
			foreach($devs as $dev){
				$rid=substr($dev['rid'],-8);
				$dev_list[$rid]=$dev;
			}
			//dump($dev_list);
			//exit;
			foreach($cows as $cow){
				$sn_code=$cow['sn_code'];
				if(strlen($sn_code)!=9){
					//dump($sn_code);
					$id=$dev_list[$sn_code]['id'];
					$psn=$dev_list[$sn_code]['psn'];
					$devid=$dev_list[$sn_code]['devid'];
					$flag=$dev_list[$sn_code]['flag'];
					if($id!=NULL&&$flag==0){
						//dump($id);
						//dump($psn);
						//dump($devid);
						$ids[]=$id;
					}
					else if($id==NULL){
						//$ids[]=$sn_code;
					}

				}else if(strlen($sn_code)==9){
					$sn=(int)$sn_code.'';
					$id=$dev_list[$sn]['id'];
					$psn=$dev_list[$sn]['psn'];
					$devid=$dev_list[$sn]['devid'];
					$flag=$dev_list[$sn]['flag'];
					if($id!=NULL&&$flag==0){
						dump($id);
						dump($psn);
						dump($devid);
						$ids[]=$id;
					}
				}
			}
			$whereids['id']=['in',$ids];
			//$ret = M('device')->where($whereids)->save(['flag'=>1]);
			//dump($ids);
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
    	$town_id=$_GET['town_id'];
    	$vaccin_id=$_GET['vaccin_id'];
			$doctors=M('doctors')->where(['town_id'=>$town_id])->where('id >= 15')->select();
			$mode=M('','','DB_CONFIG');
			$num=120;
			$vaccin=M('vaccins')->where(['id'=>$vaccin_id])->find();
			$first_time = strtotime($vaccin['time']);
			$days = $vaccin['days'];
			if(count($doctors)<1){
				echo 'DOCTOR NULL.';
				exit;
			}			
			
			if(count($doctors)==1){
				$key = $doctors[0]['name'];
				echo 'name:';
				dump($key);
				dump($town_id);

				$cows=$mode->table('cows')->field('id,farmer_id')->where(['town_id'=>$town_id])->order('farmer_id desc')->select();
				dump(count($cows));
				
				$count=0;
				$index=0;
				
				foreach($cows as $cow){
					$cow_list[]=$cow['id'];
				}

				foreach($cows as $key2=>$cow){
					$farmer_id=$cow['farmer_id'];
					$cow_id=$cow['id'];
					
					$g_count=$mode->table('gives')->where(['cows_id'=>$cow_id])->count();
					
					if($g_count==4&&$vaccin_id==4){
						continue;
					}
					
					if($count>=$num){
						if($farmer_id!=$cows[$key2-1]['farmer_id']){
							//$count=0;
							$index=(int)($count/$num);
						}
					}				
					//$index=(int)($count/$num);
					//dump($count);
					//dump($index);
					$cur_time=$index*86400+$first_time;
					//dump(date('Y-m-d H:i:s',$cur_time));
					$tmp['cows_id']=$cow['id'];
					
					$tmp['give_name']=$key;
				
					$tmp['give_time']=$cur_time;
					$tmp['give_title']=$vaccin['name'];
					$tmp['content']=$vaccin['info'];
					$tmp['created_at']=date('Y-m-d H:i:s',time());
					$tmp['updated_at']=date('Y-m-d H:i:s',time());
					$count+=1;
					
					$gives[]=$tmp;
					dump(date('Y-m-d H:i:s',$cur_time));
					dump($tmp);
				}
				
				//dump($gives);
				//$ret=$mode->table('gives')->addAll($gives);
				//dump($ret);
				exit;
			}
			
			foreach($doctors as $doc){
				$cur_doc[$doc['name']][]=$doc['village_id'];
			}
			
			foreach($cur_doc as $key=>$doc){
				echo 'name:';
				dump($key);
				dump($town_id);
				$whereinvillage['village_id']=array('in',$doc);
				dump($whereinvillage);
				$cows=$mode->table('cows')->field('id,farmer_id')->where(['town_id'=>$town_id])->where($whereinvillage)->order('farmer_id desc')->select();
				dump(count($cows));
				$count=0;
				foreach($cows as $key2=>$cow){
					$farmer_id=$cow['farmer_id'];
					$cow_id=$cow['id'];
					if($count>=$num){
						if($farmer_id!=$cows[$key2-1]['farmer_id']){
							//$count=0;
							$index=(int)($count/$num);
						}
					}				
					//$index=(int)($count/$num);
					//dump($count);
					//dump($index);
					$cur_time=$index*86400+$first_time;
					//dump(date('Y-m-d H:i:s',$cur_time));
					$tmp['cows_id']=$cow['id'];
					
					$tmp['give_name']=$key;
				
					$tmp['give_time']=$cur_time;
					$tmp['give_title']=$vaccin['name'];
					$tmp['content']=$vaccin['info'];
					$tmp['created_at']=date('Y-m-d H:i:s',time());
					$tmp['updated_at']=date('Y-m-d H:i:s',time());
					$count+=1;
					
					$gives[]=$tmp;
					dump(date('Y-m-d H:i:s',$cur_time));
					dump($tmp);
					
				}
			}
			
			//dump($gives);
			//$ret=$mode->table('gives')->addAll($gives);
			//dump($ret);
			exit;
    }
    
    public function updategives(){

    	$old_time=strtotime('2020-02-18 09:00:00');
    	$first_time=strtotime('2020-08-20 09:00:00');
			$mode=M('','','DB_CONFIG');
			$gives=$mode->table('gives')->order('give_time asc')->select();
			$mode=M('','','DB_CONFIG');
			$cows=$mode->table('cows')->select();
			
			$doctors=M('doctors')->select();
			
			foreach($doctors as $doc){
				unset($gl);
				foreach($gives as $giv){
					if($doc['name']==$giv['give_name']){
						$gl[]=$giv;
						if(count($gl)>60){
							$index= (int)(count($gl)/60);
							if($index>0){
								$rel_time = $giv['give_time']+$index*86400;
								echo 'update time:';
								dump($giv['give_time']);
								dump($rel_time);
								//$ret=$mode->table('gives')->where(array('id'=>$giv['id']))->save(array('give_time'=>$rel_time));
							}
						}
					}
				}
				
			}
			exit;
    }
}