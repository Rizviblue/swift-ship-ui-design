// CourierPro Main JavaScript

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Form validation
    $('form').on('submit', function(e) {
        var form = this;
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Confirm delete actions
    $('.btn-danger, .text-danger').on('click', function(e) {
        if ($(this).attr('href') && $(this).attr('href').includes('delete')) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        }
    });

    // Search functionality
    $('#search').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Status filter
    $('#status').on('change', function() {
        var status = $(this).val();
        if (status === '') {
            $('table tbody tr').show();
        } else {
            $('table tbody tr').hide();
            $('table tbody tr').filter(function() {
                return $(this).find('.badge').text().toLowerCase().includes(status.replace('-', ' '));
            }).show();
        }
    });

    // Auto-refresh for tracking page
    if (window.location.pathname.includes('track')) {
        setInterval(function() {
            // Auto-refresh tracking information every 30 seconds
            // This would typically make an AJAX call to update status
        }, 30000);
    }

    // Print functionality
    $('.btn-print').on('click', function() {
        window.print();
    });

    // Export functionality
    $('.btn-export').on('click', function() {
        var table = $(this).closest('.card').find('table');
        var csv = tableToCSV(table);
        downloadCSV(csv, 'export.csv');
    });

    // Sidebar toggle for mobile
    $('.sidebar-toggle').on('click', function() {
        $('.sidebar').toggleClass('show');
    });

    // Loading states
    $('form').on('submit', function() {
        $(this).find('button[type="submit"]').addClass('loading').prop('disabled', true);
    });

    // Real-time notifications (placeholder)
    function checkNotifications() {
        // This would typically make an AJAX call to check for new notifications
        // For now, it's just a placeholder
    }

    // Check for notifications every minute
    setInterval(checkNotifications, 60000);
});

// Utility Functions
function tableToCSV(table) {
    var csv = [];
    var rows = table.find('tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = $(rows[i]).find('td, th');
        
        for (var j = 0; j < cols.length; j++) {
            row.push('"' + $(cols[j]).text().trim() + '"');
        }
        
        csv.push(row.join(','));
    }
    
    return csv.join('\n');
}

function downloadCSV(csv, filename) {
    var csvFile = new Blob([csv], {type: 'text/csv'});
    var downloadLink = document.createElement('a');
    
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Generate tracking number
function generateTrackingNumber() {
    var prefix = 'CP';
    var timestamp = Date.now().toString().slice(-8);
    return prefix + timestamp;
}

// Format currency
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

// Format date
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Show loading spinner
function showLoading(element) {
    $(element).addClass('loading');
}

// Hide loading spinner
function hideLoading(element) {
    $(element).removeClass('loading');
}

// Show toast notification
function showToast(message, type = 'info') {
    var toast = $('<div class="toast align-items-center text-white bg-' + type + ' border-0" role="alert">')
        .append('<div class="d-flex">')
        .append('<div class="toast-body">' + message + '</div>')
        .append('<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>')
        .append('</div>');
    
    $('.toast-container').append(toast);
    var bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
}

// AJAX helper function
function makeAjaxRequest(url, method, data, successCallback, errorCallback) {
    $.ajax({
        url: url,
        method: method,
        data: data,
        dataType: 'json',
        success: function(response) {
            if (successCallback) successCallback(response);
        },
        error: function(xhr, status, error) {
            if (errorCallback) errorCallback(xhr, status, error);
            else showToast('An error occurred: ' + error, 'danger');
        }
    });
}