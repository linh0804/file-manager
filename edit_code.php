<?php

namespace app;

use SplFileInfo;

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
    $actionEdit = 'edit_api.php?path=' . base64_encode($path);
    $fileExt = get_format($name);

    $codeLang = 'text';
    $codeType = [
        'text' => 'Text',
        'php' => 'PHP',
        'javascript' => 'JavaScript',
        'html' => 'HTML',
        'css' => 'CSS',
        'sass' => 'SASS',
        'sql' => 'SQL',
        'json' => 'JSON'
    ];

    $forCM = [
        'mjs' => 'javascript',
        'js' => 'javascript',
        'scss' => 'sass'
    ];

    $fileExtForCM = array_key_exists($fileExt, $forCM) ? $forCM[$fileExt] : $fileExt;
    if (array_key_exists($fileExtForCM, $codeType)) {
        $codeLang = $fileExtForCM;
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
            <div>
                <a href="edit_text.php?path=<?= base64_encode($file->getPathname()) ?>">
                    <button class="button">Chế độ sửa văn bản</button>
                </a>
                <button onclick="full_screen()" class="button">F11</button>
            <hr />
            </div>
            <div class="code_action">
                Loại code:
                <select id="code_lang">
                    <?php foreach ($codeType as $cType => $cValue): ?>
                        <option value="<?= $cType ?>" <?= $codeLang === $cType ? 'selected="selected"' : '' ?>><?= $cValue ?></option>
                    <?php endforeach; ?>
                </select>
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
                    <?php if (able_format_code($fileExt)): ?>
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
        const codeCheckMessageElement = document.getElementById("code_check_message")
        const codeCheckPHPElement = document.getElementById("code_check_php")
        const codeFormElement = document.getElementById("code_form")
        const editorElement = document.getElementById("editor")

        // auto focus
        document.addEventListener("DOMContentLoaded", function() {
            editorElement.scrollIntoView({ behavior: "smooth" })
        })

        function save() {
            var data = new FormData();
            data.append("requestApi", 1);
            data.append("content", editor.state.doc.toString());

            codeCheckMessageElement.innerHTML = "";
            if (codeCheckPHPElement.checked) {
                data.append("check", 1);
            } else {
                data.append("check", 0);
            }

            fetch("<?= $actionEdit ?>", {
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
                    codeCheckMessageElement.innerHTML = data.error;
                }
            });
        }

        codeFormElement.addEventListener("submit", function (event) {
            event.preventDefault();
            save()
        })

        document.addEventListener("keydown", function(event) {
            if (event.ctrlKey && event.key === "s") {
                event.preventDefault()
                save()
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

            fetch("<?= $actionEdit ?>", {
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

        // full screen
        function full_screen() {
            if (!document.fullscreenElement) {
                editorElement.requestFullscreen().catch(err => {
                    alert(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`)
                })
            } else {
                document.exitFullscreen()
            }
        }
        document.addEventListener("keydown", function(event) {
            if (event.key === "F11") {
                event.preventDefault()
                full_screen();
            }

            if (event.key === "Escape") {
                if (document.fullscreenElement) {
                    document.exitFullscreen()
                }
            }
        });
    </script>
<?php
endif;

print_actions($file);

require '_footer.php';
