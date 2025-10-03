<?php
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../includes/functions.php';

requireRole('bac_si');

$db = new Database();
$conn = $db->connect();

// Lấy danh sách thông báo (của người nhà các bệnh nhân do bác sĩ quản lý)
$sql_bac_si = "SELECT id FROM bac_si WHERE tai_khoan_id = ?";
$bac_si = $db->fetchOne($sql_bac_si, [$_SESSION['user_id']]);
$bac_si_id = $bac_si['id'];

$sql = "SELECT 
            tb.*,
            tk.ho_va_ten as nguoi_nhan_ten,
            tk.email as nguoi_nhan_email
        FROM thong_bao tb
        JOIN tai_khoan tk ON tb.nguoi_nhan_id = tk.id
        WHERE tk.vai_tro = 'nguoi_nha'
        ORDER BY tb.ngay_tao DESC
        LIMIT 50";

$notifications = $db->fetchAll($sql);

$pageTitle = 'Danh Sách Thông Báo';
include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="mb-3">
            <a href="send.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Gửi Thông Báo Mới
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-bell"></i> Danh Sách Thông Báo Đã Gửi</h5>
            </div>
            <div class="card-body">
                <?php if (empty($notifications)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Chưa có thông báo nào.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Ngày Gửi</th>
                                    <th>Người Nhận</th>
                                    <th>Nội Dung</th>
                                    <th>Loại</th>
                                    <th>Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notifications as $notif): ?>
                                    <tr>
                                        <td><?php echo formatDateTime($notif['ngay_tao']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($notif['nguoi_nhan_ten']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($notif['nguoi_nhan_email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars(substr($notif['noi_dung'], 0, 100)); ?>...</td>
                                        <td>
                                            <?php if ($notif['canh_bao']): ?>
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-exclamation-triangle"></i> Cảnh Báo
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-info">Thông Thường</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($notif['da_doc']): ?>
                                                <span class="badge bg-success">Đã Đọc</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Chưa Đọc</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
