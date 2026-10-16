@extends('backend.layouts.app')
@section('title', 'Settings')
@section('content')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Setting Manager</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }} ">Dashboard</a></li>
                    <li class="breadcrumb-item "><a href="{{ route('admin.setting.general') }} ">Setting Manager</a></li>
                    <li class="breadcrumb-item active">View General Setting</li>
                </ol>

            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="><span class=" caption-subject font-green bold uppercase">View General Setting</span>
                    <a href="{{ route('admin.setting.general') }}" style="float:right">
                        <button type="button" class="btn btn-primary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </button>
                    </a>

                </h4>
                <div class="card-text">
                    <div class="table-responsive mb-2">
                        <table class="table table-hover mt-4">
                            <tbody>
                                <tr>
                                    <th scope="row">Title</th>
                                    <td>{{ $settings->title }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Constant/Slug</th>
                                    <td>{{ $settings->slug }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Config Value</th>
                                    <td>{{ $settings->config_value }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Field Type</th>
                                    <td>{{ $settings->field_type }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Created</th>
                                    <td>{{ date(config('GET.admin_date_format'),strtotime($settings->created_at)) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Modified</th>
                                    <td>{{ date(config('GET.admin_date_format'),strtotime($settings->updated_at)) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- <a href="{{ url()->previous() }}" class="btn btn-warning btn-flat"
                title="Back"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a> --}}
            </div>
        </div>
    </div>
</div>
@endsection