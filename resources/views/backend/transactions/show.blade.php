@extends('backend.layouts.app')
@section('title', 'Wallet Transactions')
@section('content')

<div class="container-fluid">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">View Wallet Transaction </h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item "><a href="{{ route('admin.transactions.index') }}">Wallet Transactions</a>
                    </li>
                    <li class="breadcrumb-item active">View Wallet Transaction</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="card-title" style="width:100%;display: inline-block;">

                                <a href="{{ route('admin.transactions.index') }}">
                                    <button type="button" class="btn btn-primary btn-sm float-right">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </button>
                                </a>
                            </div>

                            <table id="user" class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th row="2">Name</th>
                                        <td>{{ $transaction->user->name ?? '-' }}</td>

                                        <th row="2">Type</th>
                                        <td>{{ ucfirst($transaction->type) ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th row="2">Amount</th>
                                        <td>${{ number_format($transaction->amount, 2) ?? '-' }}</td>

                                        <th row="2">Status</th>
                                        <td>
                                            <span class="badge badge-{{ $transaction->status == 'succeeded' ? 'success' : 'danger' }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ date(config('GET.admin_date_format'), strtotime($transaction->created_at)) }}
                                        </td>

                                        <th>Updated At</th>
                                        <td>{{ date(config('GET.admin_date_format'), strtotime($transaction->updated_at)) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td colspan="3">{!! $transaction->description ?? '-' !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection