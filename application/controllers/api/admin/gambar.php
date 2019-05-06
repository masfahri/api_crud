<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Gambar extends REST_Controller {

   public function __construct($config='rest') 
   {
      parent::__construct($config);
      $this->load->library('session');
      $this->load->library('form_validation');
      $this->load->helper(array('crypt', 'jwt', 'form', 'url', 'file'));
      $this->load->model(array('Crud', 'Mproduk'));
      date_default_timezone_set('Asia/Jakarta');
   }

   public function index_post()
   {
      $action = $this->post('action');
      $id = $this->post('id');
      $crypt['token'] = $this->session->userdata('token');
      
      if ($this->is_logged_in(Crypt::decrypt_($crypt)) != null){
         if($this->form_validation->run('gambar') == FALSE) {
            $output = $this->form_validation->error_array();
            $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
         }else{
            $arrayName[] = array();
            $image_path = null;
            for ($i=0; $i < 10; $i++) { 
               if (!isset($_FILES['gambar'.$i])) {
                  $_FILES['gambar'.$i] = null;
               }
               if (isset($_FILES['gambar'.$i])) {
                  $image_path[] = $_FILES['gambar'.$i];
                  $arrayName = $_FILES['gambar'.$i];
               }
            }
            if ($image_path == null) {
               $output['error'] = 'Silahkan Masukan Gambar!';
               $output['upload'] = 'null';
            }else{
               $globalId = array('id' => $id);
               switch ($action) {
                  case 'kategori':
                     $cek = $this->Crud->where('kategori', $globalId);
                     if (!$cek) {
                        $output['error'] = 'Kategori Tidak Ada!';
                     }else{
                        if ($cek['gambar_kategori'] != 'null') {
                           $del = array(
                              'path' => '___/upload/produk/GambarKategori/', 
                              'gambar' => json_decode($cek['gambar_kategori'])
                           );
                           $unlink = $this->_doDeleteFile($del);
                        }
                        $upload = $this->_doUpload($image_path, $cek['nama_kategori'], 'produk/GambarKategori');
                        if ($upload) {
                           $dataUpdate = array('gambar_kategori' => $upload);
                           $this->crud->update('kategori', $globalId, $dataUpdate);
                           $data = $this->Crud->where('kategori', $globalId);
                           $output['data'] = $data;
                        }else{
                           $output['error'] = 'Something Wrong!';
                           return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST); 
                        }
                     }
                  break;
                  
                  case 'vendor':
                     $cek = $this->Crud->where('vendor', $globalId);
                     if (!$cek) {
                        $output['error'] = 'vendor Tidak Ada!';
                     }else{
                        if ($cek['gambar_vendor'] != 'null') {
                           $del = array(
                              'path' => '___/upload/produk/GambarVendor/', 
                              'gambar' => json_decode($cek['gambar_vendor'])
                           );
                           $unlink = $this->_doDeleteFile($del);
                        }
                        $upload = $this->_doUpload($image_path, $cek['nama_vendor'], 'produk/GambarVendor');
                        if ($upload) {
                           $dataUpdate = array('gambar_vendor' => $upload);
                           $this->crud->update('vendor', $globalId, $dataUpdate);
                           $data = $this->Crud->where('vendor', $globalId);
                           $output['data'] = $data;
                        }else{
                           $output['error'] = 'Something Wrong!';
                           return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST); 
                        }
                     }
                  break;

                  case 'produk':
                     $cek = $this->Crud->where('produk', $globalId);
                     if (!$cek) {
                        $output['error'] = 'Produk Tidak Ada!';
                     }else{
                        if ($cek['gambar_produk'] != 'null') {
                           $del = array(
                              'path' => '___/upload/produk/GambarProduk/', 
                              'gambar' => json_decode($cek['gambar_produk'])->gambar0
                           );
                           $unlink = $this->_doDeleteFile($del);
                        }
                        $upload = $this->_doUpload($image_path, $cek['nama_produk'], 'produk/GambarProduk/'.$cek['nama_produk']);
                        if ($upload) {
                           $dataUpdate = array('gambar_produk' => $upload);
                           $this->crud->update('produk', $globalId, $dataUpdate);
                           $data = $this->Crud->where('produk', $globalId);
                           $output['data'] = $data;
                        }else{
                           $output['error'] = 'Something Wrong!';
                           return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST); 
                        }
                     }
                  break;
                  
                  case 'detail_produk':
                     $condition = array('produk.id' => $id);
                     $prodId = array('produk_id' => $id);
                     $select = array(
                        'produk.id as produkId',
                        'satuan_produk', 
                        'harga_produk', 
                        'stok_produk', 
                        'deskripsi_produk_long', 
                        'gambar_thumb_detail_produk', 
                        'gambar_detail_produk',
                        'nama_produk'
                     );
                     $join = array(
                         'join' => 'produk.id= detail_produk.produk_id', 
                         'tableJoin' => 'detail_produk'
                     );
                     $cek =  $this->Mproduk->getDetailBarang('produk', $condition, $join, $select);
                     if (!$cek) {
                        $output['error'] = 'Produk Tidak Ada!';
                     }else{
                        if ($cek['gambar_detail_produk'] != 'null') {
                           $del = array(
                              'path' => '___/upload/produk/GambarProduk/'.$cek['nama_produk'].'/Detail/', 
                              'gambar' => json_decode($cek['gambar_detail_produk'])
                           );
                           $unlink = $this->_doDeleteFile($del);
                        }
                        $upload = $this->_doUpload($image_path, $cek['nama_produk'], 'produk/GambarProduk/'.$cek['nama_produk'].'/Detail');
                        if ($upload) {
                           $dataUpdate = array('gambar_detail_produk' => $upload);
                           $this->crud->update('detail_produk', $prodId, $dataUpdate);
                           $data = $this->Crud->where('detail_produk', $prodId);
                           $output['data'] = $data;
                        }else{
                           $output['error'] = 'Something Wrong!';
                           return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST); 
                        }
                     }
                  break;


                  
                  default:
                     # code...
                     break;
               }
            }
         }
         return $this->set_response($output, REST_Controller::HTTP_OK); 
      } else {
         return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST); 
      }
       

   }
}

    ?>