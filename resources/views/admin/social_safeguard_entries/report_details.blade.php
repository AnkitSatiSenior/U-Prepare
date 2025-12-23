<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-list-alt text-primary" title="Detailed Safeguard Entries"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Safeguard Report'],
            ]" />

        <!-- Project Info -->
        <div class="alert alert-info shadow-sm rounded-lg mb-4">
            <strong>Project:</strong> {{ $subProject->name }} <br>
            <strong>Compliance:</strong> {{ $compliance->name }} <br>
            
        </div>

        <!-- Entries Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <x-admin.data-table id="safeguard-entries-table" :headers="[
                    '#',
                    'Phase',
                    'Item',
                    'Yes/No',
                    'Photos/Documents',
                    'Remarks',
                    'Validity',
                    'Entry Date',
                    'Created At',
                    'Updated At',
                    'Action',
                ]" :excel="true" :print="true"
                    title="Safeguard Entries Details" searchPlaceholder="Search entries..." resourceName="entries"
                    :pageLength="25">

                    @foreach ($entries as $index => $entry)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $entry->phase_name }}</td>
                            <td>{{ $entry->master_item_description }}</td>


                            <td>
                                @if ($entry->yes_no === 1)
                                    Yes
                                @elseif ($entry->yes_no === 0)
                                    No
                                @elseif ($entry->yes_no === 3)
                                    N/A
                                @endif
                            </td>

                            <td>{{ $entry->photos_documents_case_studies }}</td>
                            <td>{{ $entry->remarks }}</td>
                            <td>{{ $entry->validity_date }}</td>
                            <td>{{ \Carbon\Carbon::parse($entry->date_of_entry)->format('d F Y') }}</td>

                            <td>{{ $entry->created_at }}</td>
                            <td>{{ $entry->updated_at }}</td>
                            <td>
                                <form action="{{ route('admin.social_safeguard_entries.destroy', $entry->sse_id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to permanently delete this entry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>
