<?php $pageTitle = 'Disparar Mensagens';
ob_start(); ?>

<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🚀 Disparar Mensagens</h1>
            <p class="text-sm text-gray-500 mt-1">Envio em massa com fila visual e delay randômico</p>
        </div>
    </div>

    <!-- Step 1: Configure -->
    <div id="configPanel" class="card mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">1. Configurar Disparo</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem Template</label>
                <select id="messageSelect" class="input-field">
                    <option value="">Selecione uma mensagem...</option>
                    <?php foreach ($messages as $msg): ?>
                        <option value="<?= $msg['id'] ?>" data-ai="<?= $msg['ai_enabled'] ?>"
                            data-body="<?= htmlspecialchars($msg['template_body']) ?>">
                            <?= htmlspecialchars($msg['title']) ?>
                            <?= $msg['ai_enabled'] ? ' 🤖' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Tags</label>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($tags as $tag): ?>
                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border-2 cursor-pointer transition-all text-sm
                            hover:shadow-md" style="border-color: <?= $tag['color'] ?>">
                            <input type="checkbox" class="tag-filter hidden" value="<?= $tag['id'] ?>">
                            <span class="w-2 h-2 rounded-full" style="background: <?= $tag['color'] ?>"></span>
                            <?= htmlspecialchars($tag['name']) ?>
                            <span class="text-gray-400">(
                                <?= $tag['contact_count'] ?>)
                            </span>
                        </label>
                    <?php endforeach; ?>
                    <?php if (empty($tags)): ?>
                        <p class="text-sm text-gray-400">Nenhuma tag criada. <a href="/tags"
                                class="text-brand-600 hover:underline">Criar tag</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 mb-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="dryRun" class="rounded border-gray-300 text-brand-600">
                <span class="text-sm text-gray-700">Simulação (não envia realmente)</span>
            </label>
        </div>

        <div class="flex gap-3">
            <button onclick="prepareQueue()" class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Preparar Fila
            </button>
        </div>
    </div>

    <!-- Step 2: Queue -->
    <div id="queuePanel" class="hidden">
        <!-- Progress Overview -->
        <div class="card mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-gray-800">Fila de Disparo</h3>
                    <p class="text-sm text-gray-500">
                        <span id="sentCount">0</span> / <span id="totalCount">0</span> mensagens
                    </p>
                </div>
                <div class="flex gap-3">
                    <button id="playBtn" onclick="startDispatch()" class="btn-success flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                        Iniciar
                    </button>
                    <button id="pauseBtn" onclick="pauseDispatch()"
                        class="btn-secondary flex items-center gap-2 hidden">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                        </svg>
                        Pausar
                    </button>
                </div>
            </div>

            <!-- Overall Progress -->
            <div class="progress-bar">
                <div id="overallProgress" class="progress-bar-fill bg-gradient-to-r from-green-400 to-emerald-500"
                    style="width: 0%"></div>
            </div>
        </div>

        <!-- Queue Items -->
        <div id="queueList" class="space-y-3">
            <!-- Items inserted by JS -->
        </div>
    </div>
</div>

<!-- Confetti Canvas -->
<canvas id="confettiCanvas" class="fixed inset-0 pointer-events-none z-50 hidden"
    style="width:100%;height:100%"></canvas>

<!-- Congratulations Modal -->
<div id="congratsModal"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md text-center animate-fade-in">
        <div class="text-6xl mb-4">🎉</div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Parabéns!</h2>
        <p class="text-gray-600 mb-2">Todos os disparos foram realizados com sucesso!</p>
        <p class="text-sm text-gray-500 mb-6">
            <span id="finalSent" class="text-emerald-600 font-bold">0</span> mensagem(ns) enviada(s)
            <?php if (false): ?>
                · <span id="finalFailed" class="text-red-500 font-bold">0</span> falha(s)
            <?php endif; ?>
        </p>
        <div class="flex gap-3">
            <a href="/dispatch/logs" class="btn-secondary flex-1">Ver Histórico</a>
            <button onclick="closeCongratsAndReset()" class="btn-primary flex-1">Novo Disparo</button>
        </div>
    </div>
</div>

<script>
    let queue = [];
    let currentIndex = 0;
    let isPaused = false;
    let isSending = false;
    let sentSuccess = 0;
    let sentFail = 0;

    function prepareQueue() {
        const messageId = document.getElementById('messageSelect').value;
        if (!messageId) {
            alert('Selecione uma mensagem template.');
            return;
        }

        const tagIds = Array.from(document.querySelectorAll('.tag-filter:checked')).map(cb => cb.value);

        const body = new URLSearchParams();
        body.append('message_id', messageId);
        tagIds.forEach(id => body.append('tag_ids[]', id));

        fetch('/dispatch/prepare', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        })
            .then(r => r.json())
            .then(data => {
                if (!data.success || data.total === 0) {
                    alert('Nenhum contato encontrado para os filtros selecionados.');
                    return;
                }

                queue = data.contacts.map(c => ({
                    ...c,
                    template: data.template,
                    aiEnabled: data.aiEnabled,
                    aiPrompt: data.aiPrompt,
                    messageId: messageId,
                    status: 'pending'
                }));

                currentIndex = 0;
                sentSuccess = 0;
                sentFail = 0;

                renderQueue();
                document.getElementById('configPanel').classList.add('hidden');
                document.getElementById('queuePanel').classList.remove('hidden');
                document.getElementById('totalCount').textContent = queue.length;
            });
    }

    function renderQueue() {
        const list = document.getElementById('queueList');
        list.innerHTML = queue.map((item, i) => `
        <div id="queueItem${i}" class="card flex items-center gap-4 animate-slide-in" style="animation-delay: ${i * 0.05}s">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm
                ${item.status === 'sent' ? 'bg-emerald-500' : item.status === 'failed' ? 'bg-red-500' : item.status === 'sending' ? 'bg-yellow-500 animate-pulse' : 'bg-gray-300'}">
                ${item.status === 'sent' ? '✓' : item.status === 'failed' ? '✗' : item.status === 'sending' ? '...' : (i + 1)}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">${item.name || item.whatsapp}</p>
                <p class="text-xs text-gray-500 font-mono">${item.whatsapp}</p>
                <div class="progress-bar mt-2" id="progressBar${i}">
                    <div class="progress-bar-fill ${item.status === 'sent' ? 'bg-emerald-500' : item.status === 'failed' ? 'bg-red-500' : 'bg-brand-500'}"
                        style="width: ${item.status === 'sent' || item.status === 'failed' ? '100' : '0'}%" id="progressFill${i}"></div>
                </div>
            </div>
            <div id="statusBadge${i}">
                ${item.status === 'sent' ? '<span class="badge bg-emerald-100 text-emerald-700">Enviada</span>'
                : item.status === 'failed' ? '<span class="badge bg-red-100 text-red-700">Falha</span>'
                    : item.status === 'sending' ? '<span class="badge bg-yellow-100 text-yellow-700">Enviando...</span>'
                        : '<span class="badge bg-gray-100 text-gray-500">Pendente</span>'}
            </div>
        </div>
    `).join('');
    }

    function startDispatch() {
        isPaused = false;
        document.getElementById('playBtn').classList.add('hidden');
        document.getElementById('pauseBtn').classList.remove('hidden');
        sendNext();
    }

    function pauseDispatch() {
        isPaused = true;
        document.getElementById('pauseBtn').classList.add('hidden');
        document.getElementById('playBtn').classList.remove('hidden');
    }

    function sendNext() {
        if (isPaused || currentIndex >= queue.length) {
            if (currentIndex >= queue.length) {
                onComplete();
            }
            return;
        }

        const item = queue[currentIndex];
        const idx = currentIndex;

        // Mark as sending
        item.status = 'sending';
        updateItemUI(idx);

        const dryRun = document.getElementById('dryRun').checked;

        // Random delay 2-10 seconds
        const delay = Math.floor(Math.random() * 8000) + 2000;

        // Animate progress bar during delay
        const fill = document.getElementById('progressFill' + idx);
        fill.style.transition = `width ${delay}ms linear`;
        fill.style.width = '80%';

        setTimeout(() => {
            // Send the message
            const body = new URLSearchParams();
            body.append('contact_id', item.id);
            body.append('message_id', item.messageId);
            body.append('template', item.template);
            body.append('ai_enabled', item.aiEnabled ? '1' : '0');
            body.append('ai_prompt', item.aiPrompt || '');
            body.append('dry_run', dryRun ? '1' : '0');

            fetch('/dispatch/send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        item.status = 'sent';
                        sentSuccess++;
                    } else {
                        item.status = 'failed';
                        sentFail++;
                    }

                    fill.style.width = '100%';
                    updateItemUI(idx);
                    updateOverall();

                    currentIndex++;
                    sendNext();
                })
                .catch((err) => {
                    item.status = 'failed';
                    sentFail++;
                    fill.style.width = '100%';
                    updateItemUI(idx);
                    updateOverall();
                    currentIndex++;
                    sendNext();
                });
        }, delay);
    }

    function updateItemUI(idx) {
        const item = queue[idx];
        const el = document.getElementById('queueItem' + idx);
        if (!el) return;

        const avatar = el.querySelector('div:first-child');
        const badge = document.getElementById('statusBadge' + idx);
        const fill = document.getElementById('progressFill' + idx);

        // Update avatar
        avatar.className = `w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm ${item.status === 'sent' ? 'bg-emerald-500' :
                item.status === 'failed' ? 'bg-red-500' :
                    item.status === 'sending' ? 'bg-yellow-500 animate-pulse' : 'bg-gray-300'}`;
        avatar.textContent = item.status === 'sent' ? '✓' : item.status === 'failed' ? '✗' : item.status === 'sending' ? '...' : (idx + 1);

        // Update badge
        if (item.status === 'sent') {
            badge.innerHTML = '<span class="badge bg-emerald-100 text-emerald-700">Enviada ✓</span>';
        } else if (item.status === 'failed') {
            badge.innerHTML = '<span class="badge bg-red-100 text-red-700">Falha ✗</span>';
        } else if (item.status === 'sending') {
            badge.innerHTML = '<span class="badge bg-yellow-100 text-yellow-700">Enviando...</span>';
        }

        // Update fill color
        if (item.status === 'sent') {
            fill.classList.remove('bg-brand-500', 'bg-red-500');
            fill.classList.add('bg-emerald-500');
        } else if (item.status === 'failed') {
            fill.classList.remove('bg-brand-500', 'bg-emerald-500');
            fill.classList.add('bg-red-500');
        }
    }

    function updateOverall() {
        const done = sentSuccess + sentFail;
        const total = queue.length;
        const pct = total > 0 ? Math.round((done / total) * 100) : 0;

        document.getElementById('sentCount').textContent = done;
        document.getElementById('overallProgress').style.width = pct + '%';
    }

    function onComplete() {
        document.getElementById('pauseBtn').classList.add('hidden');
        document.getElementById('playBtn').classList.add('hidden');

        document.getElementById('finalSent').textContent = sentSuccess;

        // Show confetti
        launchConfetti();

        // Show modal
        setTimeout(() => {
            document.getElementById('congratsModal').classList.remove('hidden');
        }, 500);
    }

    function closeCongratsAndReset() {
        document.getElementById('congratsModal').classList.add('hidden');
        document.getElementById('confettiCanvas').classList.add('hidden');
        document.getElementById('queuePanel').classList.add('hidden');
        document.getElementById('configPanel').classList.remove('hidden');
        queue = [];
        currentIndex = 0;
        sentSuccess = 0;
        sentFail = 0;
    }

    // ========== CONFETTI ==========
    function launchConfetti() {
        const canvas = document.getElementById('confettiCanvas');
        canvas.classList.remove('hidden');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        const ctx = canvas.getContext('2d');

        const pieces = [];
        const colors = ['#f43f5e', '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#06b6d4'];

        for (let i = 0; i < 200; i++) {
            pieces.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height - canvas.height,
                w: Math.random() * 10 + 5,
                h: Math.random() * 6 + 3,
                color: colors[Math.floor(Math.random() * colors.length)],
                rotation: Math.random() * 360,
                rotSpeed: (Math.random() - 0.5) * 10,
                vy: Math.random() * 3 + 2,
                vx: (Math.random() - 0.5) * 4,
                opacity: 1
            });
        }

        let frame = 0;
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            frame++;

            pieces.forEach(p => {
                p.x += p.vx;
                p.y += p.vy;
                p.rotation += p.rotSpeed;
                p.vy += 0.05; // gravity

                if (frame > 120) p.opacity -= 0.01;

                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate((p.rotation * Math.PI) / 180);
                ctx.globalAlpha = Math.max(0, p.opacity);
                ctx.fillStyle = p.color;
                ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                ctx.restore();
            });

            if (frame < 250) {
                requestAnimationFrame(animate);
            } else {
                canvas.classList.add('hidden');
            }
        }
        animate();
    }

    // Tag filter toggle styling
    document.querySelectorAll('.tag-filter').forEach(cb => {
        cb.addEventListener('change', function () {
            const label = this.parentElement;
            if (this.checked) {
                label.style.backgroundColor = label.style.borderColor;
                label.style.color = 'white';
            } else {
                label.style.backgroundColor = '';
                label.style.color = '';
            }
        });
    });
</script>

<?php $content = ob_get_clean();
include BASE_PATH . '/app/Views/layouts/main.php'; ?>