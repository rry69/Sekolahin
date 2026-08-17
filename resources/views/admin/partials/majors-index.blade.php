<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Daftar Jurusan</h3>
            <a href="{{ route('admin.majors.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Tambah Jurusan
            </a>
        </div>

        @php $trackCount = $tracks->count(); @endphp
        @forelse ($levels as $level)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-bold text-gray-900">Jenjang {{ $level->name }}
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                {{ $grouped->get($level->id)?->count() ?? 0 }} jurusan
                            </span>
                        </h4>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sekolah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jurusan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendaftar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pending</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diterima</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ditolak</th>
                                    @foreach($tracks as $t)<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $t->name }}</th>@endforeach
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Kuota</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Sisa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($grouped->get($level->id, collect()) as $major)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $major->school->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $major->code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $major->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $major->total_applicants }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">{{ $major->pending_count }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-green-100 text-green-800">{{ $major->accepted_count }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-red-100 text-red-800">{{ $major->rejected_count }}</span>
                                        </td>
                                        @foreach($tracks as $t)
                                            @php $q = $major->{"quota_{$t->id}"} ?? null; $s = $major->{"sisa_{$t->id}"} ?? null; @endphp
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($q !== null)
                                                    <span class="text-xs">{{ $q }} <span class="text-gray-400">/ sisa {{ $s }}</span></span>
                                                @else <span class="text-gray-400">-</span> @endif
                                            </td>
                                        @endforeach
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $major->trackQuotas->sum('quota') ?: $major->quota }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $major->available_quota }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.majors.show', $major) }}" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Detail</a>
                                                <a href="{{ route('admin.majors.edit', $major) }}" class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded hover:bg-yellow-200">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 9 + $trackCount + 2 }}" class="px-6 py-4 text-center text-gray-500">Tidak ada data jurusan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center text-gray-500">Tidak ada data jurusan</div>
            </div>
        @endforelse
    </div>
</div>
