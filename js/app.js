let app_modal_reload_after_close = false;

// CORE

function reload() {
    window.location.reload();
}

function redirect(url) {
    window.location.href = url;
}

// FORM AJAX

$(document).on("submit", "form[data-ajax]", function (event) {
    event.preventDefault();
    const e = $(this);
    const data = new FormData(this, event.originalEvent.submitter);

    $.ajax({
        method: "post",
        url: e.attr("action"),
        data: data,
        processData: false,
        contentType: false,
        success: function (res) {
            try {
                $.notify(res.msg, res.status ? "success" : "error");
                
                if (res.reload_after_close) {
                    app_modal_reload_after_close = true;
                }
                
                if (res.form_reset) {
                    e.trigger("reset");
                }
                
                if (res.redirect) {
                    redirect(res.redirect);
                } else if (res.reload) {
                    reload();
                }
            } catch (error) {
                $.notify("Lỗi máy chủ (JSON)", "error");
            }
        }
    });
});

// MODAL

function app_modal(data) {
    $("#app-modal-overlay").show();
    $("#app-modal-body").empty().html(data);
    $("#app-modal").show();
}

$(document).on("click", "#app-modal-close", function (event) {
    event.preventDefault();

    $("#app-modal").hide();
    $("#app-modal-body").empty();
    $("#app-modal-overlay").hide();
    
    if (app_modal_reload_after_close) {
        reload();
    }
});

$(document).on("click", "a[data-modal]", function (event) {
    event.preventDefault();
    const e = $(this);

    $.ajax({
        method: "get",
        url: e.attr("href"),
        data: {},
        success: function (res) {
            app_modal(res);
        }
    });
});

$(document).on("submit", "form[data-modal]", function (event) {
    event.preventDefault();

    const form = this;
    const submitter = event.originalEvent.submitter;
    const data = new FormData(form, submitter);

    $.ajax({
        method: submitter?.formMethod || form.method || "post",
        url: submitter?.formAction || form.action,
        data: data,
        processData: false,
        contentType: false,
        success: function (res) {
            app_modal(res);
        }
    });
});

// SIDEBAR MENU

function toggle_menu() {
    document.body.classList.toggle("has-menu");
}

document.addEventListener("click", function (e) {
    var target_id = e.target.id;
    if (target_id === "nav-menu" || target_id === "menu-overlay" || (document.body.classList.contains("has-menu") && e.target.closest(".menu-toggle a:not(.no-pusher)"))) {
        document.body.classList.toggle("has-menu");
    }
});

// INDEX

$(".list-file .btn-calc-size").on("click", function () {
    let e = $(this);

    $.ajax({
        method: "post",
        url: "api_calc.php",
        data: {
            path: e.data("path")
        },
        success: function (res) {
            e.html(res.data.total_size_readable);
        }
    });
});

$(".copy-button").click(function (e) {
    e.preventDefault();

    let data = $(this).data("copy");

    navigator.clipboard
        .writeText(data)
        .then(function () {
            $.notify("Đã copy!", "success");
        })
        .catch(function (err) {
            $.notify("Lỗi: " + err);
        });
});
