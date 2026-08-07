<?php

use Nightmare\Http\Response;

define('ACCESS', true);
require __DIR__ . '/file.php';

if (isset($_FILES['file'])) {
    $data = [];
    $data['error'] = 'Tập tin bị lỗi!';

    if (!empty($_FILES['file']['name'])) {
        if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
            $data['error'] = 'Tập tin ' . $_FILES['file']['name'] . ' vượt quá kích thước cho phép';
        } else {
            $newName = $curr_path . '/' . $_FILES['file']['name'];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $newName)) {
                $data['error'] = '';
            }
        }
    }   
    
    response($data);
}

$action = act_link('file_upload', ['path' => $curr_path]);
$site_title = 'Tải lên tập tin';

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

echo '<div class="list">
  <span>' . file_print_path($curr_path, true) . '</span><hr/>
  <form id="file-upload" enctype="multipart/form-data">        
    <div id="file-list"></div>
    <input id="files" type="file" multiple style="display:none">
 
    <button id="button-choose" class="button"><img src="icon/file.png" alt=""/> Chọn file</button>
    <button id="button-reset" class="button"><img src="icon/delete.png" alt=""/> Reset</button>
    <br>
    <button id="button-upload" class="button"><img src="icon/upload.png" alt=""/> Tải lên</button>
  </form>
</div>';

?>

<script>
  const $document = $(document);
  
  const files = [];
  let uploading = false;

  $document.on('click', '#file-upload #button-choose', function (e) {
    e.preventDefault();
    const $fileUpload = $(this).closest('#file-upload');
    $fileUpload.find('#files').val('');
    $fileUpload.find('#files').click();
  });

  $document.on('click', '#file-upload #button-reset', function (e) {
    e.preventDefault();
    const $fileUpload = $(this).closest('#file-upload');
    
    if (uploading) {
        alert("Đang upload!")
        return
    }
    
    files.length = 0;
    $fileUpload.find('#file-list').empty();
  });

  $document.on('change', '#file-upload #files', function (e) {
    const $fileUpload = $(this).closest('#file-upload');
    const $fileList = $fileUpload.find('#file-list');

	$fileList.empty();
	
	files.push(...Array.from(this.files))
    for (let i = 0; i < files.length; i++) {      
      $fileList.append(`
        <div class="file-upload" data-id="${i}">
          <span class="bull">&gt;&gt; </span>${files[i].name}<br/>
          <div class="result"></div>
          <hr />
        </div>
      `);
    }
  });

  $document.on('click', '#file-upload #button-upload', async function (e) {
    e.preventDefault()
    const $fileUpload = $(this).closest('#file-upload');

    if (!files.length) {
      alert('Chưa chọn file!');
      return;
    }

    if (uploading) {
        alert("Đang upload!")
        return
    }
    
    const uploadItems = [];
    
    $fileUpload.find('.file-upload').each(function() {
        let $item = $(this);
        let id = $item.data('id');
        
        if (files[id]) {
            uploadItems.push({
                file: files[id],
                result: $item.find('.result')
            });
        }
    })

    uploading = true;
    NProgress.start();
    
    try {
        for (const item of uploadItems) {
            await upload(item.file, item.result);
        }
    } finally {
        uploading = false;
        NProgress.done();
    }
  })

  function upload(file, result) {
    return new Promise(function (resolve) {
      console.log(file.name);

      const formData = new FormData();
      formData.append("file", file)

      var xhr = new XMLHttpRequest();
      xhr.open("POST", "<?= $action ?>");

      xhr.upload.onprogress = function (e) {
        if (e.lengthComputable) {
          let percent = Math.min(100, Math.round((e.loaded / e.total) * 100))
          let total = (e.total / (1024 * 1024)).toFixed(2) + " MB"

          result.html('<span style="color: blue">(' + percent + "%) " + total + '</span>')
        }
      }

      xhr.onload = function () {
        try {
          var res = JSON.parse(xhr.responseText)

          if (res.error) {
            result.html('<span style="color:red">' + res.error + '</span>')
            alert("Tải lên thất bại: " + file.name)
          } else if (xhr.status < 200 || xhr.status >= 300) {
            result.html('<span style="color:red">Thất bại!</span>')
            alert("Tải lên thất bại: " + file.name)
          } else {
            result.html('<span style="color:green">OK!</span>')
          }
        } catch (e) {
          result.html('<span style="color:red">Thất bại!</span>')
          alert("Tải lên thất bại: " + file.name)
          console.log(e)
        }
      }

      xhr.onerror = function () {
        result.html('<span style="color:red">Lỗi kết nối!</span>')
        alert("Tải lên thất bại: " + file.name)
      }

      xhr.onloadend = function () {
        resolve();
      }

      try {
        xhr.send(formData)
      } catch (e) {
        result.html('<span style="color:red">Thất bại!</span>')
        alert("Tải lên thất bại: " + file.name)
        console.log(e)
        resolve();
      }
    });
  }
</script>

<?php require SITE_FOOTER;
