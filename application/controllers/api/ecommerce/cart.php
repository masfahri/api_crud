<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
class Cart extends REST_Controller {

    public function __construct($config='rest') 
    {
        parent::__construct($config);
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(array('crypt', 'jwt', 'form', 'url', 'file'));
        $this->load->model(array('Crud', 'mProduk'));
        $this->user_id = $this->session->userdata('nomor_hp');
        $this->arrayName = array('nomor_hp' => $this->user_id);
        $this->users = $this->crud->wheres('users', $this->arrayName);
        $this->user_id = $this->users['id'];
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index_post ()
    {
        $produk_id = $this->post('produk_id');
        $qty = $this->post('qty');
        $getProduk = $this->_getProdukDetail($produk_id);
        
        if($this->form_validation->run('create_cart') == FALSE) {
            $output = $this->form_validation->error_array();
            return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            if ($this->_isUser()) {
                $cekData = array('user_id' => $this->user_id, 'produk_id' => $produk_id);
                $cek = $this->crud->wheres('cart', $cekData);
                if($cek){
                    $updateData = array('quantity' => $qty);
                    $update = $this->crud->update('cart', array('id' => $cek['id']), $updateData);
                    if ($update) {
                        $return = $this->set_response('Update Cart Sukses', REST_Controller::HTTP_OK);    
                    }else{
                        $return = $this->set_response('Something Went Wrong!', REST_Controller::HTTP_BAD_REQUEST);    
                    }
                }else{
                    $insertData = array(
                        'user_id' => $this->user_id, 
                        'produk_id' => $produk_id, 
                        'quantity' => $qty, 
                        'harga_produk' => $getProduk['harga_produk'], 
                    );
                    $insert = $this->crud->create('cart', $insertData);
                    $return = $this->set_response('Insert Cart Sukses', REST_Controller::HTTP_OK);
                }
                return $return;
            }else{
                return $this->set_response('silahkan login terlebih dahulu', REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    public function index_put()
    {
        $cart_id = $this->put('cart_id');
        $qty = $this->put('qty');
        $config = [
            [
                'field'     => 'cart_id',
                'label'     => 'Cart',
                'rules'     => 'required',
                'errors'    => [
                    'required' => '%s Diperlukan',
                ]
            ],
        ];

        $data = $this->put();
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules($config);
        
        if($this->form_validation->run() == FALSE) {
            $output = $this->form_validation->error_array();
            return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            if ($this->_isUser()) {
                $condition = array('id' => $cart_id);
                $data = $this->Crud->wheres('cart', $condition);
                if (!$data) {
                    return $this->set_response('Anda Belum Memasukan ke Keranjang Belanja!', REST_Controller::HTTP_BAD_REQUEST);
                }
                $paramsStok = array(
                    'produk_id' => $data['produk_id'], 
                    'stok' => $qty,
                    'action' => 'cekUpdate');
                $dataStok = $this->stok($paramsStok);
                if ($dataStok != true) {
                    if ($qty == null || $qty == '' || $qty == 0 || $qty == '0') {
                        $qty = $data['quantity'];
                    }
                    $updateData = array('quantity' => $qty);
                    $update = $this->crud->update('cart', $condition, $updateData);
                    if ($update) {
                        $return = $this->set_response('Update Cart Sukses', REST_Controller::HTTP_OK);    
                    }else{
                        $return = $this->set_response('Something Went Wrong!', REST_Controller::HTTP_BAD_REQUEST);    
                    }
                }else{
                    $return = $this->set_response('Jumlah Melebihi Stok', REST_Controller::HTTP_BAD_REQUEST);    
                }
            }else{
                $return  = $this->set_response('silahkan login terlebih dahulu', REST_Controller::HTTP_BAD_REQUEST);
            }
        }
        return $return;
    }

    public function index_delete()
    {
        $cart_id = $this->delete('cart_id');
        $config = [
            [
                'field'     => 'cart_id',
                'label'     => 'Cart',
                'rules'     => 'required',
                'errors'    => [
                    'required' => '%s Diperlukan',
                ]
            ],
        ];

        $data = $this->delete();
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules($config);

        if($this->form_validation->run('create_cart') == FALSE) {
            $output = $this->form_validation->error_array();
            return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            if ($this->_isUser()) {
                $cekData = array('id' => $cart_id);
                $cek = $this->crud->wheres('cart', $cekData);
                if (!$cek) {
                    return $this->set_response('tidak ada barang dikeranjang belanja', REST_Controller::HTTP_BAD_REQUEST);
                }else{
                    $delete = $this->crud->delete('cart', $cekData);
                    if ($delete) {
                        return $this->set_response('Sukses Delete Barang dikeranjang Belanja', REST_Controller::HTTP_OK);
                    }
                }
            }else{
                return $this->set_response('Silahkan Login', REST_Controller::HTTP_BAD_REQUEST);
            }

        }
    }

    public function index_get()
    {
        $cart_id = $this->get('cart_id');
        if ($this->_isUser()) {
            $cekData = array('id' => $cart_id);
            $select = array('*');
            $barang = $this->mProduk->getCart('cart', $cekData, $select);
            if ($barang) {
                return $this->set_response($barang, REST_Controller::HTTP_OK);
            }else{
                return $this->set_response('Tidak Ada Barang dikeranjang Belanja', REST_Controller::HTTP_BAD_REQUEST);
            }
        }else{
            return $this->set_response('Silahkan Login', REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function detail_get()
    {
        $cart_id = $this->get('cart_id');
        $config = [
            [
                'field'     => 'cart_id',
                'label'     => 'Cart',
                'rules'     => 'required|numeric',
                'errors'    => [
                    'required' => '%s Diperlukan',
                    'numeric' => '%s Hanya Angka',
                ]
            ],
        ];

        $data = $this->get();
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules($config);

        if($this->form_validation->run() == FALSE) {
            $output = $this->form_validation->error_array();
            return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            if ($this->_isUser()) {
                $cekData = array('id' => $cart_id);
                $selectCart = array('id', 'user_id', 'quantity', 'harga_produk');
                $cek = $this->mProduk->getCart('cart', $cekData, $selectCart);
                if ($cek != null) {
                    $select = array(
                        'nama_produk', 'deskripsi_produk_short', 'gambar_produk',
                        'cart.harga_produk', 'deskripsi_produk_long',
                    );
                    $join = array(
                        array(
                            'tableJoin' => 'users',
                            'join' => 'users.id = cart.user_id', 
                        ),
                        array(
                            'tableJoin' => 'produk',
                            'join' => 'produk.id = cart.produk_id', 
                        ),
                        array(
                            'tableJoin' => 'detail_produk',
                            'join' => 'produk.id = detail_produk.produk_id', 
                        ),
                    );
                    $cekCart = array('cart.id' => $cart_id);

                    $return = $this->mProduk->getCart('cart', $cekData, $selectCart);
                    $return['detail'] = $this->mProduk->getDetailCartAll('cart', $cekCart, $join, $select);
                    return $this->set_response($return, REST_Controller::HTTP_OK);
                }else{
                    return $this->set_response('Tidak Ada Barang dikeranjang Belanja', REST_Controller::HTTP_BAD_REQUEST);
                }
            }else{
                return $this->set_response('Silahkan Login', REST_Controller::HTTP_BAD_REQUEST);
            }
        }
        
    }


    public function _getProdukDetail($params)
    {
        $where = array('produk.id' => $params);
        $select = array('*');
        $join = array(
            'join' => 'produk.id= detail_produk.produk_id', 
            'tableJoin' => 'detail_produk'
        );
        return $this->mProduk->getDetailBarang('produk', $where, $join, $select);
    }

    public function exists($key) {
        $data = array('id' => $key);
        $cekProduk = $this->Crud->count('produk', $data);
        if ($cekProduk == false) {
           return false;
        }else{
           return true;
        }
    }
    
    public function stok($key) {
        if (is_array($key) == 1) {
            $prodId = $key['produk_id'];
        }else{
            $prodId = $this->post('produk_id');
        }
        $cek = $this->Crud->wheres('detail_produk', array('produk_id' => $prodId));
        if ($cek['stok_produk'] < $key) {
           return false;
        }else{
           return true;
        }
    }
}