function redirect(url) {
    window.location.href = url;
}

function my_ajax(method, data, succ) {
    return $.ajax({
        url: data.act_link || `api_${data.act}.php`,
        method: method,
        data: data,
        success: succ
    });
}

// MODAL

function app_modal(data) {
    $("#app-modal-overlay").show();
    $("#app-modal-body").empty().html(data);
    $("#app-modal").show();
}

$(document).on("click", "#app-modal-close", function(event) {
    event.preventDefault();

    $("#app-modal").hide();
    $("#app-modal-body").empty();
    $("#app-modal-overlay").hide();
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

    my_ajax("post", e.data(), function (res) {
        e.html(res.data.total_size_readable);
    });
});

$(".my-index-modal span").on("click", function() {
    event.preventDefault();
    let e = $(this);

    my_ajax("get", { act_link: e.data('modal-url') }, function (res) {
        app_modal(res);
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
