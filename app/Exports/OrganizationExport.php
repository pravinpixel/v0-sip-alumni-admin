<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Location;
use App\Models\Organization;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrganizationExport implements FromQuery, WithHeadings, WithMapping
{
     protected $filters;
    private $serialNumber = 0;

    public function __construct($filters = null)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $search = $this->filters['search'];
        $selectedLocationName = $this->filters['location'];

        $query = Organization::query()->with('location')->orderByDesc('id');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_code', 'like', '%' . $search . '%')
                    ->orWhere('company_name', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('primary_mail_id1', 'like', '%' . $search . '%')
                    ->orWhere('primary_mail_id2', 'like', '%' . $search . '%')
                    ->orWhere('primary_phone1', 'like', '%' . $search . '%')
                    ->orWhere('primary_phone2', 'like', '%' . $search . '%')
                    ->orWhere('primary_name1', 'like', '%' . $search . '%')
                    ->orWhere('primary_name2', 'like', '%' . $search . '%');
            });
        }

        if ($selectedLocationName) {
            $locationIds = Location::where('name', $selectedLocationName)->pluck('id');
            $query->whereIn('location_id', $locationIds);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Id',
            'Customer Code',
            'Company Name',
            'Address',
            'Location ID',
            'Primary Contact Name 1',
            'Primary Email ID 1',
            'Primary Phone No1',
            'Primary Contact Name 2',
            'Primary Email ID 2',
            'Primary Phone No 2',
            'Created Date',
            'Updated Date',
        ];
    }

    public function map($organization): array
    {
        $this->serialNumber++;
        return [
            $this->serialNumber,
            $organization->id ?? '',
            $organization->customer_code ?? '',
            $organization->company_name ?? '',
            $organization->address ?? '',
            $organization->location_id ?? '',
            $organization->primary_name1 ?? '',
            $organization->primary_mail_id1 ?? '',
            $organization->primary_phone1 ?? '',
            $organization->primary_name2 ?? '',
            $organization->primary_mail_id2 ?? '',
            $organization->primary_phone2 ?? '',
            $organization->created_at ? (new Carbon($organization->created_at))->format('d-m-Y') : '',
            $organization->updated_at ? (new Carbon($organization->updated_at))->format('d-m-Y') : '',
        ];
    }
}
