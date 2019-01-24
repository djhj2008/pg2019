<?php
namespace Home\Controller;
use Tools\HomeController;  
use Think\Controller;
class AcaddController extends HomeController {
    public function add(){
    	//var_dump($_POST);
        if($_POST['aip']=='ios' || $_POST['aip']=='an'){
            if(!is_numeric($_POST['temp'])){
                $jarr=array('ret'=>array('ret_message'=>'temp is number','status_code'=>10000009));
                echo json_encode(array('UserInfo'=>$jarr));
                exit;
            }
            if(is_numeric($_POST['temp'])){
                $postArr=array(
                    'temp1'=>$_POST['temp'],
                    'devid'=>intval($_POST['devid']),
                    'time'=>time()
                );
                //var_dump($postArr);
                if($addSql=M('access')->add($postArr)){
                    $jarr=array('ret'=>array('ret_message'=>'ok','status_code'=>10000000,'data'=>M('access')->where(array('id'=>$addSql))->find()));
                    echo json_encode(array('UserInfo'=>$jarr));
                    exit;
                }else{
                    echo 'error'.$addSql;
                    exit;
                } 
            }
            exit;
        }
        if(is_numeric($_POST['temp'])){
            $postArr=array(
                'temp1'=>$_POST['temp'],
                'devid'=>intval($_POST['devid']),
                'time'=>time(),
            );
            //var_dump($postArr);
            if($addSql=M('access')->add($postArr)){
                echo "<script type='text/javascript'>alert('插入成功');distory.back();</script>";
            }else{
                echo "<script type='text/javascript'>alert('插入失败');distory.back();</script>";
            } 
        }
    	
       $this->display();
    }

    public function save(){
        if($_POST['aip']=='ios' || $_POST['aip']=='an'){
            if(!is_numeric($_POST['temp']) || !is_numeric($_POST['devid']) ){
                 $jarr=array('ret'=>array('ret_message'=>'devid temp is number','status_code'=>10000009));
                    echo json_encode(array('UserInfo'=>$jarr));
                    exit;
            }
            $postArr=array(
            'temp1'=>$_POST['temp'],
            //'time'=>time(),
            );
            $idSql=M('access')->where(array('devid'=>intval($_POST['devid'])))->order('id desc')->find();
            $id=$idSql['id'];

            if($saveSql=M('access')->where(array('id'=>$id))->save($postArr)){
                $jarr=array('ret'=>array('ret_message'=>'ok','status_code'=>10000002,'data'=>M('access')->where(array('devid'=>intval($_POST['devid'])))->order('id desc')->find()));
                    echo json_encode(array('UserInfo'=>$jarr));
                    exit;
                
            }else{
                $jarr=array('ret'=>array('ret_message'=>'error','status_code'=>10000003));
                    echo json_encode(array('UserInfo'=>$jarr));
            }
            exit;
        }
        if($_POST){
            if(!is_numeric($_POST['temp']) || !is_numeric($_POST['devid']) ){
                
                echo "<script type='text/javascript'>alert('devid temp is number');distory.back();</script>";
                $this->display();
                    exit;
            }
            $postArr=array(
            'temp1'=>$_POST['temp'],
            //'time'=>time(),
            );
            $idSql=M('access')->where(array('devid'=>intval($_POST['devid'])))->order('id desc')->find();
            $id=$idSql['id'];

            if($saveSql=M('access')->where(array('id'=>$id))->save($postArr)){
               echo "<script type='text/javascript'>alert('ok');distory.back();</script>";
                $this->display();
                    exit;
                
            }else{
                echo "<script type='text/javascript'>alert('error');distory.back();</script>";
                $this->display();
                    exit;
            }
            exit;
        }
    	
       $this->display();

    }

    public function delete(){
        if($_POST['aip']=='ios' || $_POST['aip']=='an'){
            if(!is_numeric($_POST['devid'])){
                $jarr=array('ret'=>array('ret_message'=>'devid is number','status_code'=>10000009));
                echo json_encode(array('UserInfo'=>$jarr));
                exit;
            }
            if(!empty($_POST['devid']) && is_numeric($_POST['devid'])){
                $postArr=array(
                    'devid'=>intval($_POST['devid'])
                );
                $idSql=M('access')->where($postArr)->field('id')->find();
                $id=$idSql['id'];
                //var_dump($postArr);
                if($delSql=M('access')->where(array('id'=>$id))->delete()){
                    $jarr=array('ret'=>array('ret_message'=>'ok','status_code'=>10000004));
                    echo json_encode(array('UserInfo'=>$jarr));
                }else{
                    $jarr=array('ret'=>array('ret_message'=>'error','status_code'=>10000005));
                    echo json_encode(array('UserInfo'=>$jarr));
                } 
            }
            exit;
        }
        if(!empty($_POST['devid']) && is_numeric($_POST['devid'])){
            $postArr=array(
                'devid'=>intval($_POST['devid'])
                
            );
            //var_dump($postArr);
            if($delSql=M('access')->where($postArr)->delete()){
                echo "<script type='text/javascript'>alert('删除成功');distory.back();</script>";
            }else{
                echo "<script type='text/javascript'>alert('删除失败');distory.back();</script>";
            } 
        }
    	
       $this->display();

    }

    public function select(){
        if($_POST){
        	$id = intval($_POST['devid']);
        	$time =  $_POST['time'];
        	$time2 =  $_POST['time2'];
        	$psn = $_GET['psnid'];
        	
        	$start_time = strtotime($time);
        	$end_time = strtotime($time2)+86400;
            $dev=M('device')->where(array('devid'=>$id,'psn'=>$psn))->find();
            if($dev==NULL){
                $date = date("Y-m-d");
                $this->assign('date',$date);
                $this->assign('date2',$date);
                echo "<script type='text/javascript'>alert('设备不存在.');distory.back();</script>";
                $this->display();
                exit;
            }
            $psn = $dev['psn'];
            $shed = $dev['shed'];
            //var_dump($dev);

            $devSelect=M('device')->where(array('flag'=>1,'dev_type'=>1,'psn'=>$psn))->find();
            if($devSelect!=NULL){
                $devid=$devSelect['devid'];
                //var_dump($devid);
            }
            
            $devSelect2=M('device')->where(array('flag'=>1,'dev_type'=>2,'psn'=>$psn))->find();
            if($devSelect2!=NULL){
                $devid2=$devSelect2['devid'];
                //var_dump($devid2);
            }

            $devSelect3=M('device')->where(array('flag'=>1,'dev_type'=>3,'psn'=>$psn))->find();
            if($devSelect3!=NULL){
                $devid3=$devSelect3['devid'];
                //var_dump($devid3);
            }
            
            if($devid==NULL){
                if($selectSql=M('access')->where('devid ='.$id.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id desc')->select()){
                    $this->assign('devid',$id);
                    $this->assign('date',$time);
                    $this->assign('date2',$time2);
                    $this->assign('id',$id);
                    $this->assign('selectSql',$selectSql);
                }else{
                    $date = date("Y-m-d");
                    $this->assign('date',$date);
                    $this->assign('date2',$date);
                     echo "<script type='text/javascript'>alert('没有查询到结果.');distory.back();</script>"; 
                }
                $this->display();
                exit;
            }
            $tmpSql=M('taccess')->where('devid ='.$devid.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id asc')->select();

						if($devid2!=NULL){
            	$tmpSql2=M('taccess')->where('devid ='.$devid2.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id asc')->select();
							//var_dump($tmpSql2);
						}
						
						if($devid3!=NULL){
            	$tmpSql3=M('taccess')->where('devid ='.$devid3.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id asc')->select();
							//var_dump($tmpSql3);
						}
						
            if($selectSql=M('access')->where('devid ='.$id.' and psn= '.$psn.' and time >= '.$start_time.' and time <= '.$end_time)->order('id desc')->select()){
                $this->assign('devid',$id);
                $this->assign('date',$time);
                $this->assign('date2',$time2);
                $this->assign('id',$id);
                for($i=0;$i<count($selectSql);$i++){
                    if($tmpSql!=NULL){
                    		$max=count($tmpSql)-1;
                        for($j=0;$j<count($tmpSql);$j++){
                            if($selectSql[$i]['time']==$tmpSql[$j]['time']){
                                $selectSql[$i]['env_temp1']=number_format($tmpSql[$j]['temp1'],2);
                                $selectSql[$i]['env_temp2']=number_format($tmpSql[$j]['temp2'],2);
                                break;
                            }
                            else if($selectSql[$i]['time'] > $tmpSql[$j]['time']){
                                $selectSql[$i]['env_temp1']=number_format($tmpSql[$max]['temp1'],2);
                                $selectSql[$i]['env_temp2']=number_format($tmpSql[$max]['temp2'],2);
                            }
                            else{
                            	  $selectSql[$i]['env_temp1']=255; 
                        				$selectSql[$i]['env_temp2']=255;
                            }                          
                        }
                    }else{
                        $selectSql[$i]['env_temp1']=255; 
                        $selectSql[$i]['env_temp2']=255;
                    }
                    if($tmpSql2!=NULL){
                    		$max=count($tmpSql2)-1;
                        for($j=0;$j<count($tmpSql2);$j++){
                            if($selectSql[$i]['time']==$tmpSql2[$j]['time']){
                                $selectSql[$i]['env_temp3']=number_format($tmpSql2[$j]['temp1'],2);
                                $selectSql[$i]['env_temp4']=number_format($tmpSql2[$j]['temp2'],2);
                                break;
                            }
                            else if($selectSql[$i]['time'] > $tmpSql2[$j]['time']){
                                $selectSql[$i]['env_temp3']=number_format($tmpSql2[$max]['temp1'],2);
                                $selectSql[$i]['env_temp4']=number_format($tmpSql2[$max]['temp2'],2);
                            }
                            else{
                            	  $selectSql[$i]['env_temp3']=255; 
                        				$selectSql[$i]['env_temp4']=255;
                            }   
                        }
                    }else{
                        $selectSql[$i]['env_temp3']=255; 
                        $selectSql[$i]['env_temp4']=255;
                    } 
                    if($tmpSql3!=NULL){
                    		$max=count($tmpSql3)-1;
                        for($j=0;$j<count($tmpSql3);$j++){
                            if($selectSql[$i]['time']==$tmpSql3[$j]['time']){
                                $selectSql[$i]['env_temp5']=number_format($tmpSql3[$j]['temp1'],2);
                                $selectSql[$i]['env_temp6']=number_format($tmpSql3[$j]['temp2'],2);
                                break;
                            }
                            else if($selectSql[$i]['time'] > $tmpSql3[$j]['time']){
                                $selectSql[$i]['env_temp5']=number_format($tmpSql3[$max]['temp1'],2);
                                $selectSql[$i]['env_temp6']=number_format($tmpSql3[$max]['temp2'],2);
                            }
                            else{
                            	  $selectSql[$i]['env_temp5']=255; 
                        				$selectSql[$i]['env_temp6']=255;
                            }   
                        }
                    }else{
                        $selectSql[$i]['env_temp5']=255; 
                        $selectSql[$i]['env_temp6']=255;
                    }                        
                                       
                }

                $this->assign('selectSql',$selectSql);
                //var_dump($selectSql);

            }else{
            		$date = date("Y-m-d");
    	 	 				$this->assign('date',$date);
    	 	 				$this->assign('date2',$date);
                echo "<script type='text/javascript'>alert('没有查询到结果.');distory.back();</script>";
            }
        }else{
        	$date = date("Y-m-d");
    	 	 	$this->assign('date',$date);
    	 	 	$this->assign('date2',$date);
    	  }
       $this->display();

    }
    
    public function checkrssi(){
        if($_POST){
	        $time =  $_POST['time'];
	        $time2 =  $_POST['time2'];
	        $psn = $_SESSION['psn'];
	        	
	        $start_time = strtotime($time);
	        $end_time = strtotime($time2)+86400;
	        $dev=M('brssi')->where('time >= '.$start_time.' and time <= '.$end_time)->order('time desc')->select();
	        if($dev==NULL){
        		$date = date("Y-m-d");
	 	 				$this->assign('date',$date);
	 	 				$this->assign('date2',$date);
            echo "<script type='text/javascript'>alert('没有查询到结果.');distory.back();</script>";
	          $this->display();
	          exit;
	        }else{
						$this->assign('brssi',$dev);
            $this->assign('date',$time);
            $this->assign('date2',$time2);
	    	  }
	    	}else{
	    			$date = date("Y-m-d");
	    	 	 	$this->assign('date',$date);
	    	 	 	$this->assign('date2',$date);
	    	}
       $this->display();

    }
}