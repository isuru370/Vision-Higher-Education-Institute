@extends('layouts.app')

@section('title', 'Teacher Income Details')
@section('page-title', 'Teacher Income Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher_payment.index') }}">Teacher Payments</a></li>
    <li class="breadcrumb-item active">Teacher Income Details</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <!-- Current Month Display -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" id="selectedMonthYear"></h6>
                                        <small class="text-muted">Showing data for selected month</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="badge bg-light text-dark border py-2 px-3 rounded">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Teacher ID: <span id="teacherIdDisplay">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Information Card -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <i class="fas fa-user-graduate text-white" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Teacher Information</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small mb-1 d-block">Name</label>
                            <h5 class="fw-bold mb-0" id="teacherName">-</h5>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="text-muted small mb-1 d-block">ID</label>
                                <p class="fw-bold mb-0" id="teacherId">-</p>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="text-muted small mb-1 d-block">Subject</label>
                                <p class="fw-bold mb-0" id="subjectName">-</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="text-muted small mb-1 d-block">Salary Status</label>
                            <span class="badge bg-warning px-3 py-2 rounded" id="salaryStatus">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <i class="fas fa-chart-bar text-white" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Financial Summary</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Top Row - Total Collections & Advance -->
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <i class="fas fa-money-bill-wave text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 small text-muted">Total Collections</h6>
                                        </div>
                                        <h4 class="fw-bold text-primary mb-1" id="totalCollections">LKR 0.00</h4>
                                        <small class="text-muted">From student payments</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <i class="fas fa-hand-holding-usd text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 small text-muted">Advance Payments</h6>
                                        </div>
                                        <h4 class="fw-bold text-warning mb-1" id="advancePayments">LKR 0.00</h4>
                                        <small class="text-muted">Paid in advance</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <i class="fas fa-cut text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 small text-muted">Organize Cut</h6>
                                        </div>
                                        <h4 class="fw-bold text-danger mb-1" id="organizeCut">LKR 0.00</h4>
                                        <small class="text-muted">Organize share</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Percentage Split Visualization -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="small text-muted mb-2">Percentage Split</h6>
                                        <div class="progress mb-2" style="height: 20px; border-radius: 10px;">
                                            <div class="progress-bar bg-primary" id="teacherPercentageBar"
                                                style="width: 0%; border-radius: 10px 0 0 10px;">
                                                <span class="small fw-bold" id="teacherPercentageTextBar">Teacher: 0%</span>
                                            </div>
                                            <div class="progress-bar bg-danger" id="organizePercentageBar"
                                                style="width: 0%;">
                                                <span class="small fw-bold" id="organizePercentageTextBar">Organize: 0%</span>
                                            </div>
                                            <div class="progress-bar bg-secondary" id="institutionPercentageBar"
                                                style="width: 0%; border-radius: 0 10px 10px 0;">
                                                <span class="small fw-bold" id="institutionPercentageTextBar">Institution: 0%</span>
                                            </div>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <small class="text-primary fw-bold" id="teacherPercentageText">0%</small>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-danger fw-bold" id="organizePercentageText">0%</small>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-secondary fw-bold" id="institutionPercentageText">0%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shares Row -->
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 border-start border-primary border-4 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <i class="fas fa-user-tie text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 small text-muted">Teacher's Share</h6>
                                        </div>
                                        <h4 class="fw-bold text-primary mb-1" id="teacherShare">LKR 0.00</h4>
                                        <small class="text-muted" id="teacherSharePercentage">0% of total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 border-start border-danger border-4 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <i class="fas fa-building text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 small text-muted">Organize's Share</h6>
                                        </div>
                                        <h4 class="fw-bold text-danger mb-1" id="organizeShare">LKR 0.00</h4>
                                        <small class="text-muted" id="organizeSharePercentage">0% of total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 border-start border-secondary border-4 h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <i class="fas fa-university text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 small text-muted">Institution's Share</h6>
                                        </div>
                                        <h4 class="fw-bold text-secondary mb-1" id="institutionShare">LKR 0.00</h4>
                                        <small class="text-muted" id="institutionSharePercentage">0% of total</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Net Payable Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card border-0 bg-warning bg-opacity-10">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1 text-muted">
                                                    <i class="fas fa-money-check-alt me-1"></i> Net Payable to Teacher
                                                </h6>
                                                <h2 class="fw-bold mb-1" id="netPayable">LKR 0.00</h2>
                                                <small class="text-muted">(Teacher's Share - Advance Payments)</small>
                                            </div>
                                            <div>
                                                <button class="btn btn-success px-4 py-2" id="payTeacherBtn" disabled
                                                    style="border-radius: 8px;">
                                                    <i class="fas fa-money-check-alt me-1"></i> Pay Teacher
                                                </button>
                                                <button class="btn btn-dark px-4 py-2 ms-2" id="printSlipBtn" disabled
                                                    style="border-radius: 8px;">
                                                    <i class="fas fa-print me-1"></i> Print Slip
                                                </button>
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

        <!-- Classes Breakdown -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <i class="fas fa-chalkboard-teacher text-white" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Classes Breakdown</h6>
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
                        <div class="d-flex align-items-center">
                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <i class="fas fa-history text-white" style="font-size: 0.9rem;"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Advance Payment History</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="advancePaymentsTable">
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
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No advance payments found</td></tr>
                                </tbody>
                            </table>
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
        body { background-color: #f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 0.5rem; border: 1px solid #e3e6f0; transition: all 0.3s ease; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important; }
        .table { font-size: 0.85rem; margin-bottom: 0; }
        .table th { font-weight: 600; color: #5a5c69; background-color: #f8f9fc; border-bottom: 2px solid #e3e6f0; padding: 0.75rem 1rem; }
        .table td { padding: 0.75rem 1rem; vertical-align: middle; border-bottom: 1px solid #e3e6f0; }
        .btn { border-radius: 0.35rem; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease; }
        .btn-success { background-color: var(--success-color); border-color: var(--success-color); }
        .btn-dark { background-color: #5a5c69; border-color: #5a5c69; }
        .progress { border-radius: 10px; background-color: #e3e6f0; }
        .progress-bar { border-radius: 10px; }
        .badge { font-size: 0.75em; font-weight: 600; padding: 0.35em 0.65em; border-radius: 0.35rem; }
        @media (max-width: 768px) {
            .card-body { padding: 1rem; }
            .table { font-size: 0.8rem; }
            .btn { padding: 0.25rem 0.5rem; font-size: 0.8rem; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        (function() {
            'use strict';

            // Get teacher ID from URL
            function getTeacherIdFromUrl() {
                const pathParts = window.location.pathname.split('/').filter(part => part);
                for (let i = pathParts.length - 1; i >= 0; i--) {
                    if (/^\d+$/.test(pathParts[i])) {
                        return pathParts[i];
                    }
                }
                return '{{ $teacher_id ?? 0 }}';
            }

            const utils = {
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                teacherId: getTeacherIdFromUrl(),
                currentMonth: (new Date().getMonth() + 1).toString().padStart(2, '0'),
                currentYear: new Date().getFullYear().toString(),

                formatCurrency(amount) {
                    let num = parseFloat(amount);
                    if (isNaN(num)) num = 0;
                    return new Intl.NumberFormat('en-LK', {
                        style: 'currency', currency: 'LKR', minimumFractionDigits: 2
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
                            day: '2-digit', month: 'short', year: 'numeric',
                            hour: '2-digit', minute: '2-digit'
                        });
                    } catch { return dateString || '-'; }
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
                    const colors = { success: '#1cc88a', error: '#e74a3b', warning: '#f6c23e', info: '#36b9cc' };
                    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
                    const toast = document.createElement('div');
                    toast.style.cssText = `position: fixed; top: 20px; right: 20px; background: ${colors[type]}; color: white; padding: 12px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 999999; animation: slideIn 0.3s ease-out; font-size: 0.875rem;`;
                    toast.innerHTML = `<i class="fas ${icons[type]} me-2"></i>${message}`;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                }
            };

            console.log('Teacher ID:', utils.teacherId);

            let state = { teacherData: null, isProcessingPayment: false };

            const elements = {
                teacherName: document.getElementById('teacherName'), teacherId: document.getElementById('teacherId'),
                teacherIdDisplay: document.getElementById('teacherIdDisplay'), subjectName: document.getElementById('subjectName'),
                salaryStatus: document.getElementById('salaryStatus'), totalCollections: document.getElementById('totalCollections'),
                advancePayments: document.getElementById('advancePayments'), teacherShare: document.getElementById('teacherShare'),
                institutionShare: document.getElementById('institutionShare'), netPayable: document.getElementById('netPayable'),
                organizeCut: document.getElementById('organizeCut'), organizeShare: document.getElementById('organizeShare'),
                teacherPercentageBar: document.getElementById('teacherPercentageBar'), institutionPercentageBar: document.getElementById('institutionPercentageBar'),
                organizePercentageBar: document.getElementById('organizePercentageBar'),
                teacherPercentageText: document.getElementById('teacherPercentageText'), institutionPercentageText: document.getElementById('institutionPercentageText'),
                organizePercentageText: document.getElementById('organizePercentageText'),
                teacherSharePercentage: document.getElementById('teacherSharePercentage'), institutionSharePercentage: document.getElementById('institutionSharePercentage'),
                organizeSharePercentage: document.getElementById('organizeSharePercentage'),
                teacherPercentageTextBar: document.getElementById('teacherPercentageTextBar'), 
                institutionPercentageTextBar: document.getElementById('institutionPercentageTextBar'),
                organizePercentageTextBar: document.getElementById('organizePercentageTextBar'),
                classesCards: document.getElementById('classesCards'),
                advancePaymentsTableBody: document.getElementById('advancePaymentsTableBody'),
                payTeacherBtn: document.getElementById('payTeacherBtn'), printSlipBtn: document.getElementById('printSlipBtn'),
                selectedMonthYear: document.getElementById('selectedMonthYear')
            };

            async function fetchTeacherData() {
                try {
                    const url = `/api/teacher-payments/monthly-income/${utils.teacherId}/${utils.currentYear}-${utils.currentMonth}`;
                    console.log('Fetching:', url);
                    
                    const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    
                    const data = await response.json();
                    console.log('API Response:', data);
                    
                    if (data.status === 'success') {
                        state.teacherData = data.data;
                        if (data.year_month) {
                            const [year, month] = data.year_month.split('-');
                            if (elements.selectedMonthYear) {
                                elements.selectedMonthYear.textContent = `${utils.getMonthName(month)} ${year}`;
                            }
                        }
                        renderAllData();
                    } else {
                        throw new Error(data.message || 'Failed to load data');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    utils.showToast('Failed to load teacher data', 'error');
                }
            }

            function renderAllData() {
                if (!state.teacherData) return;
                renderTeacherInfo();
                renderFinancialSummary();
                renderClassesCards();
                renderAdvancePayments();
            }

            function renderTeacherInfo() {
                const data = state.teacherData;
                if (elements.teacherName) elements.teacherName.textContent = data.teacher_name || '-';
                if (elements.teacherId) elements.teacherId.textContent = data.teacher_id || '-';
                if (elements.teacherIdDisplay) elements.teacherIdDisplay.textContent = data.teacher_id || '-';
                
                if (elements.subjectName && data.class_wise && data.class_wise.length > 0) {
                    elements.subjectName.textContent = data.class_wise[0].class_name || '-';
                }
                
                if (elements.salaryStatus) {
                    const isPaid = (data.salary_paid > 0);
                    elements.salaryStatus.textContent = isPaid ? 'Salary Paid' : 'Salary Not Paid';
                    elements.salaryStatus.className = `badge bg-${isPaid ? 'success' : 'warning'} px-3 py-2 rounded`;
                }
            }

            function renderFinancialSummary() {
                const data = state.teacherData;
                const totalCollections = utils.toNumber(data.total_payments || 0);
                const advancePayments = utils.toNumber(data.advance_paid || 0);
                const teacherShare = utils.toNumber(data.teacher_share || 0);
                const organizeCut = utils.toNumber(data.total_organize_cut || 0);
                const institutionShare = utils.toNumber(data.institution_income || 0);
                const netPayable = utils.toNumber(data.net_payable || 0);
                const isPaid = (data.salary_paid > 0);

                if (elements.totalCollections) elements.totalCollections.textContent = utils.formatCurrency(totalCollections);
                if (elements.advancePayments) elements.advancePayments.textContent = utils.formatCurrency(advancePayments);
                if (elements.teacherShare) elements.teacherShare.textContent = utils.formatCurrency(teacherShare);
                if (elements.organizeCut) elements.organizeCut.textContent = utils.formatCurrency(organizeCut);
                if (elements.organizeShare) elements.organizeShare.textContent = utils.formatCurrency(organizeCut);
                if (elements.institutionShare) elements.institutionShare.textContent = utils.formatCurrency(institutionShare);
                if (elements.netPayable) elements.netPayable.textContent = utils.formatCurrency(netPayable);

                let teacherPercent = 0, organizePercent = 0, institutionPercent = 0;
                if (totalCollections > 0) {
                    teacherPercent = Math.round((teacherShare / totalCollections) * 100);
                    organizePercent = Math.round((organizeCut / totalCollections) * 100);
                    institutionPercent = Math.round((institutionShare / totalCollections) * 100);
                }

                if (elements.teacherPercentageText) elements.teacherPercentageText.textContent = `${teacherPercent}%`;
                if (elements.organizePercentageText) elements.organizePercentageText.textContent = `${organizePercent}%`;
                if (elements.institutionPercentageText) elements.institutionPercentageText.textContent = `${institutionPercent}%`;
                
                if (elements.teacherSharePercentage) elements.teacherSharePercentage.textContent = `${teacherPercent}% of total`;
                if (elements.organizeSharePercentage) elements.organizeSharePercentage.textContent = `${organizePercent}% of total`;
                if (elements.institutionSharePercentage) elements.institutionSharePercentage.textContent = `${institutionPercent}% of total`;
                
                if (elements.teacherPercentageBar) elements.teacherPercentageBar.style.width = `${teacherPercent}%`;
                if (elements.organizePercentageBar) elements.organizePercentageBar.style.width = `${organizePercent}%`;
                if (elements.institutionPercentageBar) elements.institutionPercentageBar.style.width = `${institutionPercent}%`;
                
                if (elements.teacherPercentageTextBar) elements.teacherPercentageTextBar.textContent = `Teacher: ${teacherPercent}%`;
                if (elements.organizePercentageTextBar) elements.organizePercentageTextBar.textContent = `Organize: ${organizePercent}%`;
                if (elements.institutionPercentageTextBar) elements.institutionPercentageTextBar.textContent = `Institution: ${institutionPercent}%`;

                if (elements.payTeacherBtn) {
                    elements.payTeacherBtn.disabled = isPaid;
                    elements.payTeacherBtn.title = isPaid ? 'Salary already paid' : `Pay ${utils.formatCurrency(netPayable)}`;
                }
            }

            function renderClassesCards() {
                if (!elements.classesCards) return;
                elements.classesCards.innerHTML = '';
                
                const classes = state.teacherData.class_wise;
                if (!classes || classes.length === 0) {
                    elements.classesCards.innerHTML = `<div class="col-12 text-center py-4"><i class="fas fa-chalkboard-teacher fa-2x text-muted mb-3"></i><p class="text-muted">No classes found.</p></div>`;
                    return;
                }

                classes.forEach(cls => {
                    const totalStudents = utils.toNumber(cls.total_students);
                    const paidStudents = utils.toNumber(cls.paid_students);
                    const paidPercent = totalStudents > 0 ? Math.round((paidStudents / totalStudents) * 100) : 0;
                    
                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-4 mb-3';
                    card.innerHTML = `
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 small fw-bold">${cls.class_name || 'Class'}</h6>
                                    <span class="badge bg-primary">Teacher: ${cls.teacher_percentage || 0}%</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Students:</span><span class="fw-bold small">${utils.formatNumber(totalStudents)}</span></div>
                                    <div class="progress mb-2" style="height: 6px;"><div class="progress-bar bg-success" style="width: ${paidPercent}%"></div></div>
                                    <div class="row small text-center">
                                        <div class="col-4"><span class="text-success fw-bold">${utils.formatNumber(paidStudents)}</span><div class="text-muted">Paid</div></div>
                                        <div class="col-4"><span class="text-danger fw-bold">${utils.formatNumber(cls.unpaid_students || 0)}</span><div class="text-muted">Unpaid</div></div>
                                        <div class="col-4"><span class="text-info fw-bold">${utils.formatNumber(cls.free_students || 0)}</span><div class="text-muted">Free</div></div>
                                    </div>
                                </div>
                                <div class="border-top pt-2">
                                    <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Total Amount:</span><span class="fw-bold">${utils.formatCurrency(cls.total_amount || 0)}</span></div>
                                    <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Teacher (${cls.teacher_percentage || 0}%):</span><span class="fw-bold text-success">${utils.formatCurrency(cls.teacher_earning || 0)}</span></div>
                                    <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Organize (${cls.organize_percentage || 0}%):</span><span class="fw-bold text-danger">${utils.formatCurrency(cls.organize_cut || 0)}</span></div>
                                    <div class="d-flex justify-content-between"><span class="text-muted small">Institution:</span><span class="fw-bold text-secondary">${utils.formatCurrency(cls.institution_cut || 0)}</span></div>
                                </div>
                            </div>
                        </div>`;
                    elements.classesCards.appendChild(card);
                });
            }

            function renderAdvancePayments() {
                const records = state.teacherData.advance_records;
                if (!elements.advancePaymentsTableBody) return;

                if (records && records.length > 0) {
                    elements.advancePaymentsTableBody.innerHTML = '';
                    records.forEach(rec => {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td><small>${utils.formatDateTime(rec.created_at || rec.date)}</small></td><td class="fw-bold text-warning">${utils.formatCurrency(rec.payment)}</small></td><span class="badge bg-info">${rec.reason_code || 'N/A'}</span></td><td>${rec.payment_for || 'N/A'}</td><span class="badge ${rec.status ? 'bg-success' : 'bg-danger'}">${rec.status ? 'Active' : 'Deleted'}</span><td><small>${rec.user_name || 'System'}</small></td>`;
                        elements.advancePaymentsTableBody.appendChild(row);
                    });
                } else {
                    elements.advancePaymentsTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No advance payments found</td></tr>';
                }
            }

            function setupPayTeacherButton() {
                if (!elements.payTeacherBtn) return;
                elements.payTeacherBtn.addEventListener('click', async () => {
                    if (state.isProcessingPayment) return;
                    const data = state.teacherData;
                    if (!data || data.salary_paid > 0) {
                        utils.showToast('Salary already paid for this month', 'warning');
                        return;
                    }

                    const amount = utils.toNumber(data.net_payable);
                    const teacherName = data.teacher_name;
                    const monthYear = elements.selectedMonthYear?.textContent || `${utils.getMonthName(utils.currentMonth)} ${utils.currentYear}`;

                    if (!confirm(`Confirm payment of ${utils.formatCurrency(amount)} to ${teacherName} for ${monthYear}?`)) return;

                    state.isProcessingPayment = true;
                    const originalText = elements.payTeacherBtn.innerHTML;
                    elements.payTeacherBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
                    elements.payTeacherBtn.disabled = true;

                    try {
                        const response = await fetch('/api/teacher-payments', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': utils.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ teacher_id: data.teacher_id, payment: amount, reason_code: 'salary', payment_for: monthYear })
                        });
                        const result = await response.json();
                        if (result.status === 'success') {
                            utils.showToast('Salary payment successful!', 'success');
                            setTimeout(() => fetchTeacherData(), 2000);
                        } else throw new Error(result.message || 'Payment failed');
                    } catch (error) {
                        utils.showToast(error.message || 'Payment failed', 'error');
                    } finally {
                        state.isProcessingPayment = false;
                        elements.payTeacherBtn.innerHTML = originalText;
                        elements.payTeacherBtn.disabled = false;
                    }
                });
            }

            function init() {
                console.log('Initializing Teacher Income Details...');
                setupPayTeacherButton();
                fetchTeacherData();
            }

            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
            else init();
        })();
    </script>
@endpush