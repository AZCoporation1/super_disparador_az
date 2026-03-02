<?php $pageTitle = 'Configurações';
ob_start(); ?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Configurações</h1>

    <!-- Profile -->
    <div class="card mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">👤 Perfil</h3>
        <form method="POST" action="/settings/update" class="space-y-4">
            <input type="hidden" name="action" value="update_profile">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                        class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                        class="input-field">
                </div>
            </div>
            <button type="submit" class="btn-primary">Salvar Perfil</button>
        </form>
    </div>

    <!-- Evolution API -->
    <div class="card mb-6">
        <h3 class="font-semibold text-gray-800 mb-2">📱 Evolution API (WhatsApp)</h3>
        <p class="text-sm text-gray-500 mb-4">Configure sua instância do Evolution API para enviar mensagens.</p>

        <div class="flex items-center gap-2 mb-4">
            <span class="w-2 h-2 rounded-full <?= $evolutionConfigured ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
            <span class="text-sm <?= $evolutionConfigured ? 'text-emerald-600' : 'text-red-500' ?>">
                <?= $evolutionConfigured ? 'URL da API configurada no .env' : 'URL da API não configurada no .env' ?>
            </span>
        </div>

        <form method="POST" action="/settings/update" class="space-y-4">
            <input type="hidden" name="action" value="evolution_instance">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Instância</label>
                <input type="text" name="evolution_instance"
                    value="<?= htmlspecialchars($user['evolution_instance'] ?? '') ?>" placeholder="minha-instancia"
                    class="input-field">
                <p class="text-xs text-gray-400 mt-1">O nome da instância conectada no seu Evolution API</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Salvar Instância</button>
                <button type="button" onclick="testEvolution()" class="btn-secondary" id="testEvoBtn">Testar
                    Conexão</button>
            </div>
        </form>
        <div id="evoResult" class="mt-3 hidden"></div>
    </div>

    <!-- OpenAI -->
    <div class="card mb-6">
        <h3 class="font-semibold text-gray-800 mb-2">🤖 OpenAI (ChatGPT)</h3>
        <p class="text-sm text-gray-500 mb-4">A IA é usada para personalizar mensagens de forma inteligente.</p>

        <div class="flex items-center gap-2 mb-4">
            <span class="w-2 h-2 rounded-full <?= $openaiConfigured ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
            <span class="text-sm <?= $openaiConfigured ? 'text-emerald-600' : 'text-red-500' ?>">
                <?= $openaiConfigured ? 'API Key configurada no .env' : 'API Key não configurada no .env' ?>
            </span>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="testOpenAI()" class="btn-secondary" id="testAiBtn">Testar Conexão
                OpenAI</button>
        </div>
        <div id="aiResult" class="mt-3 hidden"></div>
    </div>

    <!-- Info -->
    <div class="card bg-gray-50 border-gray-200">
        <h3 class="font-semibold text-gray-800 mb-2">ℹ️ Informações do Sistema</h3>
        <div class="space-y-2 text-sm text-gray-600">
            <p><strong>Versão:</strong> 1.0.0</p>
            <p><strong>Stack:</strong> PHP MVC + MySQL + Tailwind CDN</p>
            <p><strong>IA:</strong> OpenAI (modelo configurável via .env)</p>
            <p><strong>Integração:</strong> Evolution API</p>
        </div>
    </div>
</div>

<script>
    function testEvolution() {
        const instance = document.querySelector('[name="evolution_instance"]').value;
        const btn = document.getElementById('testEvoBtn');
        const result = document.getElementById('evoResult');

        btn.textContent = 'Testando...';
        btn.disabled = true;

        fetch('/settings/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=test_evolution&instance=' + encodeURIComponent(instance)
        })
            .then(r => r.json())
            .then(data => {
                result.classList.remove('hidden');
                if (data.success) {
                    result.innerHTML = '<div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl text-sm">✅ Conexão bem-sucedida!</div>';
                } else {
                    result.innerHTML = '<div class="p-3 bg-red-50 text-red-700 rounded-xl text-sm">❌ ' + (data.error || 'Falha na conexão') + '</div>';
                }
            })
            .finally(() => {
                btn.textContent = 'Testar Conexão';
                btn.disabled = false;
            });
    }

    function testOpenAI() {
        const btn = document.getElementById('testAiBtn');
        const result = document.getElementById('aiResult');

        btn.textContent = 'Testando...';
        btn.disabled = true;

        fetch('/settings/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=test_openai'
        })
            .then(r => r.json())
            .then(data => {
                result.classList.remove('hidden');
                if (data.success) {
                    result.innerHTML = '<div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl text-sm">✅ OpenAI conectada! Modelo: ' + data.model + '</div>';
                } else {
                    result.innerHTML = '<div class="p-3 bg-red-50 text-red-700 rounded-xl text-sm">❌ ' + (data.error || 'Falha na conexão') + '</div>';
                }
            })
            .finally(() => {
                btn.textContent = 'Testar Conexão OpenAI';
                btn.disabled = false;
            });
    }
</script>

<?php $content = ob_get_clean();
include BASE_PATH . '/app/Views/layouts/main.php'; ?>