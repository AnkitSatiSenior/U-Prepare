<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload"></i> Media Manager</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Tabs --}}
                <ul class="nav nav-tabs" id="uploadTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button">Upload</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="view-tab" data-bs-toggle="tab" data-bs-target="#view" type="button">View</button>
                    </li>
                </ul>

                {{-- Tab content --}}
                <div class="tab-content mt-3">
                    {{-- Upload Tab --}}
                    <div class="tab-pane fade show active" id="upload">
                        <form id="upload-form">
                            <input type="hidden" name="entry_id" id="modal-entry-id">
                            <input type="hidden" name="social_id" id="modal-social-id">

                            <table class="table table-bordered d-none" id="upload-table">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Size</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <input type="file" name="media_files[]" multiple class="form-control mb-3" id="file-input">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-cloud-upload-alt"></i> Upload</button>
                        </form>
                    </div>

                    {{-- View Tab --}}
                    <div class="tab-pane fade" id="view">
                        <table class="table table-bordered" id="view-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File Name</th>
                                    <th>Type</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody id="view-table-body">
                                <tr><td colspan="4" class="text-center">No files uploaded yet.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
