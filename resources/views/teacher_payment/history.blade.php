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

            <div class="col-md-4 mb-3">
                <div class="card border-0 border-start border-danger border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-cut text-danger" style="font-size: 1rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 small text-muted">Organize Cut</h6>
                                <h4 class="fw-bold text-danger mb-0" id="organizeCut">LKR 0.00</h4>
                            </div>
                        </div>
                        <small class="text-muted">Organize's share from collections</small>
                    </div>
                </div>
            </div>

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
                                <i class="fas fa-filter me-1"></i> All Time
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
                                        <th class="small text-muted py-2">Status</th>
                                        <th class="small text-muted py-2">Processed By</th>
                                    </tr>
                                </thead>
                                <tbody id="advancePaymentsTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                            Loading...
                                        </td>
                                    </tr>
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
                                <h6 class="mb-0 fw-bold">Class-wise Payment Breakdown</h6>
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
                        <div id="tableLoadingSpinner" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 small text-muted">Loading payment data...</p>
                        </div>

                        <div class="d-none" id="tableContainer">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless" id="paymentTable">
                                    <thead class="bg-light" id="paymentTableHeader"></thead>
                                    <tbody id="paymentTableBody"></tbody>
                                    <tfoot class="bg-light fw-bold" id="paymentTableFooter"></tfoot>
                                </table>
                            </div>
                        </div>

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
        }

        .btn {
            border-radius: 0.35rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-success:disabled {
            background-color: #b7e4d4;
            border-color: #b7e4d4;
            cursor: not-allowed;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
        (function() {
            'use strict';

            // Helper function to get teacher ID from URL
            function getTeacherIdFromUrl() {
                const pathParts = window.location.pathname.split('/').filter(part => part);
                for (let i = pathParts.length - 1; i >= 0; i--) {
                    if (/^\d+$/.test(pathParts[i])) {
                        return pathParts[i];
                    }
                }
                return '{{ $teacher_id ?? 0 }}';
            }

            const CONFIG = {
                API_TIMEOUT: 30000,
                TOAST_DURATION: 3000,
                REFRESH_DELAY: 2000
            };

            const utils = {
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                teacherId: getTeacherIdFromUrl(),

                formatCurrency(amount) {
                    let num = parseFloat(amount);
                    if (isNaN(num)) num = 0;
                    return new Intl.NumberFormat('en-LK', {
                        style: 'currency',
                        currency: 'LKR',
                        minimumFractionDigits: 2
                    }).format(num);
                },

                formatNumber(num) {
                    let n = parseFloat(num);
                    if (isNaN(n)) n = 0;
                    return new Intl.NumberFormat('en-LK').format(n);
                },

                formatDateTime(dateString) {
                    try {
                        const date = new Date(dateString);
                        return date.toLocaleString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } catch {
                        return dateString || '-';
                    }
                },

                getMonthName(monthNumber) {
                    const months = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
                    return months[parseInt(monthNumber) - 1] || 'Unknown';
                },

                toNumber(value) {
                    let num = parseFloat(value);
                    return isNaN(num) ? 0 : num;
                },

                showToast(message, type = 'info') {
                    const colors = {
                        success: '#1cc88a', error: '#e74a3b', warning: '#f6c23e', info: '#36b9cc'
                    };
                    const icons = {
                        success: 'fa-check-circle', error: 'fa-exclamation-circle',
                        warning: 'fa-exclamation-triangle', info: 'fa-info-circle'
                    };

                    const toast = document.createElement('div');
                    toast.style.cssText = `
                        position: fixed; top: 20px; right: 20px; background: ${colors[type]};
                        color: white; padding: 12px 20px; border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 999999;
                        animation: slideIn 0.3s ease-out; font-size: 0.875rem;
                    `;
                    toast.innerHTML = `<i class="fas ${icons[type]} me-2"></i>${message}`;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), CONFIG.TOAST_DURATION);
                },

                getPreviousMonthYear() {
                    const now = new Date();
                    if (now.getMonth() === 0) {
                        return { month: '12', year: (now.getFullYear() - 1).toString() };
                    }
                    return { month: now.getMonth().toString().padStart(2, '0'), year: now.getFullYear().toString() };
                }
            };

            console.log('Teacher ID:', utils.teacherId);

            // State management
            let state = {
                teacherData: null,
                isProcessingPayment: false
            };

            // DOM Elements
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
                organizeCut: document.getElementById('organizeCut'),
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
                lastUpdated: document.getElementById('lastUpdated')
            };

            // Fetch teacher data from API
            async function fetchTeacherData(month, year) {
                try {
                    const url = `/api/teacher-payments/monthly-income/${utils.teacherId}/${year}-${month}`;
                    console.log('Fetching:', url);

                    const response = await fetch(url, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) throw new Error(`HTTP ${response.status}`);

                    const data = await response.json();

                    if (data.status === 'success') {
                        state.teacherData = data.data;
                        renderAllData();
                    } else {
                        throw new Error(data.message || 'Failed to load data');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    utils.showToast('Failed to load salary data', 'error');
                    showEmptyStates();
                }
            }

            function showEmptyStates() {
                if (elements.tableContainer) elements.tableContainer.classList.add('d-none');
                if (elements.tableEmptyState) elements.tableEmptyState.classList.remove('d-none');
                if (elements.tableLoadingSpinner) elements.tableLoadingSpinner.classList.add('d-none');
            }

            function renderAllData() {
                if (!state.teacherData) return;
                renderTeacherInfo();
                renderFinancialSummary();
                renderPaymentStatus();
                renderClassesCards();
                renderAdvancePayments();
                renderPaymentTable();
                updateLastUpdated();
            }

            function renderTeacherInfo() {
                const data = state.teacherData;
                if (elements.teacherName) elements.teacherName.textContent = data.teacher_name || '-';
                if (elements.teacherId) elements.teacherId.textContent = data.teacher_id || '-';
                if (elements.teacherIdDisplay) elements.teacherIdDisplay.textContent = data.teacher_id || '-';

                if (data.class_wise && data.class_wise.length > 0 && elements.subjectName) {
                    elements.subjectName.textContent = data.class_wise[0].class_name?.split(' - ')[0] || '-';
                }
            }

            function renderFinancialSummary() {
                const data = state.teacherData;
                if (elements.totalCollections) elements.totalCollections.textContent = utils.formatCurrency(data.total_payments || 0);
                if (elements.teacherShare) elements.teacherShare.textContent = utils.formatCurrency(data.teacher_share || 0);
                if (elements.salaryPaid) elements.salaryPaid.textContent = utils.formatCurrency(data.salary_paid || 0);
                if (elements.advancePayments) elements.advancePayments.textContent = utils.formatCurrency(data.advance_paid || 0);
                if (elements.institutionIncome) elements.institutionIncome.textContent = utils.formatCurrency(data.institution_income || 0);
                if (elements.organizeCut) elements.organizeCut.textContent = utils.formatCurrency(data.total_organize_cut || 0);
                if (elements.netPayable) elements.netPayable.textContent = utils.formatCurrency(data.net_payable || 0);
            }

            function renderPaymentStatus() {
                const data = state.teacherData;
                const netPayable = utils.toNumber(data.net_payable);
                const salaryPaid = utils.toNumber(data.salary_paid);

                let statusText = '', statusColor = '', buttonEnabled = false, messageText = '';

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
                    messageText = `Click to pay ${utils.formatCurrency(netPayable)} salary.`;
                }

                if (elements.paymentStatus) {
                    elements.paymentStatus.textContent = statusText;
                    elements.paymentStatus.className = `fw-bold mb-0 ${statusColor}`;
                }
                if (elements.paymentMessage) elements.paymentMessage.textContent = messageText;
                if (elements.payTeacherBtn) elements.payTeacherBtn.disabled = !buttonEnabled;
            }

            function renderClassesCards() {
                if (!elements.classesCards) return;
                elements.classesCards.innerHTML = '';

                const classes = state.teacherData.class_wise;
                if (!classes || classes.length === 0) {
                    elements.classesCards.innerHTML = `<div class="col-12 text-center py-4">
                        <i class="fas fa-chalkboard-teacher fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No classes found.</p></div>`;
                    return;
                }

                classes.forEach(cls => {
                    const totalStudents = utils.toNumber(cls.total_students);
                    const paidStudents = utils.toNumber(cls.paid_students);
                    const percentagePaid = totalStudents > 0 ? Math.round((paidStudents / totalStudents) * 100) : 0;

                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-4 mb-3';
                    card.innerHTML = `
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 small fw-bold">${cls.class_name || 'Class'}</h6>
                                    <span class="badge bg-primary">${cls.teacher_percentage || 0}%</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted small">Students:</span>
                                        <span class="fw-bold small">${utils.formatNumber(totalStudents)}</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: ${percentagePaid}%"></div>
                                    </div>
                                    <div class="row small text-center">
                                        <div class="col-4"><span class="text-success fw-bold">${utils.formatNumber(paidStudents)}</span><div class="text-muted">Paid</div></div>
                                        <div class="col-4"><span class="text-danger fw-bold">${utils.formatNumber(cls.unpaid_students || 0)}</span><div class="text-muted">Unpaid</div></div>
                                        <div class="col-4"><span class="text-info fw-bold">${utils.formatNumber(cls.free_students || 0)}</span><div class="text-muted">Free</div></div>
                                    </div>
                                </div>
                                <div class="border-top pt-2">
                                    <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Total:</span><span class="fw-bold small">${utils.formatCurrency(cls.total_amount || 0)}</span></div>
                                    <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Teacher:</span><span class="fw-bold text-success small">${utils.formatCurrency(cls.teacher_earning || 0)}</span></div>
                                    <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Organize:</span><span class="fw-bold text-danger small">${utils.formatCurrency(cls.organize_cut || 0)}</span></div>
                                    <div class="d-flex justify-content-between"><span class="text-muted small">Institution:</span><span class="fw-bold text-secondary small">${utils.formatCurrency(cls.institution_cut || 0)}</span></div>
                                </div>
                            </div>
                        </div>`;
                    elements.classesCards.appendChild(card);
                });
            }

            function renderAdvancePayments() {
                const records = state.teacherData.advance_records;
                const tbody = elements.advancePaymentsTableBody;
                const emptyState = elements.advanceEmptyState;

                if (!tbody) return;

                if (records && Array.isArray(records) && records.length > 0) {
                    tbody.innerHTML = '';
                    records.forEach(record => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td><small>${utils.formatDateTime(record.created_at)}</small></td>
                            <td class="fw-bold text-warning"><small>${utils.formatCurrency(record.payment)}</small></td>
                            <td><span class="badge bg-info">${record.reason_code || 'N/A'}</span></td>
                            <td><span class="badge ${record.status ? 'bg-success' : 'bg-danger'}">${record.status ? 'Active' : 'Deleted'}</span></td>
                            <td><small>${record.user_name || 'System'}</small></td>
                        `;
                        tbody.appendChild(row);
                    });
                    if (emptyState) emptyState.classList.add('d-none');
                } else {
                    if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No advance payments found.</td></tr>';
                    if (emptyState) emptyState.classList.remove('d-none');
                }
            }

            function renderPaymentTable() {
                const classes = state.teacherData.class_wise;
                if (!classes || classes.length === 0) {
                    if (elements.tableContainer) elements.tableContainer.classList.add('d-none');
                    if (elements.tableEmptyState) elements.tableEmptyState.classList.remove('d-none');
                    if (elements.tableLoadingSpinner) elements.tableLoadingSpinner.classList.add('d-none');
                    return;
                }

                // Group by grade
                const gradeGroups = {};
                classes.forEach(cls => {
                    const match = cls.class_name?.match(/Grade\s+(\d+)/i);
                    const grade = match ? `Grade ${match[1]}` : (cls.class_name || 'Other');
                    if (!gradeGroups[grade]) {
                        gradeGroups[grade] = { totalAmount: 0, teacherEarning: 0, organizeCut: 0, institutionCut: 0 };
                    }
                    gradeGroups[grade].totalAmount += utils.toNumber(cls.total_amount);
                    gradeGroups[grade].teacherEarning += utils.toNumber(cls.teacher_earning);
                    gradeGroups[grade].organizeCut += utils.toNumber(cls.organize_cut);
                    gradeGroups[grade].institutionCut += utils.toNumber(cls.institution_cut);
                });

                // Render header
                if (elements.paymentTableHeader) {
                    elements.paymentTableHeader.innerHTML = `
                        <tr>
                            <th class="py-2">Class/Grade</th>
                            <th class="py-2 text-primary">Total Collection</th>
                            <th class="py-2 text-danger">Organize Cut</th>
                            <th class="py-2 text-secondary">Institution Cut</th>
                            <th class="py-2 text-success">Teacher Share</th>
                        </tr>
                    `;
                }

                // Render body
                const tbody = elements.paymentTableBody;
                if (tbody) {
                    tbody.innerHTML = '';
                    let grandTotal = 0, grandOrganize = 0, grandInstitution = 0, grandTeacher = 0;

                    Object.keys(gradeGroups).sort().forEach(grade => {
                        const g = gradeGroups[grade];
                        grandTotal += g.totalAmount;
                        grandOrganize += g.organizeCut;
                        grandInstitution += g.institutionCut;
                        grandTeacher += g.teacherEarning;

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="fw-bold">${grade}</td>
                            <td>${utils.formatCurrency(g.totalAmount)}</td>
                            <td class="text-danger">${utils.formatCurrency(g.organizeCut)}</td>
                            <td class="text-secondary">${utils.formatCurrency(g.institutionCut)}</td>
                            <td class="fw-bold text-success">${utils.formatCurrency(g.teacherEarning)}</td>
                        `;
                        tbody.appendChild(row);
                    });

                    if (elements.paymentTableFooter) {
                        elements.paymentTableFooter.innerHTML = `
                            <tr class="bg-light">
                                <td class="fw-bold">GRAND TOTAL</td>
                                <td class="fw-bold text-primary">${utils.formatCurrency(grandTotal)}</td>
                                <td class="fw-bold text-danger">${utils.formatCurrency(grandOrganize)}</td>
                                <td class="fw-bold text-secondary">${utils.formatCurrency(grandInstitution)}</td>
                                <td class="fw-bold text-success">${utils.formatCurrency(grandTeacher)}</td>
                            </tr>
                        `;
                    }
                }

                if (elements.tableLoadingSpinner) elements.tableLoadingSpinner.classList.add('d-none');
                if (elements.tableContainer) elements.tableContainer.classList.remove('d-none');
                if (elements.tableEmptyState) elements.tableEmptyState.classList.add('d-none');
            }

            function updateLastUpdated() {
                if (elements.lastUpdated) {
                    elements.lastUpdated.textContent = new Date().toLocaleString();
                }
            }

            // Payment processing
            function setupPayTeacherButton() {
                if (!elements.payTeacherBtn) return;
                elements.payTeacherBtn.addEventListener('click', async () => {
                    if (state.isProcessingPayment) return;
                    const data = state.teacherData;
                    if (!data) return;
                    if (utils.toNumber(data.salary_paid) > 0) {
                        utils.showToast('Salary already paid for this month', 'warning');
                        return;
                    }
                    const amount = utils.toNumber(data.net_payable);
                    if (amount <= 0) {
                        utils.showToast('No payment due for this month', 'warning');
                        return;
                    }

                    if (!confirm(`Confirm payment of ${utils.formatCurrency(amount)} to ${data.teacher_name}?`)) return;

                    state.isProcessingPayment = true;
                    const originalText = elements.payTeacherBtn.innerHTML;
                    elements.payTeacherBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                    elements.payTeacherBtn.disabled = true;

                    try {
                        const response = await fetch('/api/teacher-payments', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': utils.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                teacher_id: data.teacher_id,
                                payment: amount,
                                reason_code: 'salary',
                                payment_for: elements.selectedMonthYear?.textContent || ''
                            })
                        });

                        const result = await response.json();
                        if (result.status === 'success') {
                            utils.showToast('Salary payment successful!', 'success');
                            setTimeout(() => {
                                const month = elements.monthSelect?.value;
                                const year = elements.yearSelect?.value;
                                if (month && year) fetchTeacherData(month, year);
                            }, CONFIG.REFRESH_DELAY);
                        } else {
                            throw new Error(result.message || 'Payment failed');
                        }
                    } catch (error) {
                        console.error('Payment error:', error);
                        utils.showToast(error.message || 'Payment failed. Please try again.', 'error');
                    } finally {
                        state.isProcessingPayment = false;
                        elements.payTeacherBtn.innerHTML = originalText;
                        elements.payTeacherBtn.disabled = false;
                    }
                });
            }

            // Month/Year change handler
            function handleMonthYearChange() {
                const month = elements.monthSelect?.value;
                const year = elements.yearSelect?.value;
                if (month && year) {
                    if (elements.selectedMonthYear) {
                        elements.selectedMonthYear.textContent = `${utils.getMonthName(month)} ${year}`;
                    }
                    fetchTeacherData(month, year);
                }
            }

            function setupMonthYearSelectors() {
                if (elements.monthSelect) elements.monthSelect.addEventListener('change', handleMonthYearChange);
                if (elements.yearSelect) elements.yearSelect.addEventListener('change', handleMonthYearChange);
            }

            // Export functions
            function setupExportExcel() {
                if (!elements.exportTableExcelBtn) return;
                elements.exportTableExcelBtn.addEventListener('click', () => {
                    const classes = state.teacherData?.class_wise;
                    if (!classes || classes.length === 0) {
                        utils.showToast('No data to export', 'warning');
                        return;
                    }

                    const exportData = classes.map(cls => ({
                        'Class Name': cls.class_name || '',
                        'Total Students': cls.total_students || 0,
                        'Paid Students': cls.paid_students || 0,
                        'Unpaid Students': cls.unpaid_students || 0,
                        'Free Students': cls.free_students || 0,
                        'Total Amount': cls.total_amount || 0,
                        'Teacher %': cls.teacher_percentage || 0,
                        'Teacher Earning': cls.teacher_earning || 0,
                        'Organize Cut': cls.organize_cut || 0,
                        'Institution Cut': cls.institution_cut || 0
                    }));

                    const ws = XLSX.utils.json_to_sheet(exportData);
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Salary Breakdown');
                    XLSX.writeFile(wb, `${state.teacherData.teacher_name}_${elements.selectedMonthYear?.textContent}_Salary.xlsx`);
                    utils.showToast('Excel exported successfully', 'success');
                });
            }

            function setupExportPdf() {
                if (!elements.exportTablePdfBtn) return;
                elements.exportTablePdfBtn.addEventListener('click', async () => {
                    const classes = state.teacherData?.class_wise;
                    if (!classes || classes.length === 0) {
                        utils.showToast('No data to export', 'warning');
                        return;
                    }

                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF('landscape');

                    doc.setFontSize(14);
                    doc.text(`${state.teacherData.teacher_name} - Salary Report`, 14, 10);
                    doc.setFontSize(10);
                    doc.text(`Period: ${elements.selectedMonthYear?.textContent || ''}`, 14, 16);
                    doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 22);

                    const headers = ['Class Name', 'Total Students', 'Paid', 'Unpaid', 'Free', 'Total Amount', 'Teacher %', 'Teacher Share', 'Organize Cut', 'Institution Cut'];
                    const tableData = classes.map(cls => [
                        cls.class_name || '',
                        utils.formatNumber(cls.total_students),
                        utils.formatNumber(cls.paid_students),
                        utils.formatNumber(cls.unpaid_students),
                        utils.formatNumber(cls.free_students),
                        utils.formatCurrency(cls.total_amount),
                        `${cls.teacher_percentage || 0}%`,
                        utils.formatCurrency(cls.teacher_earning),
                        utils.formatCurrency(cls.organize_cut),
                        utils.formatCurrency(cls.institution_cut)
                    ]);

                    doc.autoTable({
                        head: [headers],
                        body: tableData,
                        startY: 30,
                        styles: { fontSize: 7 },
                        headStyles: { fillColor: [78, 115, 223] }
                    });

                    doc.save(`${state.teacherData.teacher_name}_${elements.selectedMonthYear?.textContent}_Salary.pdf`);
                    utils.showToast('PDF exported successfully', 'success');
                });
            }

            // Initialize
            function init() {
                console.log('Initializing Teacher Salary History...');
                setupMonthYearSelectors();
                setupPayTeacherButton();
                setupExportExcel();
                setupExportPdf();

                const prev = utils.getPreviousMonthYear();
                if (elements.monthSelect) elements.monthSelect.value = prev.month;
                if (elements.yearSelect) elements.yearSelect.value = prev.year;
                if (elements.selectedMonthYear) {
                    elements.selectedMonthYear.textContent = `${utils.getMonthName(prev.month)} ${prev.year}`;
                }
                fetchTeacherData(prev.month, prev.year);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
@endpush