<?php $pageTitle = 'Dashboard';
ob_start(); ?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card animate-fade-in">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Contatos</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">
                    <?= number_format($totalContacts) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="card animate-fade-in" style="animation-delay: 0.1s;">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Tags Criadas</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">
                    <?= number_format($totalTags) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="card animate-fade-in" style="animation-delay: 0.2s;">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Enviadas</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">
                    <?= number_format($stats['sent'] ?? 0) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
    </div>

    <div class="card animate-fade-in" style="animation-delay: 0.3s;">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Falhas</p>
                <p class="text-3xl font-bold text-red-500 mt-1">
                    <?= number_format($stats['failed'] ?? 0) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <a href="/contacts/import" class="card hover:shadow-md transition-shadow group cursor-pointer">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Importar Contatos</h3>
                <p class="text-sm text-gray-500">Upload CSV do Excel ou Google Sheets</p>
            </div>
        </div>
    </a>

    <a href="/messages/compose" class="card hover:shadow-md transition-shadow group cursor-pointer">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Criar Mensagem</h3>
                <p class="text-sm text-gray-500">Template com macros e IA</p>
            </div>
        </div>
    </a>

    <a href="/dispatch" class="card hover:shadow-md transition-shadow group cursor-pointer">
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-brand-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Disparar Mensagens</h3>
                <p class="text-sm text-gray-500">Envio em massa com fila visual</p>
            </div>
        </div>
    </a>
</div>

<!-- Recent Activity -->
<div class="card">
    <h3 class="font-semibold text-gray-800 mb-4">Atividade Recente</h3>
    <?php if (empty($recentLogs)): ?>
        <div class="text-center py-8 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p>Nenhum disparo realizado ainda</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($recentLogs as $log): ?>
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-2 h-2 rounded-full <?= $log['status'] === 'sent' ? 'bg-emerald-500' : ($log['status'] === 'failed' ? 'bg-red-500' : 'bg-yellow-500') ?>"></span>
                        <div>
                            <p class="text-sm font-medium text-gray-700">
                                <?= htmlspecialchars($log['contact_name'] ?? $log['contact_whatsapp']) ?>
                            </p>
                            <p class="text-xs text-gray-400">
                                <?= $log['sent_at'] ?? $log['created_at'] ?>
                            </p>
                        </div>
                    </div>
                    <span
                        class="badge <?= $log['status'] === 'sent' ? 'bg-emerald-100 text-emerald-700' : ($log['status'] === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?>">
                        <?= $log['status'] === 'sent' ? 'Enviada' : ($log['status'] === 'failed' ? 'Falha' : 'Pendente') ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean();
include BASE_PATH . '/app/Views/layouts/main.php'; ?>