<?php
require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

// Nếu là bác sĩ, lấy thống kê
$stats = [];
if (isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'bac_si') {
    $sql_bac_si = "SELECT id FROM bac_si WHERE tai_khoan_id = ?";
    $bac_si = $db->fetchOne($sql_bac_si, [$_SESSION['user_id']]);
    $bac_si_id = $bac_si['id'];
    
    // Tổng số bệnh án
    $sql_count_records = "SELECT COUNT(*) as total FROM ho_so_benh_an WHERE bac_si_id = ?";
    $result = $db->fetchOne($sql_count_records, [$bac_si_id]);
    $stats['total_records'] = $result['total'];
    
    // Số lịch hẹn đã đặt
    $sql_count_appointments = "SELECT COUNT(*) as total FROM lich_hen_kham 
                               WHERE bac_si_id = ? AND trang_thai = 'da_dat'";
    $result = $db->fetchOne($sql_count_appointments, [$bac_si_id]);
    $stats['pending_appointments'] = $result['total'];
    
    // Lịch hẹn sắp tới
    $sql_upcoming = "SELECT 
                        lhk.*,
                        bn.ma_so_benh_an,
                        tk.ho_va_ten as ten_benh_nhan,
                        tk.so_dien_thoai
                     FROM lich_hen_kham lhk
                     JOIN benh_nhan bn ON lhk.benh_nhan_id = bn.id
                     JOIN tai_khoan tk ON bn.tai_khoan_id = tk.id
                     WHERE lhk.bac_si_id = ? 
                     AND lhk.trang_thai = 'da_dat'
                     AND lhk.thoi_gian_hen >= NOW()
                     ORDER BY lhk.thoi_gian_hen ASC
                     LIMIT 5";
    $stats['upcoming_appointments'] = $db->fetchAll($sql_upcoming, [$bac_si_id]);
    
    // Hoạt động gần đây
    $sql_recent = "SELECT 
                    tt.*,
                    tk.ho_va_ten as nguoi_cap_nhat_ten,
                    bn.ma_so_benh_an,
                    tk_bn.ho_va_ten as ten_benh_nhan
                   FROM tien_trinh_dieu_tri tt
                   JOIN tai_khoan tk ON tt.nguoi_cap_nhat = tk.id
                   JOIN ho_so_benh_an hsba ON tt.ho_so_benh_an_id = hsba.id
                   JOIN benh_nhan bn ON hsba.benh_nhan_id = bn.id
                   JOIN tai_khoan tk_bn ON bn.tai_khoan_id = tk_bn.id
                   WHERE hsba.bac_si_id = ?
                   ORDER BY tt.thoi_gian_cap_nhat DESC
                   LIMIT 10";
    $stats['recent_activities'] = $db->fetchAll($sql_recent, [$bac_si_id]);
}

$pageTitle = 'Trang Chủ';
include 'includes/header.php';
?>

<?php if (isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'bac_si'): ?>
    <!-- Dashboard cho Bác sĩ -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-file-medical text-primary" style="font-size: 3rem;"></i>
                    <h3 class="mt-3"><?php echo $stats['total_records']; ?></h3>
                    <p class="text-muted">Tổng Hồ Sơ Bệnh Án</p>
                    <a href="modules/medical-records/list.php" class="btn btn-sm btn-outline-primary">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-3"><?php echo $stats['pending_appointments']; ?></h3>
                    <p class="text-muted">Lịch Hẹn Đang Chờ</p>
                    <a href="modules/appointments/list.php" class="btn btn-sm btn-outline-success">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Lịch Hẹn Sắp Tới</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($stats['upcoming_appointments'])): ?>
                        <p class="text-muted">Không có lịch hẹn sắp tới.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($stats['upcoming_appointments'] as $apt): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($apt['ten_benh_nhan']); ?></h6>
                                        <small><?php echo formatDateTime($apt['thoi_gian_hen']); ?></small>
                                    </div>
                                    <p class="mb-1">
                                        <span class="badge bg-info"><?php echo htmlspecialchars($apt['ma_so_benh_an']); ?></span>
                                        <?php if ($apt['ghi_chu']): ?>
                                            - <?php echo htmlspecialchars($apt['ghi_chu']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <small><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($apt['so_dien_thoai']); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="modules/appointments/list.php" class="btn btn-sm btn-primary">Xem Tất Cả</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-activity"></i> Hoạt Động Gần Đây</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($stats['recent_activities'])): ?>
                        <p class="text-muted">Chưa có hoạt động nào.</p>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($stats['recent_activities'] as $activity): ?>
                                <div class="border-start border-3 border-success ps-3 mb-3">
                                    <small class="text-muted">
                                        <?php echo formatDateTime($activity['thoi_gian_cap_nhat']); ?>
                                    </small>
                                    <p class="mb-0">
                                        <strong><?php echo htmlspecialchars($activity['ten_benh_nhan']); ?></strong>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($activity['ma_so_benh_an']); ?></span>
                                    </p>
                                    <small><?php echo htmlspecialchars(substr($activity['ghi_chu'], 0, 100)); ?>...</small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="modules/medical-records/list.php" class="btn btn-sm btn-success">Xem Tất Cả</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Truy Cập Nhanh</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <a href="modules/medical-records/list.php" class="btn btn-outline-primary btn-lg w-100">
                                <i class="bi bi-file-medical d-block mb-2" style="font-size: 2rem;"></i>
                                Hồ Sơ Bệnh Án
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="modules/appointments/list.php" class="btn btn-outline-success btn-lg w-100">
                                <i class="bi bi-calendar-check d-block mb-2" style="font-size: 2rem;"></i>
                                Lịch Hẹn Khám
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="modules/notifications/send.php" class="btn btn-outline-warning btn-lg w-100">
                                <i class="bi bi-bell d-block mb-2" style="font-size: 2rem;"></i>
                                Gửi Thông Báo
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="modules/relatives/list.php" class="btn btn-outline-info btn-lg w-100">
                                <i class="bi bi-people d-block mb-2" style="font-size: 2rem;"></i>
                                Người Thân
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Dashboard cho vai trò khác -->
    <div class="jumbotron text-center">
        <h1 class="display-4">Chào mừng đến với <?php echo SITE_NAME; ?>!</h1>
        <p class="lead">Hệ thống quản lý bệnh án điện tử</p>
        <hr class="my-4">
        <p>Vai trò của bạn: <strong><?php echo isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : 'Chưa xác định'; ?></strong></p>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
