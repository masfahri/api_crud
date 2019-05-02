<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Barang extends REST_Controller {

    public function __construct($config='rest') 
    {
        parent::__construct($config);
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(array('crypt', 'jwt', 'form', 'url', 'file'));
        $this->load->model('Crud');
        date_default_timezone_set('Asia/Jakarta');
    }

   public function index_post() {
      $nama_kategori = $this->post('nama_barang');
      if ($this->is_logged_in() != null){
         if($this->form_validation->run('create_kategori') == FALSE) {
            $output = $this->form_validation->error_array();
            $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
         } else {
            $insertData = array(
               'nama_kategori' => $nama_kategori,
               'gambar_kategori' => 'null');
            if (!$this->Crud->create('kategori', $insertData)) {
               return $this->set_response($error, REST_Controller::HTTP_BAD_REQUEST);       
            }else{
               return $this->set_response('Sukses Menambahkan Kategori', REST_Controller::HTTP_BAD_REQUEST);       
            }
         }
      }else{
         return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST); 
      }
   }

   public function index_get()
   {
      $id_kategori = $this->get('id');
      $condition = array('id' => $id_kategori);
      $data = $this->Crud->where('kategori', $condition);
      if ($this->is_logged_in() != null){
         if (!$data == FALSE) {
            $return =  $this->set_response($data, REST_Controller::HTTP_OK); 
         }else{
            $return =  $this->set_response('Kategori Gak Ada', REST_Controller::HTTP_BAD_REQUEST); 
         }
         return $return;
      } return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST);
   }

   public function index_put()
   {
      $id_kategori = $this->put('id_kategori');
      $nama_kategori = $this->put('nama_kategori');
      $gambar_cover = 'gambar';

      $config = [
         [
            'field'     => 'id_kategori',
            'label'     => 'Id Kategori',
            'rules'     => 'required',
            'errors'    => [
                'required' => '%s Diperlukan',
            ]
         ],
         [
            'field'     => 'nama_kategori',
            'label'     => 'Nama Kategori',
            'rules'     => 'required|is_unique[kategori.nama_kategori]',
            'errors'    => [
                'required' => '%s Diperlukan',
                'is_unique' => '%s Suda Ada',
            ]
         ],
     ];

     $data = $this->put();
     $this->form_validation->set_data($data);
     $this->form_validation->set_rules($config);

      $condition = array('id' => $id_kategori);
      $data = $this->Crud->where('kategori', $condition);
      if ($this->is_logged_in() != null){
         if($this->form_validation->run() == FALSE) {
            $output = $this->form_validation->error_array();
            $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
         }else{
            if (!$data == FALSE) {
               $updateData = array('nama_kategori' => $nama_kategori);
               $key = array('id' => $id_kategori);
               if ($this->Crud->update('kategori', $key, $updateData)) {
                  $return =  $this->set_response('sukses update kategori', REST_Controller::HTTP_OK); 
               }
            }else{
               $return =  $this->set_response('Kategori Gak Ada', REST_Controller::HTTP_BAD_REQUEST); 
            }
            return $return;
         }
      }else{
         return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST); 
      } 
   }

   public function index_delete()
   {
      $id_kategori = $this->delete('id_kategori');
      $config = [
         [
            'field'     => 'id_kategori',
            'label'     => 'Id Kategori',
            'rules'     => 'required',
            'errors'    => [
                'required' => '%s Diperlukan',
            ]
         ],
      ];

     $data = $this->delete();
     $this->form_validation->set_data($data);
     $this->form_validation->set_rules($config);
      if ($this->is_logged_in() != null){
         if($this->form_validation->run() == FALSE) {
            $output = $this->form_validation->error_array();
            $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
         }else{
            $data = array('id' => $id_kategori);
            $cek = $this->crud->where('kategori', $data);
            if (!$cek) {
               return $this->set_response('Kategori Tidak Ada', REST_Controller::HTTP_BAD_REQUEST); 
            }else{
               if ($cek['gambar_kategori'] != 'null') {
                  $del = array(
                     'path' => '___/upload/produk/GambarKategori/', 
                     'gambar' => json_decode($cek['gambar_kategori'])->gambar0
                  );
                  $unlink = $this->_doDeleteFile($del);
               }
               $delete = $this->Crud->delete('kategori', $data);
               return $this->set_response('Sukses Delete Kategori', REST_Controller::HTTP_BAD_REQUEST); 
               
            }
            
         }
      }else{
         return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST); 
      }

   }

    public function index_patch()
    {
        $this->session->unset_userdata('nomor_hp');
    }

    public function exists_kategori($key) {
        $data = array('nama_kategori' => $key);
        $cekKategori = $this->Crud->count('kategori', $data);
        if ($cekKategori == false) {
           return true;
        }else{
           return false;
        }
    }

}
