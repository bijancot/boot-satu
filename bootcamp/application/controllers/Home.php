<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['akun'] = $this->db->get_where('akun', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('home/home', $data);
    }
}
