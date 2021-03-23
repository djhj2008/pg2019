<?php
namespace Home\Controller;
use Think\Controller;
class AccmanagerController extends Controller {
		public function syncmonthacc(){
			ini_set('memory_limit','4096M');
			$psn=$_GET['psn'];
			$now = time();
			$start_time = strtotime('2020-11-01 00:00:00');
			$end_time = strtotime('2020-12-01 00:00:00');
			
			dump(date('Y-m-d H:i:s',$start_time));
			dump(date('Y-m-d H:i:s',$end_time));
			

			$ret=M('access_base')->where('time>='.$start_time.' and time<'.$end_time)->select();
			if(empty($ret)){
			
			}
			
			exit;
		}
		
		public function syncnowacc($now,$db){
			ini_set('memory_limit','2048M');
			
			$now = $now.' 00:00:00';
			$start_time = strtotime($now);
			$end_time = $start_time+86400;

			dump(date('Y-m-d H:i:s',$start_time));
			//dump(date('Y-m-d H:i:s',$end_time));

			$mydb='access_base2020'.$db;
			dump($mydb);
			$ret=M($mydb)->where('time>='.$start_time.' and time<'.$end_time)->select();
			echo 'db count:'.count($ret);
			echo '<br>';
			if(count($ret)===0){
					$table_name = 'access_base';
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

		public function syncnowacc2($now,$db,$db2){
			ini_set('memory_limit','2048M');
			
			$now = $now.' 00:00:00';
			$start_time = strtotime($now);
			$end_time = $start_time+86400;

			dump(date('Y-m-d H:i:s',$start_time));
			//dump(date('Y-m-d H:i:s',$end_time));

			$mydb='access_daily'.$db;
			dump($mydb);
			$ret=M($mydb)->where('time>='.$start_time.' and time<'.$end_time)->select();
			echo 'db count:'.count($ret);
			echo '<br>';
			if(count($ret)===0){
					$table_name = 'access_base'.$db2;
					dump($table_name);
					$acclist=M($table_name)
												->field('temp1,temp2,env_temp,delay,sign,rssi1,rssi2,rssi3,cindex,lcount,time,devid,psn,psnid,sid,cur_time')
												->where('time>='.$start_time.' and time<'.$end_time)->select();
					echo 'count:'.count($acclist);
					$als = array_chunk($acclist, 3000, true);
					//dump(date('Y-m-d H:i:s',time()));
					//$ret = M('access_base202008')->addAll($acclist);
					//dump($table_name);
					foreach($als as $al){
						unset($tl);
						//dump(date('Y-m-d H:i:s',time()));
						foreach($al as $a){
							$tl[]=$a;
						}
						$user = M($mydb);
						$ret =$user->addAll($tl);
						//dump($user->getlastsql());
						//dump($ret);
					}
			}
		}
				
		public function startsyncdb(){
			$now = $_GET['time'];
			$db=$_GET['db'];
			$db2=$_GET['db2'];
			$day = $_GET['day'];
			$now = '2021'.$now.str_pad($day,2,'0',STR_PAD_LEFT);
			//dump($now);
			//exit;
			//$this->syncnowacc($now,$db);
			if(empty($now)||empty($db)||empty($db2)||empty($day)){
				echo 'NULL';
				//exit;
			}
			$this->syncnowacc2($now,$db,$db2);
		}
	
    
    public function djtest(){
    	$id=(int)$_GET['id'];
    	$mode=M('','','DB_CONFIG');
			$cows=$mode->table('cows')->limit($id,1)->order('id desc')->select();

			foreach($cows as $cow){
				$sn= $cow['sn_code'];
				$this->outExcelRecharge($sn);
				dump($sn);
			}
    	
    }
    
    
    
    public function outExcelRecharge($sn) {   
    	$fym='2640423';
    	//$sn=$_GET['sn'];
      $rid=(int)$sn;
      if($rid < 300030){
      	$rid=$fym.str_pad($rid,8,'0',STR_PAD_LEFT);
      }

      $table_name = 'access_base';
      $dev=M('device')->where(['rid'=>$rid])->find();
      
      if($dev==NULL){
      	echo 'DEV NULL.';
	      dump($sn);
	      exit;
      }else{
	      $psn= $dev['psn'];
	      $devid= $dev['devid'];
	      
		    if($rid >= 300030){
	      	$rfid=str_pad($rid,8,'0',STR_PAD_LEFT);
	      }else{
	      	$rfid=substr($rid,-8);
	      }

				$acclist=M('access_base')
					->field('psn,devid,temp1,temp2,env_temp,rssi1,rssi2,rssi3,time')
					->where(['psn'=>$psn,'devid'=>$devid])->group('time')->order('time asc')->select();

				if(count($acclist)==0){
		      echo 'ACC NULL.';
		      dump($sn);
		      exit;
				}else{
					foreach($acclist as $key=>$acc){
			      	$temp1=$acc['temp1'];
			      	$temp2=$acc['temp2'];
			      	//dump($acc['time']);
			      	$cur_time=$acc['time'];
							if($avg>0){
								$a=array($temp1,$temp2);
								$t=max($a);
								$vt=(float)$t;
								if($vt < 32){
									if($ntemp>32){
										if($dev['cow_state']==5){
											$ntemp=$vt;
										}else{
											$ntemp=$ntemp;
										}
									}else{
										$ntemp=$vt;
									}
								}else{
									$ntemp= round($btemp+($vt-$avg)*$temp_value,2);
								}
							}else{
								$a=array($temp1,$temp2);
								$t=max($a);
								$vt=(float)$t;
								$ntemp=$vt;
							}
							
							$acclist[$key]['temp1']=$ntemp;
							//$acclist[$key]['step']=$acc['rssi2'];
							//$acclist[$key]['step']=200+$key;
							if($key<count($acclist)-1){
								$step = (int)$acc['rssi2'];
								$next_time = (int)$acclist[$key+1]['time'];
								if($next_time-$cur_time==3600){
									$next_step = (int)$acclist[$key+1]['rssi2'];
			
									if($next_step-$step>=0){
										$cur_step = $next_step-$step;
									}else{
										if(($acc['rssi3']&0x03)==0x01){
											$cur_step=0;
										}else{
											//$cur_step = $next_step-$step;
											$cur_step=65535+$next_step-$step;
										}
										if($next_step==0){
											$cur_step=0;
										}
									}
									$acclist[$key]['step']=$cur_step;
								}else{
									$acclist[$key]['step']=0;
								}
							}else{

							}
							$acclist[$key]['rid']=$rfid;
							$acclist[$key]['cur_time']=date('Y-m-d H:i:s',$acc['time']);
					}

	        $field = array(
	            'A' => array('psn', 'Factory ID'),
	            'B' => array('devid', 'Device ID'),
	            'C' => array('temp1', 'Value1'),
	            'D' => array('temp2', 'Value2'),
	            'E' => array('env_temp', 'Value3'),
	            'F' => array('rssi1', 'BUF1'),
	            'G' => array('rssi2', 'BUF2'),
	            'H' => array('rssi3', 'BUF3'),
	            'I' => array('time', 'Time'),
	            'J' => array('step', 'Step'),
	            'K' => array('cur_time', 'Time'),
	            'L' => array('rid', 'RFID'),
	        );
	        $this->phpExcelList($field, $acclist, $rfid);
	        echo 'Download:';
	        dump($rfid);
				}
      }
    }

		/**
		* 直接导出需要生产的内容
		* @param $field
		* @param $list
		* @param string $title
		* @throws \PHPExcel_Exception
		* @throws \PHPExcel_Writer_Exception
		*/
		function phpExcelList($field, $list, $title='文件')
		{

		  //$objPHPExcel = new Org\PHPExcel\PHPExcel();
		  //$objWriter = new Org\PHPExcel\PHPExcel_Writer_Excel5($objPHPExcel); //设置保存版本格式
		  
		  
		  import("Org.Util.PHPExcel");
		  import("Org.Util.PHPExcel.Worksheet.Drawing");
		  import("Org.Util.PHPExcel.Writer.Excel2007");
		  
		  $objPHPExcel = new \PHPExcel();
		  $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
		  
		  foreach ($list as $key => $value) {
		      foreach ($field as $k => $v) {
		          if ($key == 0) {
		              $objPHPExcel->getActiveSheet()->setCellValue($k . '1', $v[1]);
		          }
		          $i = $key + 2; //表格是从2开始的
		          $objPHPExcel->getActiveSheet()->setCellValue($k . $i, $value[$v[0]]);
		      }
		  }
		  header("Pragma: public");
		  header("Expires: 0");
		  header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		  header("Content-Type:application/force-download");
		  header("Content-Type:application/vnd.ms-execl");
		  header("Content-Type:application/octet-stream");
		  header("Content-Type:application/download");;
		  header('Content-Disposition:attachment;filename='.$title.'.xls');
		  header("Content-Transfer-Encoding:binary");
		  $objWriter->save('php://output');
		}
  
}