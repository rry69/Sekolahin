<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ActivityLogExport implements FromView, WithHeadings, ShouldAutoSize
{
    protected $logs;

    public function __construct($logs)
    {
        $this->logs = $logs;
    }

    public function view(): View
    {
        return view('exports.activity-logs', [
            'logs' => $this->logs,
        ]);
    }

    public function headings(): array
    {
        return [
            'Waktu', 'Aksi', 'Label', 'Kategori', 'Deskripsi',
            'User', 'Email', 'IP', 'Properties',
        ];
    }
}
