<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Periode Pendaftaran</h3>
                    <a href="{{ route('admin.periods.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Tambah Periode
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenjang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mulai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selesai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kuota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendaftar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($periods as $period)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $period->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $period->schoolLevel->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $period->start_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $period->end_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $period->max_applicants ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $period->registrations_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($period->is_active)
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-gray-100 text-gray-600">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.periods.edit', $period) }}" class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded hover:bg-yellow-200">Edit</a>
                                            <form action="{{ route('admin.periods.destroy', $period) }}" method="POST" onsubmit="return confirm('Hapus periode ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 text-xs rounded hover:bg-red-200">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Belum ada periode pendaftaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
