<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Visor MEI — Oxipro</title>
<style>
/* ─── Reset & Base ──────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; background: #f0f2f5; color: #222; overflow: hidden; }

/* ─── Layout principal ─────────────────────────────────────── */
#app {
  display: grid;
  grid-template-columns: 300px 1fr 1fr;
  grid-template-rows: 60% 40%;
  height: 100vh;
  gap: 6px;
  padding: 6px;
}

/* ─── Zona 1 — Grid registros ──────────────────────────────── */
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
#z1 .zona-header {
  padding: 10px 14px;
  background: #1a3a5c;
  color: #fff;
  font-weight: 600;
  font-size: 14px;
  flex-shrink: 0;
}
#z1 .search-bar {
  padding: 8px 10px;
  border-bottom: 1px solid #e8e8e8;
  flex-shrink: 0;
}
#z1 .search-bar input {
  width: 100%;
  padding: 5px 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 12px;
}
#lista-ordenes {
  flex: 1;
  overflow-y: auto;
}
.orden-item {
  padding: 10px 14px;
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer;
  transition: background .15s;
}
.orden-item:hover { background: #f0f6ff; }
.orden-item.activo { background: #ddeeff; border-left: 3px solid #1a3a5c; }
.orden-item .oi-paciente { font-weight: 600; color: #1a3a5c; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.orden-item .oi-meta { font-size: 11px; color: #666; margin-top: 2px; }
.orden-item .oi-eps { font-size: 11px; color: #2a7a4f; font-weight: 500; }
.orden-item .oi-fecha { font-size: 10px; color: #999; margin-top: 2px; }
.badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: 600; }
.badge-oxigeno { background: #dff0ff; color: #0066cc; }
.badge-sahos   { background: #fff0dd; color: #cc6600; }
.badge-no      { background: #ffe0e0; color: #cc0000; }

/* ─── Zona 2 — Detalle readonly ─────────────────────────────── */
#z2 {
  grid-row: 1;
  grid-column: 2;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* ─── Zona 5 — Detalle editable ─────────────────────────────── */
#z5 {
  grid-row: 1;
  grid-column: 3;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* Header zonas 2 y 5 */
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
.zona-header .btn-guardar {
  background: #27ae60;
  color: #fff;
  border: none;
  padding: 4px 12px;
  border-radius: 4px;
  font-size: 12px;
  cursor: pointer;
  display: none;
}
.zona-header .btn-guardar:hover { background: #219a52; }
.zona-header .btn-guardar.visible { display: inline-block; }

/* Scroll sincronizado */
.campos-scroll {
  flex: 1;
  overflow-y: scroll;
  overflow-x: hidden;
  padding: 12px;
}

/* Tabla de campos */
.campos-tabla { width: 100%; border-collapse: collapse; }
.campos-tabla tr { border-bottom: 1px solid #f0f0f0; }
.campos-tabla tr:hover { background: #fafafa; }
.campos-tabla td { padding: 5px 8px; vertical-align: top; }
.campos-tabla td.label {
  font-weight: 600;
  color: #555;
  width: 45%;
  font-size: 11px;
  white-space: nowrap;
}
.campos-tabla td.valor {
  color: #222;
  font-size: 12px;
  word-break: break-word;
}
.campos-tabla td.valor input,
.campos-tabla td.valor textarea,
.campos-tabla td.valor select {
  width: 100%;
  border: 1px solid #ddd;
  border-radius: 3px;
  padding: 3px 6px;
  font-size: 12px;
  font-family: inherit;
  background: #fafeff;
}
.campos-tabla td.valor textarea { resize: vertical; min-height: 50px; }
.campos-tabla td.valor input:focus,
.campos-tabla td.valor textarea:focus { outline: none; border-color: #1a3a5c; }

/* Sección separadora */
.seccion-titulo {
  background: #e8f0fa;
  color: #1a3a5c;
  font-weight: 700;
  font-size: 11px;
  padding: 4px 8px;
  text-transform: uppercase;
  letter-spacing: .5px;
}

/* Placeholder vacío */
.empty-msg {
  color: #aaa;
  text-align: center;
  padding: 40px 20px;
  font-size: 13px;
}

/* ─── Zona 3 — Adjuntos ─────────────────────────────────────── */
#z3 {
  grid-row: 2;
  grid-column: 2;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-width: 0;
}
#lista-adjuntos {
  flex: 1;
  overflow-y: auto;
  padding: 8px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.adjunto-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 8px;
  border: 1px solid #e8e8e8;
  border-radius: 6px;
  cursor: pointer;
  transition: background .15s;
}
.adjunto-item:hover { background: #f0f6ff; }
.adjunto-item.activo { background: #ddeeff; border-color: #1a3a5c; }
.adjunto-icon {
  width: 36px;
  height: 36px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}
.icon-pdf  { background: #ffe0e0; }
.icon-xlsx { background: #e0ffe0; }
.icon-txt  { background: #e8e8e8; }
.icon-img  { background: #fff0dd; }
.icon-otro { background: #f0e8ff; }
.adjunto-info { min-width: 0; }
.adjunto-nombre { font-size: 11px; font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.adjunto-meta { font-size: 10px; color: #888; }

/* ─── Zona 4 — Visor ────────────────────────────────────────── */
#z4 {
  grid-row: 2;
  grid-column: 3;
  background: #1a1a2e;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
#z4 .zona-header { background: #0f0f1a; }
#visor-toolbar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 12px;
  background: #0f0f1a;
  flex-shrink: 0;
}
#visor-toolbar button {
  background: #2a2a4a;
  color: #fff;
  border: 1px solid #444;
  padding: 3px 10px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}
#visor-toolbar button:hover { background: #3a3a6a; }
#visor-toolbar span { color: #aaa; font-size: 12px; }
#visor-contenido {
  flex: 1;
  overflow: auto;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
}
#visor-contenido iframe {
  width: 100%;
  height: 100%;
  border: none;
  background: #fff;
  border-radius: 4px;
}
#visor-contenido img {
  max-width: 100%;
  transition: transform .2s;
  transform-origin: center center;
  border-radius: 4px;
}
#visor-contenido .visor-vacio {
  color: #555;
  font-size: 14px;
  text-align: center;
}
#visor-contenido .visor-vacio .icon { font-size: 48px; margin-bottom: 10px; }

/* ─── Toast notificación ─────────────────────────────────────── */
#toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #27ae60;
  color: #fff;
  padding: 10px 20px;
  border-radius: 6px;
  font-size: 13px;
  display: none;
  z-index: 999;
  box-shadow: 0 3px 10px rgba(0,0,0,.2);
}
#toast.error { background: #e74c3c; }

/* ─── Loading spinner ────────────────────────────────────────── */
.loading { color: #888; padding: 20px; text-align: center; }

/* ─── Scrollbar estético ─────────────────────────────────────── */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: #f5f5f5; }
::-webkit-scrollbar-thumb { background: #bbb; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #999; }
</style>
</head>
<body>
<div id="app">

  <!-- ══ ZONA 1 — Grid de registros ══ -->
  <div id="z1">
    <div class="zona-header">📋 Órdenes MEI</div>
    <div class="search-bar">
      <input type="text" id="buscador" placeholder="Buscar paciente, EPS, municipio...">
    </div>
    <div id="lista-ordenes">
      <div class="loading">Cargando registros...</div>
    </div>
  </div>

  <!-- ══ ZONA 2 — Detalle readonly ══ -->
  <div id="z2">
    <div class="zona-header">🔍 Detalle del registro</div>
    <div class="campos-scroll" id="scroll-z2">
      <div class="empty-msg">← Selecciona un registro</div>
    </div>
  </div>

  <!-- ══ ZONA 5 — Detalle editable ══ -->
  <div id="z5">
    <div class="zona-header">
      ✏️ Edición
      <button class="btn-guardar" id="btn-guardar" onclick="guardarCambios()">💾 Guardar</button>
    </div>
    <div class="campos-scroll" id="scroll-z5">
      <div class="empty-msg">← Selecciona un registro</div>
    </div>
  </div>

  <!-- ══ ZONA 3 — Lista adjuntos ══ -->
  <div id="z3">
    <div class="zona-header">📎 Adjuntos</div>
    <div id="lista-adjuntos">
      <div class="empty-msg" style="padding:20px">Sin adjuntos</div>
    </div>
  </div>

  <!-- ══ ZONA 4 — Visor ══ -->
  <div id="z4">
    <div class="zona-header">
      🖼️ Visualizador
    </div>
    <div id="visor-toolbar">
      <button onclick="zoom(-0.2)" title="Alejar">−</button>
      <button onclick="zoom(0.2)"  title="Acercar">+</button>
      <button onclick="resetZoom()" title="Restablecer">⟳</button>
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

<div id="toast"></div>

<script>
// ═══════════════════════════════════════════════════════════
// Estado global
// ═══════════════════════════════════════════════════════════
let ordenActual  = null;
let zoomActual   = 1.0;
let sincScroll   = false;
let todosRegistros = [];

// ═══════════════════════════════════════════════════════════
// Campos de la orden en orden de visualización
// ═══════════════════════════════════════════════════════════
const SECCIONES = [
  { titulo: 'DATOS DEL CORREO', campos: [
    { key: 'fecha_procesamiento', label: 'Fecha procesamiento', tipo: 'text', editable: false },
    { key: 'fecha_hora_correo',   label: 'Fecha/hora correo',   tipo: 'text' },
    { key: 'asunto_correo',       label: 'Asunto',              tipo: 'text' },
    { key: 'remitente',           label: 'Remitente',           tipo: 'text' },
    { key: 'uid_correo',          label: 'UID correo',          tipo: 'text', editable: false },
    { key: 'message_id',          label: 'Message-ID',          tipo: 'text', editable: false },
  ]},
  { titulo: 'CLASIFICACIÓN', campos: [
    { key: 'es_orden_mei',        label: 'Es orden MEI',        tipo: 'bool', editable: false },
    { key: 'linea_servicio',      label: 'Línea de servicio',   tipo: 'text' },
    { key: 'proveedor',           label: 'Proveedor',           tipo: 'text' },
    { key: 'eps',                 label: 'EPS',                 tipo: 'text' },
    { key: 'entidades',           label: 'Entidad/Sucursal',    tipo: 'text' },
    { key: 'asignado_a',          label: 'Asignado a',          tipo: 'text' },
  ]},
  { titulo: 'DATOS DEL PACIENTE', campos: [
    { key: 'nombre_paciente',         label: 'Nombre completo',         tipo: 'text' },
    { key: 'primer_nombre',           label: 'Primer nombre',           tipo: 'text' },
    { key: 'segundo_nombre',          label: 'Segundo nombre',          tipo: 'text' },
    { key: 'primer_apellido',         label: 'Primer apellido',         tipo: 'text' },
    { key: 'segundo_apellido',        label: 'Segundo apellido',        tipo: 'text' },
    { key: 'tipo_documento_paciente', label: 'Tipo documento',          tipo: 'text' },
    { key: 'documento_paciente',      label: 'Documento',               tipo: 'text' },
    { key: 'fecha_nacimiento',        label: 'Fecha nacimiento',        tipo: 'text' },
    { key: 'edad',                    label: 'Edad',                    tipo: 'text' },
    { key: 'rango_edad',              label: 'Rango edad',              tipo: 'text' },
    { key: 'genero',                  label: 'Género',                  tipo: 'text' },
    { key: 'regimen',                 label: 'Régimen',                 tipo: 'text' },
    { key: 'categoria',               label: 'Categoría IBC',           tipo: 'text' },
    { key: 'nivel_ibc',               label: 'Nivel IBC',               tipo: 'text' },
    { key: 'tipo_afiliado',           label: 'Tipo afiliado',           tipo: 'text' },
  ]},
  { titulo: 'DATOS DE CONTACTO', campos: [
    { key: 'departamento',      label: 'Departamento',  tipo: 'text' },
    { key: 'municipio',         label: 'Municipio',     tipo: 'text' },
    { key: 'direccion',         label: 'Dirección',     tipo: 'textarea' },
    { key: 'telefonos',         label: 'Teléfonos',     tipo: 'text' },
    { key: 'correo_electronico',label: 'Correo',        tipo: 'text' },
  ]},
  { titulo: 'DATOS MÉDICOS', campos: [
    { key: 'diagnostico',              label: 'Diagnóstico',           tipo: 'text' },
    { key: 'ips_formula',              label: 'IPS fórmula',           tipo: 'text' },
    { key: 'tipo_documento_medico',    label: 'Tipo doc. médico',      tipo: 'text' },
    { key: 'documento_medico',         label: 'Documento médico',      tipo: 'text' },
    { key: 'nombre_medico_formulador', label: 'Médico formulador',     tipo: 'text' },
    { key: 'fecha_formulacion',        label: 'Fecha formulación',     tipo: 'text' },
    { key: 'salida_hospitalaria',      label: 'Salida hospitalaria',   tipo: 'text' },
  ]},
  { titulo: 'AUTORIZACIÓN', campos: [
    { key: 'tipo_autorizacion',   label: 'Tipo autorización',   tipo: 'text' },
    { key: 'numero_autorizacion', label: 'N° autorización',     tipo: 'text' },
    { key: 'duracion_tratamiento',label: 'Duración tratamiento',tipo: 'text' },
  ]},
  { titulo: 'OXÍGENO', campos: [
    { key: 'equipos_oxigeno',      label: 'Equipos oxígeno',     tipo: 'textarea' },
    { key: 'flujo_oxigeno_litros', label: 'Flujo (L/min)',       tipo: 'text' },
    { key: 'consumo_horas',        label: 'Consumo (horas/día)', tipo: 'text' },
    { key: 'requiere_bpp',         label: 'Requiere BPP',        tipo: 'text' },
    { key: 'insumos_oxigeno_terapia', label: 'Insumos',          tipo: 'textarea' },
  ]},
  { titulo: 'SAHOS / CPAP', campos: [
    { key: 'equipo_sahos', label: 'Equipo SAHOS', tipo: 'text' },
    { key: 'presiones',    label: 'Presiones',    tipo: 'text' },
    { key: 'tipo_mascara', label: 'Tipo máscara', tipo: 'text' },
    { key: 'talla_mascara',label: 'Talla máscara',tipo: 'text' },
  ]},
  { titulo: 'OBSERVACIONES', campos: [
    { key: 'resumen',       label: 'Resumen Claude', tipo: 'textarea', editable: false },
    { key: 'confianza',     label: 'Confianza',      tipo: 'text',     editable: false },
    { key: 'razon',         label: 'Razón',          tipo: 'textarea', editable: false },
    { key: 'observaciones', label: 'Observaciones',  tipo: 'textarea' },
  ]},
];

// ═══════════════════════════════════════════════════════════
// Cargar lista de órdenes
// ═══════════════════════════════════════════════════════════
async function cargarOrdenes() {
  const res  = await fetch('/api/ordenes');
  todosRegistros = await res.json();
  renderLista(todosRegistros);
}

function renderLista(lista) {
  const el = document.getElementById('lista-ordenes');
  if (!lista.length) {
    el.innerHTML = '<div class="empty-msg">Sin registros</div>';
    return;
  }
  el.innerHTML = lista.map(o => {
    const linea = o.linea_servicio || '';
    const badgeClass = linea === 'Oxigeno' || linea === 'OXIGENO'
      ? 'badge-oxigeno'
      : linea === 'SAHOS'
        ? 'badge-sahos'
        : o.es_orden_mei ? '' : 'badge-no';
    const badgeLabel = linea || (o.es_orden_mei ? 'MEI' : 'No MEI');
    const fecha = o.fecha_procesamiento ? o.fecha_procesamiento.substring(0,16) : '';
    return `
      <div class="orden-item" data-id="${o.id}" onclick="seleccionarOrden(${o.id}, this)">
        <div class="oi-paciente">${o.nombre_paciente || '(Sin nombre)'}</div>
        <div class="oi-eps">${o.eps || ''} <span class="badge ${badgeClass}">${badgeLabel}</span></div>
        <div class="oi-meta">${o.municipio || ''} ${o.departamento || ''}</div>
        <div class="oi-fecha">${fecha}</div>
      </div>`;
  }).join('');
}

// ═══════════════════════════════════════════════════════════
// Seleccionar orden
// ═══════════════════════════════════════════════════════════
async function seleccionarOrden(id, el) {
  document.querySelectorAll('.orden-item').forEach(e => e.classList.remove('activo'));
  el.classList.add('activo');

  const res   = await fetch(`/api/orden/${id}`);
  ordenActual = await res.json();

  renderZona2(ordenActual);
  renderZona5(ordenActual);
  cargarAdjuntos(id);

  document.getElementById('btn-guardar').classList.add('visible');
  sincronizarScrolls();
}

// ═══════════════════════════════════════════════════════════
// Renderizar Zona 2 (readonly)
// ═══════════════════════════════════════════════════════════
function renderZona2(orden) {
  const scroll = document.getElementById('scroll-z2');
  scroll.innerHTML = SECCIONES.map(sec => `
    <tr class="seccion-titulo"><td colspan="2" class="seccion-titulo">${sec.titulo}</td></tr>
    ${sec.campos.map(c => {
      const val = orden[c.key] ?? '';
      const display = c.tipo === 'bool'
        ? (val === true || val === 't' || val === '1' ? '✅ Sí' : '❌ No')
        : escHtml(String(val));
      return `<tr>
        <td class="label">${c.label}</td>
        <td class="valor">${display}</td>
      </tr>`;
    }).join('')}
  `).map(html => `<table class="campos-tabla">${html}</table>`).join('');
}

// ═══════════════════════════════════════════════════════════
// Renderizar Zona 5 (editable)
// ═══════════════════════════════════════════════════════════
function renderZona5(orden) {
  const scroll = document.getElementById('scroll-z5');
  scroll.innerHTML = SECCIONES.map(sec => `
    <table class="campos-tabla">
    <tr class="seccion-titulo"><td colspan="2" class="seccion-titulo">${sec.titulo}</td></tr>
    ${sec.campos.map(c => {
      const val = escHtml(String(orden[c.key] ?? ''));
      const editable = c.editable !== false;
      let input;
      if (!editable) {
        const display = c.tipo === 'bool'
          ? (orden[c.key] === true || orden[c.key] === 't' ? '✅ Sí' : '❌ No')
          : val;
        input = `<span style="color:#888">${display}</span>`;
      } else if (c.tipo === 'textarea') {
        input = `<textarea name="${c.key}" rows="2">${val}</textarea>`;
      } else {
        input = `<input type="text" name="${c.key}" value="${val}">`;
      }
      return `<tr><td class="label">${c.label}</td><td class="valor">${input}</td></tr>`;
    }).join('')}
    </table>
  `).join('');
}

// ═══════════════════════════════════════════════════════════
// Guardar cambios
// ═══════════════════════════════════════════════════════════
async function guardarCambios() {
  if (!ordenActual) return;
  const scroll = document.getElementById('scroll-z5');
  const inputs = scroll.querySelectorAll('input, textarea, select');
  const datos = {};
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
    renderZona2(ordenActual);
  } else {
    mostrarToast('❌ Error al guardar', true);
  }
}

// ═══════════════════════════════════════════════════════════
// Adjuntos
// ═══════════════════════════════════════════════════════════
async function cargarAdjuntos(ordenId) {
  const res      = await fetch(`/api/adjuntos/${ordenId}`);
  const adjuntos = await res.json();
  const el       = document.getElementById('lista-adjuntos');

  if (!adjuntos.length) {
    el.innerHTML = '<div class="empty-msg" style="padding:20px">Sin adjuntos</div>';
    limpiarVisor();
    return;
  }

  el.innerHTML = adjuntos.map(a => {
    const ext   = (a.extension || '').toLowerCase();
    const icon  = iconoPorExtension(ext);
    const clase = clasePorExtension(ext);
    const tam   = a.tamano_bytes ? formatBytes(a.tamano_bytes) : '';
    return `
      <div class="adjunto-item" data-id="${a.id}" onclick="verAdjunto(${a.id}, '${ext}', '${escHtml(a.mime_type)}', this)">
        <div class="adjunto-icon ${clase}">${icon}</div>
        <div class="adjunto-info">
          <div class="adjunto-nombre" title="${escHtml(a.nombre_archivo)}">${escHtml(a.nombre_archivo)}</div>
          <div class="adjunto-meta">${ext.toUpperCase()} · ${tam}</div>
        </div>
      </div>`;
  }).join('');
}

function iconoPorExtension(ext) {
  if (ext === 'pdf')  return '📄';
  if (['xlsx','xls'].includes(ext)) return '📊';
  if (ext === 'txt')  return '📝';
  if (['jpg','jpeg','png','gif','webp'].includes(ext)) return '🖼️';
  return '📎';
}

function clasePorExtension(ext) {
  if (ext === 'pdf')  return 'icon-pdf';
  if (['xlsx','xls'].includes(ext)) return 'icon-xlsx';
  if (ext === 'txt')  return 'icon-txt';
  if (['jpg','jpeg','png','gif','webp'].includes(ext)) return 'icon-img';
  return 'icon-otro';
}

// ═══════════════════════════════════════════════════════════
// Visualizador
// ═══════════════════════════════════════════════════════════
function verAdjunto(id, ext, mime, el) {
  document.querySelectorAll('.adjunto-item').forEach(e => e.classList.remove('activo'));
  el.classList.add('activo');
  zoomActual = 1.0;
  actualizarZoomLabel();

  const url = `/api/adjunto/${id}`;
  const contenido = document.getElementById('visor-contenido');
  const esImagen  = ['jpg','jpeg','png','gif','webp'].includes(ext);
  const esPdf     = ext === 'pdf';

  if (esPdf) {
    contenido.innerHTML = `<iframe src="${url}" id="visor-iframe"></iframe>`;
  } else if (esImagen) {
    contenido.innerHTML = `<img src="${url}" id="visor-img" alt="Adjunto">`;
  } else {
    contenido.innerHTML = `
      <div class="visor-vacio">
        <div class="icon">📎</div>
        <div style="margin-bottom:10px">Este tipo de archivo no tiene previsualización</div>
        <a href="${url}" target="_blank" download
           style="color:#7ab8ff;text-decoration:underline;font-size:13px">
          ⬇️ Descargar archivo
        </a>
      </div>`;
  }
}

function limpiarVisor() {
  document.getElementById('visor-contenido').innerHTML = `
    <div class="visor-vacio">
      <div class="icon">📄</div>
      <div>Selecciona un adjunto</div>
    </div>`;
}

// ═══════════════════════════════════════════════════════════
// Zoom
// ═══════════════════════════════════════════════════════════
function zoom(delta) {
  zoomActual = Math.min(4, Math.max(0.2, zoomActual + delta));
  aplicarZoom();
}
function resetZoom() {
  zoomActual = 1.0;
  aplicarZoom();
}
function aplicarZoom() {
  const img = document.getElementById('visor-img');
  if (img) img.style.transform = `scale(${zoomActual})`;
  actualizarZoomLabel();
}
function actualizarZoomLabel() {
  document.getElementById('zoom-nivel').textContent = Math.round(zoomActual * 100) + '%';
}

// ═══════════════════════════════════════════════════════════
// Scroll sincronizado Z2 ↔ Z5
// ═══════════════════════════════════════════════════════════
function sincronizarScrolls() {
  const z2 = document.getElementById('scroll-z2');
  const z5 = document.getElementById('scroll-z5');

  z2.removeEventListener('scroll', onScrollZ2);
  z5.removeEventListener('scroll', onScrollZ5);
  z2.addEventListener('scroll', onScrollZ2);
  z5.addEventListener('scroll', onScrollZ5);
}

function onScrollZ2() {
  if (sincScroll) return;
  sincScroll = true;
  document.getElementById('scroll-z5').scrollTop = document.getElementById('scroll-z2').scrollTop;
  sincScroll = false;
}
function onScrollZ5() {
  if (sincScroll) return;
  sincScroll = true;
  document.getElementById('scroll-z2').scrollTop = document.getElementById('scroll-z5').scrollTop;
  sincScroll = false;
}

// ═══════════════════════════════════════════════════════════
// Buscador
// ═══════════════════════════════════════════════════════════
document.getElementById('buscador').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  if (!q) { renderLista(todosRegistros); return; }
  const filtrados = todosRegistros.filter(o =>
    (o.nombre_paciente || '').toLowerCase().includes(q) ||
    (o.eps             || '').toLowerCase().includes(q) ||
    (o.municipio       || '').toLowerCase().includes(q) ||
    (o.documento_paciente || '').toLowerCase().includes(q)
  );
  renderLista(filtrados);
});

// ═══════════════════════════════════════════════════════════
// Helpers
// ═══════════════════════════════════════════════════════════
function escHtml(str) {
  return String(str)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;');
}
function formatBytes(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1048576) return (bytes/1024).toFixed(1) + ' KB';
  return (bytes/1048576).toFixed(1) + ' MB';
}
function mostrarToast(msg, error=false) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = error ? 'error' : '';
  t.style.display = 'block';
  setTimeout(() => t.style.display = 'none', 3000);
}

// ═══════════════════════════════════════════════════════════
// Init
// ═══════════════════════════════════════════════════════════
cargarOrdenes();
</script>
</body>
</html>
