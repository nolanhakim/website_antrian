import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;
window.Swal = Swal;

window.confirmAction = function(btn, options) {
    if (!window.Swal) {
        if (confirm(options.title || 'Lanjutkan?')) {
            btn.closest('form').submit();
        }
        return;
    }
    window.Swal.fire({
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        ...options,
    }).then(function(result) {
        if (result.isConfirmed) {
            btn.closest('form').submit();
        }
    });
};

Alpine.start();
