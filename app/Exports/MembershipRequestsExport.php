<?php

namespace App\Exports;

use App\Models\MembershipRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MembershipRequestsExport implements FromCollection, WithHeadings
{
    protected $ids;

    // Constructor me IDs pass karenge
    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        return MembershipRequest::whereIn('id', $this->ids)
            ->get([
                'id',
                'name',
                'email',
                'phone',
                'role',
                'organization',
                'address',
                'state',
                'zip',
                'level',
                'sports',
                'created_at',
                'updated_at',
            ])
            ->map(function ($item) {
                $item->sports = implode(', ', $item->sports ?? []);
                return $item;
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Role',
            'Organization',
            'Address',
            'State',
            'Zip',
            'Apparel Level',
            'Sports',
            'Submitted At',
            'Last Updated',
        ];
    }
}
