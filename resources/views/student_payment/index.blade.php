@extends('layouts.app')

@section('title', 'Student Payments')
@section('page-title', 'Student Payments')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Student Payments</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2"
                    style="background: linear-gradient(45deg, #4e73df, #2e59d9);">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                    Total Students</div>
                                <div class="h5 mb-0 font-weight-bold text-white" id="totalStudents">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2"
                    style="background: linear-gradient(45deg, #1cc88a, #17a673);">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                    Total Payments</div>
                                <div class="h5 mb-0 font-weight-bold text-white" id="totalPayments">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2"
                    style="background: linear-gradient(45deg, #36b9cc, #2c9faf);">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                    Total Amount</div>
                                <div class="h5 mb-0 font-weight-bold text-white" id="totalAmount">Rs. 0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rupee-sign fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2"
                    style="background: linear-gradient(45deg, #f6c23e, #f4b619);">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                    Classes</div>
                                <div class="h5 mb-0 font-weight-bold text-white" id="totalClasses">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chalkboard-teacher fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Filter Payments
                </h6>
                <div class="mt-2 mt-md-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="resetDate">
                        <i class="fas fa-redo me-1"></i>Reset
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="payment_date" class="form-label fw-bold">Select Date</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" class="form-control" id="payment_date" name="payment_date"
                                value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary w-100" id="loadPayments">
                            <i class="fas fa-search me-1"></i> View
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info w-100" id="printPayments">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-info mb-0 p-2">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                Showing payments for: <span id="currentDateLabel"
                                    class="fw-bold">{{ date('F d, Y') }}</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table me-2"></i>Payment Details
                </h6>
                <span class="badge bg-primary" id="summaryBadge">
                    <i class="fas fa-chart-bar me-1"></i>
                    <span id="tableSummary">No data</span>
                </span>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive" id="paymentsTableContainer" style="max-height: 500px; overflow-y: auto;">
                    <!-- Table will be loaded here by JavaScript -->
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar fa-2x mb-3 opacity-25"></i>
                        <h6 class="text-secondary">Select a date to view payments</h6>
                        <p class="text-muted small">Click "View Payments" to load payment details for the selected date</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card {
            border-radius: 0.35rem;
            border: none;
        }

        .card-header {
            border-radius: 0.35rem 0.35rem 0 0 !important;
            padding: 0.75rem 1.25rem;
        }

        .card-body {
            padding: 1rem;
        }

        /* Compact Table Styling */
        .payment-cell {
            min-width: 140px;
            vertical-align: top;
            padding: 6px 8px !important;
        }

        .payment-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            border-radius: 6px;
            padding: 6px 8px;
            border-left: 3px solid #4e73df;
            font-size: 0.75rem;
            margin-bottom: 4px;
            transition: all 0.2s ease;
        }

        .payment-item:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
            transform: translateX(2px);
        }

        .table {
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
            padding: 8px 10px !important;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
        }

        .table-bordered {
            border-color: #e3e6f0;
        }

        .table-bordered th,
        .table-bordered td {
            border-color: #e3e6f0;
        }

        .class-header {
            background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
            font-weight: 600;
            position: sticky;
            left: 0;
            z-index: 5;
            padding: 8px 10px !important;
            font-size: 0.75rem;
            border-right: 2px solid #dee2e6;
        }

        .student-header {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            min-width: 130px;
            font-size: 0.75rem;
            vertical-align: middle;
        }

        .student-header div {
            font-size: 0.8rem;
            line-height: 1.2;
            font-weight: 600;
            color: #1a237e;
        }

        .student-header small {
            font-size: 0.7rem;
            line-height: 1.1;
            color: #283593;
        }

        .total-cell {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%) !important;
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            position: sticky;
            right: 0;
            z-index: 5;
        }

        .grand-total {
            background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%) !important;
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .amount-badge {
            background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .payment-for-badge {
            background-color: #6c757d;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.65rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .payment-cell {
                min-width: 120px;
            }

            .table {
                font-size: 0.7rem;
            }

            .student-header {
                min-width: 110px;
            }

            .payment-item {
                padding: 4px 6px;
                font-size: 0.7rem;
            }
        }

        /* Form and button adjustments */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25em 0.6em;
        }

        /* Alert text size */
        .alert {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #4e73df;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Print styles */
        @media print {

            .card-header .btn,
            .filter-card,
            .breadcrumb,
            #printPayments,
            #resetDate,
            #loadPayments {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            .table thead th {
                background: #4e73df !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .total-cell,
            .grand-total {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get DOM elements
            const paymentDateInput = document.getElementById('payment_date');
            const loadPaymentsBtn = document.getElementById('loadPayments');
            const resetDateBtn = document.getElementById('resetDate');
            const printPaymentsBtn = document.getElementById('printPayments');
            const paymentsTableContainer = document.getElementById('paymentsTableContainer');
            const currentDateLabel = document.getElementById('currentDateLabel');
            const tableSummary = document.getElementById('tableSummary');

            // Stats elements
            const totalStudentsEl = document.getElementById('totalStudents');
            const totalPaymentsEl = document.getElementById('totalPayments');
            const totalAmountEl = document.getElementById('totalAmount');
            const totalClassesEl = document.getElementById('totalClasses');

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Store current data for printing
            let currentPaymentData = [];

            // Format date functions
            function formatDate(date) {
                const d = new Date(date);
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function formatDateDisplay(date) {
                const d = new Date(date);
                return d.toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });
            }

            // Update date label
            function updateDateLabel(date) {
                currentDateLabel.textContent = formatDateDisplay(date);
            }

            // Update stats cards
            function updateStats(data) {
                if (data.status === 'success') {
                    const payments = data.data || [];
                    const uniqueStudents = new Map();
                    const uniqueClasses = new Set();
                    let totalAmount = 0;

                    payments.forEach(payment => {
                        if (payment.student?.custom_id) {
                            uniqueStudents.set(payment.student.custom_id, payment.student.full_name);
                        }

                        if (payment.student_class?.class_name) {
                            uniqueClasses.add(payment.student_class.class_name);
                        }

                        totalAmount += parseFloat(payment.amount) || 0;
                    });

                    totalStudentsEl.textContent = uniqueStudents.size;
                    totalPaymentsEl.textContent = payments.length;
                    totalAmountEl.textContent = `Rs. ${totalAmount.toLocaleString('en-IN')}`;
                    totalClassesEl.textContent = uniqueClasses.size;
                    tableSummary.textContent = `${payments.length} payment${payments.length !== 1 ? 's' : ''} • ${uniqueStudents.size} student${uniqueStudents.size !== 1 ? 's' : ''}`;
                } else {
                    totalStudentsEl.textContent = '0';
                    totalPaymentsEl.textContent = '0';
                    totalAmountEl.textContent = 'Rs. 0';
                    totalClassesEl.textContent = '0';
                    tableSummary.textContent = 'No data';
                }
            }

            // Show loading indicator
            function showLoading() {
                paymentsTableContainer.innerHTML = `
                        <div class="text-center py-5">
                            <div class="loading-spinner mb-3"></div>
                            <p class="text-primary mb-0">Loading payment details...</p>
                            <small class="text-muted">Please wait</small>
                        </div>
                    `;
            }

            // Show error message
            function showError(message) {
                paymentsTableContainer.innerHTML = `
                        <div class="alert alert-danger text-center m-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> ${message}
                            <button class="btn btn-sm btn-outline-danger mt-2" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-1"></i>Retry
                            </button>
                        </div>
                    `;
            }

            // Load payments for selected date
            function loadPayments(date) {
                showLoading();

                // Make API request using fetch
                fetch(`/api/payments/by-date/${date}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Network response was not ok');
                        }
                        return data;
                    })
                    .then(data => {
                        updateStats(data);

                        if (data.status === 'success' && data.data && data.data.length > 0) {
                            currentPaymentData = data.data;
                            renderTable(data.data);
                        } else {
                            currentPaymentData = [];
                            paymentsTableContainer.innerHTML = `
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-receipt fa-3x mb-3 opacity-25"></i>
                                        <h6 class="text-secondary mb-2">No payments found</h6>
                                        <p class="text-muted small mb-0">No payments were recorded for ${formatDateDisplay(date)}</p>
                                    </div>
                                `;
                            tableSummary.textContent = 'No data available';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError(error.message || 'Failed to load payment data. Please try again.');
                        tableSummary.textContent = 'Error loading data';
                        // Reset stats on error
                        totalStudentsEl.textContent = '0';
                        totalPaymentsEl.textContent = '0';
                        totalAmountEl.textContent = 'Rs. 0';
                        totalClassesEl.textContent = '0';
                        currentPaymentData = [];
                    });
            }

            // Render the table with data
            function renderTable(data) {
                // Group data by class and student
                const groupedData = {};
                const students = new Map();
                const classes = new Map();

                // Process the data
                data.forEach(payment => {
                    const className = payment.student_class?.class_name || 'Unassigned';
                    const studentId = payment.student?.custom_id || 'Unknown';
                    const studentName = payment.student ?
                        `${payment.student.initial_name || payment.student.full_name}` :
                        'Unknown Student';

                    // Add to students map
                    if (!students.has(studentId)) {
                        students.set(studentId, {
                            id: studentId,
                            name: studentName,
                            total: 0
                        });
                    }

                    // Add to classes map
                    if (!classes.has(className)) {
                        classes.set(className, {
                            name: className,
                            total: 0
                        });
                    }

                    // Group data by class and student
                    if (!groupedData[className]) {
                        groupedData[className] = {};
                    }

                    if (!groupedData[className][studentId]) {
                        groupedData[className][studentId] = [];
                    }

                    groupedData[className][studentId].push({
                        amount: parseFloat(payment.amount) || 0,
                        payment_for: payment.payment_for || 'N/A',
                        id: payment.id,
                        payment_date: payment.payment_date
                    });

                    // Calculate totals
                    const amount = parseFloat(payment.amount) || 0;
                    students.get(studentId).total += amount;
                    classes.get(className).total += amount;
                });

                // Convert maps to arrays for ordered display
                const studentsArray = Array.from(students.entries()).sort((a, b) => a[0].localeCompare(b[0]));
                const classesArray = Array.from(classes.entries()).sort((a, b) => a[0].localeCompare(b[0]));

                // Create table HTML
                let html = `
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="paymentsTable">
                                <thead>
                                    <tr>
                                        <th class="class-header" style="min-width: 180px;">
                                            <i class="fas fa-chalkboard me-2"></i>Class / Student
                                        </th>`;

                // Add student headers
                studentsArray.forEach(([studentId, student]) => {
                    html += `<th class="text-center student-header">
                            <div>${studentId}</div>
                            <small>${student.name.length > 25 ? student.name.substring(0, 22) + '...' : student.name}</small>
                        </th>`;
                });

                html += `<th class="text-center total-cell">
                        <i class="fas fa-calculator me-1"></i>Class Total
                    </th>
                                </tr>
                            </thead>
                            <tbody>`;

                // Add rows for each class
                classesArray.forEach(([className, classInfo]) => {
                    html += `<tr>
                            <td class="class-header fw-bold">
                                <i class="fas fa-graduation-cap me-2"></i>${className}
                            </td>`;

                    let classRowTotal = 0;

                    studentsArray.forEach(([studentId]) => {
                        const payments = groupedData[className] ? (groupedData[className][studentId] || []) : [];
                        html += `<td class="payment-cell">`;

                        if (payments.length > 0) {
                            let studentClassTotal = 0;
                            payments.forEach(payment => {
                                html += `
                                        <div class="payment-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="payment-for-badge">
                                                    <i class="fas fa-tag me-1"></i>${payment.payment_for}
                                                </span>
                                                <span class="amount-badge">
                                                    <i class="fas fa-rupee-sign me-1"></i>${payment.amount.toLocaleString('en-IN')}
                                                </span>
                                            </div>
                                            <div class="text-muted mt-1" style="font-size: 0.65rem;">
                                                <i class="fas fa-clock me-1"></i>
                                                ${new Date(payment.payment_date).toLocaleTimeString()}
                                            </div>
                                        </div>`;
                                studentClassTotal += payment.amount;
                            });
                            // Add student's total for this class
                            html += `<div class="mt-2 pt-2 border-top text-end">
                                    <small class="text-success fw-bold">
                                        <i class="fas fa-coins me-1"></i>
                                        Total: <span class="text-dark">Rs. ${studentClassTotal.toLocaleString('en-IN')}</span>
                                    </small>
                                </div>`;

                            classRowTotal += studentClassTotal;
                        } else {
                            html += `<div class="text-center text-muted py-2">
                                    <i class="fas fa-minus-circle"></i>
                                    <div class="mt-1 small">No payments</div>
                                </div>`;
                        }

                        html += `</td>`;
                    });

                    // Class total column
                    html += `<td class="total-cell text-center fw-bold">
                            <i class="fas fa-money-bill-wave me-1"></i>
                            Rs. ${classInfo.total.toLocaleString('en-IN')}
                        </td>`;
                    html += `</tr>`;
                });

                // Add totals row
                html += `<tr class="table-primary">
                        <td class="fw-bold grand-total">
                            <i class="fas fa-chart-line me-2"></i>Student Total
                        </td>`;

                let grandTotal = 0;
                studentsArray.forEach(([studentId, student]) => {
                    html += `<td class="text-center fw-bold grand-total">
                            <i class="fas fa-user-check me-1"></i>
                            Rs. ${student.total.toLocaleString('en-IN')}
                        </td>`;
                    grandTotal += student.total;
                });

                // Grand total column
                html += `<td class="text-center fw-bold grand-total">
                        <i class="fas fa-trophy me-1"></i>
                        Rs. ${grandTotal.toLocaleString('en-IN')}
                    </td>`;
                html += `</tr>
                            </tbody>
                        </table>
                    </div>`;

                paymentsTableContainer.innerHTML = html;
            }

            // Print functionality
            function printPayments() {
                if (!currentPaymentData || currentPaymentData.length === 0) {
                    alert('No data to print. Please load payment data first.');
                    return;
                }

                const printWindow = window.open('', '_blank');
                const selectedDate = paymentDateInput.value;

                printWindow.document.write(`
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Payment Report - ${formatDateDisplay(selectedDate)}</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    margin: 20px;
                                    padding: 0;
                                }
                                .header {
                                    text-align: center;
                                    margin-bottom: 20px;
                                    padding-bottom: 10px;
                                    border-bottom: 2px solid #4e73df;
                                }
                                .header h1 {
                                    color: #4e73df;
                                    margin: 0;
                                }
                                .header p {
                                    margin: 5px 0;
                                    color: #666;
                                }
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    font-size: 12px;
                                }
                                th, td {
                                    border: 1px solid #ddd;
                                    padding: 8px;
                                    text-align: left;
                                }
                                th {
                                    background-color: #4e73df;
                                    color: white;
                                }
                                .total-row {
                                    background-color: #f8f9fc;
                                    font-weight: bold;
                                }
                                .grand-total {
                                    background-color: #4e73df;
                                    color: white;
                                }
                                .footer {
                                    margin-top: 20px;
                                    text-align: center;
                                    font-size: 10px;
                                    color: #666;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h1>Student Payments Report</h1>
                                <p>Date: ${formatDateDisplay(selectedDate)}</p>
                                <p>Generated on: ${new Date().toLocaleString()}</p>
                            </div>
                            <div id="printContent"></div>
                            <div class="footer">
                                <p>This is a system-generated report. For queries, please contact the administration.</p>
                            </div>
                            <script>
                                // Clone the table for printing
                                const tableContainer = document.querySelector('#paymentsTableContainer .table-responsive');
                                const printContent = document.getElementById('printContent');
                                if (tableContainer) {
                                    printContent.innerHTML = tableContainer.innerHTML;
                                }
                            <\/script>
                        </body>
                        </html>
                    `);

                printWindow.document.close();
                setTimeout(() => {
                    printWindow.print();
                }, 500);
            }

            // Event Listeners
            loadPaymentsBtn.addEventListener('click', function () {
                const selectedDate = paymentDateInput.value;
                if (selectedDate) {
                    updateDateLabel(selectedDate);
                    loadPayments(selectedDate);
                } else {
                    alert('Please select a date');
                }
            });

            resetDateBtn.addEventListener('click', function () {
                const today = new Date();
                const formattedDate = formatDate(today);
                paymentDateInput.value = formattedDate;
                updateDateLabel(today);
                loadPayments(formattedDate);
            });

            printPaymentsBtn.addEventListener('click', printPayments);

            // Date input change
            paymentDateInput.addEventListener('change', function () {
                if (this.value) {
                    updateDateLabel(this.value);
                }
            });

            // Keyboard support
            paymentDateInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    loadPaymentsBtn.click();
                }
            });

            // Auto-load for current date on page load
            const today = new Date();
            const formattedDate = formatDate(today);
            paymentDateInput.value = formattedDate;
            updateDateLabel(today);

            // Load payments on page load
            loadPayments(formattedDate);
        });
    </script>
@endpush