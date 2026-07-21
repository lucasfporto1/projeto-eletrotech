<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if (!empty($materiais)): ?>
    <h5 class="text-center mb-3">Materiais Utilizados</h5>
    <table class="table table-dark table-sm table-bordered text-center mb-4">
        <thead>
            <tr>
                <th>Material</th>
                <th>Qtd. Utilizada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materiais as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nome_produto']) ?></td>
                    <td><?= $item['qtd_utilizada'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="text-center">Nenhum material registrado para esta OS.</p>
<?php endif; ?>

<?php if (!empty($respostas)): ?>
    <h5 class="text-center mb-3">Checklist e Respostas</h5>
    <table class="table table-dark table-sm table-bordered text-center">
        <thead>
            <tr>
                <th>Pergunta</th>
                <th>Resposta</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($respostas as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['texto_pergunta']) ?></td>
                    <td>
                        <?= htmlspecialchars(ucfirst($item['resposta'])) ?>
                        <?php if (!empty($item['motivo_nao'])): ?>
                            <div class="text-start mt-2"><strong>Motivo:</strong> <?= htmlspecialchars($item['motivo_nao']) ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="text-center">Nenhuma resposta de checklist registrada para esta OS.</p>
<?php endif; ?>