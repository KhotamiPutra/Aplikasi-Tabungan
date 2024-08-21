<?php
session_start();
include 'koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT name FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Ambil data tabungan dari database
$sql_tabungan = "SELECT * FROM tabungan WHERE user_id = ?";
$stmt_tabungan = $conn->prepare($sql_tabungan);
$stmt_tabungan->bind_param('s', $user_id);
$stmt_tabungan->execute();
$result_tabungan = $stmt_tabungan->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    #addTabunganButton {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}
</style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">CelenganKU</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pencapatan-tabungan.php">Pencapaian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
        
    </nav>

    <div class="container mt-4">
        <h3>Hallo, Selamat datang <?php echo htmlspecialchars($user['name']); ?>
        </h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTabunganModal" id="addTabunganButton">Tambah Tabungan</button>

        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
        }
        ?>

        <!-- Modal untuk Menambahkan Tabungan -->
        <div class="modal fade" id="addTabunganModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addTabunganModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addTabunganModalLabel">Mau Menabung Apa?</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="aksi/proses-tabungan.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="judulTabungan" class="form-label">Judul Tabungan</label>
                                <input type="text" class="form-control" id="judulTabungan" name="judul" required>
                            </div>
                            <div class="mb-3">
                                <label for="targetNominal" class="form-label">Target Nominal</label>
                                <input type="number" step="0.01" class="form-control" id="targetNominal" name="target_nominal" required>
                            </div>
                            <div class="mb-3">
                                <label for="targetTanggal" class="form-label">Target Tanggal Tercapai</label>
                                <input type="date" class="form-control" id="targetTanggal" name="target_tanggal" required>
                            </div>
                            <div class="mb-3">
                                <label for="fotoTabungan" class="form-label">Foto Tabungan</label>
                                <input type="file" class="form-control" id="fotoTabungan" name="foto" accept="image/*">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <?php while ($row = $result_tabungan->fetch_assoc()): ?>
                <?php
                // Calculate the remaining days
                $target_date = new DateTime($row['target_tanggal']);
                $today = new DateTime();
                $interval = $today->diff($target_date);
                $remaining_days = $interval->days;

                // Calculate daily, weekly, and monthly target
                $daily_target = $row['target_nominal'] / $remaining_days;
                $weekly_target = $daily_target * 7;
                $monthly_target = $daily_target * 30;

                // Fetch total deposits
                $total_setoran = get_total_setoran($row['id']);

                // Determine status
                $status = ($total_setoran >= $row['target_nominal']) ? 'Tercapai' : 'Belum Tercapai';
                ?>
                <div class="col-md-4 mb-4">
                <div class="card">
    <?php if ($row['foto']): ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['foto']); ?>" class="card-img-top" alt="Foto Tabungan">
    <?php else: ?>
        <img src="path/to/default-image.jpg" class="card-img-top" alt="Default Image">
    <?php endif; ?>
    <div class="card-body">
        <h5 class="card-title"><?php echo htmlspecialchars($row['judul']); ?></h5>
        <p class="card-text">
            Target Nominal: <?php echo number_format($row['target_nominal'], 0, ',', '.'); ?><br>
            Target Tanggal: <?php echo htmlspecialchars($row['target_tanggal']); ?><br>
            Total Setoran: <?php echo number_format($total_setoran, 0, ',', '.'); ?><br>
            Status: <strong><?php echo $status; ?></strong><br>
            <details>
                <?php
                $target_nominal = $row['target_nominal'];
                $days_left = (strtotime($row['target_tanggal']) - time()) / (60 * 60 * 24);
                $weeks_left = $days_left / 7;
                $months_left = $days_left / 30;

                $daily_estimate = $days_left > 0 ? ceil($target_nominal / $days_left) : 0;
                $weekly_estimate = $weeks_left > 0 ? ceil($target_nominal / $weeks_left) : 0;
                $monthly_estimate = $months_left > 0 ? ceil($target_nominal / $months_left) : 0;
                ?>
                <summary>Estimasi</summary>
                <p> Estimasi Uang per Hari: <?php echo number_format($daily_estimate, 0, ',', '.'); ?></p>
                <p>Estimasi Uang per Minggu: <?php echo number_format($weekly_estimate, 0, ',', '.'); ?></p>
                <p>Estimasi Uang per Bulan: <?php echo number_format($monthly_estimate, 0, ',', '.'); ?><br></p>
            </details>
        </p>
        
        <!-- Dropdown Menu -->
        <div class="dropdown position-absolute end-0 bottom-0 p-2">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="edit-tabungan.php?id=<?php echo $row['id']; ?>">Edit</a></li>
                <li><a class="dropdown-item" href="aksi/hapus-tabungan.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus tabungan ini?');">Hapus</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addDepositModal" data-tabungan-id="<?php echo $row['id']; ?>">Tambah Setoran</a></li>
            </ul>
        </div>
    </div>
</div>

                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Modal untuk Menambahkan Setoran -->
    <div class="modal fade" id="addDepositModal" tabindex="-1" aria-labelledby="addDepositModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDepositModalLabel">Tambah Setoran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="aksi/proses-setoran.php">
                        <input type="hidden" name="tabungan_id" id="tabungan_id">
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal Setoran</label>
                            <input type="number" step="0.01" class="form-control" id="nominal" name="nominal" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal Setoran</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set tabungan ID in the modal form
        var addDepositModal = document.getElementById('addDepositModal');
        addDepositModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var tabunganId = button.getAttribute('data-tabungan-id');
            var modalInput = addDepositModal.querySelector('#tabungan_id');
            modalInput.value = tabunganId;
        });
    </script>
</body>

</html>

<?php
function get_total_setoran($tabungan_id) {
    global $conn;
    $sql = "SELECT SUM(nominal) as total FROM menabung WHERE tabungan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $tabungan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ? $row['total'] : 0;
}
?>
