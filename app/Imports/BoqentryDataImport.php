<?php

namespace App\Imports;

use App\Models\BoqEntryData; // fixed
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BoqentryDataImport implements ToCollection, WithHeadingRow
{
    protected $subPackageProjectId;

    public function __construct($subPackageProjectId)
    {
        $this->subPackageProjectId = $subPackageProjectId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $sl_no = $row['sl_no'] ?? null;

            if ($sl_no !== null) {
                $sl_no = $this->formatSlNo($sl_no);
            }

            BoqEntryData::create([
                'sub_package_project_id' => $this->subPackageProjectId,
                'sl_no' => $sl_no,
                'item_description' => $row['item'] ?? null,
                'unit' => $row['unit'] ?? null,
                'qty' => $row['qty'] ?? null,
                'rate' => $row['rate'] ?? null,
                'amount' => $row['amount'] ?? null,
            ]);
        }
    }

    private function formatSlNo(string $sl_no): string
    {
        if (strpos($sl_no, '.') !== false) {
            [$intPart, $decPart] = explode('.', $sl_no, 2);
            if (strlen($decPart) === 1) {
                $decPart .= '0';
            }
            return $intPart . '.' . $decPart;
        }

        return $sl_no;
    }
}
