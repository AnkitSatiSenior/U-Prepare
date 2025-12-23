<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumbs and Header -->
      <x-admin.breadcrumb-header 
    icon="fas fa-plus-circle text-success" 
    title="Add New EPC Entry" 
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
        ['route' => 'admin.already_define_epc.index', 'label' => 'EPC Entries'], 
        ['label' => 'Add New']
    ]" 
/>


        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.already_define_epc.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="work_service" class="form-label">Work Service <span class="text-danger">*</span></label>
                        <select name="work_service" id="work_service" class="form-control" required>
                            <option value="" disabled selected>Select Work Service</option>
                            @foreach($workServices as $service)
                                <option value="{{ $service->id }}" {{ old('work_service') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sl_no" class="form-label">SL No <span class="text-danger">*</span></label>
                        <input type="number" name="sl_no" id="sl_no" value="{{ old('sl_no') }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
    <label for="activity_id" class="form-label">Activity Name</label>
    <select name="activity_id" id="activity_id" class="form-control">
        <option value="">Select Existing Activity</option>
        @foreach($activities as $activity)
            <option value="{{ $activity->id }}" {{ old('activity_id') == $activity->id ? 'selected' : '' }}>
                {{ $activity->name }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">Or add a new activity below</small>
</div>

<div class="mb-3">
    <label for="new_activity_name" class="form-label">New Activity Name</label>
    <input type="text" name="new_activity_name" id="new_activity_name" value="{{ old('new_activity_name') }}" class="form-control" placeholder="Enter new activity name">
</div>


                    <div class="mb-3">
                        <label for="stage_name" class="form-label">Stage Name <span class="text-danger">*</span></label>
                        <input type="text" name="stage_name" id="stage_name" value="{{ old('stage_name') }}" class="form-control" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="item_description" class="form-label">Item Description</label>
                        <textarea name="item_description" id="item_description" rows="3" class="form-control">{{ old('item_description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="percent" class="form-label">Percent (%) <span class="text-danger">*</span></label>
                        <input type="number" name="percent" id="percent" value="{{ old('percent') }}" step="0.01" min="0" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.already_define_epc.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success">Create Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
