<?php $pageTitle = 'Editar Contato';
ob_start(); ?>

<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="/contacts" class="text-sm text-gray-500 hover:text-brand-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar para contatos
        </a>
    </div>

    <div class="card">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Editar Contato</h2>

        <form method="POST" action="/contacts/update" class="space-y-5">
            <input type="hidden" name="id" value="<?= $contact['id'] ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                <input type="text" name="name" value="<?= htmlspecialchars($contact['name'] ?? '') ?>"
                    class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp <span
                        class="text-red-500">*</span></label>
                <input type="text" name="whatsapp" required value="<?= htmlspecialchars($contact['whatsapp']) ?>"
                    class="input-field">
            </div>
            <button type="submit" class="btn-primary w-full">Atualizar Contato</button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean();
include BASE_PATH . '/app/Views/layouts/main.php'; ?>