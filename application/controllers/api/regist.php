<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/smsgateway/autoload.php';
use SMSGatewayMe\Client\ApiClient;
use SMSGatewayMe\Client\Configuration;
use SMSGatewayMe\Client\Api\MessageApi;
use SMSGatewayMe\Client\Model\SendMessageRequest;

class Regist extends REST_Controller {

    public function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->library('Email_Verif');
        $this->load->helper('crypt');
        $this->load->helper('otp');
        $this->load->model('Crud');
        date_default_timezone_set('Asia/Jakarta');
    }  

    public function index_post()
    {
        $nomor_hp = $this->post('nomor_hp');
        $email = $this->post('email');
        $_POST['status'] = 'regist';
        if($this->form_validation->run('regist') == FALSE) {
            $output = $this->form_validation->error_array();
            $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $insertData = array(
                'username' => $nomor_hp, 
                'status' => 'register', 
            );
            $insertUsers = array(
                'username' => $nomor_hp, 
                'password' => Crypt::encrypt_($nomor_hp), 
                'email' => $email, 
                'aktivasi' => 'regist',
                'role' => '5',

            ); 
            $log = $this->crud->create('log', $insertData);
            $insert = $this->Crud->create('users', $insertUsers);
            if ($insert) {
                $dateOtp = $this->getTimeLogin($nomor_hp);
                $dataOtpGenerate = array(
                    'date' => $dateOtp, 
                    'uname' => $nomor_hp);
                $otpGenerate = OTP::generatePamarsOtp($dataOtpGenerate);
                if (!empty($otpGenerate)) {
                    $params = array($email, $otpGenerate['token']);
                    $this->sendSms($otpGenerate['otp'], $nomor_hp);
                    
                    $this->session->set_userdata('token', $otpGenerate['token']);
                    
                    $output = Email_Verif::sendVerif($params);
                    // KALO BISA SIMPAN NOMOR_HP dan USERNAME PADA SESSION
                }
            }
            
        $this->set_response($output, REST_Controller::HTTP_OK);
        }
    }

    public function aktivasi_get()
    {
        $token = $this->get('token');
    }

    public function otp_post()
    {
        $otp = $this->post('otp');
        // $where = array('username' => $username);
        // $data = $this->Crud->where('users', $where);
        if($this->form_validation->run('otpRegist') == FALSE) {
            $output = $this->form_validation->error_array();
            $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $key = array('encrypt' => $this->session->userdata('token'), 'otp' => $otp);

            $cek = $this->Crud->wheres('otp', $key);
            if (!$cek) {
                $output['message'] = 'Salah OTP';
            }else{
                $arrToken = array('token' => $cek['encrypt']);
                $decrypt = Crypt::decrypt_($arrToken);
                $validate = OTP::validateParamsOtp($decrypt->enc, $decrypt->timestamp);
                if ('08'.$validate['otp'] != $cek['username']) {
                    $output['message'] = 'OTP EXPIRED';
                }else{
                    $output['message'] = 'SUKSES REGIST';
                    $output['data'] = $cek;
                    if ($output['data'] != null) {
                        $key = array('username' => $cek['username']);
                        $this->crud->delete('log' ,$key);
                        $updateArray = array(
                            'aktivasi' => 'pending', 
                        );
                        $this->crud->update('users', $key, $updateArray);
                        // var_dump($delete);die;
                    }
                    // $this->session->set_userdata('nomor_hp', $username);
                }
                $this->set_response($output, REST_Controller::HTTP_OK);
            }
            $this->set_response($output, REST_Controller::HTTP_OK);
        }
    }

    

    public function exists_user($params)
    {
        if (empty($params)) {
            return true;
        }else{
            $data = array(
                'username' => $params,
                'status' => 'login',
            );
            $cek = $this->crud->cekExists('otp', $data);
            if (!$cek) {
                return false;
            }else{
                return true;
            }
        }
    }
}