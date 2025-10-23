<?php
namespace app;

use ngatngay\http\request;
use SplFileInfo;

define('ACCESS', true);

require '_init.php';

$path = base64_decode((string) request::get('path'));
$file = new SplFileInfo($path);
$dir = dirname($file->getPathname());
$name = basename($file->getPathname());
$title = 'Sửa tập tin';

check_path($path, 'file');

require '_header.php';

echo '<div class="title">' . $title . '</div>';

if (!isFormatText($name) && !isFormatUnknown($name)) {
    echo '<div class="list"><span>Tập tin này không phải dạng văn bản</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
    </ul>';
} else {
    $total = 0;
    $dir = processDirectory($dir);
    $content = file_get_contents($path);
    $isExecute = isFunctionExecEnable();
    $actionEdit = 'edit_api.php?path=' . base64_encode($path);
    edit_recent_add($path);

    echo '<div class="list">
        <span class="bull">&bull; </span><span>' . printPath($dir, true) . '</span><hr/>
        <div class="ellipsis break-word">
            <span class="bull">&bull; </span>Tập tin: <strong class="file_name_edit">' . $name . '</strong><hr/>
        </div>
        <div>
            <a href="edit_code.php?dir=' . $dir . '&name=' . $name . '">
                <button class="button">Chế độ sửa code</button>
            </a><hr />
        </div>
        <form action="javascript:void(0)" id="code_form" method="post">
            <span class="bull">&bull; </span>Nội dung:

            <div class="parent_box_edit">
                <textarea id="editor" wrap="off" style="white-space: pre;" class="box_edit" name="content">'. PHP_EOL . htmlspecialchars($content) . '</textarea>
            </div>
            
            <div class="input_action">                    
                <input type="submit" name="s_save" value="Lưu lại"/>
                <span style="margin-right: 12px"></span>'.
                ($isExecute && strtolower((string) getFormat($name)) == 'php' ? '<label><input type="checkbox" id="code_check_php"/> Kiểm tra lỗi</label>' : '') . '
                <div style="display: inline-block; float: right">'
                    . (ableFormatCode($file->getExtension()) ? '<input type="button" id="code_highlight" value="Format"> ' : '')
                    . '<label><input type="checkbox" id="code_wrap" /> Wrap</label>
                </div>
            </div>
        </form>';
    echo '</div>';
    
    echo '<div class="list">
        <div class="search_replace search">
                <span class="bull">&bull; </span>Tìm kiếm:<br/>
                <input type="text" id="searchInput" name="searchInput" value=""/>
            </div>
            <div class="search_replace replace">
                <span class="bull">&bull; </span>Thay thế:<br/>
                <input type="text" id="replaceInput" name="replaceInput" value=""/>
            </div>
            <div class="input_action">                    
                <button class="button" onclick="searchText()">Tìm kiếm</button>
                <button class="button" onclick="replaceText()">Thay thế</button>
            </div>
        </div>';
    
    echo '<div id="code_check_message" class="list"></div>';
 ?>
    
    <script>        
        var local_storage = nightmare.local_storage;

        const codeCheckMessageElement = document.getElementById("code_check_message");
        const codeCheckPHPElement = document.getElementById("code_check_php");

        var editorElement = document.getElementById("editor");
        var codeFormElement = document.getElementById("code_form");

        // search & replace
        var searchInput = document.getElementById("searchInput")
        var replaceInput = document.getElementById("replaceInput")
        
        function searchText() {
            let searchValue = searchInput.value
            let lines = editorElement.value.split("\n")
            let foundLines = [];
            
            if (!searchValue) {
                alert("Chưa nhập nội dung!")
                return
            }
            
            lines.forEach((line, index) => {
                if (line.includes(searchValue)) {
                    foundLines.push(index + 1); // +1 to match human-readable line numbers
                }
            });

            if (foundLines.length > 0) {
                alert(`Tìm thấy ${foundLines.length} dòng: ${foundLines.join(", ")}`);
            } else {
                alert(`Không tìm thấy!`);
            }
        }
        function replaceText() {
            const content = editorElement.value;
            const searchValue = searchInput.value
            const replaceValue = replaceInput.value;
        
            if (!searchValue) {
                alert("Chưa nhập nội dung!")
                return
            }
            //alert(typeof searchValue)
        
            const searchValueR = searchValue.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            const regex = new RegExp(searchValueR, "g");
            const matches = content.match(regex);
            const count = matches ? matches.length : 0;
        
            if (count > 0) {
                editorElement.value = content.replaceAll(searchValue, function(){ return replaceValue });
                alert(`Đã thay thế ${count} từ.`);
            } else {
                alert(`Không có từ nào khớp!`);
            }
        }

        // auto focus
        document.addEventListener("DOMContentLoaded", function() {
          editorElement.scrollIntoView({ behavior: "smooth" })
         })

        function save() {
            var data = new FormData();
            data.append("requestApi", 1);
            data.append("content", editorElement.value);
            codeCheckMessageElement.style.display = "none";
            codeCheckMessageElement.innerHTML = "";
            if (codeCheckPHPElement && codeCheckPHPElement.checked) {
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
            data.append("requestApi", 1);
            data.append("format", "<?= $file->getExtension() ?>");
            data.append("content", editorElement.value);

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
            local_storage.set('file_manager.edit.wrap', this.checked ? "1" : "");
        });

console.log(typeof local_storage.get('file_manager.edit.wrap'));
        if (local_storage.get('file_manager.edit.wrap')) {
            $('#code_wrap').prop('checked', true);
            editorElement.removeAttribute("wrap");
            editorElement.removeAttribute("style");
        } else {
            $('#code_wrap').prop('checked', false);
            editorElement.setAttribute("wrap", "off");
            editorElement.setAttribute("style", "white-space: nowrap");
        }
        
        document.addEventListener("keydown", function(event) {
            if (event.ctrlKey && event.key === "s") {
                event.preventDefault()
                save()
            }
        })
    </script>
    
    <style>
        #code_check_message, #code_check_highlight {
            display:none;
        }
    </style>

<?php
    print_file_actions($file);
}

require '_footer.php';
