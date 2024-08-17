<?php
session_start();
include 'koneksi.php';

$error = '';
$success = '';

// Handle registration
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek jika email sudah ada
    $sql_check = "SELECT id FROM users WHERE email=?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param('s', $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $name, $email, $password);
        
        if ($stmt->execute()) {
            $success = 'Akun Anda Telah Dibuat';
        } else {
            $error = 'Error: ' . $conn->error;
        }
    }
}

// Handle login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            header("Location: home.php");
            exit();
        } else {
            $error = 'Ada kesalahan di Email atau Password Anda';
        }
    } else {
        $error = 'Ada kesalahan di Email atau Password Anda';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link rel="stylesheet" href="css/loginStyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="form-container">
        <div class="form-content">
            <div class="tab">
                <button class="tab-login active" onclick="showTab('login')">Login</button>
                <button class="tab-register" onclick="showTab('register')">Register</button>
            </div>

            <!-- Display Error or Success Messages -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <div id="login-form" class="form-section active">
                <h2>Login</h2>
                <form method="POST" action="">
                    <input type="hidden" name="login" value="1">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

            <!-- Register Form -->
            <div id="register-form" class="form-section">
                <h2>Register</h2>
                <form method="POST" action="">
                    <input type="hidden" name="register" value="1">
                    <div class="mb-3">
                        <label for="registerName" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="registerName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="registerEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="registerPassword" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tab) {
            document.querySelector('.tab-login').classList.remove('active');
            document.querySelector('.tab-register').classList.remove('active');
            document.querySelector('#login-form').classList.remove('active');
            document.querySelector('#register-form').classList.remove('active');

            if (tab === 'login') {
                document.querySelector('.tab-login').classList.add('active');
                document.querySelector('#login-form').classList.add('active');
            } else {
                document.querySelector('.tab-register').classList.add('active');
                document.querySelector('#register-form').classList.add('active');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
