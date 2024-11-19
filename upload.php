<?php

define('ACCESS', true);

require '.init.php';

$dir = processDirectory($dir);
$title = 'Tải lên tập tin';

if (!$dir || !is_dir($dir)) {
    require 'header.php';
    echo '<div class="title">' . $title . '</div>';

    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png" alt=""/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
    </ul>';
    require 'footer.php';
    exit;
}

if (isset($_FILES['file'])) {
    $data = [];
    $data['error'] = 'Tập tin bị lỗi!';

    if (!empty($_FILES['file']['name'])) {
        if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
            $data['error'] = 'Tập tin ' . $_FILES['file']['name'] . ' vượt quá kích thước cho phép';
        } else {
            $newName = $dir . '/' . $_FILES['file']['name'];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $newName)) {
                $data['error'] = '';
            }
        }
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

$action = 'upload.php?dir=' . $dirEncode . $pages['paramater_1'];

require 'header.php';

echo '<div class="title">' . $title . '</div>';

echo '<div class="list">
    <span>' . printPath($dir, true) . '</span><hr/>
    <form id="formUpload" enctype="multipart/form-data">

        <div class="fileUpload">
            <span class="bull">&bull; </span>Tập tin:<br/>
            <input type="file" size="18"/><br/>
            <div class="result"></div>
            <hr />
        </div>
        
        <div class="fileUpload">
            <span class="bull">&bull; </span>Tập tin:<br/>
            <input type="file" size="18"/><br/>
            <div class="result"></div>
            <hr />
        </div>
        
        <div class="fileUpload">
            <span class="bull">&bull; </span>Tập tin:<br/>
            <input type="file" size="18"/><br/>
            <div class="result"></div>
            <hr />
        </div>
        
        <div class="fileUpload">
            <span class="bull">&bull; </span>Tập tin:<br/>
            <input type="file" size="18"/><br/>
            <div class="result"></div>
            <hr />
        </div>
        
        <div class="fileUpload">
            <span class="bull">&bull; </span>Tập tin:<br/>
            <input type="file" size="18"/><br/>
            <div class="result"></div>
            <hr />
        </div>

        <button id="buttonUpload" class="button">Tải lên</button>
    </form>
</div>

<div class="title">Chức năng</div>
<ul class="list">
    <li><img src="icon/create.png" alt=""/> <a href="create.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Tạo mới</a></li>
    <li><img src="icon/import.png" alt=""/> <a href="import.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Nhập khẩu tập tin</a></li>
    <li><img src="icon/list.png" alt=""/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
</ul>';

?>

<script>
  const form = document.getElementById("formUpload")
  const files = document.getElementsByClassName("fileUpload")
  let uploading = 0

  $('#buttonUpload').click(function (e) {
    e.preventDefault()

    if (uploading) {
        alert("Đang upload!")
        return
    }
    
    $('.fileUpload').each(function() {
        let e = $(this)
        let file = e.find('input[type=file]')[0]
        let result = e.find('.result')
        
        if (file.files.length) {
            upload(file.files[0], result);
        }
    })
  })

  function upload(file, result) {
    uploading++;

    const formData = new FormData();
    formData.append("file", file)

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "<?= $action ?>");

    xhr.upload.onprogress = function (e) {
      if (e.lengthComputable) {
        let loaded = (e.loaded / 1024).toFixed(2) + " KB"
        let total = (e.total / 1024).toFixed(2) + " KB"
        
        result.html('<span style="color: blue">' + loaded + " / " + total + '</span>')
      }
    }

    xhr.onload = function () {
      try {
        var res = JSON.parse(xhr.responseText)

        if (res.error) {
          result.html('<span style="color:red">' + res.error + '</span>')
        } else {
          result.html('<span style="color:green">OK!</span>')
        }
      } catch (e) {
        result.html('<span style="color:red">Thất bại!</span>')
        alert("Tải lên thất bại: " + file.name)
        console.log(e)
      }
    }

    xhr.onloadend = () => {
        uploading--
    }

    xhr.send(formData)
  }
</script>

<?php require 'footer.php';
