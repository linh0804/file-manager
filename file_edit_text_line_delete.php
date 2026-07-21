<?php

defined('ACCESS') or exit;

$dir = dirname($curr_path);
$name = basename($curr_path);

$site_title = 'Xóa dòng';
$page = array('current' => 0, 'paramater_0' => null, 'paramater_1' => null);
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
    function process()
    {
        global $content, $lines, $count, $path;

        $content = file_get_contents($path);

        if (strlen($content) > 0) {
            $content = str_replace("\r\n", "\n", $content);
            $content = str_replace("\r", "\n", $content);

            if (strpos($content, "\n") !== false) {
                $lines = explode("\n", $content);
            } else {
                $lines[] = $content;
            }
        } else {
            $lines[] = $content;
        }

        $count = count($lines);
    }

    $path = $curr_path;
    $line = isset($_GET['line']) ? intval($_GET['line']) : 0;
    $content = null;
    $notice = null;
    $lines = [];
    $count = 0;

    if ($page['current'] > 1) {
        $page['paramater_0'] = '?page=' . $page['current'];
        $page['paramater_1'] = '&page=' . $page['current'];
    }

    process();

    if (isset($_POST['continue']) || isset($_POST['delete'])) {
        $data = null;

        if ($count > 1) {
            if ($line > 0) {
                for ($i = 0; $i < $line; ++$i) {
                    $data .= $lines[$i] . ($i < $line - 1 ? "\n" : null);
                }
            }

            if ($line < $count - 1) {
                for ($i = ($line + 1); $i < $count; ++$i) {
                    $data .= ($i > 1 || $line > 0 ? "\n" : null) . $lines[$i];
                }
            }
        } else {
            $data = null;
        }

        if (file_put_contents($path, $data)) {
            $notice = '<div class="notice_succeed">Xóa thành công</div>';

            if (isset($_POST['delete'])) {
                redirect(action_link('file', ['act' => 'edit_text_line', 'path' => $curr_path, 'page' => $page['current'] > 1 ? $page['current'] : null]) . '#line_number_' . ($line > $count - 2 ? $count - 2 : $line));
            }
        } else {
            $notice = '<div class="notice_failure">Xóa thất bại</div>';
        }

        process();
    }

    $isGO = false;

    if (isset($_POST['go']) && !empty($_POST['line']) && preg_match('#\\b[0-9]+\\b#', (string) $_POST['line'])) {
        $li = intval($_POST['line']);

        if ($li >= 0 && $li <= $count - 1) {
            $line = $li;
            $isGO = true;
        }
    }

    if ($line < 0) {
        redirect(action_link('file', ['act' => 'edit_text_line_delete', 'path' => $curr_path, 'line' => 0, 'page' => $page['current'] > 1 ? $page['current'] : null]));
    }

    if ($line > $count - 1) {
        redirect(action_link('file', ['act' => 'edit_text_line_delete', 'path' => $curr_path, 'line' => $count - 1, 'page' => $page['current'] > 1 ? $page['current'] : null]));
    }

    $page['current'] = $line + 1 > PAGE_SIZE ? ceil(($line + 1) / PAGE_SIZE) : 1;

    if ($page['current'] > 1) {
        $page['paramater_0'] = '?page=' . $page['current'];
        $page['paramater_1'] = '&page=' . $page['current'];
    }

    if ($isGO) {
        redirect(action_link('file', ['act' => 'edit_text_line_delete', 'path' => $curr_path, 'line' => $line, 'page' => $page['current'] > 1 ? $page['current'] : null]));
    }

    $url = array('action' => null, 'prev' => null, 'next' => null);
    $url['action'] = action_link('file', ['act' => 'edit_text_line_delete', 'path' => $curr_path, 'line' => $line, 'page' => $page['current'] > 1 ? $page['current'] : null]) . '#line_label';
    $url['prev'] = $line > 0 ? '<a href="' . action_link('file', ['act' => 'edit_text_line_delete', 'path' => $curr_path, 'line' => $line - 1, 'page' => PAGE_SIZE > 0 && $line <= PAGE_SIZE ? null : ceil($line / PAGE_SIZE)]) . '#line_label"><img src="icon/arrow_left.png"/></a>' : '<img src="icon/arrow_left.png"/>';
    $url['next'] = $line < $count - 1 ? '<a href="' . action_link('file', ['act' => 'edit_text_line_delete', 'path' => $curr_path, 'line' => $line + 1, 'page' => PAGE_SIZE > 0 && $line <= PAGE_SIZE ? null : ceil(($line + 2) / PAGE_SIZE)]) . '#line_label"><img src="icon/arrow_right.png"/></a>' : '<img src="icon/arrow_right.png"/>';

    echo $notice;
    echo '<div class="list">
        <span class="bull">&bull; </span><span>' . file_print_path($dir, true) . '</span><hr/>
        <div class="ellipsis break-word">
            <span class="bull">&bull; </span>Tập tin: <strong class="file_name_edit">' . $name . '</strong>
        </div><hr/>
        <span class="bull" id="line_label">&bull; </span>Dòng [<strong class="line_number_form">' . $line . '</strong>/<strong class="line_number_form">' . ($count - 1) . '</strong>]:
        <div class="box_text break-word">
            <span>' . htmlspecialchars($lines[$line]) . '</span>
        </div>
        <div style="margin-left: -4px">
            <form action="' . $url['action'] . '" method="post">
                <input type="submit" name="continue" value="Tiếp tục"/>
                <input type="submit" name="delete" value="Xóa"/>
                <a href="' . action_link('file', ['act' => 'edit_text_line_number', 'path' => $curr_path, 'line' => $line, 'page' => $page['current'] > 1 ? $page['current'] : null]) . '" id="href_line_edit">Sửa</a>
            </form>
        </div><hr/>
        <form action="' . $url['action'] . '" method="post">
            <table id="action_page">
                <tr>
                    <td id="prev">' . $url['prev'] . '</td>
                    <td id="input">
                        <input type="text" name="line" value="' . $line . '"/>
                    </td>
                    <td id="submit">
                        <input type="submit" name="go" value="Đến"/>
                    </td>
                    <td id="next">' . $url['next'] . '</td>
                </tr>
            </table>
        </form>
    </div>
    <div class="tips">
        <img src="icon/tips.png"/>
        <span>Ấn tiếp tục để xóa và ở lại trang, ấn xóa để xóa và quay về danh sách dòng</span>
    </div>';

    file_display_actions($curr_path);
}

require SITE_FOOTER;
