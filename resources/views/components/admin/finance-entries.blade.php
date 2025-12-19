@props(['subProjectsData' => []])

     <x-admin.card title="Finance Entries" icon="fas fa-money-bill-wave" headerClass="bg-dark text-white">
    <x-admin.data-table 
        id="finance-entries-table"
        :headers="['#', 'Sub-Project', 'Finance Amount (₹)', 'Submit Date', 'Bills', 'Serial No.', 'Files']"
        :excel="true"
        :print="true"
        :pageLength="10"
        :resourceName="'finance-entries'"
    >
        @foreach($subProjectsData as $sp)
            @foreach($sp['financeEntries'] as $entry)
                <tr>
                    <td>{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                    <td>{{ $sp['name'] }}</td>

                    {{-- Finance Amount --}}
                    <td class="text-end fw-bold text-success">
                        ₹{{ isset($entry['amount']) ? number_format($entry['amount'], 2) : '0.00' }}
                    </td>

                    {{-- Submit Date --}}
                    <td>
                        {{ !empty($entry['date']) 
                            ? \Carbon\Carbon::parse($entry['date'])->format('d M Y') 
                            : '-' }}
                    </td>

                    {{-- Bills --}}
                    <td>{{ $entry['no_of_bills'] ?? '-' }}</td>

                    {{-- Serial No. --}}
                    <td>
                        @if(!empty($entry['bill_serial_no']))
                            {{ is_array($entry['bill_serial_no']) 
                                ? implode(', ', $entry['bill_serial_no']) 
                                : $entry['bill_serial_no'] }}
                        @else
                            <span class="text-muted fst-italic">N/A</span>
                        @endif
                    </td>

                    {{-- Files --}}
                    <td>
                        @if(!empty($entry['media_files']))
                            @foreach($entry['media_files'] as $file)
                                <a href="{{ asset('storage/app/public/'.$file['path']) }}"
                                   target="_blank"
                                   class="badge bg-light border text-primary me-1">
                                    <i class="fas fa-file-alt me-1"></i> View
                                </a>
                            @endforeach
                        @else
                            <span class="text-muted fst-italic">No files</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
    </x-admin.data-table>

</x-admin.card>
