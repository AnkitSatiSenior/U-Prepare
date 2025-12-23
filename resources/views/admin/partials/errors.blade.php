@if ($errors->any())
    <div class="alert alert-danger">
        <h5 class="mb-2">
            <i class="fas fa-exclamation-circle me-1"></i>
            There were some problems with your input:
        </h5>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
