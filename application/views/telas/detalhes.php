<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if (!empty($materiais)): ?>
    <table class="table table-dark table-sm table-bordered text-center mb-0">
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