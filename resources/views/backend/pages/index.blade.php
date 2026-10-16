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
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }} ">Dashboard</a></li>
                <li class="breadcrumb-item active">Content Manager</li>
            </ol>
            <a href="{{ route('admin.page.create') }}">
                <button type="button" class="btn btn-danger d-none d-lg-block m-l-15">
                    <i class="fa fa-plus-circle"></i> Create New
                </button>
            </a>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="content-wrapper">
        @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="far fa-check-circle"></i> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-inline">
                                <form action="{{ route('admin.page') }}" method="get" id="form">
                                    <div class="input-group">
                                        <input type="text" name="search" value="{{ $search }}" class="form-control"
                                            placeholder="Search Name">
                                        <div class="input-group-append">
                                            <button class="btn btn-sidebar btn btn-secondary" type="submit"><i
                                                    class="fas fa-search fa-fw"></i></button>
                                            <a href="{{ route('admin.page') }}"
                                                class="btn btn-secondary" title="Reload"><i class="fas fa-sync"></i></a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <br>
                            <table id="pageTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>S.N.</th>
                                        <th>@sortablelink('title', 'Title')</th>
                                        <th>Slug</th>
                                        <th>@sortablelink('created_at', 'Created At')</th>
                                        <th>@sortablelink('updated_at', 'Updated At')</th>
                                        <th>@sortablelink('status', 'Status')</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($pages) > 0)
                                    @foreach($pages as $key => $content)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $content->title }}</td>

                                        <td>{{ $content->slug }}</td>
                                        <td>{{ date(config('GET.admin_date_format'), strtotime($content->created_at)) }}
                                        </td>
                                        <td>{{ date(config('GET.admin_date_format'), strtotime($content->updated_at)) ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <button class="btn btn-sm {{ $content->status == 1 ? 'btn-success' : 'btn-danger' }} confirmStatusBtn"
                                                data-id="{{ $content->id }}"
                                                data-url="{{ route('admin.page.changestatus') }}">
                                                {!! config('constants.aryStatus.'.$content->status) !!}
                                            </button>
                                        </td>
                                        <td>
                                            <a
                                                href="{{ route('admin.page.view',['id' => $content->id]) }}">
                                                <button class="btn btn-info btn-sm" title="view"><i
                                                        class="fas fa-eye"></i></button>
                                            </a>
                                            <a
                                                href="{{ route('admin.page.edit',['id' => $content->id] + request()->query()) }}">
                                                <button class="btn btn-primary btn-sm" title="edit"><i
                                                        class="fas fa-edit"></i></button>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="8">
                                            <h6>
                                                <center>No Records Found</center>
                                            </h6>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if($pages->total() > 0)
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Display custom number of entries -->
                            <div class="entries-info">
                                Showing {{ $pages->firstItem() }} to {{ $pages->lastItem() }} of
                                {{ $pages->total() }} entries
                            </div>

                            <!-- Pagination -->
                            <div class="pagination-wrapper float-right">
                                {{ $pages->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@stop