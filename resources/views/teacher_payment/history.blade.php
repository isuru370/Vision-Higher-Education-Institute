@extends('layouts.app')

@section('title', 'Teacher Salary History')
@section('page-title', 'Teacher Salary History')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher_payment.index') }}">Teacher Payments</a></li>
    <li class="breadcrumb-item active">Teacher Salary History</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <!-- Header Card -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 50px; height: 50px;">
                                        <i class="fas fa-history text-white" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 fw-bold">Salary History</h4>
                                        <small class="text-muted">View past salary payments for teacher</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="badge bg-light text-dark border py-2 px-3 rounded">
                                    <i class="fas fa-user-graduate me-1"></i>
                                    Teacher ID: <span id="teacherIdDisplay">{{ $teacher_id ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Info Card -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="mb-3">
                                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                        style="width: 80px; height: 80px;">
                                        <i class="fas fa-user-tie text-white" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="text-muted small mb-1 d-block">Teacher Name</label>
                                            <h5 class="fw-bold mb-0" id="teacherName">-</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="text-muted small mb-1 d-block">Teacher ID</label>
                                            <p class="fw-bold mb-0" id="teacherId">{{ $teacher_id ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="text-muted small mb-1 d-block">Subject</label>
                                            <p class="fw-bold mb-0" id="subjectName">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <label class="text-muted small mb-1 d-block">Overall Status</label>
                                                <span class="badge bg-success px-3 py-2 rounded"
                                                    id="overallStatus">Active</span>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted">Last Updated: <span
                                                        id="lastUpdated">-</span></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Month Selection Card -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted mb-2">Select Month</label>
                                    <select class="form-select" id="monthSelect" name="month">
                                        @php
                                            $currentMonth = date('m');
                                            $defaultMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
                                        @endphp
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $i == $defaultMonth ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted mb-2">Select Year</label>
                                    <select class="form-select" id="yearSelect" name="year">
                                        @php
                                            $currentYear = date('Y');
                                            $currentMonth = date('m');
                                            $defaultYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
                                        @endphp
                                        @for($year = date('Y'); $year >= 2020; $year--)
                                            <option value="{{ $year }}" {{ $year == $defaultYear ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="badge bg-primary text-white py-3 px-4 rounded mb-2">
                                        <h5 class="mb-0" id="selectedMonthYear">{{ date('F Y', strtotime('-1 month')) }}
                                        </h5>
                                    </div>
                                    <small class="text-muted d-block">Viewing salary data for selected month</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salary Summary Cards -->
        <div class="row mb-3">
            <!-- Total Collections -->
            <div class="col-md-3 mb-3">
                <div class="card border-0 border-start border-primary border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-money-bill-wave text-primary" style="font-size: 1rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 small text-muted">Total Collections</h6>
                                <h4 class="fw-bold text-primary mb-0" id="totalCollections">LKR 0.00</h4>
                            </div>
                        </div>
                        <small class="text-muted">Total student payments for the month</small>
                    </div>
                </div>
            </div>

            <!-- Teacher's Share -->
            <div class="col-md-3 mb-3">
                <div class="card border-0 border-start border-success border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-user-tie text-success" style="font-size: 1rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 small text-muted">Teacher's Share</h6>
                                <h4 class="fw-bold text-success mb-0" id="teacherShare">LKR 0.00</h4>
                            </div>
                        </div>
                        <small class="text-muted">Teacher's percentage of total collections</small>
                    </div>
                </div>
            </div>

            <!-- Salary Paid -->
            <div class="col-md-3 mb-3">
                <div class="card border-0 border-start border-info border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-money-check-alt text-info" style="font-size: 1rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 small text-muted">Salary Paid</h6>
                                <h4 class="fw-bold text-info mb-0" id="salaryPaid">LKR 0.00</h4>
                            </div>
                        </div>
                        <small class="text-muted">Salary already paid for this month</small>
                    </div>
                </div>
            </div>

            <!-- Advance Payments -->
            <div class="col-md-3 mb-3">
                <div class="card border-0 border-start border-warning border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-hand-holding-usd text-warning" style="font-size: 1rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 small text-muted">Advance Payments</h6>
                                <h4 class="fw-bold text-warning mb-0" id="advancePayments">LKR 0.00</h4>
                            </div>
                        </div>
                        <small class="text-muted">Payments made in advance</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Summary Row -->
        <div class="row mb-3">
            <!-- Institution Income -->
            <div class="col-md-4 mb-3">
                <div class="card border-0 border-start border-secondary border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-building text-secondary" style="font-size: 1rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 small text-muted">Institution Income</h6>
                                <h4 class="fw-bold text-secondary mb-0" id="institutionIncome">LKR 0.00</h4>
                            </div>
                        </div>
                        <small class="text-muted">Institution's share from collections</small>
                    </div>
                </div>
            </div>

            <!-- Net Payable -->
            <div class="col-md-4 mb-3">
                <div class="card border-0 border-start border-success border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-rupee-sign text-success" style="font-size: 1rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 small text-muted">Net Payable</h6>
                                <h4 class="fw-bold text-success mb-0" id="netPayable">LKR 0.00</h4>
                            </div>
                        </div>
                        <small class="text-muted">(Teacher's Share - Salary Paid - Advance)</small>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="col-md-4 mb-3">
                <div class="card border-0 border-start border-primary border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-credit-card text-primary" style="font-size: 1rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 small text-muted">Payment Status</h6>
                                <h4 class="fw-bold mb-0" id="paymentStatus">Pending</h4>
                            </div>
                        </div>
                        <small class="text-muted">Salary payment status for this month</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Action Card -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="fw-bold mb-2">Salary Payment Action</h6>
                                <p class="text-muted mb-0" id="paymentMessage">Click the button to process salary payment
                                    for this teacher.</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-success px-4 py-3" id="payTeacherBtn" disabled
                                    style="border-radius: 8px; font-size: 1.1rem;">
                                    <i class="fas fa-money-check-alt me-2"></i> Pay Salary
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes Summary -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <i class="fas fa-chalkboard-teacher text-white" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Classes Summary</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="classesCards">
                            <!-- Classes will be populated here -->
                            <div class="col-12 text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading classes...</span>
                                </div>
                                <p class="text-muted mt-2">Loading classes data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advance Payment History -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-2"
                                    style="width: 32px; height: 32px;">
                                    <i class="fas fa-history text-white" style="font-size: 0.9rem;"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">Advance Payment History</h6>
                            </div>
                            <div class="badge bg-light text-dark py-2 px-3">
                                <i class="fas fa-filter me-1"></i> Current Month
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless" id="advancePaymentsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small text-muted py-2">Date & Time</th>
                                        <th class="small text-muted py-2">Amount</th>
                                        <th class="small text-muted py-2">Reason Code</th>
                                        <th class="small text-muted py-2">Payment For</th>
                                        <th class="small text-muted py-2">Status</th>
                                        <th class="small text-muted py-2">Processed By</th>
                                    </tr>
                                </thead>
                                <tbody id="advancePaymentsTableBody">
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div id="advanceEmptyState" class="text-center d-none">
                            <div class="alert alert-light border py-5">
                                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                <h6 class="mb-1 text-muted">No Advance Payments</h6>
                                <p class="mb-0 small text-muted">No advance payments found for this teacher.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Payment Records -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                    style="width: 32px; height: 32px;">
                                    <i class="fas fa-table text-white" style="font-size: 0.9rem;"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">Detailed Payment Records</h6>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary px-3 py-1" id="exportTableExcelBtn">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </button>
                                <button class="btn btn-sm btn-outline-danger px-3 py-1" id="exportTablePdfBtn">
                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Loading Spinner -->
                        <div id="tableLoadingSpinner" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 small text-muted">Loading payment data...</p>
                        </div>

                        <!-- Table Container -->
                        <div class="d-none" id="tableContainer">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless" id="paymentTable">
                                    <thead class="bg-light" id="paymentTableHeader">
                                        <!-- Dynamic header will be populated here -->
                                    </thead>
                                    <tbody id="paymentTableBody">
                                        <!-- Data will be populated by JavaScript -->
                                    </tbody>
                                    <tfoot class="bg-light fw-bold" id="paymentTableFooter">
                                        <!-- Dynamic footer will be populated here -->
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div id="tableEmptyState" class="text-center d-none">
                            <div class="alert alert-light border py-5">
                                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                <h6 class="mb-1 text-muted">No Payment Data</h6>
                                <p class="mb-0 small text-muted">No payment records found for the selected month.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --info-color: #36b9cc;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 0.5rem;
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
        }

        .table {
            font-size: 0.85rem;
            margin-bottom: 0;
        }

        .table th {
            font-weight: 600;
            color: #5a5c69;
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            padding: 0.75rem 1rem;
        }

        .table td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e3e6f0;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
        }

        .btn {
            border-radius: 0.35rem;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.375rem 1rem;
            transition: all 0.3s ease;
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #17a673;
            border-color: #17a673;
            transform: translateY(-1px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }

        .btn-success:disabled {
            background-color: #b7e4d4;
            border-color: #b7e4d4;
            cursor: not-allowed;
        }

        .badge {
            font-size: 0.75em;
            font-weight: 600;
            padding: 0.35em 0.65em;
            border-radius: 0.35rem;
        }

        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
        }
    </style>
@endpush

@push('scripts')
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <!-- SheetJS for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- jsPDF for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
        (function () {
            'use strict';

            // ============================================
            // HELPER FUNCTION TO GET TEACHER ID FROM URL
            // ============================================
            function getTeacherIdFromUrl() {
                const pathParts = window.location.pathname.split('/').filter(part => part);
                console.log('Current path:', window.location.pathname);
                console.log('Path parts:', pathParts);

                // URL pattern: /teacher-payment/history/1
                // Teacher ID should be the last part
                const lastPart = pathParts[pathParts.length - 1];

                // Check if last part is a number
                if (lastPart && /^\d+$/.test(lastPart)) {
                    console.log('Teacher ID found in URL:', lastPart);
                    return lastPart;
                }

                // Fallback: try to find any numeric part
                for (let i = pathParts.length - 1; i >= 0; i--) {
                    if (/^\d+$/.test(pathParts[i])) {
                        console.log('Teacher ID found as numeric part:', pathParts[i]);
                        return pathParts[i];
                    }
                }

                // Try to get from the displayed element
                const teacherIdSpan = document.getElementById('teacherIdDisplay');
                if (teacherIdSpan && teacherIdSpan.textContent !== '-') {
                    const id = teacherIdSpan.textContent.trim();
                    if (/^\d+$/.test(id)) {
                        console.log('Teacher ID from display span:', id);
                        return id;
                    }
                }

                const teacherIdHidden = document.getElementById('teacherId');
                if (teacherIdHidden && teacherIdHidden.textContent !== '-') {
                    const id = teacherIdHidden.textContent.trim();
                    if (/^\d+$/.test(id)) {
                        console.log('Teacher ID from hidden span:', id);
                        return id;
                    }
                }

                console.warn('No teacher ID found in URL, using default');
                return '{{ $teacher_id ?? 0 }}';
            }

            const CONFIG = {
                API_TIMEOUT: 30000,
                MAX_RETRIES: 2,
                RETRY_DELAY: 1000,
                AUTO_CLOSE_TIMEOUT: 15000,
                TOAST_DURATION: 3000,
                PRINT_WINDOW_DELAY: 1000,
                REFRESH_DELAY: 2000
            };

            const state = {
                teacherData: null,
                allPayments: [],
                allGrades: [],
                currentFetchId: 0,
                abortController: null,
                isProcessingPayment: false
            };

            const elements = {
                teacherName: document.getElementById('teacherName'),
                teacherId: document.getElementById('teacherId'),
                teacherIdDisplay: document.getElementById('teacherIdDisplay'),
                subjectName: document.getElementById('subjectName'),
                selectedMonthYear: document.getElementById('selectedMonthYear'),
                totalCollections: document.getElementById('totalCollections'),
                teacherShare: document.getElementById('teacherShare'),
                salaryPaid: document.getElementById('salaryPaid'),
                advancePayments: document.getElementById('advancePayments'),
                institutionIncome: document.getElementById('institutionIncome'),
                netPayable: document.getElementById('netPayable'),
                paymentStatus: document.getElementById('paymentStatus'),
                paymentMessage: document.getElementById('paymentMessage'),
                payTeacherBtn: document.getElementById('payTeacherBtn'),
                classesCards: document.getElementById('classesCards'),
                advancePaymentsTableBody: document.getElementById('advancePaymentsTableBody'),
                advanceEmptyState: document.getElementById('advanceEmptyState'),
                paymentTableBody: document.getElementById('paymentTableBody'),
                paymentTableHeader: document.getElementById('paymentTableHeader'),
                paymentTableFooter: document.getElementById('paymentTableFooter'),
                tableLoadingSpinner: document.getElementById('tableLoadingSpinner'),
                tableContainer: document.getElementById('tableContainer'),
                tableEmptyState: document.getElementById('tableEmptyState'),
                monthSelect: document.getElementById('monthSelect'),
                yearSelect: document.getElementById('yearSelect'),
                exportTableExcelBtn: document.getElementById('exportTableExcelBtn'),
                exportTablePdfBtn: document.getElementById('exportTablePdfBtn'),
                lastUpdated: document.getElementById('lastUpdated'),
                overallStatus: document.getElementById('overallStatus')
            };

            const utils = {
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                // Get teacher ID from URL - FIXED
                teacherId: getTeacherIdFromUrl(),

                formatCurrency(amount) {
                    let numericAmount = amount;
                    if (typeof amount === 'string') {
                        numericAmount = amount.toString()
                            .replace(/[^\d.-]/g, '')
                            .replace(/,/g, '');
                    }
                    numericAmount = parseFloat(numericAmount);
                    if (isNaN(numericAmount) || numericAmount === null || numericAmount === undefined) {
                        numericAmount = 0;
                    }
                    return new Intl.NumberFormat('en-LK', {
                        style: 'currency',
                        currency: 'LKR',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(numericAmount);
                },

                formatNumber(num) {
                    if (num == null || num === '' || num === undefined) return '0';
                    const n = parseFloat(num);
                    if (isNaN(n)) return '0';
                    return new Intl.NumberFormat('en-LK', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(n);
                },

                formatDateTime(dateTimeString) {
                    try {
                        const date = new Date(dateTimeString);
                        return date.toLocaleString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                    } catch (error) {
                        console.warn('Invalid datetime format:', dateTimeString);
                        return dateTimeString;
                    }
                },

                formatDateTable(dateString) {
                    try {
                        const date = new Date(dateString);
                        return date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: '2-digit'
                        }).replace(/\//g, '/');
                    } catch (error) {
                        console.warn('Invalid date format for table:', dateString);
                        return dateString;
                    }
                },

                getMonthName(monthNumber) {
                    const months = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    const monthIndex = parseInt(monthNumber) - 1;
                    return months[monthIndex] || 'Unknown';
                },

                toNumber(value) {
                    if (value == null || value === '' || value === undefined) return 0;
                    if (typeof value === 'number') return value;
                    const cleaned = String(value)
                        .replace(/[^\d.-]/g, '')
                        .replace(/,/g, '');
                    const num = parseFloat(cleaned);
                    return isNaN(num) ? 0 : num;
                },

                toInt(value) {
                    return Math.floor(this.toNumber(value));
                },

                showToast(message, type = 'info') {
                    const toast = document.createElement('div');
                    const bgColor = {
                        success: '#1cc88a',
                        error: '#e74a3b',
                        warning: '#f6c23e',
                        info: '#36b9cc'
                    }[type] || '#36b9cc';

                    const icon = {
                        success: 'fa-check-circle',
                        error: 'fa-exclamation-circle',
                        warning: 'fa-exclamation-triangle',
                        info: 'fa-info-circle'
                    }[type] || 'fa-info-circle';

                    toast.style.cssText = `
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            background: ${bgColor};
                            color: white;
                            padding: 12px 20px;
                            border-radius: 8px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                            z-index: 999999;
                            animation: slideIn 0.3s ease-out;
                            font-size: 0.875rem;
                            font-weight: 500;
                        `;

                    toast.innerHTML = `
                            <div style="display: flex; align-items: center;">
                                <i class="fas ${icon} me-2"></i>
                                <span>${message}</span>
                            </div>
                        `;

                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.style.animation = 'slideOut 0.3s ease-out';
                        setTimeout(() => toast.remove(), 300);
                    }, CONFIG.TOAST_DURATION);

                    return toast;
                },

                getPreviousMonthYear() {
                    const now = new Date();
                    let month, year;

                    if (now.getMonth() === 0) {
                        month = '12';
                        year = (now.getFullYear() - 1).toString();
                    } else {
                        month = now.getMonth().toString().padStart(2, '0');
                        year = now.getFullYear().toString();
                    }

                    return { month, year };
                }
            };

            // Log the teacher ID to verify
            console.log('Teacher ID being used for API calls:', utils.teacherId);

            const ui = {
                showTableLoading(show) {
                    if (elements.tableLoadingSpinner) {
                        if (show) {
                            elements.tableLoadingSpinner.classList.remove('d-none');
                        } else {
                            elements.tableLoadingSpinner.classList.add('d-none');
                        }
                    }
                },

                showTableContainer(show) {
                    if (elements.tableContainer) {
                        if (show) {
                            elements.tableContainer.classList.remove('d-none');
                        } else {
                            elements.tableContainer.classList.add('d-none');
                        }
                    }
                },

                showTableEmptyState(show) {
                    if (elements.tableEmptyState) {
                        if (show) {
                            elements.tableEmptyState.classList.remove('d-none');
                        } else {
                            elements.tableEmptyState.classList.add('d-none');
                        }
                    }
                },

                showAdvanceEmptyState(show) {
                    if (elements.advanceEmptyState) {
                        if (show) {
                            elements.advanceEmptyState.classList.remove('d-none');
                        } else {
                            elements.advanceEmptyState.classList.add('d-none');
                        }
                    }
                },

                updateSelectedMonthYear(month, year) {
                    if (elements.selectedMonthYear) {
                        elements.selectedMonthYear.textContent =
                            `${utils.getMonthName(month)} ${year}`;
                    }
                },

                enablePayButton(enable) {
                    if (elements.payTeacherBtn) {
                        elements.payTeacherBtn.disabled = !enable;
                    }
                },

                setPayButtonLoading(loading) {
                    if (elements.payTeacherBtn) {
                        if (loading) {
                            elements.payTeacherBtn.disabled = true;
                            elements.payTeacherBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
                        } else {
                            elements.payTeacherBtn.innerHTML = '<i class="fas fa-money-check-alt me-2"></i> Pay Salary';
                        }
                    }
                },

                updateLastUpdated() {
                    if (elements.lastUpdated) {
                        const now = new Date();
                        elements.lastUpdated.textContent = now.toLocaleString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                }
            };

            async function fetchTeacherData(month, year) {
                const fetchId = ++state.currentFetchId;

                if (state.abortController) {
                    state.abortController.abort();
                }

                state.abortController = new AbortController();
                const timeoutId = setTimeout(() => {
                    state.abortController.abort();
                }, CONFIG.API_TIMEOUT);

                try {
                    ui.showTableLoading(true);
                    ui.showTableContainer(false);
                    ui.showTableEmptyState(false);

                    const url = `/api/teacher-payments/monthly-income/${utils.teacherId}/${year}-${month}`;
                    console.log('Fetching URL:', url);

                    const response = await fetch(url, {
                        signal: state.abortController.signal,
                        headers: {
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache'
                        }
                    });

                    clearTimeout(timeoutId);

                    if (fetchId !== state.currentFetchId) {
                        return;
                    }

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.status === 'success') {
                        state.teacherData = data.data;
                        renderAllData();
                    } else {
                        throw new Error(data.message || 'Failed to load teacher data');
                    }
                } catch (error) {
                    if (error.name === 'AbortError') {
                        console.log('Fetch aborted');
                        return;
                    }

                    console.error('Error fetching teacher data:', error);
                    ui.showTableEmptyState(true);
                    utils.showToast('Failed to load salary data. Please try again.', 'error');
                } finally {
                    clearTimeout(timeoutId);
                    ui.showTableLoading(false);
                }
            }

            function renderAllData() {
                if (!state.teacherData) return;

                renderTeacherInfo();
                renderFinancialSummary();
                renderPaymentStatus();
                renderClassesCards();
                renderAdvancePayments();
                renderPaymentTable();
                ui.updateLastUpdated();
            }

            function renderTeacherInfo() {
                if (!state.teacherData) return;

                const data = state.teacherData;

                if (elements.teacherName) elements.teacherName.textContent = data.teacher_name || '-';
                if (elements.teacherId) elements.teacherId.textContent = data.teacher_id || '-';
                if (elements.teacherIdDisplay) elements.teacherIdDisplay.textContent = data.teacher_id || '-';

                if (data.class_wise && data.class_wise.length > 0) {
                    const firstClass = data.class_wise[0];
                    if (elements.subjectName && firstClass.class_name) {
                        elements.subjectName.textContent = firstClass.class_name.split(' - ')[0] || '-';
                    }
                }
            }

            function renderFinancialSummary() {
                if (!state.teacherData) return;

                const data = state.teacherData;

                if (elements.totalCollections) {
                    elements.totalCollections.textContent = utils.formatCurrency(data.total_payments || 0);
                }

                if (elements.teacherShare) {
                    elements.teacherShare.textContent = utils.formatCurrency(data.teacher_share || 0);
                }

                if (elements.salaryPaid) {
                    elements.salaryPaid.textContent = utils.formatCurrency(data.salary_paid || 0);
                }

                if (elements.advancePayments) {
                    elements.advancePayments.textContent = utils.formatCurrency(data.advance_paid || 0);
                }

                if (elements.institutionIncome) {
                    elements.institutionIncome.textContent = utils.formatCurrency(data.institution_income || 0);
                }

                if (elements.netPayable) {
                    elements.netPayable.textContent = utils.formatCurrency(data.net_payable || 0);
                }
            }

            function renderPaymentStatus() {
                if (!state.teacherData) return;

                const data = state.teacherData;
                const netPayable = utils.toNumber(data.net_payable || 0);
                const salaryPaid = utils.toNumber(data.salary_paid || 0);

                let statusText = '';
                let statusColor = '';
                let buttonEnabled = false;
                let messageText = '';

                if (salaryPaid > 0) {
                    statusText = 'Paid';
                    statusColor = 'text-success';
                    buttonEnabled = false;
                    messageText = 'Salary has already been paid for this month.';
                } else if (netPayable <= 0) {
                    statusText = 'No Payment Due';
                    statusColor = 'text-secondary';
                    buttonEnabled = false;
                    messageText = 'No salary payment is due for this month.';
                } else {
                    statusText = 'Pending';
                    statusColor = 'text-warning';
                    buttonEnabled = true;
                    messageText = `Click the button to pay ${utils.formatCurrency(netPayable)} salary.`;
                }

                if (elements.paymentStatus) {
                    elements.paymentStatus.textContent = statusText;
                    elements.paymentStatus.className = `fw-bold mb-0 ${statusColor}`;
                }

                if (elements.paymentMessage) {
                    elements.paymentMessage.textContent = messageText;
                }

                ui.enablePayButton(buttonEnabled);
            }

            function renderClassesCards() {
                if (!state.teacherData || !elements.classesCards) return;

                elements.classesCards.innerHTML = '';

                if (!state.teacherData.class_wise || state.teacherData.class_wise.length === 0) {
                    elements.classesCards.innerHTML = `
                            <div class="col-12 text-center py-4">
                                <i class="fas fa-chalkboard-teacher fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No classes found for this teacher.</p>
                            </div>
                        `;
                    return;
                }

                state.teacherData.class_wise.forEach((cls, index) => {
                    const totalStudents = utils.toInt(cls.total_students || 0);
                    const paidStudents = utils.toInt(cls.paid_students || 0);
                    const unpaidStudents = utils.toInt(cls.unpaid_students || 0);
                    const freeStudents = utils.toInt(cls.free_students || 0);
                    const percentagePaid = totalStudents > 0 ? Math.round((paidStudents / totalStudents) * 100) : 0;

                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-4 mb-3';
                    card.innerHTML = `
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 small fw-bold text-truncate" title="${cls.class_name || 'Class'}">
                                            ${cls.class_name || 'Class'}
                                        </h6>
                                        <span class="badge bg-primary">${cls.teacher_percentage || 0}%</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted small">Students:</span>
                                            <span class="fw-bold small">${utils.formatNumber(totalStudents)}</span>
                                        </div>
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: ${percentagePaid}%"></div>
                                        </div>
                                        <div class="row small text-center">
                                            <div class="col-4">
                                                <span class="text-success fw-bold">${utils.formatNumber(paidStudents)}</span>
                                                <div class="text-muted">Paid</div>
                                            </div>
                                            <div class="col-4">
                                                <span class="text-danger fw-bold">${utils.formatNumber(unpaidStudents)}</span>
                                                <div class="text-muted">Unpaid</div>
                                            </div>
                                            <div class="col-4">
                                                <span class="text-info fw-bold">${utils.formatNumber(freeStudents)}</span>
                                                <div class="text-muted">Free</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top pt-2">
                                        <div class="mb-1">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted small">Total Amount:</span>
                                                <span class="fw-bold small">${utils.formatCurrency(cls.total_amount || 0)}</span>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted small">Teacher Earning:</span>
                                                <span class="fw-bold text-success small">${utils.formatCurrency(cls.teacher_earning || 0)}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted small">Institution Cut:</span>
                                                <span class="fw-bold text-secondary small">${utils.formatCurrency(cls.institution_cut || 0)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                    elements.classesCards.appendChild(card);
                });
            }

            function renderAdvancePayments() {
                if (!state.teacherData || !elements.advancePaymentsTableBody) return;

                elements.advancePaymentsTableBody.innerHTML = '';

                if (state.teacherData.advance_records &&
                    Array.isArray(state.teacherData.advance_records) &&
                    state.teacherData.advance_records.length > 0) {

                    state.teacherData.advance_records.forEach(record => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                                <td>${utils.formatDateTime(record.created_at || record.date)}</td>
                                <td class="fw-bold">${utils.formatCurrency(record.payment)}</td>
                                <td>
                                    <span class="badge bg-info">${record.reason_code || 'N/A'}</span>
                                    ${record.reason_detail ? `<br><small class="text-muted">${record.reason_detail}</small>` : ''}
                                </td>
                                <td>${record.payment_for || 'N/A'}</td>
                                <td>
                                    <span class="badge ${record.status ? 'bg-success' : 'bg-danger'}">
                                        ${record.status ? 'Active' : 'Deleted'}
                                    </span>
                                </td>
                                <td>${record.user_name || 'System'}</td>
                            `;
                        elements.advancePaymentsTableBody.appendChild(row);
                    });

                    ui.showAdvanceEmptyState(false);
                } else {
                    ui.showAdvanceEmptyState(true);
                }
            }

            function renderPaymentTable() {
                if (!state.teacherData || !elements.paymentTableBody) {
                    ui.showTableEmptyState(true);
                    ui.showTableContainer(false);
                    return;
                }

                elements.paymentTableBody.innerHTML = '';
                state.allPayments = [];
                state.allGrades = [];

                if (state.teacherData.class_wise && state.teacherData.class_wise.length > 0) {
                    state.teacherData.class_wise.forEach(cls => {
                        const gradeMatch = cls.class_name?.match(/Grade\s+(\d+)/i);
                        if (gradeMatch && !state.allGrades.includes(gradeMatch[1])) {
                            state.allGrades.push(gradeMatch[1]);
                        }
                    });
                    state.allGrades.sort();
                }

                if (state.allGrades.length === 0) {
                    ui.showTableEmptyState(true);
                    ui.showTableContainer(false);
                    return;
                }

                ui.showTableEmptyState(false);
                ui.showTableContainer(true);
                renderTableHeader();

                const totals = {
                    gradeTotals: {},
                    totalCollection: 0,
                    teacherShare: 0,
                    institutionShare: 0
                };

                state.allGrades.forEach(grade => {
                    totals.gradeTotals[grade] = 0;
                });

                state.teacherData.class_wise.forEach(cls => {
                    const gradeMatch = cls.class_name?.match(/Grade\s+(\d+)/i);
                    const grade = gradeMatch ? gradeMatch[1] : 'Unknown';
                    const amount = utils.toNumber(cls.total_amount);

                    totals.gradeTotals[grade] = (totals.gradeTotals[grade] || 0) + amount;
                    totals.totalCollection += amount;
                    totals.teacherShare += utils.toNumber(cls.teacher_earning);
                    totals.institutionShare += utils.toNumber(cls.institution_cut);
                });

                state.allGrades.forEach(grade => {
                    const row = document.createElement('tr');
                    const amount = totals.gradeTotals[grade] || 0;
                    const teacherShareForGrade = state.teacherData.class_wise
                        .filter(cls => {
                            const gradeMatch = cls.class_name?.match(/Grade\s+(\d+)/i);
                            return gradeMatch && gradeMatch[1] === grade;
                        })
                        .reduce((sum, cls) => sum + utils.toNumber(cls.teacher_earning), 0);

                    const institutionShareForGrade = state.teacherData.class_wise
                        .filter(cls => {
                            const gradeMatch = cls.class_name?.match(/Grade\s+(\d+)/i);
                            return gradeMatch && gradeMatch[1] === grade;
                        })
                        .reduce((sum, cls) => sum + utils.toNumber(cls.institution_cut), 0);

                    row.innerHTML = `
                            <td class="fw-bold">Grade ${grade}</td>
                            <td>${utils.formatCurrency(amount)}</td>
                            <td>${utils.formatCurrency(institutionShareForGrade)}</td>
                            <td class="fw-bold text-success">${utils.formatCurrency(teacherShareForGrade)}</td>
                        `;
                    elements.paymentTableBody.appendChild(row);

                    state.allPayments.push({
                        grade: grade,
                        amount: amount,
                        institutionShare: institutionShareForGrade,
                        teacherShare: teacherShareForGrade
                    });
                });

                renderTableFooter(totals);
            }

            function renderTableHeader() {
                if (!elements.paymentTableHeader) return;

                elements.paymentTableHeader.innerHTML = `
                        <tr>
                            <th class="py-2">Grade</th>
                            <th class="py-2 text-primary">Total Collection</th>
                            <th class="py-2 text-secondary">Institution Share</th>
                            <th class="py-2 text-success">Teacher Share</th>
                        </tr>
                    `;
            }

            function renderTableFooter(totals) {
                if (!elements.paymentTableFooter) return;

                elements.paymentTableFooter.innerHTML = `
                        <tr>
                            <td class="fw-bold py-2">Grand Total</td>
                            <td class="fw-bold py-2 text-primary">${utils.formatCurrency(totals.totalCollection)}</td>
                            <td class="fw-bold py-2 text-secondary">${utils.formatCurrency(totals.institutionShare)}</td>
                            <td class="fw-bold py-2 text-success">${utils.formatCurrency(totals.teacherShare)}</td>
                        </tr>
                    `;
            }

            function setupMonthYearSelectors() {
                if (!elements.monthSelect || !elements.yearSelect) return;

                elements.monthSelect.addEventListener('change', handleMonthYearChange);
                elements.yearSelect.addEventListener('change', handleMonthYearChange);
            }

            function handleMonthYearChange() {
                const month = elements.monthSelect.value;
                const year = elements.yearSelect.value;

                if (!month || !year) {
                    console.error('Month or year is undefined');
                    return;
                }

                ui.updateSelectedMonthYear(month, year);
                fetchTeacherData(month, year);
            }

            function setupPayTeacherButton() {
                if (!elements.payTeacherBtn) return;

                elements.payTeacherBtn.addEventListener('click', function () {
                    if (!state.teacherData) return;

                    const salaryPaid = utils.toNumber(state.teacherData.salary_paid || 0);
                    if (salaryPaid > 0) {
                        utils.showToast('Salary already paid for this month', 'warning');
                        return;
                    }

                    const amount = utils.toNumber(state.teacherData.net_payable || 0);
                    if (amount <= 0) {
                        utils.showToast('No payment due for this month', 'warning');
                        return;
                    }

                    const teacherName = state.teacherData.teacher_name;
                    const teacherId = state.teacherData.teacher_id;
                    const month = elements.monthSelect.value;
                    const year = elements.yearSelect.value;
                    const monthYear = `${utils.getMonthName(month)} ${year}`;

                    showPaymentConfirmation(teacherName, amount, monthYear, function (confirmed) {
                        if (confirmed) {
                            processPayment(teacherId, teacherName, amount, monthYear);
                        }
                    });
                });
            }

            function showPaymentConfirmation(teacherName, amount, monthYear, callback) {
                const modal = document.createElement('div');
                modal.id = 'paymentConfirmation';
                modal.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 9998;
                        backdrop-filter: blur(2px);
                    `;

                modal.innerHTML = `
                        <div style="background: white; padding: 25px; border-radius: 12px; max-width: 400px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                            <div style="text-align: center; margin-bottom: 20px;">
                                <div style="width: 60px; height: 60px; background: #4e73df; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                                    <i class="fas fa-money-check-alt" style="font-size: 24px; color: white;"></i>
                                </div>
                                <h5 style="margin: 0 0 5px 0; color: #333; font-weight: 600;">Confirm Salary Payment</h5>
                                <p style="color: #666; font-size: 14px; margin: 0;">Please review the payment details</p>
                            </div>

                            <div style="background: #f8f9fc; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #e3e6f0;">
                                    <span style="color: #5a5c69; font-weight: 500;">Teacher:</span>
                                    <strong>${teacherName}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #e3e6f0;">
                                    <span style="color: #5a5c69; font-weight: 500;">Amount:</span>
                                    <strong style="color: #1cc88a; font-size: 18px;">${utils.formatCurrency(amount)}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #5a5c69; font-weight: 500;">Payment For:</span>
                                    <strong>${monthYear}</strong>
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button id="confirmBtn" style="background: #1cc88a; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; flex: 1;">
                                    <i class="fas fa-check-circle me-1"></i> Confirm Payment
                                </button>
                                <button id="cancelBtn" style="background: #e74a3b; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; flex: 1;">
                                    <i class="fas fa-times-circle me-1"></i> Cancel
                                </button>
                            </div>
                        </div>
                    `;

                document.body.appendChild(modal);

                document.getElementById('confirmBtn').addEventListener('click', function () {
                    modal.remove();
                    callback(true);
                });

                document.getElementById('cancelBtn').addEventListener('click', function () {
                    modal.remove();
                    callback(false);
                });

                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        modal.remove();
                        callback(false);
                    }
                });
            }

            function processPayment(teacherId, teacherName, amount, monthYear) {
                if (state.isProcessingPayment) return;

                state.isProcessingPayment = true;
                ui.setPayButtonLoading(true);

                const selectedMonth = elements.monthSelect.value;
                const selectedYear = elements.yearSelect.value;

                const paymentData = {
                    teacher_id: teacherId,
                    payment: amount,
                    reason_code: 'salary',
                    paymentFor: `${utils.getMonthName(selectedMonth)} ${selectedYear}`,
                    net_payable: amount
                };

                fetch('/api/teacher-payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': utils.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(paymentData)
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            utils.showToast('Salary payment successful!', 'success');
                            setTimeout(() => {
                                fetchTeacherData(selectedMonth, selectedYear);
                            }, CONFIG.REFRESH_DELAY);
                        } else {
                            throw new Error(data.message || 'Payment failed');
                        }
                    })
                    .catch(error => {
                        console.error('Payment error:', error);
                        utils.showToast(error.message || 'Payment failed. Please try again.', 'error');
                    })
                    .finally(() => {
                        state.isProcessingPayment = false;
                        ui.setPayButtonLoading(false);
                    });
            }

            function setupExportTableExcel() {
                if (!elements.exportTableExcelBtn) return;

                elements.exportTableExcelBtn.addEventListener('click', function () {
                    if (!state.teacherData || state.allPayments.length === 0) {
                        utils.showToast('No data to export', 'warning');
                        return;
                    }

                    try {
                        const exportData = state.allPayments.map(payment => ({
                            'Grade': payment.grade,
                            'Total Collection': payment.amount,
                            'Institution Share': payment.institutionShare,
                            'Teacher Share': payment.teacherShare
                        }));

                        const ws = XLSX.utils.json_to_sheet(exportData);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Salary Payments');

                        const filename = `${state.teacherData.teacher_name}_${utils.getMonthName(elements.monthSelect.value)}_${elements.yearSelect.value}_Salary_Report.xlsx`;
                        XLSX.writeFile(wb, filename);

                        utils.showToast('Excel file exported successfully', 'success');
                    } catch (error) {
                        console.error('Error exporting to Excel:', error);
                        utils.showToast('Failed to export Excel file', 'error');
                    }
                });
            }

            function setupExportTablePdf() {
                if (!elements.exportTablePdfBtn) return;

                elements.exportTablePdfBtn.addEventListener('click', function () {
                    if (!state.teacherData || state.allPayments.length === 0) {
                        utils.showToast('No data to export', 'warning');
                        return;
                    }

                    try {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF('landscape');

                        doc.setFontSize(14);
                        doc.text(`${state.teacherData.teacher_name} - Salary Payment Report`, 14, 10);
                        doc.setFontSize(10);
                        doc.text(`Period: ${utils.getMonthName(elements.monthSelect.value)} ${elements.yearSelect.value}`, 14, 16);
                        doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 22);

                        const headers = ['Grade', 'Total Collection', 'Institution Share', 'Teacher Share'];
                        const tableData = state.allPayments.map(payment => [
                            payment.grade,
                            utils.formatCurrency(payment.amount),
                            utils.formatCurrency(payment.institutionShare),
                            utils.formatCurrency(payment.teacherShare)
                        ]);

                        doc.autoTable({
                            head: [headers],
                            body: tableData,
                            startY: 30,
                            styles: { fontSize: 8 },
                            headStyles: { fillColor: [78, 115, 223] }
                        });

                        const filename = `${state.teacherData.teacher_name}_${utils.getMonthName(elements.monthSelect.value)}_${elements.yearSelect.value}_Salary_Report.pdf`;
                        doc.save(filename);

                        utils.showToast('PDF file exported successfully', 'success');
                    } catch (error) {
                        console.error('Error exporting to PDF:', error);
                        utils.showToast('Failed to export PDF file', 'error');
                    }
                });
            }

            function init() {
                console.log('Initializing Teacher Salary History with Teacher ID:', utils.teacherId);

                setupMonthYearSelectors();
                setupPayTeacherButton();
                setupExportTableExcel();
                setupExportTablePdf();

                const prev = utils.getPreviousMonthYear();
                if (elements.monthSelect) elements.monthSelect.value = prev.month;
                if (elements.yearSelect) elements.yearSelect.value = prev.year;

                ui.updateSelectedMonthYear(prev.month, prev.year);
                fetchTeacherData(prev.month, prev.year);

                console.log('Teacher Salary History initialized successfully');
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            window.addEventListener('error', function (event) {
                console.error('Global error:', event.error);
                utils.showToast('An unexpected error occurred', 'error');
            });

            window.addEventListener('unhandledrejection', function (event) {
                console.error('Unhandled promise rejection:', event.reason);
                utils.showToast('A network error occurred', 'error');
            });

        })();
    </script>
@endpush