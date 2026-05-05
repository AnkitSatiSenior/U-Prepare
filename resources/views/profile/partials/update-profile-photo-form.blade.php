<div class="mb-5">
    <h5 class="text-lg font-medium text-primary m-3">{{ __('Update Profile Photo') }}</h5>

    <div class="card border-0 shadow-sm mx-3">
        <div class="card-body">
            <div class="row align-items-center g-4">
                <div class="col-md-3 text-center">
                    <img
                        id="profile-photo-preview"
                        data-profile-photo
                        src="{{ $user->profile_photo_url }}"
                        alt="{{ $user->name }}"
                        class="rounded-circle border"
                        style="width: 140px; height: 140px; object-fit: cover;"
                    >
                </div>

                <div class="col-md-9">
                    <form id="profile-photo-upload-form" action="{{ route('profile-photo.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label fw-semibold">{{ __('Choose a new photo') }}</label>
                            <input
                                class="form-control"
                                type="file"
                                id="profile_photo"
                                name="profile_photo"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                required
                            >
                            <div class="form-text">JPG, JPEG, PNG, or WEBP. Maximum 2 MB.</div>
                        </div>

                        <div id="profile-photo-error" class="alert alert-danger d-none mb-3"></div>
                        <div id="profile-photo-success" class="alert alert-success d-none mb-3"></div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" id="profile-photo-submit" class="btn btn-primary">
                                {{ __('Upload Photo') }}
                            </button>
                            <span id="profile-photo-status" class="text-muted small"></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const form = document.getElementById('profile-photo-upload-form');

        if (!form) {
            return;
        }

        const fileInput = document.getElementById('profile_photo');
        const preview = document.getElementById('profile-photo-preview');
        const submitButton = document.getElementById('profile-photo-submit');
        const status = document.getElementById('profile-photo-status');
        const errorBox = document.getElementById('profile-photo-error');
        const successBox = document.getElementById('profile-photo-success');

        const hideMessages = () => {
            errorBox.classList.add('d-none');
            successBox.classList.add('d-none');
            errorBox.textContent = '';
            successBox.textContent = '';
        };

        const updateAllProfilePhotos = (photoUrl) => {
            document.querySelectorAll('[data-profile-photo]').forEach((image) => {
                image.src = photoUrl;
            });

            const sidebarImage = document.getElementById('profileImage');

            if (sidebarImage) {
                sidebarImage.src = photoUrl;
            }
        };

        fileInput.addEventListener('change', () => {
            hideMessages();

            const [file] = fileInput.files;

            if (!file) {
                return;
            }

            const reader = new FileReader();

            reader.onload = (event) => {
                preview.src = event.target.result;
            };

            reader.readAsDataURL(file);
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            hideMessages();

            if (!fileInput.files.length) {
                errorBox.textContent = 'Please choose a profile photo to upload.';
                errorBox.classList.remove('d-none');
                return;
            }

            submitButton.disabled = true;
            status.textContent = 'Uploading...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: new FormData(form),
                });

                const data = await response.json();

                if (!response.ok) {
                    const message = data.errors?.profile_photo?.[0] || data.message || 'Unable to update profile photo.';
                    throw new Error(message);
                }

                updateAllProfilePhotos(data.photo_url);
                successBox.textContent = data.message;
                successBox.classList.remove('d-none');
                status.textContent = 'Upload complete.';
                form.reset();
            } catch (error) {
                errorBox.textContent = error.message;
                errorBox.classList.remove('d-none');
                status.textContent = '';
            } finally {
                submitButton.disabled = false;
            }
        });
    })();
</script>
