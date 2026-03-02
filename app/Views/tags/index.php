<?php $pageTitle = 'Tags / Categorias';
ob_start(); ?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tags / Categorias</h1>
            <p class="text-sm text-gray-500 mt-1">Organize seus contatos por categorias</p>
        </div>
    </div>

    <!-- Create Tag Form -->
    <div class="card mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">Criar Nova Tag</h3>
        <form method="POST" action="/tags/store" class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Tag</label>
                <input type="text" name="name" required placeholder="Ex: Clientes VIP" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cor</label>
                <input type="color" name="color" value="#6366f1"
                    class="w-12 h-[42px] rounded-xl border border-gray-200 cursor-pointer">
            </div>
            <button type="submit" class="btn-primary whitespace-nowrap">Criar Tag</button>
        </form>
    </div>

    <!-- Tags List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if (empty($tags)): ?>
            <div class="col-span-2 card text-center py-12 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <p>Nenhuma tag criada ainda</p>
            </div>
        <?php else: ?>
            <?php foreach ($tags as $tag): ?>
                <div class="card animate-fade-in flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full" style="background-color: <?= htmlspecialchars($tag['color']) ?>">
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">
                                <?= htmlspecialchars($tag['name']) ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?= $tag['contact_count'] ?> contato(s)
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Edit inline -->
                        <button
                            onclick="editTag(<?= $tag['id'] ?>, '<?= htmlspecialchars($tag['name'], ENT_QUOTES) ?>', '<?= $tag['color'] ?>')"
                            class="text-gray-400 hover:text-brand-600 transition-colors" title="Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <form method="POST" action="/tags/delete" onsubmit="return confirm('Excluir essa tag?')" class="inline">
                            <input type="hidden" name="id" value="<?= $tag['id'] ?>">
                            <button class="text-gray-400 hover:text-red-500 transition-colors" title="Excluir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md animate-fade-in">
        <h3 class="font-bold text-lg text-gray-800 mb-4">Editar Tag</h3>
        <form method="POST" action="/tags/update" class="space-y-4">
            <input type="hidden" name="id" id="editId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                <input type="text" name="name" id="editName" required class="input-field">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cor</label>
                <input type="color" name="color" id="editColor" class="w-12 h-10 rounded-xl border border-gray-200">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1">Salvar</button>
                <button type="button" onclick="closeEditModal()" class="btn-secondary flex-1">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editTag(id, name, color) {
        document.getElementById('editId').value = id;
        document.getElementById('editName').value = name;
        document.getElementById('editColor').value = color;
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>

<?php $content = ob_get_clean();
include BASE_PATH . '/app/Views/layouts/main.php'; ?>