<?php
header('Content-Type: application/json');

include 'db_connection.php'; 

$sql = "SELECT name, rollno, email, sem1, sem2, sem3, sem4, sem5, sem6, sem7, sem8, status, offer_letter FROM students";
$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(["error" => "SQL Error: " . $conn->error]);
    exit;
}

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = [
        "name" => $row["name"],
        "rollno" => $row["rollno"],
        "email" => $row["email"],
        "cgpa" => [
            "sem1" => $row["sem1"],
            "sem2" => $row["sem2"],
            "sem3" => $row["sem3"],
            "sem4" => $row["sem4"],
            "sem5" => $row["sem5"],
            "sem6" => $row["sem6"],
            "sem7" => $row["sem7"],
            "sem8" => $row["sem8"]
        ],
        "status" => $row["status"],
        "offer_letter" => $row["offer_letter"] ? $row["offer_letter"] : null
    ];
}

if (empty($students)) {
    echo json_encode(["message" => "No records found"]);
} else {
    echo json_encode($students);
}

$conn->close();
?>
