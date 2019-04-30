<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OTP
{

    public function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('crypt');
        $this->load->helper('otp');
        $this->load->model('Crud');
        date_default_timezone_set('Asia/Jakarta');
    } 

    public static function generatePamarsOtp($params)
    {
        $CI =& get_instance();
        $CI->load->model('crud');
        $rand = rand(6,9);

        for ($j=0; $j < strlen(substr($params['uname'], 2)); $j++) { 
            $unames[] = (int)substr($params['uname'], 2)[$j];
            $dates[] = array_map('intval', str_split($params['date']))[$j];
            $otp[] = self::generateOtp($unames[$j], $dates[$j]);
        }     

        $count = count($otp);
        for ($i=0; $i < count($otp); $i++) { 
            $code[] = $otp[$i]['code'];
            $enc[] = $otp[$i]['enc'];
        }

        $tokenData['enc'] = json_encode($enc);
        $tokenData['timestamp'] = $params['date'];
        $tokenData['encrypt'] = $code[$rand];
        $output = Crypt::encrypt_($tokenData);
        
        $data = array(
            'username' => $params['uname'], 
            'otp' => $code[$rand], 
            'enc' => json_encode($enc), 
            'encrypt' => $output, 
        );
        $insert = $CI->Crud->create('otp', $data);
        
        
        $return = array(
            'otp' => $code[$rand],
            'token' => $output
        );
        return $return;   
    }
    
    public static function validateParamsOtp($uname, $key)
    {
        for ($j=0; $j < count(json_decode($uname)); $j++) { 
            $dates[] = array_map('intval', str_split($key))[$j];
            $unames[] = (int)json_decode($uname)[$j];
            $otp[] = self::validateOtp($unames[$j], $dates[$j]);
        }   
        if (!$otp) {
            return false;
        }else{
            $validate = self::validateTimeStamp($key);
            if ($validate) {
                $return['otp'] = implode("", $otp);
                $return['validate'] = $validate;
            }
            return $return;
        }
    }

    public static function validateOtp($uname, $key)
    {
        $array[] = array();
        $output = ($uname - $key) % 26;
        $return =+ $output;
        
        return $return;
    }

    public static function validateTimeStamp($timeout)
	{
        $CI =& get_instance();
		if ((now() - $timeout < 120)) {
            return true;
        }else{
            return false;
        }
	}

    public static function generateOtp($uname, $key)
    {
        $output = ($uname + $key) % 26;
        $return = (
            (ord($output + 0) & 0x7f) << 24 |
            (ord($output + 1) & 0x7f) << 16 |
            (ord($output + 2) & 0x7f) << 8 |
            (ord($output + 3) & 0x7f)
        ) % pow(10, 6);
        $data = array(
            'enc' => $output, 
            'code' => $return);

        return $data;
    }
}

?>
