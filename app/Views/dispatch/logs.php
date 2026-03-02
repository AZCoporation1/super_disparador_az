<?php $pageTitle = 'Histórico de Disparos';
ob_start(); ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Histórico de Disparos</h1>
    <p class="text-sm text-gray-500 mt-1">Relatório completo de mensagens enviadas</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="card text-center">
        <p class="text-3xl font-bold text-gray-800">
            <?= number_format($stats['total'] ?? 0) ?>
        </p>
        <p class="text-xs text-gray-500">Total</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-bold text-emerald-600">
            <?= number_format($stats['sent'] ?? 0) ?>
        </p>
        <p class="text-xs text-gray-500">Enviadas</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-bold text-red-500">
            <?= number_format($stats['failed'] ?? 0) ?>
        </p>
        <p class="text-xs text-gray-500">Falhas</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-bold text-yellow-500">
            <?= number_format($stats['pending'] ?? 0) ?>
        </p>
        <p class="text-xs text-gray-500">Pendentes</p>
    </div>
</div>

<!-- Logs Table -->
<div class="card overflow-hidden p-0">
    <?php if (empty($logs)): ?>
        <div class="text-center py-12 text-gray-400">
            <p>Nenhum disparo realizado ainda.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contato</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">WhatsApp</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mensagem</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data/Hora</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span
                                    class="badge <?= $log['status'] === 'sent' ? 'bg-emerald-100 text-emerald-700' : ($log['status'] === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?>">
                                    <?= $log['status'] === 'sent' ? '✓ Enviada' : ($log['status'] === 'failed' ? '✗ Falha' : '● Pendente') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800">
                                <?= htmlspecialchars($log['contact_name'] ?? '—') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                <?= htmlspecialchars($log['contact_whatsapp'] ?? '') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                <?= htmlspecialchars(mb_substr($log['sent_message'] ?? '', 0, 80)) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= $log['sent_at'] ? date('d/m/Y H:i', strtotime($log['sent_at'])) : '—' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean();
include BASE_PATH . '/app/Views/layouts/main.php'; ?>