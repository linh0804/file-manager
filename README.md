# PHP File Manager

PHP File Manager by Izero

Edit by linh && pmtpro

## Báo lỗi

Nếu có lỗi các bạn cứ báo trên repo này (phần `Issues`), hoặc liên hệ mình, mình hứa có thời gian sẽ sửa lỗi sớm nhất có thể!

## Cài đặt nhanh

### Cài đặt bằng file

Tạo một file php, dán code ở [file này](https://raw.githubusercontent.com/ngatngay/file-manager/main/install.txt) vào và chạy nó.

### Cài đặt thủ công (FTP, SSH, File Manager,...)

Hoặc tải file zip ở Release về, giải nén vào 1 thư mục.

### Cài đặt bằng lệnh

Bản full (cài sẵn mấy công cụ như WebDAV):

```bash
mkdir file-manager
cd file-manager

wget https://github.com/ngatngay/file-manager/releases/latest/download/file-manager-full.zip
unzip file-manager-full.zip
```

## Hướng dẫn cài đặt

- Tải code về và giải nén vào 1 thư mục không phải thư mục gốc (`DOCUMENT_ROOT`)!

_Ví dụ:_

Bạn có tên miền `localhost.com` và thư mục web tương ứng là `public_html`, thì phải giải nén vào thư mục con của nó như `public_html/manager` chẳng hạn.

## Tài khoản quản trị mặc định

  * admin
  * 12345

## Một số ảnh đính kèm

![image](screenshot.png)
![image](screenshot1.png)

## Format code

- Để sử dụng chức năng **format** code, vui lòng chạy lệnh sau ở thư mục manager

```bash
composer install
```
