@extends('backend.layouts.app')
@section('title', 'transactions')
@section('content')

@php
    $statuses = ['pending', 'succeeded', 'failed'];
    $selectedStatus = request('status');
@endphp

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor"> Transactions</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Transactions</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-inline mb-3">
                                <form action="{{ route('admin.transactions.index') }}" method="get"
                                    class="w-100 d-flex align-items-center">
                                    <div class="input-group flex-grow-1 col-md-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search fa-fw"></i></span>
                                        </div>
                                        <input type="text" name="search"
                                            value="{{ request('search') }}" class="form-control"
                                            placeholder="Search by name">
                                    </div>
                                    <div class="form-group mb-0 col-md-2">
                                        <select name="status" class="form-control" style="width:100%">
                                            <option value="" {{ is_null($selectedStatus) || $selectedStatus === '' ? 'selected' : '' }}>
                                                Select status
                                            </option>

                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" {{ $selectedStatus === $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 ml-2">
                                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                        <a href="{{ route('admin.transactions.index') }}"
                                            class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>
                            </div>
                            <br>
                            <div class="table-responsive">
                                <table id="user" class="table table-bordered table-hover">
                                    <thead>
                                        <th>S.No</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>@sortablelink('created_at', 'Created At')</th>
                                        <th>@sortablelink('updated_at', 'Updated At')</th>
                                        <th>Actions</th>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $index => $transaction)
                                            <tr>
                                                <td>{{ $transactions->firstItem() + $index }}</td>
                                                <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                                <td>{{ ucfirst($transaction->type) }}</td>
                                                <td>${{ number_format($transaction->amount, 2) }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $transaction->status == 'succeeded' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $transaction->created_at->format(config('GET.admin_date_format', 'd M Y')) }}</td>
                                                <td>{{ $transaction->updated_at->format(config('GET.admin_date_format', 'd M Y')) }}</td>
                                                <td>
                                                    <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="btn btn-info btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9"><center>No records found.</center></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($transactions->total() > 0)
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="entries-info">
                                    Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of
                                    {{ $transactions->total() }} entries
                                </div>
                                <div class="pagination-wrapper float-right">
                                    {{ $transactions->links('vendor.pagination.bootstrap-4') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection