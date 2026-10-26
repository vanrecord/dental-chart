<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="color-scheme" content="light">
<title>{{ isset($activeChart) ? 'Chart - ' . $activeChart->patient->full_name : 'Interactive Dental Chart' }}</title>
<style>
:root{--ink:#171717;--paper:#fffefa;--muted:#686868;--line:#202020;--blue:#2457c5;--bg:#f2f4f7;--border:#dadde2}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:#1f2937;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
button,input,select,textarea{font:inherit}button{cursor:pointer}
.nav-strip{background:#ffffff;border-bottom:1px solid #e2e8f0;padding:10px 22px;display:flex;justify-content:space-between;align-items:center;font-size:13px}
.nav-strip a{color:#475569;text-decoration:none;font-weight:600;margin-right:15px}
.nav-strip a:hover{color:#2457c5}
.nav-strip .nav-left{display:flex;align-items:center}
.page{max-width:1440px;margin:auto;padding:20px 22px 42px}
.topbar{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;flex-wrap:wrap;margin-bottom:18px}
.eyebrow{font-size:12px;text-transform:uppercase;letter-spacing:.16em;color:#64748b;font-weight:800}
.title{margin:5px 0 7px;font-size:clamp(24px,3vw,34px);letter-spacing:-.035em;color:#111827}
.sub{margin:0;color:#697386;font-size:14px}
.actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.btn{border:1px solid #d3d8e1;background:#fff;border-radius:9px;padding:9px 13px;color:#303846;font-weight:700;font-size:13px;box-shadow:0 1px 1px #12121208;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
.btn:hover{background:#f8fafc}
.btn-primary{background:#2457c5;color:white;border-color:#2457c5}
.btn-primary:hover{background:#1d48a3}
.btn-danger{color:#b42318;border-color:#f1c5c1}
.patient-selector{display:flex;align-items:center;gap:8px;background:#fff;border:1px solid #d3d8e1;padding:4px 8px;border-radius:9px}
.patient-selector select{border:0;background:transparent;outline:none;font-size:13px;font-weight:600;color:#1e293b}
.toolrow{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px}
.filters{display:flex;gap:6px;flex-wrap:wrap}
.filter{padding:8px 12px;border:1px solid #d6dae2;background:#fff;color:#536071;border-radius:999px;font-size:12px;font-weight:800}
.filter.active{background:#e8efff;color:#1c4bb3;border-color:#adc4ff}
.status{font-size:12px;color:#667085}
.paper-shell{background:#fff;border:1px solid #e1e4e8;border-radius:16px;padding:14px;box-shadow:0 8px 30px #1118270a}
.paper{background:var(--paper);border:1px solid #d2d0c8;padding:24px 26px 22px;box-shadow:inset 0 0 0 1px #ffffffb0}
.paper-heading{font-family:Arial,Helvetica,sans-serif;font-size:20px;font-weight:800;font-style:italic;letter-spacing:-.02em;color:#2a2a2a;margin:0 0 18px}
.paper-heading span{font-weight:500;font-size:17px;letter-spacing:0}
.chart-scroll{overflow-x:auto;padding:0 0 6px}
.chart-inner{min-width:1090px;padding:8px 0 0}
.arch-row{display:flex;align-items:stretch;justify-content:center;gap:12px;min-width:max-content}
.arch-side{display:grid;grid-template-columns:repeat(8,54px);gap:9px;align-items:stretch}
.midline{width:5px;min-width:5px;background:#171717;align-self:stretch;margin:0 0px}
.tooth-slot{width:54px;min-height:80px;display:flex;align-items:center;justify-content:flex-start}
.tooth-slot.empty{pointer-events:none}
.tooth{width:54px;display:flex;flex-direction:column;align-items:center;position:relative}
.tooth-number{border:0;background:transparent;color:#262626;border-radius:3px;font-size:14px;line-height:20px;height:22px;width:100%;padding:0;font-weight:500;text-align:center}
.tooth-number:hover,.tooth-number:focus-visible{background:#e9eefc;outline:1px solid #a8b9ed}
.tooth-face{width:43px;height:51px;display:block;overflow:visible}
.tooth-face polygon{stroke:#252525;stroke-width:2.6;stroke-linejoin:miter;fill:#fff;cursor:pointer;transition:fill .12s,filter .12s}
.tooth-face polygon:hover,.tooth-face polygon:focus{filter:brightness(.89);stroke:#2457c5;stroke-width:4;outline:none}
.primary-tooth .tooth-face{width:35px;height:31px}
.primary-tooth .tooth-number{font-size:12px;height:20px;line-height:18px}
.upper-perm .tooth-number{order:0}
.upper-perm .tooth-face{order:1}
.lower-perm .tooth-face{order:0}
.lower-perm .tooth-number{order:1}
.primary-tooth .tooth-face{order:0}
.primary-tooth .tooth-number{order:1}
.upper-primary{margin-top:7px}
.lower-primary{margin-top:8px}
.upper-primary .tooth-slot,.lower-primary .tooth-slot{min-height:54px}
.upper-primary .tooth-number,.lower-primary .tooth-number{margin-top:0}
.divider-line{display:flex;align-items:center;gap:16px;margin:12px 0 9px;min-width:1090px;height:62px;color:#151515}
.rl-label{font-family:Arial,Helvetica,sans-serif;font-size:28px;font-weight:800;line-height:1}
.horizontal-rule{height:5px;background:#222;flex:1}
.divider-midline{height:62px;width:5px;min-width:5px;background:#171717;margin:0 4px}
.arch-caption{font-size:11px;letter-spacing:.17em;text-transform:uppercase;font-weight:800;color:#858585;text-align:center;margin:0 0 8px}
.legend{border-top:1px solid #d3d0c7;display:flex;align-items:center;gap:8px 15px;flex-wrap:wrap;padding:14px 0 4px;margin-top:19px}
.legend-title{font-size:12px;text-transform:uppercase;letter-spacing:.12em;color:#747474;font-weight:800;margin-right:3px}
.legend-item{display:flex;align-items:center;gap:6px;font-size:12px;color:#4b5563}
.swatch{width:12px;height:12px;border:1px solid #858585;border-radius:3px;display:inline-block}
.other-info{margin-top:20px;display:flex;gap:10px;align-items:flex-start}
.other-info label{font-family:Arial,Helvetica,sans-serif;font-size:22px;white-space:nowrap;font-weight:800;color:#232323}
.other-info textarea{border:0;border-bottom:2px solid #222;border-radius:0;resize:vertical;min-height:33px;flex:1;padding:6px 8px;background:transparent;outline:none;color:#202020;font-size:14px}
.paper-foot{display:flex;justify-content:space-between;gap:14px;margin-top:14px;color:#9b9b96;font-size:10px}
.hint{margin:13px 2px 0;color:#667085;font-size:12px;display:flex;align-items:center;gap:8px}
.hint-dot{width:7px;height:7px;background:#2457c5;border-radius:50%;display:inline-block}
.below-grid{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:14px;margin-top:15px}
.info-card{background:white;border:1px solid var(--border);border-radius:12px;padding:16px}
.info-card h3{font-size:13px;margin:0 0 9px;color:#364152}
.stat-big{font-size:24px;font-weight:800;color:#141b2d}
.stat-small{font-size:12px;color:#7b8493;margin-top:3px}
.findings-list{display:flex;flex-direction:column;gap:7px;max-height:190px;overflow:auto}
.finding-item{display:flex;justify-content:space-between;gap:10px;border:1px solid #e6e8ec;border-radius:8px;padding:8px 10px;font-size:12px}
.finding-item strong{font-weight:800}
.finding-item button{border:0;background:transparent;color:#2457c5;font-weight:800;font-size:11px}
.empty-note{font-size:12px;color:#8992a1}
.modal-backdrop{position:fixed;inset:0;z-index:30;background:rgba(18,25,38,.52);display:none;align-items:center;justify-content:center;padding:18px}
.modal-backdrop.open{display:flex}
.modal{background:#fff;border-radius:17px;width:min(100%,530px);max-height:calc(100vh - 36px);overflow:auto;box-shadow:0 25px 80px #0003}
.modal-head{display:flex;justify-content:space-between;align-items:flex-start;padding:22px 23px 14px;border-bottom:1px solid #eef0f4}
.modal-kicker{font-size:11px;text-transform:uppercase;letter-spacing:.15em;color:#697586;font-weight:800}
.modal-title{margin:5px 0 0;font-size:22px;font-weight:850;letter-spacing:-.03em;color:#161d2b}
.close{border:0;background:#f2f4f7;width:31px;height:31px;border-radius:8px;font-size:22px;line-height:1;color:#667085}
.modal-body{padding:18px 23px 22px}
.field{margin-bottom:14px}
.field label{display:block;font-size:12px;font-weight:800;color:#3b4554;margin-bottom:6px}
.field input:not([type=color]),.field select,.field textarea{width:100%;border:1px solid #d7dce5;border-radius:9px;padding:10px 11px;font-size:13px;background:#fff;color:#222b3b;outline:none}
.field input:focus,.field select:focus,.field textarea:focus{border-color:#87a7fb;box-shadow:0 0 0 3px #eaf0ff}
.field textarea{resize:vertical;min-height:86px}
.field-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.color-row{display:flex;align-items:center;gap:10px}
.color-row input[type=color]{width:45px;height:38px;border:1px solid #d7dce5;border-radius:8px;background:#fff;padding:3px}
.color-help{font-size:12px;color:#7b8493}
.modal-actions{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:19px}
.modal-actions-right{display:flex;gap:8px}
.toast{position:fixed;z-index:60;bottom:22px;left:50%;transform:translateX(-50%);background:#192130;color:white;border-radius:10px;padding:11px 16px;box-shadow:0 8px 30px #0002;font-size:13px;opacity:0;pointer-events:none;transition:opacity .2s}
.toast.show{opacity:1}
@media(max-width:740px){.page{padding:17px 10px 30px}.paper-shell{padding:6px;border-radius:11px}.paper{padding:16px 12px}.paper-heading{font-size:17px}.paper-heading span{font-size:14px}.below-grid{grid-template-columns:1fr}.other-info label{font-size:18px}.actions .btn{padding:9px 10px;font-size:12px}}
@media print{body{background:#fff}.nav-strip,.topbar,.toolrow,.below-grid,.hint,.no-print{display:none!important}.paper-shell{border:0;border-radius:0;padding:0;box-shadow:none}.paper{border:0;box-shadow:none;padding:8mm 5mm}.chart-scroll{overflow:visible}.chart-inner,.divider-line{min-width:0;width:100%}.arch-row{gap:7px}.arch-side{grid-template-columns:repeat(8,minmax(30px,1fr));gap:4px;flex:1}.tooth-slot,.tooth{width:auto;min-width:0}.tooth-face{width:36px;height:43px}.primary-tooth .tooth-face{width:31px;height:27px}.tooth-number{font-size:10px}.midline,.divider-midline{width:3px;min-width:3px}.rl-label{font-size:20px}.divider-line{height:45px;margin:8px 0}.divider-midline{height:45px}.paper-heading{font-size:16px}.paper-heading span{font-size:14px}.other-info{margin-top:14px}.other-info label{font-size:17px}.paper-foot{font-size:8px}}
</style>
</head>
<body>
<div class="nav-strip no-print">
  <div class="nav-left">
    <a href="{{ url('/') }}" style="color:#0f172a;font-weight:800;">🦷 Dental System</a>
    <a href="{{ route('patients.index') }}">Patients</a>
    <a href="{{ route('dental-charts.index') }}">Charts</a>
    <a href="{{ route('dental-conditions.index') }}">Conditions</a>
    <a href="{{ url('/dental') }}" style="color:#2457c5;font-weight:700;">Interactive Chart</a>
  </div>
  <div>
    @if(isset($activeChart))
      <span>Patient: <strong>{{ $activeChart->patient->full_name }}</strong> (#{{ $activeChart->patient->patient_number }}) · Date: {{ $activeChart->chart_date->format('Y-m-d') }}</span>
    @else
      <span style="color:#64748b;">Standalone Demo Mode</span>
    @endif
  </div>
</div>

<div class="page">
  <header class="topbar">
    <div>
      <div class="eyebrow">
        @if(isset($activeChart))
          Patient #{{ $activeChart->patient->patient_number }} · Chart #{{ $activeChart->id }}
        @else
          Dental chart workspace
        @endif
      </div>
      <h1 class="title">
        @if(isset($activeChart))
          {{ $activeChart->patient->full_name }}'s Dental Chart
        @else
          Interactive dental chart
        @endif
      </h1>
      <p class="sub">Click a tooth surface to assign its color and clinical note. Click a tooth number to edit the whole tooth.</p>
    </div>

    <div class="actions no-print">
      @if(isset($patients) && $patients->count() > 0)
        <div class="patient-selector" title="Switch chart session">
          <label for="chartPicker" style="font-size:11px;font-weight:700;color:#64748b;">Chart:</label>
          <select id="chartPicker" onchange="if(this.value) window.location.href=this.value;">
            <option value="{{ url('/dental') }}" {{ !isset($activeChart) ? 'selected' : '' }}>Demo Mode (Local)</option>
            @foreach($patients as $p)
              @foreach($p->dentalCharts as $c)
                <option value="{{ route('dental.show', $c) }}" {{ (isset($activeChart) && $activeChart->id === $c->id) ? 'selected' : '' }}>
                  {{ $p->full_name }} ({{ $c->chart_date->format('Y-m-d') }})
                </option>
              @endforeach
            @endforeach
          </select>
        </div>
      @endif

      <button class="btn" type="button" id="printBtn">Print chart</button>
      <button class="btn" type="button" id="exportBtn">Export JSON</button>
      <button class="btn btn-danger" type="button" id="resetBtn">Reset</button>
      <button class="btn btn-primary" type="button" id="saveBtn">Save chart</button>
    </div>
  </header>

  <div class="toolrow no-print">
    <div class="filters" role="group" aria-label="Show teeth">
      <button class="filter active" data-filter="all" type="button">Show all teeth</button>
      <button class="filter" data-filter="permanent" type="button">Permanent</button>
      <button class="filter" data-filter="primary" type="button">Primary</button>
    </div>
    <div id="saveStatus" class="status" aria-live="polite">Loading chart...</div>
  </div>

  <main class="paper-shell">
    <section class="paper" aria-label="Dental chart form">
      <h2 class="paper-heading">Teeth: <span>(chart teeth present, caries lesions, restorations etc)</span></h2>
      <div class="chart-scroll">
        <div class="chart-inner">
          <p class="arch-caption">Upper arch</p>
          <div id="upperPermanent" class="arch-row upper-perm" aria-label="Upper permanent teeth"></div>
          <div id="upperPrimary" class="arch-row upper-primary" aria-label="Upper primary teeth"></div>
          <div class="divider-line" aria-hidden="true"><span class="rl-label">R</span><span class="horizontal-rule"></span><span class="divider-midline"></span><span class="horizontal-rule"></span><span class="rl-label">L</span></div>
          <div id="lowerPrimary" class="arch-row lower-primary" aria-label="Lower primary teeth"></div>
          <div id="lowerPermanent" class="arch-row lower-perm" aria-label="Lower permanent teeth"></div>
          <p class="arch-caption" style="margin-top:12px;margin-bottom:0">Lower arch</p>
        </div>
      </div>
      <div class="legend" id="legend"><span class="legend-title">Color guide</span></div>
      <div class="other-info"><label for="otherInfo">Other information:</label><textarea id="otherInfo" rows="1" aria-label="Other information" placeholder="General examination notes..."></textarea></div>
      <div class="paper-foot"><span>FDI two-digit tooth numbering</span><span>Click a face · Edit color and notes</span></div>
    </section>
    <p class="hint no-print"><span class="hint-dot"></span>Each outlined face is separately clickable: top, bottom, left, right, and center. Select a tooth number for a whole-tooth note.</p>
  </main>

  <section class="below-grid no-print">
    <div class="info-card"><h3>Chart findings</h3><div id="findingsList" class="findings-list"><div class="empty-note">No findings entered yet.</div></div></div>
    <div class="info-card">
      <h3>Recorded areas</h3>
      <div id="findingCount" class="stat-big">0</div>
      <div class="stat-small">tooth / surface records</div>
      <p id="storageExplanation" class="stat-small" style="line-height:1.6;margin-top:12px">
        @if(isset($activeChart))
          Connected to database chart #{{ $activeChart->id }}. Click <strong>Save chart</strong> to update database records.
        @else
          Demo data stays in this browser localStorage. Select a patient's chart above or register one to save directly to database.
        @endif
      </p>
    </div>
  </section>
</div>

<div id="findingModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal">
    <div class="modal-head"><div><div class="modal-kicker">Dental finding editor</div><h2 id="modalTitle" class="modal-title">Tooth 23 · Top</h2></div><button type="button" class="close" id="closeModal" aria-label="Close">×</button></div>
    <form id="findingForm" class="modal-body">
      <div class="field-grid">
        <div class="field"><label for="toothNumberField">Tooth number</label><input id="toothNumberField" readonly></div>
        <div class="field"><label for="surfaceSelect">Selected area</label><select id="surfaceSelect"><option value="top">Top</option><option value="bottom">Bottom / under</option><option value="left">Left</option><option value="right">Right</option><option value="center">Center</option><option value="whole">Whole tooth</option></select></div>
      </div>
      <div class="field"><label for="conditionSelect">Condition</label><select id="conditionSelect"></select></div>
      <div class="field"><label for="colorSelect">Marking color</label><div class="color-row"><input id="colorSelect" type="color" value="#d83c43"><span class="color-help">Choose any color for this surface.</span></div></div>
      <div class="field"><label for="statusSelect">Treatment status</label><select id="statusSelect"><option value="observed">Observed</option><option value="planned">Treatment planned</option><option value="in_progress">In progress</option><option value="completed">Completed</option><option value="referred">Referred</option></select></div>
      <div class="field"><label for="noteInput">Note</label><textarea id="noteInput" rows="3" placeholder="Type a note for this tooth surface..."></textarea></div>
      <div class="modal-actions"><button type="button" class="btn btn-danger" id="deleteBtn">Remove marking</button><div class="modal-actions-right"><button type="button" class="btn" id="cancelBtn">Cancel</button><button type="submit" class="btn btn-primary">Save finding</button></div></div>
    </form>
  </div>
</div>
<div id="toast" class="toast" role="status" aria-live="polite"></div>

<script>
window.DENTAL_SERVER = {
  chart: @json($activeChart ?? null),
  conditions: @json($conditions ?? []),
  csrfToken: '{{ csrf_token() }}'
};

(() => {
  'use strict';
  const serverChart = window.DENTAL_SERVER.chart;
  const serverConditions = window.DENTAL_SERVER.conditions;
  const csrfToken = window.DENTAL_SERVER.csrfToken;

  const STORAGE_KEY = serverChart ? `dental-chart-db-${serverChart.id}` : 'dental-chart-paper-demo-v2';
  const permanent = {
    upperRight:[18,17,16,15,14,13,12,11], upperLeft:[21,22,23,24,25,26,27,28],
    lowerRight:[48,47,46,45,44,43,42,41], lowerLeft:[31,32,33,34,35,36,37,38]
  };
  const primary = {
    upperRight:[55,54,53,52,51], upperLeft:[61,62,63,64,65],
    lowerRight:[85,84,83,82,81], lowerLeft:[71,72,73,74,75]
  };
  const surfaces = ['top','left','center','right','bottom'];

  let conditions = (serverConditions && serverConditions.length > 0)
    ? serverConditions.map(c => ({ id: c.id, value: c.code, label: c.name, color: c.default_color }))
    : [
        {value:'caries',label:'Caries / decay',color:'#d83c43'},
        {value:'restoration',label:'Existing restoration',color:'#2f66cf'},
        {value:'planned',label:'Treatment planned',color:'#e59b26'},
        {value:'completed',label:'Completed treatment',color:'#319b62'},
        {value:'missing',label:'Missing tooth',color:'#707782'},
        {value:'crown',label:'Crown',color:'#8b5bc7'},
        {value:'fracture',label:'Fracture',color:'#8a5a38'},
        {value:'sealant',label:'Sealant',color:'#169d9a'}
      ];

  const findings = {};
  let selected = null;
  let toastTimer = null;
  const byId = id => document.getElementById(id);
  const keyFor = (tooth, surface) => `${tooth}-${surface}`;
  const esc = value => String(value ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));

  function toothSvg(number) {
    const polygons = {
      top: '9,7 91,7 70,29 30,29',
      left: '7,9 29,30 29,70 7,91',
      center: '34,34 66,34 66,66 34,66',
      right: '93,9 71,30 71,70 93,91',
      bottom: '9,93 30,71 70,71 91,93'
    };
    return `<svg class="tooth-face" viewBox="0 0 100 100" role="group" aria-label="Tooth ${number} surfaces">${surfaces.map(s=>`<polygon points="${polygons[s]}" data-surface="${s}" tabindex="0" role="button" aria-label="Tooth ${number}, ${s}" data-tooth="${number}" title="${s[0].toUpperCase()+s.slice(1)} surface"></polygon>`).join('')}</svg>`;
  }

  function toothSlot(number, type) {
    if (number === null) return '<div class="tooth-slot empty" aria-hidden="true"></div>';
    return `<div class="tooth-slot"><div class="tooth ${type==='primary'?'primary-tooth':''}" data-tooth="${number}" data-type="${type}">
      <button type="button" class="tooth-number" data-whole-tooth="${number}" aria-label="Edit whole tooth ${number}" title="Edit whole tooth ${number}">${number}</button>
      ${toothSvg(number)}
    </div></div>`;
  }

  function buildSide(numbers, type, padFront=0, padBack=0) {
    const slots = [...Array(padFront).fill(null), ...numbers, ...Array(padBack).fill(null)];
    return `<div class="arch-side">${slots.map(n=>toothSlot(n,type)).join('')}</div>`;
  }

  function renderRow(id, rightNumbers, leftNumbers, type, padRightFront=0, padRightBack=0, padLeftFront=0, padLeftBack=0) {
    const right = buildSide(rightNumbers,type,padRightFront,padRightBack);
    const left = buildSide(leftNumbers,type,padLeftFront,padLeftBack);
    byId(id).innerHTML = `${right}<div class="midline" aria-hidden="true"></div>${left}`;
  }

  function renderChart() {
    renderRow('upperPermanent', permanent.upperRight, permanent.upperLeft, 'permanent');
    renderRow('upperPrimary', primary.upperRight, primary.upperLeft, 'primary', 3, 0, 0, 3);
    renderRow('lowerPrimary', primary.lowerRight, primary.lowerLeft, 'primary', 3, 0, 0, 3);
    renderRow('lowerPermanent', permanent.lowerRight, permanent.lowerLeft, 'permanent');
    document.querySelectorAll('[data-surface]').forEach(el => {
      el.addEventListener('click', () => openModal(el.dataset.tooth, el.dataset.surface));
      el.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openModal(el.dataset.tooth, el.dataset.surface); } });
    });
    document.querySelectorAll('[data-whole-tooth]').forEach(el => el.addEventListener('click', () => openModal(el.dataset.wholeTooth,'whole')));
  }

  function renderLegend() {
    byId('legend').innerHTML = '<span class="legend-title">Color guide</span>' + conditions.map(c=>`<span class="legend-item"><span class="swatch" style="background:${c.color}"></span>${esc(c.label)}</span>`).join('');
    byId('conditionSelect').innerHTML = '<option value="">No condition / note only</option>' + conditions.map(c=>`<option value="${c.value}">${esc(c.label)}</option>`).join('');
  }

  function loadSavedState() {
    if (serverChart) {
      if (serverChart.examination_notes) {
        byId('otherInfo').value = serverChart.examination_notes;
      }
      if (serverChart.teeth && Array.isArray(serverChart.teeth)) {
        serverChart.teeth.forEach(tooth => {
          if (tooth.findings && Array.isArray(tooth.findings)) {
            tooth.findings.forEach(f => {
              const cond = f.condition;
              findings[keyFor(tooth.tooth_number, f.surface)] = {
                tooth: String(tooth.tooth_number),
                surface: f.surface,
                condition_id: f.dental_condition_id,
                condition: cond ? cond.code : '',
                color: f.custom_color || (cond ? cond.default_color : '#d83c43'),
                status: f.treatment_status || 'observed',
                note: f.note || ''
              };
            });
          }
        });
      }
      byId('saveStatus').textContent = `Loaded database chart #${serverChart.id} for ${serverChart.patient?.full_name || 'patient'}`;
      return;
    }

    let saved = null;
    try { saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null'); } catch (e) { saved = null; }
    if (saved && typeof saved === 'object') {
      if (saved.findings && typeof saved.findings === 'object') Object.assign(findings, saved.findings);
      byId('otherInfo').value = typeof saved.otherInfo === 'string' ? saved.otherInfo : '';
      byId('saveStatus').textContent = 'Loaded demo chart from this browser';
    } else {
      findings[keyFor('23','top')] = {tooth:'23',surface:'top',condition:'caries',color:'#d83c43',status:'observed',note:'Example marking.'};
      byId('saveStatus').textContent = 'Example chart loaded · edit a surface to begin';
    }
  }

  async function persist(message='Saved') {
    const list = Object.values(findings).map(f => {
      const condObj = conditions.find(c => c.value === f.condition);
      return {
        tooth: Number(f.tooth),
        surface: f.surface,
        condition_id: condObj?.id || f.condition_id || null,
        condition_code: f.condition || null,
        color: f.color || null,
        status: f.status || 'observed',
        note: f.note || null
      };
    });

    const payload = {
      dentition_type: 'mixed',
      examination_notes: byId('otherInfo').value,
      findings: list
    };

    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify({
        savedAt: new Date().toISOString(),
        otherInfo: byId('otherInfo').value,
        findings
      }));
    } catch(e) {}

    if (serverChart && serverChart.id) {
      byId('saveStatus').textContent = 'Saving to database...';
      try {
        const resp = await fetch(`/api/dental-charts/${serverChart.id}/sync-findings`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify(payload)
        });

        if (!resp.ok) {
          const errData = await resp.json().catch(() => ({}));
          throw new Error(errData.message || 'Server error while saving');
        }

        byId('saveStatus').textContent = `Saved to database · ${new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}`;
        showToast('Chart saved to database successfully');
        return true;
      } catch (err) {
        console.error(err);
        byId('saveStatus').textContent = `Database save error: ${err.message} (cached locally)`;
        showToast('Database save failed (saved locally)');
        return false;
      }
    }

    byId('saveStatus').textContent = `${message} in browser · ${new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}`;
    return true;
  }

  function openModal(tooth, surface) {
    selected = {tooth:String(tooth), surface};
    const finding = findings[keyFor(tooth,surface)] || (surface !== 'whole' ? findings[keyFor(tooth,'whole')] : null) || {};
    byId('modalTitle').textContent = `Tooth ${tooth} · ${surface==='whole'?'Whole tooth':surface[0].toUpperCase()+surface.slice(1)}`;
    byId('toothNumberField').value = tooth;
    byId('surfaceSelect').value = surface;
    byId('conditionSelect').value = finding.condition || '';
    byId('colorSelect').value = validColor(finding.color) ? finding.color : '#d83c43';
    byId('statusSelect').value = finding.status || 'observed';
    byId('noteInput').value = finding.note || '';
    byId('findingModal').classList.add('open');
    setTimeout(() => byId('conditionSelect').focus(), 30);
  }

  function closeModal() {
    byId('findingModal').classList.remove('open');
    selected = null;
  }

  function validColor(color) {
    return typeof color === 'string' && /^#[0-9a-f]{6}$/i.test(color);
  }

  function refreshChart() {
    document.querySelectorAll('.tooth').forEach(toothEl => {
      const n = toothEl.dataset.tooth;
      const whole = findings[keyFor(n,'whole')];
      toothEl.querySelectorAll('[data-surface]').forEach(part => {
        const face = findings[keyFor(n,part.dataset.surface)] || whole;
        part.style.fill = face && validColor(face.color) ? face.color : '#fffefa';
        part.style.stroke = face && face.condition === 'missing' ? '#707782' : '#252525';
        part.setAttribute('aria-label', `Tooth ${n}, ${part.dataset.surface}${face?.condition ? ', '+face.condition : ''}`);
        part.setAttribute('title', `Tooth ${n} · ${part.dataset.surface}${face?.condition ? ' · '+face.condition : ''}${face?.note ? ' · note added' : ''}`);
      });
      const label = toothEl.querySelector('.tooth-number');
      label.style.background = whole?.color && validColor(whole.color) ? whole.color : 'transparent';
      label.style.color = whole?.color && validColor(whole.color) ? '#fff' : '#262626';
    });

    const rows = Object.values(findings).filter(f => f && (f.condition || f.note || f.color));
    byId('findingCount').textContent = rows.length;
    renderFindingsList(rows);
  }

  function renderFindingsList(rows) {
    const list = byId('findingsList');
    if (!rows.length) {
      list.innerHTML = '<div class="empty-note">No findings entered yet. Click a tooth surface to add one.</div>';
      return;
    }
    list.innerHTML = rows.sort((a,b) => Number(a.tooth)-Number(b.tooth) || surfaces.indexOf(a.surface)-surfaces.indexOf(b.surface)).map(f => {
      const condition = conditions.find(c => c.value === f.condition)?.label || 'Note / color only';
      const area = f.surface==='whole' ? 'Whole tooth' : f.surface[0].toUpperCase() + f.surface.slice(1);
      return `<div class="finding-item">
        <span>
          <strong>Tooth ${esc(f.tooth)} · ${esc(area)}</strong>
          <br><span style="color:#7b8493">${esc(condition)}${f.note ? ' · ' + esc(f.note.slice(0,60)) : ''}</span>
        </span>
        <button type="button" data-edit-key="${esc(keyFor(f.tooth,f.surface))}">Edit</button>
      </div>`;
    }).join('');

    list.querySelectorAll('[data-edit-key]').forEach(btn => {
      btn.addEventListener('click', () => {
        const f = findings[btn.dataset.editKey];
        if (f) openModal(f.tooth, f.surface);
      });
    });
  }

  function saveFinding(event) {
    event.preventDefault();
    if (!selected) return;
    const condVal = byId('conditionSelect').value;
    const condObj = conditions.find(c => c.value === condVal);

    const data = {
      tooth: selected.tooth,
      surface: selected.surface,
      condition: condVal,
      condition_id: condObj?.id || null,
      color: byId('colorSelect').value,
      status: byId('statusSelect').value,
      note: byId('noteInput').value.trim()
    };

    if (!data.condition && !data.note && !data.color) {
      delete findings[keyFor(data.tooth, data.surface)];
    } else {
      findings[keyFor(data.tooth, data.surface)] = data;
    }

    refreshChart();
    closeModal();
    showToast('Finding recorded');
    persist('Finding saved');
  }

  function deleteFinding() {
    if (!selected) return;
    delete findings[keyFor(selected.tooth, selected.surface)];
    refreshChart();
    closeModal();
    showToast('Marking removed');
    persist('Marking removed');
  }

  function setFilter(type) {
    document.querySelectorAll('.filter').forEach(btn => btn.classList.toggle('active', btn.dataset.filter === type));
    document.querySelectorAll('.tooth').forEach(t => {
      const show = type === 'all' || t.dataset.type === type;
      t.style.opacity = show ? '1' : '.18';
      t.style.pointerEvents = show ? 'auto' : 'none';
    });
  }

  function exportJson() {
    const data = {
      version: 2,
      exportedAt: new Date().toISOString(),
      chartId: serverChart?.id ?? null,
      patient: serverChart?.patient ?? 'DEMO-0001',
      otherInfo: byId('otherInfo').value,
      findings: Object.values(findings)
    };
    const blob = new Blob([JSON.stringify(data, null, 2)], {type:'application/json'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `dental-chart-${serverChart ? 'patient-'+serverChart.patient_id : 'demo'}-${new Date().toISOString().slice(0,10)}.json`;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
    showToast('JSON backup downloaded');
  }

  function resetChart() {
    if (!window.confirm('Clear chart markings and reset?')) return;
    Object.keys(findings).forEach(k => delete findings[k]);
    byId('otherInfo').value = '';
    refreshChart();
    persist('Chart cleared');
    showToast('Chart reset');
  }

  function showToast(message) {
    const toast = byId('toast');
    toast.textContent = message;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 2400);
  }

  byId('findingForm').addEventListener('submit', saveFinding);
  byId('closeModal').addEventListener('click', closeModal);
  byId('cancelBtn').addEventListener('click', closeModal);
  byId('deleteBtn').addEventListener('click', deleteFinding);
  byId('findingModal').addEventListener('click', e => { if (e.target === byId('findingModal')) closeModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

  byId('conditionSelect').addEventListener('change', e => {
    const matched = conditions.find(c => c.value === e.target.value);
    if (matched && matched.color) {
      byId('colorSelect').value = matched.color;
    }
  });

  byId('surfaceSelect').addEventListener('change', e => {
    if (selected) {
      openModal(selected.tooth, e.target.value);
    }
  });

  byId('saveBtn').addEventListener('click', () => persist('Chart saved'));
  byId('exportBtn').addEventListener('click', exportJson);
  byId('printBtn').addEventListener('click', () => window.print());
  byId('otherInfo').addEventListener('input', () => persist('Notes updated'));
  document.querySelectorAll('.filter').forEach(btn => btn.addEventListener('click', () => setFilter(btn.dataset.filter)));
  byId('resetBtn').addEventListener('click', resetChart);

  renderChart();
  renderLegend();
  loadSavedState();
  refreshChart();
})();
</script>
</body>
</html>
