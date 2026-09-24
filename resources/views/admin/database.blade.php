<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Database Viewer - RakanKampus</title>
<style>
    :root { --teal: #2dd4bf; --navy: #2a5f59; --blue: #3355a6; --purple: #c084fc; }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: #F3FAF1;
        color: #1f2937;
    }
    .header {
        background: #fff;
        border-bottom: 1px solid #e0e7ff;
        color: #1f2937;
        padding: 18px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .header strong { font-size: 16px; color: #1f2937; }
    .header a.back-link {
        color: #3f7a52;
        background: #f1f5f9;
        border: 1px solid #e0e7ff;
        border-radius: 10px;
        padding: 8px 14px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
    }
    .header a.back-link:hover { background: #e2e8f0; }

    .content { padding: 24px 28px 60px; max-width: 1200px; margin: 0 auto; }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid #e0e7ff;
        border-radius: 14px;
        padding: 14px 16px;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 1px 2px rgba(20, 40, 100, 0.04);
        transition: box-shadow 0.15s ease, transform 0.15s ease;
    }
    .stat-card:hover { box-shadow: 0 6px 16px rgba(20, 40, 100, 0.1); transform: translateY(-1px); }
    .stat-card.active { border-color: var(--teal); box-shadow: 0 0 0 2px var(--teal) inset; }
    .stat-card .stat-num { font-size: 22px; font-weight: 800; color: #2a5f59; }
    .stat-card .stat-name { font-size: 12px; color: #64748b; font-weight: 600; margin-top: 2px; }

    .tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .tabs a {
        padding: 7px 16px; border-radius: 999px; background: white; color: #374151;
        text-decoration: none; border: 1px solid #d1d5db; font-size: 13px; font-weight: 600;
    }
    .tabs a.active { background: var(--teal); color: white; border-color: var(--teal); }

    .table-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        overflow: hidden;
    }
    .table-wrap { overflow-x: auto; }
    table { border-collapse: collapse; width: 100%; }
    th, td { padding: 10px 14px; border-bottom: 1px solid #eef2f7; text-align: left; font-size: 12.5px; white-space: nowrap; max-width: 320px; overflow: hidden; text-overflow: ellipsis; }
    th { background: #f8fafc; color: #475569; font-weight: 700; position: sticky; top: 0; }
    tr:hover td { background: #f9fdfd; }

    .pagination { margin-top: 16px; }
    .empty { padding: 40px 24px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .record-count { color: #64748b; font-size: 13px; margin: 0 0 14px; }

    .flash { border-radius: 12px; padding: 12px 16px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
    .flash.status { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .flash.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    .delete-btn {
        background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;
        border-radius: 8px; padding: 5px 10px; font-size: 11.5px; font-weight: 700; cursor: pointer;
    }
    .delete-btn:hover { background: #fee2e2; }

    .online-banner {
        display: flex; align-items: center; gap: 10px;
        background: #fff; border: 1px solid #e0e7ff; border-radius: 14px;
        padding: 14px 18px; margin-bottom: 16px;
        box-shadow: 0 1px 2px rgba(20, 40, 100, 0.04);
    }
    .online-dot {
        width: 10px; height: 10px; border-radius: 50%; background: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.18);
    }
    .online-banner strong { color: #166534; font-size: 15px; }
    .online-banner span { color: #64748b; font-size: 12.5px; }

    .status-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .status-pill.online { color: #166534; }
    .status-pill .dot { width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1; }
    .status-pill.online .dot { background: #22c55e; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.18); }
</style>
</head>
<body>

<div class="header">
    <strong>RakanKampus &mdash; Database Viewer</strong>
    <a href="{{ route('admin.dashboard') }}" class="back-link">&larr; Back to Dashboard</a>
</div>

<div class="content">

    @if (session('db_viewer_status'))
        <div class="flash status">{{ session('db_viewer_status') }}</div>
    @endif
    @if (session('db_viewer_error'))
        <div class="flash error">{{ session('db_viewer_error') }}</div>
    @endif

    <div class="online-banner">
        <div class="online-dot"></div>
        <strong>{{ $onlineNowCount }} user online now</strong>
        <span>&mdash; active within the last {{ (int) ($onlineWindowSeconds / 60) }} minutes</span>
    </div>

    <div class="stat-grid">
        @foreach ($counts as $t => $count)
            <a href="{{ route('admin.database', ['table' => $t]) }}" class="stat-card {{ $t === $table ? 'active' : '' }}">
                <div class="stat-num">{{ number_format($count) }}</div>
                <div class="stat-name">{{ $t }}</div>
            </a>
        @endforeach
    </div>

    <div class="tabs">
        @foreach ($tables as $t)
            <a href="{{ route('admin.database', ['table' => $t]) }}" class="{{ $t === $table ? 'active' : '' }}">
                {{ $t }}
            </a>
        @endforeach
    </div>

    <p class="record-count">Total records in <strong>{{ $table }}</strong>: {{ number_format($rows->total()) }}</p>

    <div class="table-card">
        @if ($rows->isEmpty())
            <div class="empty">No data in this table.</div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            @foreach ($columns as $col)
                                <th>{{ $col }}</th>
                            @endforeach
                            @if ($table === 'users')
                                <th>Last online</th>
                            @endif
                            @if ($hasId)
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                @foreach ($columns as $col)
                                    <td title="{{ $row->$col }}">{{ \Illuminate\Support\Str::limit((string) $row->$col, 60) }}</td>
                                @endforeach
                                @if ($table === 'users')
                                    <td>
                                        @php
                                            $lastTs = $lastActiveMap[$row->id] ?? null;
                                            $isOnline = $lastTs && (time() - $lastTs) <= $onlineWindowSeconds;
                                        @endphp
                                        @if ($lastTs)
                                            <span class="status-pill {{ $isOnline ? 'online' : '' }}">
                                                <span class="dot"></span>
                                                {{ $isOnline ? 'Online now' : \Carbon\Carbon::createFromTimestamp($lastTs)->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="status-pill">
                                                <span class="dot"></span>
                                                No active session
                                            </span>
                                        @endif
                                    </td>
                                @endif
                                @if ($hasId)
                                    <td>
                                        <form method="POST" action="{{ route('admin.database.destroy', ['table' => $table, 'id' => $row->id]) }}"
                                              onsubmit="return confirm('Delete record #{{ $row->id }} in {{ $table }}? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="pagination">
        {{ $rows->appends(['table' => $table])->links() }}
    </div>

</div>

</body>
</html>
