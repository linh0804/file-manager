// copy
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

// menu
function toggle_menu() {
    document.body.classList.toggle("has-menu");
}

document.addEventListener("click", function (e) {
    var target_id = e.target.id;
    if (target_id === "nav-menu" || target_id === "menu-overlay" || (document.body.classList.contains("has-menu") && e.target.closest(".menu-toggle a:not(.no-pusher)"))) {
        document.body.classList.toggle("has-menu");
    }
});

function redirect(url) {
    window.location.href = url;
}

async function fm_fetch(...args) {
    NProgress.start();

    try {
        const response = await fetch(...args);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return response;
    } catch (err) {
        alert("Lỗi server!");
        throw err;
    } finally {
        NProgress.done();
    }
}

function fm_ajax(data, success) {
    return $.ajax({
        url: `api_${data.act}.php`,
        method: "post",
        data: data,
        success: success
    });
}

$(".list-file .btn-calc-size").on("click", function () {
    let e = $(this);

    fm_ajax(e.data(), function (res) {
        e.html(res.data.total_size_readable);
    });
});
