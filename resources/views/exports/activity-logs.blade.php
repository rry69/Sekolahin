<table>
    <thead>
        <tr>
            <th>Waktu</th>
            <th>Aksi</th>
            <th>Label</th>
            <th>Kategori</th>
            <th>Deskripsi</th>
            <th>User</th>
            <th>Email</th>
            <th>IP</th>
            <th>Properties</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
            <tr>
                <td>{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '' }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->label() }}</td>
                <td>{{ $log->category() }}</td>
                <td>{{ $log->description ?? '' }}</td>
                <td>{{ $log->userName() }}</td>
                <td>{{ $log->user?->email ?? '' }}</td>
                <td>{{ $log->ip_address ?? '' }}</td>
                <td>{{ $log->properties ? json_encode($log->properties, JSON_UNESCAPED_UNICODE) : '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
