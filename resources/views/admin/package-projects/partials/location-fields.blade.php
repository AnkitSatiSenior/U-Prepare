<div class="card shadow-sm mb-3 mt-3">
    <div class="card-body">
        <h6 class="mb-3 text-muted border-bottom pb-2">
            <i class="fas fa-map-marker-alt me-2"></i>Location Information
        </h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="district_id" class="form-label">District</label>
                <select name="district_id" id="district_id" class="form-control">
                    <option value="">Select District</option>
                    @foreach ($districts as $district)
                        <option value="{{ $district->id }}" 
                            @selected(old('district_id', $packageProject->district_id) == $district->id)>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="block_id" class="form-label">Block</label>
                <select name="block_id" id="block_id" class="form-control">
                    <option value="">Select Block</option>
                    @foreach ($blocks as $block)
                        <option value="{{ $block->id }}" 
                            @selected(old('block_id', $packageProject->block_id) == $block->id)>
                            {{ $block->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="vidhan_sabha_id" class="form-label">Vidhan Sabha</label>
                <select name="vidhan_sabha_id" id="vidhan_sabha_id" class="form-control">
                    <option value="">Select Constituency</option>
                    @foreach ($constituencies as $constituency)
                        <option value="{{ $constituency->id }}" 
                            @selected(old('vidhan_sabha_id', $packageProject->vidhan_sabha_id) == $constituency->id)>
                            {{ $constituency->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="lok_sabha_id" class="form-label">Lok Sabha</label>
                <select name="lok_sabha_id" id="lok_sabha_id" class="form-control">
                    <option value="">Select Constituency</option>
                    @foreach ($assembly as $constituency)
                        <option value="{{ $constituency->id }}" 
                            @selected(old('lok_sabha_id', $packageProject->lok_sabha_id) == $constituency->id)>
                            {{ $constituency->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Select Status</option>
                    @foreach ([
    \App\Models\PackageProject::STATUS_PENDING_PROCUREMENT,
    \App\Models\PackageProject::STATUS_PENDING_CONTRACT,
    \App\Models\PackageProject::STATUS_PENDING_ACTIVITY,
    \App\Models\PackageProject::STATUS_IN_PROGRESS,
    \App\Models\PackageProject::STATUS_CANCEL,
    \App\Models\PackageProject::STATUS_REBID,
    \App\Models\PackageProject::STATUS_REMOVED,
] as $statusOption)
    <option value="{{ $statusOption }}" 
        @selected(old('status', $packageProject->status) === $statusOption)>
        {{ $statusOption }}
    </option>
@endforeach
                </select>
            </div>
        </div>
    </div>
</div>
