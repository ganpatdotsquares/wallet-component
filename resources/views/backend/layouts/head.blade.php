<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/assets/theme/images/favicon.png') }}">
    <title>{{ config('GET.system_application_name') }}</title>
    <!-- This page CSS -->
    <!-- chartist CSS -->
    <link href="{{ asset('backend/assets/theme/css/morris.css') }}" rel="stylesheet">
    <!--Toaster Popup message CSS -->
    <link href="{{ asset('backend/assets/theme/css/jquery.toast.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('backend/assets/theme/css/style.min.css') }}" rel="stylesheet">
    <!-- Dashboard 1 Page CSS -->
    <link href="{{ asset('backend/assets/theme/css/dashboard1.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/theme/css/custom-developer.css') }}" rel="stylesheet">
</head>
