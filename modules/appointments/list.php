<?php
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../includes/functions.php';

requireRole('bac_si');

$db = new Database();
$conn = $db->connect();

// Lấy ID bác sĩ hiện tại
$sql_bac_si = "SELECT id FROM bac_si WHERE tai_khoan_id = ?";
$bac_si = $db->fetchOne($sql_bac_si, [$_SESSION['user_id']]);
$bac_si_id = $bac_si['id'];

// Lấy danh sách lịch hẹn
$sql = "SELECT 
            lhk.*,
            bn.ma_so_benh_an,
            tk.ho_va_ten as ten_benh_nhan,
            tk.so_dien_thoai
        FROM lich_hen_kham lhk
        JOIN benh_nhan bn ON lhk.benh_nhan_id = bn.id
        JOIN tai_khoan tk ON bn.tai_khoan_id = tk.id
        WHERE lhk.bac_si_id = ?
        ORDER BY lhk.thoi_gian_hen DESC";

$appointments = $db->fetchAll($sql, [$bac_si_id]);

$pageTitle = 'Quản Lý Lịch Hẹn Khám';
include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Danh Sách Lịch Hẹn Khám</h5>
            </div>
            <div class="card-body">
                <?php if (empty($appointments)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Chưa có lịch hẹn nào.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã Bệnh Án</th>
                                    <th>Bệnh Nhân</th>
                                    <th>Số Điện Thoại</th>
                                    <th>Thời Gian Hẹn</th>
                                    <th>Trạng Thái</th>
                                    <th>Ghi Chú</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $apt): ?>
                                    <?php
                                    $status_badge = [
                                        'da_dat' => ['badge' => 'bg-warning', 'text' => 'Đã Đặt'],
                                        'da_kham' => ['badge' => 'bg-success', 'text' => 'Đã Khám'],
                                        'da_huy' => ['badge' => 'bg-danger', 'text' => 'Đã Hủy']
                                    ];
                                    $status = $status_badge[$apt['trang_thai']] ?? ['badge' => 'bg-secondary', 'text' => 'Không xác định'];
                                    
                                    // Kiểm tra quá hạn
                                    $is_past = strtotime($apt['thoi_gian_hen']) < time();
                                    ?>
                                    <tr class="<?php echo $is_past && $apt['trang_thai'] == 'da_dat' ? 'table-danger' : ''; ?>">
                                        <td><strong><?php echo htmlspecialchars($apt['ma_so_benh_an']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($apt['ten_benh_nhan']); ?></td>
                                        <td><?php echo htmlspecialchars($apt['so_dien_thoai']); ?></td>
                                        <td>
                                            <?php echo formatDateTime($apt['thoi_gian_hen'], 'd/m/Y H:i'); ?>
                                            <?php if ($is_past && $apt['trang_thai'] == 'da_dat'): ?>
                                                <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Quá hạn</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $status['badge']; ?>">
                                                <?php echo $status['text']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($apt['ghi_chu']); ?></td>
                                        <td>
                                            <?php if ($apt['trang_thai'] == 'da_dat'): ?>
                                                <a href="update-appointment.php?id=<?php echo $apt['id']; ?>&action=complete" 
                                                   class="btn btn-sm btn-success"
                                                   onclick="return confirm('Xác nhận đã khám?')">
                                                    <i class="bi bi-check-circle"></i> Đã Khám
                                                </a>
                                                <a href="update-appointment.php?id=<?php echo $apt['id']; ?>&action=cancel" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Xác nhận hủy lịch hẹn?')">
                                                    <i class="bi bi-x-circle"></i> Hủy
                                                </a>
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
