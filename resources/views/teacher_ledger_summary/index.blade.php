@extends('layouts.app')

@section('title', 'Teacher Ledger')
@section('page-title', 'Teacher Ledger Summary')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Teacher Ledger</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Month Selector -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1 text-primary">
                                <i class="fas fa-book me-2"></i>Monthly Ledger Summary
                            </h5>
                            <p class="text-muted small mb-0">Teacher ledger for selected month</p>
                        </div>
                        <div class="col-md-4">
                            <form id="ledgerForm" method="GET" action="{{ route('teacher_ledger_summary.index') }}">
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 py-2">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </span>
                                    <input type="month" 
                                           class="form-control border-start-0 py-2" 
                                           id="monthSelect" 
                                           name="month" 
                                           value="{{ request('month') ?? date('Y-m') }}"
                                           required>
                                    <button type="submit" class="btn btn-primary px-3 py-2">
                                        <i class="fas fa-chart-line"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($data) && $data['status'] === 'success')
        @php $ledgerData = $data['data']; @endphp
        
        <!-- Summary Cards -->
        <div class="row mb-3">
            <div class="col-xl-3 col-md-6 mb-2">
                <div class="card border-start-2 border-start-primary shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="row g-0 align-items-center">
                            <div class="col-8">
                                <div class="text-xs fw-bold text-muted text-uppercase">Opening Balance</div>
                                <div class="h6 mb-0 fw-bold">
                                    Rs. {{ number_format($ledgerData['opening_balance'], 2) }}
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-wallet text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-2">
                <div class="card border-start-2 border-start-success shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="row g-0 align-items-center">
                            <div class="col-8">
                                <div class="text-xs fw-bold text-muted text-uppercase">Total Receipts</div>
                                <div class="h6 mb-0 fw-bold text-success">
                                    Rs. {{ number_format($ledgerData['summary']['total_receipts'], 2) }}
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-download text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-2">
                <div class="card border-start-2 border-start-danger shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="row g-0 align-items-center">
                            <div class="col-8">
                                <div class="text-xs fw-bold text-muted text-uppercase">Total Payments</div>
                                <div class="h6 mb-0 fw-bold text-danger">
                                    Rs. {{ number_format($ledgerData['summary']['total_payments'], 2) }}
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-upload text-danger fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-2">
                <div class="card border-start-2 border-start-warning shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="row g-0 align-items-center">
                            <div class="col-8">
                                <div class="text-xs fw-bold text-muted text-uppercase">Closing Balance</div>
                                <div class="h6 mb-0 fw-bold text-warning">
                                    Rs. {{ $ledgerData['summary']['closing_balance'] }}
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-balance-scale text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Banner -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-primary bg-opacity-10 border-primary py-2 mb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="far fa-calendar-alt me-2"></i>
                            <strong>{{ $ledgerData['period']['month'] }}</strong>
                            <span class="text-muted ms-2">
                                {{ $ledgerData['period']['start_date'] }} - {{ $ledgerData['period']['end_date'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-2 border-bottom">
                        <h6 class="mb-0">
                            <i class="fas fa-table me-2 text-primary"></i>Ledger Details
                        </h6>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" id="ledgerTable">
                                <thead class="table-light">
                                    <tr class="small">
                                        <th class="ps-3" style="width: 15%">
                                            <i class="far fa-calendar me-1"></i>Date
                                        </th>
                                        <th style="width: 50%">
                                            <i class="far fa-file-alt me-1"></i>Description
                                        </th>
                                        <th class="text-end" style="width: 15%">
                                            <i class="fas fa-plus-circle me-1 text-success"></i>Receipt (Rs.)
                                        </th>
                                        <th class="text-end" style="width: 15%">
                                            <i class="fas fa-minus-circle me-1 text-danger"></i>Payment (Rs.)
                                        </th>
                                        <th class="text-end pe-3" style="width: 15%">
                                            <i class="fas fa-balance-scale me-1"></i>Balance (Rs.)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Opening Balance -->
                                    <tr class="table-info">
                                        <td class="ps-3 fw-bold small">
                                            {{ date('d M Y', strtotime($ledgerData['period']['start_date'])) }}
                                        </td>
                                        <td class="fw-bold small">
                                            Opening Balance
                                        </td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end pe-3 fw-bold">
                                            {{ number_format($ledgerData['opening_balance'], 2) }}
                                        </td>
                                    </tr>
                                    
                                    <!-- Ledger Entries -->
                                    @forelse($ledgerData['ledger'] as $entry)
                                        <tr class="ledger-entry small">
                                            <td class="ps-3">
                                                {{ $entry['date'] }}
                                            </td>
                                            <td>
                                                {{ $entry['description'] }}
                                            </td>
                                            <td class="text-end">
                                                @if($entry['receipt'])
                                                    <span class="text-success fw-medium">
                                                        {{ number_format((float)str_replace(',', '', $entry['receipt']), 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($entry['payment'])
                                                    <span class="text-danger fw-medium">
                                                        {{ number_format((float)str_replace(',', '', $entry['payment']), 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3 fw-medium">
                                                {{ number_format((float)str_replace(',', '', $entry['balance']), 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">No transactions found for this month</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    
                                    <!-- Closing Summary -->
                                    <tr class="table-warning fw-bold">
                                        <td colspan="2" class="ps-3">
                                            Period Summary
                                        </td>
                                        <td class="text-end text-success">
                                            {{ number_format($ledgerData['summary']['total_receipts'], 2) }}
                                        </td>
                                        <td class="text-end text-danger">
                                            {{ number_format($ledgerData['summary']['total_payments'], 2) }}
                                        </td>
                                        <td class="text-end pe-3">
                                            {{ number_format((float)str_replace(',', '', $ledgerData['summary']['closing_balance']), 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white py-2 text-muted small">
                        <i class="fas fa-clock me-1"></i>
                        Generated on {{ date('Y-m-d H:i:s') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="row text-center">
                            <div class="col-md-3 col-6 mb-2">
                                <div class="p-2 border rounded">
                                    <div class="h5 mb-1">{{ count($ledgerData['ledger']) }}</div>
                                    <small class="text-muted">Total Transactions</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="p-2 border rounded">
                                    <div class="h5 mb-1 text-success">
                                        {{ $ledgerData['ledger']->where('receipt', '!=', '')->count() }}
                                    </div>
                                    <small class="text-muted">Receipt Entries</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="p-2 border rounded">
                                    <div class="h5 mb-1 text-danger">
                                        {{ $ledgerData['ledger']->where('payment', '!=', '')->count() }}
                                    </div>
                                    <small class="text-muted">Payment Entries</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="p-2 border rounded">
                                    <div class="h5 mb-1">
                                        @php
                                            $netChange = $ledgerData['summary']['net_change'];
                                            $textClass = $netChange >= 0 ? 'text-success' : 'text-danger';
                                        @endphp
                                        <span class="{{ $textClass }}">
                                            {{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange, 2) }}
                                        </span>
                                    </div>
                                    <small class="text-muted">Net Change</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-2 border-bottom">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-pie me-2 text-primary"></i>Visual Summary
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <div class="text-center mb-3">
                                    <h4 class="text-success mb-1">
                                        Rs. {{ number_format($ledgerData['summary']['total_receipts'], 2) }}
                                    </h4>
                                    <small class="text-muted">Total Receipts</small>
                                </div>
                                <div class="text-center mb-3">
                                    <h4 class="text-danger mb-1">
                                        Rs. {{ number_format($ledgerData['summary']['total_payments'], 2) }}
                                    </h4>
                                    <small class="text-muted">Total Payments</small>
                                </div>
                                <div class="text-center">
                                    <h4 class="text-primary mb-1">
                                        Rs. {{ number_format(abs($netChange), 2) }}
                                    </h4>
                                    <small class="text-muted">Net {{ $netChange >= 0 ? 'Income' : 'Loss' }}</small>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <canvas id="ledgerChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif(isset($data) && $data['status'] === 'error')
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $data['message'] }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto submit form on month change
            const monthSelect = document.getElementById('monthSelect');
            if (monthSelect) {
                monthSelect.addEventListener('change', function() {
                    if(this.value) {
                        document.getElementById('ledgerForm').submit();
                    }
                });
            }
            
            @if(isset($data) && $data['status'] === 'success')
                initializeChart();
            @endif
        });
        
        function initializeChart() {
            const ctx = document.getElementById('ledgerChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Receipts', 'Payments'],
                    datasets: [{
                        data: [
                            {{ $ledgerData['summary']['total_receipts'] ?? 0 }},
                            {{ $ledgerData['summary']['total_payments'] ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ],
                        borderColor: [
                            'rgb(40, 167, 69)',
                            'rgb(220, 53, 69)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: Rs. ${value.toFixed(2)} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 10px;
    }
    
    .border-start-2 {
        border-left-width: 4px !important;
    }
    
    .border-start-primary {
        border-left-color: #0d6efd !important;
    }
    
    .border-start-success {
        border-left-color: #198754 !important;
    }
    
    .border-start-danger {
        border-left-color: #dc3545 !important;
    }
    
    .border-start-warning {
        border-left-color: #ffc107 !important;
    }
    
    .table-sm td, .table-sm th {
        padding: 0.6rem;
        vertical-align: middle;
    }
    
    .table > :not(:first-child) {
        border-top: none;
    }
    
    .table-info {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .table-warning {
        background-color: rgba(255, 193, 7, 0.05);
    }
    
    .small {
        font-size: 0.85rem;
    }
    
    .h6 {
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .text-xs {
        font-size: 0.7rem;
    }
    
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    
    .alert-primary.bg-opacity-10 {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    /* Hover effect for table rows */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    /* Card shadow improvement */
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endsection