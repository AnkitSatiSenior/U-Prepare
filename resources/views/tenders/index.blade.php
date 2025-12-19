{{-- resources/views/tenders/index.blade.php --}}
<x-guest-layout>
    @section('page_title', 'Tenders')
    <div class="container-fluid py-6">

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">
            <i class="fas fa-file-contract text-primary me-2"></i>Available Tenders
        </h2>

        @if($tenders->isEmpty())
            <p class="text-gray-600">No tenders available at the moment.</p>
        @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Open Date</th>
                                <th>Close Date</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tenders as $tender)
                                <tr>
                                    <td>{{ $tender->id }}</td>
                                    <td>{{ $tender->title_en }} / {{ $tender->title_hi }}</td>
                                    <td>{{ formatDate($tender->open_date) }}</td>
                                    <td>{{ formatDate($tender->close_date) }}</td>
                                    <td>
                                        @if($tender->file)
                                            <a href="{{ Storage::url($tender->file) }}" target="_blank" class="text-primary">
                                                View
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-guest-layout>
