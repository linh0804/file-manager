<?php

namespace app;

use SplFileInfo;

define('ACCESS', true);

require '_init.php';

$title = 'Xem tập tin';
$themes = ['a11y-light','a11y-dark','vs','xcode','github-dark-dimmed','github'];
$coder = ['Auto','php','javascript','html','json','text'];

function highlight_string_with_line_numbers($code) {
    $code = str_replace("\r\n", "\n", $code);
    $code = str_replace("\r", "\n", $code);
    $lines = explode("\n", $code);
    $lineCount = count($lines);
    $result = [];
    for ($i = 0; $i < $lineCount; $i++) {
        $result[] = sprintf('<span class="line">%3d</span>', $i + 1);
    }
    $text = '';
    for ($i = ($lineCount-1); $i >= 0; $i--) {
        if(isset($lines[$i]) && $lines[$i] != '') break;
        $text .= '<br /> ';
    }
    return array(
        'line' => implode('',$result),
        'text' => $text
    );
}

function detect_code_type($code) {
    if (strpos((string) $code, "<?php") !== false || strpos((string) $code, "<?=") !== false) {
        return "php";
    } elseif (strpos((string) $code, "const ") !== false || strpos((string) $code, "var ") !== false || strpos((string) $code, "function ") !== false || strpos((string) $code, "document.") !== false) {
        return "javascript";
    } elseif (strpos((string) $code, "background-color") !== false || strpos((string) $code, "background") !== false || strpos((string) $code, "-wekit-") !== false) {
        return "css";
    } elseif (strpos((string) $code, "{\"") !== false && strpos((string) $code, "\"}") !== false && strpos((string) $code, "\":\"") !== false){
        return "json";
    } else {
        return "html";
    }
}

require '_header.php';

echo '<div class="title">' . $title . '</div>';

if ($dir == null || $name == null || !is_file(process_directory($dir . '/' . $name))) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
    </ul>';
} else if (!is_format_text($name) && !is_format_unknown($name)) {
    echo '<div class="list"><span>Tập tin này không phải dạng văn bản</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
    </ul>';
} else {
    $dir = process_directory($dir);
    $path = $dir . '/' . $name;
    $file = new SplFileInfo($path);
    $content = file_get_contents($path);
    $hightlight = highlight_string_with_line_numbers($content);

    echo '<link id="classHl" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/vs.min.css">
    <style>
        pre code.hljs {
            line-height: 1.4;
            text-align: left;
            font-size: 14px !important;
            padding: 0 !important;
            padding-left: 4px !important;
            margin: 0;
        }

        .line {
            line-height: 1.4;
            font-family: monospace;
            font-size:14px;
            padding-right: 5px;
            display: block;
            text-align: right; 
            color: #999; 
            border-right: 1px solid red;
            background-color: #fff;
        }

        #view_code {
            display: flex;
        }

        #code_content {
            width: 0%;
            flex-grow: 1;
            overflow-x: scroll;
        }

        #code_content pre {
            margin: 0;
        }
    </style>';

    echo '<div class="list">
        <span class="bull">&bull; </span><span>' . print_path($dir, true) . '</span><hr/>
        <div class="ellipsis break-word">
            <span class="bull">&bull; </span>Tập tin: <strong class="file_name_edit">' . $name . '</strong>
        </div>
    </div>';

    echo '<div class="list" id="view_code">
        <div id="line_number">'. $hightlight['line'] .'</div>
        <div id="code_content">
            <pre><code class="language-' . detect_code_type($content) .'">'
                . htmlspecialchars($content)
                . $hightlight['text']
            . '</code></pre>
        </div>
    </div>';

    echo '<div class="title">Tùy chỉnh</div>
        <div class="list">
        Giao diện<br />';
    echo '<select id="themes">';
    foreach($themes as $key) {
        echo '<option value="'. $key .'">'.
            $key .'
        </option>';
    }
    echo '</select>
        <hr />
        Cú pháp<br />';
    echo '<select id="coder">';
    foreach($coder as $key) {
        echo '<option value="'. (($key == 'Auto') ? '' : 'language-'. $key) .'">'.
            $key .'
        </option>';
    }
    echo '</select>
        </div>';

    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>';

    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        hljs.configure({
            ignoreUnescapedHTML: true
        });
        hljs.highlightAll();

        const codeElements = document.querySelector("code");

        // doi theme
        var elementTheme = document.querySelector("#themes");
        elementTheme.addEventListener("change", function () {
            var currentHref = document.getElementById("classHl").href;
            var newHref = currentHref.replace(/\/[^\/]*$/, "/" + elementTheme.value);
            document.getElementById("classHl").href = newHref + ".min.css";
        });

        // doi cu phap
        var elementCode = document.querySelector("#coder");
        elementCode.addEventListener("change", function () {
            codeElements.className = elementCode.value;
            delete codeElements.dataset.highlighted;
            hljs.highlightAll();
        });
    });
    </script>';

    print_actions($file);
}

require '_footer.php';
