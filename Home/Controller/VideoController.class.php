<?php
namespace Home\Controller;
use Think\Controller;
class VideoController extends Controller {
		public function device_del(){
  		$url = "https://open.ys7.com/api/lapp/device/delete";
  		//$data['accessToken']="at.0b208l3u4n5829rb416trhc58l9o8kpe-708yokdtag-034cyzi-vbwyyg4wo";
  		//$data['deviceSerial']=$_GET['sn'];
  		//$data=json_encode($data,true);
  		//dump($data);
  		$data="accessToken=at.0b208l3u4n5829rb416trhc58l9o8kpe-708yokdtag-034cyzi-vbwyyg4wo&deviceSerial=".$_GET['sn'];
  		dump($data);
  		$ret=http($url,$data,'POST');
  		//$ret=json_decode($ret,true);
  		dump($ret);
		}
		
		public function ipc2nvr(){
			$url="https://open.ys7.com/api/lapp/device/ipc/add";
			$data="accessToken=at.0b208l3u4n5829rb416trhc58l9o8kpe-708yokdtag-034cyzi-vbwyyg4wo&deviceSerial=".$_GET['sn']."&ipcSerial=".$_GET['ipc'];
  		dump($data);
  		$ret=http($url,$data,'POST');
  		//$ret=json_decode($ret,true);
  		dump($ret);
		}
  
}