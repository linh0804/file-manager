<?php

define('ACCESS', true);

$function = 'exec';
if (!function_exists($function)) {
    exit($function . '() function not found');
}

require_once '.init.php';

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

echo '<div class="list">';

echo '<form method="post">
  <span>Thư mục:</span><br />
  <input type="text" name="folder" value="' . htmlspecialchars($folder) . '" /><br />

  <span>Lệnh:</span><br />
  <input type="text" name="command" value="' . htmlspecialchars($command) . '" /><br />

 <input type="submit" name="submit" value="OK" />
</form>';

// OK
if (isset($_POST['submit'])) {
    if ($folder) {
        $command = "cd $folder; $command";
    }

    // RUN
    $output = [];
    $result_code = '';

    if ($command) {
        exec($command, $output, $result_code);
    }

    //
    echo '<hr />';

    echo 'Thư mục:';
    echo '<pre>' . htmlspecialchars($folder) . '</pre>';

    echo 'Lệnh:';
    echo '<pre>' . htmlspecialchars($command) . '</pre>';

    echo 'Code:';
    echo '<pre>' . htmlspecialchars($result_code) . '</pre>';

    echo 'Kết quả:';
    echo '<pre id="output">' . htmlspecialchars(implode("\n", $output)) . '</pre>';
    
    //var_dump($output);
}

echo '</div>';

require 'footer.php';
