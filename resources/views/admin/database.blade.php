<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Database Viewer - RakanKampus</title>
<style>
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f3f4f6; color: #1f2937; }
    .header { background: #1f2937; color: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
    .header a { color: #a5f3fc; text-decoration: none; }
    .content { padding: 24px; }
    .tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .tabs a {
        padding: 6px 14px; border-radius: 999px; background: white; color: #374151;
        text-decoration: none; border: 1px solid #d1d5db; font-size: 14px;
    }
    .tabs a.active { background: #2dd4bf; color: white; border-color: #2dd4bf; }
    table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
    th, td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; font-size: 13px; white-space: nowrap; max-width: 320px; overflow: hidden; text-overflow: ellipsis; }
    th { background: #f9fafb; position: sticky; top: 0; }
    .table-wrap { overflow-x: auto; }
    .pagination { margin-top: 16px; }
    .pagination a, .pagination span { margin-right: 8px; }
    .empty { padding: 24px; text-align: center; color: #6b7280; }
</style>
</head>
<body>

<div class="header">
    <strong>RakanKampus — Database Viewer (read-only)</strong>
    <a href="{{ route('admin.dashboard') }}">&larr; Balik ke Dashboard</a>
</div>

<div class="content">
    <div class="tabs">
        @foreach ($tables as $t)
            <a href="{{ route('admin.database', ['table' => $t]) }}" class="{{ $t === $table ? 'active' : '' }}">
                {{ $t }}
            </a>
        @endforeach
    </div>

    <p>Jumlah rekod: {{ $rows->total() }}</p>

    @if ($rows->isEmpty())
        <div class="empty">Tiada data dalam table ini.</div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        @foreach ($columns as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            @foreach ($columns as $col)
                                <td title="{{ $row->$col }}">{{ \Illuminate\Support\Str::limit((string) $row->$col, 60) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $rows->appends(['table' => $table])->links() }}
        </div>
    @endif
</div>

</body>
</html>
