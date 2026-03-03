<?php $pageTitle = 'Configurações';
ob_start(); ?>

<div class="max-w-3xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">Configurações</h1>

    <!-- ============================================ -->
    <!-- WhatsApp Setup (Evolution API) -->
    <!-- ============================================ -->
    <div class="card relative overflow-hidden">
        <!-- Gradient accent bar -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-whatsapp-dark to-whatsapp-green"></div>

        <div class="flex items-center justify-between mb-4 pt-2">
            <div>
                <h3 class="font-semibold text-gray-800 text-lg">📱 Setup de Conexão WhatsApp</h3>
                <p class="text-sm text-gray-500">Configure sua instância da Evolution API para enviar mensagens.</p>
            </div>
            <!-- Connection status badge -->
            <?php
            $status = $credentials['connection_status'] ?? 'unconfigured';
            $statusConfig = [
                'active' => ['label' => 'Conectado', 'color' => 'bg-emerald-100 text-emerald-700', 'dot' => 'bg-emerald-500'],
                'inactive' => ['label' => 'Inativo', 'color' => 'bg-red-100 text-red-700', 'dot' => 'bg-red-500'],
                'unconfigured' => ['label' => 'Não Configurado', 'color' => 'bg-gray-100 text-gray-500', 'dot' => 'bg-gray-400'],
            ];
            $sc = $statusConfig[$status] ?? $statusConfig['unconfigured'];
            ?>
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?= $sc['color'] ?>"
                id="statusBadge">
                <span class="w-2 h-2 rounded-full <?= $sc['dot'] ?> animate-pulse" id="statusDot"></span>
                <span id="statusLabel"><?= $sc['label'] ?></span>
            </span>
        </div>

        <form id="evoForm" class="space-y-4">
            <input type="hidden" name="action" value="save_evolution_credentials">

            <!-- URL da API -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL da API (Base URL) <span
                        class="text-red-400">*</span></label>
                <input type="url" name="evolution_base_url" id="evoBaseUrl"
                    value="<?= htmlspecialchars($credentials['base_url'] ?? '') ?>"
                    placeholder="https://evolution.seuservidor.com" class="input-field" required>
                <p class="text-xs text-gray-400 mt-1">A URL base da sua Evolution API (sem barra no final)</p>
            </div>

            <!-- Nome da Instância -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Instância <span
                        class="text-red-400">*</span></label>
                <input type="text" name="evolution_instance_name" id="evoInstance"
                    value="<?= htmlspecialchars($credentials['instance_name'] ?? '') ?>" placeholder="minha-instancia"
                    class="input-field" required>
                <p class="text-xs text-gray-400 mt-1">O nome exato da instância conectada no seu Evolution API</p>
            </div>

            <!-- Token (API Key) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Token (API Key) <span
                        class="text-red-400">*</span></label>
                <div class="relative">
                    <input type="password" name="evolution_token" id="evoToken"
                        value="<?= htmlspecialchars($credentials['token'] ?? '') ?>" placeholder="seu-token-aqui"
                        class="input-field pr-12" required>
                    <button type="button" onclick="toggleTokenVisibility()" id="toggleTokenBtn"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                        title="Mostrar/Ocultar token">
                        <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Sua chave de API da Evolution API (será armazenada de forma
                    criptografada)</p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-2">
                <button type="submit" id="saveEvoBtn"
                    class="flex-1 bg-whatsapp-dark hover:bg-whatsapp-green text-white px-5 py-3 rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Salvar e Testar Conexão
                </button>
                <button type="button" onclick="testExistingConnection()" id="testEvoBtn"
                    class="btn-secondary flex items-center gap-2" <?= empty($credentials) ? 'disabled' : '' ?>>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Re-testar
                </button>
            </div>
        </form>

        <!-- Toast result area -->
        <div id="evoResult" class="mt-4 hidden animate-fade-in"></div>
    </div>

    <!-- ============================================ -->
    <!-- Profile -->
    <!-- ============================================ -->
    <div class="card">
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

    <!-- ============================================ -->
    <!-- OpenAI -->
    <!-- ============================================ -->
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-2">🤖 OpenAI (ChatGPT)</h3>
        <p class="text-sm text-gray-500 mb-4">A IA é usada para personalizar mensagens de forma inteligente.</p>

        <div class="flex items-center gap-2 mb-4">
            <span class="w-2 h-2 rounded-full <?= $openaiConfigured ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
            <span class="text-sm <?= $openaiConfigured ? 'text-emerald-600' : 'text-red-500' ?>">
                <?= $openaiConfigured ? 'API Key configurada' : 'API Key não configurada no .env' ?>
            </span>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="testOpenAI()" class="btn-secondary" id="testAiBtn">Testar Conexão
                OpenAI</button>
        </div>
        <div id="aiResult" class="mt-3 hidden"></div>
    </div>

    <!-- ============================================ -->
    <!-- System Info -->
    <!-- ============================================ -->
    <div class="card bg-gray-50 border-gray-200">
        <h3 class="font-semibold text-gray-800 mb-2">ℹ️ Informações do Sistema</h3>
        <div class="space-y-2 text-sm text-gray-600">
            <p><strong>Versão:</strong> 1.0.0</p>
            <p><strong>Stack:</strong> PHP MVC + MySQL + Tailwind CDN</p>
            <p><strong>IA:</strong> OpenAI (modelo configurável via .env)</p>
            <p><strong>Integração:</strong> Evolution API (dinâmica por usuário)</p>
        </div>
    </div>
</div>

<script>
    // ===========================
    // Toggle token visibility
    // ===========================
    function toggleTokenVisibility() {
        const input = document.getElementById('evoToken');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');
        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    }

    // ===========================
    // Show toast notification
    // ===========================
    function showToast(elementId, success, message, extra = '') {
        const el = document.getElementById(elementId);
        el.classList.remove('hidden');
        const bgClass = success ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700';
        const icon = success ? '✅' : '❌';
        el.innerHTML = `<div class="p-4 rounded-xl border ${bgClass} text-sm animate-fade-in">
            <div class="font-semibold">${icon} ${message}</div>
            ${extra ? `<div class="mt-1 text-xs opacity-75">${extra}</div>` : ''}
        </div>`;

        // Auto-hide after 8s
        setTimeout(() => { el.classList.add('hidden'); }, 8000);
    }

    // ===========================
    // Update status badge
    // ===========================
    function updateStatusBadge(status) {
        const badge = document.getElementById('statusBadge');
        const dot = document.getElementById('statusDot');
        const label = document.getElementById('statusLabel');

        const config = {
            active: { label: 'Conectado', badgeClass: 'bg-emerald-100 text-emerald-700', dotClass: 'bg-emerald-500' },
            inactive: { label: 'Inativo', badgeClass: 'bg-red-100 text-red-700', dotClass: 'bg-red-500' },
            unconfigured: { label: 'Não Configurado', badgeClass: 'bg-gray-100 text-gray-500', dotClass: 'bg-gray-400' },
        };

        const c = config[status] || config.unconfigured;
        badge.className = `inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ${c.badgeClass}`;
        dot.className = `w-2 h-2 rounded-full ${c.dotClass} animate-pulse`;
        label.textContent = c.label;
    }

    // ===========================
    // Save & Test Evolution credentials
    // ===========================
    document.getElementById('evoForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const btn = document.getElementById('saveEvoBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Testando conexão...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch('/settings/update', {
            method: 'POST',
            body: new URLSearchParams(formData),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('evoResult', true, 'Conexão verificada e credenciais salvas!', 'Sua instância está conectada e pronta para disparos.');
                    updateStatusBadge('active');
                    document.getElementById('testEvoBtn').disabled = false;
                } else {
                    const savedNote = data.saved ? 'Credenciais foram salvas, mas a conexão está inativa.' : '';
                    showToast('evoResult', false, data.error || 'Falha na conexão', savedNote);
                    updateStatusBadge(data.saved ? 'inactive' : 'unconfigured');
                }
            })
            .catch(err => {
                showToast('evoResult', false, 'Erro de rede ao testar conexão.', err.message);
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    });

    // ===========================
    // Re-test existing connection
    // ===========================
    function testExistingConnection() {
        const btn = document.getElementById('testEvoBtn');
        btn.textContent = 'Testando...';
        btn.disabled = true;

        fetch('/settings/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=test_evolution'
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('evoResult', true, 'Conexão ativa!', 'Estado: ' + (data.state || 'open'));
                    updateStatusBadge('active');
                } else {
                    showToast('evoResult', false, data.error || 'Falha na conexão');
                    updateStatusBadge('inactive');
                }
            })
            .finally(() => {
                btn.textContent = 'Re-testar';
                btn.disabled = false;
            });
    }

    // ===========================
    // Test OpenAI
    // ===========================
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
                    result.innerHTML = '<div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl text-sm animate-fade-in">✅ OpenAI conectada! Modelo: ' + data.model + '</div>';
                } else {
                    result.innerHTML = '<div class="p-3 bg-red-50 text-red-700 rounded-xl text-sm animate-fade-in">❌ ' + (data.error || 'Falha na conexão') + '</div>';
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