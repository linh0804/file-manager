<?php

use nightmare\http\response;

defined('ACCESS') or exit;

$site_title = 'Tải lên tập tin';

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
    
    (new response($data))->send();
}

$action = action_link('file', ['act' => 'upload', 'path' => $curr_path]);



require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

echo '<div class="list">
  <span>' . file_print_path($curr_path, true) . '</span><hr/>
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
  let uploading = false;

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

  $('#buttonUpload').click(async function (e) {
    e.preventDefault()

    if (!files.length) {
      alert('Chưa chọn file!');
      return;
    }

    if (uploading) {
        alert("Đang upload!")
        return
    }
    
    const uploadItems = [];
    
    $('.fileUpload').each(function() {
        let e = $(this);
        let id = e.data('id');
        
        if (files[id]) {
            uploadItems.push({
                file: files[id],
                result: e.find('.result')
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
