<?php
namespace Home\Controller;
use Think\Controller;
class BreedController extends Controller {
  public function index(){
       ob_clean();
       echo 'test';
       exit;
  }
  
  public function breedlist(){
		$alarm_b=$_POST['count'];
		if(empty($_POST['time'])||empty($_POST['time2'])){
			  $now = time();
			  $v = strtotime(date('2019-01-01',$now));
			  $time =date('Y-m-d',$v);
  			$time2 =date('Y-m-d',$now);
        //$this->assign('date',$time);
        //$this->assign('date2',$time2);
				//$this->display();
		}else{
		  	$time =  $_POST['time'];
		  	$time2 =  $_POST['time2'];

		}
		
		if($alarm_b){
			$now = time();
		}
		
		//dump($alarm_b);
		$start_time=strtotime($time);
		$end_time=strtotime($time2);
		
		if($alarm_b){
		  $now = time();
		  $v = strtotime(date('Y-m-d',$now))-$alarm_b*86400;
			$end_time=$v;
			$wheretime='bs.breeding_time <='.$end_time;
		}else{
			$wheretime='bs.breeding_time >='.$start_time.' and bs.breeding_time<='.$end_time;
		}
		

		$mode=M();
		$breedlist = $mode->table(array('breedings'=>'bs','cows'=>'cs'))
								->field('cs.sn_code,cs.farmer_id,cs.village_id,bs.breeding_time,bs.admin_id,bs.breeding_tel')
								->where('bs.cows_id = cs.id and '.$wheretime)
								->group('cs.sn_code')
								->order('bs.breeding_time desc')
								->select();
		//dump($mode->getlastsql());
		foreach($breedlist as $key=>$breed){
			$farmers[]=$breed['farmer_id'];
		}

		$farmers=array_unique($farmers);
		$wherefarmers['fs.id']=array('in',$farmers);
		$farmerlist = M()->table(array('farmers'=>'fs'))
								->field('fs.id as id,fs.name as name ,fs.village_id as village_id')
								->where($wherefarmers)
								->select();
					
		foreach($farmerlist as $farmer){
			$farmer_sel[$farmer['id']]=$farmer['name'];
			$villages[]=$farmer['village_id'];
		}
		
		$villages=array_unique($villages);
		$wherevillages['ss.id']=array('in',$villages);
		$villagelist = M()->table(array('subareas'=>'ss'))
								->field('ss.id as id,ss.name as name')
								->where($wherevillages)
								->select();
								
		foreach($villagelist as $village){
			$village_sel[$village['id']]=$village['name'];
		}

	  $today = strtotime(date('Y-m-d',time()));
		foreach($breedlist as $key=>$breed){
			$breedlist[$key]['farmer']=$farmer_sel[$breed['farmer_id']];
			$breedlist[$key]['village']=$village_sel[$breed['village_id']];
			$breedlist[$key]['days']=($today-$breed['breeding_time'])/86400;
		}
		//dump($breedlist);

		$this->assign('breedlist',$breedlist);
    $this->assign('date',$time);
    $this->assign('date2',$time2);
    $this->assign('count',$alarm_b);
		$this->display();
	}
  
}