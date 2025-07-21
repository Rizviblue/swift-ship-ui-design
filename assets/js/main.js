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
    $('.btn-danger, .text-danger, a[href*="delete"]').on('click', function(e) {
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

    // DataTables initialization for large tables
    if ($('.data-table').length) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            language: {
                search: "Search records:",
                lengthMenu: "Show _MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ records",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }

    // Auto-complete for tracking numbers
    $('#tracking_number').on('input', function() {
        var value = $(this).val();
        if (value.length >= 3) {
            // AJAX call to get suggestions
            // Implementation would depend on backend API
        }
    });

    // Status update confirmation
    $('.status-update').on('change', function() {
        var newStatus = $(this).val();
        var trackingNumber = $(this).data('tracking');
        
        if (confirm('Are you sure you want to update the status to "' + newStatus + '" for tracking number ' + trackingNumber + '?')) {
            // Submit the form or make AJAX call
            $(this).closest('form').submit();
        } else {
            // Reset to previous value
            $(this).val($(this).data('previous-value'));
        }
    });

    // Dynamic form validation
    $('.required-field').on('blur', function() {
        if ($(this).val() === '') {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });

    // Phone number formatting
    $('input[type="tel"]').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        var formattedValue = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
        $(this).val(formattedValue);
    });
});

// Utility Functions
function tableToCSV(table) {
    var csv = [];
    var rows = table.find('tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = $(rows[i]).find('td, th');
        
        for (var j = 0; j < cols.length - 1; j++) { // Exclude last column (actions)
            var cellText = $(cols[j]).text().trim();
            row.push('"' + cellText.replace(/"/g, '""') + '"');
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

// Real-time status updates
function updateCourierStatus(courierId, newStatus, notes = '') {
    makeAjaxRequest(
        'ajax/update-status.php',
        'POST',
        {
            courier_id: courierId,
            status: newStatus,
            notes: notes
        },
        function(response) {
            if (response.success) {
                showToast('Status updated successfully!', 'success');
                location.reload();
            } else {
                showToast('Error updating status: ' + response.message, 'danger');
            }
        }
    );
}

// Print courier details
function printCourierDetails(courierId) {
    var printWindow = window.open('print-courier.php?id=' + courierId, '_blank');
    printWindow.onload = function() {
        printWindow.print();
    };
}

// Bulk actions
function bulkAction(action, selectedIds) {
    if (selectedIds.length === 0) {
        showToast('Please select at least one item.', 'warning');
        return;
    }
    
    if (confirm('Are you sure you want to ' + action + ' ' + selectedIds.length + ' item(s)?')) {
        makeAjaxRequest(
            'ajax/bulk-action.php',
            'POST',
            {
                action: action,
                ids: selectedIds
            },
            function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    location.reload();
                } else {
                    showToast('Error: ' + response.message, 'danger');
                }
            }
        );
    }
}