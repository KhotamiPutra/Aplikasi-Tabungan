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
$sql_user = "SELECT name FROM users WHERE id='$user_id'";
$result_user = $conn->query($sql_user);
$user = $result_user->fetch_assoc();

// Ambil data tabungan yang sudah tercapai
$sql_tabungan = "SELECT * FROM tabungan WHERE user_id = ? AND target_nominal <= (SELECT SUM(nominal) FROM menabung WHERE tabungan_id = tabungan.id)";
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
    <title>Tabungan Tercapai</title>
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
            <?php echo htmlspecialchars($user['name']); ?>
        </div>
        
    </nav>

    <div class="container mt-4">

        <div class="row mt-4">
            <?php while ($row = $result_tabungan->fetch_assoc()): ?>
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
                                Total Setoran: <?php echo number_format(get_total_setoran($row['id']), 0, ',', '.'); ?><br>
                            </p>
                            <a href="edit-tabungan.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
