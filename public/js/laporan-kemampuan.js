/**
 * Laporan Kemampuan Siswa - Custom JavaScript
 */

$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-toggle="popover"]').popover();
    
    // Add loading states to buttons (only for form buttons, not links, and exclude collapse buttons)
    $('.btn:not(a):not([data-toggle="collapse"]):not([data-target]):not([aria-controls])').on('click', function() {
        if ($(this).hasClass('btn-loading')) {
            return false;
        }
        
        $(this).addClass('btn-loading');
        $(this).prop('disabled', true);
        
        // Remove loading state after 3 seconds (fallback)
        setTimeout(() => {
            $(this).removeClass('btn-loading');
            $(this).prop('disabled', false);
        }, 3000);
    });
    
    // Form validation (exclude laporan forms)
    $('form:not([action*="laporan-kemampuan"])').on('submit', function(e) {
        const form = $(this);
        const requiredFields = form.find('[required]');
        let isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showAlert('error', 'Mohon lengkapi semua field yang wajib diisi.');
        }
    });
    
    // Search functionality for student selection
    $('#siswa-search').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.siswa-card').each(function() {
            const studentName = $(this).find('.siswa-name').text().toLowerCase();
            const studentEmail = $(this).find('.siswa-email').text().toLowerCase();
            
            if (studentName.includes(searchTerm) || studentEmail.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Filter functionality for package selection
    $('#paket-filter').on('change', function() {
        const filterValue = $(this).val();
        $('.paket-card').each(function() {
            if (filterValue === '' || $(this).data('paket') === filterValue) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Chart initialization for summary cards
    if (typeof Chart !== 'undefined') {
        initializeCharts();
    }
    
    // Print functionality
    window.printLaporan = function() {
        window.print();
    };
    
    // Export functionality
    window.exportLaporan = function(format) {
        if (format === 'pdf') {
            exportToPDF();
        } else if (format === 'excel') {
            exportToExcel();
        }
    };
    
    // Auto-refresh data every 5 minutes
    setInterval(function() {
        if (window.location.pathname.includes('laporan-kemampuan')) {
            refreshData();
        }
    }, 300000); // 5 minutes
});

/**
 * Initialize charts for summary data
 */
function initializeCharts() {
    // Summary chart for overall performance
    const ctx = document.getElementById('summaryChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Meningkat', 'Menurun', 'Stagnan'],
                datasets: [{
                    data: [12, 3, 5],
                    backgroundColor: ['#1ab394', '#ed5565', '#f8ac59'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Progress chart for individual student
    const progressCtx = document.getElementById('progressChart');
    if (progressCtx) {
        new Chart(progressCtx, {
            type: 'line',
            data: {
                labels: ['Tes 1', 'Tes 2', 'Tes 3', 'Tes 4', 'Tes 5'],
                datasets: [{
                    label: 'Skor',
                    data: [65, 72, 68, 75, 80],
                    borderColor: '#1ab394',
                    backgroundColor: 'rgba(26, 179, 148, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }
}

/**
 * Show alert message
 */
function showAlert(type, message) {
    const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.ibox-content').prepend(alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

/**
 * Export to PDF
 */
function exportToPDF() {
    const element = document.getElementById('laporan-content');
    const opt = {
        margin: 1,
        filename: 'laporan-kemampuan-siswa.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(element).save();
}

/**
 * Export to Excel
 */
function exportToExcel() {
    const table = document.getElementById('laporan-table');
    if (table) {
        const wb = XLSX.utils.table_to_book(table);
        XLSX.writeFile(wb, 'laporan-kemampuan-siswa.xlsx');
    }
}

/**
 * Refresh data
 */
function refreshData() {
    if (window.location.pathname.includes('laporan-kemampuan')) {
        location.reload();
    }
}

/**
 * Format number with thousand separator
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

/**
 * Format date to Indonesian format
 */
function formatDate(date) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return new Date(date).toLocaleDateString('id-ID', options);
}

/**
 * Calculate percentage change
 */
function calculatePercentageChange(oldValue, newValue) {
    if (oldValue === 0) return 0;
    return ((newValue - oldValue) / oldValue) * 100;
}

/**
 * Get performance status based on score change
 */
function getPerformanceStatus(change) {
    if (change > 0) {
        return { status: 'improved', class: 'success', icon: 'fa-arrow-up' };
    } else if (change < 0) {
        return { status: 'declined', class: 'danger', icon: 'fa-arrow-down' };
    } else {
        return { status: 'stable', class: 'warning', icon: 'fa-minus' };
    }
}

/**
 * Generate recommendations based on analysis
 */
function generateRecommendations(analysis) {
    const recommendations = [];
    
    analysis.forEach(item => {
        if (item.selisih_skor > 0) {
            recommendations.push({
                type: 'success',
                message: `Kategori ${item.kategori.nama} menunjukkan peningkatan yang baik. Pertahankan konsistensi latihan.`
            });
        } else if (item.selisih_skor < 0) {
            recommendations.push({
                type: 'danger',
                message: `Kategori ${item.kategori.nama} mengalami penurunan. Perbanyak latihan dan fokus pada area yang lemah.`
            });
        } else {
            recommendations.push({
                type: 'warning',
                message: `Kategori ${item.kategori.nama} stagnan. Coba variasi metode belajar.`
            });
        }
    });
    
    return recommendations;
}

/**
 * Initialize data tables
 */
function initializeDataTables() {
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            }
        });
    }
}

/**
 * Initialize select2 for better UX
 */
function initializeSelect2() {
    if ($.fn.select2) {
        $('.select2').select2({
            placeholder: 'Pilih...',
            allowClear: true,
            width: '100%'
        });
    }
}

// Initialize additional features when document is ready
$(document).ready(function() {
    initializeDataTables();
    initializeSelect2();
});
