<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Visor MEI — Oxipro</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; background: #f0f2f5; color: #222; overflow: hidden; }

#app {
  display: grid;
  grid-template-columns: 300px 1fr;
  grid-template-rows: 60% 40%;
  height: 100vh;
  gap: 6px;
  padding: 6px;
}

/* ZONA 1 */
#z1 {
  grid-row: 1 / 3;
  grid-column: 1;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.zona-header {
  padding: 10px 14px;
  background: #1a3a5c;
  color: #fff;
  font-weight: 600;
  font-size: 14px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.search-bar { padding: 8px 10px; border-bottom: 1px solid #e8e8e8; flex-shrink: 0; }
.search-bar input { width: 100%; padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; }
.filter-bar { padding: 8px 10px; border-bottom: 1px solid #e8e8e8; flex-shrink: 0; background: #f8f9fa; display: flex; flex-direction: column; gap: 6px; }
.filter-row { display: flex; align-items: center; gap: 6px; }
.filter-row label { font-size: 10px; font-weight: 600; color: #666; white-space: nowrap; min-width: 36px; }
.filter-row select { flex: 1; padding: 4px 6px; border: 1px solid #ccc; border-radius: 4px; font-size: 11px; font-family: inherit; background: #fff; }
.filter-row input[type="date"] { flex: 1; padding: 4px 6px; border: 1px solid #ccc; border-radius: 4px; font-size: 11px; font-family: inherit; background: #fff; }
.filter-count { font-size: 10px; color: #888; text-align: right; padding: 0 2px; }
.btn-limpiar { padding: 3px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 10px; background: #fff; cursor: pointer; color: #666; white-space: nowrap; }
.btn-limpiar:hover { background: #f0f0f0; }
#lista-ordenes { flex: 1; overflow-y: auto; }
.orden-item { padding: 10px 14px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background .15s; }
.orden-item:hover { background: #f0f6ff; }
.orden-item.activo { background: #ddeeff; border-left: 3px solid #1a3a5c; }
.oi-paciente { font-weight: 600; color: #1a3a5c; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.oi-meta { font-size: 11px; color: #666; margin-top: 2px; }
.oi-eps { font-size: 11px; color: #2a7a4f; font-weight: 500; }
.oi-fecha { font-size: 10px; color: #999; margin-top: 2px; }
.badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: 600; }
.badge-oxigeno   { background: #dff0ff; color: #0066cc; }
.badge-sahos     { background: #fff0dd; color: #cc6600; }
.badge-no        { background: #ffe0e0; color: #cc0000; }
.badge-gestionada{ background: #d4edda; color: #155724; }

/* ZONA 2+5 combinada */
#z25-wrapper {
  grid-row: 1;
  grid-column: 2;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.z25-headers { display: flex; flex-shrink: 0; }
.z25-header-col {
  flex: 1;
  padding: 10px 14px;
  background: #1a3a5c;
  color: #fff;
  font-weight: 600;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.z25-header-col:first-child { border-right: 1px solid #2a5a8c; }

.btn-accion {
  border: none;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 11px;
  cursor: pointer;
  display: none;
  margin-left: 4px;
}
.btn-accion.visible { display: inline-block; }
.btn-guardar   { background: #27ae60; color: #fff; }
.btn-guardar:hover { background: #219a52; }
.btn-gestionar { background: #e67e22; color: #fff; }
.btn-gestionar:hover { background: #ca6f1e; }
.btn-gestionada-disabled { background: #7f8c8d; color: #fff; cursor: default; }

#scroll-z25 { flex: 1; overflow-y: scroll; overflow-x: hidden; }

/* Tabla de campos alineados — 3 columnas */
.campos-tabla { width: 100%; border-collapse: collapse; table-layout: fixed; }
.campos-tabla tr { border-bottom: 1px solid #f0f0f0; }
.campos-tabla tr:hover { background: #fafafa; }
.campos-tabla td { padding: 5px 8px; vertical-align: middle; overflow: hidden; }
.col-label  { width: 18%; font-weight: 600; color: #555; font-size: 11px; white-space: nowrap; text-overflow: ellipsis; }
.col-detail { width: 41%; font-size: 12px; word-break: break-word; color: #222; border-right: 2px solid #e8f0fa; }
.col-edit   { width: 41%; font-size: 12px; }
.col-edit input,
.col-edit textarea,
.col-edit select {
  width: 100%; border: 1px solid #ddd; border-radius: 3px;
  padding: 3px 6px; font-size: 12px; font-family: inherit; background: #fafeff;
}
.col-edit textarea { resize: vertical; min-height: 46px; }
.col-edit input:focus, .col-edit textarea:focus { outline: none; border-color: #1a3a5c; }
.seccion-titulo { background: #e8f0fa; color: #1a3a5c; font-weight: 700; font-size: 11px; padding: 4px 8px; text-transform: uppercase; letter-spacing: .5px; }
.empty-msg { color: #aaa; text-align: center; padding: 40px 20px; font-size: 13px; }

/* ZONA 3 + 4 */
#z3 {
  grid-row: 2;
  grid-column: 2;
  display: grid;
  grid-template-columns: 220px 1fr;
  gap: 6px;
}
#z3-adjuntos {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
#lista-adjuntos { flex: 1; overflow-y: auto; padding: 8px; display: flex; flex-direction: column; gap: 6px; }
.adjunto-item { display: flex; align-items: center; gap: 8px; padding: 6px 8px; border: 1px solid #e8e8e8; border-radius: 6px; cursor: pointer; transition: background .15s; }
.adjunto-item:hover { background: #f0f6ff; }
.adjunto-item.activo { background: #ddeeff; border-color: #1a3a5c; }
.adjunto-icon { width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.icon-pdf  { background: #ffe0e0; }
.icon-xlsx { background: #e0ffe0; }
.icon-txt  { background: #e8e8e8; }
.icon-img  { background: #fff0dd; }
.icon-otro { background: #f0e8ff; }
.adjunto-info { min-width: 0; }
.adjunto-nombre { font-size: 11px; font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
.adjunto-meta { font-size: 10px; color: #888; }

/* ZONA 4 */
#z4 {
  background: #1a1a2e;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
#visor-toolbar { display: flex; align-items: center; gap: 8px; padding: 6px 12px; background: #0f0f1a; flex-shrink: 0; }
#visor-toolbar button { background: #2a2a4a; color: #fff; border: 1px solid #444; padding: 3px 10px; border-radius: 4px; cursor: pointer; font-size: 16px; }
#visor-toolbar button:hover { background: #3a3a6a; }
#visor-toolbar span { color: #aaa; font-size: 12px; }
#visor-contenido { flex: 1; overflow: auto; display: flex; align-items: center; justify-content: center; padding: 10px; }
#visor-contenido iframe { width: 100%; height: 100%; border: none; background: #fff; border-radius: 4px; }
#visor-contenido img { max-width: 100%; transition: transform .2s; transform-origin: center center; border-radius: 4px; }
.visor-vacio { color: #555; font-size: 14px; text-align: center; }
.visor-vacio .icon { font-size: 48px; margin-bottom: 10px; }
#visor-txt {
  width: 100%; height: 100%; margin: 0; padding: 16px; background: #fff; color: #222;
  font-family: 'Consolas', 'Courier New', monospace; font-size: 12px; white-space: pre-wrap;
  word-break: break-word; overflow: auto; border-radius: 4px; text-align: left;
}
#visor-excel-wrap { width: 100%; height: 100%; overflow: auto; background: #fff; border-radius: 4px; padding: 10px; }
#visor-excel-wrap table { border-collapse: collapse; font-size: 11px; }
#visor-excel-wrap table td, #visor-excel-wrap table th { border: 1px solid #ddd; padding: 4px 8px; white-space: nowrap; }
#visor-excel-wrap table tr:first-child td { background: #e8f0fa; font-weight: 600; }

/* Toast */
#toast { position: fixed; bottom: 20px; right: 20px; background: #27ae60; color: #fff; padding: 10px 20px; border-radius: 6px; font-size: 13px; display: none; z-index: 999; box-shadow: 0 3px 10px rgba(0,0,0,.2); }
#toast.error { background: #e74c3c; }

::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: #f5f5f5; }
::-webkit-scrollbar-thumb { background: #bbb; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #999; }
</style>
</head>
<body>
<div id="app">

  <!-- ZONA 1 -->
  <div id="z1">
    <div class="zona-header">📋 Órdenes MEI <span id="refresh-countdown" style="font-size:10px; font-weight:400; opacity:.7;">⟳ 60s</span></div>

    <!-- Filtros -->
    <div class="filter-bar">
      <div class="filter-row">
        <label>Estado</label>
        <select id="filtro-estado" onchange="aplicarFiltros()">
          <option value="">Todos</option>
          <option value="gestionada">✅ Gestionadas</option>
          <option value="pendiente">⏳ Pendientes</option>
          <option value="mei">📋 Es MEI</option>
          <option value="no-mei">❌ No MEI</option>
        </select>
        <button class="btn-limpiar" onclick="limpiarFiltros()">✕ Limpiar</button>
      </div>
      <div class="filter-row">
        <label>Desde</label>
        <input type="date" id="filtro-desde" onchange="aplicarFiltros()">
      </div>
      <div class="filter-row">
        <label>Hasta</label>
        <input type="date" id="filtro-hasta" onchange="aplicarFiltros()">
      </div>
      <div class="filter-count" id="filtro-count"></div>
    </div>

    <div class="search-bar">
      <input type="text" id="buscador" placeholder="Buscar paciente, EPS, municipio..." oninput="aplicarFiltros()">
    </div>
    <div id="lista-ordenes">
      <div class="empty-msg">Cargando registros...</div>
    </div>
  </div>

  <!-- ZONA 2+5 combinada -->
  <div id="z25-wrapper">
    <div class="z25-headers">
      <div class="z25-header-col">🔍 Detalle del registro</div>
      <div class="z25-header-col">
        ✏️ Edición
        <div>
          <button id="btn-gestionar" class="btn-accion btn-gestionar" onclick="marcarGestionada()">✅ Marcar gestionada</button>
          <button id="btn-guardar"   class="btn-accion btn-guardar"   onclick="guardarCambios()">💾 Guardar</button>
        </div>
      </div>
    </div>
    <div id="scroll-z25">
      <div class="empty-msg">← Selecciona un registro</div>
    </div>
  </div>

  <!-- ZONA 3 + 4 -->
  <div id="z3">
    <div id="z3-adjuntos">
      <div class="zona-header" style="font-size:13px;">📎 Adjuntos</div>
      <div id="lista-adjuntos">
        <div class="empty-msg" style="padding:20px; font-size:12px;">Sin adjuntos</div>
      </div>
    </div>
    <div id="z4">
      <div class="zona-header" style="background:#0f0f1a; font-size:13px;">🖼️ Visualizador</div>
      <div id="visor-toolbar">
        <button onclick="zoom(-0.2)">−</button>
        <button onclick="zoom(0.2)">+</button>
        <button onclick="resetZoom()">⟳</button>
        <span id="zoom-nivel">100%</span>
      </div>
      <div id="visor-contenido">
        <div class="visor-vacio">
          <div class="icon">📄</div>
          <div>Selecciona un adjunto</div>
        </div>
      </div>
    </div>
  </div>

</div>
<div id="toast"></div>

<script>
let ordenActual    = null;
let zoomActual     = 1.0;
let todosRegistros = [];

const SECCIONES = [
  { titulo: 'DATOS DEL CORREO', campos: [
    { key: 'fecha_procesamiento', label: 'Fecha proceso',   tipo: 'text',     editable: false },
    { key: 'fecha_hora_correo',   label: 'Fecha correo',    tipo: 'text' },
    { key: 'asunto_correo',       label: 'Asunto',          tipo: 'text' },
    { key: 'remitente',           label: 'Remitente',       tipo: 'text' },
    { key: 'uid_correo',          label: 'UID correo',      tipo: 'text',     editable: false },
    { key: 'message_id',          label: 'Message-ID',      tipo: 'text',     editable: false },
  ]},
  { titulo: 'CLASIFICACIÓN', campos: [
    { key: 'gestionada',          label: 'Gestionada',      tipo: 'bool',     editable: false },
    { key: 'es_orden_mei',        label: 'Es orden MEI',    tipo: 'bool',     editable: false },
    { key: 'linea_servicio',      label: 'Línea servicio',  tipo: 'text' },
    { key: 'proveedor',           label: 'Proveedor',       tipo: 'text' },
    { key: 'eps',                 label: 'EPS',             tipo: 'text' },
    { key: 'entidades',           label: 'Entidad/Sucursal',tipo: 'text' },
    { key: 'asignado_a',          label: 'Asignado a',      tipo: 'text' },
  ]},
  { titulo: 'DATOS DEL PACIENTE', campos: [
    { key: 'nombre_paciente',         label: 'Nombre completo',  tipo: 'text' },
    { key: 'primer_nombre',           label: 'Primer nombre',    tipo: 'text' },
    { key: 'segundo_nombre',          label: 'Segundo nombre',   tipo: 'text' },
    { key: 'primer_apellido',         label: 'Primer apellido',  tipo: 'text' },
    { key: 'segundo_apellido',        label: 'Segundo apellido', tipo: 'text' },
    { key: 'tipo_documento_paciente', label: 'Tipo documento',   tipo: 'text' },
    { key: 'documento_paciente',      label: 'Documento',        tipo: 'text' },
    { key: 'fecha_nacimiento',        label: 'Fecha nacimiento', tipo: 'text' },
    { key: 'edad',                    label: 'Edad',             tipo: 'text' },
    { key: 'rango_edad',              label: 'Rango edad',       tipo: 'text' },
    { key: 'genero',                  label: 'Género',           tipo: 'text' },
    { key: 'regimen',                 label: 'Régimen',          tipo: 'text' },
    { key: 'categoria',               label: 'Categoría IBC',    tipo: 'text' },
    { key: 'nivel_ibc',               label: 'Nivel IBC',        tipo: 'text' },
    { key: 'tipo_afiliado',           label: 'Tipo afiliado',    tipo: 'text' },
  ]},
  { titulo: 'DATOS DE CONTACTO', campos: [
    { key: 'departamento',       label: 'Departamento', tipo: 'text' },
    { key: 'municipio',          label: 'Municipio',    tipo: 'text' },
    { key: 'direccion',          label: 'Dirección',    tipo: 'textarea' },
    { key: 'telefonos',          label: 'Teléfonos',    tipo: 'text' },
    { key: 'correo_electronico', label: 'Correo',       tipo: 'text' },
  ]},
  { titulo: 'DATOS MÉDICOS', campos: [
    { key: 'diagnostico',              label: 'Diagnóstico',        tipo: 'text' },
    { key: 'ips_formula',              label: 'IPS fórmula',        tipo: 'text' },
    { key: 'tipo_documento_medico',    label: 'Tipo doc. médico',   tipo: 'text' },
    { key: 'documento_medico',         label: 'Documento médico',   tipo: 'text' },
    { key: 'nombre_medico_formulador', label: 'Médico formulador',  tipo: 'text' },
    { key: 'fecha_formulacion',        label: 'Fecha formulación',  tipo: 'text' },
    { key: 'salida_hospitalaria',      label: 'Salida hospitalaria',tipo: 'text' },
  ]},
  { titulo: 'AUTORIZACIÓN', campos: [
    { key: 'tipo_autorizacion',    label: 'Tipo autorización',    tipo: 'text' },
    { key: 'numero_autorizacion',  label: 'N° autorización',      tipo: 'text' },
    { key: 'duracion_tratamiento', label: 'Duración tratamiento', tipo: 'text' },
  ]},
  { titulo: 'OXÍGENO', campos: [
    { key: 'equipos_oxigeno',         label: 'Equipos oxígeno', tipo: 'textarea' },
    { key: 'flujo_oxigeno_litros',    label: 'Flujo (L/min)',   tipo: 'text' },
    { key: 'consumo_horas',           label: 'Horas/día',       tipo: 'text' },
    { key: 'requiere_bpp',            label: 'Requiere BPP',    tipo: 'text' },
    { key: 'insumos_oxigeno_terapia', label: 'Insumos',         tipo: 'textarea' },
  ]},
  { titulo: 'SAHOS / CPAP', campos: [
    { key: 'equipo_sahos',  label: 'Equipo SAHOS',  tipo: 'text' },
    { key: 'presiones',     label: 'Presiones',     tipo: 'text' },
    { key: 'tipo_mascara',  label: 'Tipo máscara',  tipo: 'text' },
    { key: 'talla_mascara', label: 'Talla máscara', tipo: 'text' },
  ]},
  { titulo: 'OBSERVACIONES', campos: [
    { key: 'resumen',       label: 'Resumen Claude', tipo: 'textarea', editable: false },
    { key: 'confianza',     label: 'Confianza',      tipo: 'text',     editable: false },
    { key: 'razon',         label: 'Razón',          tipo: 'textarea', editable: false },
    { key: 'observaciones', label: 'Observaciones',  tipo: 'textarea' },
  ]},
];

// ── Cargar lista ──────────────────────────────────────────
async function cargarOrdenes() {
  const res = await fetch('/api/ordenes');
  todosRegistros = await res.json();
  aplicarFiltros(); // en vez de renderLista(todosRegistros) directo, para preservar filtros activos
}

function renderLista(lista) {
  const el = document.getElementById('lista-ordenes');
  if (!lista.length) { el.innerHTML = '<div class="empty-msg">Sin registros</div>'; return; }
  el.innerHTML = lista.map(o => {
    const linea = o.linea_servicio || '';
    const badgeClass = linea === 'Oxigeno' || linea === 'OXIGENO' ? 'badge-oxigeno'
      : linea === 'SAHOS' ? 'badge-sahos'
      : o.es_orden_mei ? '' : 'badge-no';
    const badgeLabel = linea || (o.es_orden_mei ? 'MEI' : 'No MEI');
    const fecha = o.fecha_procesamiento ? o.fecha_procesamiento.substring(0,16) : '';
    const esGestionada = o.gestionada === true || o.gestionada === 't';
    const gBadge = esGestionada ? '<span class="badge badge-gestionada">✅ Gestionada</span>' : '';
    return `<div class="orden-item" data-id="${o.id}" onclick="seleccionarOrden(${o.id}, this)">
      <div class="oi-paciente">${o.nombre_paciente || '(Sin nombre)'}</div>
      <div class="oi-eps">${o.eps || ''} <span class="badge ${badgeClass}">${badgeLabel}</span> ${gBadge}</div>
      <div class="oi-meta">${o.municipio || ''} ${o.departamento || ''}</div>
      <div class="oi-fecha">${fecha}</div>
    </div>`;
  }).join('');
}

// ── Seleccionar orden ─────────────────────────────────────
async function seleccionarOrden(id, el) {
  document.querySelectorAll('.orden-item').forEach(e => e.classList.remove('activo'));
  el.classList.add('activo');

  // Limpiar zonas al cambiar de orden
  limpiarVisor();
  document.getElementById('lista-adjuntos').innerHTML =
    '<div class="empty-msg" style="padding:20px;font-size:12px;">Cargando...</div>';

  const res   = await fetch(`/api/orden/${id}`);
  ordenActual = await res.json();

  renderZonas25(ordenActual);
  cargarAdjuntos(id);

  const esGestionada = ordenActual.gestionada === true || ordenActual.gestionada === 't';
  const btnG = document.getElementById('btn-gestionar');
  const btnS = document.getElementById('btn-guardar');

  btnS.classList.add('visible');
  btnG.classList.add('visible');

  if (esGestionada) {
    btnG.textContent  = '✅ Gestionada';
    btnG.className    = 'btn-accion btn-gestionada-disabled visible';
    btnG.onclick      = null;
  } else {
    btnG.textContent  = '✅ Marcar gestionada';
    btnG.className    = 'btn-accion btn-gestionar visible';
    btnG.onclick      = marcarGestionada;
  }
}

// ── Render zonas 2+5 alineadas ───────────────────────────
function renderZonas25(orden) {
  const scroll = document.getElementById('scroll-z25');
  let html = '';

  for (const sec of SECCIONES) {
    html += `<table class="campos-tabla">
      <tr><td colspan="3" class="seccion-titulo">${sec.titulo}</td></tr>`;

    for (const c of sec.campos) {
      const val      = orden[c.key] ?? '';
      const editable = c.editable !== false;

      let display;
      if (c.tipo === 'bool') {
        display = (val === true || val === 't' || val === '1' || val === 'true') ? '✅ Sí' : '❌ No';
      } else {
        display = escHtml(String(val));
      }

      let input;
      if (!editable) {
        input = `<span style="color:#888;font-size:12px;">${display}</span>`;
      } else if (c.tipo === 'textarea') {
        input = `<textarea name="${c.key}" rows="2">${escHtml(String(val))}</textarea>`;
      } else {
        input = `<input type="text" name="${c.key}" value="${escHtml(String(val))}">`;
      }

      html += `<tr>
        <td class="col-label">${c.label}</td>
        <td class="col-detail">${display}</td>
        <td class="col-edit">${input}</td>
      </tr>`;
    }
    html += '</table>';
  }
  scroll.innerHTML = html;
}

// ── Guardar cambios ───────────────────────────────────────
async function guardarCambios() {
  if (!ordenActual) return;
  const scroll = document.getElementById('scroll-z25');
  const inputs = scroll.querySelectorAll('input, textarea, select');
  const datos  = {};
  inputs.forEach(inp => { if (inp.name) datos[inp.name] = inp.value; });

  const res = await fetch(`/api/orden/${ordenActual.id}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(datos)
  });
  const r = await res.json();
  if (r.ok) {
    mostrarToast('✅ Guardado correctamente');
    ordenActual = { ...ordenActual, ...datos };
    renderZonas25(ordenActual);
    cargarOrdenes();
  } else {
    mostrarToast('❌ Error al guardar', true);
  }
}

// ── Marcar gestionada ─────────────────────────────────────
async function marcarGestionada() {
  if (!ordenActual) return;
  const res = await fetch(`/api/orden/${ordenActual.id}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ gestionada: 'true' })
  });
  const r = await res.json();
  if (r.ok) {
    mostrarToast('✅ Orden marcada como gestionada');
    ordenActual.gestionada = true;
    renderZonas25(ordenActual);
    const btnG   = document.getElementById('btn-gestionar');
    btnG.textContent = '✅ Gestionada';
    btnG.className   = 'btn-accion btn-gestionada-disabled visible';
    btnG.onclick     = null;
    cargarOrdenes();
  } else {
    mostrarToast('❌ Error al marcar', true);
  }
}

// ── Adjuntos ──────────────────────────────────────────────
async function cargarAdjuntos(ordenId) {
  const res      = await fetch(`/api/adjuntos/${ordenId}`);
  const adjuntos = await res.json();
  const el       = document.getElementById('lista-adjuntos');

  if (!adjuntos.length) {
    el.innerHTML = '<div class="empty-msg" style="padding:20px;font-size:12px;">Sin adjuntos</div>';
    return;
  }

  el.innerHTML = adjuntos.map(a => {
    const ext   = (a.extension || '').toLowerCase();
    const icon  = iconoPorExt(ext);
    const clase = clasePorExt(ext);
    const tam   = a.tamano_bytes ? formatBytes(a.tamano_bytes) : '';
    return `<div class="adjunto-item" data-id="${a.id}"
      onclick="verAdjunto(${a.id},'${ext}','${escHtml(a.mime_type)}',this)">
      <div class="adjunto-icon ${clase}">${icon}</div>
      <div class="adjunto-info">
        <div class="adjunto-nombre" title="${escHtml(a.nombre_archivo)}">${escHtml(a.nombre_archivo)}</div>
        <div class="adjunto-meta">${ext.toUpperCase()} · ${tam}</div>
      </div>
    </div>`;
  }).join('');
}

function iconoPorExt(ext) {
  if (ext === 'pdf') return '📄';
  if (['xlsx','xls'].includes(ext)) return '📊';
  if (ext === 'txt') return '📝';
  if (['jpg','jpeg','png','gif','webp'].includes(ext)) return '🖼️';
  return '📎';
}
function clasePorExt(ext) {
  if (ext === 'pdf') return 'icon-pdf';
  if (['xlsx','xls'].includes(ext)) return 'icon-xlsx';
  if (ext === 'txt') return 'icon-txt';
  if (['jpg','jpeg','png','gif','webp'].includes(ext)) return 'icon-img';
  return 'icon-otro';
}

// ── Visualizador ──────────────────────────────────────────
async function verAdjunto(id, ext, mime, el) {
  document.querySelectorAll('.adjunto-item').forEach(e => e.classList.remove('activo'));
  el.classList.add('activo');
  zoomActual = 1.0;
  actualizarZoomLabel();

  const url       = `/api/adjunto/${id}`;
  const contenido = document.getElementById('visor-contenido');
  const esImg     = ['jpg','jpeg','png','gif','webp'].includes(ext);
  const esPdf     = ext === 'pdf';
  const esHtml    = ext === 'html' || ext === 'htm';
  const esTxt     = ext === 'txt';
  const esExcel   = ['xlsx','xls'].includes(ext);

  if (esPdf || esHtml) {
    // El navegador renderiza PDF e HTML directo dentro del iframe
    contenido.innerHTML = `<iframe src="${url}"></iframe>`;

  } else if (esImg) {
    contenido.innerHTML = `<img src="${url}" id="visor-img" alt="Adjunto">`;

  } else if (esTxt) {
    contenido.innerHTML = `<div class="visor-vacio">Cargando texto...</div>`;
    try {
      const res  = await fetch(url);
      const text = await res.text();
      contenido.innerHTML = `<pre id="visor-txt">${escHtml(text)}</pre>`;
    } catch(e) {
      contenido.innerHTML = `<div class="visor-vacio">
        <div class="icon">⚠️</div>
        <div style="margin-bottom:10px">No se pudo cargar el archivo</div>
        <a href="${url}" target="_blank" download style="color:#7ab8ff;text-decoration:underline;">⬇️ Descargar</a>
      </div>`;
    }

  } else if (esExcel) {
    contenido.innerHTML = `<div class="visor-vacio">Cargando hoja de cálculo...</div>`;
    try {
      const res    = await fetch(url);
      const buffer = await res.arrayBuffer();
      const wb     = XLSX.read(buffer, { type: 'array' });
      const hoja   = wb.Sheets[wb.SheetNames[0]];
      const tabla  = XLSX.utils.sheet_to_html(hoja, { editable: false });
      contenido.innerHTML = `<div id="visor-excel-wrap">${tabla}</div>`;
    } catch(e) {
      contenido.innerHTML = `<div class="visor-vacio">
        <div class="icon">⚠️</div>
        <div style="margin-bottom:10px">No se pudo previsualizar el Excel</div>
        <a href="${url}" target="_blank" download style="color:#7ab8ff;text-decoration:underline;">⬇️ Descargar</a>
      </div>`;
    }

  } else {
    contenido.innerHTML = `<div class="visor-vacio">
      <div class="icon">📎</div>
      <div style="margin-bottom:10px">Sin previsualización para este tipo</div>
      <a href="${url}" target="_blank" download style="color:#7ab8ff;text-decoration:underline;">⬇️ Descargar</a>
    </div>`;
  }
}

function limpiarVisor() {
  document.getElementById('visor-contenido').innerHTML = `
    <div class="visor-vacio"><div class="icon">📄</div><div>Selecciona un adjunto</div></div>`;
  document.querySelectorAll('.adjunto-item').forEach(e => e.classList.remove('activo'));
  zoomActual = 1.0;
  actualizarZoomLabel();
}

function zoom(delta) { zoomActual = Math.min(4, Math.max(0.2, zoomActual + delta)); aplicarZoom(); }
function resetZoom() { zoomActual = 1.0; aplicarZoom(); }
function aplicarZoom() {
  const img = document.getElementById('visor-img');
  if (img) img.style.transform = `scale(${zoomActual})`;
  actualizarZoomLabel();
}
function actualizarZoomLabel() {
  document.getElementById('zoom-nivel').textContent = Math.round(zoomActual * 100) + '%';
}

// ── Filtros ───────────────────────────────────────────────
function aplicarFiltros() {
  const q      = (document.getElementById('buscador').value || '').toLowerCase();
  const estado = document.getElementById('filtro-estado').value;
  const desde  = document.getElementById('filtro-desde').value;
  const hasta  = document.getElementById('filtro-hasta').value;

  const resultado = todosRegistros.filter(o => {
    // Filtro texto
    if (q) {
      const match =
        (o.nombre_paciente    || '').toLowerCase().includes(q) ||
        (o.eps                || '').toLowerCase().includes(q) ||
        (o.municipio          || '').toLowerCase().includes(q) ||
        (o.documento_paciente || '').toLowerCase().includes(q);
      if (!match) return false;
    }

    // Filtro estado
    if (estado === 'gestionada') {
      if (o.gestionada !== true && o.gestionada !== 't') return false;
    } else if (estado === 'pendiente') {
      if (o.gestionada === true || o.gestionada === 't') return false;
    } else if (estado === 'mei') {
      if (!o.es_orden_mei) return false;
    } else if (estado === 'no-mei') {
      if (o.es_orden_mei) return false;
    }

    // Filtro fecha
    if (desde || hasta) {
      const fechaStr = (o.fecha_procesamiento || '').substring(0, 10);
      if (!fechaStr) return false;
      if (desde && fechaStr < desde) return false;
      if (hasta && fechaStr > hasta) return false;
    }

    return true;
  });

  // Mostrar conteo
  const total = todosRegistros.length;
  const shown = resultado.length;
  document.getElementById('filtro-count').textContent =
    shown === total ? `${total} registros` : `${shown} de ${total} registros`;

  renderLista(resultado);
}

function limpiarFiltros() {
  document.getElementById('filtro-estado').value = '';
  document.getElementById('filtro-desde').value  = '';
  document.getElementById('filtro-hasta').value  = '';
  document.getElementById('buscador').value       = '';
  document.getElementById('filtro-count').textContent = '';
  renderLista(todosRegistros);
}

// ── Helpers ───────────────────────────────────────────────
function escHtml(str) {
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function formatBytes(b) {
  if (b < 1024) return b + ' B';
  if (b < 1048576) return (b/1024).toFixed(1) + ' KB';
  return (b/1048576).toFixed(1) + ' MB';
}
function mostrarToast(msg, error=false) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className   = error ? 'error' : '';
  t.style.display = 'block';
  setTimeout(() => t.style.display = 'none', 3000);
}

// ── Auto-refresh con contador ────────────────────────────
const REFRESH_INTERVAL_SEG = 60;
let segundosParaRefresh    = REFRESH_INTERVAL_SEG;
let refreshTimerId         = null;

function iniciarAutoRefresh() {
  if (refreshTimerId) clearInterval(refreshTimerId);
  segundosParaRefresh = REFRESH_INTERVAL_SEG;
  actualizarContadorRefresh();

  refreshTimerId = setInterval(async () => {
    segundosParaRefresh--;
    if (segundosParaRefresh <= 0) {
      await cargarOrdenes();
      segundosParaRefresh = REFRESH_INTERVAL_SEG;
    }
    actualizarContadorRefresh();
  }, 1000);
}

function actualizarContadorRefresh() {
  const el = document.getElementById('refresh-countdown');
  if (el) el.textContent = `⟳ ${segundosParaRefresh}s`;
}

cargarOrdenes();
iniciarAutoRefresh();
</script>
</body>
</html>
