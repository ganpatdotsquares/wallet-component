@extends('backend.layouts.app')
@section('content')

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">Settings Manager</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }} ">Dashboard</a></li>
                <li class="breadcrumb-item active">Settings Manager</li>
                <li class="breadcrumb-item active">SMTP Settings</li>
            </ol>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="box-header d-flex justify-content-between">
                <h4 class="box-title"><span class="caption-subject font-green bold uppercase">SMTP Settings</span></h4>
            </div>
            <div class="box-body">
                <form method="post" accept-charset="utf-8" role="form"
                    action="{{ route('admin.setting.smtp.update') }}" id="smtp-settings-form">
                    <!-- Include the CSRF token -->
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="card-body">
                        <div class="row">
                            <!-- First Setting: SMTP Allow -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Constant/Slug<span class="text-danger"></span></label>
                                    <input type="text" name="setting[0][slug]" value="smtp_allow" class="form-control" readonly="readonly">
                                    <input type="hidden" name="setting[0][title]" value="SMTP ALLOW" class="form-control">
                                    <input type="hidden" name="setting[0][field_type]" value="text" class="form-control">
                                    <input type="hidden" name="setting[0][manager]" value="smtp" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="smtp_allow">Config Value</label>
                                    <div class="form-group field-switch-type">
                                        <label class="switch">
                                            <input type="hidden" name="setting[0][config_value]" value="0">
                                            <input type="checkbox" name="setting[0][config_value]" value="1" class="switch-status"
                                                id="smtp_allow" {{ isset($smtp[0]['config_value']) && $smtp[0]['config_value'] == 1 ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.row -->

                        <!-- Second Setting: Email Host Name -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Constant/Slug<span class="text-danger"></span></label>
                                    <input type="text" name="setting[1][slug]" value="email_host_name" class="form-control" readonly="readonly">
                                    <input type="hidden" name="setting[1][title]" value="Email Host Name" class="form-control">
                                    <input type="hidden" name="setting[1][field_type]" value="text" class="form-control">
                                    <input type="hidden" name="setting[1][manager]" value="smtp" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="smtp-host-config">Config Value</label>
                                    <input type="text" name="setting[1][config_value]"
                                        value="{{ isset($smtp[1]['config_value']) ? $smtp[1]['config_value'] : '' }}"
                                        class="form-control" placeholder="SMTP server Host" id="smtp-host-config" required="required">
                                </div>
                            </div>
                        </div><!-- /.row -->

                        <!-- Third Setting: SMTP Username -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Constant/Slug<span class="text-danger"></span></label>
                                    <input type="text" name="setting[2][slug]" value="smtp_username" class="form-control" readonly="readonly">
                                    <input type="hidden" name="setting[2][title]" value="SMTP Username" class="form-control">
                                    <input type="hidden" name="setting[2][field_type]" value="text" class="form-control">
                                    <input type="hidden" name="setting[2][manager]" value="smtp" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Config Value</label>
                                    <input type="text" name="setting[2][config_value]"
                                        value="{{ isset($smtp[2]['config_value']) ? $smtp[2]['config_value'] : '' }}"
                                        class="form-control" placeholder="SMTP Username" required="required">
                                </div>
                            </div>
                        </div><!-- /.row -->

                        <!-- Fourth Setting: SMTP Password -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Constant/Slug<span class="text-danger"></span></label>
                                    <input type="text" name="setting[3][slug]" value="smtp_password" class="form-control" readonly="readonly">
                                    <input type="hidden" name="setting[3][title]" value="SMTP password" class="form-control">
                                    <input type="hidden" name="setting[3][field_type]" value="text" class="form-control">
                                    <input type="hidden" name="setting[3][manager]" value="smtp" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Config Value</label>
                                    <input type="text" name="setting[3][config_value]"
                                        value="{{ isset($smtp[3]['config_value']) ? $smtp[3]['config_value'] : '' }}"
                                        class="form-control" placeholder="SMTP Password" required="required">
                                </div>
                            </div>
                        </div><!-- /.row -->

                        <!-- Fifth Setting: SMTP Port -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Constant/Slug<span class="text-danger"></span></label>
                                    <input type="text" name="setting[4][slug]" value="smtp_port" class="form-control" readonly="readonly">
                                    <input type="hidden" name="setting[4][title]" value="SMTP port" class="form-control">
                                    <input type="hidden" name="setting[4][field_type]" value="text" class="form-control">
                                    <input type="hidden" name="setting[4][manager]" value="smtp" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Config Value</label>
                                    <input type="text" name="setting[4][config_value]"
                                        value="{{ isset($smtp[4]['config_value']) ? $smtp[4]['config_value'] : '' }}"
                                        class="form-control" placeholder="SMTP Port" required="required">
                                </div>
                            </div>
                        </div><!-- /.row -->

                        <!-- Sixth Setting: SMTP TLS -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Constant/Slug<span class="text-danger"></span></label>
                                    <input type="text" name="setting[5][slug]" value="smtp_tls" class="form-control" readonly="readonly">
                                    <input type="hidden" name="setting[5][title]" value="SMTP Tls" class="form-control">
                                    <input type="hidden" name="setting[5][field_type]" value="text" class="form-control">
                                    <input type="hidden" name="setting[5][manager]" value="smtp" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Config Value</label>
                                    <div class="form-group field-switch-type">
                                        <label class="css-input switch switch-sm switch-primary">
                                            <input type="hidden" name="setting[5][config_value]" value="0">
                                            <input type="checkbox" name="setting[5][config_value]" value="1" class="switch-status"
                                                {{ isset($smtp[5]['config_value']) && $smtp[5]['config_value'] == 1 ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.row -->

                        <button id="smtp-settings-form-btn" class="btn btn-primary btn-flat pull-right" type="submit">
                            <i class="fa fa-fw fa-save"></i> Submit
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@push('custom-script')
<script>
    $('#smtp-settings-form').validate({
        focusInvalid: false,
        ignore: [],
        rules: {
            'setting[1][config_value]': {
                required: true
            },
            'setting[2][config_value]': {
                required: true
            },
            'setting[31][config_value]': {
                required: true
            },
            'setting[4][config_value]': {
                required: true
            },
        },

        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
        errorPlacement: function(error, element) {
            if (element.hasClass('discount-field')) {
                return false;
            } else if (element.hasClass('plan_pricing')) {
                return false;
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            $('#smtp-settings-form-btn').attr("disabled", true);
            form.submit();
        }
    });
</script>
@endpush
<style>
    .switch {
        width: 50px;
        height: 17px;
        position: relative;
        display: inline-block;
    }

    .switch input {
        display: none;
    }

    .switch .slider {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        cursor: pointer;
        background-color: #e7ecf1;
        border-radius: 30px !important;
        border: 0;
        padding: 0;
        display: block;
        margin: 12px 10px;
        min-height: 11px;
    }

    .switch .slider:before {
        position: absolute;
        background-color: #aaa;
        height: 15px;
        width: 15px;
        content: "";
        left: 0px;
        bottom: -2px;
        border-radius: 50%;
        transition: ease-in-out .5s;
    }

    .switch .slider:after {
        content: "";
        color: white;
        display: block;
        position: absolute;
        transform: translate(-50%, -50%);
        top: 50%;
        left: 70%;
        transition: all .5s;
        font-size: 10px;
        font-family: Verdana, sans-serif;
    }

    .switch input:checked+.slider:after {
        transition: all .5s;
        left: 30%;
        content: "";
    }

    .switch input:checked+.slider {
        background-color: #d3d6d9;
    }

    .switch input:checked+.slider:before {
        transform: translateX(15px);
        background-color: #007BFF;
    }
</style>
@endsection