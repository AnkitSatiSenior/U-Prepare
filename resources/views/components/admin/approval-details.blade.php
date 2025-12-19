        <div class="row">
            <!-- HPC and DEC Approvals -->
            <div class="col-md-12 mb-2">
                <div class="card h-100">
                    <div class="card-body">

                    @if ($packageProject->dec_approved)
                            <div class="row">
                                <div class="col-md-8 mb-2">
                                    <i class="fas fa-check-circle {{ $packageProject->dec_approved ? 'text-success' : 'text-secondary' }}"></i>
                                    <span class="form-label text-muted h3"> DEC Letter No. </span>
                                        <span class="form-label text-muted h4">
                                        {{ $packageProject->dec_letter_number ?? 'N/A' }} </span>
                                    <br/>
                                    <span class="form-label text-muted h4"> Dated : </span>
                                    <span class="form-label text-muted h4">
                                    {{ formatDate($packageProject->dec_approval_date) ?? 'N/A' }} </span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    @if ($packageProject->dec_document_path)
                                        <p class="form-control-static">
                                            <span class="form-label text-muted"> </span>

                                            <a href="{{ Storage::url($packageProject->dec_document_path) }}"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-pdf me-1"></i> View DEC Doc
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <hr />
                        @endif

                        @if ($packageProject->hpc_approved)
                            <div class="row mt-3">
                                <div class="col-md-8 mb-0">
                                    <i class="fas fa-check-circle {{ $packageProject->dec_approved ? 'text-success' : 'text-secondary' }}"></i>
                                    <span class="form-label text-muted h3"> HPC Letter No.</span>
                                    <span class="form-label text-muted h4">
                                        {{ $packageProject->hpc_letter_number ?? 'N/A' }} </span>
                                     <br/>
                                   
                                    <span class="form-label text-muted h4"> Dated : </span>
                                    <span class="form-label text-muted h4">
                                        {{ formatDate($packageProject->hpc_approval_date) ?? 'N/A' }} </span>
                                </div>

                                <div class="col-md-4 mb-0">
                                    @if ($packageProject->hpc_document_path)
                                        <p class="form-control-static">
                                            <label class="form-label text-muted"> </label>
                                            <a href="{{ Storage::url($packageProject->hpc_document_path) }}"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-pdf me-1"></i> View HPC Doc
                                            </a>
                                        </p>
                                    @endif
                                </div>

                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>