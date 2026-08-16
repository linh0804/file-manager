function redirect(url) {
    window.location.href = url;
}

function reload() {
    window.location.reload();
}

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
        url: "api_calc.php",
        method: "post",
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
            alert("Đã copy!");
        })
        .catch(function (err) {
            alert("Lỗi: ", err);
        });
});
