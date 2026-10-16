@extends('backend.layouts.app')
@section('content')
<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">Edit Site Logos</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                <li class="breadcrumb-item active">State</li>
            </ol>
        </div>
    </div>
</div>
<div class="container-fluid">
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
    <div class="row">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
                <form action="{{ route('site_logos.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="card-body">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="emailname">Main Logo<span
                                        style="color: red;font-size:18px;"><b>*</b></span></label>
                                <input type="file" name="main_logo" value="{{ old('main_logo') }}"
                                    class="form-control stop_first_space" pattern="^[a-zA-Z_ ]*$"
                                    placeholder="Enter Title" id="language_name">
                                @error('main_logo')
                                <p style="color:red;">{{ $message }}</p>
                                @enderror
                                @if($siteLogo->main_logo)
                                <img src="{{ asset('upload/site_logos/' . $siteLogo->main_logo) }}"
                                    alt="Main Logo" height="100" width="100">
                                @endif
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="emailname">Main Favicon<span
                                        style="color: red;font-size:18px;"><b>*</b></span></label>
                                <input type="file" name="mail_favicon"
                                    value="{{ old('mail_favicon') }}"
                                    class="form-control stop_first_space" pattern="^[a-zA-Z_ ]*$"
                                    placeholder="Enter Title" id="language_name">
                                @error('language_name')
                                <p style="color:red;">{{ $message }}</p>
                                @enderror
                                @if($siteLogo->mail_favicon)
                                <img src="{{ asset('upload/site_logos/' . $siteLogo->mail_favicon) }}"
                                    alt="Main Logo" height="100" width="100">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop