<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-semibold text-dark" id="uploadModalLabel">
                    <i class="fas fa-images text-primary me-2"></i>Media Manager
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0">
                <div class="px-4 pt-3 border-bottom">
                    <ul class="nav nav-tabs border-0" id="mediaTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-medium" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-pane" type="button" role="tab">
                                Upload Files
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-medium" id="view-tab" data-bs-toggle="tab" data-bs-target="#view-pane" type="button" role="tab">
                                View Uploaded
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-4" id="mediaTabsContent">
                    <div class="tab-pane fade show active" id="upload-pane" role="tabpanel">
                        <form id="upload-form">
                            <input type="hidden" name="entry_id" id="modal-entry-id">
                            <input type="hidden" name="social_id" id="modal-social-id">

                            <div class="mb-4">
                                <label for="file-input" class="form-label fw-medium text-secondary small text-uppercase">Select Media Files</label>
                                <input type="file" class="form-control" id="file-input" multiple accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx">
                            </div>

                            <div class="table-responsive d-none border rounded" id="upload-table-container">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-secondary small">
                                        <tr>
                                            <th style="width: 60px;" class="text-center">Preview</th>
                                            <th>File Name</th>
                                            <th style="width: 35%;">Remark</th>
                                            <th style="width: 100px;">Size</th>
                                            <th style="width: 60px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="upload-table-body"></tbody>
                                </table>
                            </div>

                            <div class="text-end mt-4 d-none" id="upload-action-container">
                                <button type="submit" class="btn btn-primary px-4" id="upload-btn">
                                    <i class="fas fa-cloud-upload-alt me-2"></i>Upload Files
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="view-pane" role="tabpanel">
                        <div class="table-responsive border rounded">
                            <table class="table table-hover align-middle mb-0" id="view-table">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th style="width: 60px;" class="text-center">Preview</th>
                                        <th>File Name</th>
                                        <th style="width: 35%;">Remark</th>
                                        <th style="width: 120px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="view-table-body">
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 text-light"></i><br>
                                            No files uploaded yet.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

