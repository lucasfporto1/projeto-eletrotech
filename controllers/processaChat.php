<?php
header('Content-Type: application/json');
require_once '../lib-php/libUtils.php';

carregarEnv(__DIR__ . '/../.env');

$flowiseUrl = $_ENV['FLOWISE_API_URL'] ?? '';

if (empty($flowiseUrl)) {
    echo json_encode(['erro' => 'Erro de configuração: URL do Flowise não encontrada no .env.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
$mensagemUsuario = $dados['mensagem'] ?? '';

if (empty($mensagemUsuario)) {
    echo json_encode(['erro' => 'A mensagem não pode estar vazia.']);
    exit;
}

$payload = [
    "question" => $mensagemUsuario
];

$ch = curl_init($flowiseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$resposta = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$erroCurl = curl_error($ch);
curl_close($ch);

if ($erroCurl) {
    echo json_encode(['erro' => 'Falha de comunicação com o motor de IA: ' . $erroCurl]);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode(['erro' => 'O servidor de IA retornou o código HTTP ' . $httpCode]);
    exit;
}

$resultado = json_decode($resposta, true);

$textoIA = $resultado['text'] ?? 'Não foi possível obter uma resposta do documento técnico.';

echo json_encode(['resposta' => $textoIA]);
