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
                    <li class="breadcrumb-item active">Setting Manager</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="far fa-check-circle"></i>
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="far fa-check-circle"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="box-header d-flex justify-content-between">
                    <h4 class="box-title"><span class="caption-subject font-green bold uppercase">General
                            Settings</span></h4>
                    {{-- <a href="{{ route('admin.setting.general.add') }}"
                    class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus"></i> New Setting</a> --}}
                </div>
                {{-- @include('admin.layout.flash_message') --}}
                <div class="table-responsive">
                    <table class="table table-bordered  no-wrap mt-1">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>@sortablelink('title', 'Title')</th>
                                <th>@sortablelink('slug', 'Slug')</th>
                                <th>Value</th>
                                <th scope="col" class="actions" style="width: 15%;">Actions</th>
                            </tr>
                        </thead>
                        @if($settings->count() > 0)
                        <tbody>
                            @php
                            $i = ($settings->currentPage() - 1) * $settings->perPage() + 1;
                            @endphp
                            @foreach($settings as $setting)
                            <tr>
                                <td style=""> {{ $i }}. </td>
                                <td style="">{{ $setting->title }}</td>
                                <td style="">{{ $setting->slug }}</td>
                                <td style="">{{ $setting->config_value }}
                                </td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <a href="{{ url('admin/settings/general/view', $setting->id) }}"
                                            class="btn btn-info btn-sm" data-tooltip="View" data-toggle="tooltip"
                                            alt="View setting" style="z-index:0;" data-original-title="View"><i
                                                class="fa fa-fw fa-eye"></i></a>
                                        <a href="{{ url('admin/settings/general/edit', $setting->id) }}"
                                            class="btn btn-primary btn-sm mx-1" data-tooltip="Edit"
                                            data-toggle="tooltip" alt="Edit" style="z-index:0;"
                                            data-original-title="Edit"><i class="fa fa-edit"></i></a>
                                        @if($setting->is_delete == '1')
                                        <form
                                            action="{{ url('admin/settings/general/delete/' . $setting->id) }}"
                                            method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm mx-1"><i
                                                    class="far fa-trash-alt"></i></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                        </tbody>
                        @else
                        <tfoot>
                            <tr>
                                <td colspan='7' align='center'> <strong>Record Not Available</strong> </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                <div class="card-footer clearfix">
                    @if($settings->total() > 0)
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Display custom number of entries -->
                        <div class="entries-info">
                            Showing {{ $settings->firstItem() }} to {{ $settings->lastItem() }} of
                            {{ $settings->total() }} entries
                        </div>

                        <!-- Pagination -->
                        <div class="pagination-wrapper float-right">
                            {{ $settings->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection