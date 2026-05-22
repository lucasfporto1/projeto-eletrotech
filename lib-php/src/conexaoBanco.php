<?php
class ConexaoBanco
{
    private static $conn = null;

    public static function connect()
    {
        if (self::$conn === null) {

            $caminhoEnv = $_SERVER['DOCUMENT_ROOT'] . '/projeto-final/.env';

            if (file_exists($caminhoEnv)) {
                $env = parse_ini_file($caminhoEnv);

                $host = $env['DB_HOST'];
                $user = $env['DB_USER'];
                $pass = $env['DB_PASS'];
                $db   = $env['DB_NAME'];
            } else {
                die("Erro Crítico: Arquivo .env não encontrado na raiz do projeto.");
            }

            self::$conn = new mysqli($host, $user, $pass, $db);

            if (self::$conn->connect_error) {
                die("Erro de Conexão: " . self::$conn->connect_error);
            }
            self::$conn->set_charset("utf8mb4");
        }
        return self::$conn;
    }
}
