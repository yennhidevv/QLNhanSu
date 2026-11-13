-- Import từ tab Import ở TRANG CHỦ phpMyAdmin (Server: 127.0.0.1)
-- Không xóa cơ sở dữ liệu QLHocPhan cũ.
CREATE DATABASE IF NOT EXISTS QLNhanSu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE QLNhanSu;
CREATE TABLE IF NOT EXISTS Users (
  admin_id INT(10) NOT NULL AUTO_INCREMENT,
  admin_email VARCHAR(255) NOT NULL,
  admin_password VARCHAR(255) NOT NULL,
  images VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (admin_id),
  UNIQUE KEY uq_admin_email (admin_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Các tài khoản được thêm từ biểu mẫu website; mật khẩu sẽ được băm an toàn.
