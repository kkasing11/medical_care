<?php
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../includes/functions.php';

requireRole('bac_si');

$db = new Database();
$conn = $db->connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success = '';
$error = '';

if ($id <= 0) {
    redirect('modules/medical-records/list.php');
}

// Lấy thông tin hồ sơ bệnh án
$sql = "SELECT hsba.*, bn.ma_so_benh_an, tk.ho_va_ten as ten_benh_nhan
        FROM ho_so_benh_an hsba
        JOIN benh_nhan bn ON hsba.benh_nhan_id = bn.id
        JOIN tai_khoan tk ON bn.tai_khoan_id = tk.id
        WHERE hsba.id = ?";

$record = $db->fetchOne($sql, [$id]);

if (!$record) {
    redirect('modules/medical-records/list.php');
}

// Xử lý cập nhật tiến trình
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ghi_chu = sanitize($_POST['ghi_chu']);
    $loai_cap_nhat = $_POST['loai_cap_nhat'];
    
    if (empty($ghi_chu)) {
        $error = 'Vui lòng nhập nội dung cập nhật!';
    } else {
        // Thêm tiến trình điều trị
        $sql_insert = "INSERT INTO tien_trinh_dieu_tri (ho_so_benh_an_id, nguoi_cap_nhat, ghi_chu)
                       VALUES (?, ?, ?)";
        
        if ($db->insert($sql_insert, [$id, $_SESSION['user_id'], $ghi_chu])) {
            
            // Nếu chọn gửi thông báo cho người nhà
            if (isset($_POST['gui_thong_bao']) && $_POST['gui_thong_bao'] == '1') {
                // Lấy danh sách người thân
                $sql_nguoi_than = "SELECT nt.tai_khoan_id, tk.email, tk.ho_va_ten
                                   FROM nguoi_than nt
                                   JOIN tai_khoan tk ON nt.tai_khoan_id = tk.id
                                   WHERE nt.benh_nhan_id = ?";
                $nguoi_than = $db->fetchAll($sql_nguoi_than, [$record['benh_nhan_id']]);
                
                foreach ($nguoi_than as $nt) {
                    // Thêm thông báo vào database
                    $noi_dung_thong_bao = "Cập nhật tiến trình điều trị cho bệnh nhân " . $record['ten_benh_nhan'] . ": " . $ghi_chu;
                    $sql_thong_bao = "INSERT INTO thong_bao (nguoi_nhan_id, noi_dung, canh_bao)
                                     VALUES (?, ?, ?)";
                    $db->insert($sql_thong_bao, [$nt['tai_khoan_id'], $noi_dung_thong_bao, 0]);
                    
                    // Gửi email (sẽ implement sau)
                    if (!empty($nt['email'])) {
                        // TODO: Gửi email qua Gmail SMTP
                    }
                }
            }
            
            $success = 'Cập nhật tiến trình điều trị thành công!';
            
            // Redirect về trang xem sau 2 giây
            header("refresh:2;url=view.php?id=$id");
        } else {
            $error = 'Có lỗi xảy ra khi cập nhật!';
        }
    }
}

$pageTitle = 'Cập Nhật Tiến Trình Điều Trị';
include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="mb-3">
            <a href="view.php?id=<?php echo $id; ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay Lại
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Cập Nhật Tiến Trình Điều Trị</h5>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <?php echo showAlert($success, 'success'); ?>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <?php echo showAlert($error, 'error'); ?>
                <?php endif; ?>
                
                <div class="alert alert-info">
                    <strong>Bệnh án:</strong> <?php echo htmlspecialchars($record['ma_so_benh_an']); ?><br>
                    <strong>Bệnh nhân:</strong> <?php echo htmlspecialchars($record['ten_benh_nhan']); ?>
                </div>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-card-text"></i> Loại cập nhật</label>
                        <select name="loai_cap_nhat" class="form-select">
                            <option value="tien_trinh">Tiến trình điều trị</option>
                            <option value="thuoc">Thay đổi thuốc</option>
                            <option value="xet_nghiem">Kết quả xét nghiệm</option>
                            <option value="khac">Khác</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-chat-left-text"></i> Nội dung cập nhật *</label>
                        <textarea name="ghi_chu" class="form-control" rows="6" required 
                                  placeholder="Nhập nội dung cập nhật tiến trình điều trị..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="gui_thong_bao" value="1" id="guiThongBao" checked>
                            <label class="form-check-label" for="guiThongBao">
                                <i class="bi bi-bell"></i> Gửi thông báo cho người nhà (qua hệ thống và email)
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu Cập Nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Hướng Dẫn</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Nhập chi tiết về tình trạng bệnh nhân</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Ghi nhận các thay đổi quan trọng</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Cập nhật kết quả điều trị</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Người nhà sẽ nhận thông báo ngay</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
