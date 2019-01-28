<?php
namespace Home\Controller;
use Tools\HomeController; 
use Think\Controller;
class ValueController extends HomeController {

	public function checkvsum(){
		
		$CRC_LEN  = 4;//校验码
		
		$str="323031393031323531313034303032313038363735363330300000200301500000000000000000003e000020026621048202622116619115598115ffffffffffffffffff000020035121048202621116620116589115ffffffffffffffffff000020045021048202600116608115588115ffffffffffffffffff000020054e21048202599115607115578115ffffffffffffffffff000020085121040102611116608115587115ffffffffffffffffff0000200b4e21040002591116597115587115ffffffffffffffffff0000200c7d21140102611116609115598115ffffffffffffffffff0000202b5621048202001513380150273113ffffffffffffffffff000020a85921048202005a134201a0317113ffffffffffffffffff000020e55721048202444114450114400114ffffffffffffffffff000020e65521048202508114465114441114ffffffffffffffffff000020e74921048202166111173111144111ffffffffffffffffff000020e84e21048202433114422114421114ffffffffffffffffff000020e94d21048102402114439113401114ffffffffffffffffff000020ea5b21048202446114440114420114ffffffffffffffffff000020ec5521048202454114461114401114ffffffffffffffffff000020ed5821048202477114453114431114ffffffffffffffffff000020ee5821048202478114442114430114ffffffffffffffffff000020ef5721048202477114482114433114ffffffffffffffffff000020f05721048202466114452114421114ffffffffffffffffff000020f15721048202488114454114440114ffffffffffffffffff000020f25721048202466114452114411114ffffffffffffffffff000020f35521048202477114483114434114ffffffffffffffffff000020f45821048202467114492114425114ffffffffffffffffff000020f55721048102468114472114433114ffffffffffffffffff000020f65521048102454114440114400114ffffffffffffffffff000020f75521048202456114470114413114ffffffffffffffffff000020f85821048202444114469113402114ffffffffffffffffff000020f95821048102475114442114409113ffffffffffffffffff000020fa5721048202455114470114402114ffffffffffffffffff000020fb5721048102423114438113398113ffffffffffffffffff000020fc5921048202423114428113387113ffffffffffffffffff000020fd5721048102409113435113358113ffffffffffffffffff000020fe5821040002399113415113357113ffffffffffffffffff000020ff5c21048202464113442114300114ffffffffffffffffff000021005621048202456114461114412114ffffffffffffffffff000021015921048202508114466114442114ffffffffffffffffff000021025a21040002490115505114456114ffffffffffffffffff000021035921040102510115477114463114ffffffffffffffffff000021045821048202489114483114454114ffffffffffffffffff000021055821048202501115496114475114ffffffffffffffffff0000210658210482035441155411155101154661144601ffffffff000021075721048202480115494114465114ffffffffffffffffff000021095721048102490115505114465114ffffffffffffffffff0000210a5821048102509114486114454114ffffffffffffffffff0000210b5721048202482114474114382114ffffffffffffffffff0000210d5821048202477114462114422114ffffffffffffffffff0000210e5821048202476114483114424114ffffffffffffffffff0000210f5621048202478114473114433114ffffffffffffffffff000021105821048202477114463114421114ffffffffffffffffff000021115721048202453114470114382114ffffffffffffffffff000021125821048202432114428113387113ffffffffffffffffff000021135921048202410114427113367113ffffffffffffffffff000021145521048202419113407113356113ffffffffffffffffff000021155921048202454114431114408113ffffffffffffffffff000021165721048102486114473114423114ffffffffffffffffff000021175b21048202712227740226615226ffffffffffffffffff000021185121040002721227772226616226ffffffffffffffffff000021195021048202853228873227725227ffffffffffffffffff0000211a5621048202744227734226633226ffffffffffffffffff0000211b5e21048202818234880227814227ffffffffffffffffff0000211c6621048202598115587115567115ffffffffffffffffff0000018d";
	  $len=strlen($str);
    $crc=substr($str,$len-$CRC_LEN*2);//收到发来的crc
    var_dump($crc);
    $crc=hexdec($crc);
    var_dump($crc);
	
    $sum=0;
    $len = strlen($str);
		for($i=0 ; $i < $len/2-$CRC_LEN;$i++)
		{
			$value = hexdec(substr($str, $i*2,2));
			//var_dump($value);
			$sum+=$value;
		}	
		$sum=$sum&0xffff;
		var_dump(dechex($sum));
		exit;
	}


	public function value(){
		//$_POST['devid']=0;
		if(isset($_POST['devid']) && ($_POST['aip']=='ios' || $_POST['aip']=='an')){
			
		for($i=0;$i<8;$i++){
			//前七天范围,到过去第八天
			if($i==0){
				//当前时间戳
				${'beginday'.$i}=mktime(0,0,0,date('m'),date('d'),date('Y'));
				${'endday'.$i}	=time();
			}else{
				${'beginday'.$i}=mktime(0,0,0,date('m'),date('d')-$i,date('Y'));
				${'endday'.$i}	=mktime(23,59,59,date('m'),date('d')-$i,date('Y'));
			}
			
			//前七周范围,到过去第八周
			${'beginweek'.$i}=mktime(0,0,0,date('m'),date('d')-date('w')+1-7*$i,date('Y'));
			${'endweek'.$i}	 =mktime(23,59,59,date('m'),date('d')-date('w')+7-7*$i,date('Y'));
			//前七个月范围,到过去第八个月
			${'beginmonth'.$i}=mktime(0,0,0,date('m')-$i,1,date('Y'));
			if($i==0){//本月
			 	${'endmonth'.$i}	 =mktime(23,59,59,date('m'),date('t'),date('Y'));
			}else{
				${'endmonth'.$i}=mktime(0,0,0,date('m')-$i+1,1,date('Y'));
			}
			${'beginyear'.$i}=mktime(0,0,0,date('1'),date('1'),date('Y')-$i);
			${'endyear'.$i}	 =mktime(23,59,59,date('12'),date('31'),date('Y')-$i);

			

			$str="devid = ".$_POST['devid']." and time BETWEEN ".${'beginday'.$i}." AND ".${'endday'.$i};
			${'accessday'.$i}=M('access')->where($str)->select();



			//echo $str="devid = ".$_POST['devid']." and time BETWEEN ".${'beginday'.$i}." AND ".${'endday'.$i};
			$str1="devid = ".$_POST['devid']." and time BETWEEN ".${'beginweek'.$i}." AND ".${'endweek'.$i};

			${'accessweek'.$i}=D('access')->where($str1)->order('id desc')->select();





			$str2="devid = ".$_POST['devid']." and time BETWEEN ".${'beginmonth'.$i}." AND ".${'endmonth'.$i};

			${'accessmonth'.$i}=D('access')->where($str2)->order('id desc')->select();





			$str3="devid = ".$_POST['devid']." and time BETWEEN ".${'beginyear'.$i}." AND ".${'endyear'.$i};

			${'accessyear'.$i}=D('access')->where($str3)->order('id desc')->select();



			
	
		}



		$jarr=array('ret'=>array("ret_message"=>'ok','status_code'=>10000400,
										'data'=>array(
                                                '7day'=>array( array('day'=>$accessday0),
    		                                                   array('day'=>$accessday1),
    		                                                   array('day'=>$accessday2),
    		                                                   array('day'=>$accessday3),
    		                                                   array('day'=>$accessday4),
    		                                                   array('day'=>$accessday5),
    		                                                   array('day'=>$accessday6),
    		                                                   array('day'=>$accessday7)
		                                                             ),
			                                       '7week'=>array(array('week'=>$accessweek0),
			                                                      array('week'=>$accessweek1),
			                                                      array('week'=>$accessweek2),
			                                                      array('week'=>$accessweek3),
			                                                      array('week'=>$accessweek4),
			                                                      array('week'=>$accessweek5),
			                                                      array('week'=>$accessweek6),
			                                                      array('week'=>$accessweek7),
			                                                      ),
		                                        
		                                        '7month'=>array(array('month'=>$accessmonth0),
														        array('month'=>$accessmonth1),
														        array('month'=>$accessmonth2),
														        array('month'=>$accessmonth3),
														        array('month'=>$accessmonth4),
														        array('month'=>$accessmonth5),
														        array('month'=>$accessmonth6),
														        array('month'=>$accessmonth7),
														        ),
		                                        /*'7year'=>array(array('year'=>$accessyear0),
														       array('year'=>$accessyear1),
														       array('year'=>$accessyear2),
														       array('year'=>$accessyear3),
														       array('year'=>$accessyear4),
														       array('year'=>$accessyear5),
														       array('year'=>$accessyear6),
														       array('year'=>$accessyear7),
														       ),*/
		                                )
			    				)
                );
		echo json_encode(array('UserInfo'=>$jarr));exit;
		}
		


	}

	public function temp(){
		$_POST['devid']=$_POST['devid']?$_POST['devid']:$_GET['devid'];
		$psn = $_SESSION['psn'];
		//$ajarr=M('access')->where(array('devid'=>(int)$_POST['devid']))->field('temp1,time')->select();
		$time   =date('Y,m,d,H,i,s',mktime(0,0,0,date('m'),date('d'),date('Y')));
		for($i=0;$i<144;$i++){
			$start_time = mktime(0,0,0,date('m'),date('d'),date('Y'));
			//var_dump($i);
			${'beginday'.$i}=$start_time+$i*(60*10)*6;
			${'endday'.$i}=$start_time+($i+1)*(60*10)*6;
			//var_dump(${'beginday'.$i});
			//var_dump(${'endday'.$i});
			$str="devid = ".$_POST['devid']." and psn=".$psn." and time BETWEEN ".${'beginday'.$i}." AND ".${'endday'.$i};
			${'accessday'.$i}=M('access')->where($str)->field('temp1,temp2,time,cur_time')->limit(0,1)->select();
			if(count(${'accessday'.$i})>0){
				$c_temp1 = ${'accessday'.$i};
				//var_dump($c_temp1);
				//var_dump($c_temp1[0]['temp1']);
				$temp1=(float)$c_temp1[0]['temp1'];
				$temp2=(float)$c_temp1[0]['temp2'];
			}else{
				$temp1=NULL;
				$temp2=NULL;
			}
			//var_dump($temp1);
			$temp1_1[]=$temp1;
			$temp2_1[]=$temp2;
	  }
	  //exit;
		$time   =date('Y,m,d,H,i,s',mktime(0,0,0,date('m'),date('d'),date('Y')));
		//var_dump($time);
			
    //foreach($ajarr as $value){
    	
    	//$temp1_1[]=(int)$value['temp1'];
    	//$time   =date('Y,m,d,H,i,s',$value['time']);
    	//$time1[]=$time;
    	//var_dump($temp1_1);
    //}
    $time=explode(',', $time);
    //var_dump($time);
    foreach($time as $value){
    	$time1[]=(int)$value;
    }
		$temp1_1=json_encode($temp1_1);
		$temp2_1=json_encode($temp2_1);
		$time1 =json_encode($time1);
		//var_dump($temp2_1);
	  //exit;
		$this->assign('temp1_1',$temp1_1);
		$this->assign('temp2_1',$temp2_1);
		$this->assign('time',$time1);
		$this->display();
	}
}