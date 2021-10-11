<?php

use chriskacerguis\RestServer\RestController;

class Akun extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AkunModel', 'akun');
    }

    public function index_get()
    {
        $id = $this->get('id');
        if ($id === null) {
            $akun = $this->akun->getAkun();
        } else {
            $akun = $this->akun->getAkun($id);
        }

        if ($akun) {
            $this->response([
                'status' => true,
                'data' => $akun
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'id not found'
            ], RestController::HTTP_NOT_FOUND);
        }
    }

    public function index_delete()
    {
        $id = $this->delete('id');

        if ($id === null) {
            $this->response([
                'status' => false,
                'message' => 'provide an id!'
            ], RestController::HTTP_BAD_REQUEST);
        } else {
            if ($this->akun->deleteAkun($id) > 0) {
                $this->response([
                    'status' => true,
                    'id' => $id,
                    'message' => 'deleted.'
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'id not found'
                ], RestController::HTTP_BAD_REQUEST);
            }
        }
    }

    public function index_post()
    {
        $data = [
            'nama_depan' => $this->input->post('nama_depan', true),
            'nama_belakang' => $this->input->post('nama_belakang', true),
            'email' => $this->input->post('email', true),
            'password' => $this->input->post('password'),
            'role_id' => 1,
            'is_active' => 1,
            'date_created' => time()
        ];

        if ($this->akun->createAkun($data) > 0) {
            $this->response([
                'status' => true,
                'message' => 'new akun has been crated'
            ], RestController::HTTP_CREATED);
        } else {
            $this->response([
                'status' => false,
                'message' => 'failed to created new akun'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function index_put()
    {
        $id = $this->put('id');
        $data = [
            'nama_depan' => $this->put('nama_depan'),
            'nama_belakang' => $this->put('nama_belakang'),
            'email' => $this->put('email'),
            'password' => $this->put('password'),
            'role_id' => 1,
            'is_active' => 1,
            'date_created' => time()
        ];

        if ($this->akun->updateAkun($data, $id) > 0) {
            $this->response([
                'status' => true,
                'message' => 'Data akun has been updated'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'failed to updated akun'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
}
