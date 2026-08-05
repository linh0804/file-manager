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
$editor_path = base64_encode($curr_path);
$file_ext = file_get_ext($name);
$can_format = file_can_format_code($name);
$can_syntax = $is_execute && $file_ext === 'php';
?>

<div class="list">
    <span class="bull">&bull; </span>Tập tin: <strong class="file_name_edit"><?= $name ?></strong>
    <hr>

    <div id="editor-panel" style="display:block; width:100%; overflow-x:auto; white-space:nowrap; padding-bottom: 0px">
        <button type="button" class="button" id="editor-save">
            Lưu lại
        </button>

        <a href="<?= act_link('file', [
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
        <div class="parent_box_edit">
            <textarea id="editor" wrap="off" style="white-space: nowrap;" class="box_edit" name="content"><?= PHP_EOL . htmlspecialchars($content) ?></textarea>
        </div>
    </form>
</div>

<div id="code_check_message" class="list"></div>

<script>
    const editorPath = <?= json_encode($editor_path) ?>;

    const editorElement = document.getElementById("editor");
    const saveButton = document.getElementById("editor-save");
    const wrapButton = document.getElementById("editor-wrap");
    const syntaxButton = document.getElementById("editor-syntax");
    const formatButton = document.getElementById("editor-format");
    const messageElement = document.getElementById("code_check_message");
    let wrapEnabled = false;

    function showMessage(message) {
        messageElement.textContent = message || "";
        messageElement.style.display = "block";
    }

    function save() {
        messageElement.textContent = "";
        messageElement.style.display = "none";

        my_ajax("post", {
            act: "text_save",
            path: editorPath,
            content: editorElement.value
        }, function (data) {
            $.notify(data.message, "success");

            if (data.error) {
                showMessage(data.error);
            }
        });
    }

    function checkSyntax() {
        my_ajax("post", {
            act: "text_syntax",
            path: editorPath,
            content: editorElement.value
        }, function (data) {
            $.notify(data.message, "success");
            showMessage(data.error || data.message);
        });
    }

    function formatCode() {
        if (!window.confirm(
            "Chức năng có thể thay đổi cấu trúc code, xác nhận dùng!"
        )) {
            return;
        }

        my_ajax("post", {
            act: "text_format",
            path: editorPath,
            format: <?= json_encode($file_ext) ?>,
            content: editorElement.value
        }, function (data) {
            if (data.error) {
                alert(data.error);
                return;
            }

            editorElement.value = data.format;
        });
    }

    saveButton.addEventListener("click", save);
    syntaxButton.addEventListener("click", checkSyntax);
    formatButton.addEventListener("click", formatCode);

    wrapButton.addEventListener("click", function () {
        wrapEnabled = !wrapEnabled;

        if (wrapEnabled) {
            editorElement.removeAttribute("wrap");
            editorElement.removeAttribute("style");
            wrapButton.style.borderColor = "green";
        } else {
            editorElement.setAttribute("wrap", "off");
            editorElement.setAttribute("style", "white-space: nowrap");
            wrapButton.style.borderColor = "";
        }
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
