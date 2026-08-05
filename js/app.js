// CORE

function reload() {
    window.location.reload();
}

function redirect(url) {
    window.location.href = url;
}

function my_ajax(method, url, data, callback) {
    return $.ajax({
        url: url,
        method: method,
        data: data,
        success: callback
    });
}

// FORM AJAX

$(document).on("submit", "form[data-ajax][data-ajax-url]", function (event) {
    event.preventDefault();
    const e = $(this);

    my_ajax(
        "post",
        e.data("ajax-url"),
        e.serialize(),
        function (res) {
            $.notify(res.msg, res.status ? "success" : "error");
        }
    );
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

    my_ajax("get", e.data("modal-url"), {}, function (res) {
        app_modal(res);
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

    my_ajax("post", "api_calc.php", {
        path: e.data("path")
    }, function (res) {
        e.html(res.data.total_size_readable);
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
