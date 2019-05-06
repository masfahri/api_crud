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

   public function index_post() {
      $nama_produk = $this->post('nama_produk');
      $deskripsi_produk_short = $this->post('deskripsi_produk_short');
      $kategori_id = $this->post('kategori_id');
      $vendor_id = $this->post('vendor_id');
      $slug = str_replace(' ', '-', $nama_produk);
      if ($this->is_logged_in() != null){
         if($this->form_validation->run('create_produk') == FALSE) {
            $output = $this->form_validation->error_array();
            $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
         } else {
            $insertData = array(
               'nama_produk' => $nama_produk,
               'slug' => $slug,
               'deskripsi_produk_short' => $deskripsi_produk_short,
               'kategori_id' => $kategori_id,
               'vendor_id' => $vendor_id,
               'thumb_produk' => 'null',
               'gambar_produk' => 'null'
            );
            if (!$this->Crud->create('produk', $insertData)) {
                return $this->set_response('something Error', REST_Controller::HTTP_BAD_REQUEST);       
             }else{
                return $this->set_response('Sukses Menambahkan Produk', REST_Controller::HTTP_OK);       
             }
         }
      }else{
         return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST); 
      }
   }

   public function detail_post() {
        $produk_id = $this->post('produk_id');
        $satuan_produk = $this->post('satuan_produk');
        $harga_produk = $this->post('harga_produk');
        $stok_produk = $this->post('stok_produk');
        $deskripsi_produk_long = $this->post('deskripsi_produk_long');
        $_POST['status_produk'] = 'show';
        $_POST['gambar_detail_produk'] = 'null';
        $_POST['gambar_thumb_detail_produk'] = 'null';
        if ($this->is_logged_in() != null){
            if($this->form_validation->run('create_detail_produk') == FALSE) {
                $output = $this->form_validation->error_array();
                $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
            }else{

                 if (!$this->Crud->create('detail_produk', $_POST)) {
                    return $this->set_response('something Error', REST_Controller::HTTP_BAD_REQUEST);       
                 }else{
                    return $this->set_response('Sukses Menambahkan Produk', REST_Controller::HTTP_OK);       
                 }
            }
        }else{
            return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST); 
        }
        
   }

    public function index_put() {
        $_POST['nama_produk'] = $this->put('nama_produk');
        $_POST['deskripsi_produk_short'] = $this->put('deskripsi_produk_short');
        $_POST['kategori_id'] = $this->put('kategori_id');
        $_POST['vendor_id'] = $this->put('vendor_id');
        $_POST['id'] = $this->put('id');
        $_POST['slug'] = str_replace(' ', '-', $_POST['nama_produk']);

        $config = [
            [
                'field'     => 'nama_produk',
                'label'     => 'Produk',
                'rules'     => 'required',
                'errors'    => [
                    'required' => '%s Diperlukan',
                ]
            ],
            [
                'field'     => 'nama_produk',
                'label'     => 'Produk',
                'rules'     => 'required',
                'errors'    => [
                    'required' => '%s Diperlukan',
                ]
            ],
            [
                'field'     => 'deskripsi_produk_short',
                'label'     => 'Deskripsi Produk',
                'rules'     => 'max_length[255]|min_length[15]',
                'errors'    => [
                    'min_length' => '%s Kurang Karakter',
                    'max_length' => '%s Kelebihan Karakter',
                ]
            ],
            [
                'field'     => 'kategori_id',
                'label'     => 'Kategori',
                'rules'     => 'numeric|callback_exists_kategori',
                'errors'    => [
                    'numeric' => '%s Hanya Numeric',
                    'exists_kategori' => '%s Tidak Ada',
                ]
            ],
            [
                'field'     => 'vendor_id',
                'label'     => 'Vendor',
                'rules'     => 'numeric|callback_exists_vendor',
                'errors'    => [
                    'numeric' => '%s Hanya Numeric',
                    'exists_vendor' => '%s Tidak Ada',
                ]
            ],
        ];

        $data = $this->put();
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules($config);
        
        $condition = array('id' => $_POST['id']);
        $data = $this->Crud->where('produk', $condition);
        if ($this->is_logged_in() != null){
            if($this->form_validation->run() == FALSE) {
                $output = $this->form_validation->error_array();
                $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
            }else{
                if (!$data == FALSE) {

                    if ($_POST['nama_produk'] == '' || $_POST['nama_produk'] == null) {
                        $_POST['nama_produk'] = $data['nama_produk'];
                    }
                    if ($_POST['deskripsi_produk_short'] == '' || $_POST['deskripsi_produk_short'] == null) {
                        $_POST['deskripsi_produk_short'] = $data['deskripsi_produk_short'];
                    }
                    if ($_POST['kategori_id'] == '' || $_POST['kategori_id'] == null) {
                        $_POST['kategori_id'] = $data['kategori_id'];
                    }
                    if ($_POST['vendor_id'] == '' || $_POST['vendor_id'] == null) {
                        $_POST['vendor_id'] = $data['vendor_id'];
                    }

                    $key = array('id' => $_POST['id']);
                    if ($this->Crud->update('produk', $key, $_POST)) {
                        $return =  $this->set_response('sukses update Produk', REST_Controller::HTTP_OK); 
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

    public function detail_put() {
        $_POST['id'] = $this->put('id');
        $_POST['satuan_produk'] = $this->put('satuan_produk');
        $_POST['harga_produk'] = $this->put('harga_produk');
        $_POST['stok_produk'] = $this->put('stok_produk');
        $_POST['deskripsi_produk_long'] = $this->put('deskripsi_produk_long');

        $config = [
            [
                'field'     => 'produk_id',
                'label'     => 'Produk',
                'rules'     => 'required|numeric|callback_cekProduk',
                'errors'    => [
                    'required' => '%s Diperlukan',
                    'numeric' => '%s Hanya Angka',
                    'cekProduk' => '%s Tidak Ada',
                ]
            ],
            [
                'field'     => 'harga_produk',
                'label'     => 'Harga Produk',
                'rules'     => 'numeric|greater_than[0.1000000]',
                'errors'    => [
                    'is_unique' => '%s Sudah Ada',
                    'greater_than' => '%s Tidak Boleh Melebihi {greated_than}',
                ]
            ],
            [
                'field'     => 'satuan_produk',
                'label'     => 'Satuan Produk',
                'rules'     => 'min_length[2]|max_length[5]',
                'errors'    => [
                    'min_length' => '%s Kurang Karakter',
                    'max_length' => '%s Kelebihan Karakter',
                ]
            ],
            [
                'field'     => 'stok_produk',
                'label'     => 'Stok',
                'rules'     => 'numeric|greater_than[0.100]',
                'errors'    => [
                    'numeric' => '%s Hanya Numeric',
                    'greater_than' => '%s Tidak Boleh Melebihi {greated_than}',
                ]
            ],
        ];

        $data = $this->put();
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules($config);
        
        $condition = array('id' => $_POST['id']);
        $data = $this->Crud->where('detail_produk', $condition);
        if ($this->is_logged_in() != null){
            if($this->form_validation->run() == FALSE) {
                $output = $this->form_validation->error_array();
                $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
            }else{
                if (!$data == FALSE) {

                    if ($_POST['satuan_produk'] == '' || $_POST['satuan_produk'] == null) {
                        $_POST['satuan_produk'] = $data[0]['satuan_produk'];
                    }
                    if ($_POST['harga_produk'] == '' || $_POST['harga_produk'] == null) {
                        $_POST['harga_produk'] = $data[0]['harga_produk'];
                    }
                    if ($_POST['stok_produk'] == '' || $_POST['stok_produk'] == null) {
                        $_POST['stok_produk'] = $data[0]['stok_produk'];
                    }
                    if ($_POST['deskripsi_produk_long'] == '' || $_POST['deskripsi_produk_long'] == null) {
                        $_POST['deskripsi_produk_long'] = $data[0]['deskripsi_produk_long'];
                    }
                    

                    $key = array('id' => $_POST['id']);
                    if ($this->Crud->update('detail_produk', $key, $_POST)) {
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

   public function index_get() {
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
      if ($this->is_logged_in() != null){
         if (!$data == FALSE) {
            $data['detail'] =  $this->Mproduk->getDetailBarang('produk', $condition['detail_produk'], $join, $select['detail']);
            $return['produk'] = $data;
            $return =  $this->set_response($return, REST_Controller::HTTP_OK); 
         }else{
            $return =  $this->set_response('Produk Gak Ada', REST_Controller::HTTP_BAD_REQUEST); 
         }
         return $return;
      } return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST);
   }

   public function index_delete() {
      $id_produk = $this->delete('id_produk');
      $config = [
         [
            'field'     => 'id_produk',
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
            $data['produk'] = array(
                'id' => $id_produk,
            );
            $data['detail'] = array(
                'produk_id' => $id_produk
            );
            $cek = $this->crud->wheres('produk', $data['produk']);
            if (!$cek) {
               return $this->set_response('Produk Tidak Ada', REST_Controller::HTTP_BAD_REQUEST); 
            }else{
               if ($cek['gambar_produk'] != 'null') {
                  $del = array(
                     'path' => '___/upload/produk/GambarProduk/'.$cek['nama_produk'].'/', 
                     'gambar' => json_decode($cek['gambar_produk'])
                  );
                  if (delete_files($del['path'], true)) {
                    $unlink = $this->_doDeleteFolder($del['path']);
                  }
               }
               $deleteProduk = $this->Crud->delete('produk', $data['produk']);
               $deleteDetail = $this->Crud->delete('detail_produk', $data['detail']);
               return $this->set_response('Sukses Delete Produk', REST_Controller::HTTP_OK); 
               
            }
         }
      }else{
         return $this->set_response('Session Expired', REST_Controller::HTTP_BAD_REQUEST); 
      }

   }

    public function index_patch() {
        $this->session->unset_userdata('nomor_hp');
    }

    public function cekProduk($key)
    {
        $data = array('id' => $key);
        $cek = $this->Mproduk->check('produk', $data);
        if ($cek == false) {
            return false;
        }else{
            return true;
        }
    }

    public function exists_produk($key) {
        $data = array('nama_produk' => $key);
        $cek = $this->Mproduk->check('produk', $data);
        if ($cek == false) {
            return true;
        }else{
            return false;
        }
    }

    public function exists_kategori($key) {
        $data = array('id' => $key);
        $cek = $this->Mproduk->check('kategori', $data);
        if ($cek == false) {
            return false;
        }else{
            return true;
        }
    }
    
    public function exists_vendor($key) {
        $data = array('id' => $key);
        $cek = $this->Mproduk->check('vendor', $data);
        if ($cek == false) {
            return false;
        }else{
            return true;
        }
            
    }

}
