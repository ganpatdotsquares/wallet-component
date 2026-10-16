@extends('backend.layouts.app')
@section('title', 'General User')
@section('content')

<div class="container-fluid">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">View User </h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item "><a href="{{ route('admin.users.index') }}">User</a>
                    </li>
                    <li class="breadcrumb-item active">View User Detail</li>
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

                                <a href="{{ route('admin.users.index') }}">
                                    <button type="button" class="btn btn-primary btn-sm float-right">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </button>
                                </a>
                            </div>

                            <table id="user" class="table table-bordered table-striped">

                                <tbody>
                                    <tr>
                                        <th style="width:230px">Profile image</th>
                                        <td>
                                            <div class="user-img">
                                                <img id="login-user-image" src="{{$user->profile_image != null ? \Storage::url($user->profile_image) : asset('backend/images/profile/default-user.png')}} " alt="Profile image" style="width:50px">
                                            </div>
                                        </td>
                                        <th style="width:230px">Email Verified</th>
                                        <td> @if ($user->email_verified_at)
                                            <span class="badge badge-success">Verified</span>
                                            @else
                                            <span class="badge badge-danger">Not Verified</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width:230px">Name</th>
                                        <td>{{ $user->name ?? '-' }}</td>

                                        <th>Email Address</th>
                                        <td>{{ $user->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width:230px">Date of birth</th>
                                        <td>{{ $user->dob ?? '-' }}</td>

                                        <th>Gender</th>
                                        <td>{{ ucfirst($user->gender) ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone Number</th>
                                        <td>{{ $user->phone_number ?? '-' }}</td>
                                        <th>Status</th>
                                        <td>
                                            @if($user->status == 1)
                                            <span class="label label-success label-rounded"
                                                style="color: white;">Active</span>
                                            @else
                                            <span class="not-verified-text">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ date(config('GET.admin_date_format'), strtotime($user->created_at)) }}
                                        </td>

                                        <th>Updated At</th>
                                        <td>{{ date(config('GET.admin_date_format'), strtotime($user->updated_at)) }}
                                        </td>
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