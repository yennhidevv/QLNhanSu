<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals(csrfToken(), (string)($_POST['csrf'] ?? ''))) {
    http_response_code(403);
    exit('Yêu cầu không hợp lệ.');
}
function saveImage(?array $file): ?string {
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Tải ảnh không thành công.');
    if ($file['size'] > 3 * 1024 * 1024) throw new RuntimeException('Ảnh không được lớn hơn 3 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime]) || !getimagesize($file['tmp_name'])) throw new RuntimeException('Chỉ nhận ảnh JPG, PNG hoặc WEBP.');
    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $filename)) throw new RuntimeException('Không lưu được hình ảnh.');
    return $filename;
}
function deleteImage(?string $name): void {
    if ($name && basename($name) === $name && is_file(__DIR__ . '/uploads/' . $name)) unlink(__DIR__ . '/uploads/' . $name);
}
$action = (string)($_POST['action'] ?? '');
$id = filter_var($_POST['admin_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
try {
    if ($action === 'create' || $action === 'update') {
        $email = trim((string)($_POST['admin_email'] ?? ''));
        $password = (string)($_POST['admin_password'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) throw new RuntimeException('Vui lòng nhập email hợp lệ (tối đa 255 ký tự).');
        if ($action === 'create' && strlen($password) < 6) throw new RuntimeException('Mật khẩu phải có ít nhất 6 ký tự.');
        if ($password !== '' && strlen($password) < 6) throw new RuntimeException('Mật khẩu mới phải có ít nhất 6 ký tự.');
        if (strlen($password) > 72) throw new RuntimeException('Mật khẩu tối đa 72 byte.');
        if ($action === 'create') {
            $image = saveImage($_FILES['images'] ?? null);
            try {
                $stmt = $pdo->prepare('INSERT INTO Users (admin_email, admin_password, images) VALUES (?, ?, ?)');
                $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $image]);
            } catch (Throwable $ex) { deleteImage($image); throw $ex; }
            redirectWith('success', 'Đã thêm người dùng thành công.');
        }
        if (!$id) throw new RuntimeException('ID người dùng không hợp lệ.');
        $stmt = $pdo->prepare('SELECT images FROM Users WHERE admin_id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();
        if (!$old) throw new RuntimeException('Không tìm thấy người dùng cần sửa.');
        $newImage = saveImage($_FILES['images'] ?? null);
        $image = $newImage ?? $old['images'];
        try {
            if ($password !== '') {
                $stmt = $pdo->prepare('UPDATE Users SET admin_email = ?, admin_password = ?, images = ? WHERE admin_id = ?');
                $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $image, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE Users SET admin_email = ?, images = ? WHERE admin_id = ?');
                $stmt->execute([$email, $image, $id]);
            }
        } catch (Throwable $ex) { deleteImage($newImage); throw $ex; }
        if ($newImage) deleteImage($old['images']);
        redirectWith('success', 'Đã cập nhật người dùng thành công.');
    }
    if ($action === 'delete') {
        if (!$id) throw new RuntimeException('ID người dùng không hợp lệ.');
        $stmt = $pdo->prepare('SELECT images FROM Users WHERE admin_id = ?');
        $stmt->execute([$id]);
        $old = $stmt->fetch();
        if (!$old) throw new RuntimeException('Không tìm thấy người dùng.');
        $stmt = $pdo->prepare('DELETE FROM Users WHERE admin_id = ?');
        $stmt->execute([$id]);
        deleteImage($old['images']);
        redirectWith('success', 'Đã xóa người dùng thành công.');
    }
    throw new RuntimeException('Thao tác không được hỗ trợ.');
} catch (PDOException $e) {
    if ($e->getCode() === '23000') redirectWith('error', 'Email đã tồn tại. Hãy dùng email khác.');
    redirectWith('error', 'Lỗi cơ sở dữ liệu. Vui lòng kiểm tra lại.');
} catch (Throwable $e) { redirectWith('error', $e->getMessage()); }
