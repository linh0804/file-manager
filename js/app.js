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

$(document).on("click", ".app-modal-close-btn", function (event) {
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

// FILE UPLOAD

$(function () {
    const files = [];
    let uploading = false;

    $(document).on("click", "#file-upload #button-choose", function (e) {
        e.preventDefault();
        const $fileUpload = $(this).closest("#file-upload");
        $fileUpload.find("#files").val("");
        $fileUpload.find("#files").click();
    });

    $(document).on("click", "#file-upload #button-reset", function (e) {
        e.preventDefault();
        const $fileUpload = $(this).closest("#file-upload");

        if (uploading) {
            alert("Đang upload!");
            return;
        }

        files.length = 0;
        $fileUpload.find("#file-list").empty();
    });

    $(document).on("change", "#file-upload #files", function () {
        const $fileUpload = $(this).closest("#file-upload");
        const $fileList = $fileUpload.find("#file-list");

        $fileList.empty();

        files.push(...Array.from(this.files));
        for (let i = 0; i < files.length; i++) {
            $fileList.append(`
                <div class="file-upload list" data-id="${i}" style="font-size: small">
                    <span class="bull"></span> ${files[i].name}<br>
                    <span style="color: #FF00FF">(${(files[i].size / (1024 * 1024)).toFixed(2)} MB)</span>
                    <span class="result"></span>
                </div>
            `);
        }
    });

    $(document).on("click", "#file-upload #button-upload", async function (e) {
        e.preventDefault();
        const $fileUpload = $(this).closest("#file-upload");

        if (!files.length) {
            alert("Chưa chọn file!");
            return;
        }

        if (uploading) {
            alert("Đang upload!");
            return;
        }

        const uploadItems = [];

        $fileUpload.find(".file-upload").each(function () {
            const $item = $(this);
            const id = $item.data("id");

            if (files[id]) {
                uploadItems.push({
                    file: files[id],
                    result: $item.find(".result")
                });
            }
        });

        uploading = true;
        NProgress.start();

        try {
            const action = $fileUpload.attr("action");
            for (const item of uploadItems) {
                await upload(item.file, item.result, action);
            }
        } finally {
            uploading = false;
            NProgress.done();
        }
    });

    function upload(file, result, action) {
        return new Promise(function (resolve) {
            console.log(file.name);

            const formData = new FormData();
            formData.append("file", file);

            const xhr = new XMLHttpRequest();
            xhr.open("POST", action);

            let lastLoaded = 0;
            let lastTime = performance.now();

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    const now = performance.now();
                    const elapsed = (now - lastTime) / 1000;
                    const uploaded = e.loaded - lastLoaded;
                    const speed = elapsed > 0 ? uploaded / elapsed : 0;
                    const percent = Math.min(100, Math.round((e.loaded / e.total) * 100));
                    const speedKB = (speed / 1024).toFixed(0);

                    result.html("<span style=\"color: orange\">[" + percent + "%] (" + speedKB + " kb/s)</span>");

                    lastLoaded = e.loaded;
                    lastTime = now;
                }
            };

            xhr.onload = function () {
                try {
                    const res = JSON.parse(xhr.responseText);

                    if (xhr.status < 200 || xhr.status >= 300 || !res.status) {
                        const msg = res.msg || "Thất bại!";

                        result.html("<span style=\"color:red\">" + msg + "</span>");
                        alert("Tải lên thất bại: " + file.name);
                    } else {
                        if (res.reload_after_close) {
                            app_modal_reload_after_close = true;
                        }

                        result.html("<span style=\"color:green\">" + (res.msg || "OK!") + "</span>");
                    }
                } catch (e) {
                    result.html("<span style=\"color:red\">Thất bại!</span>");
                    alert("Tải lên thất bại: " + file.name);
                    console.log(e);
                }
            };

            xhr.onerror = function () {
                result.html("<span style=\"color:red\">Lỗi kết nối!</span>");
                alert("Tải lên thất bại: " + file.name);
            };

            xhr.onloadend = function () {
                resolve();
            };

            try {
                xhr.send(formData);
            } catch (e) {
                result.html("<span style=\"color:red\">Thất bại!</span>");
                alert("Tải lên thất bại: " + file.name);
                console.log(e);
                resolve();
            }
        });
    }
});
