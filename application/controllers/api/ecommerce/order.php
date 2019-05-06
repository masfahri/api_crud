<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
class Order extends REST_Controller {

    public function __construct($config='rest') 
    {
        parent::__construct($config);
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(array('crypt', 'jwt', 'form', 'url', 'file'));
        $this->load->model(array('Crud', 'Mproduk'));
        date_default_timezone_set('Asia/Jakarta');
    }

    public function (Type $var = null)
    {
        # code...
    }
}