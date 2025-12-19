<x-app-layout>
    <div class="container py-5">
        <h2 class="mb-4">Upload BOQ Excel File</h2>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Upload Form --}}
        <form method="POST" action="{{ route('admin.boqentry.upload') }}" enctype="multipart/form-data" class="mb-5">
            @csrf
            <div class="mb-3">
                <label for="sub_package_project_id" class="form-label">Select Sub Package Project</label>
                <select name="sub_package_project_id" id="sub_package_project_id" class="form-control" required>
                    <option value="">-- Select Project --</option>
                    @foreach($subProjects as $project)
                        <option value="{{ $project->id }}" {{ (old('sub_package_project_id') == $project->id) ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="excel_file" class="form-label">Upload Excel File</label>
                <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>

            <button type="submit" class="btn btn-primary">Import</button>
        </form>

        {{-- Display BOQ Entries if present --}}
        @if(isset($subProject))
            <h3>BOQ Entries for Project: <strong>{{ $subProject->name }}</strong></h3>

            @if($boqEntries->isEmpty())
                <p>No BOQ entries found for this project.</p>
            @else
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Sl. No.</th>
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Qty.</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($boqEntries as $parentSlNo => $entries)
                            {{-- Parent row --}}
                            @php
                                $parentEntry = $entries->firstWhere('sl_no', $parentSlNo);
                            @endphp
                            <tr class="table-primary fw-bold">
                                <td>{{ $parentSlNo }}</td>
                                <td>{{ $parentEntry ? $parentEntry->item_description : '' }}</td>
                                <td>{{ $parentEntry ? $parentEntry->unit : '' }}</td>
                                <td>{{ $parentEntry ? $parentEntry->qty : '' }}</td>
                                <td>{{ $parentEntry ? $parentEntry->rate : '' }}</td>
                                <td>{{ $parentEntry ? $parentEntry->amount : '' }}</td>
                            </tr>

                            {{-- Child entries --}}
                            @foreach($entries as $entry)
                                @if($entry->sl_no != $parentSlNo)
                                    <tr>
                                        <td class="ps-4">{{ $entry->sl_no }}</td>
                                        <td>{{ $entry->item_description }}</td>
                                        <td>{{ $entry->unit }}</td>
                                        <td>{{ $entry->qty }}</td>
                                        <td>{{ $entry->rate }}</td>
                                        <td>{{ $entry->amount }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif
    </div>
</x-app-layout>
