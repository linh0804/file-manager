<?php

defined('ACCESS') or exit;

$dir = dirname($curr_path);
$name = basename($curr_path);
$site_title = 'Sửa: ' . $name;

$content = (string) file_get_contents($curr_path);
$file_ext = file_get_ext($name);
$api_save = action_link('api_text_save', [
    'path' => base64_encode($curr_path)
]);
$api_format = action_link('api_text_format', [
    'path' => base64_encode($curr_path)
]);
$api_syntax = action_link('api_text_syntax', [
    'path' => base64_encode($curr_path)
]);
$is_execute = function_can_use('exec');
$can_format = file_can_format_code($name);
$can_syntax = $is_execute && $file_ext === 'php';

$code_lang = 'text';
$code_langs = [
    'js' => 'javascript',
    'php' => 'php',
    'txt' => 'text',
    'sql' => 'sql',
    'json' => 'json'
];
ksort($code_langs);

if (array_key_exists($file_ext, $code_langs)) {
    $code_lang = $file_ext;
}

require SITE_HEADER;
?>

<div class="title"><?= file_print_path($dir, true) ?></div>

<style>
    .cm-editor {
        height: 100%;
        font-size: 12px;
        line-height: 1.25;
    }
    
    .cm-panel {
        padding: 5px 10px;
        font-family: monospace;
      }
</style>

<div class="list">
    <div class="break-word">
        <span class="bull">&bull; </span>Tập tin: <strong class="file_name_edit"><?= $name ?></strong>
    </div><hr>

    <div class="code_action">
        <select id="code_lang">
            <?php foreach ($code_langs as $code_type_key => $code_type_value): ?>
                <option value="<?= $code_type_value ?>" <?= $code_lang === $code_type_key ? 'selected="selected"' : '' ?>>Mode: <?= $code_type_key ?></option>
            <?php endforeach; ?>
        </select>

        <a href="<?= action_link('file', ['act' => 'edit_text', 'path' => $curr_path]) ?>">
            <button class="button">[Text]</button>
        </a>
    </div>

    <form
        id="code_form"
        action="javascript:void(0)"
        data-save="<?= htmlspecialchars($api_save, ENT_QUOTES) ?>"
        data-format="<?= htmlspecialchars($api_format, ENT_QUOTES) ?>"
        data-syntax="<?= htmlspecialchars($api_syntax, ENT_QUOTES) ?>"
        data-file-ext="<?= htmlspecialchars($file_ext, ENT_QUOTES) ?>"
    >
        <textarea id="editor-content" style="display: none"><?= PHP_EOL . htmlspecialchars($content) ?></textarea>
        <div id="editor"></div>
            
        <div class="input_action">
            <input type="submit" value="Lưu lại" />
            <span style="margin-right: 12px"></span>

            <span style="float: right">
                <button
                    type="button"
                    class="button"
                    id="code_syntax"
                    <?= $can_syntax ? '' : 'disabled' ?>
                >
                    Syntax
                </button>

                <button
                    type="button"
                    class="button"
                    id="code_format"
                    <?= $can_format ? '' : 'disabled' ?>
                >
                    Format
                </button>

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
<script src="<?= asset('js/edit_code.bundle.js') ?>"></script>
<script>
    (function () {
        var form = document.getElementById("code_form");
        var saveAction = form.dataset.save;
        var formatAction = form.dataset.format;
        var syntaxAction = form.dataset.syntax;
        var fileExt = form.dataset.fileExt;

        var syntaxButton = document.getElementById("code_syntax");
        var formatButton = document.getElementById("code_format");
        var messageElement = document.getElementById("code_check_message");

        var codeLangElement = document.getElementById("code_lang");

        function postJson(url, data) {
            return fm_fetch(url, {
                method: "POST",
                body: data,
                cache: "no-cache"
            }).then(function (response) {
                if (response.status !== 200) {
                    throw new Error("Lỗi kết nối!");
                }

                return response.json();
            });
        }

        function showMessage(message) {
            messageElement.textContent = message || "";
            messageElement.style.display = "block";
        }

        function saveCode() {
            var data = new FormData();

            data.append("content", editor.state.doc.toString());

            messageElement.textContent = "";
            messageElement.style.display = "none";

            postJson(saveAction, data)
                .then(function (data) {
                    alert(data.message);

                    if (data.error) {
                        showMessage(data.error);
                    }
                })
                .catch(function (error) {
                    alert(error.message);
                });
        }

        function checkSyntax() {
            var data = new FormData();

            data.append("content", editor.state.doc.toString());

            postJson(syntaxAction, data)
                .then(function (data) {
                    showMessage(data.error || data.message);
                })
                .catch(function (error) {
                    alert(error.message);
                });
        }

        function formatCode() {
            if (!window.confirm(
                "Chức năng có thể thay đổi cấu trúc code, xác nhận dùng!"
            )) {
                return;
            }

            var data = new FormData();

            data.append("format", fileExt);
            data.append("content", editor.state.doc.toString());

            postJson(formatAction, data)
                .then(function (data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    editor.dispatch({
                        changes: {
                            from: 0,
                            to: editor.state.doc.length,
                            insert: data.format
                        }
                    });
                })
                .catch(function (error) {
                    alert(error.message);
                });
        }

        form.addEventListener("submit", function (event) {
            event.preventDefault();
            saveCode();
        });

        syntaxButton.addEventListener("click", checkSyntax);
        formatButton.addEventListener("click", formatCode);

        document.addEventListener("keydown", function (event) {
            if (event.ctrlKey && event.key === "s") {
                event.preventDefault();
                saveCode();
            }
        });

        document.addEventListener("DOMContentLoaded", function () {
            if (codeLangElement) {
                codeLangElement.scrollIntoView({
                    behavior: "smooth"
                });
            }
        });
    })();
</script>

<script>edit_recent.add('<?= htmlspecialchars($curr_path, ENT_QUOTES) ?>');</script>

<?php file_display_actions($curr_path); ?>
<?php require SITE_FOOTER; ?>
