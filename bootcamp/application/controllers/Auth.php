<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');


        if ($this->form_validation->run() == false) {
            $data['title'] = 'Login';
            $this->load->view('auth/login', $data);
            $this->session->sess_destroy();
        } else {
            $this->_login();
        }
    }

    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('akun', ['email' => $email])->row_array();

        //  Jika User Terdaftar
        if ($user != null) {
            // Jika User Aktif
            if ($user['is_active'] == 1) {
                // Cek Password
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    $this->session->set_userdata($data);
                    if ($user['role_id'] == 1) {
                        redirect('Home');
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                        Password Salah!</div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                    Email Belum Aktif!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                Email Belum Terdaftar!</div>');
            redirect('auth');
        }
    }

    public function registrasi()
    {
        $this->form_validation->set_rules('nama_depan', 'Nama Depan', 'required|trim');
        $this->form_validation->set_rules('nama_belakang', 'Nama Belakang', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[akun.email]', [
            'is_unique' => 'Email ini telah digunakan!'
        ]);
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password Tidak Sama!', 'min_length' => 'Password Sangat Pendek!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|min_length[3]|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Registrasi';
            $this->load->view('auth/register', $data);
        } else {
            $email = $this->input->post('email', true);
            $data = [
                'nama_depan' => $this->input->post('nama_depan', true),
                'nama_belakang' => $this->input->post('nama_belakang', true),
                'email' => htmlspecialchars($email),
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 1,
                'is_active' => 0,
                'date_created' => time()
            ];

            // Token
            $token = base64_encode(random_bytes(32));
            $data_token = [
                'email' => $email,
                'token' => $token,
                'date_created' => time()
            ];

            $this->db->insert('akun', $data);
            $this->db->insert('akun_token', $data_token);

            $this->_sendEmail($token, 'verify');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                Akun Anda Berhasil Dibuat! Tolong Aktifkan Akunmu
              </div>');
            redirect('auth');
        }
    }

    private function _sendEmail($token, $type)
    {

        $config = [
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'Ephanzen4@gmail.com',
            'smtp_pass' => 'salam1jiwa',
            'smtp_port' => 465,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        ];

        $this->load->library('email', $config);
        $this->email->initialize($config);

        $this->email->from('Ephanzen4@gmail.com', 'Bootcamp');
        $this->email->to($this->input->post('email'));

        if ($type == 'verify') {
            $this->email->subject('Verifikasi Akun');
            $this->email->message('Klik disini untuk verifikasi : <a href="' . base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Aktif</a>');
        } else if ($type == 'forgot') {
            $this->email->subject('Reset Password');
            $this->email->message('Klik disini untuk reset password kamu : <a href="' . base_url() . 'auth/resetpassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Reset password</a>');
        }

        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }

    public function verify()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->db->get_where('akun', ['email' => $email])->row_array();

        if ($user) {
            $user_token = $this->db->get_where('akun_token', ['token' => $token])->row_array();

            if ($user_token) {
                if (time() - $user_token['date_created'] < 86400) {
                    $this->db->set('is_active', 1);
                    $this->db->where('email', $email);
                    $this->db->update('akun');

                    $this->db->delete('akun_token', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                    ' . $email . ' sudah aktif! Silahkan Login.
                    </div>');
                    redirect('auth');
                } else {
                    $this->db->delete('akun', ['email' => $email]);
                    $this->db->delete('akun_token', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                    Aktivasi gagal! Token expired.
                    </div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                Aktivasi gagal! Token salah.
                </div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Aktivasi gagal! Email salah.
           </div>');
            redirect('auth');
        }
    }

    public function lupaPassword()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        if ($this->form_validation->run() == false) {
            $data['title'] = 'Lupa Password';
            $this->load->view('auth/lupaPassword', $data);
        } else {
            $email = $this->input->post('email');
            $user = $this->db->get_where('akun', ['email' => $email, 'is_active' => 1])->row_array();

            if ($user) {
                $token = base64_encode(random_bytes(32));
                $data_token = [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time()
                ];

                $this->db->insert('akun_token', $data_token);
                $this->_sendEmail($token, 'forgot');

                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                Silahkan periksa email kamu untuk reset password!
                </div>');
                redirect('auth/lupaPassword');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                Email belum terdaftar atau belum aktif!
                </div>');
                redirect('auth/lupaPassword');
            }
        }
    }

    public function resetpassword()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->db->get_where('akun', ['email' => $email])->row_array();

        if ($user) {
            $user_token = $this->db->get_where('akun_token', ['token' => $token])->row_array();

            if ($user_token) {
                $this->session->set_userdata('reset_email', $email);
                $this->gantiPassword();
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                Reset password gagal! Token salah.
                </div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Reset password gagal! Email salah.
            </div>');
            redirect('auth');
        }
    }

    public function gantiPassword()
    {
        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
        }
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password Tidak Sama!', 'min_length' => 'Password Sangat Pendek!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|min_length[3]|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Ganti Password';
            $this->load->view('auth/gantiPassword', $data);
        } else {
            $password = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
            $email = $this->session->userdata('reset_email');

            $this->db->set('password', $password);
            $this->db->where('email', $email);
            $this->db->update('akun');

            $this->session->unset_userdata('reset_email');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Password sudah diganti! Silahkan login.
            </div>');
            redirect('auth');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Anda Berhasil Logout!
          </div>');
        redirect('auth');
    }
}
