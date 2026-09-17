<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log de Correos — Oxipro</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; background: #f0f2f5; color: #222; }

#app {
  display: flex;
  flex-direction: column;
  height: 100vh;
  gap: 6px;
  padding: 6px;
}

.panel {
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
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  flex-shrink: 0;
}
.panel-header a {
  font-size: 11px;
  font-weight: 400;
  color: #aac4e0;
  text-decoration: none;
}
.panel-header a:hover { color: #fff; }

/* Filtros */
#filtros {
  padding: 10px 16px;
  background: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}
#filtros label { font-size: 11px; color: #666; font-weight: 600; }
#filtros input, #filtros select {
  padding: 5px 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 12px;
  font-family: inherit;
}
#filtros input[type="date"] { min-width: 130px; }
#busqueda { min-width: 220px; }
#filtro-estado { min-width: 140px; }

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
.btn-sm { padding: 3px 10px; font-size: 11px; }

/* Resumen */
#resumen {
  padding: 6px 16px;
  background: #fffbf0;
  border-bottom: 1px solid #f0e0a0;
  font-size: 11px;
  color: #886600;
  display: none;
}
#resumen.visible { display: block; }

/* Tabla */
#tabla-container {
  flex: 1;
  overflow-y: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
}
thead th {
  position: sticky;
  top: 0;
  background: #1a3a5c;
  color: #fff;
  padding: 8px 12px;
  text-align: left;
  font-weight: 600;
  white-space: nowrap;
  z-index: 1;
}
tbody tr {
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer;
  transition: background .12s;
}
tbody tr:hover { background: #f0f6ff; }
tbody td {
  padding: 7px 12px;
  vertical-align: top;
}
td.asunto { max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
td.remitente { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #555; }
td.razon { max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #666; font-style: italic; }

.badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 700;
  white-space: nowrap;
}
.badge-mei     { background: #d4edda; color: #155724; }
.badge-no-mei  { background: #f8d7da; color: #721c24; }
.badge-pending { background: #fff3cd; color: #856404; }

.empty { text-align: center; padding: 40px; color: #aaa; font-size: 13px; }

/* Modal detalle */
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
  width: 700px;
  max-width: 95vw;
  max-height: 85vh;
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
}
#modal-body {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}
.detalle-fila {
  display: grid;
  grid-template-columns: 160px 1fr;
  gap: 6px 12px;
  margin-bottom: 8px;
  font-size: 12px;
}
.detalle-label { font-weight: 600; color: #555; }
.detalle-valor { color: #222; word-break: break-word; }
.detalle-razon {
  margin-top: 12px;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 6px;
  border-left: 3px solid #1a3a5c;
  font-size: 12px;
  line-height: 1.6;
  color: #333;
}

/* Footer */
#footer-tabla {
  padding: 6px 16px;
  background: #f8f9fa;
  border-top: 1px solid #e0e0e0;
  font-size: 11px;
  color: #888;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

#loading {
  text-align: center;
  padding: 40px;
  color: #888;
  font-size: 13px;
}

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #f5f5f5; }
::-webkit-scrollbar-thumb { background: #bbb; border-radius: 3px; }
</style>
</head>
<body>

<div id="app">

  <div class="panel" style="flex-shrink:0;">
    <div class="panel-header">
      <span>📬 Log de Correos Clasificados</span>
      <span>
        <a href="/prompt">⚙️ Editor de Prompt</a>
        &nbsp;&nbsp;
        <a href="/">← Volver al visor</a>
      </span>
    </div>

    <div id="filtros">
      <label>Desde:</label>
      <input type="date" id="fecha-desde" />

      <label>Hasta:</label>
      <input type="date" id="fecha-hasta" />

      <label>Estado:</label>
      <select id="filtro-estado">
        <option value="">Todos</option>
        <option value="MEI">MEI</option>
        <option value="NO_MEI">No MEI</option>
        <option value="PENDIENTE_CLASIFICACION">Pendiente</option>
      </select>

      <input type="text" id="busqueda" placeholder="🔍 Buscar en asunto..." oninput="buscarDebounce()" />

      <button class="btn btn-primary btn-sm" onclick="cargarDatos()">🔄 Filtrar</button>
      <button class="btn btn-sm" style="background:#e0e0e0;color:#333;" onclick="limpiarFiltros()">✕ Limpiar</button>
    </div>

    <div id="resumen"></div>
  </div>

  <div class="panel" style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
    <div id="tabla-container">
      <div id="loading">Cargando...</div>
      <table id="tabla" style="display:none;">
        <thead>
          <tr>
            <th style="width:140px;">Fecha recepción</th>
            <th style="width:80px;">Hora</th>
            <th>Asunto</th>
            <th>Remitente</th>
            <th style="width:90px;">Clasificación</th>
            <th>Razón</th>
          </tr>
        </thead>
        <tbody id="tabla-body"></tbody>
      </table>
    </div>
    <div id="footer-tabla">
      <span id="total-registros">—</span>
      <span id="ultimo-actualizado"></span>
    </div>
  </div>

</div>

<!-- Modal detalle -->
<div id="modal-overlay" onclick="cerrarModal(event)">
  <div id="modal">
    <div id="modal-header">
      <span>📧 Detalle del correo</span>
      <button onclick="cerrarModalBtn()">✕ Cerrar</button>
    </div>
    <div id="modal-body"></div>
  </div>
</div>

<script>
let todosLosDatos = [];
let debounceTimer = null;

// Fechas por defecto: últimos 7 días
function fechaDefault() {
  const hoy  = new Date();
  const hace7 = new Date();
  hace7.setDate(hoy.getDate() - 7);
  document.getElementById('fecha-desde').value = hace7.toISOString().slice(0, 10);
  document.getElementById('fecha-hasta').value = hoy.toISOString().slice(0, 10);
}

async function cargarDatos() {
  document.getElementById('loading').style.display = 'block';
  document.getElementById('tabla').style.display   = 'none';

  const desde  = document.getElementById('fecha-desde').value;
  const hasta  = document.getElementById('fecha-hasta').value;
  const estado = document.getElementById('filtro-estado').value;

  let url = '/api/log-correos?';
  if (desde)  url += `desde=${desde}&`;
  if (hasta)  url += `hasta=${hasta}&`;
  if (estado) url += `estado=${estado}&`;

  try {
    const res  = await fetch(url);
    todosLosDatos = await res.json();
    renderTabla(todosLosDatos);
  } catch (e) {
    document.getElementById('loading').textContent = '❌ Error al cargar datos';
  }
}

function renderTabla(datos) {
  const busqueda = document.getElementById('busqueda').value.toLowerCase().trim();
  const filtrados = busqueda
    ? datos.filter(r => (r.asunto || '').toLowerCase().includes(busqueda))
    : datos;

  const tbody = document.getElementById('tabla-body');
  document.getElementById('loading').style.display = 'none';

  if (!filtrados.length) {
    tbody.innerHTML = '<tr><td colspan="6" class="empty">Sin registros para los filtros seleccionados</td></tr>';
    document.getElementById('tabla').style.display = 'table';
    document.getElementById('total-registros').textContent = '0 registros';
    ocultarResumen();
    return;
  }

  tbody.innerHTML = filtrados.map(r => `
    <tr onclick="verDetalle(${r.id})">
      <td>${r.fecha_recepcion || r.fecha_registro_fmt || '—'}</td>
      <td>${r.hora_recepcion || '—'}</td>
      <td class="asunto" title="${escHtml(r.asunto || '')}">${escHtml(r.asunto || '—')}</td>
      <td class="remitente" title="${escHtml(r.remitente || '')}">${escHtml(limpiarRemitente(r.remitente || ''))}</td>
      <td>${badgeEstado(r.estado, r.es_mei)}</td>
      <td class="razon" title="${escHtml(r.razon_clasificacion || '')}">${escHtml(r.razon_clasificacion || '—')}</td>
    </tr>
  `).join('');

  document.getElementById('tabla').style.display = 'table';

  // Resumen
  const total  = filtrados.length;
  const mei    = filtrados.filter(r => r.es_mei === true  || r.estado === 'MEI').length;
  const noMei  = filtrados.filter(r => r.es_mei === false || r.estado === 'NO_MEI').length;
  const pend   = filtrados.filter(r => r.estado === 'PENDIENTE_CLASIFICACION').length;

  document.getElementById('total-registros').textContent = `${total} registro${total !== 1 ? 's' : ''}`;
  mostrarResumen(`Mostrando ${total} correos — ✅ MEI: ${mei} | ❌ No MEI: ${noMei} | ⏳ Pendiente: ${pend}`);
  document.getElementById('ultimo-actualizado').textContent = `Actualizado: ${new Date().toLocaleTimeString('es-CO')}`;
}

function badgeEstado(estado, esMei) {
  if (estado === 'MEI' || esMei === true)
    return '<span class="badge badge-mei">✅ MEI</span>';
  if (estado === 'NO_MEI' || esMei === false)
    return '<span class="badge badge-no-mei">❌ No MEI</span>';
  return '<span class="badge badge-pending">⏳ Pendiente</span>';
}

function limpiarRemitente(remitente) {
  // Extraer solo el nombre o email sin el formato completo
  const match = remitente.match(/^([^<]+)</);
  if (match) return match[1].trim().replace(/^"|"$/g, '');
  return remitente.replace(/<[^>]+>/g, '').trim();
}

function verDetalle(id) {
  const r = todosLosDatos.find(x => x.id === id);
  if (!r) return;

  document.getElementById('modal-body').innerHTML = `
    <div class="detalle-fila"><span class="detalle-label">Fecha recepción:</span><span class="detalle-valor">${r.fecha_recepcion || '—'} ${r.hora_recepcion || ''}</span></div>
    <div class="detalle-fila"><span class="detalle-label">Asunto:</span><span class="detalle-valor">${escHtml(r.asunto || '—')}</span></div>
    <div class="detalle-fila"><span class="detalle-label">Remitente:</span><span class="detalle-valor">${escHtml(r.remitente || '—')}</span></div>
    <div class="detalle-fila"><span class="detalle-label">UID Correo:</span><span class="detalle-valor">${escHtml(r.uid_correo || '—')}</span></div>
    <div class="detalle-fila"><span class="detalle-label">Message ID:</span><span class="detalle-valor" style="font-size:10px;color:#888;">${escHtml(r.message_id || '—')}</span></div>
    <div class="detalle-fila"><span class="detalle-label">Clasificación:</span><span class="detalle-valor">${badgeEstado(r.estado, r.es_mei)}</span></div>
    <div class="detalle-fila"><span class="detalle-label">Cantidad órdenes:</span><span class="detalle-valor">${r.cantidad_ordenes ?? '—'}</span></div>
    <div class="detalle-fila"><span class="detalle-label">Fecha registro:</span><span class="detalle-valor">${r.fecha_registro_fmt || '—'}</span></div>
    ${r.razon_clasificacion ? `
    <div style="margin-top:8px;font-size:11px;font-weight:600;color:#555;">Razón de clasificación:</div>
    <div class="detalle-razon">${escHtml(r.razon_clasificacion)}</div>` : ''}
  `;
  document.getElementById('modal-overlay').classList.add('visible');
}

function cerrarModal(e) {
  if (e.target === document.getElementById('modal-overlay')) cerrarModalBtn();
}
function cerrarModalBtn() {
  document.getElementById('modal-overlay').classList.remove('visible');
}

function limpiarFiltros() {
  fechaDefault();
  document.getElementById('filtro-estado').value = '';
  document.getElementById('busqueda').value = '';
  cargarDatos();
}

function buscarDebounce() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => renderTabla(todosLosDatos), 300);
}

function mostrarResumen(texto) {
  const el = document.getElementById('resumen');
  el.textContent = texto;
  el.classList.add('visible');
}
function ocultarResumen() {
  document.getElementById('resumen').classList.remove('visible');
}

function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

fechaDefault();
cargarDatos();
</script>
</body>
</html>
