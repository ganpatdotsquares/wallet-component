@extends('backend.layouts.app')
@section('title', 'Social Links')
@section('content')
<div class="container-fluid">
    <div class="row page-titles" style="padding:14px 10px ">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Social Links Settings</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }} ">Dashboard</a></li>
                    <li class="breadcrumb-item">Social Links</li>
                    <li class="breadcrumb-item active">Social Links Settings</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">


            @if($errors->any())
            <div id="admin_flash" class="alert alert-danger fade in alert-dismissible show">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true" style="font-size:20px">×</span></button>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </div>
            @endif
            <form method="post" enctype="multipart/form-data" accept-charset="utf-8" role="form"
                action="{{ route('admin.setting.social.update') }}" id="logo-setting-form">
                <!-- Include the CSRF token -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="table-responsive mb-2">
                    <table class="table table-striped table-bordered" id="tab-theme-files">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Constant/Slug</th>
                                <th>Config Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($settings->count() > 0)
                            @php
                            $i = 1;
                            @endphp
                            @foreach($settings as $k => $setting)
                            <tr id="row_{{ $k }}"
                                class="row-{{ isset($setting->id) ? $setting->id : 0 }}">
                                <td style="width: 50px;">{{ $i }}.</td>
                                <td>
                                    <div class="form-group">
                                        <div class="input text required">
                                            <input type="text" name="setting[{{ $k }}][slug]"
                                                value="{{ old('setting.' . $k . '.slug') ? old('setting.' . $k . '.slug') : $setting->slug }}"
                                                class="form-control"
                                                readonly="{{ old('setting.' . $k . '.slug') ? 'false' : 'true' }}">
                                        </div>
                                        <input type="hidden" name="setting[{{ $k }}][title]"
                                            value="{{ old('setting.' . $k . '.title') ? old('setting.' . $k . '.title') : $setting->title }}">
                                        <input type="hidden" name="setting[{{ $k }}][field_type]"
                                            value="{{ old('setting.' . $k . '.field_type') ? old('setting.' . $k . '.field_type') : $setting->field_type }}">
                                        <input type="hidden" name="setting[{{ $k }}][manager]"
                                            value="{{ old('setting.' . $k . '.manager') ? old('setting.' . $k . '.manager') : $setting->manager }}">
                                        @if(isset($setting->id))
                                        <input type="hidden" name="setting[{{ $k }}][id]"
                                            value="{{ old('setting.' . $k . '.id') ? old('setting.' . $k . '.id') : $setting->id }}">
                                        @endif
                                        @if ($errors->has('setting.' . $k . '.slug'))
                                        <span class="help-block" style="color:red">
                                            {{ $errors->first('setting.' . $k . '.slug') }}
                                        </span>
                                        @endif
                                    </div>
                                </td>
                                <td>

                                    <input type="text" name="setting[{{  $k }}][config_value]"
                                        class="img_hidden button-upload img-input"
                                        value="{{ $setting->config_value }}"
                                        id="img-input_{{ $setting->id }}">
                                    <div class="error-box-validate mt-2"
                                        id="error-box-validate_{{ $setting->id }}"></div>
                                    <blade
                                        @if ($errors->has('setting.' . $k . '.config_value'))
                                        <span class="help-block" style="color:red">
                                            {{ $errors->first('setting.' . $k . '.config_value') }}
                                        </span>
                                        @endif
                                </td>
                            </tr>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-primary btn-flat" title="Submit" type="submit">
                    <i class="fa fa-fw fa-save"></i> Submit
                </button>
                {{-- <a href="{{ route('admin.setting.general') }}" class="btn
                btn-warning btn-flat" title="Back">
                <i class="fa fa-fw fa-chevron-circle-left"></i> Back
                </a> --}}
            </form>

        </div>
    </div>
</div>

@endsection