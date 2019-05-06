<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Vendor extends REST_Controller {

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
      $nama_vendor = $this->post('nama_vendor');
      $email_vendor = $this->post('email_vendor');
      $nomor_vendor = $this->post('nomor_vendor');
      $alamat_vendor = $this->post('alamat_vendor');
      if ($this->is_logged_in() != null){
         if($this->form_validation->run('create_vendor') == FALSE) {
            $output = $this->form_validation->error_array();
            $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
         } else {
            $insertData = array(
               'nama_vendor' => $nama_vendor,
               'email_vendor' => $email_vendor,
               'nomor_vendor' => $nomor_vendor,
               'alamat_vendor' => $alamat_vendor,
               'gambar_vendor' => 'null');
            if (!$this->Crud->create('vendor', $insertData)) {
               return $this->set_response($error, REST_Controller::HTTP_OK);       
            }else{
               return $this->set_response('Sukses Menambahkan Vendor', REST_Controller::HTTP_BAD_REQUEST);       
            }
         }
      }else{
         return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST); 
      }
   }

    public function index_put() {
        $id_vendor = $this->put('id_vendor');
        $nama_vendor = $this->put('nama_vendor');
        $email_vendor = $this->put('email_vendor');
        $nomor_vendor = $this->put('nomor_vendor');
        $alamat_vendor = $this->put('alamat_vendor');

        $config = [
            [
                'field'     => 'id_vendor',
                'label'     => 'Id vendor',
                'rules'     => 'required',
                'errors'    => [
                    'required' => '%s Diperlukan',
                ]
            ],
            [
                'field'     => 'email_vendor',
                'label'     => 'Email vendor',
                'rules'     => 'valid_email',
                'errors'    => [
                    'valid_email' => '%s Tidak Valid',
                ]
            ],
            [
                'field'     => 'nomor_vendor',
                'label'     => 'Nomor vendor',
                'rules'     => 'numeric|min_length[10]|max_length[13]',
                'errors'    => [
                    'numeric' => '%s Tidak Valid',
                    'min_length' => '%s Kurang Karakter',
                    'max_length' => '%s Kelebihan Karakter',
                ]
            ],
            [
                'field'     => 'alamat_vendor',
                'label'     => 'Alamat vendor',
                'rules'     => 'max_length[255]',
                'errors'    => [
                    'max_length' => '%s Kelebihan Karakter',
                ]
            ],
        ];

        $data = $this->put();
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules($config);
        
        $condition = array('id' => $id_vendor);
        $data = $this->Crud->where('vendor', $condition);
        if ($this->is_logged_in() != null){
            if($this->form_validation->run() == FALSE) {
                $output = $this->form_validation->error_array();
                $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
            }else{
                if (!$data == FALSE) {

                    if ($nama_vendor == '') {
                        $nama_vendor = $data['nama_vendor'];
                    }
                    if ($email_vendor == '') {
                        $email_vendor = $data['email_vendor'];
                    }
                    if ($nomor_vendor == '') {
                        $nomor_vendor = $data['nomor_vendor'];
                    }
                    if ($alamat_vendor == '') {
                        $alamat_vendor = $data['alamat_vendor'];
                    }

                    $updateData = array(
                        'nama_vendor' => $nama_vendor,
                        'email_vendor' => $email_vendor,
                        'nomor_vendor' => $nomor_vendor,
                        'alamat_vendor' => $alamat_vendor,
                    );
                    $key = array('id' => $id_vendor);
                    if ($this->Crud->update('vendor', $key, $updateData)) {
                        $return =  $this->set_response('sukses update Vendor', REST_Controller::HTTP_OK); 
                    }
                }else{
                    $return =  $this->set_response('Vendor Gak Ada', REST_Controller::HTTP_BAD_REQUEST); 
                }
                return $return;
            }
        }else{
            return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    }

   public function index_get()
   {
      $id_vendor = $this->get('id_vendor');
      $condition = array('id' => $id_vendor);
      $data = $this->Crud->where('vendor', $condition);
      if ($this->is_logged_in() != null){
         if (!$data == FALSE) {
            $return =  $this->set_response($data, REST_Controller::HTTP_OK); 
         }else{
            $return =  $this->set_response('Vendor Gak Ada', REST_Controller::HTTP_BAD_REQUEST); 
         }
         return $return;
      } return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST);
   }

   public function index_delete()
   {
      $id_vendor = $this->delete('id_vendor');
      $config = [
         [
            'field'     => 'id_vendor',
            'label'     => 'Id Vendor',
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
            $data = array('id' => $id_vendor);
            $cek = $this->crud->where('vendor', $data);
            if (!$cek) {
               return $this->set_response('Vendor Tidak Ada', REST_Controller::HTTP_BAD_REQUEST); 
            }else{
               if ($cek['gambar_vendor'] != 'null') {
                  $del = array(
                     'path' => '___/upload/produk/GambarVendor/', 
                     'gambar' => json_decode($cek['gambar_vendor'])
                  );
                  if ($this->_doDeleteFile($del)) {
                    $unlink = $this->_doDeleteFolder($del['path']);
                  }
               }
               $delete = $this->Crud->delete('vendor', $data);
               return $this->set_response('Sukses Delete Vendor', REST_Controller::HTTP_OK); 
               
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

    public function exists_vendor($key) {
        $data = array('nama_vendor' => $key);
        $cekKategori = $this->Crud->count('vendor', $data);
        if ($cekKategori == false) {
           return true;
        }else{
           return false;
        }
    }

}
