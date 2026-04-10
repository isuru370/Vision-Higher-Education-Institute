@extends('layouts.app')

@section('title', 'Teacher Payment Matrix')
@section('page-title', 'Teacher Payment Matrix')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher_payment.index') }}">Teacher Payments</a></li>
    <li class="breadcrumb-item active">Payment Matrix</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <input type="hidden" id="teacherId" value="{{ $teacherId ?? '' }}">

    <!-- Teacher Summary -->
    <div class="row mb-4" id="teacherSummary" style="display: none;">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Period</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="periodDisplay">-</div>
                            <div class="text-muted small mt-1" id="classesDisplay">0 classes</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-info border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Students</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="totalStudentsDisplay">0</div>
                            <div class="text-muted small mt-1">
                                <span class="text-success" id="paidStudentsDisplay">0 paid</span> •
                                <span class="text-danger" id="unpaidStudentsDisplay">0 unpaid</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Payment Rate</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="paymentRateDisplay">0%</div>
                            <div class="progress progress-sm mt-2">
                                <div id="paymentProgressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Collection</div>
                            <div class="h5 mb-0 fw-bold text-gray-800" id="totalCollectionDisplay">Rs 0</div>
                            <div class="text-muted small mt-1" id="freeCardDisplay">0 free cards</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Earnings Summary Card -->
    <div class="row mb-4" id="teacherEarningsSummary" style="display: none;">
        <div class="col-md-4 mb-3">
            <div class="card border-start border-success border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Teacher Earnings</div>
                            <div class="h4 mb-0 fw-bold" id="teacherEarningDisplay">Rs 0</div>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-user-tie text-success fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-start border-danger border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Organize Cut</div>
                            <div class="h4 mb-0 fw-bold" id="organizeCutDisplay">Rs 0</div>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-building text-danger fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-start border-secondary border-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs fw-bold text-secondary text-uppercase mb-1">Institution Income</div>
                            <div class="h4 mb-0 fw-bold" id="institutionIncomeDisplay">Rs 0</div>
                        </div>
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-university text-secondary fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Filter -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-filter me-2 text-primary"></i>Filter Data
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Year</label>
                        <select class="form-select form-select-sm" id="yearFilter">
                            @php
                                $currentYear = date('Y');
                                $years = range($currentYear, $currentYear - 5);
                            @endphp
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Month</label>
                        <select class="form-select form-select-sm" id="monthFilter">
                            @php
                                $months = ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                            @endphp
                            @foreach($months as $key => $month)
                                <option value="{{ $key }}" {{ $key == date('m') ? 'selected' : '' }}>{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Per Page</label>
                        <select class="form-select form-select-sm" id="perPageFilter">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="mb-2 w-100">
                        <button class="btn btn-primary btn-sm w-100" onclick="loadData()" id="loadBtn">
                            <i class="fas fa-sync-alt me-1"></i>Load Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div class="text-center py-5" id="loadingState">
        <div class="spinner-border text-primary" style="width: 2rem; height: 2rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h6 class="mt-3 text-primary">Loading Payment Data...</h6>
    </div>

    <!-- Error State -->
    <div class="text-center py-5" id="errorState" style="display: none;">
        <div class="text-danger mb-3"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
        <h6 class="mb-2" id="errorMessage">Error loading data</h6>
        <button class="btn btn-primary btn-sm" onclick="loadData()"><i class="fas fa-redo me-1"></i>Try Again</button>
    </div>

    <!-- No Data Message -->
    <div class="text-center py-5" id="noDataMessage" style="display: none;">
        <div class="mb-3"><i class="fas fa-database fa-3x text-muted opacity-50"></i></div>
        <h6 class="text-muted mb-2">No Data Available</h6>
        <p class="text-muted small">No payment data found for the selected period</p>
    </div>

    <!-- Class Cards Grid -->
    <div id="classCardsContainer" class="row mb-4" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-chalkboard me-2 text-primary"></i>Classes
                        <small class="text-muted ms-1" id="selectedMonthInfo"></small>
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3" id="classCardsGrid"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table with Pagination -->
    <div id="studentsContainer" class="row mb-4" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-users me-2 text-primary"></i>Students
                                <small class="text-muted ms-1" id="selectedClassInfo"></small>
                            </h6>
                            <small class="text-muted small" id="paginationInfo">Showing 0 students</small>
                        </div>
                        <div>
                            <button class="btn btn-outline-secondary btn-sm" onclick="goBackToClasses()">
                                <i class="fas fa-arrow-left me-1"></i>Back to Classes
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th width="40">#</th>
                                    <th width="100">Student ID</th>
                                    <th>Student Name</th>
                                    <th width="80">Status</th>
                                    <th width="100">Total Paid</th>
                                    <th width="80">Payments</th>
                                    <th width="80">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody"></tbody>
                            <tfoot id="studentsTableFooter" class="table-light"></tfoot>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top px-3 pb-3">
                        <div class="text-muted small"><span id="totalItems">0</span> students</div>
                        <nav aria-label="Page navigation"><ul class="pagination pagination-sm mb-0" id="paginationControls"></ul></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-3">
                    <h6 class="modal-title mb-0 fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>Payment Details</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="paymentDetailsContent"></div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .class-card { transition: all 0.2s ease; border: 1px solid #dee2e6; border-left: 3px solid #4e73df; cursor: pointer; height: 100%; }
        .class-card:hover { transform: translateY(-2px); box-shadow: 0 3px 10px rgba(0,0,0,0.08); border-color: #4e73df; }
        .class-card.selected { background-color: #f8f9fc; border-color: #28a745; border-left-color: #28a745; }
        .stats-badge { font-size: 0.7rem; padding: 0.2rem 0.4rem; }
        .progress-thin { height: 4px; border-radius: 2px; }
        .avatar-circle { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 11px; }
        .table th { font-size: 0.75rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
        .table td { font-size: 0.8rem; vertical-align: middle; padding: 0.75rem 0.5rem; }
        .badge-sm { font-size: 0.65rem; padding: 0.15rem 0.35rem; }
    </style>
@endpush

@push('scripts')
    <script>
        let matrixData = null, selectedClassId = null, selectedClassName = '', currentClassStudents = [], currentPage = 1, perPage = 25;

        document.addEventListener('DOMContentLoaded', function() {
            perPage = parseInt(document.getElementById('perPageFilter').value);
            setTimeout(() => loadData(), 100);
        });

        async function loadData() {
            const teacherId = document.getElementById('teacherId').value;
            if (!teacherId) { showErrorState('Teacher ID not found'); return; }
            
            const year = document.getElementById('yearFilter').value;
            const month = document.getElementById('monthFilter').value;
            perPage = parseInt(document.getElementById('perPageFilter').value);
            
            showLoading();
            
            try {
                const response = await fetch(`/api/teacher-payments/class-wise/${teacherId}/${year}-${month}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                });
                
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                if (data.status === 'success') {
                    matrixData = data;
                    renderTeacherSummary(data);
                    renderClassCards(data.classes || []);
                    showClassCards();
                    hideErrorState();
                } else {
                    throw new Error(data.message || 'Failed to load data');
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorState(error.message);
                hideAllContainers();
            } finally {
                hideLoading();
            }
        }

        function renderTeacherSummary(data) {
            document.getElementById('teacherSummary').style.display = 'flex';
            document.getElementById('teacherEarningsSummary').style.display = 'flex';
            
            const summary = data.summary || {};
            document.getElementById('periodDisplay').textContent = data.year_month || '-';
            document.getElementById('classesDisplay').textContent = `${summary.total_classes || 0} classes`;
            document.getElementById('totalStudentsDisplay').textContent = summary.total_students || 0;
            document.getElementById('paidStudentsDisplay').textContent = `${summary.paid_students || 0} paid`;
            document.getElementById('unpaidStudentsDisplay').textContent = `${summary.unpaid_students || 0} unpaid`;
            document.getElementById('freeCardDisplay').textContent = `${summary.free_card_students || 0} free cards`;
            document.getElementById('teacherEarningDisplay').innerHTML = `Rs ${formatNumber(summary.teacher_earning || 0)}`;
            document.getElementById('organizeCutDisplay').innerHTML = `Rs ${formatNumber(summary.total_organize_cut || 0)}`;
            document.getElementById('institutionIncomeDisplay').innerHTML = `Rs ${formatNumber(summary.institution_income || 0)}`;
            
            const totalStudents = summary.total_students || 0;
            const paidStudents = summary.paid_students || 0;
            const paymentRate = totalStudents > 0 ? Math.round((paidStudents / totalStudents) * 100) : 0;
            document.getElementById('paymentRateDisplay').textContent = `${paymentRate}%`;
            document.getElementById('paymentProgressBar').style.width = `${paymentRate}%`;
            document.getElementById('totalCollectionDisplay').innerHTML = `Rs ${formatNumber(summary.total_amount || 0)}`;
            document.getElementById('selectedMonthInfo').textContent = `for ${data.year_month || 'selected month'}`;
        }

        function renderClassCards(classes) {
            const container = document.getElementById('classCardsGrid');
            if (!classes || classes.length === 0) {
                container.innerHTML = `<div class="col-12"><div class="alert alert-info py-2"><i class="fas fa-info-circle me-2"></i>No classes found for this teacher in the selected month.</div></div>`;
                return;
            }
            
            let html = '';
            classes.forEach(cls => {
                const totalStudents = cls.total_students || 0;
                const paidStudents = cls.paid_students || 0;
                const unpaidStudents = cls.unpaid_students || 0;
                const freeCardStudents = cls.free_card_students || 0;
                const paidPercentage = totalStudents > 0 ? Math.round((paidStudents / totalStudents) * 100) : 0;
                const progressColor = paidPercentage >= 70 ? 'bg-success' : (paidPercentage >= 40 ? 'bg-warning' : 'bg-danger');
                
                html += `<div class="col-lg-4 col-md-6">
                    <div class="card class-card" onclick="selectClass(${cls.class_id}, '${escapeString(cls.class_name)}')">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div><h6 class="card-title fw-bold text-dark mb-1 small">${escapeString(cls.class_name)}</h6>
                                <p class="card-text text-muted mb-0 small"><i class="fas fa-graduation-cap me-1"></i>${cls.grade || 'N/A'} ${cls.subject ? `• ${cls.subject}` : ''}</p></div>
                                <span class="badge bg-primary stats-badge">${totalStudents} students</span>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small">Payment Rate</span>
                                    <span class="fw-bold small ${paidPercentage >= 70 ? 'text-success' : (paidPercentage >= 40 ? 'text-warning' : 'text-danger')}">${paidPercentage}%</span>
                                </div>
                                <div class="progress progress-thin"><div class="progress-bar ${progressColor}" style="width: ${paidPercentage}%"></div></div>
                            </div>
                            <div class="row g-1 mb-2">
                                <div class="col-4"><div class="text-center p-1 bg-success bg-opacity-10 rounded"><div class="fw-bold text-success small">${paidStudents}</div><small class="text-muted">Paid</small></div></div>
                                <div class="col-4"><div class="text-center p-1 bg-danger bg-opacity-10 rounded"><div class="fw-bold text-danger small">${unpaidStudents}</div><small class="text-muted">Unpaid</small></div></div>
                                <div class="col-4"><div class="text-center p-1 bg-info bg-opacity-10 rounded"><div class="fw-bold text-info small">${freeCardStudents}</div><small class="text-muted">Free</small></div></div>
                            </div>
                            <div class="mt-2 pt-2 border-top">
                                <div class="d-flex justify-content-between"><small class="text-muted">Collection</small><span class="fw-bold text-success">Rs ${formatNumber(cls.total_amount || 0)}</span></div>
                                <div class="mt-1"><small class="text-muted"><i class="fas fa-chart-line me-1"></i>Teacher: Rs ${formatNumber(cls.teacher_earning || 0)}</small></div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        }

        function selectClass(classId, className) {
            selectedClassId = classId;
            selectedClassName = unescapeString(className);
            document.querySelectorAll('.class-card').forEach(card => card.classList.remove('selected'));
            if (event.currentTarget) event.currentTarget.classList.add('selected');
            
            const selectedClass = matrixData.classes.find(cls => cls.class_id == classId);
            if (!selectedClass) return;
            
            currentClassStudents = selectedClass.students || [];
            document.getElementById('selectedClassInfo').textContent = `- ${selectedClassName} (${selectedClass.grade || 'N/A'})`;
            currentPage = 1;
            renderStudentsTable();
            document.getElementById('classCardsContainer').style.display = 'none';
            document.getElementById('studentsContainer').style.display = 'block';
        }

        function renderStudentsTable() {
            const tbody = document.getElementById('studentsTableBody');
            if (!tbody) return;
            
            const startIndex = (currentPage - 1) * perPage;
            const pageStudents = currentClassStudents.slice(startIndex, startIndex + perPage);
            const totalPages = Math.ceil(currentClassStudents.length / perPage);
            
            let totalCollection = 0;
            pageStudents.forEach(s => totalCollection += parseFloat(s.total_paid) || 0);
            
            if (pageStudents.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No students found</td></tr>`;
                document.getElementById('studentsTableFooter').innerHTML = '';
            } else {
                let html = '';
                pageStudents.forEach((student, idx) => {
                    const status = student.status || 'Unpaid';
                    const statusClass = status === 'Paid' ? 'success' : (status === 'Free' ? 'info' : 'danger');
                    html += `<tr>
                        <td class="text-muted">${startIndex + idx + 1}</td>
                        <td><span class="fw-medium small">${student.custom_id || student.student_id || 'N/A'}</span></td>
                        <td><div class="d-flex align-items-center"><div class="avatar-circle bg-primary bg-opacity-10 text-primary me-2">${getInitials(student.name)}</div><div class="fw-medium small">${escapeString(student.name)}</div></div></td>
                        <td><span class="badge bg-${statusClass} stats-badge">${status}</span></td>
                        <td><span class="fw-bold ${student.total_paid > 0 ? 'text-success' : 'text-muted'}">Rs ${formatNumber(student.total_paid)}</span></td>
                        <td><span class="badge bg-secondary stats-badge">${student.payments ? student.payments.length : 0}</span></td>
                        <td><button class="btn btn-sm btn-outline-primary" onclick='showPaymentDetails(${JSON.stringify(student).replace(/'/g, "&#39;")})' ${!student.payments || student.payments.length === 0 ? 'disabled' : ''}><i class="fas fa-eye"></i></button></td>
                    </tr>`;
                });
                tbody.innerHTML = html;
                
                document.getElementById('studentsTableFooter').innerHTML = `<tr class="bg-light"><td colspan="4" class="text-end fw-bold">Total Collection:</td><td class="fw-bold text-success">Rs ${formatNumber(totalCollection)}</td><td colspan="2"></td></tr>`;
            }
            
            document.getElementById('totalItems').textContent = currentClassStudents.length;
            document.getElementById('paginationInfo').textContent = `Showing ${Math.min(perPage, currentClassStudents.length)} of ${currentClassStudents.length} students`;
            
            let paginationHtml = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;"><i class="fas fa-chevron-left"></i></a></li>`;
            for (let i = 1; i <= totalPages && i <= 5; i++) {
                paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a></li>`;
            }
            if (totalPages > 5) paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li><li class="page-item"><a class="page-link" href="#" onclick="changePage(${totalPages}); return false;">${totalPages}</a></li>`;
            paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;"><i class="fas fa-chevron-right"></i></a></li>`;
            document.getElementById('paginationControls').innerHTML = paginationHtml;
        }

        function changePage(page) {
            const totalPages = Math.ceil(currentClassStudents.length / perPage);
            if (page < 1 || page > totalPages || page === currentPage) return;
            currentPage = page;
            renderStudentsTable();
        }

        function showPaymentDetails(student) {
            const modalContent = document.getElementById('paymentDetailsContent');
            if (!student.payments || student.payments.length === 0) {
                modalContent.innerHTML = `<div class="p-4 text-center"><i class="fas fa-receipt fa-3x text-muted mb-3"></i><h6>No Payment History</h6><p class="text-muted small">This student has no payment records yet</p></div>`;
                new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
                return;
            }
            
            let totalAmount = 0;
            student.payments.forEach(p => totalAmount += parseFloat(p.amount) || 0);
            const sortedPayments = [...student.payments].sort((a,b) => new Date(b.date) - new Date(a.date));
            
            let paymentsHtml = '';
            sortedPayments.forEach((p, idx) => {
                paymentsHtml += `<tr>
                    <td>${idx + 1}</td>
                    <td>${formatDate(p.date)}</td>
                    <td>${p.payment_for || 'N/A'}</td>
                    <td class="text-end fw-bold text-success">Rs ${formatNumber(p.amount)}</td>
                </tr>`;
            });
            
            modalContent.innerHTML = `
                <div class="p-4">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-2">${escapeString(student.name)}</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary">ID: ${student.custom_id || student.student_id}</span>
                                <span class="badge bg-${student.status === 'Paid' ? 'success' : 'danger'}">${student.status}</span>
                                <span class="badge bg-secondary">${student.payments.length} payments</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end"><div class="bg-light p-3 rounded"><h4 class="fw-bold text-success mb-0">Rs ${formatNumber(totalAmount)}</h4><small class="text-muted">Total Paid Amount</small></div></div>
                    </div>
                    <h6 class="fw-bold mb-3"><i class="fas fa-history me-2"></i>Payment History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light"><tr><th width="50">#</th><th>Date</th><th>Payment For</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>${paymentsHtml}</tbody>
                            <tfoot class="table-light"><tr><th colspan="3" class="text-end">Total:</th><th class="text-end fw-bold text-success">Rs ${formatNumber(totalAmount)}</th></tr></tfoot>
                        </table>
                    </div>
                </div>`;
            new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
        }

        function goBackToClasses() {
            document.getElementById('studentsContainer').style.display = 'none';
            document.getElementById('classCardsContainer').style.display = 'block';
            selectedClassId = null;
            currentClassStudents = [];
        }

        function showLoading() {
            document.getElementById('loadingState').style.display = 'block';
            hideAllContainers();
            const btn = document.getElementById('loadBtn');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...'; }
        }

        function hideLoading() {
            document.getElementById('loadingState').style.display = 'none';
            const btn = document.getElementById('loadBtn');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Load Data'; }
        }

        function showClassCards() {
            document.getElementById('classCardsContainer').style.display = 'block';
            document.getElementById('studentsContainer').style.display = 'none';
            document.getElementById('noDataMessage').style.display = 'none';
            document.getElementById('errorState').style.display = 'none';
        }

        function hideAllContainers() {
            ['classCardsContainer', 'studentsContainer', 'noDataMessage', 'errorState', 'teacherSummary', 'teacherEarningsSummary'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
        }

        function showErrorState(message) {
            document.getElementById('errorState').style.display = 'block';
            document.getElementById('errorMessage').textContent = message;
            hideAllContainers();
        }

        function hideErrorState() { document.getElementById('errorState').style.display = 'none'; }
        function getInitials(name) { return name ? name.split(' ').map(w => w[0]).join('').toUpperCase().substring(0,2) : '??'; }
        function formatNumber(num) { return (parseFloat(num) || 0).toLocaleString('en-US'); }
        function formatDate(dateStr) { try { return new Date(dateStr).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' }); } catch(e) { return dateStr; } }
        function escapeString(str) { return str ? str.replace(/'/g, "\\'").replace(/"/g, '&quot;') : ''; }
        function unescapeString(str) { return str ? str.replace(/\\'/g, "'").replace(/&quot;/g, '"') : ''; }
        
        document.getElementById('yearFilter').addEventListener('change', () => loadData());
        document.getElementById('monthFilter').addEventListener('change', () => loadData());
        document.getElementById('perPageFilter').addEventListener('change', function() { perPage = parseInt(this.value); if (selectedClassId) { currentPage = 1; renderStudentsTable(); } });
    </script>
@endpush