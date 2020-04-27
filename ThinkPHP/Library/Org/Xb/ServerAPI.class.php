<?php
namespace Org\Xb;
/**
 * ��������server API ��ʵ��
 * Class ServerAPI
 * @author  chensheng dengyuan
 * @created date    2018-02-02  13:45
 *
 *
 ***/

class ServerAPI
{
    public $AppKey;                //������ƽ̨�����AppKey
    public $AppSecret;             //������ƽ̨�����AppSecret,��ˢ��
    public $Nonce;                    //���������󳤶�128���ַ���
    public $CurTime;                 //��ǰUTCʱ�������1970��1��1��0��0 ��0 �뿪ʼ�����ڵ�����(String)
    public $CheckSum;                //SHA1(AppSecret + Nonce + CurTime),��������ƴ�ӵ��ַ���������SHA1��ϣ���㣬ת����16�����ַ�(String��Сд)
    const HEX_DIGITS = "0123456789abcdef";

    /**
     * ������ʼ��
     * @param $AppKey
     * @param $AppSecret
     * @param $RequestType [ѡ��php����ʽ��fsockopen��curl,��Ϊcurl��ʽ������php�����Ƿ���]
     */
    public function __construct($AppKey, $AppSecret, $RequestType = 'curl')
    {
        $this->AppKey = $AppKey;
        $this->AppSecret = $AppSecret;
        $this->RequestType = $RequestType;
    }

    /**
     * API checksumУ������
     * @param  void
     * @return $CheckSum(����˽������)
     */
    public function checkSumBuilder()
    {
        //�˲�����������ַ���
        $hex_digits = self::HEX_DIGITS;
        $this->Nonce;
        for ($i = 0; $i < 128; $i++) {            //����ַ������128���ַ���Ҳ����С�ڸ���
            $this->Nonce .= $hex_digits[rand(0, 15)];
        }
        $this->CurTime = (string)(time());    //��ǰʱ���������Ϊ��λ

        $join_string = $this->AppSecret . $this->Nonce . $this->CurTime;
        $this->CheckSum = sha1($join_string);
        //print_r($this->CheckSum);
    }

    /**
     * ��json�ַ���ת����php����
     * @param  $json_str
     * @return $json_arr
     */
    public function json_to_array($json_str)
    {

        if (is_array($json_str) || is_object($json_str)) {
            $json_str = $json_str;
        } else if (is_null(json_decode($json_str))) {
            $json_str = $json_str;
        } else {
            $json_str = strval($json_str);
            $json_str = json_decode($json_str, true);
        }
        $json_arr = array();
        foreach ($json_str as $k => $w) {
            if (is_object($w)) {
                $json_arr[$k] = $this->json_to_array($w); //�ж������ǲ���object
            } else if (is_array($w)) {
                $json_arr[$k] = $this->json_to_array($w);
            } else {
                $json_arr[$k] = $w;
            }
        }
        return $json_arr;
    }

    /**
     * ʹ��CURL��ʽ����post����
     * @param  $url     [�����ַ]
     * @param  $data    [array��ʽ����]
     * @return $���󷵻ؽ��(array)
     */
    public function postDataCurl($url, $data)
    {
        $this->checkSumBuilder();       //��������ǰ��������checkSum

        $timeout = 5000;
        $http_header = array(
            'AppKey:' . $this->AppKey,
            'Nonce:' . $this->Nonce,
            'CurTime:' . $this->CurTime,
            'CheckSum:' . $this->CheckSum,
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8'
        );
        //print_r($http_header);

        // $postdata = '';
        $postdataArray = array();
        foreach ($data as $key => $value) {
            array_push($postdataArray, $key . '=' . urlencode($value));
        }
        $postdata = join('&', $postdataArray);

        // var_dump($postdata);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //����http֤������
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (false === $result) {
            $result = curl_errno($ch);
        }
        curl_close($ch);
        return $this->json_to_array($result);
    }


    /**
     * ʹ��FSOCKOPEN��ʽ����post����
     * @param  $url     [�����ַ]
     * @param  $data    [array��ʽ����]
     * @return $���󷵻ؽ��(array)
     */
    public function postDataFsockopen($url, $data)
    {
        $this->checkSumBuilder();//��������ǰ��������checkSum

        $postdata = '';
        foreach ($data as $key => $value) {
            $postdata .= ($key . '=' . urlencode($value) . '&');
        }
        // building POST-request:
        $URL_Info = parse_url($url);
        if (!isset($URL_Info["port"])) {
            $URL_Info["port"] = 80;
        }
        $request = '';
        $request .= "POST " . $URL_Info["path"] . " HTTP/1.1\r\n";
        $request .= "Host:" . $URL_Info["host"] . "\r\n";
        $request .= "Content-type: application/x-www-form-urlencoded;charset=utf-8\r\n";
        $request .= "Content-length: " . strlen($postdata) . "\r\n";
        $request .= "Connection: close\r\n";
        $request .= "AppKey: " . $this->AppKey . "\r\n";
        $request .= "Nonce: " . $this->Nonce . "\r\n";
        $request .= "CurTime: " . $this->CurTime . "\r\n";
        $request .= "CheckSum: " . $this->CheckSum . "\r\n";
        $request .= "\r\n";
        $request .= $postdata . "\r\n";

        print_r($request);
        $fp = fsockopen($URL_Info["host"], $URL_Info["port"]);
        fputs($fp, $request);
        $result = '';
        while (!feof($fp)) {
            $result .= fgets($fp, 128);
        }
        fclose($fp);

        $str_s = strpos($result, '{');
        $str_e = strrpos($result, '}');
        $str = substr($result, $str_s, $str_e - $str_s + 1);
        print_r($result);
        return $this->json_to_array($str);
    }

    /**
     * ���Ͷ�����֤��
     * @param  $templateid    [ģ����(�ɿͷ�����֮���֪������)]
     * @param  $mobile       [Ŀ���ֻ���]
     * @param  $deviceId     [Ŀ���豸�ţ���ѡ����]
     * @return $codeLen      [��֤�볤��,��Χ4��10��Ĭ��Ϊ4]
     */
    public function sendSmsCode($templateid, $mobile, $deviceId = '', $codeLen)
    {
        $url = 'https://api.netease.im/sms/sendcode.action';
        $data = array(
            'templateid' => $templateid,
            'mobile' => $mobile,
            'deviceId' => $deviceId,
            'codeLen' => $codeLen
        );
        if ($this->RequestType == 'curl') {
            $result = $this->postDataCurl($url, $data);
        } else {
            $result = $this->postDataFsockopen($url, $data);
        }
        return $result;
    }



    /**
     * ����ģ�����
     * @param  $templateid       [ģ����(�ɿͷ�����֮���֪������)]
     * @param  $mobiles          [��֤��]
     * @param  $params          [���Ų����б������������ģ�壬JSONArray��ʽ����["xxx","yyy"];���ڲ�����������ģ�壬����˲�����ʾģ�弴����ȫ������]
     * @return $result      [����array�������]
     */
    public function sendSMSTemplate($templateid, $mobiles = array(), $params = '')
    {
        $url = 'https://api.netease.im/sms/sendtemplate.action';
        $data = array(
            'templateid' => $templateid,
            'mobiles' => json_encode($mobiles),
            'params' => json_encode($params)
        );
        //dump($data);
        if ($this->RequestType == 'curl') {
            $result = $this->postDataCurl($url, $data);
        } else {
            $result = $this->postDataFsockopen($url, $data);
        }
        return $result;
    }


}

?>