<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class ContactsImport implements ToCollection, WithHeadingRow
{
    public $data;

    public function __construct()
    {
        $this->data = collect();
    }

    public function collection(Collection $rows)
    {
        $this->data = $rows->map(function ($row) {
            return [
                'name' => (string)$row['name'],
                'address' => (string)$row['address'],
                'phone_number' => (string)$row['phone_number'],
                'group_name' => (string)$row['group_name'],
            ];
        });
    }

    public function getData()
    {
        return $this->data;
    }
}
