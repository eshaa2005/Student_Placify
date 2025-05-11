<?php
include 'db_connection.php'; 

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $studentId = $data['id'];

    $query = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $studentId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Database error"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}

$conn->close();
?>
