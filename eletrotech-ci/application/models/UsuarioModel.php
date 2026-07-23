<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UsuarioModel extends CI_Model
{
    public function listarUsuarios($limite = null, $offset = 0)
    {
        $this->db
            ->select('id, usuario')
            ->order_by('id', 'DESC');

        if ($limite !== null) {
            $this->db->limit($limite, $offset);
        }

        return $this->db->get('tabela_usuarios')->result();
    }

    public function contarUsuarios()
    {
        return $this->db->count_all_results('tabela_usuarios');
    }

    public function buscarUsuarioPorId($id)
    {
        return $this->db
            ->select('id, usuario')
            ->where('id', (int) $id)
            ->get('tabela_usuarios')
            ->row();
    }

    public function buscarUsuarioPorNome($nomeUsuario)
    {
        return $this->db
            ->select('id, senha')
            ->where('usuario', $nomeUsuario)
            ->get('tabela_usuarios')
            ->row();
    }

    public function usuarioExiste($nomeUsuario, $ignorarId = null)
    {
        $this->db->where('usuario', $nomeUsuario);

        if ($ignorarId !== null) {
            $this->db->where('id !=', (int) $ignorarId);
        }

        return $this->db->count_all_results('tabela_usuarios') > 0;
    }

    public function criarUsuario($nomeUsuario, $senha)
    {
        return $this->db->insert('tabela_usuarios', array(
            'usuario' => $nomeUsuario,
            'senha'   => password_hash($senha, PASSWORD_DEFAULT),
        ));
    }

    public function atualizarUsuario($id, $nomeUsuario)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update('tabela_usuarios', array('usuario' => $nomeUsuario));
    }

    public function excluirUsuario($id)
    {
        // db_debug off: com vínculo (FK) o delete retorna FALSE em vez de estourar erro
        $debugOriginal = $this->db->db_debug;
        $this->db->db_debug = false;

        $ok = $this->db->delete('tabela_usuarios', array('id' => (int) $id));

        $this->db->db_debug = $debugOriginal;

        return $ok;
    }
}
