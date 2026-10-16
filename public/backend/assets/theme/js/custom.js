$(function () {
    "use strict";

    $(".confirmStatusBtn").click(function () {
        var _this = $(this);
        var url = _this.data("url");

        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to change the status?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, change it!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                
                window.location.href = url;

                // var queryString = new URLSearchParams(
                //     window.location.search
                // ).toString();
                // var finalUrl = `${url}?id=${id}${
                //     queryString ? "&" + queryString : ""
                // }`;

                // // Redirect to the final URL
                // window.location.href = finalUrl;
            }
        });
    });

    $(".confirmDeleteBtn").click(function () {
        var _this = $(this);
        var id = _this.data("id");
        var url = _this.data("url");

        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete this record?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                // Construct the URL with dynamic query parameters
                var queryString = new URLSearchParams(
                    window.location.search
                ).toString();
                var finalUrl = `${url}?id=${id}${
                    queryString ? "&" + queryString : ""
                }`;

                // Redirect to the final URL
                window.location.href = finalUrl;
            }
        });
    });
});

// CSRF setup
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function openModelToChangeStatus(id, currentStatus) {
    Swal.fire({
        title: "Change Withdraw Status",
        input: "select",
        inputOptions: {
            approved: "Approve",
            declined: "Decline",
            pending: "Pending"
        },
        inputValue: currentStatus,
        inputPlaceholder: "Select status",
        showCancelButton: true,
        confirmButtonText: "Update",
        cancelButtonText: "Cancel",
        preConfirm: (selectedStatus) => {
            if (!selectedStatus) {
                Swal.showValidationMessage("Please select a status.");
                return false;
            }
            return selectedStatus;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            changeStatusAjax(id, result.value);
        }
    });
}

function changeStatusAjax(id, selectedStatus) {
    $.ajax({
        url: `/admin/withdraws/${id}/status`, // Make sure this matches your Laravel route
        method: 'POST',
        data: {
            status: selectedStatus,
            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
        },
        success: function (response) {
            Swal.fire('Success!', response.message, 'success').then(() => {
                location.reload(); // Reload the page or re-fetch the data if using DataTables etc.
            });
        },
        error: function (xhr) {
            let message = 'Something went wrong.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire('Error', message, 'error');
        }
    });
}



