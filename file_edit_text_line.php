<?php


defined('ACCESS') or exit;

$dir = dirname($curr_path);
$name = basename($curr_path);

$site_title = 'Sửa tập tin theo dòng';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = $page < 1 ? 1 : $page;

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

if (!is_file(process_directory($curr_path))) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="' . action_link('index') . '">Danh sách</a></li>
    </ul>';
} elseif (!file_is_text($name) && !file_is_unknown($name)) {
    echo '<div class="list"><span>Tập tin này không phải dạng văn bản</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="' . action_link('index', ['path' => $dir]) . '">Danh sách</a></li>
    </ul>';
} else {
    $path = $curr_path;
    $content = file_get_contents($path);
    $lines = [];
    $count = 0;

    if (strlen($content) > 0) {
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);

        if (strpos($content, "\n") !== false) {
            $lines = explode("\n", $content);
            $count = count($lines);
        } else {
            $lines[] = $content;
            $count = 1;
        }
    } else {
        $lines[] = $content;
        $count = 1;
    }

    if (PAGE_SIZE <= 0) {
        $page = 1;
    } elseif ($page > (int) ceil($count / PAGE_SIZE)) {
        $page = 1;
    }

    $display_lines = paging_arr($lines, $page, PAGE_SIZE);
    $offset = PAGE_SIZE > 0 ? ($page - 1) * PAGE_SIZE : 0;

    echo '<div class="list">
        <span class="bull">&bull; </span><span>' . file_print_path($dir, true) . '</span><hr/>
        <div class="ellipsis break-word">
            <span class="bull">&bull; </span>Tập tin: <strong class="file_name_edit">' . $name . '</strong>
        </div>
    </div>
    <div class="list_line">';

    foreach ($display_lines as $index => $line) {
        $line_number = $offset + $index;

        echo '<div id="line">
            <div id="line_number_' . $line_number . '">' . htmlspecialchars($line) . '</div>
            <div>
                <span id="line_number">[<span>' . ($line_number + 1) . '</span>]</span>
                <a href="' . action_link('file', ['act' => 'edit_text_line_number', 'path' => $curr_path, 'line' => $line_number, 'page' => $page > 1 ? $page : null]) . '">Sửa</a>
                <span> | </span>
                <a href="' . action_link('file', ['act' => 'edit_text_line_delete', 'path' => $curr_path, 'line' => $line_number, 'page' => $page > 1 ? $page : null]) . '">Xóa</a>
            </div>
        </div>';
    }

    echo paging('file', 'page', ['act' => 'edit_text_line', 'path' => $curr_path], $page, $count, PAGE_SIZE);

    echo '</div>
    <div class="tips">
        <img src="icon/tips.png"/>
        <span>Khuyên bạn nên sửa dạng văn bản, dạng sửa này xử lý khá nhiều trong một lần request</span>
    </div>';

    file_display_actions($curr_path);
}

require SITE_FOOTER;
