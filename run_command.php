<?php

define('ACCESS', true);

require '.init.php';

$function = 'exec';
if (!function_exists($function)) {
    exit($function . '() function not found');
}

$title = 'Chạy lệnh hệ thống';

require 'header.php';

echo '<style>
  input[type="text"] {
    width: 100%;
  }

  pre {
    padding: 6px;
    border: 0.5px solid #cecece;
    white-space: pre-wrap;
  }

  pre#output {
    overflow-x: scroll;
    white-space: pre;
  }
</style>';

echo '<div class="title">' . $title . '</div>';

$folder = $_POST['folder'] ?? (string) $dir;
$command = $_POST['command'] ?? '';

echo '<div class="list">
  <form method="post">
    <span>Thư mục:</span><br />
    <input type="text" name="folder" value="' . htmlspecialchars($folder) . '" /><br />

    <span>Lệnh:</span><br />
    <input type="text" name="command" value="' . htmlspecialchars($command) . '" /><br />

   <input type="submit" name="submit" value="OK" />
  </form>
</div>';

if (isset($_POST['submit'])) {
    echo '<div class="list">';

    if ($folder) {
        $command = "cd $folder; $command";
    }

    // RUN
    if ($command) {
        $res = runCommand($command);
    }

    if (isset($res) && $res !== false) {
        echo 'Lệnh:';
        echo '<pre>' . htmlspecialchars($command) . '</pre>';

        if ($res['err']) {
            echo 'Lỗi:';
            echo '<pre style="color: red">' . htmlspecialchars($res['err']) . '</pre>';
        }

        echo 'Code:';
        echo '<pre style="color: blue">' . htmlspecialchars($res['code']) . '</pre>';

        echo 'Kết quả:';
        echo '<pre id="output">' . htmlspecialchars($res['out']) . '</pre>';

        echo 'Thư mục thực thi:';
        echo '<pre>' . htmlspecialchars($folder) . '</pre>';
    } else {
        echo 'Không thể thực thi lệnh!';
    }

    echo '</div>';
}

require 'footer.php';
