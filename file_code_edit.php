<?php

defined('ACCESS') or exit;

$file = new SplFileInfo($curr_path);
$dir = dirname($curr_path);
$name = basename($curr_path);
$site_title = 'Sửa code - ' . $name;

$is_text = file_is_text($name) || is_format_unknown($name);

$content = null;
$action_edit = null;
$file_ext = null;
$code_lang = 'text';
$code_type = [
    'text' => 'Text',
    'php' => 'PHP',
    'javascript' => 'JavaScript',
    'html' => 'HTML',
    'css' => 'CSS',
    'sass' => 'SASS',
    'sql' => 'SQL',
    'json' => 'JSON'
];
$codemirror_format_map = [
    'mjs' => 'javascript',
    'js' => 'javascript',
    'scss' => 'sass'
];

if ($is_text) {
    $content = file_get_contents($curr_path);
    $action_edit = action_link('api_file_edit_text', ['path' => base64_encode($curr_path)]);
    $file_ext = get_file_ext($name);

    $file_ext_for_codemirror = array_key_exists($file_ext, $codemirror_format_map)
        ? $codemirror_format_map[$file_ext]
        : $file_ext;

    if (array_key_exists($file_ext_for_codemirror, $code_type)) {
        $code_lang = $file_ext_for_codemirror;
    }

}

require SITE_HEADER;
?>

<div class="tips" style="margin-top: 0 !important">
    <img src="icon/tips.png" alt="">
    Nếu không thấy nội dung file, vui lòng không chỉnh sửa trên web!
</div>

<div class="title"><?= $site_title ?></div>

<?php if (!$is_text): ?>
    <div class="list"><span>Tập tin này không phải dạng văn bản</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="<?= action_link('index', ['path' => $dir] + get_page_list_params()) ?>">Danh sách</a></li>
    </ul>
<?php else: ?>
    <style>
        .cm-editor {
            height: 100%;
            font-size: 13px;
        }
    </style>

    <div class="list">
        <span><?= print_path($dir, true) ?></span><hr/>
        <div class="ellipsis break-word">
            <span class="bull">&bull;</span>
            Tập tin:
            <strong class="file_name_edit"><?= $name ?></strong><hr />
            <div class="code_action">
                <select id="code_lang">
                    <?php foreach ($code_type as $code_type_key => $code_type_value): ?>
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
              data-action="<?= $action_edit ?>"
              data-format="<?= $file_ext ?>">
            <div>
                <textarea id="content" style="display: none"><?= PHP_EOL . htmlspecialchars($content) ?></textarea>
                <div id="editor" contenteditable="contenteditable"></div>
            </div>
            <div class="input_action">
                <input type="submit" value="Lưu lại" />
                <span style="margin-right: 12px"></span>
                <input type="checkbox" id="code_check_php" /> Kiểm tra lỗi PHP

                <span style="float: right">
                    <?php if (can_format_code($file_ext)): ?>
                        <button class="button" id="code_format">Format</button>
                    <?php endif; ?>
                    <label><input type="checkbox" id="code_wrap" /> Wrap</label>
                </span>
            </div>
        </form>
    </div>
    <div id="code_check_message" class="list"></div>

    <script>window.EditContext = false</script>
    <script src="<?= asset('js/edit_code.bundle.js') ?>"></script>
    <script>
        (function () {
            var form = document.getElementById("code_form");
            var action = form.dataset.action;
            var format = form.dataset.format;
            var codeCheckMessageElement = document.getElementById("code_check_message");
            var codeCheckPhpElement = document.getElementById("code_check_php");
            var editorElement = document.getElementById("editor");

            document.addEventListener("DOMContentLoaded", function () {
                editorElement.scrollIntoView({ behavior: "smooth" });
            });

            function saveCode() {
                var data = new FormData();
                data.append("request_api", 1);
                data.append("content", editor.state.doc.toString());

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

            $("#code_format").click(function () {
                if (!window.confirm("Chức năng có thể thay đổi cấu trúc code, xác nhận dùng!")) {
                    return;
                }

                var data = new FormData();
                data.append("request_api", 1);
                data.append("format", format);
                data.append("content", editor.state.doc.toString());

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
                        editor.dispatch({
                            changes: {
                                from: 0,
                                to: editor.state.doc.length,
                                insert: data.format
                            }
                        });
                    } else {
                        alert(data.error);
                    }
                });
            });
        })();
    </script>
    <script>edit_recent.add('<?= htmlspecialchars($curr_path, ENT_QUOTES) ?>');</script>
<?php
endif;

file_display_actions($file);
require SITE_FOOTER;
