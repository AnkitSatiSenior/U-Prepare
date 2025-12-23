        <div class="card mb-3">
            <div class="card-body bg-white py-0">
                <p class="h2">
                    <i class="fas fa-info-circle me-2 "></i> Package Name : {{ $packageProject->package_name }}
                    <hr class="mb-2" />
                </p>

                <div class="row">
                    <div class="col-md-3">
                        <span class="form-label text-muted h3">
                            <i class="fas fa-info-circle me-2"></i> Package No : {{ $packageProject->package_number }}
                        </span>
                    </div>

                    <div class="col-md-3">
                        <span class="form-label text-muted h3"> Implementation Agency : </span>
                        <span class="form-label text-muted h4"> {{ $packageProject->department->name ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="col-md-2 mb-3">
                        <span class="form-label text-muted h3"> Category : </span>
                        <span class="form-label text-muted h4"> {{ $packageProject->category->name ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="col-md-2 mb-3">
                        <span class="form-label text-muted h3"> Sub Category : </span>
                        <span class="form-label text-muted h4"> {{ $packageProject->subCategory->name ?? 'N/A' }}
                        </span>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <span class="form-label text-muted h3"> Sanction Cost : (Including GST)</span>
                        <span class="form-label text-muted h4">
                            {{ formatPriceToCR($packageProject->estimated_budget_incl_gst) }}
                        </span>
                    </div>

                    <div class="col-md-3 mb-3">
                        <span class="form-label text-muted h3"> District : </span>
                        <span class="form-label text-muted h4"> {{ $packageProject->district->name ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="col-md-2 mb-3">
                        <span class="form-label text-muted h3"> Block : </span>
                        <span class="form-label text-muted h4"> {{ $packageProject->block->name ?? 'N/A' }} </span>
                    </div>

                    <div class="col-md-2 mb-3">
                        <span class="form-label text-muted h3"> Vidhan Sabha : </span>
                        <span class="form-label text-muted h4"> {{ $packageProject->vidhanSabha->name ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- End: Card Body -->
        </div>
