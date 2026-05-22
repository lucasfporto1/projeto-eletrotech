<?php
class Validador
{

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
}