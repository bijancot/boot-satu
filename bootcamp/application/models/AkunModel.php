<?php
class AkunModel extends CI_Model
{
    public function getAkun($id = null)
    {
        if ($id === null) {
            return $this->db->get('akun')->result_array();
        } else {
            return $this->db->get_where('akun', ['id_akun' => $id])->result_array();
        }
    }

    public function deleteAkun($id)
    {
        $this->db->delete('akun', ['id_akun' => $id]);
        return $this->db->affected_rows();
    }

    public function createAkun($data)
    {
        $this->db->insert('akun', $data);
        return $this->db->affected_rows();
    }

    public function updateAkun($data, $id)
    {
        $this->db->update('akun', $data, ['id_akun' => $id]);
        return $this->db->affected_rows();
    }
}
