<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
*
*/
class Crud extends CI_Model {

    public function create($table, $data)
    {
        $qry = $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function check($table, $params)
    {
        $this->db->where($params);
        return $this->db->get($table);
    }

    public function where($table, $key)
    {
        if (!empty($key['id'])) {
            $this->db->where($key);
        }
        $query = $this->db->get($table);
        if( $query->num_rows() > 0 ){
            if( !empty($key['id']) ){
                return $query->row_array();
            }else{
                return $query->result_array();  
            }
            
        }
    }

    public function orwhere($table, $key)
    {
        $this->db->where('username', $key['username']);
        $this->db->or_where('status', $key['status']);
        $qry = $this->db->get($table);
        if ($qry->num_rows() > 0) {
            $return = true;
        }else{
            $return = false;
        }
        return $return;
    }

    public function update($table, $key, $data)
    {
        $this->db->where($key);
        return $this->db->update($table, $data);
    }

    public function delete($table, $key)
    {
        $this->db->where($key);
        return $this->db->delete($table);
    }

    public function _orwhere($table, $key)
    {
        $this->db->where('username', $key['username']);
        $this->db->or_where('nomor_hp', $key['nomor_hp']);
        $this->db->or_where('email', $key['email']);
        $qry = $this->db->get($table);
        if ($qry->num_rows() > 0) {
            $return = $qry->result_array();
        }else{
            $return = false;
        }
        return $return;
    }

    public function whereLike($table, $key, $like)
    {
        $this->db->like($like);
        $this->db->where($key);
        $query = $this->db->get($table);
        if( $query->num_rows() > 0 ){
            return $query->result_array();
        }else{
            return false;  
        }
    }

    public function login($table, $key)
    {
        $this->db->where($key);
        $query = $this->db->get($table);
        if( $query->num_rows() > 0 ){
            return $query->result_array();  
        }else{
            return $query->row_array();
        }
    }
    
    public function cekExists($table, $key)
    {
        $this->db->where($key);
        $query = $this->db->get($table);
        if( $query->num_rows() > 0 ){
            return true;
        }else{
            return false;  
        }
    }
}

?>