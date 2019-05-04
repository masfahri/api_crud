<?php


$config = array(
    'login' => array(
        array(
            'field'     => 'username',
            'label'     => 'Username',
            'rules'     => 'required',
            'errors'    => [
                'required' => '%s atau Nomor Hp atau email Diperlukan'
            ]
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required|min_length[8]|alpha_numeric',
            'errors'    => [
                'required' => '%s Diperlukan',
                'min_length' => '%s minimal 8 karakter',
                'alpha_numeric' => '%s kombinasi angka dan huruf',
            ]
            
        ),
    ),
    'otp' => array(
        array(
            'field'     => 'username',
            'label'     => 'Username',
            'rules'     => 'required|callback_exists_user',
            'errors'    => [
                'required' => '%s Diperlukan',
                'exists_user' => '%s Gak Terdaftar',
            ]
        ),
        array(
            'field'     => 'otp',
            'label'     => 'OTP',
            'rules'     => 'required|max_length[6]|min_length[6]|numeric',
            'errors'    => [
                'required' => '%s Diperlukan',
                'max_length' => '%s Kelebihan',
                'min_length' => '%s Kurang',
                'numeric' => '%s Hanya Angka',
            ]
        ),
    ),
    'create_user' => array(
        array(
            'field'     => 'email',
            'label'     => 'Email',
            'rules'     => 'required|valid_email|is_unique[users.email]|max_length[256]',
            'errors'    => [
                'required'    => '%s Diperlukan',
                'valid_email' => '%s Tidak Valid',
                'is_unique'    => '%s Sudah Ada',
                'max_length'    => '%s Melebihi Batas',
            ]
        ),
        array(
            'field'     => 'nomor_hp',
            'label'     => 'Nomor Hp',
            'rules'     => 'required|numeric|is_unique[users.nomor_hp]|max_length[13]',
            'errors'    => [
                'required'    => '%s Diperlukan',
                'numeric' => '%s Tidak Valid',
                'is_unique'    => '%s Sudah Ada',
                'max_length'    => '%s Melebihi Batas',
            ]
        ),
        array(
            'field'     => 'nama',
            'label'     => 'Nama Pegawai',
            'rules'     => 'required|max_length[255]',
            'errors'    => [
                'required'    => '%s Diperlukan',
                'max_length'    => '%s Melebihi Batas',
            ]
        ),
        array(
            'field'     => 'alamat',
            'label'     => 'Alamat Pegawai',
            'rules'     => 'required',
            'errors'    => [
                'required'    => '%s Diperlukan',
            ]
        ),
        array(
            'field'     => 'role',
            'label'     => 'Role',
            'rules'     => 'required|callback_role',
            'errors'    => [
                'required'    => '%s Diperlukan',
                'role'        => '%s Tidak Ada',
            ]
        ),
    ),
    'create_kategori' => array(
        array(
            'field'     => 'nama_kategori',
            'label'     => 'Nama Kategori',
            'rules'     => 'required|callback_exists_kategori',
            'errors'    => [
                'required' => '%s Diperlukan',
                'exists_kategori' => '%s Sudah Ada',
            ]
        ),
    ),
    'update_kategori' => array(
        array(
            'field'     => 'id_kategori',
            'label'     => 'Id Kategori',
            'rules'     => 'required',
            'errors'    => [
                'required' => '%s Diperlukan',
            ]
        ),
        
    ),
    'create_vendor' => array(
        array(
            'field'     => 'nama_vendor',
            'label'     => 'Nama vendor',
            'rules'     => 'required|callback_exists_vendor',
            'errors'    => [
                'required' => '%s Diperlukan',
                'exists_vendor' => '%s Sudah Ada',
            ]
        ),
        array(
            'field'     => 'email_vendor',
            'label'     => 'Email vendor',
            'rules'     => 'required|is_unique[vendor.email_vendor]|valid_email',
            'errors'    => [
                'required' => '%s Diperlukan',
                'is_unique' => '%s Sudah Ada',
                'valid_email' => '%s Tidak Valid',
            ]
        ),
        array(
            'field'     => 'nomor_vendor',
            'label'     => 'Nomor vendor',
            'rules'     => 'required|is_unique[vendor.nomor_vendor]|numeric|min_length[10]|max_length[13]',
            'errors'    => [
                'required' => '%s Diperlukan',
                'is_unique' => '%s Sudah Ada',
                'numeric' => '%s Tidak Valid',
                'min_length' => '%s Kurang Karakter',
                'max_length' => '%s Kelebihan Karakter',
            ]
        ),
        array(
            'field'     => 'alamat_vendor',
            'label'     => 'Alamat vendor',
            'rules'     => 'required|max_length[255]',
            'errors'    => [
                'required' => '%s Diperlukan',
                // 'min_length' => '%s Kurang Karakter',
                'max_length' => '%s Kelebihan Karakter',
            ]
        ),
    ),
    'create_produk' => array(
        array(
            'field'     => 'nama_produk',
            'label'     => 'Nama Produk',
            'rules'     => 'required|callback_exists_produk',
            'errors'    => [
                'required' => '%s Diperlukan',
                'exists_produk' => '%s Sudah Ada',
            ]
        ),
        array(
            'field'     => 'deskripsi_produk_short',
            'label'     => 'Deskripsi Produk',
            'rules'     => 'required|max_length[255]|min_length[15]',
            'errors'    => [
                'required' => '%s Diperlukan',
                'min_length' => '%s Kurang Karakter',
                'max_length' => '%s Kelebihan Karakter',
            ]
        ),
        array(
            'field'     => 'kategori_id',
            'label'     => 'Kategori',
            'rules'     => 'required|numeric|callback_exists_kategori',
            'errors'    => [
                'required' => '%s Diperlukan',
                'numeric' => '%s Hanya Numeric',
                'exists_kategori' => '%s Tidak Ada',
            ]
        ),
        array(
            'field'     => 'vendor_id',
            'label'     => 'Vendor',
            'rules'     => 'required|numeric|callback_exists_vendor',
            'errors'    => [
                'required' => '%s Diperlukan',
                'numeric' => '%s Hanya Numeric',
                'exists_vendor' => '%s Tidak Ada',
            ]
        ),
    ),
    'create_detail_produk' => array(
        array(
            'field'     => 'produk_id',
            'label'     => 'Produk',
            'rules'     => 'required|numeric|callback_cekProduk|is_unique[detail_produk.produk_id]',
            'errors'    => [
                'required' => '%s Diperlukan',
                'numeric' => '%s Hanya Angka',
                'cekProduk' => '%s Tidak Ada',
                'is_unique' => '%s Sudah Ditambahkan',
            ]
        ),
        array(
            'field'     => 'harga_produk',
            'label'     => 'Harga Produk',
            'rules'     => 'required|numeric|greater_than[0.1000000]',
            'errors'    => [
                'required' => '%s Diperlukan',
                'is_unique' => '%s Sudah Ada',
                'greater_than' => '%s Tidak Boleh Melebihi {greated_than}',
            ]
        ),
        array(
            'field'     => 'satuan_produk',
            'label'     => 'Satuan Produk',
            'rules'     => 'required|min_length[2]|max_length[5]',
            'errors'    => [
                'required' => '%s Diperlukan',
                'min_length' => '%s Kurang Karakter',
                'max_length' => '%s Kelebihan Karakter',
            ]
        ),
        array(
            'field'     => 'stok_produk',
            'label'     => 'Stok',
            'rules'     => 'required|numeric|greater_than[0.100]',
            'errors'    => [
                'required' => '%s Diperlukan',
                'numeric' => '%s Hanya Numeric',
                'greater_than' => '%s Tidak Boleh Melebihi {greated_than}',
            ]
        ),
    ),
    
    'gambar' => array(
        array(
            'field'     => 'action',
            'label'     => 'Action',
            'rules'     => 'required',
            'errors'    => [
                'required' => '%s Diperlukan',
            ]
        ),
        array(
            'field'     => 'id',
            'label'     => 'Id',
            'rules'     => 'required',
            'errors'    => [
                'required' => '%s Diperlukan',
            ]
        ),
        
    ),
);


?>