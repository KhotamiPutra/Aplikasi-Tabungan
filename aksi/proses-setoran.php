<?php
session_start();
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tabungan_id = $_POST['tabungan_id'];
    $nominal = $_POST['nominal'];
    $tanggal = $_POST['tanggal'];

    $sql = "INSERT INTO menabung (tabungan_id, nominal, tanggal) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ids', $tabungan_id, $nominal, $tanggal);

    if ($stmt->execute()) {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">Setoran berhasil ditambahkan<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">Setoran gagal ditambahkan<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        ' . $stmt->error . '
        </div>';
    }

    header("Location: ../home.php"); // Redirect setelah berhasil
    exit();
}
?>
' . $stmt->error . '