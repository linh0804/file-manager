// scroll
var scroll_to_top_timeout = null;
var last_scroll_top = window.scrollY || document.documentElement.scrollTop;

var top_buttons = document.querySelector("#scroll");
top_buttons.style.transform = "rotate(180deg)";

top_buttons.addEventListener("click", function () {
    var scroll = 0;

    if (top_buttons.style.transform == "rotate(180deg)") {
        scroll = document.documentElement.scrollHeight;
    }

    window.scroll({
        top: scroll,
        left: 0,
        behavior: "smooth",
    });
});

window.addEventListener("scroll", function () {
    const scroll_top_position = window.scrollY || document.documentElement.scrollTop;

    if (top_buttons.style.display == "none") {
        top_buttons.style.display = "block";
    }

    if (scroll_top_position > last_scroll_top) {
        top_buttons.style.transform = "rotate(180deg)";
    } else if (scroll_top_position < last_scroll_top) {
        top_buttons.style.transform = "rotate(0deg)";
    }

    last_scroll_top = scroll_top_position <= 0 ? 0 : scroll_top_position;

    clearTimeout(scroll_to_top_timeout);
    scroll_to_top_timeout = setTimeout(() => {
        top_buttons.style.display = "none";
    }, 3000);
});

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
        return await fetch(...args);
    } finally {
        NProgress.done();
    }
}

function file_ajax(data, success) {
    const requestData = { ...data };
    const act = String(requestData.act || "");

    delete requestData.act;

    if (!/^[a-z0-9_]+$/.test(act)) {
        alert("Lỗi action!");
        return;
    }

    NProgress.start();

    $.ajax({
        url: `api_${act}.php`,
        method: "post",
        data: requestData,
        success: success,
        error: function () {
            alert("Lỗi server!");
        },
    }).always(function () {
        NProgress.done();
    });
}

$(".btn-calc-size").on("click", function () {
    let e = $(this);
    file_ajax(e.data(), function (res) {
        e.html(res.data.total_size_readable);
    });
});
