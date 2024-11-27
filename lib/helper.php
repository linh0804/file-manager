<?php

namespace NgatNgay\Helper;

// array helper pro max

class Arr
{
    public static function multipleUploadToSimple(array $fileUpload): array
    {
        $files = [];

        foreach ($fileUpload as $k => $l) {
            foreach ($l as $i => $v) {
                if (!array_key_exists($i, $files)) {
                    $files[$i] = [];
                }

                $files[$i][$k] = $v;
            }
        }

        return $files;
    }

    public static function getFromPage(array $data, int $page, int $perPage = 10): array
    {
        $result = [];
        $total = count($data);
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage;

        if ($start < 0) {
            $start = 0;
        }

        if ($end > $total) {
            $end = $total;
        }

        for ($start; $start < $end; $start++) {
            $result[] = $data[$start];
        }

        return $result;
    }

    public static function toFile($file, $arr) {
        return file_put_contents(
            $file,
            '<?php return ' . var_export($arr, true) . ';'
        );
    }
}

?><?php

namespace NgatNgay\Helper;

class Date
{
    public static function now()
    {
        return time();
    }

    public static function startDay($day)
    {
        return mktime(00, 00, 00, date('n'), $day);
    }

    public static function startMonth($month)
    {
        return mktime(00, 00, 00, $month);
    }

    public static function startYear()
    {
    }


    public static function currentDay()
    {
        return date('d');
    }

    public static function currentMonth()
    {
        return date('m');
    }

    public static function currentYear()
    {
        date('Y');
    }

    public static function displayAgo($time)
    {
        $time  = intval($time);
        $times = time() - $time;

        if ($times < 1) {
            $t = 'Vừa xong';
        } elseif ($times < 60) {
            $t = $times . ' giây trước';
        } elseif ($times < 3600) {
            $t = round($times / 60) . ' phút trước';
        } elseif ($times < 86400) {
            $t = round($times / 3600) . ' giờ trước';
        } elseif ($times < 2_592_000) {
            $t = round($times / 86400) . ' ngày trước';
        } elseif ($times < 31_536_000) {
            $t = round($times / 2_592_000) . ' tháng trước';
        } else {
            $t = round($times / 31_536_000) . ' năm trước';
        }

        return $t;
    }
}

?><?php

namespace NgatNgay\Helper;

// file system
class FS
{
    /*
     * file, file1, file2...
     */
    function nameIncrement(string $file_name_body, string $file_ext): string
    {
        $i = 1;
        $file_exists = true;

        do {
            $file_save = $file_name_body . $i . '.' . $file_ext;

            if (!file_exists($file_save)) {
                $file_exists = false;
            }

            $i++;
        } while ($file_exists);

        return $file_save;
    }

    public static function getExtension(string $name): string
    {
        $name = strval($name);

        preg_match('/\.([^\.]*$)/', $name, $extension);

        if (is_array($extension) && sizeof($extension) > 0) {
            return strtolower($extension[1]);
        }

        return '';
    }

    /**
     * @param $fileSize string
     * @return string
     */
    public static function readableSize($fileSize)
    {
        $size = floatval($fileSize);

        if ($size < 1024) {
            $s = $size . ' B';
        } elseif ($size < 1048576) {
            $s = round($size / 1024, 2) . ' KB';
        } elseif ($size < 1073741824) {
            $s = round($size / 1048576, 2) . ' MB';
        } elseif ($size < 1099511627776) {
            $s = round($size / 1073741824, 2) . ' GB';
        } elseif ($size < 1125899906842624) {
            $s = round($size / 1099511627776, 2) . ' TB';
        } elseif ($size < 1152921504606846976) {
            $s = round($size / 1125899906842624, 2) . ' PB';
        } elseif ($size < 1.1805916207174E+21) {
            $s = round($size / 1152921504606846976, 2) . ' EB';
        } elseif ($size < 1.2089258196146E+24) {
            $s = round($size / 1.1805916207174E+21, 2) . ' ZB';
        } else {
            $s = round($size / 1.2089258196146E+24, 2) . ' YB';
        }

        return $s;
    }

    public static function remove($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public static function removeDir($dir, $remove_this = true)
    {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? $this->removeDir($obj) : $this->remove($obj);
            }
        }

        if ($remove_this) {
            rmdir($dir);
        }
    }
}

?><?php

namespace NgatNgay\Helper;

// best string helper

class Str
{
    /**
     * @param string $string
     * @return bool
     */
    public static function empty($string)
    {
        return strlen($string) === 0;
    }

    public static function wordCut(string $string, int $words = 35, string $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $string, $matches);

        if (!isset($matches[0]) || self::length($string) === self::length($matches[0])) {
            return $string;
        }

        return rtrim($matches[0]) . $end;
    }

    public static function nl2br(string $str): string
    {
        return str_replace(PHP_EOL, '<br />', $str);
    }

    public static function br2nl(string $str): string
    {
        return preg_replace('#<br\s*/?>#i', PHP_EOL, $str);
    }

    public static function length(string $str): int
    {
        return mb_strlen($str);
    }

    /**
     * Chuyển đổi tiếng Việt sang tiếng Anh
     * @param string $str
     * @return string
     */
    public static function vn2en($str)
    {
        $unicode = [
            'a' => '/á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ/',
            'd' => '/đ/',
            'e' => '/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/',
            'i' => '/í|ì|ỉ|ĩ|ị/',
            'o' => '/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/',
            'u' => '/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/',
            'y' => '/ý|ỳ|ỷ|ỹ|ỵ/',
            'A' => '/Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ằ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ/',
            'D' => '/Đ/',
            'E' => '/É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ/',
            'I' => '/Í|Ì|Ỉ|Ĩ|Ị/',
            'O' => '/Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ/',
            'U' => '/Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự/',
            'Y' => '/Ý|Ỳ|Ỷ|Ỹ|Ỵ/'
        ];

        return preg_replace(array_values($unicode), array_keys($unicode), $str);
    }
}

?><?php

namespace NgatNgay\Helper;

interface IResponse
{
    public function data($data);
    public function status($status);
    public function headers($headers);
    public function json();
    public function send();
}
function response($data = null, $status = 200, $headers = []): IResponse
{
    return new class ($data, $status, $headers) implements IResponse {      
        public function __construct(
        private $data,
        private $status,
        private array $headers = []
        ) {}
        
        public function data($data) {
            $this->data = $data;
            return $this;
}
        public function status($status) {
            $this->status = $status;
            return $this;
}
        public function json() {
            $this->headers += ['Content-Type: application/json'];
            
            if (is_array($this->data)) {
            $this->data = json_encode($this->data);
            }
            return $this;
       }

        public function headers($headers)
        {
            $this->headers = $headers;
            return $this;
        }
        
        public function send() {
            @ob_end_clean();
            
            if (is_array($this->data)) {
                $this->json();
            }
            
            http_response_code($this->status);
            
            $this->headers = array_unique($this->headers);
            foreach ($this->headers as $header) {
                header($header);
            }
                 
            exit($this->data);
        }
    };
}

?>