<?php
namespace app;

class Update {         
    public function __construct() {    
         
    }
    public function exec($file, $folder, $save){
      if (!file_exists($save)) {
        mkdir($save, 0777, true);
      }
      if (rename($folder . '/' . $file, $save . '/' . $file)) {
        return true;
      } else {
        return false;
      }
    }   
    public function compare_all($dir, $dirOne,$types, $number = 0) {
      $start = $number == 0 ? 0 : ($number + 10);
      $number = ($number + 10);     
      $out = '';
      if(!is_dir($dir)) return 'Chưa có update!';
      $scan = scandir($dir);
      $scan = array_diff($scan, ['.', '..','tmp','config.inc.php','config.db.inc.php']);
      if($types == 1) {
        $notif = '<span style="color:red">[remove]</span>';
      } else {
        $notif = '<span style="color:green">[new]</span>';
      }
      usort($scan, function ($a, $b) use ($dir) {
        $fullPathA = $dir . '/' . $a;
        $fullPathB = $dir . '/' . $b;
        $isDirA = is_dir($fullPathA);
        $isDirB = is_dir($fullPathB);
        if ($isDirA && !$isDirB) {
          return -1;
        } elseif (!$isDirA && $isDirB) {
          return 1;
        } else {
          return strcasecmp($a, $b);
        } 
      });
      if($start != 0 && !count($scan)) return '<span style="padding: 5px 0;margin-left:'. (($start == 0) ? 10 : ($start + 10)) .'px;">Thư mục trống!</span></div></div>';
      foreach($scan as $key=>$keys) {        
        $value = $keys;
        $folder = $file = '';
        $link = $dir . '/'. $value;
        $linkOne = $dirOne . '/'. $value;
        if(is_dir($link)) {
          $check = $this->check_dir($linkOne, $types);
          $isUpdate = '';
          if($types == 2) {
            if($this->file_folder_update($link, $linkOne)) {
              $isUpdate = '<font color="Brown">[new or updated files]</font>';
            }
          }
          $rand = rand(1,10000000000);
          $folder .= '<div style="margin-top:5px;margin-bottom:5px;margin-left:'. (($start == 0) ? 20 : ($start + 10)) .'px;border-left:0.5px dashed #aaa;padding-left: 5px;"><div class="spoilerhead" onclick="var _m=document.querySelector(\'.'. str_replace('.','str',$value) . $rand .'\');var _n=document.querySelector(\'#'. str_replace('.','str',$value) . $rand .'\');if(_n.style.display==\'none\'){_n.style.display=\'\';_m.innerHTML=\'<b>-</b>\';}else{_n.style.display=\'none\';_m.innerHTML=\'<b>+</b>\';}">';
          if($check == 0) // new and remove
            $folder .= '<span><span class="'. str_replace('.','str',$value) . $rand .'"><b>+</b></span> <img src="icon/folder.png" alt="folder" /><b> '.$value.'</b> '. $notif .'</span><br />';
          else if($check == 1) // same
            $folder .= '<span><span class="'. str_replace('.','str',$value) . $rand .'"><b>+</b></span> <img src="icon/folder.png" alt="folder" /><b> '.$value.'</b> '. $isUpdate .'</span><br />';
          else if($check == 2) // add
            $folder .= '<span><span class="'. str_replace('.','str',$value) . $rand .'"><b>+</b></span> <img src="icon/folder.png" alt="folder" /><b> '.$value.'</b> <font color="Purple">[you add]</font></span><br />';
          $folder .= '</div><div id="'.str_replace('.','str',$value) . $rand .'" class="folder spoilerbody" style="display:none">';
        } else {
          $checkbox = '';
          if($types == 2) {
            $checkbox = '<input type="checkbox" name="select[]" value="'. str_replace(__DIR__,'',$dirOne) .'/'. $value .'" /> ';
          }
          $check = $this->check_file($link,$linkOne, $types);
          $icon   = 'unknown';
          $type   = get_format($value);
          $isEdit = false;
          if (in_array($type, FORMATS['other'])) {
            $icon = $type;
          } elseif (in_array($type, FORMATS['text'])) {
            $icon   = $type;
            $isEdit = true;
          } elseif (in_array($type, FORMATS['archive'])) {
            $icon = $type;
          } elseif (in_array($type, FORMATS['audio'])) {
            $icon = $type;
          } elseif (in_array($type, FORMATS['font'])) {
            $icon = $type;
          } elseif (in_array($type, FORMATS['binary'])) {
            $icon = $type;
          } elseif (in_array($type, FORMATS['document'])) {
            $icon = $type;
          } elseif (in_array($type, FORMATS['image'])) {
            $icon = 'image';
          } elseif (in_array(strtolower(strpos($value, '.') !== false ? substr($value, 0, strpos($value, '.')) : $value), FORMATS['source'])) {
            $icon   = strtolower(strpos($value, '.') !== false ? substr($value, 0, strpos($value, '.')) : $value);
            $isEdit = true;
          } elseif (is_format_unknown($value)) {
            $icon   = 'unknown';
            $isEdit = true;
          }
          if($check == 2) // update
            $file .= '<div class="fileNew file" style="padding:5px;padding-left:10px;margin-left:'. (($start == 0) ? 10 : ($start + 10)) .'px;">'. $checkbox .' <img src="icon/mime/' . $icon . '.png" alt="file" /> '.$value.' <font color="blue">[update]</font></div>';
          else if($check == 3) // new and remove
            $file .= '<div class="fileNew file" style="padding:5px;padding-left:10px;margin-left:'. (($start == 0) ? 10 : ($start + 10)) .'px;">'. $checkbox .' <img src="icon/mime/' . $icon . '.png" alt="file" /> '.$value.' '. $notif .'</div>';
          else if($check == 4) // add
            $file .= '<div class="fileNew file" style="padding:5px;padding-left:10px;margin-left:'. (($start == 0) ? 10 : ($start + 10)) .'px;"><img src="icon/mime/' . $icon . '.png" alt="file" /> '.$value.' <font color="Purple">[you add]</font></div>';
          else if($check == 5) // edit
            $file .= '<div class="fileNew file" style="padding:5px;padding-left:10px;margin-left:'. (($start == 0) ? 10 : ($start + 10)) .'px;"><img src="icon/mime/' . $icon . '.png" alt="file" /> '.$value.' <font color="pink">[youedit]</font></div>';
          else if($check == 6) // edit and update
            $file .= '<div class="fileNew file" style="padding:5px;padding-left:10px;margin-left:'. (($start == 0) ? 10 : ($start + 10)) .'px;"><img src="icon/mime/' . $icon . '.png" alt="file" /> '.$value.' <font color="Brown">[youedit and new update]</font></div>';
          else // same
            $file .= '<div class="fileSame file" style="display:none;padding:5px;padding-left:10px;margin-left:'. (($start == 0) ? 10 : ($start + 10)) .'px;"><img src="icon/mime/' . $icon . '.png" alt="file" /> '.$value.'</div>';
        }          
        if($start != 0 && $key === count($scan) - 1) {
          $file .= '<span class="emptys" style="display:none;padding: 5px 0;margin-left:'. (($start == 0) ? 10 : ($start + 10)) .'px;">Thư mục trống!</span>';
          $file .= '</div></div>';  
        }
        $out .= $folder . $file;
        if(is_dir($link)) $out .= $this->compare_all($link, $linkOne, $types, $number);
      }    
      return $out;
    }
    public function file_folder_update($dir, $dirOne) {      
      $scan = scandir($dir);
      $scan = array_diff($scan, ['.', '..','tmp','config.inc.php','config.db.inc.php']);
      foreach($scan as $value) {     
        if(is_dir($dir .'/'. $value)) {
          return $this->file_folder_update($dir.'/'. $value, $dirOne.'/'. $value);
        }   
        if(file_exists($dir .'/'. $value)) {
          $check = $this->check_file($dir .'/'. $value, $dirOne .'/'. $value, 2);
          if($check == 2 || $check == 3) {
            return true;
          }
        }
      }
      return false;
    }
    public function check_file($dir, $dirOne , $type){
      $thisver = is_dir(__DIR__ .'/tmp/thisversion/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER) ? __DIR__ .'/tmp/thisversion/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER . str_replace(__DIR__,'',$dir) : $dir;
      if($type == 2 && is_dir(__DIR__ .'/tmp/thisversion/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER)) $dirOne = __DIR__ .'/tmp/thisversion/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER . str_replace(__DIR__,'',$dirOne);;
      $value = file_get_contents($dir);
      $edit = 0;
      if($type == 1) {
        if(!file_exists($thisver)){
          return 4;
        }
        $valuethis = file_get_contents($thisver);
        if($value != $valuethis) {
          $edit = 2;
        }
      }
      if(!file_exists($dirOne)) {
        return 3;
      }
      $valueOne = file_get_contents($dirOne);
      if($type == 1) {
        if($valuethis != $value && $edit == 2) {
          $edit = 1;
        }
      }
      if($edit == 1) {
        return 5;
      } else if($edit == 2) {
        return 6;
      }
      if($value == $valueOne) {
        return 1;
      }
      return 2;
    }
    public function check_dir($dir,$type){
      if($type == 1 && is_dir(__DIR__ .'/tmp/thisversion/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER)) {
        $thisver = __DIR__ .'/tmp/thisversion/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER . str_replace(__DIR__ .'/tmp/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER,'',$dir);
        if(!is_dir($thisver))
          return 2;
      }
      if(is_dir($dir))
        return 1;
      return 0;
    }
  }

