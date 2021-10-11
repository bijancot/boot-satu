<?php

use chriskacerguis\RestServer\RestController;

class Akun_token extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('TokenModel', 'token');
    }

    public function index_get()
    {
        $id = $this->get('id');
        if ($id === null) {
            $token = $this->token->getToken();
        } else {
            $token = $this->token->getToken($id);
        }

        if ($token) {
            $this->response([
                'status' => true,
                'data' => $token
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
            if ($this->token->deleteToken($id) > 0) {
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
            'email' => $this->input->post('email', true),
            'token' => $this->input->post('token'),
            'date_created' => time()
        ];

        if ($this->token->createToken($data) > 0) {
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
            'email' => $this->put('email'),
            'token' => $this->put('token'),
            'date_created' => time()
        ];

        if ($this->token->updateToken($data, $id) > 0) {
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
