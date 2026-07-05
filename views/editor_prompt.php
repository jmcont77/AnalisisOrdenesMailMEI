<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editor de Prompt MEI — Oxipro</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; background: #f0f2f5; color: #222; overflow: hidden; }

#app {
  display: grid;
  grid-template-columns: 1fr 380px;
  height: 100vh;
  gap: 6px;
  padding: 6px;
}

/* ── Panel izquierdo — Editor ── */
#panel-editor {
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,.12);
  overflow: hidden;
}

.panel-header {
  padding: 12px 16px;
  background: #1a3a5c;
  color: #fff;
  font-weight: 600;
  font-size: 15px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}
.panel-header .info { font-size: 11px; font-weight: 400; color: #aac4e0; }
.panel-header a {
  font-size: 11px;
  font-weight: 400;
  color: #aac4e0;
  text-decoration: none;
}
.panel-header a:hover { color: #fff; }

.toolbar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  background: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
  flex-shrink: 0;
}
.toolbar label { font-size: 11px; color: #666; font-weight: 600; }
#clave-selector {
  padding: 4px 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 12px;
  font-family: inherit;
  min-width: 180px;
}
.btn {
  padding: 5px 14px;
  border: none;
  border-radius: 4px;
  font-size: 12px;
  font-family: inherit;
  cursor: pointer;
  font-weight: 600;
}
.btn-primary { background: #1a3a5c; color: #fff; }
.btn-primary:hover { background: #1f4a70; }
.btn-success { background: #27ae60; color: #fff; }
.btn-success:hover { background: #219a52; }
.btn-warning { background: #e67e22; color: #fff; }
.btn-warning:hover { background: #ca6f1e; }
.btn-sm { padding: 3px 10px; font-size: 11px; }

.editor-meta {
  padding: 8px 14px;
  background: #fffbf0;
  border-bottom: 1px solid #f0e0a0;
  font-size: 11px;
  color: #886600;
  flex-shrink: 0;
  display: none;
}
.editor-meta.visible { display: block; }

#editor-prompt {
  flex: 1;
  padding: 14px;
  font-family: 'Courier New', monospace;
  font-size: 12px;
  line-height: 1.6;
  border: none;
  resize: none;
  outline: none;
  color: #1a1a1a;
  background: #fff;
}

.editor-footer {
  padding: 8px 14px;
  background: #f8f9fa;
  border-top: 1px solid #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}
.editor-footer .chars { font-size: 11px; color: #888; }
.footer-btns { display: flex; gap: 8px; }

/* ── Panel derecho — Auditoría ── */
#panel-auditoria {
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,.12);
  overflow: hidden;
}

#lista-auditoria { flex: 1; overflow-y: auto; }

.audit-item {
  padding: 10px 14px;
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer;
  transition: background .15s;
}
.audit-item:hover { background: #f0f6ff; }
.audit-item.activo { background: #ddeeff; border-left: 3px solid #1a3a5c; }
.audit-fecha { font-size: 11px; font-weight: 600; color: #1a3a5c; }
.audit-clave { font-size: 11px; color: #27ae60; font-weight: 500; }
.audit-desc  { font-size: 10px; color: #888; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Modal diff */
#modal-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.5);
  z-index: 100;
  align-items: center;
  justify-content: center;
}
#modal-overlay.visible { display: flex; }
#modal {
  background: #fff;
  border-radius: 8px;
  width: 90vw;
  max-width: 1000px;
  height: 80vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0,0,0,.3);
}
#modal-header {
  padding: 12px 16px;
  background: #1a3a5c;
  color: #fff;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}
#modal-header button {
  background: none;
  border: 1px solid rgba(255,255,255,.3);
  color: #fff;
  padding: 3px 10px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 13px;
  margin-left: 6px;
}
#btn-restaurar { background: rgba(230,126,34,.7) !important; }
#btn-restaurar:hover { background: rgba(202,111,30,.9) !important; }

#modal-body {
  flex: 1;
  display: grid;
  grid-template-columns: 1fr 1fr;
  overflow: hidden;
}
.diff-panel { display: flex; flex-direction: column; overflow: hidden; border-right: 1px solid #e0e0e0; }
.diff-panel:last-child { border-right: none; }
.diff-title { padding: 6px 12px; background: #f8f9fa; font-size: 11px; font-weight: 700; color: #555; border-bottom: 1px solid #e0e0e0; flex-shrink: 0; }
.diff-content { flex: 1; overflow-y: auto; padding: 10px 12px; font-family: 'Courier New', monospace; font-size: 11px; line-height: 1.6; white-space: pre-wrap; word-break: break-word; color: #333; }
.diff-anterior { background: #fff8f8; }
.diff-nuevo    { background: #f8fff8; }

/* Toast */
#toast { position: fixed; bottom: 20px; right: 20px; background: #27ae60; color: #fff; padding: 10px 20px; border-radius: 6px; font-size: 13px; display: none; z-index: 200; box-shadow: 0 3px 10px rgba(0,0,0,.2); }
#toast.error { background: #e74c3c; }

/* Confirm */
#confirm-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 300; align-items: center; justify-content: center; }
#confirm-overlay.visible { display: flex; }
#confirm-box { background: #fff; border-radius: 8px; padding: 24px; max-width: 400px; width: 90%; box-shadow: 0 8px 32px rgba(0,0,0,.3); }
#confirm-box h3 { font-size: 15px; color: #1a3a5c; margin-bottom: 10px; }
#confirm-box p  { font-size: 13px; color: #555; margin-bottom: 20px; }
#confirm-box .btns { display: flex; gap: 8px; justify-content: flex-end; }

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #f5f5f5; }
::-webkit-scrollbar-thumb { background: #bbb; border-radius: 3px; }
</style>
</head>
<body>

<div id="app">

  <!-- ══ PANEL EDITOR ══ -->
  <div id="panel-editor">
    <div class="panel-header">
      <span>⚙️ Editor de Prompt MEI</span>
      <span>
        <span class="info" id="header-info">Cargando...</span>
        &nbsp;&nbsp;
        <a href="/">← Volver al visor</a>
      </span>
    </div>

    <div class="toolbar">
      <label>Clave:</label>
      <select id="clave-selector" onchange="cargarPrompt()">
        <option value="prompt_mei">prompt_mei</option>
      </select>
      <button class="btn btn-primary btn-sm" onclick="cargarPrompt()">🔄 Recargar</button>
    </div>

    <div class="editor-meta" id="editor-meta"></div>

    <textarea id="editor-prompt" placeholder="Cargando prompt..."></textarea>

    <div class="editor-footer">
      <span class="chars" id="char-count">0 caracteres</span>
      <div class="footer-btns">
        <button class="btn btn-warning" onclick="confirmarDescartar()">↩️ Descartar cambios</button>
        <button class="btn btn-success" onclick="confirmarGuardar()">💾 Guardar prompt</button>
      </div>
    </div>
  </div>

  <!-- ══ PANEL AUDITORÍA ══ -->
  <div id="panel-auditoria">
    <div class="panel-header" style="font-size:14px;">
      📋 Historial de cambios
    </div>
    <div id="lista-auditoria">
      <div style="color:#aaa;text-align:center;padding:30px;font-size:12px;">Cargando historial...</div>
    </div>
  </div>

</div>

<!-- Modal diff -->
<div id="modal-overlay" onclick="cerrarModal(event)">
  <div id="modal">
    <div id="modal-header">
      <span id="modal-titulo">Cambio del ...</span>
      <div>
        <button id="btn-restaurar" onclick="restaurarVersion()">↩️ Restaurar esta versión</button>
        <button onclick="cerrarModalBtn()">✕ Cerrar</button>
      </div>
    </div>
    <div id="modal-body">
      <div class="diff-panel">
        <div class="diff-title">⬅️ Versión anterior</div>
        <div class="diff-content diff-anterior" id="diff-anterior"></div>
      </div>
      <div class="diff-panel">
        <div class="diff-title">➡️ Versión nueva (guardada)</div>
        <div class="diff-content diff-nuevo" id="diff-nuevo"></div>
      </div>
    </div>
  </div>
</div>

<!-- Confirm -->
<div id="confirm-overlay">
  <div id="confirm-box">
    <h3 id="confirm-titulo">Confirmar</h3>
    <p id="confirm-msg">¿Estás seguro?</p>
    <div class="btns">
      <button class="btn" style="background:#e0e0e0;color:#333;" onclick="cerrarConfirm()">Cancelar</button>
      <button class="btn btn-success" id="confirm-ok">Confirmar</button>
    </div>
  </div>
</div>

<div id="toast"></div>

<script>
let promptOriginal  = '';
let auditoriaActual = null;

// ── Cargar prompt ─────────────────────────────────────────
async function cargarPrompt() {
  const clave = document.getElementById('clave-selector').value;
  const res   = await fetch(`/api/config/${clave}`);

  if (!res.ok) {
    mostrarToast('❌ No se encontró la clave: ' + clave, true);
    return;
  }

  const data = await res.json();
  promptOriginal = data.valor || '';

  document.getElementById('editor-prompt').value = promptOriginal;
  actualizarContador();

  const meta = document.getElementById('editor-meta');
  if (data.actualizado_en) {
    meta.textContent = `Última modificación: ${data.actualizado_en}`;
    meta.classList.add('visible');
  }

  document.getElementById('header-info').textContent = `Clave: ${clave}`;
  cargarAuditoria(clave);
}

// ── Contador ──────────────────────────────────────────────
document.getElementById('editor-prompt').addEventListener('input', actualizarContador);
function actualizarContador() {
  const n = document.getElementById('editor-prompt').value.length;
  document.getElementById('char-count').textContent = n.toLocaleString() + ' caracteres';
}

// ── Guardar ───────────────────────────────────────────────
function confirmarGuardar() {
  const nuevo = document.getElementById('editor-prompt').value.trim();
  if (!nuevo) { mostrarToast('❌ El prompt no puede estar vacío', true); return; }
  if (nuevo === promptOriginal) { mostrarToast('ℹ️ No hay cambios para guardar'); return; }
  mostrarConfirm(
    '💾 Guardar prompt',
    '¿Deseas guardar los cambios? Se registrará en el historial de auditoría.',
    '#27ae60',
    guardarPrompt
  );
}

async function guardarPrompt() {
  cerrarConfirm();
  const clave = document.getElementById('clave-selector').value;
  const nuevo = document.getElementById('editor-prompt').value;

  const res = await fetch(`/api/config/${clave}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ valor: nuevo, anterior: promptOriginal })
  });
  const r = await res.json();

  if (r.ok) {
    mostrarToast('✅ Prompt guardado correctamente');
    promptOriginal = nuevo;
    cargarAuditoria(clave);
    const meta = document.getElementById('editor-meta');
    meta.textContent = `Última modificación: ${new Date().toLocaleString('es-CO')}`;
    meta.classList.add('visible');
  } else {
    mostrarToast('❌ Error al guardar: ' + (r.error || ''), true);
  }
}

// ── Descartar ─────────────────────────────────────────────
function confirmarDescartar() {
  if (document.getElementById('editor-prompt').value === promptOriginal) {
    mostrarToast('ℹ️ No hay cambios para descartar');
    return;
  }
  mostrarConfirm(
    '↩️ Descartar cambios',
    '¿Deseas descartar todos los cambios no guardados?',
    '#e67e22',
    () => {
      cerrarConfirm();
      document.getElementById('editor-prompt').value = promptOriginal;
      actualizarContador();
    }
  );
}

// ── Auditoría ─────────────────────────────────────────────
async function cargarAuditoria(clave) {
  const res  = await fetch(`/api/config-auditoria/${clave}`);
  const data = await res.json();
  const el   = document.getElementById('lista-auditoria');

  if (!data.length) {
    el.innerHTML = '<div style="color:#aaa;text-align:center;padding:30px;font-size:12px;">Sin historial de cambios</div>';
    return;
  }

  el.innerHTML = data.map(a => `
    <div class="audit-item" data-id="${a.id}" onclick="verDiff(${a.id}, this)">
      <div class="audit-fecha">🕐 ${a.modificado_en}</div>
      <div class="audit-clave">${a.clave}</div>
      <div class="audit-desc">${escHtml(a.descripcion || 'Sin descripción')}</div>
    </div>`).join('');
}

// ── Ver diff ──────────────────────────────────────────────
async function verDiff(id, el) {
  document.querySelectorAll('.audit-item').forEach(e => e.classList.remove('activo'));
  el.classList.add('activo');

  const res       = await fetch(`/api/config-auditoria-item/${id}`);
  auditoriaActual = await res.json();

  document.getElementById('modal-titulo').textContent  = `Cambio del ${auditoriaActual.modificado_en}`;
  document.getElementById('diff-anterior').textContent = auditoriaActual.valor_anterior || '(vacío)';
  document.getElementById('diff-nuevo').textContent    = auditoriaActual.valor_nuevo    || '(vacío)';
  document.getElementById('modal-overlay').classList.add('visible');
}

// ── Restaurar ─────────────────────────────────────────────
function restaurarVersion() {
  if (!auditoriaActual) return;
  cerrarModalBtn();
  mostrarConfirm(
    '↩️ Restaurar versión',
    `¿Deseas restaurar el prompt a la versión del ${auditoriaActual.modificado_en}? El cambio se registrará en el historial.`,
    '#e67e22',
    async () => {
      cerrarConfirm();
      const clave = document.getElementById('clave-selector').value;
      const nuevo = auditoriaActual.valor_anterior;
      const res   = await fetch(`/api/config/${clave}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          valor: nuevo,
          anterior: promptOriginal,
          descripcion: `Restauración desde versión del ${auditoriaActual.modificado_en}`
        })
      });
      const r = await res.json();
      if (r.ok) {
        mostrarToast('✅ Versión restaurada correctamente');
        await cargarPrompt();
      } else {
        mostrarToast('❌ Error al restaurar', true);
      }
    }
  );
}

// ── Modal ─────────────────────────────────────────────────
function cerrarModal(e) {
  if (e.target === document.getElementById('modal-overlay')) cerrarModalBtn();
}
function cerrarModalBtn() {
  document.getElementById('modal-overlay').classList.remove('visible');
  auditoriaActual = null;
  document.querySelectorAll('.audit-item').forEach(e => e.classList.remove('activo'));
}

// ── Confirm ───────────────────────────────────────────────
function mostrarConfirm(titulo, msg, color, callback) {
  document.getElementById('confirm-titulo').textContent = titulo;
  document.getElementById('confirm-msg').textContent    = msg;
  const btn = document.getElementById('confirm-ok');
  btn.style.background = color;
  btn.onclick = callback;
  document.getElementById('confirm-overlay').classList.add('visible');
}
function cerrarConfirm() {
  document.getElementById('confirm-overlay').classList.remove('visible');
}

// ── Toast ─────────────────────────────────────────────────
function mostrarToast(msg, error=false) {
  const t = document.getElementById('toast');
  t.textContent   = msg;
  t.className     = error ? 'error' : '';
  t.style.display = 'block';
  setTimeout(() => t.style.display = 'none', 3000);
}

function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

cargarPrompt();
</script>
</body>
</html>
