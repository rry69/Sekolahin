<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Peringkat Pendaftaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <form method="GET" action="{{ route('registration.ranking') }}" class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Jurusan</label>
                            <select name="major_id" onchange="this.form.submit()" required class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($majors as $m)
                                    <option value="{{ $m->id }}" {{ request('major_id', $major->id) == $m->id ? 'selected' : '' }}>
                                        {{ $m->school->name }} - {{ $m->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $major->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $major->school->name }} &middot; Kuota: {{ $major->quota }}</p>
                        </div>
                    </div>

                    @if ($userRegistration)
                        <div class="mb-4 p-4 rounded border {{ $userRegistration->total_score !== null ? 'bg-blue-50 border-blue-200' : 'bg-yellow-50 border-yellow-200' }}">
                            <p class="text-sm font-medium text-gray-800">
                                Posisi Anda: <span class="font-bold">#{{ $userRegistration->rank_position }}</span>
                                @if ($userRegistration->total_score !== null)
                                    &middot; Nilai: {{ $userRegistration->total_score }}
                                @else
                                    <span class="text-yellow-700">(Belum dinilai)</span>
                                @endif
                            </p>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peringkat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jalur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($rankings as $ranking)
                                    <tr class="{{ $userRegistration && $ranking->id === $userRegistration->id ? 'bg-blue-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">#{{ $ranking->rank_position }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ranking->applicant->full_name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ranking->registrationTrack->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $ranking->total_score }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada data ranking</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>