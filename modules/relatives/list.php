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

// Lấy danh sách người thân của các bệnh nhân do bác sĩ quản lý
$sql = "SELECT 
            nt.id,
            nt.moi_quan_he,
            bn.ma_so_benh_an,
            tk_bn.ho_va_ten as ten_benh_nhan,
            tk_nt.ho_va_ten as ten_nguoi_than,
            tk_nt.email as email_nguoi_than,
            tk_nt.so_dien_thoai as sdt_nguoi_than
        FROM nguoi_than nt
        JOIN benh_nhan bn ON nt.benh_nhan_id = bn.id
        JOIN tai_khoan tk_bn ON bn.tai_khoan_id = tk_bn.id
        JOIN tai_khoan tk_nt ON nt.tai_khoan_id = tk_nt.id
        JOIN ho_so_benh_an hsba ON bn.id = hsba.benh_nhan_id
        WHERE hsba.bac_si_id = ?
        GROUP BY nt.id
        ORDER BY bn.ma_so_benh_an, nt.id";

$relatives = $db->fetchAll($sql, [$bac_si_id]);

$pageTitle = 'Danh Sách Người Thân Bệnh Nhân';
include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-people"></i> Danh Sách Người Thân Bệnh Nhân</h5>
            </div>
            <div class="card-body">
                <?php if (empty($relatives)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Chưa có thông tin người thân nào.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã Bệnh Án</th>
                                    <th>Bệnh Nhân</th>
                                    <th>Người Thân</th>
                                    <th>Mối Quan Hệ</th>
                                    <th>Số Điện Thoại</th>
                                    <th>Email</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($relatives as $rel): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($rel['ma_so_benh_an']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($rel['ten_benh_nhan']); ?></td>
                                        <td><?php echo htmlspecialchars($rel['ten_nguoi_than']); ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo htmlspecialchars($rel['moi_quan_he']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="bi bi-telephone"></i>
                                            <a href="tel:<?php echo htmlspecialchars($rel['sdt_nguoi_than']); ?>">
                                                <?php echo htmlspecialchars($rel['sdt_nguoi_than']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if (!empty($rel['email_nguoi_than'])): ?>
                                                <i class="bi bi-envelope"></i>
                                                <a href="mailto:<?php echo htmlspecialchars($rel['email_nguoi_than']); ?>">
                                                    <?php echo htmlspecialchars($rel['email_nguoi_than']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa có</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="../notifications/send.php" class="btn btn-sm btn-primary">
                                                <i class="bi bi-send"></i> Gửi Thông Báo
                                            </a>
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
