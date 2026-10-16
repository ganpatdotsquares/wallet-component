@extends('backend.layouts.app')
@section('title', 'Home Page')
@section('content')

<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Dashboard</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Info box -->
    <!-- ============================================================== -->
    <!--.row -->
    <div class="row">
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Total Users</h5>
                    <div class="d-flex align-items-center no-block m-t-20 m-b-10">
                        <h1><i class="fa fa-users text-info"></i></h1>
                        <div class="ml-auto">
                            <h1 class="text-muted">{{ $totalUsers ?? 0 }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Total Active Users</h5>
                    <div class="d-flex align-items-center no-block m-t-20 m-b-10">
                        <h1><i class="fa fa-users text-success"></i></h1>
                        <div class="ml-auto">
                            <h1 class="text-muted">{{ $activeUsers ?? 0 }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Total Transactions</h5>
                    <div class="d-flex align-items-center no-block m-t-20 m-b-10">
                        <h1><i class="fa fa-exchange-alt text-danger"></i></h1>
                        <div class="ml-auto">
                            <h1 class="text-muted">{{ $totalTransactions ?? 0 }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase">Total Ernings</h5>
                    <div class="d-flex align-items-center no-block m-t-20 m-b-10">
                        <h1><i class="ti-wallet text-purple"></i></h1>
                        <div class="ml-auto">
                            <h1 class="text-muted">{{ $adminCommission }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
    <!-- ============================================================== -->
    <!-- End Info box -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Over Visitor, Our income , slaes different and  sales prediction -->
    <!-- ============================================================== -->
    <!-- .row -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex m-b-40 align-items-center">
                        <h5 class="card-title">PROPERTIES STATS</h5>
                        <div class="ml-auto">
                            <ul class="list-inline font-12">
                                <li><i class="fa fa-circle text-cyan"></i> For Sale</li>
                                <li><i class="fa fa-circle text-primary"></i> For Rent</li>
                                <li><i class="fa fa-circle text-purple"></i> All</li>
                            </ul>
                        </div>
                    </div>
                    <div id="morris-bar-chart" style="height:352px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="card m-b-15">
                        <div class="card-body">
                            <h5 class="card-title">PROPERTY SALES INCOME</h5>
                            <div class="row">
                                <div class="col-6 m-t-30">
                                    <h1 class="text-info">$64057</h1>
                                    <p class="text-muted">APRIL 2017</p> <b>(150 Sales)</b> </div>
                                <div class="col-6">
                                    <div id="sparkline2dash" class="text-right"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card bg-purple m-b-15">
                        <div class="card-body">
                            <h5 class="text-white card-title">PROPERTY ON RENT INCOME</h5>
                            <div class="row">
                                <div class="col-6 m-t-30">
                                    <h1 class="text-white">$30447</h1>
                                    <p class="text-white">APRIL 2017</p> <b class="text-white">(110 Sales)</b> </div>
                                <div class="col-md-6 col-sm-6 col-6">
                                    <div id="sales1" class="text-right"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
    <!-- ============================================================== -->
    <!-- Comment - table -->
    <!-- ============================================================== -->
    <!-- row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">PROPERTY OVERVIEW</h5>
                    <div class="table-responsive">
                        <table class="table product-overview">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Order ID</th>
                                    <th>Property</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Steave Jobs</td>
                                    <td>#85457898</td>
                                    <td>Swanim villa</td>
                                    <td>Sold</td>
                                    <td>10-7-2017</td>
                                    <td> <span class="label label-success font-weight-100">Paid</span> </td>
                                    <td><a href="javascript:void(0)" class="text-dark p-r-10" data-toggle="tooltip" title="Edit"><i class="ti-marker-alt"></i></a> <a href="javascript:void(0)" class="text-dark" title="Delete" data-toggle="tooltip"><i class="ti-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>Varun Dhavan</td>
                                    <td>#95457898</td>
                                    <td>River view home</td>
                                    <td>On Rent</td>
                                    <td>09-7-2017</td>
                                    <td> <span class="label label-warning font-weight-100">Pending</span> </td>
                                    <td><a href="javascript:void(0)" class="text-dark p-r-10" data-toggle="tooltip" title="Edit"><i class="ti-marker-alt"></i></a> <a href="javascript:void(0)" class="text-dark" title="Delete" data-toggle="tooltip"><i class="ti-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>Ritesh Desh</td>
                                    <td>#68457898</td>
                                    <td>Gray Chair</td>
                                    <td>12</td>
                                    <td>08-7-2017</td>
                                    <td> <span class="label label-success font-weight-100">Paid</span> </td>
                                    <td><a href="javascript:void(0)" class="text-dark p-r-10" data-toggle="tooltip" title="Edit"><i class="ti-marker-alt"></i></a> <a href="javascript:void(0)" class="text-dark" title="Delete" data-toggle="tooltip"><i class="ti-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>Hrithik</td>
                                    <td>#45457898</td>
                                    <td>Pure Wooden chair</td>
                                    <td>18</td>
                                    <td>02-7-2017</td>
                                    <td> <span class="label label-danger font-weight-100">Failed</span> </td>
                                    <td><a href="javascript:void(0)" class="text-dark p-r-10" data-toggle="tooltip" title="Edit"><i class="ti-marker-alt"></i></a> <a href="javascript:void(0)" class="text-dark" title="Delete" data-toggle="tooltip"><i class="ti-trash"></i></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
