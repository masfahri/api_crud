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
);


?>