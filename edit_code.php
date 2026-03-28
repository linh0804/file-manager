<?php

define('ACCESS', true);

require '_init.php';

$path = get_path();
check_path($path);

$dir = dirname($path);
$name = basename($path);
$title = 'Sửa code - ' . $name;

require '_header.php';

$file = new SplFileInfo($path);

?>

<div class="tips" style="margin-top: 0 !important">
    <img src="icon/tips.png" alt="">
    Nếu không thấy nội dung file, vui lòng không chỉnh sửa trên web!
</div>

<div class="title"><?= $title ?></div>

<?php if (!is_format_text($name) && !is_format_unknown($name)): ?>
    <div class="list"><span>Tập tin này không phải dạng văn bản</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php?path=<?= $dirEncode . $pages['paramater_1'] ?>">Danh sách</a></li>
    </ul>
<?php else:
    $content = file_get_contents($path);
    $action_edit = 'edit_api.php?path=' . base64_encode($path);
    $file_ext = get_format($name);

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

    $file_ext_for_codemirror = array_key_exists($file_ext, $codemirror_format_map) ? $codemirror_format_map[$file_ext] : $file_ext;
    if (array_key_exists($file_ext_for_codemirror, $code_type)) {
        $code_lang = $file_ext_for_codemirror;
    }
    ?>
    <style type="text/css" media="screen">
        .cm-editor {
            height: 100%;
        }
        
        .cm-editor {
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

                <a href="edit_text.php?path=<?= base64_encode($file->getPathname()) ?>">
                    <button class="button">[Text Mode]</button>
                </a>
            </div>
            <hr/>
        </div>

        <form id="code_form" action="javascript:void(0)">
            <div>
                <textarea id="content" style="display: none"><?= PHP_EOL . htmlspecialchars($content) ?></textarea>
                <div id="editor" contenteditable="contenteditable"></div>
            </div>
            <div class="input_action">
                <input type="submit" value="Lưu lại" />
                <span style="margin-right: 12px"></span>
                <input type="checkbox" id="code_check_php" /> Kiểm tra lỗi PHP

                <span style="float: right">
                    <?php if (able_format_code($file_ext)): ?>
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
        const code_check_message_element = document.getElementById("code_check_message")
        const code_check_php_element = document.getElementById("code_check_php")
        const code_form_element = document.getElementById("code_form")
        const editor_element = document.getElementById("editor")

        // auto focus
        document.addEventListener("DOMContentLoaded", function() {
            editor_element.scrollIntoView({ behavior: "smooth" })
        })

        function save_code() {
            var data = new FormData();
            data.append("requestApi", 1);
            data.append("content", editor.state.doc.toString());

            code_check_message_element.innerHTML = "";
            if (code_check_php_element.checked) {
                data.append("check", 1);
            } else {
                data.append("check", 0);
            }

            fetch("<?= $action_edit ?>", {
                method: "POST",
                body: data,
                cache: "no-cache"
            }).then(function (response) {
                if (response.status != 200) {
                    alert("Lỗi kết nối!");
                    return false;
                }

                return response.json();
            }).then((data) => {
                alert(data.message)

                if (data.error) {
                    code_check_message_element.innerHTML = data.error;
                }
            });
        }

        code_form_element.addEventListener("submit", function (event) {
            event.preventDefault();
            save_code()
        })

        document.addEventListener("keydown", function(event) {
            if (event.ctrlKey && event.key === "s") {
                event.preventDefault()
                save_code()
            }
        })

        // format code
        $("#code_format").click(function() {
            if (!window.confirm("Chức năng có thể thay đổi cấu trúc code, xác nhận dùng!")) {
                return;
            }

            var data = new FormData();
            data.append("requestApi", 1);
            data.append("format", "<?= $file->getExtension() ?>");
            data.append("content", editor.state.doc.toString());

            fetch("<?= $action_edit ?>", {
                method: "POST",
                body: data,
                cache: "no-cache"
            }).then(function (response) {
                if (response.status != 200) {
                    alert("Lỗi kết nối!");
                    return false;
                }
                return response.json();
            }).then((data) => {
                if (!data.error) {
                    editor.dispatch({
                        changes: {
                            from: 0,
                            to: editor.state.doc.length,
                            insert: data.format
                        }
                    })
                } else {
                    alert(data.error);
                }
            })
        })
    </script>
<?php
endif;

print_actions($file);

require '_footer.php';
