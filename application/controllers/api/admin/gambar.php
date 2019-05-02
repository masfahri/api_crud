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
      $this->load->model('Crud');
      date_default_timezone_set('Asia/Jakarta');
   }

   public function index_post()
   {
      $action = $this->post('action');
      $id = $this->post('id');
      if ($this->is_logged_in() != null){
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
                     $data = $this->Crud->where('kategori', $globalId);
                     if (!$data) {
                        $output['error'] = 'Kategori Tidak Ada!';
                     }else{
                        if ($data['gambar_kategori'] != 'null') {
                           $del = array(
                              'path' => '___/upload/produk/GambarKategori/', 
                              'gambar' => json_decode($data['gambar_kategori'])->gambar0
                           );
                           $unlink = $this->_doDeleteFile($del);
                        }
                        $upload = $this->_doUpload($image_path, $data['nama_kategori'], 'produk/GambarKategori');
                        if ($upload) {
                           $dataUpdate = array('gambar_kategori' => $upload);
                           $this->crud->update('kategori', $globalId, $dataUpdate);
                           $output['data'] = $data;
                        }else{
                           $output['error'] = 'Something Wrong!';
                           return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST); 
                        }
                     }
                  break;
                  
                  case 'vendor':
                     $data = $this->Crud->where('vendor', $globalId);
                     if (!$data) {
                        $output['error'] = 'Vendor Tidak Ada!';
                     }else{
                        if ($data['gambar_vendor'] != 'null') {
                           $del = array(
                              'path' => '___/upload/produk/GambarVendor/', 
                              'gambar' => json_decode($data['gambar_vendor'])->gambar0
                           );
                           $unlink = $this->_doDeleteFile($del);
                        }
                        $upload = $this->_doUpload($image_path, $data['nama_vendor'], 'produk/GambarVendor/'.$data['nama_vendor']);
                        if ($upload) {
                           $dataUpdate = array('gambar_vendor' => $upload);
                           $this->crud->update('vendor', $globalId, $dataUpdate);
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