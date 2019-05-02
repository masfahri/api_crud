<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/smsgateway/autoload.php';
use SMSGatewayMe\Client\ApiClient;
use SMSGatewayMe\Client\Configuration;
use SMSGatewayMe\Client\Api\MessageApi;
use SMSGatewayMe\Client\Model\SendMessageRequest;

class Auth extends REST_Controller {

    public function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('crypt');
        $this->load->helper('otp');
        $this->load->model('Crud');
        date_default_timezone_set('Asia/Jakarta');
    }  

    public function login_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');
        if($this->form_validation->run('login') == FALSE) {
            $output = $this->form_validation->error_array();
            $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $like = array(
                'username' => $username, 
                'nomor_hp' => $username, 
                'email' => $username, 
            );
            $data = $this->Crud->_orwhere('users', $like);
            if (!$data) {
                $output = 'Tidak ada user';
                $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
            }else{
                $pass = array('password' => $password);
                $cek = $this->Crud->login('users', $pass);
                if (!$cek) {
                    $output = 'Salah Password';
                    $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
                }else{
                    $output['data'] = $this->Crud->_orwhere('users', $like);
                    $insertData = array(
                        'username' => $output['data'][0]['nomor_hp'], 
                        'status'   => 'login', 
                        'created_at' => date('Y-m-d H:i:s'), 
                        'updated_at' => date('Y-m-d H:i:s'), 
                    );
                    $cekData = array(
                        'username' => $output['data'][0]['username'], 
                        'status'   => 'login', 
                    );
                    $cek = $this->Crud->orwhere('log', $cekData);
                    if (!$cek) {
                        $insert = $this->Crud->create('log', $insertData);
                        if ($insert) {
                            $dateOtp = $this->getTimeLogin($output['data'][0]['username']);
                            $dataOtpGenerate = array(
                                'date' => $dateOtp, 
                                'uname' => $output['data'][0]['nomor_hp']);
                            $otpGenerate = OTP::generatePamarsOtp($dataOtpGenerate);
                            $output['code'] = $otpGenerate; 
                            if (!empty($output['code'])) {
                                // $this->sendSms($output['otp'], $output['data'][0]['nomor_hp']);
                                // KALO BISA SIMPAN NOMOR_HP dan USERNAME PADA SESSION
                            }
                        }
                    }else{
                        $output = 'Maaf anda sedang login di Device Lain';
                    }
                }
                $this->set_response($output, REST_Controller::HTTP_OK);
            }
        }
    }

    public function otp_post()
    {
        $otp = $this->post('otp');
        $username = $this->post('username');
        $where = array('username' => $username);
        $data = $this->Crud->where('users', $where);
        if($this->form_validation->run('otp') == FALSE) {
            $output = $this->form_validation->error_array();
            $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $like = array('otp' => $otp);
            $key = array('username' => $data[0]['nomor_hp']);

            $cek = $this->Crud->whereLike('otp', $key, $like);
            if (!$cek) {
                $output['message'] = 'Salah OTP';
            }else{
                $arrToken = array('token' => $cek[0]['encrypt']);
                $decrypt = Crypt::decrypt_($arrToken);
                $validate = OTP::validateParamsOtp($decrypt->enc, $decrypt->timestamp);
                if ('08'.$validate['otp'] != $username) {
                    $output['message'] = 'OTP EXPIRED';
                }else{
                    $output['message'] = 'SUKSES MASUK';
                    $output['data'] = $data[0];
                    $this->session->set_userdata('nomor_hp', $username);
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
            $cek = $this->crud->cekExists('log', $data);
            if (!$cek) {
                return false;
            }else{
                return true;
            }
        }
    }
}