<?php
require_once 'conexaoBanco.php';

class Verificador
{
    // Verifica campos obrigatórios
    public static function checkRequired(array $data, array $requiredFields): array
    {
        $missing = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    // Verifica se existe no banco 
    public static function exists(string $table, string $column, $value): bool
    {
        $db = ConexaoBanco::connect();

        $sql = "SELECT COUNT(*) as total FROM $table WHERE $column = ?";
        $stmt = $db->prepare($sql);


        $stmt->bind_param("s", $value);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        return ($result['total'] > 0);
    }
}