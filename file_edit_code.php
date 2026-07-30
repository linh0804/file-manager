<?php

defined('ACCESS') or exit;

$dir = dirname($curr_path);
$name = basename($curr_path);
$site_title = 'Sửa - ' . $name;


$content = (string) file_get_contents($curr_path);
$api_edit = action_link('api_file_edit_text', ['path' => base64_encode($curr_path)]);
$file_ext = file_get_ext($name);

$code_lang = 'text';
$code_langs = [];
$code_lang_files = glob(__DIR__ . '/js/ace-editor/mode-*.js');
foreach($code_lang_files as $f) {
    $code_langs[] = substr(basename($f), 5, -3);
}

if (in_array($file_ext, $code_langs)) {
    $code_lang = $file_ext;
} else {
    foreach([
        'js' => 'javascript'
    ] as $k => $v) {
        if ($file_ext === $k) {
            $code_lang = $v;
        }
    }
}

require SITE_HEADER;
?>

<div class="title"><?= $site_title ?></div>


<style>
    #editor {
        height: 100%;
        font-size: 12px;
        line-height: 1.25;
    }
</style>

<div class="list">
    <span><?= file_print_path($dir, true) ?></span><hr/>

    <div class="ellipsis break-word">
        <span class="bull">&bull;</span>
        Tập tin:
        <strong class="file_name_edit"><?= $name ?></strong><hr />
        <div class="code_action">
            <select id="code_lang">
                <?php foreach ($code_langs as $code_type_key => $code_type_value): ?>
                    <option value="<?= $code_type_key ?>" <?= $code_lang === $code_type_key ? 'selected="selected"' : '' ?>>Mode: <?= $code_type_value ?></option>
                <?php endforeach; ?>
            </select>

            <a href="<?= action_link('file', ['act' => 'edit_text', 'path' => $curr_path]) ?>">
                <button class="button">Text Mode</button>
            </a>
        </div>
        <hr/>
    </div>

    <form id="code_form" action="javascript:void(0)"
          data-action="<?= $api_edit ?>"
          data-format="<?= $file_ext ?>"
    >
        <div id="editor" style="display: none"><?=  htmlspecialchars($content) ?></div>

        <div class="input_action">
            <input type="submit" value="Lưu lại" />
            <span style="margin-right: 12px"></span>
            <input type="checkbox" id="code_check_php" /> Kiểm tra lỗi PHP

            <span style="float: right">
                <?php if (file_can_format_code($name)): ?>
                    <button type="button" class="button" id="code_format">Format</button>
                <?php endif; ?>
                <label><input type="checkbox" id="code_wrap" /> Wrap</label>
            </span>
        </div>
    </form>
</div>

<div class="tips" style="margin-top: 0 !important">
<img src="icon/tips.png" alt="">
Nếu không thấy nội dung file, vui lòng không chỉnh sửa trên web!
</div>

<div id="code_check_message" class="list"></div>

<script>window.EditContext = false</script>
<script src="<?= asset('js/ace-editor/ace.js') ?>"></script>
<script>
    (function () {
        var form = document.getElementById("code_form");
        var action = form.dataset.action;
        var format = form.dataset.format;
        var codeCheckMessageElement = document.getElementById("code_check_message");
        var codeCheckPhpElement = document.getElementById("code_check_php");
        var editorElement = document.getElementById("editor");
        var codeLangElement = document.getElementById("code_lang");
        var codeWrapElement = document.getElementById("code_wrap");
        var codeFormatElement = document.getElementById("code_format");

        var editor = ace.edit(editorElement);
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/<?= $code_lang ?>");
        editor.session.setTabSize(4);
        editor.session.setUseSoftTabs(true);
        editor.setShowPrintMargin(false);
        
        editorElement.style.display = 'block';

        document.addEventListener("DOMContentLoaded", function () {
            editorElement.scrollIntoView({ behavior: "smooth" });
        });

        function saveCode() {
            var data = new FormData();
            data.append("request_api", 1);
            data.append("content", editor.getValue());

            codeCheckMessageElement.innerHTML = "";
            data.append("check", codeCheckPhpElement && codeCheckPhpElement.checked ? 1 : 0);

            fetch(action, {
                method: "POST",
                body: data,
                cache: "no-cache"
            }).then(function (response) {
                if (response.status !== 200) {
                    alert("Lỗi kết nối!");
                    return false;
                }
                return response.json();
            }).then(function (data) {
                alert(data.message);
                if (data.error) {
                    codeCheckMessageElement.innerHTML = data.error;
                }
            });
        }

        form.addEventListener("submit", function (event) {
            event.preventDefault();
            saveCode();
        });

        document.addEventListener("keydown", function (event) {
            if (event.ctrlKey && event.key === "s") {
                event.preventDefault();
                saveCode();
            }
        });

        codeLangElement.addEventListener("change", function () {
            editor.session.setMode("ace/mode/" + codeLangElement.value);
        });

        codeWrapElement.addEventListener("change", function () {
            editor.session.setUseWrapMode(codeWrapElement.checked);
        });

        if (codeFormatElement) {
            codeFormatElement.addEventListener("click", function () {
                if (!window.confirm("Chức năng có thể thay đổi cấu trúc code, xác nhận dùng!")) {
                    return;
                }

                var data = new FormData();
                data.append("request_api", 1);
                data.append("format", format);
                data.append("content", editor.getValue());

                fetch(action, {
                    method: "POST",
                    body: data,
                    cache: "no-cache"
                }).then(function (response) {
                    if (response.status !== 200) {
                        alert("Lỗi kết nối!");
                        return false;
                    }
                    return response.json();
                }).then(function (data) {
                    if (!data.error) {
                        editor.setValue(data.format, -1);
                    } else {
                        alert(data.error);
                    }
                });
            });
        }
    })();
</script>

<script>edit_recent.add('<?= htmlspecialchars($curr_path, ENT_QUOTES) ?>');</script>

<?php file_display_actions($curr_path); ?>
<?php require SITE_FOOTER; ?>