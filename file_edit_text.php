<?php

defined('ACCESS') or exit;

$dir = dirname($curr_path);
$name = basename($curr_path);
$site_title = 'Sửa tập tin';

require SITE_HEADER;

?>
<style>
    #code_check_message, #code_check_highlight {
        display:none;
    }
</style>

<div class="title"><?= $site_title ?></div>

<?php if (!file_is_text($name) && !file_is_unknown($name)): ?>
    <div class="list"><span>Tập tin này không phải dạng văn bản</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="<?= action_link('index', ['path' => $dir] + get_page_list_params()) ?>">Danh sách</a></li>
    </ul>
<?php else: ?>
    <?php
    $total = 0;
    $dir = process_directory($dir);
    $content = file_get_contents($curr_path);
    $is_execute = function_can_use('exec');
    $action_edit = action_link('api_file_edit_text', ['path' => base64_encode($curr_path)]);
    ?>
    <div class="list">
        <span class="bull">&bull; </span><span><?= file_print_path($dir, true) ?></span><hr/>
        <div class="ellipsis break-word">
            <span class="bull">&bull; </span>Tập tin: <strong class="file_name_edit"><?= $name ?></strong><hr/>
        </div>
        <div>
            <a href="<?= action_link('file', ['act' => 'code_edit', 'path' => $curr_path]) ?>">
                <button class="button">[Code]</button>
            </a><hr />
        </div>
        <form action="javascript:void(0)" id="code_form" method="post">
            <div class="parent_box_edit">
                <textarea id="editor" wrap="off" style="white-space: pre;" class="box_edit" name="content"><?= PHP_EOL . htmlspecialchars($content) ?></textarea>
            </div>
            
            <div class="input_action">                    
                <input type="submit" name="s_save" value="Lưu lại"/>
                <span style="margin-right: 12px"></span>
                <?php if ($is_execute && file_get_ext($name) == 'php'): ?>
                    <label><input type="checkbox" id="code_check_php"/> Kiểm tra lỗi</label>
                <?php endif; ?>
                <div style="display: inline-block; float: right">
                    <?php if (file_can_format_code($name)): ?>
                        <input type="button" id="code_highlight" value="Format"> 
                    <?php endif; ?>
                    <label><input type="checkbox" id="code_wrap" /> Wrap</label>
                </div>
            </div>
        </form>
    </div>
    
    <div id="code_check_message" class="list"></div>
    
    <script>
        const codeCheckMessageElement = document.getElementById("code_check_message");
        const codeCheckPHPElement = document.getElementById("code_check_php");

        var editorElement = document.getElementById("editor");
        var codeFormElement = document.getElementById("code_form");

        // auto focus
        document.addEventListener("DOMContentLoaded", function() {
          editorElement.scrollIntoView({ behavior: "smooth" })
         })

        function save() {
            var data = new FormData();
            data.append("request_api", 1);
            data.append("content", editorElement.value);
            codeCheckMessageElement.style.display = "none";
            codeCheckMessageElement.innerHTML = "";
            if (codeCheckPHPElement && codeCheckPHPElement.checked) {
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
                    codeCheckMessageElement.innerHTML = data.error;
                    codeCheckMessageElement.style.display = "block";
                }
            })
        }

        codeFormElement.addEventListener("submit", function (event) {
            event.preventDefault()    
            save()
        })


        $("#code_highlight").on("click", function () {
            if(!window.confirm("Chức năng có thể thay đổi cấu trúc code, xác nhận dùng!")) {
                return;
            }

            var data = new FormData();
            data.append("request_api", 1);
            data.append("format", "<?= file_get_ext($name) ?>");
            data.append("content", editorElement.value);

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
                    editorElement.value = data.format;
                } else {
                    alert(data.error);
                }
            });                  
        });


        $('#code_wrap').on("change", function () {
            if (this.checked) {
                editorElement.removeAttribute("wrap");
                editorElement.removeAttribute("style");
            } else {
                editorElement.setAttribute("wrap", "off");
                editorElement.setAttribute("style", "white-space: nowrap");
            }
        });
        
        document.addEventListener("keydown", function(event) {
            if (event.ctrlKey && event.key === "s") {
                event.preventDefault()
                save()
            }
        })
    </script>

    <script>edit_recent.add('<?= htmlspecialchars($curr_path, ENT_QUOTES) ?>');</script>

<?php
    file_display_actions($curr_path);
endif;

require SITE_FOOTER;
