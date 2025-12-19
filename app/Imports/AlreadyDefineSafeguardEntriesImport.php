<?php

namespace App\Imports;

use App\Models\AlreadyDefineSafeguardEntry;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AlreadyDefineSafeguardEntriesImport implements ToModel, WithHeadingRow
{
    protected $complianceId;
    protected $phaseId;
    protected $categoryId;
    protected $rowCount = 0; // To track row order for order_by

    /**
     * Constructor
     *
     * @param int $complianceId
     * @param int $phaseId
     * @param int|null $categoryId
     */
    public function __construct($complianceId, $phaseId, $categoryId = null)
    {
        $this->complianceId = $complianceId;
        $this->phaseId = $phaseId;
        $this->categoryId = $categoryId;
    }

    /**
     * Map each row of the Excel sheet to the model.
     *
     * @param array $row
     * @return AlreadyDefineSafeguardEntry|null
     */
    public function model(array $row)
    {
        $slNo = trim($row['sl_no'] ?? '');
        $itemDescription = trim($row['item_description'] ?? '');

        // Skip empty rows
        if ($slNo === '' || $itemDescription === '') {
            return null;
        }

        // Increment row count for order
        $this->rowCount++;

        return new AlreadyDefineSafeguardEntry([
            'safeguard_compliance_id' => $this->complianceId,
            'contraction_phase_id'    => $this->phaseId,
            'category_id'             => $this->categoryId ?? ($row['category_id'] ?? null),
            'sl_no'                   => $slNo,
            'order_by'                => $this->rowCount, // preserves file order
            'item_description'        => $itemDescription,
            
            'is_validity'             => isset($row['is_validity']) ? (int)$row['is_validity'] : 0,
            'is_major_head'           => isset($row['is_major_head']) ? (int)$row['is_major_head'] : 0,
        ]);
    }
}
