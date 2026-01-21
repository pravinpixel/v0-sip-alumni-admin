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
        $query = Alumnis::with(['city', 'centerLocation', 'occupation'])->orderBy('id', 'desc');

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

        if ($this->request->filled('centerLocations')) {
            $centerLocations = is_array($this->request->centerLocations)
                ? $this->request->centerLocations
                : explode(',', $this->request->centerLocations);

            $query->whereHas('centerLocation', function ($q) use ($centerLocations) {
                $q->whereIn('name', $centerLocations);
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

        if ($this->request->filled('search')) {
            $this->applySearch($query, $this->request->search);
        }


        return $query->get();
    }
    private function applySearch($query, $searchValue)
    {
        $parsedDate = date('Y-m-d', strtotime($searchValue));
        $yearSearch = preg_match('/^\d{4}$/', $searchValue) ? $searchValue : null;
        $query->where(function ($q) use ($searchValue, $parsedDate, $yearSearch) {
            $q->where('full_name', 'like', "%{$searchValue}%")
            ->orWhere('year_of_completion', 'like', "%{$searchValue}%")
            ->orWhere('status', 'like', "%{$searchValue}%")
            ->orWhere('email', 'like', "%{$searchValue}%")
            ->orWhere('mobile_number', 'like', "%{$searchValue}%")
            ->orWhereHas('occupation', fn($o) =>
                    $o->where('name', 'like', "%{$searchValue}%"))
            ->orWhereHas('centerLocation', fn($cl) =>
                    $cl->where('name', 'like', "%{$searchValue}%"))
            ->orWhereHas('city', fn($c) =>
                    $c->where('name', 'like', "%{$searchValue}%")
                    ->orWhereHas('state', fn($s) =>
                            $s->where('name', 'like', "%{$searchValue}%")
                    )
            );
            if ($parsedDate && $parsedDate !== '1970-01-01') {
                $q->orWhereDate('created_at', $parsedDate);
            }
            if ($yearSearch) {
                $q->orWhereYear('created_at', $yearSearch);
            }
        });
    }


    public function headings(): array
    {
        return [
            'Created On',
            'Name',
            'Year',
            'Center Location',
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
            $row->centerLocation?->name ?? '-',
            $row->city?->name . ' - ' . $row->city?->state?->name,
            $row->email ?? '-',
            $row->mobile_number ?? '-',
            $row->occupation->name ?? '-',
            ucfirst($row->status) ?? '-',
        ];
    }
}
