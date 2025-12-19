@foreach ($filesGrouped as $month => $files)
    <div class="row g-3 mb-5" id="gallery">
        <div class="col-12">
            <h3 class="mt-3 h3">{{ $month }}</h3>
        </div>

        @foreach ($files as $index => $file)
            <div class="col-md-3 col-6">
                <div class="card shadow-sm h-100">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#galleryModal" data-index="{{ $index }}">
                        @if ($file['isImage'])
                            <img src="{{ $file['thumb'] }}" alt="{{ $file['filename'] }}"
                                 class="img-fluid rounded-top"
                                 style="height:200px; width:100%; object-fit:cover; background:#f9f9f9;" loading="lazy">
                        @elseif ($file['extension'] === 'pdf')
                            <div class="d-flex align-items-center justify-content-center bg-light rounded-top"
                                 style="height:200px;">
                                <i class="fas fa-file-pdf fa-3x text-danger"></i>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light rounded-top"
                                 style="height:200px;">
                                <i class="fas fa-file fa-3x text-secondary"></i>
                            </div>
                        @endif
                    </a>
                    <div class="card-body p-2 text-center">
                        <small class="d-block text-truncate">{{ $file['filename'] }}</small>
                        <div class="btn-group mt-2">
                            <button class="btn btn-sm btn-outline-secondary copy-btn" data-url="{{ $file['url'] }}">
                                <i class="fa fa-link"></i>
                            </button>
                            <a href="{{ $file['url'] }}" download class="btn btn-sm btn-outline-success">
                                <i class="fa fa-download"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $file['id'] }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endforeach
