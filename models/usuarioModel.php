<?php
require_once '../lib-php/libUtils.php';

class UsuarioModel
{
    private $db;

    public function __construct()
    {
        $this->db = ConexaoBanco::connect();
    }

    public function usuarioExiste($nomeUsuario)
    {
        return Verificador::exists('tabela_usuarios', 'usuario', $nomeUsuario);
    }

    public function criarUsuario($nomeUsuario, $senha)
    {
        $senhaCriptografada = createHash($senha);

        $sql = "INSERT INTO tabela_usuarios (usuario, senha) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $nomeUsuario, $senhaCriptografada);

        return $stmt->execute();
    }
    public function atualizarUsuario($id, $nomeUsuario)
    {
        $sql = "UPDATE tabela_usuarios SET usuario = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nomeUsuario, $id);

        return $stmt->execute();
    }
    public function buscarUsuarioPorNome($nomeUsuario)
    {
        $sql = "SELECT id, senha FROM tabela_usuarios WHERE usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $nomeUsuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            return $resultado->fetch_assoc();
        }
        return null;
    }
}
