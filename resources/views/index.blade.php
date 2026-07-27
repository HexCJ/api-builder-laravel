<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel API Builder</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            --bg: #f4f6f9;
            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --line: #dfe5ee;
            --line-strong: #c6d0dd;
            --text: #17202e;
            --muted: #667085;
            --primary: #2563eb;
            --primary-strong: #1d4ed8;
            --primary-soft: #eff6ff;
            --success: #087443;
            --success-soft: #ecfdf3;
            --danger: #b42318;
            --danger-soft: #fff1f3;
            --warning-soft: #fffaeb;
            --shadow: 0 18px 55px rgba(15, 23, 42, .08);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, .10), transparent 34rem),
                linear-gradient(180deg, #fbfcff 0%, var(--bg) 42%);
            color: var(--text);
        }
        a { color: var(--primary); font-weight: 650; text-decoration: none; }
        a:hover { color: var(--primary-strong); }
        header {
            position: sticky;
            top: 0;
            z-index: 5;
            background: rgba(255, 255, 255, .86);
            border-bottom: 1px solid rgba(198, 208, 221, .7);
            backdrop-filter: blur(16px);
        }
        .topbar {
            max-width: 1280px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }
        .brand { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .brand-mark {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #111827;
            color: #fff;
            font-weight: 800;
            letter-spacing: .02em;
            box-shadow: 0 10px 24px rgba(17, 24, 39, .18);
        }
        h1 { font-size: 19px; line-height: 1.15; margin: 0; font-weight: 750; letter-spacing: 0; }
        .subtitle { margin: 3px 0 0; color: var(--muted); font-size: 13px; }
        .top-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        main {
            max-width: 1280px;
            margin: 0 auto;
            padding: 24px;
            display: grid;
            grid-template-columns: minmax(390px, 500px) minmax(0, 1fr);
            gap: 22px;
            align-items: start;
        }
        section {
            background: rgba(255, 255, 255, .96);
            border: 1px solid rgba(198, 208, 221, .86);
            border-radius: 8px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .panel-title {
            padding: 16px 18px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-weight: 750;
        }
        .panel-title span { color: var(--muted); font-size: 12px; font-weight: 650; }
        form { padding: 18px; display: grid; gap: 14px; }
        label { display: grid; gap: 7px; font-size: 13px; font-weight: 700; color: #344054; }
        input, select, textarea {
            width: 100%;
            border: 1px solid var(--line-strong);
            border-radius: 7px;
            padding: 10px 11px;
            font: inherit;
            background: #fff;
            color: var(--text);
            outline: none;
            transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
        }
        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }
        select {
            appearance: none;
            background-image:
                linear-gradient(45deg, transparent 50%, #667085 50%),
                linear-gradient(135deg, #667085 50%, transparent 50%);
            background-position:
                calc(100% - 17px) calc(50% - 2px),
                calc(100% - 12px) calc(50% - 2px);
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
            padding-right: 34px;
        }
        textarea {
            min-height: 128px;
            resize: vertical;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12px;
            line-height: 1.55;
            background: #0f172a;
            border-color: #1e293b;
            color: #dbeafe;
        }
        textarea::placeholder { color: #94a3b8; opacity: 1; }
        button, .button-link {
            min-height: 38px;
            border: 1px solid var(--primary);
            background: var(--primary);
            color: #fff;
            border-radius: 7px;
            padding: 9px 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font: inherit;
            font-weight: 750;
            cursor: pointer;
            transition: transform .14s ease, box-shadow .14s ease, background .14s ease, border-color .14s ease;
        }
        button:hover, .button-link:hover {
            background: var(--primary-strong);
            border-color: var(--primary-strong);
            color: #fff;
            box-shadow: 0 10px 22px rgba(37, 99, 235, .22);
        }
        button:active, .button-link:active { transform: translateY(1px); }
        button.secondary, .button-link.secondary {
            background: #fff;
            color: #273449;
            border-color: var(--line-strong);
        }
        button.secondary:hover, .button-link.secondary:hover {
            background: var(--surface-soft);
            color: var(--text);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
        }
        button.danger {
            min-height: 32px;
            background: var(--danger-soft);
            border-color: #fecdd6;
            color: var(--danger);
            padding: 7px 10px;
        }
        button.danger:hover {
            background: #fee4e2;
            border-color: #fda29b;
            color: var(--danger);
            box-shadow: none;
        }
        .row { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px; }
        .checks {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }
        .checks label {
            min-height: 42px;
            display: flex;
            grid-template-columns: none;
            align-items: center;
            gap: 9px;
            padding: 9px 10px;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: var(--surface-soft);
            font-weight: 700;
            min-width: 0;
        }
        input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: var(--primary);
            flex: 0 0 auto;
        }
        .columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
            max-height: 212px;
            overflow: auto;
            border: 1px solid var(--line);
            padding: 10px;
            border-radius: 7px;
            background: var(--surface-soft);
        }
        .columns label {
            min-height: 36px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 9px;
            border: 1px solid #e8edf4;
            border-radius: 7px;
            background: #fff;
            font-weight: 650;
            min-width: 0;
        }
        .columns span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .toolbar { display: flex; gap: 9px; flex-wrap: wrap; justify-content: flex-end; }
        .builder-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface-soft);
            overflow: hidden;
        }
        .builder-head {
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-bottom: 1px solid var(--line);
            font-size: 13px;
            font-weight: 800;
            color: #344054;
        }
        .builder-head button { min-height: 32px; padding: 6px 10px; }
        .builder-body { padding: 12px; display: grid; gap: 10px; }
        .builder-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
            gap: 8px;
            align-items: end;
        }
        .builder-row.where-row { grid-template-columns: minmax(0, 1fr) 130px minmax(0, 1fr) 92px auto; }
        .builder-row.order-row { grid-template-columns: minmax(0, 1fr) 130px auto; }
        .builder-row.group-row { grid-template-columns: minmax(0, 1fr) auto; }
        .builder-row label { gap: 5px; font-size: 12px; }
        .mini-button {
            min-height: 36px;
            width: 36px;
            padding: 0;
            background: #fff;
            border-color: var(--line-strong);
            color: var(--danger);
            box-shadow: none;
        }
        .mini-button:hover {
            background: var(--danger-soft);
            border-color: #fecdd6;
            color: var(--danger);
            box-shadow: none;
        }
        .advanced-grid { display: grid; gap: 12px; }
        .hint { color: var(--muted); font-size: 12px; line-height: 1.45; margin: -3px 0 0; }
        .switch-card {
            min-height: 48px;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: var(--surface-soft);
            padding: 9px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .switch-card label {
            display: flex;
            grid-template-columns: none;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .switch-card input { width: 18px; height: 18px; }
        .switch-title { font-weight: 800; color: #344054; font-size: 13px; }
        .switch-note { color: var(--muted); font-size: 12px; margin-top: 1px; }
        .table-wrap { width: 100%; overflow-x: auto; }
        table { width: 100%; min-width: 660px; border-collapse: collapse; }
        th, td {
            text-align: left;
            padding: 13px 14px;
            border-bottom: 1px solid var(--line);
            font-size: 13px;
            vertical-align: middle;
        }
        th {
            color: var(--muted);
            font-weight: 800;
            background: var(--surface-soft);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .04em;
        }
        tbody tr:hover { background: #fbfdff; }
        code {
            display: inline-flex;
            max-width: 390px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            background: #eef4ff;
            color: #1e3a8a;
            border: 1px solid #dbeafe;
            border-radius: 6px;
            padding: 4px 7px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            border-radius: 6px;
            padding: 3px 8px;
            border: 1px solid var(--line);
            background: #fff;
            color: #344054;
            font-size: 12px;
            font-weight: 750;
            white-space: nowrap;
        }
        .badge.active { background: var(--success-soft); border-color: #abefc6; color: var(--success); }
        .badge.inactive { background: var(--warning-soft); border-color: #fedf89; color: #93370d; }
        .badge.auth { background: var(--primary-soft); border-color: #bfdbfe; color: #1d4ed8; }
        .state { display: flex; gap: 6px; flex-wrap: wrap; }
        .actions { display: flex; gap: 8px; align-items: center; justify-content: flex-end; }
        .status, .error {
            margin: 0;
            padding: 11px 12px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 650;
        }
        .status { background: var(--success-soft); border: 1px solid #abefc6; color: var(--success); }
        .error { background: var(--danger-soft); border: 1px solid #fecdd6; color: var(--danger); }
        .muted { color: var(--muted); }
        .empty-row { text-align: center; padding: 38px 14px; }
        .pagination { padding: 14px; }
        .delete-form { padding: 0; display: block; }
        @media (max-width: 980px) {
            main { grid-template-columns: 1fr; padding: 16px; }
            .topbar { padding: 14px 16px; }
        }
        @media (max-width: 640px) {
            .topbar { align-items: flex-start; flex-direction: column; }
            .top-actions { width: 100%; }
            .button-link { width: 100%; }
            .row, .checks, .columns, .builder-row, .builder-row.where-row, .builder-row.order-row, .builder-row.group-row { grid-template-columns: 1fr; }
            .mini-button { width: 100%; }
            main { padding: 12px; }
            form { padding: 14px; }
            .panel-title { padding: 14px; }
            code { max-width: 260px; }
        }
    </style>
</head>
<body>
<header>
    <div class="topbar">
        <div class="brand">
            <div class="brand-mark">API</div>
            <div>
                <h1>Laravel API Builder</h1>
                <p class="subtitle">Dynamic endpoint management</p>
            </div>
        </div>
        <div class="top-actions">
            <a class="button-link secondary" href="{{ url(config('api-builder.route_prefix', 'api').'/builder/swagger.json') }}">OpenAPI</a>
        </div>
    </div>
</header>
<main>
    <section>
        <div class="panel-title">
            Create Endpoint
            <span>{{ count($tables) }} tables</span>
        </div>
        <form method="post" action="{{ route('api-builder.store') }}" id="endpoint-form">
            @csrf
            @if (session('status'))
                <p class="status">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <p class="error">{{ $errors->first() }}</p>
            @endif

            <label>Name
                <input name="name" required value="{{ old('name') }}" placeholder="Users list">
            </label>

            <div class="row">
                <label>Path
                    <input name="path" required value="{{ old('path') }}" placeholder="users">
                </label>
                <label>Method
                    <select name="method">
                        @foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method)
                            <option value="{{ $method }}">{{ $method }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <label>Table
                <select name="table_name" id="table-select" required>
                    <option value="">Select table</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table }}">{{ $table }}</option>
                    @endforeach
                </select>
            </label>

            <label>Columns
                <div class="columns" id="column-list">
                    <span class="muted">Select a table</span>
                </div>
            </label>

            <div class="builder-card">
                <div class="builder-head">
                    Joins
                    <button type="button" class="secondary" id="add-join">Add Join</button>
                </div>
                <div class="builder-body" id="join-builder">
                    <p class="hint">Select a base table first, then add joins using table and column dropdowns.</p>
                </div>
            </div>

            <div class="builder-card">
                <div class="builder-head">
                    Where
                    <button type="button" class="secondary" id="add-where">Add Filter</button>
                </div>
                <div class="builder-body" id="where-builder">
                    <p class="hint">Filters use selectable columns from the base table and joined tables.</p>
                </div>
            </div>

            <div class="row">
                <div class="builder-card">
                    <div class="builder-head">
                        Group By
                        <button type="button" class="secondary" id="add-group">Add</button>
                    </div>
                    <div class="builder-body" id="group-builder">
                        <p class="hint">Group using selected table columns.</p>
                    </div>
                </div>

                <div class="builder-card">
                    <div class="builder-head">
                        Order By
                        <button type="button" class="secondary" id="add-order">Add</button>
                    </div>
                    <div class="builder-body" id="order-builder">
                        <p class="hint">Sort using one or more columns.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <label>Pagination
                    <select id="pagination-type">
                        <option value="">None</option>
                        <option value="paginate">paginate()</option>
                        <option value="simplePaginate">simplePaginate()</option>
                        <option value="cursorPaginate">cursorPaginate()</option>
                    </select>
                </label>
                <label>Per Page
                    <input id="per-page" type="number" min="1" max="{{ config('api-builder.security.max_limit', 500) }}" value="50">
                </label>
            </div>

            <div class="checks">
                <label><input id="distinct" type="checkbox"> DISTINCT</label>
                <label><input name="auth_required" type="checkbox" value="1"> Auth Required</label>
                <div class="switch-card">
                    <label>
                        <input type="hidden" name="active" value="0">
                        <input name="active" type="checkbox" value="1" checked>
                        <span>
                            <span class="switch-title">API Enabled</span>
                            <span class="switch-note">Turn off to disable this endpoint.</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="advanced-grid">
                <label>Raw SQL Expression
                    <textarea id="raw-expression" placeholder="Example: status = 'active'"></textarea>
                </label>
                <p class="hint">This maps to a RAW where expression and only works when raw expressions are enabled in config.</p>

                <label>Advanced Configuration JSON
                    <textarea name="configuration" id="configuration-json">{{ old('configuration') }}</textarea>
                </label>
            </div>

            <div class="toolbar">
                <button type="button" class="secondary" id="sync-config">Sync Configuration</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </section>

    <section>
        <div class="panel-title">
            Endpoints
            <span>{{ $endpoints->total() }} total</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Route</th>
                    <th>Table</th>
                    <th>State</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($endpoints as $endpoint)
                    <tr>
                        <td><strong>{{ $endpoint->name }}</strong></td>
                        <td><code>{{ $endpoint->method }} /{{ trim(config('api-builder.route_prefix', 'api'), '/') }}/{{ $endpoint->path }}</code></td>
                        <td><span class="badge">{{ $endpoint->table_name }}</span></td>
                        <td>
                            <div class="state">
                                <span class="badge {{ $endpoint->active ? 'active' : 'inactive' }}">{{ $endpoint->active ? 'Active' : 'Inactive' }}</span>
                                @if ($endpoint->auth_required)
                                    <span class="badge auth">Auth</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="actions">
                                <form class="delete-form" method="post" action="{{ route('api-builder.update', $endpoint) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="name" value="{{ $endpoint->name }}">
                                    <input type="hidden" name="path" value="{{ $endpoint->path }}">
                                    <input type="hidden" name="method" value="{{ $endpoint->method }}">
                                    <input type="hidden" name="table_name" value="{{ $endpoint->table_name }}">
                                    <input type="hidden" name="description" value="{{ $endpoint->description }}">
                                    <input type="hidden" name="auth_required" value="{{ $endpoint->auth_required ? 1 : 0 }}">
                                    <input type="hidden" name="active" value="{{ $endpoint->active ? 0 : 1 }}">
                                    <input type="hidden" name="configuration" value="{{ json_encode($endpoint->configuration) }}">
                                    <button class="secondary" type="submit">{{ $endpoint->active ? 'Disable' : 'Enable' }}</button>
                                </form>
                                <form class="delete-form" method="post" action="{{ route('api-builder.destroy', $endpoint) }}" onsubmit="return confirm('Delete endpoint?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted empty-row">No endpoints saved</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $endpoints->links() }}</div>
    </section>
</main>

<script>
const tables = @json($tables);
const configInput = document.getElementById('configuration-json');
const columnList = document.getElementById('column-list');
const tableSelect = document.getElementById('table-select');
const joinBuilder = document.getElementById('join-builder');
const whereBuilder = document.getElementById('where-builder');
const groupBuilder = document.getElementById('group-builder');
const orderBuilder = document.getElementById('order-builder');
const rawExpression = document.getElementById('raw-expression');
const metadataCache = {};
let baseTable = '';
let baseColumns = [];

const tableOptions = (selected = '') => [
    '<option value="">Table</option>',
    ...tables.map((table) => `<option value="${table}" ${table === selected ? 'selected' : ''}>${table}</option>`)
].join('');

const columnOptions = (columns, selected = '', placeholder = 'Column') => [
    `<option value="">${placeholder}</option>`,
    ...columns.map((column) => `<option value="${column.value}" ${column.value === selected ? 'selected' : ''}>${column.label}</option>`)
].join('');

async function fetchMetadata(table) {
    if (!table) return { columns: [] };
    if (metadataCache[table]) return metadataCache[table];

    const response = await fetch(`{{ url(config('api-builder.route_prefix', 'api').'/builder/table') }}/${table}`);
    metadataCache[table] = await response.json();

    return metadataCache[table];
}

function normalizeColumns(table, columns) {
    return columns.map((column) => ({
        name: column.name,
        value: table ? `${table}.${column.name}` : column.name,
        label: table ? `${table}.${column.name}` : column.name,
        type: column.type_name || column.type || ''
    }));
}

function currentSelectableColumns() {
    const joined = Array.from(document.querySelectorAll('.join-table'))
        .map((select) => select.value)
        .filter(Boolean)
        .flatMap((table) => normalizeColumns(table, metadataCache[table]?.columns || []));

    return [
        ...baseColumns.map((column) => ({
            name: column.name,
            value: column.name,
            label: column.name,
            type: column.type,
        })),
        ...joined,
    ];
}

function clearBuilder(container, message) {
    container.innerHTML = `<p class="hint">${message}</p>`;
}

function ensureBuilderReady(container) {
    const hint = container.querySelector('.hint');
    if (hint) hint.remove();
}

tableSelect.addEventListener('change', async (event) => {
    const table = event.target.value;
    baseTable = table;
    baseColumns = [];
    columnList.innerHTML = '<span class="muted">Loading</span>';
    if (!table) {
        columnList.innerHTML = '<span class="muted">Select a table</span>';
        clearBuilder(joinBuilder, 'Select a base table first, then add joins using table and column dropdowns.');
        clearBuilder(whereBuilder, 'Filters use selectable columns from the base table and joined tables.');
        clearBuilder(groupBuilder, 'Group using selected table columns.');
        clearBuilder(orderBuilder, 'Sort using one or more columns.');
        syncConfiguration();
        return;
    }
    const metadata = await fetchMetadata(table);
    baseColumns = normalizeColumns('', metadata.columns);
    columnList.innerHTML = metadata.columns.map((column) => `
        <label title="${column.type_name || column.type || ''}">
            <input type="checkbox" class="column-option" value="${column.name}" checked>
            <span>${column.name}</span>
        </label>
    `).join('');
    updateColumnSelects();
    syncConfiguration();
});

document.getElementById('add-join').addEventListener('click', () => addJoinRow());
document.getElementById('add-where').addEventListener('click', () => addWhereRow());
document.getElementById('add-group').addEventListener('click', () => addGroupRow());
document.getElementById('add-order').addEventListener('click', () => addOrderRow());

function addJoinRow() {
    ensureBuilderReady(joinBuilder);
    const row = document.createElement('div');
    row.className = 'builder-row join-row';
    row.innerHTML = `
        <label>Type
            <select class="join-type">
                <option value="inner">INNER</option>
                <option value="left">LEFT</option>
                <option value="right">RIGHT</option>
                <option value="cross">CROSS</option>
            </select>
        </label>
        <label>Join Table
            <select class="join-table">${tableOptions()}</select>
        </label>
        <label>Base Column
            <select class="join-first">${columnOptions(baseColumns, '', 'Base column')}</select>
        </label>
        <label>Join Column
            <select class="join-second"><option value="">Join column</option></select>
        </label>
        <button type="button" class="mini-button remove-row">x</button>
    `;
    joinBuilder.appendChild(row);
    row.querySelector('.join-table').addEventListener('change', async (event) => {
        const table = event.target.value;
        const metadata = await fetchMetadata(table);
        row.querySelector('.join-second').innerHTML = columnOptions(normalizeColumns(table, metadata.columns), '', 'Join column');
        updateColumnSelects();
        syncConfiguration();
    });
    row.querySelector('.remove-row').addEventListener('click', () => {
        row.remove();
        updateColumnSelects();
        syncConfiguration();
    });
    row.addEventListener('change', syncConfiguration);
}

function addWhereRow() {
    ensureBuilderReady(whereBuilder);
    const row = document.createElement('div');
    row.className = 'builder-row where-row';
    row.innerHTML = `
        <label>Column
            <select class="where-column">${columnOptions(currentSelectableColumns())}</select>
        </label>
        <label>Operator
            <select class="where-operator">
                ${['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE', 'BETWEEN', 'NOT BETWEEN', 'NULL', 'NOT NULL', 'IN', 'NOT IN'].map((operator) => `<option value="${operator}">${operator}</option>`).join('')}
            </select>
        </label>
        <label>Value
            <input class="where-value" placeholder="value">
        </label>
        <label>Boolean
            <select class="where-boolean">
                <option value="and">AND</option>
                <option value="or">OR</option>
            </select>
        </label>
        <button type="button" class="mini-button remove-row">x</button>
    `;
    whereBuilder.appendChild(row);
    row.querySelector('.remove-row').addEventListener('click', () => {
        row.remove();
        syncConfiguration();
    });
    row.addEventListener('input', syncConfiguration);
    row.addEventListener('change', syncConfiguration);
}

function addGroupRow() {
    ensureBuilderReady(groupBuilder);
    const row = document.createElement('div');
    row.className = 'builder-row group-row';
    row.innerHTML = `
        <label>Column
            <select class="group-column">${columnOptions(currentSelectableColumns())}</select>
        </label>
        <button type="button" class="mini-button remove-row">x</button>
    `;
    groupBuilder.appendChild(row);
    row.querySelector('.remove-row').addEventListener('click', () => {
        row.remove();
        syncConfiguration();
    });
    row.addEventListener('change', syncConfiguration);
}

function addOrderRow() {
    ensureBuilderReady(orderBuilder);
    const row = document.createElement('div');
    row.className = 'builder-row order-row';
    row.innerHTML = `
        <label>Column
            <select class="order-column">${columnOptions(currentSelectableColumns())}</select>
        </label>
        <label>Direction
            <select class="order-direction">
                <option value="asc">ASC</option>
                <option value="desc">DESC</option>
            </select>
        </label>
        <button type="button" class="mini-button remove-row">x</button>
    `;
    orderBuilder.appendChild(row);
    row.querySelector('.remove-row').addEventListener('click', () => {
        row.remove();
        syncConfiguration();
    });
    row.addEventListener('change', syncConfiguration);
}

function updateColumnSelects() {
    const columns = currentSelectableColumns();
    document.querySelectorAll('.where-column, .group-column, .order-column').forEach((select) => {
        const selected = select.value;
        select.innerHTML = columnOptions(columns, selected);
    });
}

function parseValue(value, operator) {
    if (['NULL', 'NOT NULL'].includes(operator)) return null;
    if (['IN', 'NOT IN'].includes(operator)) {
        return value.split(',').map((item) => item.trim()).filter(Boolean);
    }
    if (['BETWEEN', 'NOT BETWEEN'].includes(operator)) {
        return value.split(',').map((item) => item.trim()).filter(Boolean).slice(0, 2);
    }
    if (value === 'true') return true;
    if (value === 'false') return false;
    if (value !== '' && !Number.isNaN(Number(value))) return Number(value);
    return value;
}

function collectJoins() {
    return Array.from(document.querySelectorAll('.join-row')).map((row) => {
        const type = row.querySelector('.join-type').value;
        const table = row.querySelector('.join-table').value;
        if (!table) return null;
        if (type === 'cross') return { type, table };

        return {
            type,
            table,
            first: row.querySelector('.join-first').value,
            operator: '=',
            second: row.querySelector('.join-second').value,
        };
    }).filter((join) => join && (join.type === 'cross' || (join.first && join.second)));
}

function collectWhere() {
    const conditions = Array.from(document.querySelectorAll('.where-row')).map((row) => {
        const column = row.querySelector('.where-column').value;
        const operator = row.querySelector('.where-operator').value;
        if (!column) return null;

        return {
            column,
            operator,
            value: parseValue(row.querySelector('.where-value').value.trim(), operator),
            boolean: row.querySelector('.where-boolean').value,
        };
    }).filter(Boolean);

    if (rawExpression.value.trim()) {
        conditions.push({
            operator: 'RAW',
            expression: rawExpression.value.trim(),
            boolean: conditions.length ? 'and' : 'and',
        });
    }

    return conditions;
}

function collectGroupBy() {
    return Array.from(document.querySelectorAll('.group-column')).map((select) => select.value).filter(Boolean);
}

function collectOrderBy() {
    return Array.from(document.querySelectorAll('.order-row')).map((row) => {
        const column = row.querySelector('.order-column').value;
        if (!column) return null;

        return {
            column,
            direction: row.querySelector('.order-direction').value,
        };
    }).filter(Boolean);
}

function syncConfiguration() {
    const columns = Array.from(document.querySelectorAll('.column-option:checked')).map((input) => input.value);
    const paginationType = document.getElementById('pagination-type').value;
    const config = {
        columns,
        joins: collectJoins(),
        where: collectWhere(),
        with: [],
        aggregations: [],
        group_by: collectGroupBy(),
        having: [],
        order_by: collectOrderBy(),
        computed: [],
        aliases: [],
        window: [],
        pagination: paginationType ? { enabled: true, type: paginationType, per_page: Number(document.getElementById('per-page').value || 50) } : {},
        distinct: document.getElementById('distinct').checked,
        limit: null,
        offset: null
    };
    configInput.value = JSON.stringify(config, null, 2);
}

document.getElementById('sync-config').addEventListener('click', syncConfiguration);
document.getElementById('endpoint-form').addEventListener('submit', syncConfiguration);
columnList.addEventListener('change', syncConfiguration);
rawExpression.addEventListener('input', syncConfiguration);
</script>
</body>
</html>
