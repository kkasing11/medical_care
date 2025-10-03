<?php
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../includes/functions.php';

requireRole('bac_si');

$db = new Database();
$conn = $db->connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id <= 0 || !in_array($action, ['complete', 'cancel'])) {
    redirect('modules/appointments/list.php');
}

// Xác định trạng thái mới
$new_status = $action === 'complete' ? 'da_kham' : 'da_huy';

// Cập nhật trạng thái
$sql = "UPDATE lich_hen_kham SET trang_thai = ? WHERE id = ?";
if ($db->update($sql, [$new_status, $id])) {
    $_SESSION['success_message'] = 'Cập nhật trạng thái lịch hẹn thành công!';
} else {
    $_SESSION['error_message'] = 'Có lỗi xảy ra khi cập nhật!';
}

redirect('modules/appointments/list.php');
?>
