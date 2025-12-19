@props(['subProjectsData' => []])
<x-admin.card title="Physical Entries" icon="fas fa-dumbbell" headerClass="bg-info text-white">

    <x-admin.data-table id="physical-entries-table" :headers="['#', 'Sub-Project', 'Item Description', 'Amount / Percent', 'Date']" :excel="true" :print="true"
        :pageLength="10" resourceName="physical-entries">
        @php $rowIndex = 1; @endphp

        @foreach ($subProjectsData as $sp)
            @foreach ($sp['physicalEntries'] as $entry)
                <tr>
                    <td>{{ $rowIndex++ }}</td>
                    <td class="text-truncate" style="max-width:200px;" title="{{ $sp['name'] }}">
                        {{ $sp['name'] }}
                    </td>
                    <td>{{ $entry['item_description'] }}</td>

                    <td class="text-end">
                        @if (isset($entry['percent']))
                            {{ $entry['percent'] }}%
                        @elseif(isset($entry['amount']))
                            ₹{{ number_format($entry['amount']) }}
                        @else
                            -
                        @endif
                    </td>


                    <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d M Y') }}</td>
                </tr>
            @endforeach
        @endforeach
    </x-admin.data-table>

</x-admin.card>
