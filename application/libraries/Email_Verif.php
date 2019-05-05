<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/smsgateway/autoload.php';
use SMSGatewayMe\Client\ApiClient;
use SMSGatewayMe\Client\Configuration;
use SMSGatewayMe\Client\Api\MessageApi;
use SMSGatewayMe\Client\Model\SendMessageRequest;
class Email_Verif {

    

    public static function sendVerif($params)
    {
        $CI =& get_instance();
        $CI->load->library('email');
        $config['protocol'] = "smtp";
        $config['smtp_host'] = 'ssl://smtp.gmail.com';
        $config['smtp_port'] = "465";
        $config['smtp_user'] = "hsevfakhri@gmail.com";
        $config['smtp_pass'] = "hardjump123";
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";
        $CI->email->initialize($config);
        $CI->email->from('hsevfakhri@gmail.com', 'Admin');
        $list = array($params);
        $CI->email->to($params[0]);
        $CI->email->subject('Aktivasi Akun');
        $CI->email->message('silahkan klik <a href="http://localhost:8080/crud/api/regist/Aktivasi?token='.$params[1].'">link</a> Untuk melakukan Aktivasi ');
        if ($CI->email->send()) {
            return $output = "Kode Aktivasi Terkirim";
        } else {
            return $output['error'] = show_error($CI->email->print_debugger());
        }
        
    }


}