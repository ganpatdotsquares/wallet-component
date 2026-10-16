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
                    <li class="breadcrumb-item active">Edit General Setting</li>
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
                <h4 class=""><span class="caption-subject font-green bold uppercase">Edit General Setting</span>
                    <a href="{{ route('admin.setting.general') }}" style="float:right">
                        <button type="button" class="btn btn-primary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </button>
                    </a>
                </h4>
                <div class="card-text">
                    {{-- @if (isset($settings))
                        {{ Form::model($settings, ['route' => ['admin.setting.general.update', $settings->id], 'method' => 'patch', 'id' => 'general-setting-from']) }}
                    @else
                    {{ Form::open(['route' => 'admin.setting.general.store', 'id' => 'general-setting-from']) }}
                    @endif--}}
                    {{-- @dd($settings) --}}
                    <form action="{{ route('admin.setting.general.update', $settings->id) }}"
                        method="POST" id="general-setting-from">
                        <!-- Include the CSRF token -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <!-- Use the PUT method as specified -->
                        <input type="hidden" name="_method" value="PATCH">

                        <div class="row">
                            <!-- Hidden input for manager -->
                            <input type="hidden" name="manager" value="general">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">Title<span
                                            style="color: red; font-size: 18px;"><b>*</b></span></label>
                                    <input type="text" name="title" class="form-control" required="required"
                                        placeholder="Title"
                                        value="{{ old('title',$settings->title) }}">
                                    @if($errors->has('title'))
                                    <span
                                        class="invalid-feedback d-block">{{ $errors->first('title') }}</span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="slug">Constant/Slug</label>
                                    <input type="text" name="slug" class="form-control" placeholder="Constant/Slug"
                                        value="{{ old('slug',$settings->slug) }}"
                                        {{ isset($settings) ? 'readonly' : '' }}>
                                    <p class="help-block">No space, separate each word with an underscore. (If you want it
                                        auto-generated, please leave blank)</p>
                                </div>

                                <input type="hidden" name="field_type" value="text">

                                <div class="form-group">
                                    <div class="input textarea" id="input_value">
                                        <label for="setting_textarea">Config Value<span
                                                style="color: red; font-size: 18px;"><b>*</b></span></label>
                                        <textarea name="config_value" class="form-control" required="required"
                                            placeholder="Config Value"
                                            rows="5">{{ old('config_value', $settings->config_value) }}</textarea>
                                        @if($errors->has('config_value'))
                                        <span
                                            class="invalid-feedback d-block">{{ $errors->first('config_value') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button id="general-setting-from-btn" class="btn btn-primary btn-flat" type="submit">
                            <i class="fa fa-fw fa-save"></i> Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('custom-script')
<script>
    $('#general-setting-from').validate({
        rules: {
            title: {
                required: true,
                minlength: 3,
                maxlength: 50
            },
            config_value: {
                required: true,
                maxlength: 255
            }
        },
        messages: {
            title: {
                required: "Please enter the title.",
                minlength: "Title must be at least 3 characters.",
                maxlength: "Title must not exceed 50 characters."
            },
            config_value: {
                required: "Please enter a config value.",
                maxlength: "Config value must not exceed 255 characters."
            }
        },
        errorClass: 'text-danger',
        errorElement: 'small',
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        },
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });
</script>
@endpush