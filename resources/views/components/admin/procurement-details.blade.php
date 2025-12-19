<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3">
                        <i class="fas fa-check-circle me-2 h2 
                            {{ $procurementDetail->method_of_procurement ? 'text-success' : 'text-secondary' }}"></i>
                        <span class="form-label text-muted h2"> Procurement Details </span>
                         
                    </div>
                    
                    <div class="col-md-3">
                        <span class="form-label text-muted h3"> {{ $procurementDetail->method_of_procurement }} </span> <br/>
                         <span class="form-label text-muted h3"> {{ $procurementDetail->type_of_procurement }} </span> 
                    </div>
                    
                    <div class="col-md-3">
                        <span class="form-label text-muted h3"> Bid Validity : {{ $procurementDetail->bid_validity_days }} days </span>
                    </div>
                    <div class="col-md-3">
                        @if ($procurementDetail->publication_document_path)
                            <div class="mt-3 pull-right">
                                <a href="{{ asset('storage/' . $procurementDetail->publication_document_path) }}"
                                    target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-file-pdf me-2"></i> View Bid Publish Doc 
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                     <div class="row">
                    <div class="col-md-12">
                        <dl class="row mb-0">
                            <dt class="col-sm-2 text-muted h3">Publication Date : </dt>
                            <dd class="col-sm-2 text-muted h3">
                                {{ optional($procurementDetail->publication_date)->format('d M Y') ?? 'N/A' }}
                            </dd>

                            <dt class="col-sm-2 text-muted h3">Tender Fee : </dt>
                            <dd class="col-sm-2 text-muted h3">
                                ₹ {{ number_format($procurementDetail->tender_fee, 2) }}
                            </dd>
                            <dt class="col-sm-2 text-muted h3">EMD Amount : </dt>
                            <dd class="col-sm-2 text-muted h3">
                                ₹ {{ number_format($procurementDetail->earnest_money_deposit, 2) }}
                            </dd> 
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
