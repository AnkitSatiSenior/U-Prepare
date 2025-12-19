// AJAX start event: Show loader
$(document).ajaxStart(function () {

    busy(1);
});

// AJAX stop event: Hide loader
$(document).ajaxStop(function () {
    busy();
});

$(".previousButton").click(function () {
    window.history.back();
});

$(window).on("load", function () {
    $("#loader").remove();
});

$(document).on("click", ".getProjectId", function () {
    var id = $(this).data("id");
    $("#projectid").val(id);
});

function openPDF(url) {
    var pdfURL = url;
    var windowHeight = 600;
    var windowWidth = 1000;
    var windowTop = (window.screen.height - windowHeight) / 2;
    var windowLeft = (window.screen.width - windowWidth) / 2;
    var windowFeatures =
        "height=" +
        windowHeight +
        ",width=" +
        windowWidth +
        ",top=" +
        windowTop +
        ",left=" +
        windowLeft +
        ",resizable=yes,scrollbars=yes";
    window.open(pdfURL, "_blank", windowFeatures);
}

//show image Previewe
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var imagePreview = document.getElementById("image-preview");
        imagePreview.src = reader.result;
        imagePreview.style.display = "block";
    };
    reader.readAsDataURL(event.target.files[0]);
}

// form Submit ajax request code
$("#ajax-form, .ajax-form").on("submit", function (event) {
    let $form = $(this);
    event.preventDefault();
    // Clear existing error messages
    $(".error").text("");

    var url = $(this).data("action");
    var method = $(this).data("method");
    var formData = new FormData(this); // Use FormData for file upload

    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            toastr.options = {
                positionClass: "toast-top-right",
                closeButton: true,
                progressBar: true,
                timeOut: 3000,
                extendedTimeOut: 2000,
                iconClass: "toast-success-icon",
                toastClass: "custom-toast-width",
            };

            if (response.msg || response.message) {
                if(Number(response.ok)) {
                    toastr.success(response.msg);

                    if (response.url) {
                        redirectTo(response.url);
                    }

                    if(response.mud) {
                        $('.modal.mud input[type="file"]').val('');
                        handleRuleImageFetch($('.modal.mud input[name="rule_id"]').val(), $('.modal.mud input[name="project_id"]').val());
                    }

                    return;
                }

                if(!response.success) {
                    toastr.error(response.msg);
                }
            }

            if (Number(response.ok)) {
                toastr.success(response.msg);

                if ($form.hasClass("dt")) {
                    if ($gb_DataTable) {
                        $gb_DataTable.ajax.reload();
                    }
                }

                $form.find('input[name="name"]').val("");
                $form.closest("modal").find(".close").trigger("click");
            }

            if (response?.data?.epc) {
                handleBOQSData(response);
            }

            if (response.errors) {
                // Display validation errors
                var msg = Object.keys(response.errors)[0];
                msg = response.errors[msg];

                $.each(response.errors, function (field, message) {
                    var ff = field.replace(/\./g, "-");
                    $("#error-" + ff).text(message[0]);
                });
                toastr.error(msg);
            } else if (response.success) {
                // Handle successful submission
                toastr.success("Success! " + response.message);
                // toastr.success("Success! Form Submitted successfully.");
            }

            if (response.url) {
                redirectTo(response.url);
            }
        },
        error: function (jqXHR, textstatus) {
            if (textstatus == "timeout") {
                msg = messes.tiout;
            } else {
                switch (jqXHR.status) {
                    case 500:
                        msg = messes.er500;
                        break;
                    case 401:
                        msg = messes.er401;
                        break;
                    case 403:
                        msg = messes.er401;
                        break;
                    case 419:
                        msg = messes.er419;
                        break;
                    case 0:
                        msg = messes.nonet;
                        break;
                    default:
                        break;
                }
            }

            toastr.warning(msg)
        },
    }).always(function() {
        showAL = 1;
    });
});

function redirectTo(url) {
    setTimeout(function () {
        if(url == 'reload') {
            window.location.reload();
        } else {
            window.location = url;
        }
    }, 1000);
}


$("#state").on("change", function (event) {
    var state_id = $(this).val();
    var url = $(this).data("url");

    $.ajax({
        url: url,
        type: "GET",
        data: { id: state_id },
        success: function (response) {
            $("#city").removeAttr("disabled");
            if (response.data) {
                var selectElement = $("#city");

                // Clear any existing options
                selectElement.empty();
                selectElement.append(
                    $("<option>", {
                        value: "",
                        text: "SELECT CITY",
                    })
                );
                // Append new options
                $.each(response.data, function (index, item) {
                    selectElement.append(
                        $("<option>", {
                            value: item.id,
                            text: item.name,
                        })
                    );
                });
            }
        },
        error: function (err) {
            toastr.info("Error! Please Contact Admin.");
        },
    });
});

$(".addButton").click(function () {
    var aa = $(this).data("val");
    var bb = $(this).data("target");
    var num = $(this).data("num") + 1;
    $(this).data("num", num);

    // var html = '<input class="form-control" name="education[]" placeholder="job education details type here..."     />';
    // html += "<p class='error' id='error-education-"+  +"'></p>";

    // var newResponsibility = $(".responsibility-item:first").clone();
    // newResponsibility.find('input').val('');
    // container.append(newResponsibility);
});

// edit details fetch modal
$(".ajax-form-edit").on("submit", function (event) {
    event.preventDefault();

    $(".error").text("");

    var url = $(this).data("action");
    var method = $(this).data("method");
    var formData = new FormData(this); // Use FormData for file upload

    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            toastr.options = {
                positionClass: "toast-top-right",
                closeButton: true,
                progressBar: true,
                timeOut: 3000,
                extendedTimeOut: 2000,
                iconClass: "toast-success-icon",
                toastClass: "custom-toast-width",
            };

            if (response.errors) {
                // Display validation errors
                var msg = Object.keys(response.errors)[0];
                msg = response.errors[msg];
                $.each(response.errors, function (field, message) {
                    var ff = field.replace(/\./g, "-");
                    $("#editerror-" + ff).text(message[0]);
                });
                toastr.error(msg);
            } else if (response.success) {
                // Handle successful submission
                toastr.success("Success! " + response.message);
                // toastr.success("Success! Form Submitted successfully.");
                if (response.url) {
                    setTimeout(function () {
                        window.location = response.url;
                    }, 500);
                }
            }
        },
        error: function (err) {
            toastr.info("Error! Please Contact Admin.");
        },
    });
});

$(".edit-button").on("click", function () {
    var url = $(this).data("url");
    var editUrl = $(this).data("edit");

    $(".error").text("");

    $.ajax({
        url: url,
        method: "GET",
        success: function (response) {
            $.each(response, function (key, value) {
                var input = $('[data-key="' + key + '"]');
                if (input.length) {
                    input.val(value);
                }
            });

            // Open the modal
            console.log(editUrl);
            $("#editform").attr("data-action", editUrl);
            $(".editmodal").modal("show");
        },
        error: function (error) {
            console.error("Error fetching details:", error);
        },
    });
});

// Save Changes button click event
$("#save-changes").on("click", function () {
    // Close the modal after saving changes
    $("#edit-modal").modal("hide");
});
