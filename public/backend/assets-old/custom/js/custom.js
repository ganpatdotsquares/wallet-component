$(function () {
    "use strict";

    $(".confirmStatusBtn").click(function () {
        var _this = $(this);
        var id = _this.data("id");
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

function openModelToChangeStatus(id, userstatus, baseUrl) {
    Swal.fire({
        title: "Select Action",
        input: "select",
        inputOptions: {
            approve: "Approve",
            decline: "Decline",
            pending: "Pending",
        },
        inputValue: userstatus,
        inputPlaceholder: "Select an action",
        showCancelButton: true,
        cancelButtonText: "Cancel",
        confirmButtonText: "Submit",
        inputValidator: (value) => {
            return new Promise((resolve) => {
                if (!value) {
                    resolve("You need to select an action!");
                } else {
                    resolve();
                }
            });
        },
    }).then((result) => {
        if (result.isConfirmed) {
            const action = result.value;
            if (action !== userstatus) {
                // Show loader without OK button
                Swal.fire({
                    title: "Updating...",
                    text: "Please wait while we update the user's approval status.",
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    showConfirmButton: false, // Hide the OK button
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    },
                });

                const url = `${baseUrl}?id=${encodeURIComponent(
                    id
                )}&action=${encodeURIComponent(action)}`;
                $.get(url, function (response) {
                    // On success, close the loading modal
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: "User status updated successfully.",
                        timer: 2000, // Auto-hide after 2 seconds
                        showConfirmButton: false, // Hide the OK button
                    }).then(() => {
                        location.reload();
                    });
                })
                    .fail(function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: "There was a problem updating the user status.",
                            timer: 2000, // Auto-hide after 2 seconds
                            showConfirmButton: false, // Hide the OK button
                        });
                    })
                    .always(() => {
                        // Hide the loader explicitly
                        Swal.close();
                    });
            } else {
                Swal.fire(
                    "No changes detected",
                    "The selected action is the same as the previous one.",
                    "info"
                );
            }
        }
    });
}


