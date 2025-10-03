<?php
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../includes/functions.php';

requireRole('bac_si');

$db = new Database();
$conn = $db->connect();

$success = '';
$error = '';

// Lấy danh sách bệnh nhân của bác sĩ hiện tại
$sql_bac_si = "SELECT id FROM bac_si WHERE tai_khoan_id = ?";
$bac_si = $db->fetchOne($sql_bac_si, [$_SESSION['user_id']]);
$bac_si_id = $bac_si['id'];

$sql_benh_nhan = "SELECT DISTINCT 
                    bn.id,
                    bn.ma_so_benh_an,
                    tk.ho_va_ten
                  FROM benh_nhan bn
                  JOIN tai_khoan tk ON bn.tai_khoan_id = tk.id
                  JOIN ho_so_benh_an hsba ON bn.id = hsba.benh_nhan_id
                  WHERE hsba.bac_si_id = ?
                  ORDER BY tk.ho_va_ten";

$benh_nhan_list = $db->fetchAll($sql_benh_nhan, [$bac_si_id]);

// Xử lý gửi thông báo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $benh_nhan_id = (int)$_POST['benh_nhan_id'];
    $noi_dung = sanitize($_POST['noi_dung']);
    $canh_bao = isset($_POST['canh_bao']) ? 1 : 0;
    $gui_email = isset($_POST['gui_email']) ? 1 : 0;
    
    if ($benh_nhan_id <= 0 || empty($noi_dung)) {
        $error = 'Vui lòng chọn bệnh nhân và nhập nội dung thông báo!';
    } else {
        // Lấy danh sách người thân của bệnh nhân
        $sql_nguoi_than = "SELECT nt.tai_khoan_id, tk.email, tk.ho_va_ten
                           FROM nguoi_than nt
                           JOIN tai_khoan tk ON nt.tai_khoan_id = tk.id
                           WHERE nt.benh_nhan_id = ?";
        $nguoi_than = $db->fetchAll($sql_nguoi_than, [$benh_nhan_id]);
        
        if (empty($nguoi_than)) {
            $error = 'Bệnh nhân này chưa có người thân trong hệ thống!';
        } else {
            $sent_count = 0;
            
            foreach ($nguoi_than as $nt) {
                // Thêm thông báo vào database
                $sql_insert = "INSERT INTO thong_bao (nguoi_nhan_id, noi_dung, canh_bao)
                               VALUES (?, ?, ?)";
                
                if ($db->insert($sql_insert, [$nt['tai_khoan_id'], $noi_dung, $canh_bao])) {
                    $sent_count++;
                    
                    // Gửi email nếu được chọn
                    if ($gui_email && !empty($nt['email'])) {
                        // TODO: Implement email sending
                        // sendEmail($nt['email'], $noi_dung, $canh_bao);
                    }
                }
            }
            
            if ($sent_count > 0) {
                $success = "Đã gửi thông báo thành công cho $sent_count người thân!";
            } else {
                $error = 'Có lỗi xảy ra khi gửi thông báo!';
            }
        }
    }
}

$pageTitle = 'Gửi Thông Báo Cho Người Nhà';
include '../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-bell"></i> Gửi Thông Báo Cho Người Nhà Bệnh Nhân</h5>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <?php echo showAlert($success, 'success'); ?>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <?php echo showAlert($error, 'error'); ?>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person"></i> Chọn Bệnh Nhân *</label>
                        <select name="benh_nhan_id" class="form-select" required>
                            <option value="">-- Chọn bệnh nhân --</option>
                            <?php foreach ($benh_nhan_list as $bn): ?>
                                <option value="<?php echo $bn['id']; ?>">
                                    <?php echo htmlspecialchars($bn['ma_so_benh_an'] . ' - ' . $bn['ho_va_ten']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-chat-left-text"></i> Nội Dung Thông Báo *</label>
                        <textarea name="noi_dung" class="form-control" rows="6" required
                                  placeholder="Nhập nội dung thông báo cho người nhà bệnh nhân..."></textarea>
                        <small class="text-muted">Ví dụ: Bệnh nhân cần chuẩn bị nhập viện vào ngày...</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="canh_bao" value="1" id="canhBao">
                            <label class="form-check-label text-danger" for="canhBao">
                                <i class="bi bi-exclamation-triangle"></i> <strong>Thông báo khẩn cấp</strong> (đánh dấu là cảnh báo quan trọng)
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="gui_email" value="1" id="guiEmail" checked>
                            <label class="form-check-label" for="guiEmail">
                                <i class="bi bi-envelope"></i> Gửi kèm email thông báo
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send"></i> Gửi Thông Báo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Lưu Ý</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success"></i> 
                        Thông báo sẽ được gửi đến <strong>tất cả người thân</strong> của bệnh nhân
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success"></i> 
                        Người nhà sẽ nhận thông báo qua hệ thống
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success"></i> 
                        Nếu chọn gửi email, thông báo cũng được gửi qua email
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-exclamation-triangle text-warning"></i> 
                        Chỉ đánh dấu <strong>"Cảnh báo"</strong> cho thông báo thực sự khẩn cấp
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-warning">
                <h6 class="mb-0"><i class="bi bi-lightbulb"></i> Mẫu Thông Báo</h6>
            </div>
            <div class="card-body">
                <small>
                    <strong>Nhắc nhở tái khám:</strong><br>
                    "Kính gửi gia đình bệnh nhân, lịch tái khám tiếp theo là ngày [ngày/tháng] lúc [giờ]. Vui lòng đến đúng giờ."
                    <hr>
                    <strong>Cần chuẩn bị:</strong><br>
                    "Bệnh nhân cần chuẩn bị nhập viện vào [ngày]. Vui lòng mang theo [danh sách đồ cần thiết]."
                </small>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
