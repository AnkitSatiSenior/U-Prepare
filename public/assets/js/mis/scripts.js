/**
 * MIS Panel Common Scripts
 *
 * @author Robin Tomar <robintomr@icloud.com>
 * @version 1.6.1
 * @Consultants Cafal Advisors, AdxVenture
 *
 * @copyright USDMA
 */
// Global Variables
$body = $("body");
$baseURL = window.location.origin;
$csrfToken = null;

//
var messes = {
    nonet: "Connection to server failed, check your network connection!",
    tiout: "The request is timed out, check your connection speed!",
    er419: "Your current session has expired please refresh page.",
    er401: "You are not having sufficient privileges to perform this action.",
    er500: "An unexpected error has occurred on server. Please report to webmaster.",
};

// Toastr Alert Library Options
toastr.options = {
    debug: false,
    onclick: null,
    timeOut: "12000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
    closeButton: false,
    newestOnTop: true,
    progressBar: false,
    showDuration: "500",
    hideDuration: "1000",
    positionClass: "toast-bottom-right",
    extendedTimeOut: "1000",
    preventDuplicates: false,
};

function handleBOQSData(response) {
    if (Number(response.data.epc)) {
        $(".modal.cms_pub").find("button.btn-secondary").trigger("click");

        let $btnm = $(".btn-cmspup");
        let $xcon = $btnm.closest(".x_panel").find(".x_content");

        $btnm.remove();

        fetchBOQData(response.data.id);
    }
}

// Fetch BOQ Sheet Data for Contract
function fetchBOQData(id, en, mn, vw) {
    en = typeof en != 'undefined' ? en : 0;
    mn = typeof mn != 'undefined' ? mn : 0;
    vw = typeof vw != 'undefined' ? vw : 0;

    let $xcon = $(".x_panel.msxp");
    let $table = $xcon.find('table.table-boq-sheet');

    if($table.length) {
        $table.find('.table-boq-sheet input.live').each(function(el, i) {
            $(el).off('blur');
        });

        $table.remove();
    };

    $(".feda-card").remove();

    $xcon.append(
        '<div class="card-body text-center feda-card"><img src="http://u-prepare.local/assets/img/svg/img_loader.svg" width="48"><p>Please Wait...</p><b class="m-0">Fetching BOQ Sheet Data</b></div>'
    );

    scrollTo($xcon.parent(), 0, 500);

    let fd = newFormData('');
        fd.append('view', vw);
        fd.append('entry', en);
        fd.append("contract_id", id);

    if(mn) {
        // fd.append('month', mn);
        fd.append('date', mn);
    }

    let pm = {
        url: `${$baseURL}/get-contract-boq-data`,
        data: fd,
    };

    // let bs = () => busy(1, "Fetching BOQ...");
    let bs = () => null;

    let cb = (resp) => {
        $(".feda-card").remove();
        // console.log("Appending Boq to", $xcon);

        $xcon.append(resp.boq);

        scrollTo($xcon.parent(), 0, 1000);

        // const currencyFormatter = new Intl.NumberFormat('en-IN', {
        //     style: 'currency',
        //     currency: 'INR',
        //     maximumFractionDigits: 2,
        //     // minimumFractionDigits: 0,
        // });

        document.querySelectorAll(".calca").forEach(function (el, i) {
            el.innerText = currencyFormatter.format(el.innerText);
        });

        document.querySelectorAll(".calcr").forEach(function (el, i) {
            el.innerText = currencyFormatter.format(el.innerText);
        });

        document.querySelectorAll(".fbtots").forEach(function (el, i) {
            el.innerText = currencyFormatter.format(el.innerText);
        });

        // Calculate & Update Up to Date Quantity Column
        document.querySelectorAll('.calcl_utq').forEach(function(el, i) {
            updateUTDRE(el.parentElement);
        });

        // document.querySelectorAll('.calcl_uta').forEach(function(el, i) {
        //     updateUTDRE(el);
        // })

        // Update Currently Editing Values Total
        setTimeout(()=> {
            updateUptoDateTotals();
            updateCurrentlyEditableTotals();
        }, 1500);

        if(!vw) {
            document.querySelectorAll('.table-boq-sheet input.live').forEach(function(el, i) {
                // el.addEventListener('keypress', function(e) {
                //     if ( (e.keyCode || e.which) === 13) {
                //         e.preventDefault();
                //         alert('Input Value Changed');
                //     }
                // });
                updateEntryAmount($(el));

                $(el).on('blur', function(e) {
                    if(parseFloat($(this).val()) >= 0) {
                        console.log($(this).val());
                        let $esh = $(this).attr('_esheet');
                        let $url = `${$baseURL}/contract/milestone/epc-boq-entry`;
                        // Send Ajax to update record
                        let fd = newFormData();
                            fd.append('qty', $(this).val());
                            fd.append('boq_id', $(this).attr('_bid'));
                            // fd.append('boq_mid', $(this).attr('_mid'));
                            fd.append('boq_date', $(this).attr('_date'));

                        if($esh) {
                            $url = `${$baseURL}/contract/milestone/boq-sheet-edit`;
                        }

                        let pm = {
                            url: $url,
                            data: fd
                        }

                        let bs = () => {}
                        let cb = (resp) => {
                            updateEntryAmount($(el));
                            updateUTDRE(el.parentElement.parentElement);
                            updateCurrentlyEditableTotals();
                            updateUptoDateTotals();
                        }
                        let al = (err) => {
                            if(err) {
                                $(el).val('');
                                updateEntryAmount($(el));
                            }
                        }

                        // if(parseFloat($(this).val())) {
                            ajaxify(pm, bs, cb, al)
                        // }
                    }else {
                        if($(this).val().length) {
                            $(this).val('');
                            showMsg('error', 'Negative values are not allowed!');
                        }
                    }
                });
            });
        }
    };

    showAL = 0;
    ajaxify(pm, bs, cb);
}

function updateEntryAmount($el) {
    // let $mid = $el.attr('_mid');
    let $mid = $el.attr('_date');
        $mid = $mid.replace(/-/g, "");
    let $ntr = $el.closest('tr');
    let rate = $ntr.find('.calcr').text();
        rate = rate.replace(/₹/g, '');
        rate = rate.replace(/,/g, '').trim();

    let $iv = parseFloat($el.val());
        $iv = $iv ? $iv : 0;

    // if(parseFloat($el.val())) {
        $ntr.find(`.calcl_${$mid}`).text(currencyFormatter.format(Number(rate) * $iv));
    // }
}

function updateUTDRE(tr) {
    let el   = tr.querySelector('.calcl_utq');

    // Update Qty First
    let pbq = tr.querySelector('.pbq');
        pbq = pbq ? Number(pbq.innerText) : null;
    let ceq = tr.querySelector('input');
        ceq = ceq ? Number(ceq.value) : null;

    if(pbq || ceq) {
        pbq = pbq || 0;
        ceq = ceq || 0;

        el.innerText = pbq + ceq;
    }


    let utda = tr.querySelector('.calcl_uta');
    let rate = tr.querySelector('.calcr');

    if(rate && rate.innerText.length) {
        rate = rate.innerText.replace(/₹/g, '');
        rate = rate.replace(/,/g, '').trim();
        rate =  Number(rate);
    }else {
        rate = null;
    }

    let qty  = tr.querySelector('.calcl_utq');
        qty  = qty && qty.innerText.length ? Number(qty.innerText) : 0;

    if(rate != null) {
        utda.innerText = currencyFormatterFraction.format(rate * qty);
    }
}

function updateCurrentlyEditableTotals() {
    let $total = 0;
    document.querySelectorAll('.calcl_cev').forEach(function (el, i) {
        let $cev = $(el).text();
            $cev = $cev.replace(/₹/g, '');
            $cev = $cev.replace(/,/g, '').trim();
            $cev = parseFloat($cev);

        if($cev) {
            $total += $cev;
        }
    });

    let $gst = ($total * 18) / 100;

    $('.cea_tot').text(currencyFormatter.format($total));
    $('.cegst_tot').text(currencyFormatter.format($gst));
    $('.ceg_tot').text(currencyFormatter.format($gst + $total));
}

function updateUptoDateTotals() {
    let $total = 0;
    document.querySelectorAll('.calcl_uta').forEach(function (el, i) {
        let $cev = $(el).text();
            $cev = $cev.replace(/₹/g, '');
            $cev = $cev.replace(/,/g, '').trim();
            $cev = parseFloat($cev);

        if($cev) {
            $total += $cev;
        }
    });

    let $gst = ($total * 18) / 100;

    $('.uda_tot').text(currencyFormatter.format($total));
    $('.udgst_tot').text(currencyFormatter.format($gst));
    $('.udg_tot').text(currencyFormatter.format($gst + $total));
}

function loadSafeGuardSheet(data) {
    let $esp = $('.x_content.sres');

    let fd = newFormData();
        fd.append('view', data.view);
        fd.append('type', data.sgtyp);
        fd.append('step', data.sgptyp);
        fd.append('date', data.date);
        fd.append('project_id', data.project);

    $('input[name="entry-date"]').off('click', 'change');
    $esp.find('a.btn-ssr').off('click');
    $esp.find('select[name="applicable"]').off('click');

    $(".feda-card").remove();

    $esp.html(
        '<div class="card-body text-center feda-card"><img src="http://u-prepare.local/assets/img/svg/img_loader.svg" width="48"><p>Please Wait...</p><b class="m-0">Fetching Entry Sheet</b></div>'
    );

    pm = {
        url: `${$baseURL}/mis/project/tracking/safeguard-entry-sheet`,
        data: fd
    }

    bs = () => {}

    cb = (resp) => {
        $(".feda-card").remove();

        $esp.append(resp.data);

        initDatePicker($('.sres table').get(0));

        $('input[name="entry-date"').on('click', function(e) {
            e.preventDefault();

            $(this).get(0).showPicker();
        });

        $('input[name="entry-date"]').on('change', function(e) {
            e.preventDefault();

            $data.date = $(this).val();

            loadSafeGuardSheet($data);
        });

        if(!data.view) {
            $esp.find('a.btn-ssr').on('click', function(e) {
                e.preventDefault();

                handleSafeguardEntrySave($(this).closest('tr'));
            });

            $esp.find('select[name="applicable"]').on('change', function(e) {
                e.preventDefault();

                let tr = $(this).closest('tr');

                if(Number($(this).val())) {
                    tr.find('input').removeAttr('disabled');
                    tr.find('textarea').removeAttr('disabled');
                } else {
                    tr.find('input').attr('disabled', 'disabled');
                    tr.find('textarea').attr('disabled', 'disabled');
                }
            });

            $esp.find('select[name="validity"]').on('change', function(e) {
                e.preventDefault();
                let vdi = $(this).next();

                if(parseInt($(this).val())) {
                    vdi.removeClass('d-none');
                } else {
                    vdi.addClass('d-none');
                    vdi.val('');
                }
            });

            $esp.find('select[name="applicable"]').trigger('change');
        }

        $esp.find('.btn-mud').on('click', function(e) {
            e.preventDefault();
            let btn = $(this);
            let rid = parseInt(btn.closest('tr').attr('_rid'));
            let pid = parseInt(btn.closest('tr').attr('_pid'));

            $('.modal.mud').addClass('show d-block');
            $('.modal.mud input[name="rule_id"]').val(rid);

            handleRuleImageFetch(rid, pid, data.view);
        });
    }

    ajaxify(pm, bs, cb);
}


//
function handleSafeguardEntrySave(el) {
    let fd        = newFormData();
    let vdate     = el.find('input[name="validity-date"]');
    let numbr     = el.find('input[name="number"]');
    let select    = el.find('select[name="applicable"]');
    let txtarea   = el.find('textarea');
    let validity  = el.find('select[name="validity"]');
    let fileInput = el.find('input[type="file"]');

    fd.append('date', $('input[name="entry-date"]').val());
    fd.append('rule_id', el.attr('_rid'));
    fd.append('project_id', el.attr('_pid'));

    if(fileInput.length) {
        fileInput = fileInput.get(0);

        let len = fileInput.files.length, reader, file;

        for (let i=0; i < len; i++) {
            file = fileInput.files[i];

            if (window.FileReader) {
                reader = new FileReader();
                reader.onloadend = function(e) {
                    // showUploadedItem(e.target.result, file.fileName);
                };
                reader.readAsDataURL(file);
            }

            if (fd) {
                fd.append("files[]", file);
            }
        }
    }

    fd.append('remark', txtarea.val());
    fd.append('number', numbr.length ? numbr.val() : null);
    fd.append('validity', validity.val());
    fd.append('applicable', select.val());
    fd.append('validity_date', vdate.val());

    let pm = {
        url: `${$baseURL}/mis/project/tracking/safeguard-entry-save`,
        data: fd
    }

    let bs = () => {}

    let cb = (resp) => {
        if(resp.url && resp.url == 'reload') {
            window.location.reload();
        }
    }

    ajaxify(pm, bs, cb);
}

//
function handleRuleImageFetch(rid, pid, viw) {
    let $tel   = '';
    let $table = $('.modal.mud table tbody');
    let $dhtml = '<tr><td colspan="4" class="text-center"><img src="http://u-prepare.local/assets/img/svg/img_loader.svg" width="32px"><br>Updating Records</td></tr>';

    $table.html($dhtml);

    if(rid && pid) {
        let fd = newFormData();
            fd.append('view', viw);
            fd.append('rule_id', rid);
            fd.append('project_id', pid)

        let pm = {
            url: `${$baseURL}/mis/project/tracking/safeguard-entry-images`,
            data: fd
        }

        let bs = () => {}
        let cb = (resp) => {
            if(resp.data) {
                $table.html(resp.data);

                lightbox.option({
                    'wrapAround': true,
                    'resizeDuration': 200,
                })
            } else {
                $table.html('<tr><td colspan="4" class="text-center">No Records Found!</td></tr>');
            }
        }

        ajaxify(pm, bs, cb);
    } else {
        alert('Invalid Rule/Project Id');
    }
}

// Body Overflow Hide
function bodyOFSH(act) {
    act
        ? $body.addClass("overflow-hidden")
        : $body.removeClass("overflow-hidden");
}

// Method to Show Toastr Messages
function showMsg(type, msg) {
    toastr[type](msg);
}

// Method to Redirect URL
function redirect(url) {
    window.location.href = url;
}

// Create New Form Data
function newFormData() {
    let fd = new FormData();
    fd.append("_token", $csrfToken);

    return fd;
}

// Method to Focus Div in View
function scrollTo(el, om, dr) {
    om = typeof om !== "undefined" ? om : 0;
    dr = typeof dr !== "undefined" ? dr : 2000;

    let ot = el.offset().top + om;

    $("html, body").animate(
        {
            scrollTop: ot,
        },
        dr
    );
}

// Method to Show Alert Box
function alertBox(typ, data, cb) {
    let $icon = "";
    switch (typ) {
        case "err":
            $icon = "exclamation-diamond-fill.svg";
            break;
        case "warn":
            $icon = "exclamation-triangle-fill.svg";
            break;
        case "info":
            $icon = "exclamation-circle-fill.svg";
            break;
        default:
            $icon = "";
            break;
    }

    $icon = `${$baseURL}/assets/img/svg/icon/${$icon}`;

    let $html = `<div class="popup fixed dialog center">
        <div class="box ${typ} d-flex colm">
            <div class="content d-flex aic">
                <div class="img">
                    <img src="${$icon}" />
                </div>
                <div class="pl-3">
                    <h1 class="m-0">${data.heading}</h1>
                    <p class="txt">${data.text}</p>
                </div>
            </div>
            <div class="foot d-flex jcse">
                <button class="btn-can mr-3">Cancel</button>
                <button class="btn-ok">Confirm</button>
            </div>
        </div>
    </div>`;

    $ppb = $body.find(".popup.dialog");

    if ($ppb.length) {
        $ppb.find("button").off("click");
        $ppb.remove();
    }

    $body.append($html);

    $body.find(".popup.dialog").addClass("d-flex");

    // Init JS Handler
    $body.find(".popup.dialog .btn-can").on("click", function (e) {
        e.preventDefault();

        let $ppb = $(this).closest(".popup");

        $ppb.find("button").off("click");
        $ppb.remove();
    });

    $body.find(".popup.dialog .btn-ok").on("click", function (e) {
        e.preventDefault();

        cb();
    });
}

// Method to Show Busy Indicator
function busy(show, txt) {
    txt = typeof txt != undefined ? txt : "Processing...";
    show = typeof show != undefined ? show : 0;

    let $html = `<div class="popup fixed center busy d-flex">
        <div class="card">
            <div class="card-body text-center">
                <img src="${$baseURL}/assets/img/svg/img_loader.svg">
                <p class="m-0">Processing...</p>
            </div>
        </div>
    </div>`;

    bodyOFSH(0);
    $body.find(".popup.busy").remove();

    if (show) {
        bodyOFSH(1);
        $body.append($html);
    }
}

// Init Date Picker on Click
function initDatePicker(el) {
    let selector = 'input[type="date"]:not(:disabled):not([readonly])';
    let $targets = '';

    if(typeof el !== 'undefined') {
        $targets = $(el).find(selector);
    } else {
        $targets = $(selector);
    }

    $targets.on('click', function(e) {
        e.preventDefault();

        $(this).get(0).showPicker();
    });
}

// Common Method for Handling Ajax Requests
function ajaxify(init, bSend, callback, always, method) {
    let err = 1;
    let msg = "";
    let ret = null;

    method = typeof method != "undefined" ? method : "POST";

    $.ajax({
        url: init.url,
        type: method,
        data: init.data,
        enctype: "multipart/form-data",
        processData: false,
        contentType: false,
        beforeSend: function () {
            bSend();
            if ($(".popup.dialog").length) {
                $(".popup.dialog").find("button.btn-can").trigger("click");
            }
        },
        success: function (resp) {
            if (Number(resp.ok)) {
                err = 0;
            }

            ret = resp;
            msg = resp.msg;
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
        },
    }).always(function () {
        busy();

        if (msg && msg != "") {
            showMsg(err ? "error" : "success", msg);
        }

        if (!err) {
            callback(ret);
        }

        if (typeof always != "undefined" && always) {
            always(err);
        }
    });
}
