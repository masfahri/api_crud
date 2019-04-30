<?php
class MY_Form_validation extends CI_Form_validation{    
     function __construct($config = array()){
          parent::__construct($config);
     }
     function exists_nomor_hp($params){
         var_dump($params);die;
        $data = array(
            'nomor_hp' => $params,
            'status' => 'login',
        );
        $cek = $this->crud->cekExists('log', $data);
     }
}
