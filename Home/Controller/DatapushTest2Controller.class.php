<?php
namespace Home\Controller;
use Think\Controller;
class DatapushTest2Controller extends Controller {
	public function pushnewsubV30(){
		// $checksum=crc32("Thequickbrownfoxjumpedoverthelazydog.");
		// printf("%u\n",$checksum);
		
		$TIME_LEN = 15;//时间字符长度
		$DELAY_START = 10;
		$HOUR_DELAY_LEN = 2;
		$MIN_DELAY_LEN = 2;
		$FREQ_LEN = 1;
		
		$BTSN_LEN  = 10;//统编10位1类型,2-4国家编码,5-10区域编码
		$BDSN_LEN  = 4;//BS字符长度
		$BSN_LEN  = $BTSN_LEN+$BDSN_LEN;//BS字符长度
		$BVS_LEN  = 1; //B device version
		
		$BRSSI_LEN = 9;
		$BRSSI_MAX_LEN = 1;
		$BRSSI_COUNT = 4;
		$BRSSI_SN_LEN = 1;
		$BRSSI_SIGN_LEN =1;
		
		$CDATA_START = $TIME_LEN+$BSN_LEN+$BVS_LEN+$BRSSI_LEN;
		
		$COUNT_LEN =2; //data的条数
		$CSN_LEN  =4;//设备字符长度
		$SIGN_LEN =1;//信号
		$CVS_LEN =1;//client version
		$STATE_LEN  =1;//state
		$DELAY_LEN  =1;//delay
		$VAILD_LEN  =1;//有效值个数
		
		$VALUE_LEN = 9;//data中每个长度
		$COUNT_VALUE = 4;

		$DATA_LEN = ($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2+$VALUE_LEN*$COUNT_VALUE; //一条data长度
		
		//var_dump($DATA_LEN);
		
		$CRC_LEN  = 4;//校验码
		$post      =file_get_contents('php://input');//抓取内容
    $strarr    =unpack("H*", $post);//unpack() 函数从二进制字符串对数据进行解包。
    $str       =implode("", $strarr);

		$str = "323032303033303931373032303032313038363735363330300003e0020450000000000000000001ae0003e01f5c13240202720117721118790118ffffffffffffffffff0003e0205e13240102712117718117788117ffffffffffffffffff0003e0216113240002722117727117766117ffffffffffffffffff0003e0226013240202722117727117777117ffffffffffffffffff0003e0236613240202700117717117778117ffffffffffffffffff0003e0246113240102711117718117788117ffffffffffffffffff0003e0256413440102712117728117788117ffffffffffffffffff0003e0265e13240102733117729117788117ffffffffffffffffff0003e0276013240002789117780118810118ffffffffffffffffff0003e0285c13240202788117781118811118ffffffffffffffffff0003e0406513240202712117725117766117ffffffffffffffffff0003e0426213440102724117736117776117ffffffffffffffffff0003e0416513240002711117715117755117ffffffffffffffffff0003e0435d13240202721117726117755117ffffffffffffffffff0003e0445c13240002733117736117766117ffffffffffffffffff0003e0455a13240102745117747117776117ffffffffffffffffff0003e0465a13240002755117740118810118ffffffffffffffffff0003e0475c13240102753117741118800118ffffffffffffffffff0003e0485d13240002745117748117787117ffffffffffffffffff0003e0496013240202710117727117767117ffffffffffffffffff0003e04a6413240102721117727117767117ffffffffffffffffff0003e04b6213240002754117759117788117ffffffffffffffffff0003e04c6113240002732117737117777117ffffffffffffffffff0003e04d5d13240102722117718117787117ffffffffffffffffff0003e04e5b13240202733117738117798117ffffffffffffffffff0003e04f5913240002768117769117819117ffffffffffffffffff0003e0505913240002754117742118811118ffffffffffffffffff0003e0525d13240102723117728117787117ffffffffffffffffff0003e0536413240202731117728117767117ffffffffffffffffff0003e0547413240202722117707117765117ffffffffffffffffff0003e0556213440102711117715117756117ffffffffffffffffff0003e0566013240202742117738117767117ffffffffffffffffff0003e0576013240102734117757117788117ffffffffffffffffff0003e0595c13240102733117738117788117ffffffffffffffffff0003e05a5b13240102733117720118809117ffffffffffffffffff0003e05b5a13240202743117739117778117ffffffffffffffffff0003e05c6013240202723117734117744117ffffffffffffffffff0003e05d6613240102711117723117723117ffffffffffffffffff0003e05e6c13240202722117723117743117ffffffffffffffffff0003e05f5f13240202711117712117723117ffffffffffffffffff0003e0605913240002722117723117733117ffffffffffffffffff0003e0615a13240202731117725117734117ffffffffffffffffff0003e0625913240202722117734117745117ffffffffffffffffff0003e0635a13240102755117756117766117ffffffffffffffffff0003e0645813240102745117747117797117ffffffffffffffffff0003e0655913240102743117748117778117ffffffffffffffffff0003e0665c13240102743117745117745117ffffffffffffffffff0003e0675e13240102721117723117733117ffffffffffffffffff0003e0686b13240102710117712117722117ffffffffffffffffff0003e06960132402037891178031177441177331177301ffffffff0003e06a5a13240102723117722117733117ffffffffffffffffff0003e06d5713440102734117735117765117ffffffffffffffffff0003e06e5913240102734117737117787117ffffffffffffffffff0003e06f5913240102743117758117779117ffffffffffffffffff0003e0705d13240002722117724117755117ffffffffffffffffff0003e0735f13440202744117733117743117ffffffffffffffffff0003e0745d13240002755117744117754117ffffffffffffffffff0003e0755913240202732117724117743117ffffffffffffffffff0003e0765713240102754117745117745117ffffffffffffffffff0003e0775713240102733117735117755117ffffffffffffffffff0003e0795813240102765117751118800118ffffffffffffffffff0003e07a5c13440202744117738117787117ffffffffffffffffff0003e07b5f13240002721117726117766117ffffffffffffffffff0003e07c6113240002732117737117767117ffffffffffffffffff0003e07e5c13240202733117737117777117ffffffffffffffffff0003e07f5b13240202733117747117767117ffffffffffffffffff0003e0805713240102756117757117787117ffffffffffffffffff0003e0815813240002755117757117788117ffffffffffffffffff0003e0825813240102733117749117800118ffffffffffffffffff0003e0835d13240202743117751118801118ffffffffffffffffff0003e0846213240202721117727117767117ffffffffffffffffff0003e0856413240102721117727117756117ffffffffffffffffff0003e0876213240102711117716117766117ffffffffffffffffff0003e0886113240102722117727117766117ffffffffffffffffff0003e0895b13240002743117747117767117ffffffffffffffffff0003e08a5c13240002733117747117778117ffffffffffffffffff0003e08b5c13240102754117747117777117ffffffffffffffffff0003e08c5713240202744117740118800118ffffffffffffffffff0003e08d5d13240002766117759117799117ffffffffffffffffff0003e08e6013240002743117726117754117ffffffffffffffffff0003e08f6513440302743117734117733117ffffffffffffffffff0003e0915e13240002711117713117733117ffffffffffffffffff0003e0925913240202722117713117743117ffffffffffffffffff0003e0935a13240102735117743117764117ffffffffffffffffff0003e0945813240102733117734117744117ffffffffffffffffff0003e0955813240202733117745117755117ffffffffffffffffff0003e0965813240202755117749117808117ffffffffffffffffff0003e0975b13240402774117760118789117ffffffffffffffffff0003e0985d13240202733117725117744117ffffffffffffffffff0003e0996013240002732117734117744117ffffffffffffffffff0003e09a5c13240102732117724117733117ffffffffffffffffff0003e09b5913240102711117723117723117ffffffffffffffffff0003e09c58132400036871166731177221177431177301ffffffff0003e09d5713240002732117754117736117ffffffffffffffffff0003e09e5713240202743117745117745117ffffffffffffffffff0003e09f5713240002744117744117755117ffffffffffffffffff0003e0a05613240202733117737117788117ffffffffffffffffff0003e0a15913240102775117750118799117ffffffffffffffffff0003e0a25c13240102723117725117755117ffffffffffffffffff0003e0a45c13240102712117713117743117ffffffffffffffffff0003e0a55a13240102722117724117744117ffffffffffffffffff0003e0a75613240102743117735117744117ffffffffffffffffff0003e0a85613240202755117745117755117ffffffffffffffffff0003e0ac5c13240102732117738117778117ffffffffffffffffff0003e0ad5e13240202732117728117777117ffffffffffffffffff0003e0ae5b13240202733117737117777117ffffffffffffffffff0003e0af5a13240202755117758117788117ffffffffffffffffff0003e0b05813240002743117748117778117ffffffffffffffffff0003e0b15613240102743117747117778117ffffffffffffffffff0003e0b35513240202735117737117798117ffffffffffffffffff0003e0b45613240202743117730118800118ffffffffffffffffff0003e0b56013240002753117752118802118ffffffffffffffffff0003e0b66013240102734117728117787117ffffffffffffffffff0003e0b76213240002721117727117767117ffffffffffffffffff0003e0b95d13240002712117746117769117ffffffffffffffffff0003e0ba5f13240002733117737117777117ffffffffffffffffff0003e0bb5d13240002754117749117788117ffffffffffffffffff0003e0bd5c13240002744117748117789117ffffffffffffffffff0003e0be5813240102755117740118800118ffffffffffffffffff0003e0bf5a13240202755117750118799117ffffffffffffffffff0003e0c05e13240102743117747117756117ffffffffffffffffff0003e0c15e13240102723117724117744117ffffffffffffffffff0003e0c25d13240102731117724117734117ffffffffffffffffff0003e0c35b13240002732117724117733117ffffffffffffffffff0003e0c55813240202734117734117755117ffffffffffffffffff0003e0c65713248102744117755117756117ffffffffffffffffff0003e0c75413240002766117765117756117ffffffffffffffffff0003e0c85613240202754117748117798117ffffffffffffffffff0003e0c95a13240102764117750118790118ffffffffffffffffff0003e0ca5c13240002743117736117755117ffffffffffffffffff0003e0cb5a13240102722117744117735117ffffffffffffffffff0003e0cc5c13240202723117723117733117ffffffffffffffffff0003e0cd5713240202733117733117743117ffffffffffffffffff0003e0ce5713240002766117754117743117ffffffffffffffffff0003e0cf55132401036901177341177431177441177401ffffffff0003e0d056132401039131199051177551177451177401ffffffff0003e0d15513240002745117745117765117ffffffffffffffffff0003e0d25813240102744117747117798117ffffffffffffffffff0003e0d35713240202756117760118811118ffffffffffffffffff0003e0d55b13240102732117724117744117ffffffffffffffffff0003e0d65813240002722117724117744117ffffffffffffffffff0003e0d85613240102733117745117745117ffffffffffffffffff0003e0d95613240002734117745117755117ffffffffffffffffff0003e0da5513240002744117755117766117ffffffffffffffffff0003e0db5213240102754117757117767117ffffffffffffffffff0003e0dc5213240202745117748117798117ffffffffffffffffff0003e0dd5713240002775117763118813118ffffffffffffffffff0003e0de5913240002744117749117799117ffffffffffffffffff0003e0df5a13240002733117748117778117ffffffffffffffffff0003e0e05913240102733117738117788117ffffffffffffffffff0003e0e15713240002733117738117788117ffffffffffffffffff0003e0e35713240102745117757117788117ffffffffffffffffff0003e0e45513240002743117748117788117ffffffffffffffffff0003e0e55413240002743117739117788117ffffffffffffffffff0003e0e65613240102733117730118800118ffffffffffffffffff0003e0e75e13240002765117773118824118ffffffffffffffffff0003e0e85d13240202754117749117789117ffffffffffffffffff0003e0ea5c13240102734117749117789117ffffffffffffffffff0003e0eb5c13240102744117748117789117ffffffffffffffffff0003e0ec5d13240202756117768117789117ffffffffffffffffff0003e0ed5813240102745117739117808117ffffffffffffffffff0003e0ee5b13240202744117729117797117ffffffffffffffffff0003e0ef5b13240002755117758117789117ffffffffffffffffff0003e0f05a13240102744117740118801118ffffffffffffffffff0003e0f15a13240202765117750118800118ffffffffffffffffff0003e0f25b13240202753117747117766117ffffffffffffffffff0003e0f35b13240002743117746117756117ffffffffffffffffff0003e0f45913240002734117735117755117ffffffffffffffffff0003e0f55913240202754117755117755117ffffffffffffffffff0003e0f65913240002753117737117755117ffffffffffffffffff0003e0f75613240102733117745117756117ffffffffffffffffff0003e0f85613240202732117725117755117ffffffffffffffffff0003e0f95413240102744117746117766117ffffffffffffffffff0003e0fa5213440102778117778117799117ffffffffffffffffff0003e0fb5813440002787117771118800118ffffffffffffffffff0003e0fc5913440002755117757117767117ffffffffffffffffff0003e0fd5813440102765117756117755117ffffffffffffffffff0003e0fe5813440202733117745117745117ffffffffffffffffff0003e0ff5713440102745117745117765117ffffffffffffffffff0003e1005213440102745117755117755117ffffffffffffffffff0003e1015613440002732117735117745117ffffffffffffffffff0003e1025413440102734117745117756117ffffffffffffffffff0003e10355134401037011177241177441177661177601ffffffff0003e1047113440002766117758117799117ffffffffffffffffff0003e1055813440102776117761118811118ffffffffffffffffff0003e1065813440002755117758117778117ffffffffffffffffff0003e1085713440b02755117756117766117ffffffffffffffffff0003e1095513440102744117746117776117ffffffffffffffffff0003e10a5513440202744117746117766117ffffffffffffffffff0003e10b5213440002765117757117766117ffffffffffffffffff0003e10c5113440302767117776117766117ffffffffffffffffff0003e10d5113440202754117746117766117ffffffffffffffffff0003e10e5013440202755117758117790118ffffffffffffffffff0003e10f5613440002786117774118823118ffffffffffffffffff0003e1105813440002755117750118800118ffffffffffffffffff0003e1125813440102755117750118809117ffffffffffffffffff0003e1135513440102744117759117790118ffffffffffffffffff0003e1145413440002755117750118799117ffffffffffffffffff0003e1155213440202799117790118800118ffffffffffffffffff0003e1175213440102754117749117799117ffffffffffffffffff0003e11a5913440302755117752118811118ffffffffffffffffff0003e11b5713440202766117761118811118ffffffffffffffffff0003e11c5713440102765117761118800118ffffffffffffffffff0003e11d5813440002744117750118800118ffffffffffffffffff0003e11e5813440202754117750118800118ffffffffffffffffff0003e11f5913440202744117740118800118ffffffffffffffffff0003e1205713440102744117759117790118ffffffffffffffffff0003e1215813440102754117760118791118ffffffffffffffffff0003e122581324ff02755117762118823118ffffffffffffffffff0003e1235713440102775117763118822118ffffffffffffffffff0003e1245a13440002744117759117789117ffffffffffffffffff0003e1265813440002744117757117788117ffffffffffffffffff0003e1285713440302733117757117778117ffffffffffffffffff0003e1295413440102734117748117788117ffffffffffffffffff0003e12a5413440002733117738117777117ffffffffffffffffff0003e12c5613440102744117740118800118ffffffffffffffffff0003e12b5213440002733117737117787117ffffffffffffffffff0003e12d5613440202776117773118833118ffffffffffffffffff0003e12e5813440002776117770118799117ffffffffffffffffff0003e12f5813440002754117758117788117ffffffffffffffffff0003e1305613440102764117768117778117ffffffffffffffffff0003e1315413440202744117757117777117ffffffffffffffffff0003e1325413440202744117757117777117ffffffffffffffffff0003e1335113440102743117768117779117ffffffffffffffffff0003e1354e13440102733117737117778117ffffffffffffffffff0003e13651134402037221177441177441178011188001ffffffff0003e1375713440002776117773118833118ffffffffffffffffff0003e1385713440002776117771118801118ffffffffffffffffff0003e1395513440002766117789117801118ffffffffffffffffff0003e13a5513440202754117769117789117ffffffffffffffffff0003e13d5113440102754117759117788117ffffffffffffffffff0003e13e5113440202743117758117789117ffffffffffffffffff0003e13f5113440002733117748117788117ffffffffffffffffff0003e1415813440102776117775118845118ffffffffffffffffff0003e1404f13440002755117750118800118ffffffffffffffffff0003e1425613440102767117782118823118ffffffffffffffffff0003e1435713440102800118811118822118ffffffffffffffffff0003e1445513440102786117773118811118ffffffffffffffffff0003e1455513240002756117771118812118ffffffffffffffffff0003e1465513440102756117771118811118ffffffffffffffffff0003e1475213440202787117793118824118ffffffffffffffffff0003e1485013440102754117761118811118ffffffffffffffffff0003e1495113440102734117750118811118ffffffffffffffffff0003e14a5013440002744117752118833118ffffffffffffffffff0003e14b5813440002766117762118822118ffffffffffffffffff0003e14c5513440002755117750118790118ffffffffffffffffff0003e14f5413440202743117739117788117ffffffffffffffffff0003e1505213440102745117749117798117ffffffffffffffffff0003e1515013440102754117758117778117ffffffffffffffffff0003e1535713440002733117737117777117ffffffffffffffffff0003e1545613440102733117739117799117ffffffffffffffffff0003e1555713440202787117781118801118ffffffffffffffffff0003e1565713440202765117758117777117ffffffffffffffffff0003e1575513440202765117748117776117ffffffffffffffffff0003e1585513440102743117746117756117ffffffffffffffffff0003e1595413440002754117737117755117ffffffffffffffffff0003e15b5213440102744117745117745117ffffffffffffffffff0003e15c5113440202733117734117744117ffffffffffffffffff0003e15e4c13440102755117758117788117ffffffffffffffffff0003e15f5713440002777117781118800118ffffffffffffffffff0003e1605613440102777117777117777117ffffffffffffffffff0003e1615613440202755117756117766117ffffffffffffffffff0003e1625213440002776117767117765117ffffffffffffffffff0003e1635013440302745117755117756117ffffffffffffffffff0003e1645113440202744117736117755117ffffffffffffffffff0003e1655113440102743117755117746117ffffffffffffffffff0003e1665113440202754117746117755117ffffffffffffffffff0003e1675113440002755117755117765117ffffffffffffffffff0003e1685113440102744117747117798117ffffffffffffffffff0003e16957134400037771177661177561178191178001ffffffff0003e16a5413440202765117757117776117ffffffffffffffffff0003e16b5413240402755117756117765117ffffffffffffffffff0003e16c5213440102755117756117755117ffffffffffffffffff0003e16d5113440002776117766117755117ffffffffffffffffff0003e16e5013440102766117765117755117ffffffffffffffffff0003e16f4d13440002744117745117755117ffffffffffffffffff0003e1705013440002743117745117745117ffffffffffffffffff0003e1714f13440202764117756117755117ffffffffffffffffff0003e1725013440102743117738117788117ffffffffffffffffff0003e1735413440202765117751118800118ffffffffffffffffff0003e1745713440002754117749117788117ffffffffffffffffff0003e1775013440202765117758117777117ffffffffffffffffff0003e1784e13440302775117758117777117ffffffffffffffffff0003e1794f13440202744117746117776117ffffffffffffffffff0003e17a5013440102744117747117767117ffffffffffffffffff0003e17b4e13440002756117757117787117ffffffffffffffffff0003e17c4d13440002744117739117809117ffffffffffffffffff0003e17d5613440202788117773118822118ffffffffffffffffff0003e17e5513440102765117750118799117ffffffffffffffffff0003e17f5613440002765117759117789117ffffffffffffffffff0003e1805213440102755117759117799117ffffffffffffffffff0003e1815113440202755117768117788117ffffffffffffffffff0003e1824e13440002744117748117788117ffffffffffffffffff0003e1845113440102755117757117778117ffffffffffffffffff0003e1855413440102744117748117788117ffffffffffffffffff0003e1865213440202743117740118801118ffffffffffffffffff0003e1875913440202787117771118799117ffffffffffffffffff0003e1885513440102755117757117766117ffffffffffffffffff0003e1895713440002755117756117766117ffffffffffffffffff0003e18a5213440102755117746117765117ffffffffffffffffff0003e18b5513440202755117755117755117ffffffffffffffffff0003e18c5213440202755117745117755117ffffffffffffffffff0003e18d5213240002744117735117744117ffffffffffffffffff0003e18f4d13440102745117745117755117ffffffffffffffffff0003e1905213440002766117757117797117ffffffffffffffffff0003e1915513440302777117770118790118ffffffffffffffffff0003e1925613440002755117766117756117ffffffffffffffffff0003e1955113440102744117745117754117ffffffffffffffffff0003e1965213440202734117744117755117ffffffffffffffffff0003e1975013440202733117734117744117ffffffffffffffffff0003e1985013440202733117744117744117ffffffffffffffffff0003e1995013440002755117755117755117ffffffffffffffffff0003e19b5613440102776117760118790118ffffffffffffffffff0003e19a5013440002766117768117788117ffffffffffffffffff0003e19c56134400037441177351177541177651177501ffffffff0003e19d5213440102776117766117755117ffffffffffffffffff0003e19f4d13440102744117744117754117ffffffffffffffffff0003e1a0501324ff02755117754117745117ffffffffffffffffff0003e1a25013440202744117735117754117ffffffffffffffffff0003e1a34e13440102753117746117745117ffffffffffffffffff0003e1a44d13440102744117747117788117ffffffffffffffffff0003e1a55813440202743117740118790118ffffffffffffffffff0003e1a65513440102753117739117777117ffffffffffffffffff0003e1a75213440102744117737117776117ffffffffffffffffff0003e1a94f13440102733117726117766117ffffffffffffffffff0003e1aa4f13440002733117736117766117ffffffffffffffffff0003e1ab4d13440002743117727117765117ffffffffffffffffff0003e1ad5013440002733117726117776117ffffffffffffffffff0003e1ae4f13440002732117729117799117ffffffffffffffffff0003e1af5713440002755117762118813118ffffffffffffffffff0003e1b05413440202744117740118799117ffffffffffffffffff0003e1b15213440002743117739117788117ffffffffffffffffff0003e1b25113440102755117758117788117ffffffffffffffffff0003e1b35213440202733117728117778117ffffffffffffffffff0003e1b45213440102722117738117778117ffffffffffffffffff0003e1b55113440102723117727117777117ffffffffffffffffff0003e1b75113440002732117718117777117ffffffffffffffffff0003e1b85113440002722117720118800118ffffffffffffffffff0003e1b95913440002765117760118790118ffffffffffffffffff0003e1ba5713440102755117756117766117ffffffffffffffffff0003e1bb5513440202733117745117756117ffffffffffffffffff0003e1bd5413440102743117735117754117ffffffffffffffffff0003e1be5113440202733117734117744117ffffffffffffffffff0003e1bf5113440102732117744117735117ffffffffffffffffff0003e1c05013440102733117734117744117ffffffffffffffffff0003e1c15013440102755117755117755117ffffffffffffffffff0003e1c25013440002743117737117788117ffffffffffffffffff0003e1c45613440102745117746117775117ffffffffffffffffff0003e1c35813440002765117750118799117ffffffffffffffffff0003e1c55213440102764117756117745117ffffffffffffffffff0003e1c65513440002755117754117745117ffffffffffffffffff0003e1c75013440302733117744117744117ffffffffffffffffff0003e1c85113440002733117724117743117ffffffffffffffffff0003e1c95213440002733117724117743117ffffffffffffffffff0003e1ca5013440202733117734117744117ffffffffffffffffff0003e1cb5013440102755117745117744117ffffffffffffffffff0003e1cc4e13440102755117748117798117ffffffffffffffffff0003e1cd5a13440002765117760118789117ffffffffffffffffff0003e1ce5513440002755117755117766117ffffffffffffffffff0003e1cf4f134401037441177441177441177441177501ffffffff0003e1d15113440102754117745117745117ffffffffffffffffff0003e1d25013440102734117744117754117ffffffffffffffffff0003e1d44f13440102733117734117743117ffffffffffffffffff0003e1d64d13440102744117747117788117ffffffffffffffffff0003e1d75c13440202744117740118800118ffffffffffffffffff0003e1d85813440002743117737117777117ffffffffffffffffff0003e1d95613440102744117747117766117ffffffffffffffffff0003e1da5413440202744117756117766117ffffffffffffffffff0003e1db5013440002744117746117766117ffffffffffffffffff0003e1dc5013440002744117736117766117ffffffffffffffffff0003e1dd4d13440102734117736117765117ffffffffffffffffff0003e1de4e13440102744117746117766117ffffffffffffffffff0003e1df4d13440202755117746117776117ffffffffffffffffff0003e1e05013440002732117738117799117ffffffffffffffffff0003e1e25413440102755117740118799117ffffffffffffffffff0003e1e35213440002733117749117789117ffffffffffffffffff0003e1e45413440102733117738117789117ffffffffffffffffff0003e1e55213440202733117738117788117ffffffffffffffffff0003e1e65213440002732117748117789117ffffffffffffffffff0003e1e77513440202721117717117777117ffffffffffffffffff0003e1e84f13440202722117728117778117ffffffffffffffffff0003e1e95113440102722117728117788117ffffffffffffffffff0003e1ea4f13440002745117730118810118ffffffffffffffffff0003e1eb5713240302764117751118789117ffffffffffffffffff0003e1ec5713440202732117736117756117ffffffffffffffffff0003e1ed5713240002732117725117744117ffffffffffffffffff0003e1ee5413440102733117735117755117ffffffffffffffffff0003e1ef5613440002744117735117744117ffffffffffffffffff0003e1f05013440002744117734117754117ffffffffffffffffff0003e1f15213440102734117724117754117ffffffffffffffffff0003e1f25013440002742117725117744117ffffffffffffffffff0003e1f34e13440102755117755117756117ffffffffffffffffff0003e1f45013440102743117738117788117ffffffffffffffffff0003e1f55813440102754117740118790118ffffffffffffffffff0003e1f65713440002743117726117755117ffffffffffffffffff0003e1f77513440102733117735117745117ffffffffffffffffff0003e1f85213440202743117734117744117ffffffffffffffffff0003e1f95213440102734117734117754117ffffffffffffffffff0003e1fa5213440102733117724117743117ffffffffffffffffff0003e1fb5113440302711117723117724117ffffffffffffffffff0003e1fc4f13440302732117734117745117ffffffffffffffffff0003e1fd4f13440102733117734117745117ffffffffffffffffff0003e1fe5013440202722117727117788117ffffffffffffffffff0003e1ff5c13440102753117740118799117ffffffffffffffffff0003e2005813440102743117747117767117ffffffffffffffffff0003e2015813440002732117724117745117ffffffffffffffffff0003e2025113440002743117735117745117ffffffffffffffffff0003e20350134403037221177131177221177441177401ffffffff0003e2045013440202732117724117744117ffffffffffffffffff0003e2054f13240002733117734117744117ffffffffffffffffff0003e2064e13440302754117745117744117ffffffffffffffffff0003e2074d13440102733117735117755117ffffffffffffffffff0003e2084e13440202722117728117788117ffffffffffffffffff0003e2095e13440202743117731118801118ffffffffffffffffff0003e20a5d13440202733117758117779117ffffffffffffffffff0003e20b5713440002723117726117766117ffffffffffffffffff0003e20d5013440102733117736117766117ffffffffffffffffff0003e20f5113440102744117736117765117ffffffffffffffffff0003e2104f13440202732117736117756117ffffffffffffffffff0003e2115013440002722117726117766117ffffffffffffffffff0003e2135613240202754117753118824118ffffffffffffffffff0003e2145613440102732117731118801118ffffffffffffffffff0003e2164f13440202710117719117790118ffffffffffffffffff0003e2175413440102711117729117790118ffffffffffffffffff0003e2185513440102711117719117790118ffffffffffffffffff0003e2195213440102731117710118799117ffffffffffffffffff0003e21a5013240002722117739117791118ffffffffffffffffff0003e21c5013440102721117712118812118ffffffffffffffffff0003e21b5113440002711117719117790118ffffffffffffffffff0003e21d5913440102754117742118812118ffffffffffffffffff0016a7f1";
    $sndir = substr($str, ($TIME_LEN+$BTSN_LEN)*2,$BDSN_LEN*2);
    $sn_footer = hexdec($sndir)&0x1fff;
    $sn_header = hexdec($sndir)>>13;
    $logbase="lora_backupV2030/";
    $logerr="lora_errorV2030/";
    $logreq="lora_reqV2030/";
    

		if(strlen($str) < $CDATA_START){
    	echo "OKF".date('YmdHis')."00101";
    	exit;
		}

    $sid =  (int)$_GET['sid']&0x1fff;
    //var_dump($sid);
    $bsnstr   =substr($str, $TIME_LEN*2,$BSN_LEN*2);
    $btsnstr =substr($str, $TIME_LEN*2,$BTSN_LEN*2);
    $bdsnstr =substr($str, ($TIME_LEN+$BTSN_LEN)*2,$BDSN_LEN*2);
    $btsn=hex2bin($btsnstr);
    $bsnint = hexdec($bdsnstr)&0x1fff;
    $psn = hexdec($bdsnstr)>>13;
    //var_dump($bsnint);
    //var_dump($psn);
    
    //$psn = $bdevinfo['psn'];
    //var_dump($bsnint);
    if($bsnint!=$sid){
    	echo "OKE".date('YmdHis')."00101";
 	
    	exit;
    }
    $psninfo = D('psn')->where(array('tsn'=>$btsn,'sn'=>$psn))->find();
    if($psninfo){
    	$psnid=$psninfo['id'];
    }else{
    	echo "OKE".date('YmdHis')."00101";
    	exit;
    }

		$bversion = substr($str, ($TIME_LEN+$BSN_LEN)*2,$BVS_LEN*2);
		$brssimaxstr = substr($str, ($TIME_LEN+$BSN_LEN+$BVS_LEN)*2,$BRSSI_MAX_LEN*2);
		$brssistr = substr($str, ($TIME_LEN+$BSN_LEN+$BVS_LEN+$BRSSI_MAX_LEN)*2,$BRSSI_LEN*2);

		$bvs = hexdec($bversion);
		$brssimax = hexdec($brssimaxstr);
		$brssimax = 0-$brssimax;
		for($i=0;$i < $BRSSI_COUNT;$i++){
			$brssisnstr= substr($brssistr, $i*($BRSSI_SN_LEN+$BRSSI_SIGN_LEN)*2,$BRSSI_SN_LEN*2);
			//var_dump($brssisnstr);
			$brssisn[$i] = hexdec($brssisnstr);
			if($brssisn>0){
				$brssisignstr= substr($brssistr, $i*($BRSSI_SN_LEN+$BRSSI_SIGN_LEN)*2+$BRSSI_SN_LEN*2,$BRSSI_SIGN_LEN*2);
				$brssisign = hexdec($brssisignstr);
				//var_dump($brssisign);
				if(($brssisign&0x08)==0x08){
					$bsign[$i] = 0-($brssisign&0x07);
				}else{
					$bsign[$i] = $brssisign;
				}
			}else{
				$bsign[$i]=0;
			}
		}
		$rssi = array(
						'psnid'=>$psnid,
						'bsn'=>$bsnint,
						'rssi'=>$brssimax,
						'sn1'=>$brssisn[0],
						'rssi1'=>$bsign[0],
						'sn2'=>$brssisn[1],
						'rssi2'=>$bsign[1],
						'sn3'=>$brssisn[2],
						'rssi3'=>$bsign[2],
						'sn4'=>$brssisn[3],
						'rssi4'=>$bsign[3],
						'time'=>time(),
						);
	 	//$saveRssi=D('brssi')->add($rssi);
	 	//var_dump($rssi);
	 	//var_dump($bsign);
	 	//exit;
		//var_dump($bvs);
    $bdevinfo    =D('bdevice')->where(array('id'=>$bsnint,'psnid'=>$psnid))->find();

    if($bdevinfo){
    	$uptime=$bdevinfo['uptime'];
    	//var_dump($uptime);
    	$rate = $bdevinfo['rate_id'];
    	$dev_freq = $bdevinfo['count'];
    	$delay_time  = str_pad($uptime,4,'0',STR_PAD_LEFT).$dev_freq;
    	if($bdevinfo['version']!=$bvs){
    		//var_dump($bdevinfo['version']);
    		$saveSql=M('bdevice')->where(array('id'=>$bsnint,'psnid'=>$psnid))->save(array('version'=>$bvs));	
    	}
    	$url_flag = $bdevinfo['url_flag'];
			$url = $bdevinfo['url'];
			$change_flag=$bdevinfo['change_flag'];
			$new_bsn=$bdevinfo['new_bsn'];
			if($url_flag==1){
				$urllen=str_pad(strlen($url),2,'0',STR_PAD_LEFT);
				$footer=$url_flag.$urllen.$url;
			}else{
				$footer="0";
			}
			if($change_flag==1){
				$change_str=$change_flag.$new_bsn;
				$ch_psnint=(int)substr($new_bsn,0,5);
				$ch_bsnint=(int)substr($new_bsn,5,4);
				$ch_bdevinfo=D('bdevice')->where(array('id'=>$ch_bsnint,'psnid'=>$ch_psnint))->find();
				if($ch_bdevinfo)
				{
					$rate = $ch_bdevinfo['rate_id'];
				}
				
			}else{
				$change_str="0";
			}
    }else{
    	$dev_freq = 1;
    	$url_flag = 0;
    	$delay_time = "00101".$dev_freq;
    	echo "OKE";
    	exit;
    }
    
    //echo "delay_time:";
		//var_dump($delay_time);

    $count     =substr($str,$CDATA_START*2,$COUNT_LEN*2);//2为解包后的倍数
    $count	   =hexdec($count);//从十六进制转十进制
    $data      =substr($str,($CDATA_START+$COUNT_LEN)*2,$count*$DATA_LEN);//取出data
    $env_temp = 0;
    $snint = 0;
    $battery = 0;
    //var_dump($count);
    
    $hour_delay =substr($str,$DELAY_START*2,$HOUR_DELAY_LEN*2);
    $hour_delay =(int)pack("H*",$hour_delay);
    $min_delay =substr($str,($DELAY_START+$HOUR_DELAY_LEN)*2,$HOUR_DELAY_LEN*2);
    $min_delay =(int)pack("H*",$min_delay);
    $freq = substr($str,($DELAY_START+$HOUR_DELAY_LEN+$MIN_DELAY_LEN)*2,$FREQ_LEN*2);
    $freq = (int)pack("H*",$freq);
    
    //var_dump($hour_delay);
    //var_dump($min_delay);
    //echo "freq:";
    //var_dump($freq);
    
    $day_begin = strtotime(date('Y-m-d',time()));
    //var_dump(date('Y-m-d',time()));
    $hour_time = 60*60;
    $pre_time =5*60;
    $hour_pre_time=15*60;
    $now = time();
    $today = strtotime(date('Y-m-d',$now).'00:00:00');
   	$now = $now-$today;
   	
   	$re_devs =D('device')->where(array('psnid'=>$psnid,'re_flag'=>1))->order('devid asc')->limit(0,64)->select();
   	
   	$re_devs2 =D('device')->where(array('psnid'=>$psnid,'re_flag'=>2))->order('devid asc')->limit(0,64)->select();
   	
   	$cur_devs =D('device')->where(array('psnid'=>$psnid))->order('devid asc')->select();
   	
   	$change_devs = D('changeidlog')->where(array('psnid' => $psnid))->select();
   	//var_dump($re_devs);
   	
    //未解包比对
    $len=strlen($str);
    $crc=substr($str,$len-$CRC_LEN*2);//收到发来的crc
    dump($crc);
    $crc=hexdec($crc);
    dump($crc);

    $sum=0;
    $len = strlen($str);
		for($i=0 ; $i < $len/2-$CRC_LEN;$i++)
		{
			$value = hexdec(substr($str, $i*2,2));
			//var_dump($value);
			$sum+=$value;
		}
		$sum=$sum&0xffffffff;
		dump($sum);
		echo "count:";
		dump($count);
		if($crc==$sum){
	    for($i=0 ; $i < $count ; $i++){
	    	$snstr   =substr($data, $i*$DATA_LEN,$CSN_LEN*2);
	    	//var_dump($snstr);
	    	$snint = hexdec($snstr)&0x1fff;;	//从十六进制转十进制
	    	$dev_psn = hexdec($snstr) >> 13;
	    	
	    	$rfid = $dev_psn*10000+$snint;
	    	//var_dump($snint);
	    	if($dev_psn!=$psn)
	    	{
					echo "sn:";
	    		dump($psn);
	    		dump($snint);
	    		continue;
	    	}
	    	$signstr = substr($data, $i*$DATA_LEN+($CSN_LEN)*2,$SIGN_LEN*2);
	    	$cvsstr = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN)*2,$CVS_LEN*2);
	    	$stastr = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN)*2,$STATE_LEN*2);
	    	$destr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN)*2,$DELAY_LEN*2);
	    	$vaildstr  = substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN)*2,$VAILD_LEN*2);
	    	$sign= 0-hexdec($signstr);
	    	$cvs = hexdec($cvsstr);
	    	$cvs = $cvs&0x0f;
	    	$cindex = hexdec($cvsstr);
	    	$cindex = ($cindex&0xf0)>>4;
	    	$state =  hexdec($stastr);
	    	$delay =  hexdec($destr);
	    	$vaild = hexdec($vaildstr);
	    	//echo "devid:";
	    	//var_dump($snint);
	    	$devbuf[]=$snint;

	    	//var_dump($cvs);
	    	//var_dump($state);
	    	//echo "vaild:";
	    	//var_dump($vaild);
	    	$stmp = 0x07;
	    	$stmp2 = 0x80;
	    	$stmp3 = 0x08;
	    	if(($state & $stmp2) == $stmp2){
	    		$battery=1;
	    	}
	    	else{
	    		$battery=0;
	    	}
	    	$lcount = $state&0x70;
	    	$lcount = ($lcount)>>4;
	    	$type = $state&$stmp3;
	    	$state=$state&$stmp;
	    	
	    	//var_dump($ast);
	    	//var_dump($state);
	    	//var_dump($type);
	    	
	    	if($type>0){
	    		$type=1;
	    	}
	    	//echo "type:";
	    	//var_dump($type);
	    	if($snint>0)
	    	{
	    		$rfid_find=false;
		    	foreach($cur_devs as $cur_dev){
		    		if($cur_dev['rid']==$rfid){
		    			$rfid_find=true;
		    			break;
		    		}
		    	}
		    	//var_dump($info);
		    	if($rfid_find==false){
		    		  $change_dev_find=false;
		    		  foreach($change_devs as $ch_dev){
		    		  	if($ch_dev['psnid']==$psnid&&
		    		  		$ch_dev['new_devid']==$snint){
		    		  			$change_dev_find=true;
		    		  			$rfid=$ch_dev['rfid'];
		    		  			if($ch_dev['flag']==2){
		    		  				$change_dev_find=false;
		    		  				$ret=M('changeidlog')->where(array('id'=>$ch_dev['id']))->save(array('flag'=>3));
		    		  			}
		    		  		}
		    		  }
		    		  foreach($rfid_list as $rfid_dev){
		    		  	if($rfid_dev['devid']==$snint){
		    		  		$change_dev_find=true;
		    		  		break;
		    		  	}
		    		  }
		    		  if($change_dev_find==false){
								$addrfdev=array(
									'psn'=>$dev_psn,
									'psnid'=>$psnid,
									'shed'=>1,
									'fold'=>1,
									'flag'=>0,
									'state'=>0,
									'battery'=>$battery,
						  	 	'dev_state'=>$state,
						  	 	'version'=>$cvs,
									's_count'=>0,
									'rid'=>$rfid,
									'age'=>1,
									'devid'=>$snint,
								);
								$rfid_list[]=$addrfdev;
		    		  }else{
		    		  	//dump('NEVER.');
		    		  	//NEVER HAPPEN.
		    		  }
		    	}
	    	}
	    	
		    if($min_delay == 0){
		      $real_time = ((int)(($now+$hour_pre_time)/($hour_delay*$hour_time))-1)*$hour_delay*$hour_time;
		      $real_time = $today+$real_time;
		      $interval = ($hour_delay/$freq)*$hour_time;
		      //$real_time = strtotime(date('Y-m-d H',$real_time).':00:00');
		    }else{
		    	$real_time = ((int)(($now+$pre_time)/($min_delay*60)-1))*$min_delay*60;
		    	$real_time = $today+$real_time;
		    	$interval = ($min_delay/$freq)*60;
		      //$real_time = strtotime(date('Y-m-d H:i',$real_time).':00');
		    }
		    
		    $start = $real_time-$interval*$freq;
		    $end = $real_time;
		    
		    $tempstr=substr($data, $i*$DATA_LEN+($CSN_LEN+$SIGN_LEN+$CVS_LEN+$STATE_LEN+$DELAY_LEN+$VAILD_LEN)*2,$VALUE_LEN*$COUNT_VALUE);//temp1十六进制字符
	    	for($j=0;$j < $vaild;$j++){

		    	$up_time = $real_time-$interval*$freq+$interval*($j+1)+$interval*($freq-$vaild);
			    $up_time = strtotime(date('Y-m-d H:i',$up_time).':00');
			    
		    	if($type==0){
							if($j==0){
						    $temp1str1 = substr($tempstr,3,1);
					  		$temp1str2 = substr($tempstr,0,1);
					    	$temp1str3 = substr($tempstr,1,1);
					    	$temp1int =base_convert($temp1str1,16,10);

					    	$temp2str1 = substr($tempstr,4,1);
					  		$temp2str2 = substr($tempstr,5,1);
					    	$temp2str3 = substr($tempstr,2,1);
					    	$temp2int =base_convert($temp2str1,16,10);
					    	
					    	$temp3str1 = substr($tempstr,9,1);
					  		$temp3str2 = substr($tempstr,6,1);
					    	$temp3str3 = substr($tempstr,7,1);
					    	$temp3int =base_convert($temp3str1,16,10);
				    	}else if($j==1){
				    		$temp1str1 = substr($tempstr,10,1);
					  		$temp1str2 = substr($tempstr,11,1);
					    	$temp1str3 = substr($tempstr,8,1);
					    	$temp1int =base_convert($temp1str1,16,10);
					    	
					    	$temp2str1 = substr($tempstr,15,1);
					  		$temp2str2 = substr($tempstr,12,1);
					    	$temp2str3 = substr($tempstr,13,1);
					    	$temp2int =base_convert($temp2str1,16,10);
					    	
					    	$temp3str1 = substr($tempstr,16,1);
					  		$temp3str2 = substr($tempstr,17,1);
					    	$temp3str3 = substr($tempstr,14,1);
					    	$temp3int =base_convert($temp3str1,16,10);
				    	}else if($j==2){
						    $temp1str1 = substr($tempstr,21,1);
					  		$temp1str2 = substr($tempstr,18,1);
					    	$temp1str3 = substr($tempstr,19,1);
					    	$temp1int =base_convert($temp1str1,16,10);
					    	
					    	$temp2str1 = substr($tempstr,22,1);
					  		$temp2str2 = substr($tempstr,23,1);
					    	$temp2str3 = substr($tempstr,20,1);
					    	$temp2int =base_convert($temp2str1,16,10);
					    	
					    	$temp3str1 = substr($tempstr,27,1);
					  		$temp3str2 = substr($tempstr,24,1);
					    	$temp3str3 = substr($tempstr,25,1);
					    	$temp3int =base_convert($temp3str1,16,10);			    	
				    	}else if($j==3){
				    		$temp1str1 = substr($tempstr,28,1);
					  		$temp1str2 = substr($tempstr,29,1);
					    	$temp1str3 = substr($tempstr,26,1);
					    	$temp1int =base_convert($temp1str1,16,10);

					    	$temp2str1 = substr($tempstr,33,1);
					  		$temp2str2 = substr($tempstr,30,1);
					    	$temp2str3 = substr($tempstr,31,1);
					    	$temp2int =base_convert($temp2str1,16,10);

					    	$temp3str1 = substr($tempstr,34,1);
					  		$temp3str2 = substr($tempstr,35,1);
					    	$temp3str3 = substr($tempstr,32,1);
					    	$temp3int =base_convert($temp3str1,16,10);					    	
				    	}
		    		
			    if(($temp1int&0x08)==0x08){
		    		$temp1str1=$temp1int&0x07;
		    		if($temp1str1==0){
		    			$temp1 = '-'.$temp1str2.".".$temp1str3;
		    		}else{
		    			$temp1 =  '-'.$temp1str1.$temp1str2.".".$temp1str3;
		    		}
		    	}else{
			    		if($temp1str1==0){
			    			$temp1 = $temp1str2.".".$temp1str3;
			    		}else{
			    			$temp1 = $temp1str1.$temp1str2.".".$temp1str3;
			    		}
		    	}

				  //var_dump('temp1:'.$temp1);
				  if(($temp2int&0x08)==0x08){
				  	$temp2str1=$temp2int&0x07;
						if($temp2str1 == 0){
					    $temp2 = '-'.$temp2str2.".".$temp2str3;
				 	 	}else{
				 	 	 	$temp2 = '-'.$temp2str1.$temp2str2.".".$temp2str3;
				 	 	}
			 		}else{
			 			if($temp2str1 == 0){
					    $temp2 = $temp2str2.".".$temp2str3;
				 	 	}else{
				 	 	 	$temp2 = $temp2str1.$temp2str2.".".$temp2str3;
				 	 	}
			 			
			 		}
			 	
			    //var_dump('temp2:'.$temp2);
			    if(($temp3int&0x08)==0x08){
			    	$temp3str1=$temp3int&0x07;
				    if($temp3str1 == 0){
					    $temp3 = '-'.$temp3str2.".".$temp3str3;
				 	 	}else{
				 	 	 	$temp3 = '-'.$temp3str1.$temp3str2.".".$temp3str3;
				 	 	}
			 		}else{
			 			if($temp3str1 == 0){
					    $temp3 = $temp3str2.".".$temp3str3;
				 	 	}else{
				 	 	 	$temp3 = $temp3str1.$temp3str2.".".$temp3str3;
				 	 	}
			 		}
			    	//var_dump('temp3:'.$temp3);
						$acc_add=array(
				  				'psn'=>$dev_psn,
				  				'psnid'=>$psnid,
						  		'devid'=>$snint,
						  		'temp1'=>$temp1,
						  		'temp2'=>$temp2,
						  		'env_temp'=>$temp3,
						  		'sign'=>$sign,
						  		'cindex'=>$cindex,
						  		'lcount'=>$lcount,
						  		'delay'=>$delay,
						  		'time' =>$up_time,
						  		'sid' =>$sid,
						  	);
						$dev_save=array(
									'devid'=>$snint,
									'psn'=>$dev_psn,
									'psnid'=>$psnid,
									'battery'=>$battery,
						  	 	'dev_state'=>$state,
						  	 	'version'=>$cvs);
						  	 	
						$accadd_list[]=$acc_add;
						$devsave_list[]=$dev_save;
		
					}else{
							if($j==0){
						    $temp1str1 = substr($tempstr,3,1);
					  		$temp1str2 = substr($tempstr,0,1);
					    	$temp1str3 = substr($tempstr,1,1);
					    	$temp1int =base_convert($temp1str1,16,10);

					    	$temp2str1 = substr($tempstr,4,1);
					  		$temp2str2 = substr($tempstr,5,1);
					    	$temp2str3 = substr($tempstr,2,1);
					    	
					    	$temp3str1 = substr($tempstr,9,1);
					  		$temp3str2 = substr($tempstr,6,1);
					    	$temp3str3 = substr($tempstr,7,1);
				    	}else if($j==1){
				    		$temp1str1 = substr($tempstr,10,1);
					  		$temp1str2 = substr($tempstr,11,1);
					    	$temp1str3 = substr($tempstr,8,1);
					    	$temp1int =base_convert($temp1str1,16,10);
					    	
					    	$temp2str1 = substr($tempstr,15,1);
					  		$temp2str2 = substr($tempstr,12,1);
					    	$temp2str3 = substr($tempstr,13,1);	
					    	
					    	$temp3str1 = substr($tempstr,16,1);
					  		$temp3str2 = substr($tempstr,17,1);
					    	$temp3str3 = substr($tempstr,14,1);	
				    	}else if($j==2){
						    $temp1str1 = substr($tempstr,21,1);
					  		$temp1str2 = substr($tempstr,18,1);
					    	$temp1str3 = substr($tempstr,19,1);
					    	$temp1int =base_convert($temp1str1,16,10);
					    	
					    	$temp2str1 = substr($tempstr,22,1);
					  		$temp2str2 = substr($tempstr,23,1);
					    	$temp2str3 = substr($tempstr,20,1);
					    	
					    	$temp3str1 = substr($tempstr,27,1);
					  		$temp3str2 = substr($tempstr,24,1);
					    	$temp3str3 = substr($tempstr,25,1);					    	
				    	}else if($j==3){
				    		$temp1str1 = substr($tempstr,28,1);
					  		$temp1str2 = substr($tempstr,29,1);
					    	$temp1str3 = substr($tempstr,26,1);
					    	$temp1int =base_convert($temp1str1,16,10);

					    	$temp2str1 = substr($tempstr,33,1);
					  		$temp2str2 = substr($tempstr,30,1);
					    	$temp2str3 = substr($tempstr,31,1);	

					    	$temp3str1 = substr($tempstr,34,1);
					  		$temp3str2 = substr($tempstr,35,1);
					    	$temp3str3 = substr($tempstr,32,1);						    	
				    	}

			    if(($temp1int&0x08)==0x08){
		    		$temp1str1=$temp1int&0x07;
		    		if($temp1str1==0){
		    			$temp1 = '-'.$temp1str2.".".$temp1str3;
		    		}else{
		    			$temp1 =  '-'.$temp1str1.$temp1str2.".".$temp1str3;
		    		}
		    	}else{
			    		if($temp1str1==0){
			    			$temp1 = $temp1str2.".".$temp1str3;
			    		}else{
			    			$temp1 = $temp1str1.$temp1str2.".".$temp1str3;
			    		}
		    	}

				  //var_dump('temp1:'.$temp1);
					if($temp2str1 == 0){
				    $temp2 = $temp2str2.".".$temp2str3;
			 	 	}else{
			 	 	 	$temp2 = $temp2str1.$temp2str2.".".$temp2str3;
			 	 	}
			    //var_dump('temp2:'.$temp2);
			    if($temp3str1 == 0){
				    $temp3 = $temp3str2.".".$temp3str3;
			 	 	}else{
			 	 	 	$temp3 = $temp3str1.$temp3str2.".".$temp3str3;
			 	 	}
			    //var_dump('temp3:'.$temp3);
			    
				  	$acc_add2=array(
				   	  'psn'=>$dev_psn,
				   	  'psnid'=>$psnid,
				  		'devid'=>$snint,
				  		'temp1'=>$temp1,
				  		'temp2'=>$temp2,
			  			'env_temp'=>$temp3,
			  			'sign'=>$sign,
				  		'cindex'=>$cindex,
				  		'lcount'=>$lcount,
				  		'delay'=>$delay,
				  		'time' =>$up_time,
				  		'sid' =>$sid,
				  	);

						$dev_save2=array(
														'devid'=>$snint,
														'psn'=>$dev_psn,
														'psnid'=>$psnid,
														'battery'=>$battery,
											  	 	'dev_state'=>$state,
											  	 	'version'=>$cvs);

						$accadd_list2[]=$acc_add2;
						$devsave_list[]=$dev_save2;
					}
				}
				//var_dump($temp1);
	    	//var_dump($temp2);
	    }
  	}

  	$mydb='access_'.$psn;
    $user=D($mydb);
		//$access1=$user->addAll($accadd_list);
    		
    $user2=D('taccess');
		//$access2=$user2->addAll($accadd_list2);
		//dump($user->getlastsql());
		//dump("acc add 1:");
		//dump($access1);

		$user3=D('device');
		//$ret=$user3->addAll($rfid_list);
		//dump($rfid_list);
		
		foreach($cur_devs as $dev){
			$devid = $dev['devid'];
			$dev_psn =$dev['psn'];
			$battery= $dev['battery'];
			$dev_state= $dev['dev_state'];
			$version= $dev['version'];
			foreach($devsave_list as $devsave){
				if($devid==$devsave['devid']){
					if($battery!=$devsave['battery']){
						$mysave['battery']=$devsave['battery'];
					}
					if($dev_state!=$devsave['dev_state']){
						$mysave['dev_state']=$devsave['dev_state'];
					}
					if($version!=$devsave['version']){
						$mysave['version']=$devsave['version'];
					}
					if(!empty($mysave)){
						//$dev1=D('device')->where(array('devid'=>$devid,'psn'=>$dev_psn))->save($mysave);
						//$dev1=D('device')->save($mysave);
						//dump($mysave);
					}
				}
			}
		}



  	foreach($re_devs as $redev){
  			$devid_tmp=$redev['devid'];
  			foreach($devbuf as $devre){
  				if($devre==$devid_tmp){
						$devres[]=$devre;
						break;
  				}
  			}
  	}

  	foreach($re_devs2 as $redev){
  			$devid_tmp=$redev['devid'];
  			foreach($devbuf as $devre){
  				if($devre==$devid_tmp){
						$devres2[]=$devre;
						break;
  				}
  			}
  	}

		$devres_count=count($devres);
		$devres_count=str_pad($devres_count,2,'0',STR_PAD_LEFT);
		$devres_str=$devres_count.'';
		foreach($devres as $devre_id){
				//$string=base_convert($devre_id, 10, 16);
				$devre_id=str_pad($devre_id,4,'0',STR_PAD_LEFT);
				$devres_str=$devres_str.$devre_id;
		}

		if(!empty($devres)){
			$whereredev['devid']=array('in',$devres);
			//$dev1=D('device')->where($whereredev)->where(array('psn'=>$psn))->save(array(re_flag=>2));
		}

		if(!empty($devres2)){
			$whereredev2['devid']=array('in',$devres2);
			//$dev1=D('device')->where($whereredev2)->where(array('psn'=>$psn))->save(array(re_flag=>3));
		}
		
		if($crc==$sum){
			$header="OK1".date('YmdHis');
		}else{
			$header="OK2".date('YmdHis');
		}
		$body=$header.$delay_time.$rate.$change_str.$footer.$devres_str;


		echo $body;
		exit;
	}
}