# QL NHÂN SỰ – BẢN ĐÚNG ĐỀ KIỂM TRA

## Đối chiếu đề gốc
- CSDL MySQL: `QLNhanSu`
- Bảng: `Users` (`users` được phpMyAdmin hiển thị chữ thường trên Windows).
- CHÍNH XÁC 4 trường trong bảng: `admin_id` INT(10) AUTO_INCREMENT PRIMARY KEY, `admin_email` VARCHAR(255), `admin_password` VARCHAR(255), `images` VARCHAR(255).
- Chức năng: **Thêm** (4 điểm), **hiển thị frontend** (4 điểm), **responsive** (1 điểm), **sửa + xóa** ở trang quản trị (1 điểm).
- Không thêm trường tên nhân sự vì đề không có trường tên. `admin_id` là số tự tăng.
- Giao diện danh sách hiển thị đầy đủ 4 tiêu đề cột, thêm cột Thao tác để sửa/xóa. Mật khẩu chỉ báo 'Đã mã hóa', không lộ giá trị băm hoặc mật khẩu gốc.

## Cập nhật từ project QLNhanSu trước (KHÔNG MẤT DỮ LIỆU)
1. Mở XAMPP, bật Apache + MySQL.
2. Đóng website đang chạy; đổi tên thư mục `C:\xampp\htdocs\QLNhanSu` cũ thành `QLNhanSu_backup`. Nếu trên máy đang bị lồng thư mục `QLNhanSu\QLNhanSu`, đổi tên thư mục **ngoài cùng** thành `QLNhanSu_backup`.
3. Giải nén ZIP này, chép **thư mục QLNhanSu** bên trong ZIP vào `C:\xampp\htdocs\`. Đảm bảo `C:\xampp\htdocs\QLNhanSu\index.php` tồn tại.
4. **KHÔNG cần Import SQL lại** nếu trong phpMyAdmin đã có `qlnhansu > users` đủ 4 trường. Dữ liệu MySQL cũ vẫn còn. SQL `database.sql` chỉ dùng khi chưa có CSDL.
5. Vào `http://localhost/QLNhanSu/` (Ctrl+F5 để tải lại nếu cần).
6. Mở thư mục này trong VS Code (File > Open Folder).

## Kiểm tra bài
1. Trong phpMyAdmin > qlnhansu > users > Structure: chụp 4 trường.
2. Trên web, điền Email, mật khẩu, chọn ảnh rồi Thêm người dùng. ID tự tăng.
3. Trở lại phpMyAdmin > users > Browse, tải lại để thấy bản ghi mới.
4. Trên web, thử nút bút chì (Sửa), nút thùng rác (Xóa); kiểm tra thay đổi ở phpMyAdmin.
5. Thu nhỏ cửa sổ hoặc mở F12 > thiết bị di động để kiểm tra responsive.
6. Chụp màn hình hoặc quay video có cả CSDL theo đề.

Mật khẩu được băm bằng `password_hash`, chỉ dùng app local để demo (chưa có xác thực đăng nhập).
