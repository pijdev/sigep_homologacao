// ==UserScript==
// @name         iPEN - Coletor de Internos por Situação Penal, Relatório 2.1 do iPEN.
// @namespace    https://www.sc.gov.br/sap/ipen/
// @version      2.0
// @description  Consulta a lista de internos com situação penal em todas as unidades do estado, ou apenas a unidade selecionada.
// @author       Dev Team | SIGEP
// @match        https://www.sc.gov.br/ipen/RelatorioIpen_008SituacaoPenalImprimir.asp?*
// @grant        unsafeWindow
// @grant        GM_xmlhttpRequest
// @connect      sc.gov.br
// @run-at       document-start
// ==/UserScript==

(function() {
    'use strict';
    const originalPrint = unsafeWindow.print;
    // Bloqueia o auto-print
    unsafeWindow.print = () => false;
    // Todas as unidades cadastradas até 18/03/2026
    const UNIDADES_RAW = [{id: 8093, nome: "ARARANGUÁ - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 164, nome: "ARARANGUÁ - PRESÍDIO"}, {id: 166, nome: "BALNEÁRIO CAMBORIÚ - Presídio"}, {id: 8126, nome: "BARRA VELHA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 184, nome: "BARRA VELHA - PRESÍDIO"}, {id: 8119, nome: "BIGUAÇÚ - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 173, nome: "BIGUAÇU - PRESÍDIO"}, {id: 8094, nome: "BLUMENAU - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8169, nome: "BLUMENAU - CPMA"}, {id: 8091, nome: "BLUMENAU - PENITENCIÁRIA INDUSTRIAL PIB"}, {id: 168, nome: "BLUMENAU - PRESÍDIO"}, {id: 8128, nome: "BRUSQUE - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8067, nome: "BRUSQUE - PRESÍDIO"}, {id: 8129, nome: "CAÇADOR - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 160, nome: "CAÇADOR - PRESÍDIO"}, {id: 8130, nome: "CAMPOS NOVOS - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8069, nome: "CAMPOS NOVOS - PRESÍDIO"}, {id: 8124, nome: "CANOINHAS - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8070, nome: "CANOINHAS - PRESÍDIO"}, {id: 8064, nome: "CAPINZAL - UPA"}, {id: 11038, nome: "CENTRAL DE REGULAÇÃO DE VAGAS"}, {id: 171, nome: "CHAPECÓ - Casa Albergado"}, {id: 8170, nome: "CHAPECÓ - CPMA"}, {id: 150, nome: "CHAPECÓ - Penitenciária"}, {id: 8092, nome: "CHAPECÓ - PENITENCIÁRIA INDUSTRIAL PICH"}, {id: 163, nome: "CHAPECÓ - Presídio"}, {id: 8133, nome: "CHAPECÓ - PRESIDIO FEMININO - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8095, nome: "CHAPECÓ - PRESIDIO MASCULINO - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8139, nome: "CHAPECÓ - PRESÍDIO REGIONAL FEMININO"}, {id: 8096, nome: "CONCÓRDIA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 165, nome: "CONCÓRDIA - PRESÍDIO"}, {id: 8035, nome: "CORREIA PINTO - UPA"}, {id: 8097, nome: "CRICIÚMA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8171, nome: "CRICIÚMA - CPMA"}, {id: 8111, nome: "CRICIÚMA - PENITENCIÁRIA FEMININA"}, {id: 8065, nome: "CRICIÚMA - PENITENCIÁRIA SUL"}, {id: 157, nome: "CRICIÚMA - PRESÍDIO"}, {id: 8118, nome: "CURITIBANOS - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 151, nome: "CURITIBANOS - PENITENCIÁRIA"}, {id: 190, nome: "CURITIBANOS - UPA"}, {id: 8160, nome: "DIRETORIA DE SEGURANÇA E OPERAÇÕES"}, {id: 170, nome: "FLORIANÓPOLIS - CASA DO ALBERGADO"}, {id: 8114, nome: "FLORIANÓPOLIS - CENTRAL AUDIÊNCIA DE CUSTÓDIA CASA DO ALBERGADO"}, {id: 8090, nome: "FLORIANÓPOLIS - CENTRAL AUDIÊNCIA DE CUSTÓDIA PENITENCIÁRIA"}, {id: 8113, nome: "FLORIANÓPOLIS - CENTRAL AUDIÊNCIA DE CUSTÓDIA PRESÍDIO FEMININO"}, {id: 8112, nome: "FLORIANÓPOLIS - CENTRAL AUDIÊNCIA DE CUSTÓDIA PRESÍDIO MASCULINO"}, {id: 8162, nome: "FLORIANÓPOLIS - Central de Regime Aberto"}, {id: 8076, nome: "FLORIANÓPOLIS - Central de Triagem da Trindade"}, {id: 8066, nome: "FLORIANÓPOLIS - Central de Triagem do Estreito"}, {id: 8172, nome: "FLORIANÓPOLIS - CPMA"}, {id: 8161, nome: "FLORIANÓPOLIS - DPP"}, {id: 149, nome: "FLORIANÓPOLIS - HCTP"}, {id: 152, nome: "FLORIANÓPOLIS - PENITENCIÁRIA"}, {id: 169, nome: "FLORIANÓPOLIS - PRESÍDIO FEMININO"}, {id: 153, nome: "FLORIANÓPOLIS - PRESÍDIO MASCULINO"}, {id: 8106, nome: "FLORIANÓPOLIS - UME - UNIDADE DE MONITORAMENTO ELETRÔNICO"}, {id: 8121, nome: "IMBITUBA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 177, nome: "IMBITUBA - PRESÍDIO"}, {id: 8098, nome: "INDAIAL - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 181, nome: "INDAIAL - PRESÍDIO"}, {id: 8180, nome: "IPEN - ACESSO PARA OUTROS ÓRGÃOS"}, {id: 8099, nome: "ITAJAÍ - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8173, nome: "ITAJAÍ - CPMA"}, {id: 8078, nome: "ITAJAÍ - CPVI - PENITENCIÁRIA"}, {id: 8077, nome: "ITAJAÍ - CPVI - PRESÍDIO"}, {id: 155, nome: "ITAJAÍ - Presídio"}, {id: 8140, nome: "ITAJAÍ - PRESÍDIO FEMININO"}, {id: 8127, nome: "ITAPEMA  - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8071, nome: "ITAPEMA - PRESÍDIO"}, {id: 8144, nome: "ITUPORANGA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8143, nome: "ITUPORANGA - PRESÍDIO FEMININO"}, {id: 182, nome: "ITUPORANGA - UPA"}, {id: 8100, nome: "JARAGUÁ DO SUL -  CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8174, nome: "JARAGUÁ DO SUL - CPMA"}, {id: 172, nome: "JARAGUÁ DO SUL - PRESÍDIO"}, {id: 8134, nome: "JOAÇABA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 162, nome: "JOAÇABA - PRESÍDIO REGIONAL"}, {id: 8101, nome: "JOINVILLE - CENTRAL AUDIÊNCIA DE CUSTÓDIA - PENITENCIÁRIA"}, {id: 8123, nome: "JOINVILLE - CENTRAL AUDIÊNCIA DE CUSTÓDIA - PRESÍDIOS"}, {id: 8175, nome: "JOINVILLE - CPMA"}, {id: 8019, nome: "JOINVILLE - PENITENCIÁRIA INDUSTRIAL"}, {id: 154, nome: "JOINVILLE - PRESÍDIO"}, {id: 8156, nome: "JOINVILLE - PRESIDIO FEMININO"}, {id: 8102, nome: "LAGES - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8176, nome: "LAGES - CPMA"}, {id: 8082, nome: "LAGES - PRESIDIO MASCULINO"}, {id: 159, nome: "LAGES - PRESÍDIO REGIONAL"}, {id: 8122, nome: "LAGUNA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8177, nome: "LAGUNA - CPMA"}, {id: 176, nome: "LAGUNA - PRESÍDIO"}, {id: 8103, nome: "MAFRA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 161, nome: "MAFRA - PRESÍDIO"}, {id: 8136, nome: "MARAVILHA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8088, nome: "MARAVILHA - PRESÍDIO"}, {id: 8163, nome: "NOT - SR01 - SUPERINTENDÊNCIA REGIONAL DA GRANDE FLORIANÓPOLIS"}, {id: 11039, nome: "NOT - SR01 SÃO PEDRO DE ALCANTARA"}, {id: 8164, nome: "NOT - SR02 - Superintendência da Região Sul"}, {id: 8165, nome: "NOT - SR03 - Superintendência da Região do Norte"}, {id: 8157, nome: "NOT - SR04 - SUPERINTENDÊNCIA REGIONAL DO VALE DO ITAJAÍ"}, {id: 8166, nome: "NOT - SR05 - Superintendência da Região Serrana"}, {id: 8167, nome: "NOT - SR07 - Superintendência da Região do Médio Vale do Itajaí"}, {id: 8168, nome: "NOT - SR08 - SUPERINTENDÊNCIA DA REGIÃO DO PLANALTO NORTE"}, {id: 11035, nome: "NÚCLEO DE BUSCA E RECAPTURA"}, {id: 8059, nome: "PALHOÇA - CAPH COLÔNIA AGRÍCOLA"}, {id: 8117, nome: "PALHOÇA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8178, nome: "PALHOÇA - CPMA"}, {id: 8040, nome: "PIÇARRAS - UPA"}, {id: 8131, nome: "PORTO UNIÃO - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8001, nome: "PORTO UNIÃO - PRESÍDIO"}, {id: 8132, nome: "RIO DO SUL - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 158, nome: "RIO DO SUL - PRESÍDIO"}, {id: 11036, nome: "SÃO BENTO DO SUL - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 11034, nome: "SÃO BENTO DO SUL - PENITENCIÁRIA INDUSTRIAL"}, {id: 8089, nome: "SÃO CRISTÓVÃO DO SUL - PENITENCIÁRIA INDUSTRIAL"}, {id: 8125, nome: "SÃO FRANCISCO DO SUL - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8075, nome: "SÃO FRANCISCO DO SUL - PRESÍDIO"}, {id: 188, nome: "SÃO JOAQUIM - UPA"}, {id: 8116, nome: "SÃO JOSÉ - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8179, nome: "SÃO JOSÉ - CPMA"}, {id: 8159, nome: "SÃO JOSÉ - Grupo de Operações Aéreas - GOA/DPP"}, {id: 8138, nome: "SÃO JOSÉ DO CEDRO - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8087, nome: "SÃO JOSÉ DO CEDRO - PRESÍDIO"}, {id: 8137, nome: "SÃO MIGUEL DO OESTE - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 183, nome: "SÃO MIGUEL DO OESTE - PRESÍDIO"}, {id: 175, nome: "SÃO PEDRO DE ALCÂNTARA - PENITENCIÁRIA"}, {id: 11037, nome: "TESTE I-PEN"}, {id: 8104, nome: "TIJUCAS - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 174, nome: "TIJUCAS - PRESÍDIO"}, {id: 8120, nome: "TUBARÃO - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8146, nome: "TUBARÃO - PENITENCIÁRIA MASCULINA"}, {id: 156, nome: "TUBARÃO - Presídio"}, {id: 8080, nome: "TUBARÃO - PRESÍDIO MASCULINO"}, {id: 8081, nome: "TUBARÃO - Presídio Regional Feminino"}, {id: 8105, nome: "VIDEIRA - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 8068, nome: "VIDEIRA - PRESÍDIO"}, {id: 8135, nome: "XANXERÊ - CENTRAL AUDIÊNCIA DE CUSTÓDIA"}, {id: 167, nome: "XANXERÊ - PRESÍDIO"}];

    let state = {
        isRunning: false,
        isPaused: false,
        currentIndex: 0,
        data: [],
        selectedIds: JSON.parse(localStorage.getItem('ipen_selected_units') || '[]')
    };

    // 2. ESTILIZAÇÃO (MODERN UI COM BOTÃO FECHAR)
    const style = document.createElement('style');
    style.innerHTML = `
        #ipen-app { --zinc-950: #09090b; --zinc-200: #e4e4e7; font-family: 'Segoe UI', Tahoma, sans-serif; position: fixed; top: 15px; left: 15px; z-index: 2147483647; }
        .card { background: #fff; border: 1px solid var(--zinc-200); border-radius: 12px; width: 380px; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); overflow: hidden; }
        .header { background: var(--zinc-950); color: #fff; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; }
        .header-title { display: flex; flex-direction: column; }
        .btn-close { background: none; border: none; color: #a1a1aa; font-size: 20px; cursor: pointer; padding: 0 5px; transition: color 0.2s; }
        .btn-close:hover { color: #ef4444; }
        .content { padding: 16px; }
        .grid-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; }
        .stat-box { border: 1px solid var(--zinc-200); padding: 12px; border-radius: 8px; text-align: center; background: #fafafa; }
        .stat-val { font-size: 20px; font-weight: 700; display: block; color: #18181b; }
        .stat-lab { font-size: 10px; color: #71717a; text-transform: uppercase; font-weight: 600; }
        .progress-bg { background: var(--zinc-200); height: 6px; border-radius: 3px; margin-bottom: 12px; }
        .progress-bar { background: #2563eb; height: 100%; width: 0%; transition: width 0.3s ease; border-radius: 3px; }
        .controls { display: flex; gap: 6px; margin-bottom: 12px; }
        .btn { flex: 1; border: none; padding: 10px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 12px; transition: all 0.2s; }
        .btn-play { background: #2563eb; color: #fff; }
        .btn-play:hover { background: #1d4ed8; }
        .btn-pause { background: #f59e0b; color: #fff; display: none; }
        .btn-stop { background: #ef4444; color: #fff; }
        .btn-dl { background: #10b981; color: #fff; width: 100%; display: none; margin-top: 8px; }
        .selector-toggle { font-size: 11px; color: #2563eb; cursor: pointer; text-decoration: underline; margin-bottom: 10px; display: block; font-weight: 500; }
        .unit-list { border: 1px solid var(--zinc-200); max-height: 180px; overflow-y: auto; border-radius: 6px; display: none; padding: 8px; margin-bottom: 12px; background: #fdfdfd; }
        .unit-item { font-size: 11px; display: flex; align-items: center; gap: 8px; padding: 5px 0; border-bottom: 1px solid #f4f4f5; }
        .log { font-size: 10px; color: #71717a; background: #f8fafc; padding: 8px; border-radius: 6px; height: 32px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; border: 1px solid #e4e4e7; }
    `;
    document.head.appendChild(style);

    function createUI() {
        if (document.getElementById('ipen-app')) return;
        const app = document.createElement('div');
        app.id = 'ipen-app';
        app.innerHTML = `
            <div class="card">
                <div class="header">
                    <div class="header-title">
                        <span style="font-weight:700; font-size:13px; letter-spacing: 0.5px;">iPEN Analytics Pro</span>
                        <span style="font-size:9px; opacity:0.6; font-weight:400;">ESTADO DE SANTA CATARINA</span>
                    </div>
                    <button id="btn-close-ui" class="btn-close" title="Fechar ferramenta e ver relatório normal">&times;</button>
                </div>
                <div class="content">
                    <div class="grid-stats">
                        <div class="stat-box"><span id="val-units" class="stat-val">0</span><span class="stat-lab">Unidades</span></div>
                        <div class="stat-box"><span id="val-inmates" class="stat-val">0</span><span class="stat-lab">Detentos</span></div>
                    </div>

                    <span class="selector-toggle" id="toggle-list">⚙️ Configurar Filtro de Unidades</span>
                    <div class="unit-list" id="unit-container">
                        <div class="unit-item"><input type="checkbox" id="check-all" checked> <b>SELECIONAR TODAS AS UNIDADES</b></div>
                        <hr style="border:0; border-top:1px solid #eee; margin: 5px 0;">
                        ${UNIDADES_RAW.map(u => `
                            <div class="unit-item">
                                <input type="checkbox" class="unid-check" value="${u.id}" ${state.selectedIds.length === 0 || state.selectedIds.includes(u.id.toString()) ? 'checked' : ''}>
                                <span>${u.nome}</span>
                            </div>
                        `).join('')}
                    </div>

                    <div class="progress-bg"><div id="main-bar" class="progress-bar"></div></div>

                    <div class="controls">
                        <button id="btn-play" class="btn btn-play">INICIAR COLETA</button>
                        <button id="btn-pause" class="btn btn-pause">PAUSAR</button>
                        <button id="btn-stop" class="btn btn-stop">RESETAR</button>
                    </div>

                    <div id="main-log" class="log">Sistema pronto para análise estadual...</div>
                    <button id="btn-dl" class="btn btn-dl">BAIXAR RELATÓRIO CONSOLIDADO</button>
                </div>
            </div>
        `;
        document.body.appendChild(app);
        attachEvents();
    }

    // Lógica:
    function attachEvents() {
        document.getElementById('btn-close-ui').onclick = () => {
            if (state.isRunning && !confirm("A coleta está em andamento. Deseja realmente fechar e parar tudo?")) return;
            state.isRunning = false;
            document.getElementById('ipen-app').remove();
            unsafeWindow.print = originalPrint;
        };

        document.getElementById('toggle-list').onclick = () => {
            const list = document.getElementById('unit-container');
            list.style.display = list.style.display === 'block' ? 'none' : 'block';
        };

        document.getElementById('check-all').onclick = (e) => {
            document.querySelectorAll('.unid-check').forEach(c => c.checked = e.target.checked);
            updateSelected();
        };

        document.querySelectorAll('.unid-check').forEach(c => c.onclick = updateSelected);

        document.getElementById('btn-play').onclick = startProcessing;
        document.getElementById('btn-pause').onclick = togglePause;
        document.getElementById('btn-stop').onclick = resetProcess;
        document.getElementById('btn-dl').onclick = downloadCSV;
    }

    function updateSelected() {
        state.selectedIds = Array.from(document.querySelectorAll('.unid-check:checked')).map(c => c.value);
        localStorage.setItem('ipen_selected_units', JSON.stringify(state.selectedIds));
    }

    async function startProcessing() {
        if (state.isRunning && !state.isPaused) return;

        updateSelected();
        const selectedUnits = UNIDADES_RAW.filter(u => state.selectedIds.includes(u.id.toString()));

        if (selectedUnits.length === 0) return alert("Selecione pelo menos uma unidade para coletar.");

        state.isRunning = true;
        state.isPaused = false;

        document.getElementById('btn-play').style.display = 'none';
        document.getElementById('btn-pause').style.display = 'block';

        const log = document.getElementById('main-log');
        const bar = document.getElementById('main-bar');
        const unitLabel = document.getElementById('val-units');
        const inmateLabel = document.getElementById('val-inmates');

        for (let i = state.currentIndex; i < selectedUnits.length; i++) {
            if (!state.isRunning) break;
            while (state.isPaused) await new Promise(r => setTimeout(r, 500));

            const u = selectedUnits[i];
            log.innerText = `Varrendo: ${u.nome}`;
            unitLabel.innerText = `${i + 1} / ${selectedUnits.length}`;
            bar.style.width = `${((i + 1) / selectedUnits.length) * 100}%`;

            const results = await fetchUnitData(u);
            state.data = state.data.concat(results);
            inmateLabel.innerText = state.data.length;

            state.currentIndex = i + 1;
            await new Promise(r => setTimeout(r, 400));
        }

        if (state.currentIndex >= selectedUnits.length) {
            log.innerText = "Coleta finalizada com sucesso!";
            document.getElementById('btn-dl').style.display = 'block';
            document.getElementById('btn-pause').style.display = 'none';
            document.getElementById('btn-play').style.display = 'block';
            document.getElementById('btn-play').innerText = 'REINICIAR';
            state.isRunning = false;
            state.currentIndex = 0;
        }
    }

    function togglePause() {
        state.isPaused = !state.isPaused;
        const btn = document.getElementById('btn-pause');
        btn.innerText = state.isPaused ? 'CONTINUAR' : 'PAUSAR';
        btn.style.background = state.isPaused ? '#2563eb' : '#f59e0b';
    }

    function resetProcess() {
        if (confirm("Isso apagará os dados coletados nesta sessão. Continuar?")) {
            location.reload();
        }
    }

    async function fetchUnitData(unidade) {
        return new Promise((resolve) => {
            GM_xmlhttpRequest({
                method: "GET",
                url: `https://www.sc.gov.br/ipen/RelatorioIpen_008SituacaoPenalImprimir.asp?cd_Unidade=${unidade.id}&cd_albergado=0&cd_situacaoPenal=0`,
                overrideMimeType: "text/html; charset=iso-8859-1",
                timeout: 60000,
                onload: (res) => {
                    const doc = new DOMParser().parseFromString(res.responseText, "text/html");
                    const rows = doc.querySelectorAll('table tr');
                    const mapa = new Map();
                    // Regex: garante Prontuário | Nome | Situação
                    const regex = /^(\d{4,8})\s+([A-ZÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ\s.']{5,})$/i;

                    rows.forEach(tr => {
                        const tds = tr.querySelectorAll('td');
                        if (tds.length >= 2) {
                            let match = tds[0].innerText.trim().match(regex);
                            if (match) {
                                const pront = match[1];
                                if (!mapa.has(pront)) {
                                    mapa.set(pront, {
                                        id_u: unidade.id,
                                        unidade: unidade.nome,
                                        pront: pront,
                                        nome: match[2].replace(/\s+/g, " ").trim(),
                                        situ: (tds[1] ? tds[1].innerText.trim() : "").replace(/\s+/g, " ")
                                    });
                                }
                            }
                        }
                    });
                    resolve(Array.from(mapa.values()));
                },
                onerror: () => resolve([]),
                timeout: () => resolve([])
            });
        });
    }

    function downloadCSV() {
        let csv = "\uFEFFID_UNIDADE;UNIDADE;PRONTUARIO;NOME;SITUACAO_PENAL\n";
        state.data.forEach(r => csv += `${r.id_u};"${r.unidade}";${r.pront};"${r.nome}";"${r.situ}"\n`);
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        const dataStr = new Date().toISOString().slice(0,10);
        link.download = `BASE_IPEN_SC_${dataStr}.csv`;
        link.click();
    }

    const checkReady = setInterval(() => {
        if (document.body) { createUI(); clearInterval(checkReady); }
    }, 400);

})();