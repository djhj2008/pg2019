<?php
namespace Home\Controller;
use Think\Controller;
class DevmanagerController extends Controller {
	public function devlist(){
		$psnid = $_GET['psnid'];
		$uid= $_SESSION['admin_userid'];
		$devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psnid))->where('devid>=400')->order('devid asc')->select();
		//dump($dev);
		if($uid!=100){
			echo "<script type='text/javascript'>alert('Not Admin.');distory.back();</script>";
			exit;
		}
		$sicktype=M('sicktype')->order('type asc')->select();
		$this->assign('sicktype',$sicktype);
		$this->assign('psnid',$psnid);
		$this->assign('devSelect',$devSelect);
		$this->display();
	}

	public function changevalue(){
		$len = $_POST['datatable-buttons_length'];
		$psnid = $_POST['psnid'];
		$uid= $_SESSION['admin_userid'];

		if($uid!=100){
			echo "<script type='text/javascript'>alert('Not Admin.');distory.back();</script>";
			exit;
		}
		$devSelect=M('device')->where(array('dev_type'=>0,'psn'=>$psnid))->where('devid>=400')->order('devid asc')->select();
		
		foreach($devSelect as $dev){
			$id=$dev['id'];
			$sn=$_POST['sn-'.$id];
			$shed=$_POST['shed-'.$id];
			$flag_check=$_POST['flag-'.$id];
			if($flag_check=="on"){
				$flag=1;
			}else{
				$flag=0;
			}
			unset($savedev);
			if(empty($sn)||empty($shed)){
				continue;
			}
			//dump('submit:');
			//dump($sn);
			//dump($shed);
			//dump('local:');
			//dump($dev['sn']);
			//dump($dev['shed']);
			if($dev['sn']!=$sn){
				$savedev['sn']=$sn;
			}
			if($dev['shed']!=$shed){
				$savedev['shed']=$shed;
			}
			if($dev['flag']!=$flag){
				$savedev['flag']=$flag;
			}
			if(!empty($savedev)){
				//dump('save:'.$id);
				//dump($savedev);
				$ret=M('device')->where(array('id'=>$id))->save($savedev);
			}
		}
		$this ->redirect('/Devmanager/devlist',array('psnid'=>$psnid),0,'');
	}

	public function querytemp(){
		if(empty($_POST['time'])){
				if(empty($_GET['time'])){
				  $now = time();
				  $v = strtotime(date('Y-m-d',$now));
				  $time =date('Y-m-d',$v);
	  			$time2 =date('Y-m-d',$now);
				}else{
			  	$time =  $_GET['time'];
			  	$time2 =  $_GET['time2'];
				}

		}else{
		  	$time =  $_POST['time'];
		  	$time2 =  $_POST['time2'];
		}
    {
	  	$psn = $_GET['psnid'];
	  	$id=$_GET['devid'];
			$psnid = $_GET['psnid'];
    	
    	$start_time = strtotime($time);
    	$end_time = strtotime($time)+86400-1;
        $dev=M('device')->where(array('devid'=>$id,'psn'=>$psn))->find();
        if($dev==NULL){
            $date = date("Y-m-d");
            $this->assign('date',$date);
            $this->assign('date2',$date);
            echo "<script type='text/javascript'>alert('DEV NULL.');distory.back();</script>";
            $this->display();
            exit;
        }
        $psn = $dev['psn'];
        $shed = $dev['shed'];
        //var_dump($dev);
        
        if($selectSql=M('access')->group('time')->where('devid ='.$id.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id desc')->select()){
            $this->assign('devid',$id);
            $this->assign('date',$time);
            $this->assign('date2',$time2);
            $this->assign('id',$id);
            $this->assign('selectSql',$selectSql);
            //var_dump($selectSql);
        }else{
        		$date = date("Y-m-d");
	 	 				$this->assign('date',$date);
	 	 				$this->assign('date2',$date);
            echo "<script type='text/javascript'>alert('没有查询到结果.');distory.back();</script>";
        }
    }
    $kind0 =M('sicktype')->where(array('kind'=>0))->order('type asc')->select();
    $kind1 =M('sicktype')->where(array('kind'=>1))->order('type asc')->select();
    $kind2 =M('sicktype')->where(array('kind'=>2))->order('type asc')->select();
    
    $this->assign('kind0',$kind0);
    $this->assign('kind1',$kind1);
    $this->assign('kind2',$kind2);
		$this->assign('psnid',$psnid);
		$this->assign('devid',$id);
		$this->display();
	}
	
	public function edittemp(){
		//dump($_POST);
		$psnid=$_POST['psnid'];
		$devid=$_POST['devid'];
		$time=$_POST['time'];
		$uid= $_SESSION['admin_userid'];
  	$start_time = strtotime($time);
  	$end_time = strtotime($time)+86400-1;
  	
  	$dev=M('device')->where(array('devid'=>$devid,'psn'=>$psnid))->find();
  	
  	if(empty($dev)||$uid!=100)
		{
			echo "<script type='text/javascript'>alert('Not Admin.');distory.back();</script>";
			exit;
		}  	
  	
		$selectSql=M('access')->group('time')->where('devid ='.$devid.' and psn= '.$psnid.' and time >= '.$start_time.' and time <= '.$end_time)->order('id desc')->select();
		
		foreach($selectSql as $acc){
			$id=$acc['id'];
			$cur_time=$acc['time'];
			$state=$_POST['state-'.$id];
			if($state==1){
				$state=$_POST['kind1-'.$id];
			}else if($state==2){
				$state=$_POST['kind2-'.$id];
			}else{
				$state=$_POST['kind0-'.$id];
			}
			$real_temp=$_POST['real_temp-'.$id];
			unset($saveacc);
			if($acc['state']!=$state){
				$saveacc['state']=$state;
			}
			if($acc['real_temp']!=$real_temp){
				$saveacc['real_temp']=$real_temp;
			}
			
			if(!empty($saveacc)){
				//dump('save:');
				//dump(date('Y-m-d H:s:i',$acc['time']));
				//dump($saveacc);
				$ret=M('access')->where(array('id'=>$id))->save($saveacc);
			}
		}
		$lastacc=M('access')->where(array('devid'=>$devid,'psn'=>$psnid))->where('state>0')->order('time desc')->find();
		$lastsate=$lastacc['state'];
		$ret=M('device')->where(array('devid'=>$devid,'psn'=>$psnid))->save(array('state'=>$lastsate));
		
		$this ->redirect('/Devmanager/querytemp',array('psnid'=>$psnid,'devid'=>$devid,'time'=>$time),0,'');
		exit;
	}
}