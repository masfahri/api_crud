<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
*
*/
class Mproduk extends CI_Model {
    

    public function check($table, $key)
    {
        $this->db->where($key);
        $qry = $this->db->get($table);
        if ($qry->num_rows() > 0) {
            return true;
        }else{
            return false;
        }
    }

    public function getBarang($table, $initialId, $select)
    {
        $this->db->select($select);
        if (!empty($initialId['id'])) {
            $this->db->where($initialId);
        }
        $query = $this->db->get($table);
        if( $query->num_rows() > 0 ){
            if( !empty($initialId['id']) ){
                return $query->row_array();
            }else{
                return $query->result_array();  
            }
            
        }
    }

    public function whereDetail($table, $key)
    {
        if (!empty($key['produk_id'])) {
            $this->db->where($key);
        }
        $query = $this->db->get($table);
        if( $query->num_rows() > 0 ){
            if( !empty($key['produk_id']) ){
                return $query->row_array();
            }else{
                return $query->result_array();  
            }
            
        }
    }

    public function getDetailBarang($table, $initialId, $join, $select)
    {
        $this->db->select($select);
        $this->db->where($initialId);
        $this->db->join($join['tableJoin'], $join['join']);
        $qry = $this->db->get($table);
        if ($qry->num_rows() > 0) {
            return $qry->row_array();
        }else{
            return $qry->result_array();
        }
    }

    public function getDetailCartAll($table, $initialId, $join, $select)
    {
        $this->db->select($select);
        $this->db->where($initialId);
        foreach ($join as $key => $keys ) {
            $this->db->join($keys['tableJoin'], $keys['join']);
        }
        $qry = $this->db->get($table);
        if ($qry->num_rows() > 0) {
            return $qry->row_array();
        }else{
            return $qry->result_array();
        }
    }

    public function wheres($table, $params)
    {
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

    public function getCart($table, $initialId, $select)
    {
        $this->db->select($select);
        if (!empty($initialId['id'])) {
            $this->db->where($initialId);
        }
        $query = $this->db->get($table);
        if( $query->num_rows() > 0 ){
            if( !empty($initialId['id']) ){
                return $query->row_array();
            }else{
                return $query->result_array();  
            }
            
        }
    }
}