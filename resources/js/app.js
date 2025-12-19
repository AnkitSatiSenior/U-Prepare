import $ from 'jquery';
window.$ = window.jQuery = $;
/* jQuery (required for DataTables) */
import "https://code.jquery.com/jquery-3.7.0.min.js";

/* Bootstrap 5 (includes Popper) */
import "https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js";
import "https://code.jquery.com/jquery-3.7.1.min.js"

/* DataTables core */
import "https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js";
import "https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js";

/* DataTables Buttons */
import "https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js";
import "https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js";

/* Export dependencies */
import "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js";
import "https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js";
import "https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js";

import 'bootstrap';
import 'animate.css';

/* DataTables core */
import 'datatables.net-bs5';

/* DataTables Buttons */
import 'datatables.net-buttons-bs5';

import 'datatables.net-buttons/js/buttons.print.js';


/* Excel export dependency */
import JSZip from 'jszip';
window.JSZip = JSZip;


/* -------------------------------------------------
 * 🔹 Custom Modules
 * ------------------------------------------------- */
import { initSafeguardCards } from './safeguard';

/* -------------------------------------------------
 * 🔹 Show / Hide Password
 * ------------------------------------------------- */
export function initPasswordToggle() {
    const toggleBtn = document.getElementById('togglePassword');
    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', () => {
        const password = document.getElementById('password');
        const icon = toggleBtn.querySelector('i');
        if (!password || !icon) return;

        const type = password.type === 'password' ? 'text' : 'password';
        password.type = type;
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
}

/* -------------------------------------------------
 * 🔹 Progress Input Validation
 * ------------------------------------------------- */
export function initProgressValidation() {
    document.querySelectorAll('.progress-input').forEach(input => {
        input.addEventListener('input', function () {
            let val = parseFloat(this.value) || 0;
            const max = parseFloat(this.max) || 100;
            this.value = Math.min(Math.max(val, 0), max);
        });
    });
}

/* -------------------------------------------------
 * 🔹 Chat Modal
 * ------------------------------------------------- */
export function initChatModal() {
    const chatModal = document.getElementById('chat-modal');
    if (!chatModal) return;

    document.getElementById('chat-toggle')?.addEventListener('click', () => {
        chatModal.style.display = 'block';
    });

    document.getElementById('chat-close')?.addEventListener('click', () => {
        chatModal.style.display = 'none';
    });

    window.addEventListener('click', e => {
        if (e.target === chatModal) chatModal.style.display = 'none';
    });
}

/* -------------------------------------------------
 * 🔹 Location Capture
 * ------------------------------------------------- */
export function initLocationCapture() {
    if (!navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(position => {
        fetch('/activity-log-location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            })
        });
    });
}

/* -------------------------------------------------
 * 🔹 DOM Ready
 * ------------------------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggle();
    initProgressValidation();
    initChatModal();
    initLocationCapture();
    initSafeguardCards();

    // Global accordion helper
    window.toggleAcc = index => {
        document.querySelectorAll('.acc-item')[index]?.classList.toggle('active');
    };
});
