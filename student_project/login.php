<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $db_role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password) && $role === $db_role) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $db_role;

            if ($db_role === 'student') {
                header("Location: student_form.html");
            } else {
                header("Location: teacher_dashboard.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid credentials or role mismatch!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.history.back();</script>";
    }
    $stmt->close();
    $conn->close();
}
?>
