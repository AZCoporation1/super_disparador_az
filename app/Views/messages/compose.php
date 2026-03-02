<?php $pageTitle = 'Compor Mensagem';
ob_start(); ?>

<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="/messages" class="text-sm text-gray-500 hover:text-brand-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar para mensagens
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Editor -->
        <div class="card">
            <h2 class="text-xl font-bold text-gray-800 mb-6">
                <?= $message ? 'Editar' : 'Nova' ?> Mensagem
            </h2>

            <form method="POST" action="/messages/store" class="space-y-5">
                <?php if ($message): ?>
                    <input type="hidden" name="id" value="<?= $message['id'] ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($message['title'] ?? '') ?>"
                        placeholder="Nome do template" class="input-field">
                </div>

                <!-- Macro Buttons -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Macros (clique para inserir)</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="insertMacro('[nome]')"
                            class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors">
                            [nome]
                        </button>
                        <button type="button" onclick="insertMacro('[whatsapp]')"
                            class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition-colors">
                            [whatsapp]
                        </button>
                        <button type="button" onclick="insertMacro('[tag]')"
                            class="px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-200 transition-colors">
                            [tag]
                        </button>
                        <button type="button" onclick="insertMacro('[categoria]')"
                            class="px-3 py-1.5 bg-orange-100 text-orange-700 rounded-lg text-sm font-medium hover:bg-orange-200 transition-colors">
                            [categoria]
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem</label>
                    <textarea id="messageBody" name="template_body" rows="8"
                        placeholder="Olá, [nome]! Tudo bem? 😊&#10;&#10;Gostaríamos de..."
                        class="input-field resize-none"><?= htmlspecialchars($message['template_body'] ?? '') ?></textarea>
                </div>

                <!-- AI Toggle -->
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="ai_enabled" value="1" id="aiToggle" <?= ($message['ai_enabled'] ?? false) ? 'checked' : '' ?>
                        onchange="toggleAI()"
                        class="w-5 h-5 text-purple-600 rounded border-gray-300">
                        <div>
                            <span class="font-medium text-purple-800">🤖 Personalizar com IA</span>
                            <p class="text-xs text-purple-600">O ChatGPT vai personalizar cada mensagem individualmente
                            </p>
                        </div>
                    </label>

                    <div id="aiPromptArea" class="mt-4 <?= ($message['ai_enabled'] ?? false) ? '' : 'hidden' ?>">
                        <label class="block text-sm font-medium text-purple-700 mb-2">Prompt para IA (instruções
                            extras)</label>
                        <textarea name="ai_prompt" rows="3"
                            placeholder="Ex: Seja amigável e use o nome da pessoa. Mencione promoção de fim de ano..."
                            class="input-field border-purple-200 focus:ring-purple-500"><?= htmlspecialchars($message['ai_prompt'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-primary flex-1">Salvar Mensagem</button>
                    <button type="button" onclick="previewMessage()"
                        class="btn-secondary flex-1">Pré-visualizar</button>
                </div>
            </form>
        </div>

        <!-- WhatsApp Preview -->
        <div>
            <div class="sticky top-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Pré-visualização WhatsApp
                </h3>

                <!-- WhatsApp Phone Frame -->
                <div class="bg-gray-800 rounded-3xl p-3 shadow-2xl max-w-sm mx-auto">
                    <!-- Status Bar -->
                    <div
                        class="bg-whatsapp-dark rounded-t-2xl px-4 py-2 flex items-center justify-between text-white text-xs">
                        <span>
                            <?= date('H:i') ?>
                        </span>
                        <div class="flex gap-1 items-center">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21l-8-8h5V3h6v10h5z" />
                            </svg>
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2 22h20V2z" />
                            </svg>
                        </div>
                    </div>

                    <!-- WhatsApp Header -->
                    <div class="bg-whatsapp-dark px-4 py-3 flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            <span id="previewInitial">J</span>
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm" id="previewName">João Silva</p>
                            <p class="text-green-300 text-xs">online</p>
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="bg-[#efeae2] px-3 py-4 min-h-[300px] flex flex-col justify-end"
                        style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 80 80%22 opacity=%220.03%22><circle cx=%2220%22 cy=%2220%22 r=%222%22/><circle cx=%2260%22 cy=%2260%22 r=%222%22/></svg>')">
                        <!-- Message Bubble -->
                        <div class="bg-whatsapp-light rounded-lg rounded-tr-none p-3 max-w-[85%] ml-auto shadow-sm">
                            <p class="text-sm text-gray-800 whitespace-pre-wrap" id="previewText">
                                Clique em "Pré-visualizar" para ver a mensagem aqui
                            </p>
                            <div class="flex justify-end items-center gap-1 mt-1">
                                <span class="text-[10px] text-gray-500">
                                    <?= date('H:i') ?>
                                </span>
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="bg-whatsapp-dark rounded-b-2xl px-3 py-2 flex items-center gap-2">
                        <div class="flex-1 bg-gray-700 rounded-full px-4 py-2 text-gray-400 text-sm">
                            Mensagem
                        </div>
                        <div class="w-10 h-10 bg-whatsapp-green rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function insertMacro(macro) {
        const textarea = document.getElementById('messageBody');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + macro + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + macro.length;
        textarea.focus();
    }

    function toggleAI() {
        const area = document.getElementById('aiPromptArea');
        area.classList.toggle('hidden');
    }

    function previewMessage() {
        const template = document.getElementById('messageBody').value;

        fetch('/messages/preview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'template=' + encodeURIComponent(template)
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('previewText').textContent = data.preview;
                    document.getElementById('previewName').textContent = data.contact.name;
                    document.getElementById('previewInitial').textContent = data.contact.name.charAt(0).toUpperCase();
                }
            });
    }

    // Live preview
    document.getElementById('messageBody').addEventListener('input', function () {
        const text = this.value || 'Sua mensagem aparecerá aqui...';
        document.getElementById('previewText').textContent = text;
    });
</script>

<?php $content = ob_get_clean();
include BASE_PATH . '/app/Views/layouts/main.php'; ?>