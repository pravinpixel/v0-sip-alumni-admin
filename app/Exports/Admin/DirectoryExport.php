<?php

namespace App\Exports\Admin;

use App\Models\Alumnis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DirectoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Alumnis::with(['city', 'occupation'])->orderBy('id', 'desc');

        // Filters (same as your DataTable)
        if ($this->request->filled('years')) {
            $years = is_array($this->request->years)
                ? $this->request->years
                : explode(',', $this->request->years);

            $query->whereIn('year_of_completion', $years);
        }

        if ($this->request->filled('cities')) {
            $cities = is_array($this->request->cities)
                ? $this->request->cities
                : explode(',', $this->request->cities);

            $query->whereHas('city', function ($q) use ($cities) {
                $q->whereIn('name', $cities);
            });
        }

        if ($this->request->filled('occupations')) {
            $occupations = is_array($this->request->occupations)
                ? $this->request->occupations
                : explode(',', $this->request->occupations);

            $query->whereHas('occupation', function ($q) use ($occupations) {
                $q->whereIn('name', $occupations);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Created On',
            'Name',
            'Batch',
            'City & State',
            'Email',
            'Contact',
            'Occupation',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d-m-Y') ?? '-',
            $row->full_name ?? '-',
            $row->year_of_completion ?? '-',
            $row->city?->name . ' - ' . $row->city?->state?->name,
            $row->email ?? '-',
            $row->mobile_number ?? '-',
            $row->occupation->name ?? '-',
            ucfirst($row->status) ?? '-',
        ];
    }
}
