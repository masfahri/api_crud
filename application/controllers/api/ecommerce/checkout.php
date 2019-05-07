<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
class Checkout extends REST_Controller {

    public function __construct($config='rest') 
    {
        parent::__construct($config);
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(array('crypt', 'jwt', 'form', 'url', 'file'));
        $this->load->model(array('Crud', 'mProduk'));
        if ($this->session->userdata('nomor_hp')) {
            $this->user_id = $this->session->userdata('nomor_hp');
            $this->arrayName = array('nomor_hp' => $this->user_id);
            $this->users = $this->crud->wheres('users', $this->arrayName);
            $this->user_id = $this->users['id'];
        }else{
            return $this->set_response('silahkan login terlebih dahulu', REST_Controller::HTTP_BAD_REQUEST);
        }

        date_default_timezone_set('Asia/Jakarta');
    }

    public function index_post()
    {
        if ($this->_isUser()) {
            $select = ('*');
            $key = array(
                'user_id' => $this->user_id, 
                'status' => 'active', 
            );
            $harga = array();
            $cartAktif = array();
            $cart = $this->mProduk->getCart('cart', $key, $select);
            foreach ($cart as $key => $value) {
                $harga[] .= $value['harga_produk'];
                $cartAktif[] .=$value['id'];
            }
            $jumlahHarga = array_sum($harga);
            $produkCart = json_encode($cartAktif);
            $alamatPengirimanUser = $this->cekUser();
            $alamatPengirimanCus = $this->cekCus();
            if ($alamatPengirimanUser['alamat'] == '' && $alamatPengirimanCus['alamat_perusahaan'] == '') {
                return $this->set_response('silahkan isikan alamat perusahaan atau pribadi terlebih dahulu untuk pengiriman', REST_Controller::HTTP_BAD_REQUEST);
            }
            if ($alamatPengirimanUser['alamat'] == '' && $alamatPengirimanCus['alamat_perusahaan'] != '') {
                $alamatPengiriman = $alamatPengirimanCus['alamat_perusahaan'];
            }
            if ($alamatPengirimanUser['alamat'] != '' && $alamatPengirimanCus['alamat_perusahaan'] == '') {
                $alamatPengiriman = $alamatPengirimanUser['alamat'];
            }
            if ($alamatPengirimanUser['alamat'] != null && $alamatPengirimanCus['alamat_perusahaan'] != null ) {
                $alamatPengiriman = $alamatPengirimanCus['alamat_perusahaan'];
            }
            $insertData = array(
                'user_id' => $this->user_id,
                'total_harga' => $jumlahHarga,
                'produk_cart' => $produkCart,
                'alamat_pengiriman' => $alamatPengiriman,
                'status' => 'ordered',
            );
            $dataOrderDetail = array(
                'user_id' => $this->user_id,
                'status' => 'ordered',
            );
            $cekOrderDetail = $this->crud->wheres('orders', $dataOrderDetail);
            if (empty($cekOrderDetail)) {
                $insert = $this->crud->create('orders', $insertData);
                if ($insert) {
                    return $this->set_response('Sukses Order Silahkan ke Tahap berikut', REST_Controller::HTTP_BAD_REQUEST);    
                }
            }else{
                return $this->set_response('Silahkan Selesaikan Orderan Sebelumnya', REST_Controller::HTTP_BAD_REQUEST);    
            }

        }else{
            return $this->set_response('silahkan login terlebih dahulu', REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function payment_post()
    {
        $order_id = $this->post('order_id');
        $bank = $this->post('bank');
        $norek = $this->post('norek');
        if ($this->_isUser()) {
            if($this->form_validation->run('create_payment') == FALSE) {
                $output = $this->form_validation->error_array();
                return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST);
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
                    $select = array('*');
                    $join = array(
                        'tableJoin' => 'users',
                        'join' => 'users.id = orders.user_id'
                    );
                    $globalId = array('id' => $order_id);
                    $key = array('orders.id' => $order_id);
                    $cek = $this->mProduk->getDetailBarang('orders', $key, $join, $select);
                    if (!$cek) {
                        $output['error'] = 'Order Tidak Ada!';
                     }else{
                         if ($cek['nama'] == null || $cek['nama'] == '') {
                             $namaGambar = 'Payment_'.$order_id;
                        }else{
                            $namaGambar = 'Payment_'.$order_id;
                        }
                        $upload = $this->_doUpload($image_path, $namaGambar, 'produk/GambarPayment');
                        if ($upload) {
                           $dataInsert = array(
                               'order_id' => $order_id,
                               'bank' => $bank,
                               'norek' => $norek,
                               'gambar_payment' => $upload,
                            );
                            $keyOrder = array('order_id' => $order_id);
                            $cekPayment = $this->cekPayment('payment', $keyOrder);
                            if($cekPayment) {
                                return $this->set_response('Mohon tunggu konfirmasi dari Admin', REST_Controller::HTTP_BAD_REQUEST);     
                            }else{
                                $insert = $this->crud->create('payment', $dataInsert);
                                if ($insert) {
                                    $updateOrderStatus = $this->crud->update('orders', $globalId, array('status' => 'payment-pending'));
                                }
                            }
                            $output['data'] = $this->cekPayment('payment', $keyOrder);
                            return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST); 
                        }else{
                           $output['error'] = 'Something Wrong!';
                           return $this->set_response($output, REST_Controller::HTTP_BAD_REQUEST); 
                        }
                     }
                }
            }
        }
    }

    public function cekPayment($tabel, $key)
    {
        return $dataPayment = $this->Crud->wheres($tabel, $key);
    }


    public function cekUser()
    {
        $user = $this->crud->wheres('users', array('id' => $this->user_id));
        if ($user) return $user;
    }

    public function cekCus()
    {
        $user = $this->crud->wheres('customer', array('users_id' => $this->user_id));
        if ($user) return $user;
    }

    public function exists($key) {
        $data = array('id' => $key);
        $cekProduk = $this->Crud->count('orders', $data);
        if ($cekProduk == false) {
           return false;
        }else{
           return true;
        }
    }
}