<?php

defined('ACCESS') or exit;

$dir = dirname($curr_path);
$name = basename($curr_path);
$site_title = 'Sửa: ' . $name;

require SITE_HEADER;

?>
<style>
    #code_check_message {
        display:none;
    }
</style>

<div class="title"><?= file_print_path($dir, true) ?></div>

<?php
$total = 0;
$dir = process_directory($dir);
$content = file_get_contents($curr_path);
$is_execute = function_can_use('exec');
$api_save = action_link('api_text_save', [
    'path' => base64_encode($curr_path)
]);
$api_format = action_link('api_text_format', [
    'path' => base64_encode($curr_path)
]);
$api_syntax = action_link('api_text_syntax', [
    'path' => base64_encode($curr_path)
]);
$file_ext = file_get_ext($name);
$can_format = file_can_format_code($name);
$can_syntax = $is_execute && $file_ext === 'php';
?>

<div class="list">
    <span class="bull">&bull; </span>Tập tin: <strong class="file_name_edit"><?= $name ?></strong>
    <hr>

    <div id="editor-panel">
        <button type="button" class="button" id="editor-save">
            Lưu lại
        </button>

        <a href="<?= action_link('file', [
            'act' => 'edit_code',
            'path' => $curr_path
        ]) ?>">
            <button type="button" class="button">[Code]</button>
        </a>

        <button type="button" class="button" id="editor-wrap">
            Wrap
        </button>

        <button
            type="button"
            class="button"
            id="editor-syntax"
            <?= $can_syntax ? '' : 'disabled' ?>
        >
            Syntax
        </button>

        <button
            type="button"
            class="button"
            id="editor-format"
            <?= $can_format ? '' : 'disabled' ?>
        >
            Format
        </button>
    </div>

    <form action="javascript:void(0)" id="code_form" method="post">            
        <div class="input_action">                    
            <input type="submit" name="s_save" value="Lưu lại">

            
            <span style="margin-right: 12px"></span>
            <div style="display: inline-block; float: right">
                <label><input type="checkbox" id="code_wrap" /> Wrap</label>
            </div>
        </div>
        
        <div class="parent_box_edit">
            <textarea id="editor" wrap="off" style="white-space: pre;" class="box_edit" name="content"><?= PHP_EOL . htmlspecialchars($content) ?></textarea>
        </div>
    </form>
</div>

<div id="code_check_message" class="list"></div>

<script>
    const apiSave = <?= json_encode($api_save) ?>;
    const apiFormat = <?= json_encode($api_format) ?>;
    const apiSyntax = <?= json_encode($api_syntax) ?>;

    const editorElement = document.getElementById("editor");
    const codeFormElement = document.getElementById("code_form");
    const saveButton = document.getElementById("editor-save");
    const syntaxButton = document.getElementById("editor-syntax");
    const formatButton = document.getElementById("editor-format");
    const wrapElement = document.getElementById("code_wrap");
    const messageElement = document.getElementById("code_check_message");

    function postJson(url, data) {
        return fetch(url, {
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

    function save() {
        const data = new FormData();

        data.append("content", editorElement.value);

        messageElement.textContent = "";
        messageElement.style.display = "none";

        postJson(apiSave, data)
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
        const data = new FormData();

        data.append("content", editorElement.value);

        postJson(apiSyntax, data)
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

        const data = new FormData();

        data.append("format", <?= json_encode($file_ext) ?>);
        data.append("content", editorElement.value);

        postJson(apiFormat, data)
            .then(function (data) {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                editorElement.value = data.format;
            })
            .catch(function (error) {
                alert(error.message);
            });
    }

    saveButton.addEventListener("click", save);
    syntaxButton.addEventListener("click", checkSyntax);
    formatButton.addEventListener("click", formatCode);

    wrapElement.addEventListener("change", function () {
        if (this.checked) {
            editorElement.removeAttribute("wrap");
            editorElement.removeAttribute("style");
        } else {
            editorElement.setAttribute("wrap", "off");
            editorElement.setAttribute("style", "white-space: nowrap");
        }
    });

    codeFormElement.addEventListener("submit", function (event) {
        event.preventDefault();
        save();
    });

    document.addEventListener("keydown", function(event) {
        if (event.ctrlKey && event.key === "s") {
            event.preventDefault();
            save();
        }
    });
</script>

<script>edit_recent.add('<?= htmlspecialchars($curr_path, ENT_QUOTES) ?>');</script>

<?php file_display_actions($curr_path); ?>
<?php require SITE_FOOTER; ?>
