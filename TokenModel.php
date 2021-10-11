<?php
class TokenModel extends CI_Model
{
    public function getToken($id = null)
    {
        if ($id === null) {
            return $this->db->get('akun_token')->result_array();
        } else {
            return $this->db->get_where('akun_token', ['id' => $id])->result_array();
        }
    }

    public function deleteToken($id)
    {
        $this->db->delete('akun_token', ['id' => $id]);
        return $this->db->affected_rows();
    }

    public function createToken($data)
    {
        $this->db->insert('akun_token', $data);
        return $this->db->affected_rows();
    }

    public function updateToken($data, $id)
    {
        $this->db->update('akun_token', $data, ['id' => $id]);
        return $this->db->affected_rows();
    }
}
