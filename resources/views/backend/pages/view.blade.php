@extends('backend.layouts.app')
@section('title', 'CMS Page')
@section('content')
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">Content Manager</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Content Manager</li>
                <li class="breadcrumb-item active">View Content</li>
            </ol>
        </div>
    </div>
</div>
<div class="container-fluid">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title" style="width:100%;display: inline-block;">
                                <span class="caption-subject font-green bold uppercase">View Page Detail</span>
                                <a href="{{ route('admin.page') }}"> <button type="button"
                                        class="btn btn-primary btn-sm float-right"><i class="fa fa-arrow-left"></i>
                                        Back</button></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example2" class="table table-hover">
                                <thead>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $page->title }}</td>
                                    </tr>

                                    <tr>
                                        <th>Slug</th>
                                        <td>{{ $page->slug }}</td>
                                    </tr>

                                    <tr>
                                        <th>Short Description</th>
                                        <td>{{ $page->short_description }}</td>
                                    </tr>

                                    <tr>
                                        <th>Description</th>
                                        <td>{!! $page->description !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ date(config('GET.admin_date_format'), strtotime($page->created_at)) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ date(config('GET.admin_date_format'), strtotime($page->updated_at)) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>@if ( $page->status == 1) <span
                                                class="verified-text">Active</span> @else <span
                                                class="not-verified-text">Inactive </span>@endif</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@stop