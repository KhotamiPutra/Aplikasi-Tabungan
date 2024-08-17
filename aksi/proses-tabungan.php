<?php
session_start();
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $target_nominal = $_POST['target_nominal'];
    $target_tanggal = $_POST['target_tanggal'];
    
    $foto_data = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto = $_FILES['foto'];
        $foto_tmp = $foto['tmp_name'];
        $foto_data = file_get_contents($foto_tmp);
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO tabungan (user_id, judul, target_nominal, target_tanggal, foto) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $user_id, $judul, $target_nominal, $target_tanggal, $foto_data);

    if ($stmt->execute()) {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">Tabungan berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Gagal menambahkan tabungan. Error: ' . $stmt->error . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }

    $stmt->close();
    header("Location: ../home.php"); // Redirect setelah berhasil
    exit();
}
?>
