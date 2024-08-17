<?php
session_start();
include '../koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Delete tabungan data
    $sql = "DELETE FROM tabungan WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">tabungan berhasil dihapus<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">Tabungan gagal dihapus<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    
    header("Location: ../home.php");
    exit();
}
?>
