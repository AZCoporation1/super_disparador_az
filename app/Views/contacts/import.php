<?php $pageTitle = 'Importar Contatos';
ob_start(); ?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="/contacts" class="text-sm text-gray-500 hover:text-brand-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar para contatos
        </a>
    </div>

    <div class="card mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Importar Contatos via CSV</h2>
        <p class="text-sm text-gray-500 mb-6">Faça upload de um arquivo CSV exportado do Google Sheets ou Excel.</p>

        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <h3 class="text-sm font-semibold text-blue-800 mb-2">📋 Instruções</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• O arquivo deve ter as colunas: <strong>nome</strong> e <strong>whatsapp</strong></li>
                <li>• A coluna <strong>nome</strong> é opcional (pode ficar vazia)</li>
                <li>• A coluna <strong>whatsapp</strong> é obrigatória</li>
                <li>• Formatos aceitos: CSV com vírgula ou ponto-e-vírgula</li>
                <li>• Compatível com Excel (Windows/Mac) e Google Sheets</li>
            </ul>
        </div>

        <!-- Download Template -->
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-700">Modelo de CSV</p>
                <p class="text-xs text-gray-500">Baixe o modelo para não errar o formato</p>
            </div>
            <a href="/contacts/template" class="btn-secondary text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Baixar Modelo
            </a>
        </div>

        <!-- Upload Form -->
        <form method="POST" action="/contacts/import" enctype="multipart/form-data">
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-brand-500 transition-colors cursor-pointer"
                onclick="document.getElementById('csvFile').click()">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p class="text-gray-600 font-medium">Clique para selecionar o arquivo CSV</p>
                <p class="text-sm text-gray-400 mt-1">ou arraste e solte aqui</p>
                <p id="fileName" class="text-sm text-brand-600 font-medium mt-3 hidden"></p>
                <input type="file" id="csvFile" name="csv_file" accept=".csv" class="hidden"
                    onchange="showFileName(this)">
            </div>

            <button type="submit" class="btn-primary w-full mt-6">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Importar Contatos
                </span>
            </button>
        </form>
    </div>
</div>

<script>
    function showFileName(input) {
        const name = input.files[0]?.name;
        const el = document.getElementById('fileName');
        if (name) {
            el.textContent = '📄 ' + name;
            el.classList.remove('hidden');
        }
    }
</script>

<?php $content = ob_get_clean();
include BASE_PATH . '/app/Views/layouts/main.php'; ?>