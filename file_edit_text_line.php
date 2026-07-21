<?php


defined('ACCESS') or exit;

$dir = dirname($curr_path);
$name = basename($curr_path);

$site_title = 'Sửa tập tin theo dòng';
$page = array('current' => 0, 'total' => 1, 'paramater_0' => null, 'paramater_1' => null);
$page['current'] = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page['current'] = $page['current'] <= 0 ? 1 : $page['current'];

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
    if ($page['current'] > 1 && PAGE_SIZE > 0) {
        $page['paramater_0'] = '?page=' . $page['current'];
        $page['paramater_1'] = '&page=' . $page['current'];
    }

    $path = $curr_path;
    $content = file_get_contents($path);
    $lines = [];
    $count = 0;
    $start = 0;
    $end = 0;

    if (strlen($content) > 0) {
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);

        if (strpos($content, "\n") !== false) {
            $lines = explode("\n", $content);
            $count = count($lines);

            if (PAGE_SIZE > 0) {
                $page['total'] = ceil($count / PAGE_SIZE);
            }
        } else {
            $lines[] = $content;
            $count = 1;
        }
    } else {
        $lines[] = $content;
        $count = 1;
    }

    if (PAGE_SIZE > 0) {
        $start = ($page['current'] * PAGE_SIZE) - PAGE_SIZE;
        $end = $start + PAGE_SIZE > $count - 1 ? $count : $start + PAGE_SIZE;
    } else {
        $start = 0;
        $end = $count;
    }

    if ($page['current'] < 0 && PAGE_SIZE > 0) {
        redirect(action_link('file', ['act' => 'edit_text_line', 'path' => $curr_path]));
    }

    if ($page['current'] > $page['total'] && PAGE_SIZE > 0) {
        redirect(action_link('file', [
            'act' => 'edit_text_line',
            'path' => $curr_path,
            'page' => $page['total'] > 1 ? $page['total'] : null,
        ]));
    }

    echo '<div class="list">
        <span class="bull">&bull; </span><span>' . file_print_path($dir, true) . '</span><hr/>
        <div class="ellipsis break-word">
            <span class="bull">&bull; </span>Tập tin: <strong class="file_name_edit">' . $name . '</strong>
        </div>
    </div>
    <div class="list_line">';

    for ($i = $start; $i < $end; ++$i) {
        echo '<div id="line">
            <div id="line_number_' . $i . '">' . htmlspecialchars($lines[$i]) . '</div>
            <div>
                <span id="line_number">[<span>' . ($i + 1) . '</span>]</span>
                <a href="' . action_link('file', ['act' => 'edit_text_line_number', 'path' => $curr_path, 'line' => $i, 'page' => $page['current'] > 1 ? $page['current'] : null]) . '">Sửa</a>
                <span> | </span>
                <a href="' . action_link('file', ['act' => 'edit_text_line_delete', 'path' => $curr_path, 'line' => $i, 'page' => $page['current'] > 1 ? $page['current'] : null]) . '">Xóa</a>
            </div>
        </div>';
    }

    if ($page['total'] > 1 && PAGE_SIZE > 0) {
        $pageUrl = action_link('file', ['act' => 'edit_text_line', 'path' => $curr_path]);
        echo page($page['current'], $page['total'], array(PAGE_URL_DEFAULT => $pageUrl, PAGE_URL_START => $pageUrl . '&page='));
    }

    echo '</div>
    <div class="tips">
        <img src="icon/tips.png"/>
        <span>Khuyên bạn nên sửa dạng văn bản, dạng sửa này xử lý khá nhiều trong một lần request</span>
    </div>';

    file_display_actions($curr_path);
}

require SITE_FOOTER;
