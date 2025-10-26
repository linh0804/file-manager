<?php
namespace app;

define('ACCESS', true);

require '_init.php';

$title  = 'Hành động';
$entry  = $_POST['entry'] ?? [];
$option = isset($_POST['option']) ? intval($_POST['option']) : -1;

if ($dir == null || !is_dir(process_directory($dir))) {
    require_once '_header.php';

    echo '<div class="title">' . $title . '</div>
        <div class="list"><span>Đường dẫn không tồn tại</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li>
                <img src="icon/list.png" alt="" />
                <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a>
            </li>
        </ul>';
} elseif (!$_POST || ($option < 0 || $option > 5)) {
    require_once '_header.php';

    echo '<div class="title">' . $title . '</div>
        <div class="list"><span>Không có hành động</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
        </ul>';
} elseif (count($entry) <= 0) {
    require_once '_header.php';

    echo '<div class="title">' . $title . '</div>
        <div class="list"><span>Không có lựa chọn</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
        </ul>';
} else {
    $dir = process_directory($dir);
    $entryCheckbox = null;
    $entryHtmlList = null;

    if ($option != 5) {
        $entryHtmlList = '<ul class="list">';
    }

    foreach ($entry as $e) {
        $isFolder = is_dir($dir . '/' . $e);

        $entryCheckbox .= '<input type="hidden" name="entry[]" value="' . $e . '" checked="checked"/>';

        if ($option != 5) {
            $entryHtmlList .= '<li>'
                . get_icon($isFolder ? 'folder' : 'file', $e) . ' '
                . ($isFolder ? '<strong class="folder_name">' . $e . '</strong>' : '<span class="file_name">' . $e . '</span>') . '
                </li>';
        }
    }

    if ($option != 5) {
        $entryHtmlList .= '</ul>';
    }

    if ($option === 0) {
        $title = 'Sao chép';

        require_once '_header.php';

        echo '<div class="title">' . $title . '</div>';

        if (isset($_POST['submit']) && isset($_POST['is_action'])) {
            echo '<div class="notice_failure">';

            if (empty($_POST['path'])) {
                echo 'Chưa nhập đầy đủ thông tin';
            } elseif ($dir == process_directory($_POST['path'])) {
                echo 'Đường dẫn mới phải khác đường dẫn hiện tại';
            } elseif (!is_dir($_POST['path'])) {
                echo 'Đường dẫn mới không tồn tại';
            } elseif (!copys($entry, $dir, process_directory($_POST['path']))) {
                echo 'Sao chép thất bại';
            } else {
                redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
            }

            echo '</div>';
        }

        echo $entryHtmlList;
        echo '<div class="list">
                <span>' . print_path($dir, true) . '</span><hr/>
                <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull; </span>Đường dẫn tập tin mới:<br/>
                    <textarea name="path" data-autoresize>' . ($_POST['path'] ?? $dir) . '</textarea><br/>
                    <input type="hidden" name="is_action" value="1"/>
                    <input type="hidden" name="option" value="' . $option . '"/>';

        echo $entryCheckbox;

        echo '<input type="submit" name="submit" value="Sao chép"/>
                </form>
            </div>';
    } elseif ($option === 1) {
        $title = 'Di chuyển';

        require_once '_header.php';

        echo '<div class="title">' . $title . '</div>';

        if (isset($_POST['submit']) && isset($_POST['is_action'])) {
            echo '<div class="notice_failure">';

            if (empty($_POST['path'])) {
                echo 'Chưa nhập đầy đủ thông tin';
            } elseif ($dir == process_directory($_POST['path'])) {
                echo 'Đường dẫn mới phải khác đường dẫn hiện tại';
            } elseif (!is_dir($_POST['path'])) {
                echo 'Đường dẫn mới không tồn tại';
            } elseif (!moves($entry, $dir, process_directory($_POST['path']))) {
                echo 'Di chuyển thất bại';
            } else {
                redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
            }

            echo '</div>';
        }

        echo $entryHtmlList;
        echo '<div class="list">
                <span>' . print_path($dir, true) . '</span><hr/>
                <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull; </span>Đường dẫn tập tin mới:<br/>
                    <textarea name="path" data-autoresize>' . ($_POST['path'] ?? $dir) . '</textarea><br/>
                    <input type="hidden" name="is_action" value="1"/>
                    <input type="hidden" name="option" value="' . $option . '"/>';

        echo $entryCheckbox;

        echo '<input type="submit" name="submit" value="Di chuyển"/>
                </form>
            </div>';
    } elseif ($option === 2) {
        $title = 'Xóa';

        require_once '_header.php';

        echo '<div class="title">' . $title . '</div>';

        if (isset($_POST['accept'])) {
            if (!rrms($entry, $dir)) {
                echo '<div class="notice_failure">Xóa thất bại</div>';
            } else {
                redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
            }
        } elseif (isset($_POST['not_accept'])) {
            redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
        }

        echo $entryHtmlList;
        echo '<div class="list">
                <span>' . print_path($dir, true) . '</span><hr/>
                <span>Bạn có thực sự muốn xóa các mục đã chọn không?</span><hr/><br/>
                <center>
                    <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                        <input type="hidden" name="is_action" value="1"/>
                        <input type="hidden" name="option" value="' . $option . '"/>';

        echo $entryCheckbox;

        echo '<input type="submit" name="accept" value="Đồng ý"/>
                        <input type="submit" name="not_accept" value="Huỷ bỏ"/>
                    </form>
                </center>
            </div>';
    } elseif ($option === 3) {
        $title = 'Nén zip';

        require_once '_header.php';

        echo '<div class="title">' . $title . '</div>';

        if (isset($_POST['submit']) && isset($_POST['is_action'])) {
            echo '<div class="notice_failure">';

            if (empty($_POST['name']) || empty($_POST['path'])) {
                echo 'Chưa nhập đầy đủ thông tin';
            } elseif (isset($_POST['is_delete']) && process_directory($_POST['path']) == $dir . '/' . $name) {
                echo 'Nếu chọn xóa thư mục bạn không thể lưu tập tin nén ở đó';
            } elseif (is_name_error($_POST['name'])) {
                echo 'Tên tập tin zip không hợp lệ';
            } elseif (file_exists(process_directory($_POST['path'] . '/' . process_name($_POST['name'])))) {
                echo 'Tập tin đã tồn tại, vui lòng đổi tên!';
            } elseif (!zips($dir, $entry, process_directory($_POST['path'] . '/' . process_name($_POST['name'])), isset($_POST['is_delete']))) {
                echo 'Nén zip thất bại';
            } else {
                redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
            }

            echo '</div>';
        }

        echo $entryHtmlList;
        echo '<div class="list">
                <span>' . print_path($dir, true) . '</span><hr/>
                <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull; </span>Tên tập tin nén:<br/>
                    <input type="text" name="name" value="' . ($_POST['name'] ?? 'archive.zip') . '" size="18"/><br/>
                    <span class="bull">&bull; </span>Đường dẫn lưu:<br/>
                    <textarea name="path" data-autoresize>' . ($_POST['path'] ?? $dir) . '</textarea><br/>
                    <input type="checkbox" name="is_delete" value="1"' . (isset($_POST['is_delete']) ? ' checked="checked"' : null) . '/> Xóa nguồn<br/>
                    <input type="hidden" name="is_action" value="1"/>
                    <input type="hidden" name="option" value="' . $option . '"/>';

        echo $entryCheckbox;

        echo '<input type="submit" name="submit" value="Nén"/>
                </form>
            </div>';
    } elseif ($option === 4) {
        $title = 'Chmod';

        require_once '_header.php';

        echo '<div class="title">' . $title . '</div>';

        if (isset($_POST['submit']) && isset($_POST['is_action'])) {
            echo '<div class="notice_failure">';

            if (empty($_POST['folder']) || empty($_POST['file'])) {
                echo 'Chưa nhập đầy đủ thông tin';
            } elseif (!chmods($dir, $entry, $_POST['folder'], $_POST['file'])) {
                echo 'Chmod thất bại';
            } else {
                redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
            }

            echo '</div>';
        }

        echo $entryHtmlList;
        echo '<div class="list">
                <span>' . print_path($dir, true) . '</span><hr/>
                <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull; </span>Thư mục:<br/>
                    <input type="text" name="folder" value="' . ($_POST['folder'] ?? '755') . '" size="18"/><br/>
                    <span class="bull">&bull; </span>Tập tin:<br/>
                    <input type="text" name="file" value="' . ($_POST['file'] ?? '644') . '" size="18"/><br/>
                    <input type="hidden" name="is_action" value="1"/>
                    <input type="hidden" name="option" value="' . $option . '"/>';

        echo $entryCheckbox;

        echo '<input type="submit" name="submit" value="Chmod"/>
                </form>
            </div>';
    } elseif ($option === 5) {
        $title    = 'Đổi tên';
        $modifier = $entry;

        require_once '_header.php';

        echo '<div class="title">' . $title . '</div>';

        if (isset($_POST['submit']) && isset($_POST['is_action'])) {
            $modifier  = $_POST['modifier'];
            $isFailed  = false;
            $isSucceed = true;

            foreach ($modifier as $k => $e) {
                $entryPath = $dir . '/' . $entry[$k];

                if (empty($e)) {
                    $isFailed = true;

                    echo '<div class="notice_failure">Không được để trống ô nào</div>';
                    break;
                } elseif (is_name_error($e)) {
                    $isFailed   = true;
                    $entryLabel = is_dir($entryPath) ? 'thư mục' : 'tập tin';
                    $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                    echo '<div class="notice_failure">Tên ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $entry[$k] . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> không hợp lệ</div>';
                    break;
                } elseif (count_string_array($modifier, strtolower((string) $e), true) > 1 && $e != $entry[$k]) {
                    $isFailed   = true;
                    $entryLabel = is_dir($entryPath) ? 'thư mục' : 'tập tin';
                    $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                    echo '<div class="notice_failure">Tên ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $entry[$k] . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> này đã tồn tại ở một khung nhập khác</div>';
                    break;
                } elseif (!is_in_array($entry, strtolower((string) $e), true) && file_exists($dir . '/' . $e)) {
                    $isFailed   = true;
                    $entryLabel = is_dir($entryPath) ? 'thư mục' : 'tập tin';
                    $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                    echo '<div class="notice_failure">Tên ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $entry[$k] . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> này đã tồn tại</div>';
                    break;
                }
            }

            if (!$isFailed) {
                $isSucceed = true;
                $rand      = md5(rand(1000, 99999) . '-' . $dir);
                $rand      = substr($rand, 0, strlen($rand) >> 1);

                foreach ($entry as $e) {
                    $entryPath = $dir . '/' . $e;

                    @rename($entryPath, $entryPath . '-' . $rand);
                }

                foreach ($entry as $k => $e) {
                    $entryPath  = $dir . '/' . $e;
                    $entryLabel = is_dir($entryPath) ? 'thư mục' : 'tập tin';
                    $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                    if (!@rename($entryPath . '-' . $rand, $dir . '/' . process_name($modifier[$k]))) {
                        $isSucceed = false;

                        echo '<div class="notice_failure">Đổi tên ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $modifier[$k] . '</strong> thất bại</div>';
                    } else {
                        $entry[$k] = $modifier[$k];

                        echo '<div class="notice_succeed">Đổi tên ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $modifier[$k] . '</strong> thành công</div>';
                    }
                }
            }

            if (!$isFailed && $isSucceed) {
                redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
            }
        }

        echo $entryHtmlList;
        echo '<div class="list break-word">
                <span>' . print_path($dir, true) . '</span><hr/>
                <form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">';

        for ($i = 0; $i < count($entry); ++$i) {
            $entryPath = $dir . '/' . $entry[$i];
            $entryName = $entry[$i];

            if (is_dir($entryPath)) {
                echo '<span class="bull">&bull; </span>Tên thư mục (<strong class="folder_name_rename_action">' . $entryName . '</strong>):<br/>';
            } else {
                echo '<span class="bull">&bull; </span>Tên tập tin (<strong class="file_name_rename_action">' . $entryName . '</strong>):<br/>';
            }

            echo '<input type="text" name="modifier[]" value="' . $modifier[$i] . '" size="18"/><br/>';
        }

        echo '<input type="hidden" name="is_action" value="1"/>
                <input type="hidden" name="option" value="' . $option . '"/>';

        echo $entryCheckbox;

        echo '<input type="submit" name="submit" value="Đổi tên"/>
                </form>
            </div>';
    }

    echo '<div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
        </ul>';
}

require_once '_footer.php';
