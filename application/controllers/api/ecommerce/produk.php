<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
class Produk extends REST_Controller {

    public function __construct($config='rest') 
    {
        parent::__construct($config);
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(array('crypt', 'jwt', 'form', 'url', 'file'));
        $this->load->model(array('Crud', 'Mproduk'));
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index_get()
    {
      $id_produk = $this->get('id_produk');
      $condition['produk'] = array('id' => $id_produk);
      $condition['detail_produk'] = array('produk.id' => $id_produk);
      $select['produk'] = array(
          'id', 
          'nama_produk', 
          'deskripsi_produk_short', 
          'thumb_produk', 
          'gambar_produk', 
          'kategori_id', 
          'vendor_id'
        );
      $select['detail'] = array(
          'satuan_produk', 
          'harga_produk', 
          'stok_produk', 
          'deskripsi_produk_long', 
          'gambar_thumb_detail_produk', 
          'gambar_detail_produk'
        );
      $data = $this->Mproduk->getBarang('produk', $condition['produk'], $select['produk']);
      $join = array(
          'join' => 'produk.id= detail_produk.produk_id', 
          'tableJoin' => 'detail_produk'
        );
         if (!$data == FALSE) {
            $data['detail'] =  $this->Mproduk->getDetailBarang('produk', $condition['detail_produk'], $join, $select['detail']);
            $return['produk'] = $data;
            $return =  $this->set_response($return, REST_Controller::HTTP_OK); 
         }else{
            $return =  $this->set_response('Produk Gak Ada', REST_Controller::HTTP_BAD_REQUEST); 
         }
         return $return;
    }

}

/* End of file Produk.php */



?>