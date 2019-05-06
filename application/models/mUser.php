<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Muser extends CI_Model {

    public function wheres($table, $params)
    {
        // var_dump($params);die;
        foreach ($params as $key => $keys ) {
            $this->db->where($key, $keys);
        }
        $query = $this->db->get($table);
        if( $query->num_rows() > 0 ){
            return $query->row_array();
        }else{
            return $query->result_array();  
            
        }
    }

}

/* End of file Muser.php */



?>