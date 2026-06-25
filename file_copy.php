<?php

defined('ACCESS') or exit;

$site_title = 'Sao chép tập tin';

$dir = dirname($curr_path);
$name = basename($curr_path);

$new_name = $_POST['name'] ?? $name;
$new_dir = $_POST['dir'] ?? $dir;
$new_path = "$new_dir/$new_name";
$notice = '';

if (isset($_POST['submit'])) {
    if (empty($new_dir) || empty($new_name)) {
        $notice = 'Chưa nhập đầy đủ thông tin';
    } elseif (file_exists($new_path)) {
        $notice = 'Tệp đã tồn tại';
    } elseif (!@copy($dir . '/' . $name, $new_path)) {
        $notice = 'Sao chép tập tin thất bại';
    } else {
        redirect(action_link('index', ['path' => $dir] + get_page_list_params()));
    }
}

require SITE_HEADER;
?>

<div class="title"><?= $site_title ?></div>

<?php if (!empty($notice)) { ?>
    <div class="notice_failure"><?= $notice ?></div>
<?php } ?>

<div class="list">
    <span class="bull">&bull;</span><span><?= file_print_path($dir . '/' . $name) ?></span><hr/>
    <form action="" method="post">
        <span class="bull">&bull;</span>Đường dẫn tập tin mới:<br/>
        <input type="text" name="dir" value="<?= htmlspecialchars((string) $new_dir) ?>" size="18"/><br/>
        <input type="text" name="name" value="<?= htmlspecialchars((string) $new_name) ?>" size="18"/><br/>
        <input type="submit" name="submit" value="Sao chép"/>
    </form>
</div>

<?php

file_display_actions($curr_path);

require SITE_FOOTER;
