<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ChecklistModel extends CI_Model
{
    protected $table = 'tabela_checklist';
    protected $tablePerguntas = 'tabela_checklist_perguntas';
    protected $tableRespostas = 'tabela_os_checklist_respostas';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $query = $this->db
            ->select('c.*, COUNT(p.id) AS total_perguntas')
            ->from($this->table . ' c')
            ->join($this->tablePerguntas . ' p', 'p.id_checklist = c.id', 'left')
            ->group_by('c.id')
            ->order_by('c.id', 'DESC')
            ->get();

        if ($query === false) {
            return [];
        }

        return $query->result_array();
    }

    public function get_selected_by_type($tipo)
    {
        $checklist = $this->db
            ->where('tipo', $tipo)
            ->where('selecionado', 1)
            ->get($this->table)
            ->row_array();

        if (!$checklist) {
            return null;
        }

        $checklist['perguntas'] = $this->get_perguntas($checklist['id']);
        return $checklist;
    }


    public function normalizar_resposta($valor)
    {
        $valor = mb_strtolower(trim((string) $valor), 'UTF-8');

        return strtr($valor, [
            'á' => 'a',
            'à' => 'a',
            'ã' => 'a',
            'â' => 'a',
            'ä' => 'a',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'í' => 'i',
            'ì' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ó' => 'o',
            'ò' => 'o',
            'õ' => 'o',
            'ô' => 'o',
            'ö' => 'o',
            'ú' => 'u',
            'ù' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ç' => 'c',
        ]);
    }

    public function get_perguntas($idChecklist)
    {
        $query = $this->db
            ->where('id_checklist', $idChecklist)
            ->order_by('ordem', 'ASC')
            ->get($this->tablePerguntas);

        if ($query === false) {
            return [];
        }

        $perguntas = $query->result_array();


        foreach ($perguntas as &$pergunta) {
            $pergunta['bloqueia_normalizado'] = $this->normalizar_resposta($pergunta['bloqueia_abertura'] ?? '');
        }
        unset($pergunta);

        return $perguntas;
    }

    public function insert_checklist(array $data, array $perguntas)
    {
        if (empty($data['titulo']) || empty($data['tipo']) || empty($perguntas)) {
            return false;
        }

        $this->db->trans_start();
        $this->db->insert($this->table, $data);
        $idChecklist = $this->db->insert_id();

        $ordem = 1;
        foreach ($perguntas as $pergunta) {

            if (is_array($pergunta)) {
                $texto = trim((string) ($pergunta['texto'] ?? ''));
                $tipoResp = isset($pergunta['tipo_resposta']) && in_array($pergunta['tipo_resposta'], ['radio', 'text']) ? $pergunta['tipo_resposta'] : 'text';
                $bloq = isset($pergunta['bloqueia_abertura']) ? trim((string) $pergunta['bloqueia_abertura']) : null;
            } else {
                $texto = trim((string) $pergunta);
                $tipoResp = 'text';
                $bloq = null;
            }

            if ($texto === '') {
                continue;
            }

            $this->db->insert($this->tablePerguntas, [
                'id_checklist' => $idChecklist,
                'texto_pergunta' => $texto,
                'tipo_resposta' => $tipoResp,
                'bloqueia_abertura' => $bloq,
                'ordem' => $ordem++,
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $idChecklist;
    }

    public function clear_selected($tipo)
    {
        return $this->db
            ->where('tipo', $tipo)
            ->update($this->table, ['selecionado' => 0]);
    }

    public function select_default($idChecklist, $tipo)
    {
        $this->db->trans_start();
        $this->clear_selected($tipo);
        $this->db->where('id', $idChecklist)->update($this->table, ['selecionado' => 1]);
        $this->db->trans_complete();

        return $this->db->trans_status() !== FALSE;
    }

    public function delete_checklist($idChecklist)
    {
        $this->db->trans_start();
        $this->db->where('id', $idChecklist)->delete($this->table);
        $this->db->trans_complete();

        return $this->db->trans_status() !== FALSE;
    }
}
