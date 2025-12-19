<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumbs and Header -->
        
    <x-admin.breadcrumb-header 
    icon="fas fa-info-circle text-info" 
    title="EPC Entry Details #{{ $alreadyDefineEpc->id }}" 
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
        ['route' => 'admin.already_define_epc.index', 'label' => 'EPC Entries'], 
        ['label' => 'Details']
    ]" 
/>


        <div class="card shadow-sm">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $alreadyDefineEpc->id }}</dd>

                    <dt class="col-sm-3">Work Service</dt>
                    <dd class="col-sm-9">{{ $alreadyDefineEpc->workService->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">SL No</dt>
                    <dd class="col-sm-9">{{ $alreadyDefineEpc->sl_no }}</dd>

                    

                    <dt class="col-sm-3">Stage Name</dt>
                    <dd class="col-sm-9">{{ $alreadyDefineEpc->stage_name }}</dd>

                    <dt class="col-sm-3">Item Description</dt>
                    <dd class="col-sm-9">{{ $alreadyDefineEpc->item_description ?: '-' }}</dd>

                    <dt class="col-sm-3">Percent</dt>
                    <dd class="col-sm-9">{{ number_format($alreadyDefineEpc->percent, 2) }}%</dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $alreadyDefineEpc->created_at->format('d M Y, h:i A') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $alreadyDefineEpc->updated_at->format('d M Y, h:i A') }}</dd>
                </dl>

                <a href="{{ route('admin.already_define_epc.index') }}" class="btn btn-secondary">Back to List</a>
                <a href="{{ route('admin.already_define_epc.edit', $alreadyDefineEpc) }}" class="btn btn-primary">Edit Entry</a>
            </div>
        </div>
    </div>
</x-app-layout>
