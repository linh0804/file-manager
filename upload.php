<?php
namespace app;

use nightmare\http\response;

define('ACCESS', true);

require '_init.php';

$title = 'Tải lên tập tin';

check_path($path);

if (isset($_FILES['file'])) {
    $data = [];
    $data['error'] = 'Tập tin bị lỗi!';

    if (!empty($_FILES['file']['name'])) {
        if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
            $data['error'] = 'Tập tin ' . $_FILES['file']['name'] . ' vượt quá kích thước cho phép';
        } else {
            $newName = $path . '/' . $_FILES['file']['name'];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $newName)) {
                $data['error'] = '';
            }
        }
    }   
    
    (new response($data))->send();
}

$action = 'upload.php?path=' . $path;

require '_header.php';

echo '<div class="title">' . $title . '</div>';

echo '<div class="list">
  <span>' . print_path($path, true) . '</span><hr/>
  <form enctype="multipart/form-data">        
    <div id="fileList"></div>
    <input id="files" type="file" multiple style="display:none">
 
    <button id="buttonChoose" class="button"><img src="icon/file.png" alt=""/> Chọn file</button>
    <button id="buttonReset" class="button"><img src="icon/delete.png" alt=""/> Reset</button>
    <br>
    <button id="buttonUpload" class="button"><img src="icon/upload.png" alt=""/> Tải lên</button>
  </form>
</div>';

show_back();

?>

<script>
  const fileList = $('#fileList');
  
  const files = [];
  let uploading = 0;

  $('#buttonChoose').on('click', function (e) {
    e.preventDefault();
    $('#files').val('');
    $('#files').click();
  });
  $('#buttonReset').on('click', function (e) {
    e.preventDefault();
    
    if (uploading) {
        alert("Đang upload!")
        return
    }
    
    files.length = 0;
    fileList.empty();
  });
  $('#files').on('change', function (e) {
	fileList.empty();
	
	files.push(...Array.from($(this)[0].files))
    for (let i = 0; i < files.length; i++) {      
      fileList.append(`
        <div class="fileUpload" data-id="${i}">
          <span class="bull">&gt;&gt; </span>${files[i].name}<br/>
          <div class="result"></div>
          <hr />
        </div>
      `);
    }
  });

  $('#buttonUpload').click(function (e) {
    e.preventDefault()

    if (!files) {
      alert('Chưa chọn file!');
      return;
    }

    if (uploading) {
        alert("Đang upload!")
        return
    }
    
    NProgress.start();
    
    $('.fileUpload').each(function() {
        let e = $(this);
        let id = e.data('id');
        
        if (files[id]) {
            upload(files[id], e.find('.result'));
        }
    })
  })

  function upload(file, result) {
    console.log(file.name);
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
        
        if (uploading == 0) {
            NProgress.done();
        }
    }

    xhr.send(formData)
  }
</script>

<?php require '_footer.php';
