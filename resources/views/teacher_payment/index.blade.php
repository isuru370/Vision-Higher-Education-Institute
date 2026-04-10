@extends('layouts.app')

@section('title', 'Teacher Income')
@section('page-title', 'Teacher Income')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Teacher Income</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <!-- Summary Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white py-2">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-bar"></i> Summary for <span
                                id="currentMonthYear">{{ date('F Y') }}</span>
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white mb-2">
                                    <div class="card-body py-2">
                                        <small class="card-title">Total Payments</small>
                                        <h5 class="mb-0" id="summaryTotalPayments">LKR 0.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white mb-2">
                                    <div class="card-body py-2">
                                        <small class="card-title">Gross Earnings</small>
                                        <h5 class="mb-0" id="summaryGrossEarnings">LKR 0.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white mb-2">
                                    <div class="card-body py-2">
                                        <small class="card-title">Net Payable</small>
                                        <h5 class="mb-0" id="summaryNetPayable">LKR 0.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-secondary text-white mb-2">
                                    <div class="card-body py-2">
                                        <small class="card-title">Institution Income</small>
                                        <h5 class="mb-0" id="summaryInstitutionIncome">LKR 0.00</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Teacher Monthly Income - <span
                                        id="headerMonthYear">{{ date('F Y') }}</span></h6>
                                <small id="recordCount" class="text-white">0 records found</small>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-success btn-sm" id="exportExcelBtn">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                <button class="btn btn-danger btn-sm" id="exportPdfBtn">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button id="refreshBtn" class="btn btn-light btn-sm">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Filter -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" id="teacherSearch" class="form-control form-control-sm"
                                        placeholder="Search by teacher name or ID...">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="text-center d-none">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <small class="mt-2 d-block">Loading teacher data...</small>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped" id="teacherTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="py-1" data-sort="teacher_id">
                                            <small>ID <i class="fas fa-sort"></i></small>
                                        </th>
                                        <th class="py-1" data-sort="teacher_name">
                                            <small>Teacher Name <i class="fas fa-sort"></i></small>
                                        </th>
                                        <th class="py-1 text-end" data-sort="total_payments_this_month">
                                            <small>Total Payments <i class="fas fa-sort"></i></small>
                                        </th>
                                        <th class="py-1 text-end" data-sort="gross_teacher_earning">
                                            <small>Gross Earnings <i class="fas fa-sort"></i></small>
                                        </th>
                                        <th class="py-1 text-end" data-sort="total_organize_cut">
                                            <small>Organize Cut <i class="fas fa-sort"></i></small>
                                        </th>
                                        <th class="py-1 text-end" data-sort="advance_deducted_this_month">
                                            <small>Advance Deducted <i class="fas fa-sort"></i></small>
                                        </th>
                                        <th class="py-1 text-end" data-sort="net_teacher_payable">
                                            <small>Net Payable <i class="fas fa-sort"></i></small>
                                        </th>
                                        <th class="py-1 text-end" data-sort="institution_income">
                                            <small>Institution Income <i class="fas fa-sort"></i></small>
                                        </th>
                                        <th class="py-1 text-center">
                                            <small>Actions</small>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="teacherTableBody">
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div id="emptyState" class="text-center d-none">
                            <div class="alert alert-info py-2">
                                <small><i class="fas fa-info-circle"></i> No teacher payment data found for this
                                    month.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Wise Breakdown Modal -->
    <div class="modal fade" id="breakdownModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title mb-0">Class Wise Breakdown</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <small class="text-muted" id="breakdownTeacherInfo"></small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-1"><small>Class ID</small></th>
                                    <th class="py-1"><small>Class Name</small></th>
                                    <th class="py-1 text-end"><small>Teacher %</small></th>
                                    <th class="py-1 text-end"><small>Organize %</small></th>
                                    <th class="py-1 text-end"><small>Institution %</small></th>
                                    <th class="py-1 text-end"><small>Total Amount</small></th>
                                    <th class="py-1 text-end"><small>Teacher Cut</small></th>
                                    <th class="py-1 text-end"><small>Organize Cut</small></th>
                                    <th class="py-1 text-end"><small>Institution Cut</small></th>
                                </tr>
                            </thead>
                            <tbody id="breakdownTableBody">
                                <!-- Breakdown data will be populated here -->
                            </tbody>
                            <tfoot id="breakdownTableFooter" class="d-none">
                                <tr class="table-secondary">
                                    <td colspan="5" class="py-1"><small><strong>Totals:</strong></small></td>
                                    <td class="py-1 text-end"><small id="breakdownTotalAmount">LKR 0.00</small></td>
                                    <td class="py-1 text-end"><small id="breakdownTotalTeacherCut">LKR 0.00</small></td>
                                    <td class="py-1 text-end"><small id="breakdownTotalOrganizeCut">LKR 0.00</small></td>
                                    <td class="py-1 text-end"><small id="breakdownTotalInstitutionCut">LKR 0.00</small></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="noBreakdownData" class="text-center d-none">
                        <div class="alert alert-warning py-2 mb-0">
                            <small><i class="fas fa-exclamation-circle"></i> No class breakdown data available.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Advance Payment Modal -->
    <div class="modal fade" id="advanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark py-2">
                    <h6 class="modal-title mb-0">Make Advance Payment</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="advancePaymentForm">
                    <div class="modal-body">
                        <input type="hidden" id="advanceTeacherId" name="teacher_id">

                        <div class="mb-2">
                            <label for="teacherName" class="form-label"><small>Teacher</small></label>
                            <input type="text" class="form-control form-control-sm" id="teacherName" readonly>
                        </div>

                        <div class="mb-2">
                            <label for="availableEarning" class="form-label"><small>Available Net Payable</small></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">LKR</span>
                                <input type="text" class="form-control" id="availableEarning" readonly>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="amount" class="form-label"><small>Advance Amount *</small></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">LKR</span>
                                <input type="number" class="form-control" id="amount" name="payment" min="1" step="0.01"
                                    required>
                            </div>
                            <div class="form-text"><small>Enter amount up to available net payable</small></div>
                            <div class="invalid-feedback" id="amountError"></div>
                        </div>

                        <div class="mb-2">
                            <label for="reasonCode" class="form-label"><small>Reason Code *</small></label>
                            <select class="form-control form-control-sm" id="reasonCode" name="reason_code" required>
                                <option value="">Select a reason...</option>
                            </select>
                            <div class="invalid-feedback" id="reasonCodeError"></div>
                        </div>

                        <div class="alert alert-info py-2 mt-2">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                This advance payment will be deducted from the teacher's next payment.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning btn-sm" id="submitAdvanceBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <small>Submit Advance</small>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        body {
            font-size: 0.875rem;
        }

        .card-title {
            font-size: 1rem;
        }

        .table th,
        .table td {
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .form-control-sm {
            font-size: 0.875rem;
        }

        .modal-header .btn-close {
            padding: 0.5rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25em 0.6em;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .text-end {
            text-align: right;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.8rem;
            }

            .btn-group {
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            .card-header .d-flex {
                flex-direction: column;
                align-items: start !important;
                gap: 0.5rem;
            }

            .card-header .btn-group {
                align-self: flex-end;
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SheetJS for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- jsPDF for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
        (function () {
            'use strict';

            // CSRF Token setup for Laravel
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Global variables
            let teachersData = [];
            let paymentReasons = [];
            let currentSort = {
                column: null,
                direction: 'asc'
            };
            let advanceModalInstance = null;
            let breakdownModalInstance = null;

            // DOM Elements
            const teacherTableBody = document.getElementById('teacherTableBody');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const emptyState = document.getElementById('emptyState');
            const teacherSearch = document.getElementById('teacherSearch');
            const clearSearch = document.getElementById('clearSearch');
            const refreshBtn = document.getElementById('refreshBtn');
            const exportExcelBtn = document.getElementById('exportExcelBtn');
            const exportPdfBtn = document.getElementById('exportPdfBtn');
            const recordCount = document.getElementById('recordCount');
            const currentMonthYear = document.getElementById('currentMonthYear');
            const headerMonthYear = document.getElementById('headerMonthYear');

            // Summary elements
            const summaryTotalPayments = document.getElementById('summaryTotalPayments');
            const summaryGrossEarnings = document.getElementById('summaryGrossEarnings');
            const summaryNetPayable = document.getElementById('summaryNetPayable');
            const summaryInstitutionIncome = document.getElementById('summaryInstitutionIncome');

            // API Endpoints (Update these according to your routes)
            const API_ENDPOINTS = {
                teacherPayments: '/api/teacher-payments/monthly-income',
                advancePayment: '/api/teacher-payments',
                paymentReasons: '/api/payment-reason/dropdown',
                viewTeacher: (id) => `/teacher-payment/view/${id}`,
                payTeacher: (id) => `/teacher-payment/pay/${id}`,
                payhistory: (id) => `/teacher-payment/history/${id}`
            };

            // Configuration
            const CONFIG = {
                debounceDelay: 300,
                alertTimeout: {
                    success: 5000,
                    error: 10000,
                    warning: 8000,
                    info: 5000
                },
                currency: {
                    locale: 'en-LK',
                    currency: 'LKR',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            };

            // Format currency to LKR
            function formatCurrency(amount) {
                if (isNaN(amount) || amount === null || amount === undefined) {
                    amount = 0;
                }
                return new Intl.NumberFormat(CONFIG.currency.locale, {
                    style: 'currency',
                    currency: CONFIG.currency.currency,
                    minimumFractionDigits: CONFIG.currency.minimumFractionDigits,
                    maximumFractionDigits: CONFIG.currency.maximumFractionDigits
                }).format(amount);
            }

            // Format percentage
            function formatPercentage(percentage) {
                if (isNaN(percentage) || percentage === null || percentage === undefined) {
                    return '0%';
                }
                return parseFloat(percentage).toFixed(2).replace(/\.00$/, '') + '%';
            }

            // Show alert message
            function showAlert(message, type = 'info') {
                const alertTypes = {
                    'success': {
                        class: 'alert-success',
                        icon: 'fa-check-circle'
                    },
                    'danger': {
                        class: 'alert-danger',
                        icon: 'fa-exclamation-circle'
                    },
                    'warning': {
                        class: 'alert-warning',
                        icon: 'fa-exclamation-triangle'
                    },
                    'info': {
                        class: 'alert-info',
                        icon: 'fa-info-circle'
                    }
                };

                const alertConfig = alertTypes[type] || alertTypes.info;
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert ${alertConfig.class} alert-dismissible fade show py-2`;
                alertDiv.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="fas ${alertConfig.icon} me-2"></i>
                            <div><small>${message}</small></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;

                const cardBody = document.querySelector('.card-body');
                if (cardBody) {
                    cardBody.insertBefore(alertDiv, cardBody.firstChild);
                    setTimeout(() => {
                        if (alertDiv.parentNode) alertDiv.remove();
                    }, CONFIG.alertTimeout[type] || CONFIG.alertTimeout.info);
                }
            }

            // Show/hide loading spinner
            function showLoading(show) {
                if (!loadingSpinner) return;
                if (show) {
                    loadingSpinner.classList.remove('d-none');
                    if (teacherTableBody) teacherTableBody.innerHTML = '';
                } else {
                    loadingSpinner.classList.add('d-none');
                }
            }

            // Show/hide empty state
            function showEmptyState(show) {
                if (!emptyState) return;
                if (show) {
                    emptyState.classList.remove('d-none');
                } else {
                    emptyState.classList.add('d-none');
                }
            }

            // Update record count
            function updateRecordCount(count) {
                if (!recordCount) return;
                recordCount.textContent = `${count} record${count !== 1 ? 's' : ''} found`;
            }

            // Update summary cards
            function updateSummary(data) {
                if (!summaryTotalPayments || !summaryGrossEarnings || !summaryNetPayable || !summaryInstitutionIncome) return;

                const totalPaymentsSum = data.reduce((sum, teacher) => sum + (parseFloat(teacher.total_payments_this_month) || 0), 0);
                const grossEarningsSum = data.reduce((sum, teacher) => sum + (parseFloat(teacher.gross_teacher_earning) || 0), 0);
                const netPayableSum = data.reduce((sum, teacher) => sum + (parseFloat(teacher.net_teacher_payable) || 0), 0);
                const institutionIncomeSum = data.reduce((sum, teacher) => sum + (parseFloat(teacher.institution_income) || 0), 0);

                summaryTotalPayments.textContent = formatCurrency(totalPaymentsSum);
                summaryGrossEarnings.textContent = formatCurrency(grossEarningsSum);
                summaryNetPayable.textContent = formatCurrency(netPayableSum);
                summaryInstitutionIncome.textContent = formatCurrency(institutionIncomeSum);
            }

            // Load payment reasons dropdown
            async function loadPaymentReasons() {
                try {
                    const response = await fetch(API_ENDPOINTS.paymentReasons);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    if (data.status === 'success') {
                        paymentReasons = data.data || [];
                        populateReasonCodes();
                    } else {
                        throw new Error(data.message || 'Failed to load payment reasons');
                    }
                } catch (error) {
                    console.error('Error loading payment reasons:', error);
                    showAlert('Could not load payment reasons. Please refresh the page.', 'warning');
                }
            }

            // Populate reason codes in select dropdown
            function populateReasonCodes() {
                const select = document.getElementById('reasonCode');
                if (!select) return;

                select.innerHTML = '<option value="">Select a reason...</option>';

                if (paymentReasons.length === 0) {
                    // Default reasons if API fails
                    const defaultReasons = ['Salary Advance', 'Emergency Advance', 'Personal Advance', 'Other'];
                    defaultReasons.forEach(reason => {
                        const option = document.createElement('option');
                        option.value = reason;
                        option.textContent = reason;
                        select.appendChild(option);
                    });
                } else {
                    paymentReasons.forEach(reason => {
                        const option = document.createElement('option');
                        option.value = reason.reason_code || reason.code || reason;
                        option.textContent = reason.reason_code || reason.code || reason;
                        select.appendChild(option);
                    });
                }
            }

            // Show Breakdown Modal
            function showBreakdownModal(data) {
                const breakdownModalElement = document.getElementById('breakdownModal');
                if (!breakdownModalElement) return;

                const breakdownTeacherInfo = document.getElementById('breakdownTeacherInfo');
                const breakdownTableBody = document.getElementById('breakdownTableBody');
                const breakdownTableFooter = document.getElementById('breakdownTableFooter');
                const noBreakdownData = document.getElementById('noBreakdownData');
                const breakdownTotalAmount = document.getElementById('breakdownTotalAmount');
                const breakdownTotalTeacherCut = document.getElementById('breakdownTotalTeacherCut');
                const breakdownTotalOrganizeCut = document.getElementById('breakdownTotalOrganizeCut');
                const breakdownTotalInstitutionCut = document.getElementById('breakdownTotalInstitutionCut');

                // Set teacher info
                if (breakdownTeacherInfo) {
                    breakdownTeacherInfo.textContent = `Teacher: ${data.teacherName} (ID: ${data.teacherId})`;
                }

                // Clear previous data
                if (breakdownTableBody) breakdownTableBody.innerHTML = '';

                if (data.breakdown && data.breakdown.length > 0) {
                    if (breakdownTableFooter) breakdownTableFooter.classList.remove('d-none');
                    if (noBreakdownData) noBreakdownData.classList.add('d-none');

                    let totalAmount = 0;
                    let totalTeacherCut = 0;
                    let totalOrganizeCut = 0;
                    let totalInstitutionCut = 0;

                    data.breakdown.forEach(item => {
                        const classAmount = parseFloat(item.total_amount) || 0;
                        const teacherCut = parseFloat(item.teacher_cut) || 0;
                        const organizeCut = parseFloat(item.organize_cut) || 0;
                        const institutionCut = parseFloat(item.institution_cut) || 0;

                        totalAmount += classAmount;
                        totalTeacherCut += teacherCut;
                        totalOrganizeCut += organizeCut;
                        totalInstitutionCut += institutionCut;

                        const row = document.createElement('tr');
                        row.innerHTML = `
                                <td class="py-1"><small>${item.class_id || ''}</small></td>
                                <td class="py-1"><small>${item.class_name || ''}</small></td>
                                <td class="py-1 text-end"><small>${formatPercentage(item.teacher_percentage)}</small></td>
                                <td class="py-1 text-end"><small>${formatPercentage(item.organize_percentage)}</small></td>
                                <td class="py-1 text-end"><small>${formatPercentage(100 - (parseFloat(item.teacher_percentage) || 0) - (parseFloat(item.organize_percentage) || 0))}%</small></td>
                                <td class="py-1 text-end"><small>${formatCurrency(classAmount)}</small></td>
                                <td class="py-1 text-end"><small>${formatCurrency(teacherCut)}</small></td>
                                <td class="py-1 text-end"><small>${formatCurrency(organizeCut)}</small></td>
                                <td class="py-1 text-end"><small>${formatCurrency(institutionCut)}</small></td>
                            `;
                        if (breakdownTableBody) breakdownTableBody.appendChild(row);
                    });

                    if (breakdownTotalAmount) breakdownTotalAmount.textContent = formatCurrency(totalAmount);
                    if (breakdownTotalTeacherCut) breakdownTotalTeacherCut.textContent = formatCurrency(totalTeacherCut);
                    if (breakdownTotalOrganizeCut) breakdownTotalOrganizeCut.textContent = formatCurrency(totalOrganizeCut);
                    if (breakdownTotalInstitutionCut) breakdownTotalInstitutionCut.textContent = formatCurrency(totalInstitutionCut);
                } else {
                    if (breakdownTableFooter) breakdownTableFooter.classList.add('d-none');
                    if (noBreakdownData) noBreakdownData.classList.remove('d-none');
                }

                // Show modal
                try {
                    if (breakdownModalInstance) {
                        breakdownModalInstance.dispose();
                    }
                    breakdownModalInstance = new bootstrap.Modal(breakdownModalElement);
                    breakdownModalInstance.show();
                } catch (error) {
                    console.error('Error showing breakdown modal:', error);
                    breakdownModalElement.classList.add('show');
                    breakdownModalElement.style.display = 'block';
                }
            }

            // Handle view details click
            function handleViewDetails(event) {
                const btn = event.currentTarget;
                const teacherId = btn.getAttribute('data-teacher-id');
                const teacherName = btn.getAttribute('data-teacher-name');
                let breakdown = btn.getAttribute('data-breakdown');

                try {
                    breakdown = JSON.parse(breakdown);
                } catch (e) {
                    breakdown = [];
                }

                showBreakdownModal({
                    teacherName: teacherName,
                    teacherId: teacherId,
                    breakdown: breakdown
                });
            }

            // Attach view details listeners
            function attachViewDetailsListeners() {
                document.querySelectorAll('.view-details-btn').forEach(btn => {
                    btn.removeEventListener('click', handleViewDetails);
                    btn.addEventListener('click', handleViewDetails);
                });
            }

            // Show teacher breakdown (global function for onclick)
            window.showTeacherBreakdown = function (element) {
                const row = element.closest('tr');
                const breakdownData = row?.dataset.breakdown;

                if (breakdownData) {
                    const data = JSON.parse(breakdownData);
                    showBreakdownModal(data);
                }
            };

            // Show Advance Modal
            function showAdvanceModal(teacherId, teacherName, teacherEarning) {
                const advanceModalElement = document.getElementById('advanceModal');
                if (!advanceModalElement) return;

                const teacherNameInput = document.getElementById('teacherName');
                const availableEarningInput = document.getElementById('availableEarning');
                const advanceTeacherIdInput = document.getElementById('advanceTeacherId');
                const amountInput = document.getElementById('amount');
                const reasonCodeSelect = document.getElementById('reasonCode');

                if (teacherNameInput) teacherNameInput.value = teacherName || '';
                if (availableEarningInput) availableEarningInput.value = formatCurrency(teacherEarning);
                if (advanceTeacherIdInput) advanceTeacherIdInput.value = teacherId || '';

                if (amountInput) {
                    amountInput.value = '';
                    amountInput.max = teacherEarning;
                    amountInput.placeholder = `Max: ${formatCurrency(teacherEarning)}`;
                    amountInput.classList.remove('is-invalid');
                }

                if (reasonCodeSelect) {
                    reasonCodeSelect.value = '';
                    reasonCodeSelect.classList.remove('is-invalid');
                }

                const amountError = document.getElementById('amountError');
                const reasonCodeError = document.getElementById('reasonCodeError');
                if (amountError) amountError.textContent = '';
                if (reasonCodeError) reasonCodeError.textContent = '';

                try {
                    if (advanceModalInstance) advanceModalInstance.dispose();
                    advanceModalInstance = new bootstrap.Modal(advanceModalElement);
                    advanceModalInstance.show();
                } catch (error) {
                    console.error('Error showing modal:', error);
                    advanceModalElement.classList.add('show');
                    advanceModalElement.style.display = 'block';
                }
            }

            // Handle advance button click
            function handleAdvanceClick(event) {
                const btn = event.currentTarget;
                if (btn.disabled) return;

                const teacherId = btn.getAttribute('data-teacher-id');
                const teacherName = btn.getAttribute('data-teacher-name');
                const teacherEarning = parseFloat(btn.getAttribute('data-teacher-earning')) || 0;

                showAdvanceModal(teacherId, teacherName, teacherEarning);
            }

            // Attach advance button listeners
            function attachAdvanceButtonListeners() {
                document.querySelectorAll('.advance-btn').forEach(btn => {
                    btn.removeEventListener('click', handleAdvanceClick);
                    btn.addEventListener('click', handleAdvanceClick);
                });
            }

            // Filter table data
            function filterTable(searchTerm, data) {
                if (!searchTerm || !data) return data;
                const term = searchTerm.toLowerCase();
                return data.filter(teacher => {
                    const teacherName = (teacher.teacher_name || '').toLowerCase();
                    const teacherId = (teacher.teacher_id || '').toString().toLowerCase();
                    return teacherName.includes(term) || teacherId.includes(term);
                });
            }

            // Sort table data
            function sortTable(column, data) {
                if (!column || !data) return data;
                return [...data].sort((a, b) => {
                    let aValue = a[column];
                    let bValue = b[column];

                    if (column === 'teacher_name') {
                        aValue = (aValue || '').toLowerCase();
                        bValue = (bValue || '').toLowerCase();
                    } else {
                        aValue = parseFloat(aValue) || 0;
                        bValue = parseFloat(bValue) || 0;
                    }

                    if (aValue < bValue) return currentSort.direction === 'asc' ? -1 : 1;
                    if (aValue > bValue) return currentSort.direction === 'asc' ? 1 : -1;
                    return 0;
                });
            }

            // Render table with data
            function renderTable(data) {
                if (!teacherTableBody) return;

                if (!data || data.length === 0) {
                    showEmptyState(true);
                    teacherTableBody.innerHTML = '';
                    return;
                }

                showEmptyState(false);
                teacherTableBody.innerHTML = '';

                // Check if we're in the last 5 days of the month
                const today = new Date();
                const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
                const showPayButton = today.getDate() > (lastDayOfMonth - 25);

                data.forEach(teacher => {
                    const row = document.createElement('tr');

                    const totalPayments = parseFloat(teacher.total_payments_this_month) || 0;
                    const grossEarning = parseFloat(teacher.gross_teacher_earning) || 0;
                    const organizeCut = parseFloat(teacher.total_organize_cut) || 0;
                    const advanceDeducted = parseFloat(teacher.advance_deducted_this_month) || 0;
                    const netPayable = parseFloat(teacher.net_teacher_payable) || 0;
                    const institutionIncome = parseFloat(teacher.institution_income) || 0;

                    const hasBreakdown = teacher.class_wise_breakdown && teacher.class_wise_breakdown.length > 0;
                    const canAdvance = netPayable > 0 && hasBreakdown;

                    if (hasBreakdown) {
                        row.dataset.breakdown = JSON.stringify({
                            teacherName: teacher.teacher_name,
                            teacherId: teacher.teacher_id,
                            breakdown: teacher.class_wise_breakdown
                        });
                    }

                    row.innerHTML = `
        <td class="py-1"><small>${teacher.teacher_id || ''}</small></td>
        <td class="py-1 ${hasBreakdown ? 'cursor-pointer' : ''}" ${hasBreakdown ? 'onclick="window.showTeacherBreakdown(this)"' : ''}>
            <small>${escapeHtml(teacher.teacher_name || '')}</small>
            ${hasBreakdown ? '<br><small class="text-primary" style="font-size: 0.7rem;"><i class="fas fa-info-circle"></i> View breakdown</small>' : ''}
        </td>
        <td class="py-1 text-end"><small>${formatCurrency(totalPayments)}</small></td>
        <td class="py-1 text-end"><small>${formatCurrency(grossEarning)}</small></td>
        <td class="py-1 text-end"><small>${formatCurrency(organizeCut)}</small></td>
        <td class="py-1 text-end"><small>${formatCurrency(advanceDeducted)}</small></td>
        <td class="py-1 text-end">
            <small class="${netPayable > 0 ? 'text-success fw-bold' : ''}">${formatCurrency(netPayable)}</small>
        </td>
        <td class="py-1 text-end"><small>${formatCurrency(institutionIncome)}</small></td>
        <td class="py-1 text-center">
            <div class="btn-group btn-group-sm" role="group">
                <!-- View Details Button - Using API_ENDPOINTS -->
                <a href="${API_ENDPOINTS.viewTeacher(teacher.teacher_id)}" 
                   class="btn btn-info btn-sm" 
                   title="View Details">
                    <i class="fas fa-eye"></i>
                </a>
                ${showPayButton ? `
                <a href="${API_ENDPOINTS.payTeacher(teacher.teacher_id)}" 
                   class="btn btn-success btn-sm ${netPayable === 0 ? 'disabled' : ''}"
                   ${netPayable === 0 ? 'aria-disabled="true" title="No payment due"' : 'title="Make Payment"'}>
                    <i class="fas fa-money-bill-wave"></i>
                </a>
                ` : ''}
                <button type="button" class="btn btn-warning btn-sm advance-btn" 
                        data-teacher-id="${teacher.teacher_id || ''}"
                        data-teacher-name="${escapeHtml(teacher.teacher_name || '')}"
                        data-teacher-earning="${netPayable}"
                        ${!canAdvance ? 'disabled title="No net payable available"' : 'title="Make Advance Payment"'}>
                    <i class="fas fa-hand-holding-usd"></i>
                </button>
                <a href="${API_ENDPOINTS.payhistory(teacher.teacher_id)}" 
                   class="btn btn-primary btn-sm" title="Payment History">
                    <i class="fas fa-history"></i>
                </a>
            </div>
        </td>
    `;

                    teacherTableBody.appendChild(row);
                });

                attachViewDetailsListeners();
                attachAdvanceButtonListeners();
            }

            // Escape HTML to prevent XSS
            function escapeHtml(str) {
                if (!str) return '';
                return str
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            // Fetch teacher payments data
            async function fetchTeacherPayments() {
                showLoading(true);

                try {
                    const response = await fetch(API_ENDPOINTS.teacherPayments);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    if (data.status === 'success') {
                        teachersData = Array.isArray(data.data) ? data.data : [];

                        // Update month/year display
                        if (data.year_month) {
                            const [year, month] = data.year_month.split('-');
                            const date = new Date(parseInt(year), parseInt(month) - 1);
                            const monthYearText = date.toLocaleString('default', {
                                month: 'long',
                                year: 'numeric'
                            });
                            if (currentMonthYear) currentMonthYear.textContent = monthYearText;
                            if (headerMonthYear) headerMonthYear.textContent = monthYearText;
                        }

                        renderTable(teachersData);
                        updateSummary(teachersData);
                        updateRecordCount(teachersData.length);
                    } else {
                        throw new Error(data.message || 'Failed to load teacher payments');
                    }
                } catch (error) {
                    console.error('Error fetching teacher payments:', error);
                    showAlert('Failed to load teacher payments. Please try again.', 'danger');
                    teacherTableBody.innerHTML = '';
                    showEmptyState(true);
                    updateRecordCount(0);
                } finally {
                    showLoading(false);
                }
            }

            // Submit Advance Payment
            function setupAdvancePaymentForm() {
                const advancePaymentForm = document.getElementById('advancePaymentForm');
                if (!advancePaymentForm) return;

                advancePaymentForm.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const submitBtn = document.getElementById('submitAdvanceBtn');
                    const teacherId = document.getElementById('advanceTeacherId')?.value;
                    const amountInput = document.getElementById('amount');
                    const amount = parseFloat(amountInput?.value) || 0;
                    const maxAmount = parseFloat(amountInput?.max) || 0;
                    const reasonCodeSelect = document.getElementById('reasonCode');
                    const reasonCode = reasonCodeSelect?.value || '';

                    if (amountInput) amountInput.classList.remove('is-invalid');
                    if (reasonCodeSelect) reasonCodeSelect.classList.remove('is-invalid');

                    let isValid = true;

                    if (!amount || amount <= 0) {
                        if (amountInput) {
                            amountInput.classList.add('is-invalid');
                            const errorEl = document.getElementById('amountError');
                            if (errorEl) errorEl.textContent = 'Please enter a valid amount';
                        }
                        isValid = false;
                    } else if (amount > maxAmount) {
                        if (amountInput) {
                            amountInput.classList.add('is-invalid');
                            const errorEl = document.getElementById('amountError');
                            if (errorEl) errorEl.textContent = `Amount cannot exceed ${formatCurrency(maxAmount)}`;
                        }
                        isValid = false;
                    }

                    if (!reasonCode) {
                        if (reasonCodeSelect) {
                            reasonCodeSelect.classList.add('is-invalid');
                            const errorEl = document.getElementById('reasonCodeError');
                            if (errorEl) errorEl.textContent = 'Please select a reason code';
                        }
                        isValid = false;
                    }

                    if (!isValid) return;

                    const formData = {
                        teacher_id: teacherId,
                        payment: amount,
                        reason_code: reasonCode
                    };

                    try {
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <small>Processing...</small>';
                        }

                        const response = await fetch(API_ENDPOINTS.advancePayment, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(formData)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || data.error || `HTTP error! status: ${response.status}`);
                        }

                        if (data.status === 'success') {
                            if (advanceModalInstance) advanceModalInstance.hide();
                            showAlert(data.message || 'Advance payment submitted successfully!', 'success');
                            setTimeout(() => fetchTeacherPayments(), 1500);
                        } else {
                            throw new Error(data.message || 'Failed to submit advance payment');
                        }
                    } catch (error) {
                        console.error('Error submitting advance payment:', error);
                        showAlert(error.message || 'Failed to submit advance payment. Please try again.', 'danger');
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> <small>Submit Advance</small>';
                        }
                    }
                });
            }

            // Export to Excel
            function setupExportExcel() {
                if (!exportExcelBtn) return;
                exportExcelBtn.addEventListener('click', function () {
                    if (!teachersData || teachersData.length === 0) {
                        showAlert('No data to export', 'warning');
                        return;
                    }

                    try {
                        const exportData = teachersData.map(teacher => ({
                            'Teacher ID': teacher.teacher_id || '',
                            'Teacher Name': teacher.teacher_name || '',
                            'Total Payments': parseFloat(teacher.total_payments_this_month) || 0,
                            'Gross Earnings': parseFloat(teacher.gross_teacher_earning) || 0,
                            'Organize Cut': parseFloat(teacher.total_organize_cut) || 0,
                            'Advance Deducted': parseFloat(teacher.advance_deducted_this_month) || 0,
                            'Net Payable': parseFloat(teacher.net_teacher_payable) || 0,
                            'Institution Income': parseFloat(teacher.institution_income) || 0,
                            'Classes Count': teacher.class_wise_breakdown?.length || 0
                        }));

                        const ws = XLSX.utils.json_to_sheet(exportData);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Teacher Payments');
                        const timestamp = new Date().toISOString().split('T')[0];
                        XLSX.writeFile(wb, `teacher_payments_${timestamp}.xlsx`);
                        showAlert('Excel file exported successfully!', 'success');
                    } catch (error) {
                        console.error('Error exporting to Excel:', error);
                        showAlert('Failed to export Excel file. Please try again.', 'danger');
                    }
                });
            }

            // Export to PDF
            function setupExportPdf() {
                if (!exportPdfBtn) return;
                exportPdfBtn.addEventListener('click', function () {
                    if (!teachersData || teachersData.length === 0) {
                        showAlert('No data to export', 'warning');
                        return;
                    }

                    try {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF('landscape');

                        doc.setFontSize(12);
                        doc.text('Teacher Payments Report', 14, 15);
                        doc.setFontSize(8);
                        doc.text(`Month: ${currentMonthYear?.textContent || ''}`, 14, 22);
                        doc.text(`Date: ${new Date().toLocaleDateString()}`, 14, 27);

                        const tableData = teachersData.map(teacher => [
                            teacher.teacher_id || '',
                            teacher.teacher_name || '',
                            formatCurrency(parseFloat(teacher.total_payments_this_month) || 0),
                            formatCurrency(parseFloat(teacher.gross_teacher_earning) || 0),
                            formatCurrency(parseFloat(teacher.total_organize_cut) || 0),
                            formatCurrency(parseFloat(teacher.advance_deducted_this_month) || 0),
                            formatCurrency(parseFloat(teacher.net_teacher_payable) || 0),
                            formatCurrency(parseFloat(teacher.institution_income) || 0)
                        ]);

                        doc.autoTable({
                            head: [
                                ['ID', 'Name', 'Total Payments', 'Gross Earnings', 'Organize Cut', 'Advance Deducted', 'Net Payable', 'Institution Income']
                            ],
                            body: tableData,
                            startY: 35,
                            styles: {
                                fontSize: 7
                            },
                            headStyles: {
                                fillColor: [13, 110, 253],
                                textColor: [255, 255, 255]
                            },
                            margin: {
                                top: 30
                            }
                        });

                        const timestamp = new Date().toISOString().split('T')[0];
                        doc.save(`teacher_payments_${timestamp}.pdf`);
                        showAlert('PDF file exported successfully!', 'success');
                    } catch (error) {
                        console.error('Error exporting to PDF:', error);
                        showAlert('Failed to export PDF file. Please try again.', 'danger');
                    }
                });
            }

            // Setup search functionality
            function setupSearch() {
                if (!teacherSearch) return;
                let searchTimeout;
                teacherSearch.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value.trim();
                        const filteredData = searchTerm ? filterTable(searchTerm, teachersData) : teachersData;
                        renderTable(filteredData);
                        updateSummary(filteredData);
                        updateRecordCount(filteredData.length);
                    }, CONFIG.debounceDelay);
                });
            }

            // Setup clear search
            function setupClearSearch() {
                if (!clearSearch) return;
                clearSearch.addEventListener('click', function () {
                    if (teacherSearch) teacherSearch.value = '';
                    renderTable(teachersData);
                    updateSummary(teachersData);
                    updateRecordCount(teachersData.length);
                });
            }

            // Setup refresh button
            function setupRefreshButton() {
                if (!refreshBtn) return;
                refreshBtn.addEventListener('click', function () {
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> Refresh';
                    fetchTeacherPayments().finally(() => {
                        setTimeout(() => {
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                        }, 1000);
                    });
                });
            }

            // Setup table sorting
            function setupTableSorting() {
                const sortableHeaders = document.querySelectorAll('th[data-sort]');
                sortableHeaders.forEach(th => {
                    th.addEventListener('click', function () {
                        const column = this.getAttribute('data-sort');
                        if (!column) return;

                        if (currentSort.column === column) {
                            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                        } else {
                            currentSort.column = column;
                            currentSort.direction = 'asc';
                        }

                        document.querySelectorAll('th[data-sort] i').forEach(icon => {
                            icon.className = 'fas fa-sort';
                        });

                        const sortIcon = this.querySelector('i');
                        if (sortIcon) {
                            sortIcon.className = currentSort.direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                        }

                        const sortedData = sortTable(column, teachersData);
                        renderTable(sortedData);
                    });
                });
            }

            // Initialize everything
            function init() {
                console.log('Initializing Teacher Payments module...');

                if (typeof bootstrap === 'undefined') {
                    console.error('Bootstrap is not loaded');
                    showAlert('Required components failed to load. Please refresh the page.', 'danger');
                    return;
                }

                setupAdvancePaymentForm();
                setupExportExcel();
                setupExportPdf();
                setupSearch();
                setupClearSearch();
                setupRefreshButton();
                setupTableSorting();

                Promise.all([
                    fetchTeacherPayments(),
                    loadPaymentReasons()
                ]).catch(error => {
                    console.error('Error during initialization:', error);
                    showAlert('Failed to initialize page. Please refresh.', 'danger');
                });
            }

            // Start when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
@endpush