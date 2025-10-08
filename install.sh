#!/bin/bash
set -e

file_url="https://static.ngatngay.net/php/file-manager/release.zip"

# Lấy tên thư mục từ tham số $1, mặc định là "manager"
dir_name="${1:-manager}"

# Tạo thư mục và chuyển vào
mkdir -p "$dir_name"
cd ./"$dir_name"

# Tải file và giải nén (ghi đè nếu trùng)
curl -L "$file_url" -o release.zip
unzip -o release.zip
rm -f release.zip

echo "Cài đặt hoàn tất trong thư mục: $dir_name"

