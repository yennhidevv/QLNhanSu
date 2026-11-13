<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';
$search = trim((string)($_GET['q'] ?? ''));
if ($search !== '') {
    $stmt = $pdo->prepare('SELECT admin_id, admin_email, admin_password, images FROM Users WHERE admin_email LIKE ? OR CAST(admin_id AS CHAR) LIKE ? ORDER BY admin_id DESC');
    $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    $rows = $stmt->fetchAll();
} else {
    $rows = $pdo->query('SELECT admin_id, admin_email, admin_password, images FROM Users ORDER BY admin_id DESC')->fetchAll();
}
$total = (int)$pdo->query('SELECT COUNT(*) FROM Users')->fetchColumn();
?>
<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Quản lý nhân sự | QLNhanSu</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="page"><header class="header"><div class="brand"><div class="brand-icon">👥</div><div><strong>QL NHÂN SỰ</strong><small>Quản lý tài khoản người dùng</small></div></div><span class="header-tag">TRANG QUẢN TRỊ</span></header><main>
<section class="intro"><div><div class="eyebrow">DANH SÁCH NGƯỜI DÙNG</div><h1>Quản lý nhân sự</h1><p>Thêm, xem, sửa và xóa người dùng trực tiếp trên cơ sở dữ liệu MySQL.</p></div><a class="btn btn-primary" href="#form-title" id="add-button">＋ Thêm người dùng</a></section>
<section class="stats"><article class="stat"><div class="stat-icon">👥</div><div><small>Tổng người dùng</small><strong><?= $total ?></strong></div></article><article class="stat"><div class="stat-icon blue">🪪</div><div><small>Số người đang hiển thị</small><strong><?= count($rows) ?></strong></div></article><article class="stat"><div class="stat-icon purple">🗃️</div><div><small>Cơ sở dữ liệu</small><strong style="font-size:17px">QLNhanSu</strong></div></article></section>
<?php if (isset($_GET['success'])): ?><div class="notice success"><?= e((string)$_GET['success']) ?></div><?php endif; ?>
<?php if (isset($_GET['error'])): ?><div class="notice error"><?= e((string)$_GET['error']) ?></div><?php endif; ?>
<section class="content-grid"><div class="panel list-panel"><div class="panel-heading"><div><h2>Danh sách người dùng</h2><p>Bảng Users – đầy đủ 4 trường theo đề: admin_id, admin_email, admin_password, images</p></div><span class="counter"><?= count($rows) ?> kết quả</span></div>
<form class="search" method="get"><span>⌕</span><input aria-label="Tìm người dùng" type="search" name="q" value="<?= e($search) ?>" placeholder="Tìm theo ID hoặc email..."><button type="submit">Tìm kiếm</button><?php if ($search !== ''): ?><a href="index.php">Xóa lọc</a><?php endif; ?></form>
<div class="table-wrap"><table><thead><tr><th>admin_id</th><th>admin_email</th><th>admin_password</th><th>images</th><th>Thao tác</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr>
<td><span class="id-label"><?= (int)$r['admin_id'] ?></span></td>
<td><strong class="email-cell"><?= e($r['admin_email']) ?></strong></td>
<td><span class="class-label" title="Mật khẩu được băm, không hiển thị bản gốc">Đã mã hóa</span></td>
<td><?php if ($r['images']): ?><div class="image-cell"><img src="uploads/<?= e($r['images']) ?>" alt="Ảnh của <?= e($r['admin_email']) ?>"><small><?= e($r['images']) ?></small></div><?php else: ?><span class="no-image">Chưa có ảnh</span><?php endif; ?></td>
<td><div class="actions"><button class="icon-btn edit-btn" type="button" title="Sửa" aria-label="Sửa" data-id="<?= (int)$r['admin_id'] ?>" data-email="<?= e($r['admin_email']) ?>">✎</button><form method="post" action="action.php" class="delete-form"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="admin_id" value="<?= (int)$r['admin_id'] ?>"><button class="icon-btn delete-btn" type="submit" title="Xóa" aria-label="Xóa">🗑</button></form></div></td>
</tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="5" class="empty">Chưa có người dùng. Hãy thêm tài khoản đầu tiên!</td></tr><?php endif; ?></tbody></table></div></div>
<aside class="panel form-panel"><div class="panel-heading"><div><h2 id="form-title">Thêm người dùng</h2><p id="form-description">Nhập thông tin người dùng mới</p></div><span class="form-icon">✦</span></div>
<form method="post" action="action.php" enctype="multipart/form-data" id="user-form"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" id="form-action" value="create"><input type="hidden" name="admin_id" id="admin_id" value="">
<div class="auto-id">admin_id: <strong>MySQL tự tăng khi thêm mới</strong></div>
<label for="admin_email">admin_email (Email) <b>*</b></label><input id="admin_email" name="admin_email" type="email" maxlength="255" autocomplete="off" placeholder="vidu@gmail.com" required><span class="hint">Email không được trùng với tài khoản khác</span>
<label for="admin_password">admin_password (Mật khẩu) <b id="password-required">*</b></label><input id="admin_password" name="admin_password" type="password" minlength="6" maxlength="72" autocomplete="new-password" placeholder="Ít nhất 6 ký tự" required><span class="hint" id="password-hint">Mật khẩu được mã hóa khi lưu trong MySQL</span>
<label for="images">images (Hình ảnh người dùng)</label><label for="images" class="upload-area" id="upload-label"><span>📷</span><strong>Nhấn để chọn hình ảnh</strong><small>JPG, PNG, WEBP · Tối đa 3 MB</small><img id="preview" alt="Xem trước" hidden></label><input id="images" name="images" type="file" accept="image/jpeg,image/png,image/webp" class="file-input">
<button class="btn btn-primary submit" id="submit-btn" type="submit">＋ Thêm người dùng</button><button class="btn btn-neutral" id="cancel-btn" type="button" hidden>Hủy chỉnh sửa</button></form></aside></section>
</main><footer>© <?= date('Y') ?> QL Nhân Sự · PHP + MySQL · VS Code + XAMPP</footer></div><script src="assets/script.js"></script></body></html>
