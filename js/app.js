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

async function fm_api(method, url, data = null, callback = null) {
    NProgress.start();

    try {
        const options = {
            method: method.toUpperCase(),
        };

        if (options.method === "POST") {
            options.body = data;
        }

        const response = await fetch(url, options);

        if (!response.ok) {
            throw new Error(`HTTP STATUS ${response.status}`);
        }

        let json;
        try {
            json = await response.json();
        } catch {
            throw new Error("Invalid JSON");
        }

        if (typeof callback === "function") {
            callback(json);
        }

        return json;
    } catch (err) {
        alert("Lỗi server!");
        throw err;
    } finally {
        NProgress.done();
    }
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
