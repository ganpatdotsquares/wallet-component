@extends('backend.layouts.app')
@section('title', 'Users')
@section('content')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor"> Users</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
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
                                <form action="{{ route('admin.users.index') }}" method="get"
                                    class="w-100 d-flex align-items-center">
                                    <div class="input-group flex-grow-1 col-md-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search fa-fw"></i></span>
                                        </div>
                                        <input type="text" name="search"
                                            value="{{ request('search') }}" class="form-control"
                                            placeholder="Search by name or email">
                                    </div>
                                    <div class="form-group mb-0 col-md-2">
                                        <select name="status" class="form-control" style="width:100%">
                                            <option value=""
                                                {{ is_null(request('status')) ? 'selected' : '' }}>
                                                Select status</option>
                                            <option value="1"
                                                {{ request('status') == '1' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="0"
                                                {{ request('status') == '0' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 ml-2">
                                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                        <a href="{{ route('admin.users.index') }}"
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
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Email Verified</th>
                                        <th>@sortablelink('created_at', 'Created At')</th>
                                        <th>@sortablelink('updated_at', 'Updated At')</th>
                                        <th>Actions</th>
                                    </thead>
                                    <tbody>
                                        @if(count($users) > 0)
                                        @php
                                        $i = ($users->perPage() * ($users->currentPage() - 1));
                                        @endphp
                                        @foreach($users as $user)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <button class="btn btn-sm {{ $user->status == 1 ? 'btn-success' : 'btn-danger' }} confirmStatusBtn"
                                                    data-id="{{ $user->id }}"
                                                    data-url="{{ route('admin.users.changeStatus', $user->id) }}">
                                                    {!! config('constants.aryStatus.'.$user->status) !!}
                                                </button>
                                            </td>
                                            <td>
                                                @if ($user->email_verified_at)
                                                <span class="badge badge-success">Verified</span>
                                                @else
                                                <span class="badge badge-danger">Not Verified</span>
                                                @endif
                                            </td>
                                            <td>{{ date(config('GET.admin_date_format'), strtotime($user->created_at)) }}</td>
                                            <td>{{ date(config('GET.admin_date_format'), strtotime($user->updated_at)) ?? 'N/A' }}</td>
                                            <td>
                                                <a
                                                    href="{{ route('admin.users.show', $user->id) }}">
                                                    <button class="btn btn-info btn-sm" title="view"><i
                                                            class="fas fa-eye"></i></button>
                                                </a>

                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="8">
                                                <h6>
                                                    <center>No records Found</center>
                                                </h6>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if($users->total() > 0)
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="entries-info">
                                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of
                                    {{ $users->total() }} entries
                                </div>
                                <div class="pagination-wrapper float-right">
                                    {{ $users->links('vendor.pagination.bootstrap-4') }}
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