<x-app-layout>
<div class="container-fluid">
    <h3>Work Status for Packages</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>S.No.</th>
                <th>Name of Package</th>
                <th>Estimated Value</th>
                <th>Bid Published</th>
                <th>Under Financial Evaluation</th>
                <th>Under Technical Evaluation</th>
                <th>LOA Issued</th>
                <th>Contract Signed</th>
                <th>PDI Completed</th>
                <th>Delivery Completed</th>
                <th>Payment Complete</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $index => $row)
                @php
                    // Helper function to check if a status was achieved
                    $hasStatus = function($statusName) use ($row) {
                        return in_array($statusName, $row['history']) ? '☑ ✓' : '☐';
                    };
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['package_name'] }}</td>
                    <td>{{ number_format($row['estimated_value'], 2) }}</td>
                    
                    <td class="text-center">{{ $hasStatus(\App\Models\PackageProject::STATUS_RFP_BID_DOCUMENTS_PUBLISHED) }}</td>
                    <td class="text-center">{{ $hasStatus(\App\Models\PackageProject::STATUS_FINANCIAL_EVALUATION) }}</td>
                    <td class="text-center">{{ $hasStatus(\App\Models\PackageProject::STATUS_TECHNICAL_EVALUATION) }}</td>
                    <td class="text-center">{{ $hasStatus(\App\Models\PackageProject::STATUS_LOA_ISSUED) }}</td>
                    <td class="text-center">{{ $hasStatus(\App\Models\PackageProject::STATUS_CONTRACT_SIGNED) }}</td>
                    <td class="text-center">{{ $hasStatus(\App\Models\PackageProject::STATUS_PRE_DISPATCH_INSPECTION) }}</td>
                    <td class="text-center">{{ $hasStatus(\App\Models\PackageProject::STATUS_DELIVERED) }}</td>
                    <td class="text-center">{{ $hasStatus(\App\Models\PackageProject::STATUS_PAYMENT) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right"><strong>Total Award up to 15th Feb 2026</strong></td>
                <td colspan="9"><strong>21.66 Cr.</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
</x-app-layout>