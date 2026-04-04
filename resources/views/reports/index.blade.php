@extends('layouts.app')

@section('title', 'Reports - Savidya Education')
@section('page-title', 'Reports')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Report Management</li>
@endsection

@section('content')

<div class="row">

    {{-- ================= Payment Reports ================= --}}
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                    <div>
                        <h4 class="mb-1">Teacher Payment Reports</h4>
                        <p class="text-muted mb-0">Download daily, weekly, and teacher-wise payment reports as PDF.</p>
                    </div>
                </div>

                <div class="row g-4">

                    {{-- ================= All Teachers Reports ================= --}}
                    <div class="col-lg-6">
                        <div class="border rounded p-3 h-100 bg-light">
                            <h5 class="mb-3">All Teachers Reports</h5>

                            <div class="mb-3">
                                <label for="daily_date" class="form-label">Select Day</label>
                                <input type="date" id="daily_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="week_start_date" class="form-label">Week Start Date</label>
                                    <input type="date" id="week_start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="week_end_date" class="form-label">Week End Date</label>
                                    <input type="date" id="week_end_date" class="form-control">
                                </div>
                            </div>

                            <div class="row g-2 mt-3">
                                <div class="col-md-6">
                                    <button id="dailyAllBtn" class="btn btn-danger w-100">
                                        📄 Download Daily Report
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button id="weeklyAllBtn" class="btn btn-warning w-100">
                                        📄 Download Weekly Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= Teacher Wise Reports ================= --}}
                    <div class="col-lg-6">
                        <div class="border rounded p-3 h-100 bg-light">
                            <h5 class="mb-3">Teacher Wise Reports</h5>

                            <div class="mb-3">
                                <label for="teacher_id" class="form-label">Teacher</label>
                                <select id="teacher_id" class="form-select">
                                    <option value="">-- Select Teacher --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="teacher_daily_date" class="form-label">Select Day</label>
                                <input type="date" id="teacher_daily_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="teacher_week_start" class="form-label">Week Start Date</label>
                                    <input type="date" id="teacher_week_start" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="teacher_week_end" class="form-label">Week End Date</label>
                                    <input type="date" id="teacher_week_end" class="form-control">
                                </div>
                            </div>

                            <div class="row g-2 mt-3">
                                <div class="col-md-6">
                                    <button id="dailyTeacherBtn" class="btn btn-primary w-100">
                                        📄 Daily Teacher Report
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button id="weeklyTeacherBtn" class="btn btn-success w-100">
                                        📄 Weekly Teacher Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= Monthly Salary Slip ================= --}}
                    <div class="col-12">
                        <div class="border rounded p-3 bg-light">
                            <h5 class="mb-3">Teacher Monthly Payment Report</h5>

                            <div class="row g-3 align-items-end">

                                <div class="col-md-5">
                                    <label class="form-label">Teacher</label>
                                    <select id="salary_teacher_id" class="form-select">
                                        <option value="">-- Select Teacher --</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Month</label>
                                    <input type="month" id="year_month" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <button id="downloadReport" class="btn btn-dark w-100">
                                        📄 Download Payment Report
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>{{-- row --}}
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const teacherSelect = document.getElementById('teacher_id');
    const salaryTeacherSelect = document.getElementById('salary_teacher_id');

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function setDefaultWeekRange(startInputId, endInputId) {
        const today = new Date();
        const endDate = new Date(today);
        const startDate = new Date(today);
        startDate.setDate(today.getDate() - 6);

        document.getElementById(startInputId).value = formatDate(startDate);
        document.getElementById(endInputId).value = formatDate(endDate);
    }

    setDefaultWeekRange('week_start_date', 'week_end_date');
    setDefaultWeekRange('teacher_week_start', 'teacher_week_end');

    /* ---------- Load Teachers Dropdown ---------- */
    fetch('/api/teachers/dropdown')
        .then(response => response.json())
        .then(res => {
            if (res.data) {
                res.data.forEach(teacher => {
                    const option1 = document.createElement('option');
                    option1.value = teacher.id;
                    option1.textContent = `${teacher.custom_id} - ${teacher.fname} ${teacher.lname ?? ''}`;
                    teacherSelect.appendChild(option1);

                    const option2 = document.createElement('option');
                    option2.value = teacher.id;
                    option2.textContent = `${teacher.custom_id} - ${teacher.fname} ${teacher.lname ?? ''}`;
                    salaryTeacherSelect.appendChild(option2);
                });
            }
        })
        .catch(() => {
            alert('Failed to load teachers');
        });

    /* ---------- Daily All Teachers Report ---------- */
    document.getElementById('dailyAllBtn').addEventListener('click', function () {
        const day = document.getElementById('daily_date').value;

        if (!day) {
            alert('Please select a date');
            return;
        }

        const url = `/teacher-payment/daily?day=${day}`;
        window.open(url, '_blank');
    });

    /* ---------- Weekly All Teachers Report ---------- */
    document.getElementById('weeklyAllBtn').addEventListener('click', function () {
        const startDate = document.getElementById('week_start_date').value;
        const endDate = document.getElementById('week_end_date').value;

        if (!startDate || !endDate) {
            alert('Please select start and end dates');
            return;
        }

        if (startDate > endDate) {
            alert('Start date cannot be greater than end date');
            return;
        }

        const url = `/teacher-payment/weekly?start_date=${startDate}&end_date=${endDate}`;
        window.open(url, '_blank');
    });

    /* ---------- Daily Teacher Report ---------- */
    document.getElementById('dailyTeacherBtn').addEventListener('click', function () {
        const teacherId = document.getElementById('teacher_id').value;
        const day = document.getElementById('teacher_daily_date').value;

        if (!teacherId || !day) {
            alert('Please select teacher and date');
            return;
        }

        const url = `/teacher-payment/daily/teacher?teacher_id=${teacherId}&day=${day}`;
        window.open(url, '_blank');
    });

    /* ---------- Weekly Teacher Report ---------- */
    document.getElementById('weeklyTeacherBtn').addEventListener('click', function () {
        const teacherId = document.getElementById('teacher_id').value;
        const startDate = document.getElementById('teacher_week_start').value;
        const endDate = document.getElementById('teacher_week_end').value;

        if (!teacherId || !startDate || !endDate) {
            alert('Please select teacher, start date, and end date');
            return;
        }

        if (startDate > endDate) {
            alert('Start date cannot be greater than end date');
            return;
        }

        const url = `/teacher-payment/weekly/teacher?teacher_id=${teacherId}&start_date=${startDate}&end_date=${endDate}`;
        window.open(url, '_blank');
    });

    /* ---------- Download Monthly Salary Slip ---------- */
    document.getElementById('downloadReport').addEventListener('click', function () {

        const teacherId = document.getElementById('salary_teacher_id').value;
        const yearMonth = document.getElementById('year_month').value;

        if (!teacherId || !yearMonth) {
            alert('Please select Teacher and Month');
            return;
        }

        const url = `/teacher-payment/salary-slip/${teacherId}/${yearMonth}`;
        window.open(url, '_blank');
    });

});
</script>
@endpush