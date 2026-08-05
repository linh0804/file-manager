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
});

$(document).on("click", "[data-modal][data-modal-url]", function (event) {
    event.preventDefault();
    const e = $(this);

    $.ajax({
        method: "get",
        url: e.data("modal-url"),
        data: {},
        success: function (res) {
            app_modal(res);
        }
    });
});

// MENU

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
