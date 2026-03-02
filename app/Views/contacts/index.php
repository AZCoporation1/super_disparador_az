<?php $pageTitle = 'Contatos';
ob_start(); ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Contatos</h1>
        <p class="text-sm text-gray-500 mt-1">
            <?= count($contacts) ?> contato(s) cadastrado(s)
        </p>
    </div>
    <div class="flex gap-3">
        <a href="/contacts/template" class="btn-secondary flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Modelo CSV
        </a>
        <a href="/contacts/import" class="btn-secondary flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            Importar CSV
        </a>
        <a href="/contacts/create" class="btn-primary flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Novo Contato
        </a>
    </div>
</div>

<!-- Search -->
<div class="card mb-6">
    <form method="GET" action="/contacts" class="flex gap-3">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
            placeholder="Buscar por nome ou WhatsApp..." class="input-field flex-1">
        <button type="submit" class="btn-primary text-sm">Buscar</button>
        <?php if (!empty($search)): ?>
            <a href="/contacts" class="btn-secondary text-sm">Limpar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Tag Assignment Bar (hidden until contacts are selected) -->
<div id="tagAssignBar" class="card mb-6 hidden animate-fade-in">
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600"><strong id="selectedCount">0</strong> contato(s) selecionado(s)</span>
        <select id="tagSelect" class="input-field w-auto">
            <option value="">Atribuir tag...</option>
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag['id'] ?>">
                    <?= htmlspecialchars($tag['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button onclick="assignTags()" class="btn-success text-sm">Atribuir</button>
    </div>
</div>

<!-- Contacts Table -->
<div class="card overflow-hidden p-0">
    <?php if (empty($contacts)): ?>
        <div class="text-center py-16 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="text-lg font-medium">Nenhum contato ainda</p>
            <p class="text-sm mt-1">Importe um CSV ou adicione manualmente</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" onchange="toggleAll()" class="rounded border-gray-300">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            WhatsApp</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tags
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($contacts as $contact): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" class="contact-cb rounded border-gray-300" value="<?= $contact['id'] ?>"
                                    onchange="updateSelection()">
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-800">
                                    <?= htmlspecialchars($contact['name'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600 font-mono">
                                    <?= htmlspecialchars($contact['whatsapp']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($contact['tags'] ?? [] as $tag): ?>
                                        <span class="badge text-white text-[10px]" style="background-color: <?= $tag['color'] ?>">
                                            <?= htmlspecialchars($tag['name']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="/contacts/edit?id=<?= $contact['id'] ?>"
                                        class="text-gray-400 hover:text-brand-600 transition-colors" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="/contacts/delete"
                                        onsubmit="return confirm('Excluir este contato?')" class="inline">
                                        <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                                        <button class="text-gray-400 hover:text-red-500 transition-colors" title="Excluir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleAll() {
        const checked = document.getElementById('selectAll').checked;
        document.querySelectorAll('.contact-cb').forEach(cb => cb.checked = checked);
        updateSelection();
    }

    function updateSelection() {
        const selected = document.querySelectorAll('.contact-cb:checked');
        const bar = document.getElementById('tagAssignBar');
        const count = document.getElementById('selectedCount');
        count.textContent = selected.length;
        bar.classList.toggle('hidden', selected.length === 0);
    }

    function assignTags() {
        const tagId = document.getElementById('tagSelect').value;
        if (!tagId) { alert('Selecione uma tag.'); return; }

        const ids = Array.from(document.querySelectorAll('.contact-cb:checked')).map(cb => cb.value);

        fetch('/contacts/assign-tags', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'tag_id=' + tagId + '&' + ids.map(id => 'contact_ids[]=' + id).join('&')
        })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                if (data.success) location.reload();
            });
    }
</script>

<?php $content = ob_get_clean();
include BASE_PATH . '/app/Views/layouts/main.php'; ?>