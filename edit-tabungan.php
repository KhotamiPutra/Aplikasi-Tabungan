<?php
session_start();
include 'koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get tabungan ID from query string
if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$tabungan_id = $_GET['id'];

// Fetch tabungan data from database
$sql = "SELECT * FROM tabungan WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $tabungan_id);
$stmt->execute();
$result = $stmt->get_result();
$tabungan = $result->fetch_assoc();

// Check if data exists
if (!$tabungan) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tabungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <a class="nav-link" href="home.php">Home</a>
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
        <h1>Edit Tabungan</h1>
        <form method="POST" action="aksi/proses-edit-tabungan.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($tabungan['id']); ?>">

            <div class="mb-3">
                <label for="judulTabungan" class="form-label">Judul Tabungan</label>
                <input type="text" class="form-control" id="judulTabungan" name="judul" value="<?php echo htmlspecialchars($tabungan['judul']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="targetNominal" class="form-label">Target Nominal</label>
                <input type="number" step="0.01" class="form-control" id="targetNominal" name="target_nominal" value="<?php echo htmlspecialchars($tabungan['target_nominal']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="targetTanggal" class="form-label">Target Tanggal Tercapai</label>
                <input type="date" class="form-control" id="targetTanggal" name="target_tanggal" value="<?php echo htmlspecialchars($tabungan['target_tanggal']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="fotoTabungan" class="form-label">Foto Tabungan</label>
                <?php if ($tabungan['foto']): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($tabungan['foto']); ?>" class="img-thumbnail mb-3" alt="Foto Tabungan">
                <?php endif; ?>
                <input type="file" class="form-control" id="fotoTabungan" name="foto" accept="image/*"  value="<?php echo htmlspecialchars($tabungan['foto']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="home.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
