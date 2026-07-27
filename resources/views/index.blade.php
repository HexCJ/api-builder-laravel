<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel API Builder</title>
    <style>
        :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f7f8fa; color: #20242a; }
        header { background: #ffffff; border-bottom: 1px solid #d9dee7; padding: 18px 28px; display: flex; justify-content: space-between; align-items: center; }
        h1 { font-size: 20px; margin: 0; font-weight: 650; }
        main { max-width: 1180px; margin: 0 auto; padding: 24px; display: grid; grid-template-columns: minmax(360px, 430px) 1fr; gap: 24px; }
        section { background: #ffffff; border: 1px solid #d9dee7; border-radius: 8px; }
        .panel-title { padding: 16px 18px; border-bottom: 1px solid #e4e8ef; font-weight: 650; }
        form { padding: 18px; display: grid; gap: 14px; }
        label { display: grid; gap: 6px; font-size: 13px; font-weight: 600; color: #3e4652; }
        input, select, textarea { width: 100%; border: 1px solid #cbd3df; border-radius: 6px; padding: 9px 10px; font: inherit; background: #fff; color: #1f2933; }
        textarea { min-height: 150px; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 12px; }
        button { border: 1px solid #1f6feb; background: #1f6feb; color: #fff; border-radius: 6px; padding: 9px 12px; font-weight: 650; cursor: pointer; }
        button.secondary { background: #fff; color: #1f2933; border-color: #cbd3df; }
        button.danger { background: #b42318; border-color: #b42318; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .checks { display: flex; gap: 18px; align-items: center; }
        .checks label { display: flex; grid-template-columns: none; align-items: center; gap: 8px; }
        .checks input { width: auto; }
        .columns { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; max-height: 190px; overflow: auto; border: 1px solid #e4e8ef; padding: 10px; border-radius: 6px; }
        .columns label { display: flex; align-items: center; gap: 8px; font-weight: 500; min-width: 0; }
        .columns input { width: auto; }
        .toolbar { display: flex; gap: 8px; flex-wrap: wrap; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 11px 12px; border-bottom: 1px solid #e4e8ef; font-size: 13px; vertical-align: top; }
        th { color: #57606f; font-weight: 650; background: #fbfcfe; }
        code { background: #eef2f7; border-radius: 4px; padding: 2px 5px; }
        .status { margin: 0 0 14px; background: #ecfdf3; border: 1px solid #abefc6; color: #067647; padding: 10px 12px; border-radius: 6px; }
        .error { margin: 0 0 14px; background: #fff1f3; border: 1px solid #fecdd6; color: #b42318; padding: 10px 12px; border-radius: 6px; }
        .muted { color: #697386; }
        @media (max-width: 860px) { main { grid-template-columns: 1fr; padding: 14px; } header { padding: 14px; } }
    </style>
</head>
<body>
<header>
    <h1>Laravel API Builder</h1>
    <a href="{{ url(config('api-builder.route_prefix', 'api').'/builder/swagger.json') }}">OpenAPI</a>
</header>
<main>
    <section>
        <div class="panel-title">Create Endpoint</div>
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

            <div class="row">
                <label>Join JSON
                    <textarea id="joins-json" placeholder='[{"type":"left","table":"roles","first":"users.role_id","operator":"=","second":"roles.id"}]'></textarea>
                </label>
                <label>Where JSON
                    <textarea id="where-json" placeholder='[{"column":"active","operator":"=","value":1}]'></textarea>
                </label>
            </div>

            <div class="row">
                <label>Group By JSON
                    <textarea id="group-json" placeholder='["role_id"]'></textarea>
                </label>
                <label>Order JSON
                    <textarea id="order-json" placeholder='[{"column":"created_at","direction":"desc"}]'></textarea>
                </label>
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
                <label><input name="active" type="checkbox" value="1" checked> Active</label>
            </div>

            <label>Configuration JSON
                <textarea name="configuration" id="configuration-json">{{ old('configuration') }}</textarea>
            </label>

            <div class="toolbar">
                <button type="button" class="secondary" id="sync-config">Sync Configuration</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </section>

    <section>
        <div class="panel-title">Endpoints</div>
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
                    <td>{{ $endpoint->name }}</td>
                    <td><code>{{ $endpoint->method }} /{{ trim(config('api-builder.route_prefix', 'api'), '/') }}/{{ $endpoint->path }}</code></td>
                    <td>{{ $endpoint->table_name }}</td>
                    <td>{{ $endpoint->active ? 'Active' : 'Inactive' }}{{ $endpoint->auth_required ? ' / Auth' : '' }}</td>
                    <td>
                        <form method="post" action="{{ route('api-builder.destroy', $endpoint) }}" onsubmit="return confirm('Delete endpoint?')">
                            @csrf
                            @method('DELETE')
                            <button class="danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">No endpoints saved</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="padding: 12px;">{{ $endpoints->links() }}</div>
    </section>
</main>

<script>
const configInput = document.getElementById('configuration-json');
const columnList = document.getElementById('column-list');
const parseJson = (id, fallback) => {
    const value = document.getElementById(id).value.trim();
    if (!value) return fallback;
    return JSON.parse(value);
};

document.getElementById('table-select').addEventListener('change', async (event) => {
    const table = event.target.value;
    columnList.innerHTML = '<span class="muted">Loading</span>';
    if (!table) {
        columnList.innerHTML = '<span class="muted">Select a table</span>';
        return;
    }
    const response = await fetch(`{{ url(config('api-builder.route_prefix', 'api').'/builder/table') }}/${table}`);
    const metadata = await response.json();
    columnList.innerHTML = metadata.columns.map((column) => `
        <label title="${column.type_name || column.type || ''}">
            <input type="checkbox" class="column-option" value="${column.name}" checked>
            <span>${column.name}</span>
        </label>
    `).join('');
    syncConfiguration();
});

function syncConfiguration() {
    const columns = Array.from(document.querySelectorAll('.column-option:checked')).map((input) => input.value);
    const paginationType = document.getElementById('pagination-type').value;
    const config = {
        columns,
        joins: parseJson('joins-json', []),
        where: parseJson('where-json', []),
        with: [],
        aggregations: [],
        group_by: parseJson('group-json', []),
        having: [],
        order_by: parseJson('order-json', []),
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
</script>
</body>
</html>
