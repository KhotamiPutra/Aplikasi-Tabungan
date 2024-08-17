<?php
session_start();
include '../koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $target_nominal = $_POST['target_nominal'];
    $target_tanggal = $_POST['target_tanggal'];
    
    // Handle file upload
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }

    // Update tabungan data
    $sql = "UPDATE tabungan SET judul = ?, target_nominal = ?, target_tanggal = ?, foto = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters: string, decimal, date, blob, integer
    $stmt->bind_param('sdssi', $judul, $target_nominal, $target_tanggal, $foto, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">Tabungan berhasil diperbarui<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">Tabungan gagal diperbarui<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }

    header("Location: ../home.php");
    exit();
}
