<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório Checklist - OS #<?= str_pad($idOs, 5, '0', STR_PAD_LEFT) ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; padding: 40px; }
        h1 { font-size: 20px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; font-size: 13px; }
        th { background: #eee; }
        .info { margin-bottom: 15px; font-size: 14px; }
        .observacao { margin-top: 25px; padding: 12px; border: 1px solid #999; background: #f7f7f7; }
        .no-print { margin-top: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <h1>Relatório de Checklist de <?= $tipo === 'inicio' ? 'Início' : 'Fim' ?> — OS #<?= str_pad($idOs, 5, '0', STR_PAD_LEFT) ?></h1>

    <div class="info">
        <strong>Eletricista:</strong> <?= htmlspecialchars($ordem['nome_eletricista'] ?? '-') ?><br>
        <strong>Data OS:</strong> <?= !empty($ordem['data_os']) ? date('d/m/Y', strtotime($ordem['data_os'])) : '-' ?><br>
        <strong>Status atual da OS:</strong> <?= htmlspecialchars($ordem['status']) ?>
    </div>

    <table>
        <thead>
            <tr><th>Pergunta</th><th>Resposta</th></tr>
        </thead>
        <tbody>
            <?php if (!empty($respostas)): ?>
                <?php foreach ($respostas as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['texto_pergunta']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($r['resposta'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2">Nenhuma resposta registrada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (!empty($status['observacao'])): ?>
        <div class="observacao">
            <strong>Observação da finalização:</strong><br>
            <?= nl2br(htmlspecialchars($status['observacao'])) ?><br>
            <small>Finalizado em: <?= date('d/m/Y H:i', strtotime($status['data_finalizacao'])) ?></small>
        </div>
    <?php elseif (!empty($status['bloqueado'])): ?>
        <div class="observacao"><strong>Este checklist ainda está pendente de revisão.</strong></div>
    <?php endif; ?>

    <div class="no-print">
        <button onclick="window.print()">Imprimir / Salvar como PDF</button>
    </div>
</body>
</html>