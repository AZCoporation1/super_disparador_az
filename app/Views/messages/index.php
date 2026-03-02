<?php $pageTitle = 'Mensagens';
ob_start(); ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Mensagens</h1>
        <p class="text-sm text-gray-500 mt-1">Seus templates de mensagem</p>
    </div>
    <a href="/messages/compose" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Nova Mensagem
    </a>
</div>

<?php if (empty($messages)): ?>
    <div class="card text-center py-16 text-gray-400">
        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        <p class="text-lg font-medium">Nenhuma mensagem criada</p>
        <p class="text-sm mt-1">Crie seu primeiro template de mensagem</p>
        <a href="/messages/compose" class="btn-primary inline-flex items-center gap-2 mt-4">Criar Mensagem</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($messages as $msg): ?>
            <div class="card animate-fade-in">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-800">
                            <?= htmlspecialchars($msg['title']) ?>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?>
                        </p>
                    </div>
                    <?php if ($msg['ai_enabled']): ?>
                        <span class="badge bg-purple-100 text-purple-700">🤖 IA</span>
                    <?php endif; ?>
                </div>
                <div class="bg-whatsapp-bg rounded-xl p-3 mb-4">
                    <div class="bg-whatsapp-light rounded-lg p-3 text-sm text-gray-800 max-h-24 overflow-hidden">
                        <?= nl2br(htmlspecialchars(mb_substr($msg['template_body'], 0, 150))) ?>
                        <?= mb_strlen($msg['template_body']) > 150 ? '...' : '' ?>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="/messages/compose?id=<?= $msg['id'] ?>" class="btn-secondary text-sm flex-1 text-center">Editar</a>
                    <form method="POST" action="/messages/delete" onsubmit="return confirm('Excluir essa mensagem?')"
                        class="flex-1">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <button class="btn-danger text-sm w-full">Excluir</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php $content = ob_get_clean();
include BASE_PATH . '/app/Views/layouts/main.php'; ?>