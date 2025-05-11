<?php
include 'db_connection.php';

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$name = isset($_POST['name']) ? cleanInput($_POST['name']) : null;
$rollno = isset($_POST['rollno']) ? cleanInput($_POST['rollno']) : null;
$email = isset($_POST['email']) ? cleanInput($_POST['email']) : null;
$status = isset($_POST['status']) ? cleanInput($_POST['status']) : null;


$sem1 = isset($_POST['sem1']) ? (float)$_POST['sem1'] : null;
$sem2 = isset($_POST['sem2']) ? (float)$_POST['sem2'] : null;
$sem3 = isset($_POST['sem3']) ? (float)$_POST['sem3'] : null;
$sem4 = isset($_POST['sem4']) ? (float)$_POST['sem4'] : null;
$sem5 = isset($_POST['sem5']) ? (float)$_POST['sem5'] : null;
$sem6 = isset($_POST['sem6']) ? (float)$_POST['sem6'] : null;
$sem7 = isset($_POST['sem7']) ? (float)$_POST['sem7'] : null;
$sem8 = isset($_POST['sem8']) ? (float)$_POST['sem8'] : null;

// Handle Offer Letter File Upload
$offerLetterPath = null;
if ($status === "Already Placed" && isset($_FILES['offerLetter']) && $_FILES['offerLetter']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
    $fileName = uniqid() . "_" . basename($_FILES['offerLetter']['name']);
    $offerLetterPath = $targetDir . $fileName;

    if (!move_uploaded_file($_FILES['offerLetter']['tmp_name'], $offerLetterPath)) {
        echo "❌ Error uploading file.";
        exit;
    }
}


$sql = "INSERT INTO students (name, rollno, email, status, offer_letter, sem1, sem2, sem3, sem4, sem5, sem6, sem7, sem8)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssdddddddd", $name, $rollno, $email, $status, $offerLetterPath, $sem1, $sem2, $sem3, $sem4, $sem5, $sem6, $sem7, $sem8);

if ($stmt->execute()) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Success</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background-color: #f4f4f4; }
            .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
            h2 { color: green; }
            a { text-decoration: none; color: blue; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>✅ Student Record Saved Successfully!</h2>
            <p><a href='index.html'>Go Back</a></p>
        </div>
    </body>
    </html>";
} else {
    echo "<h3 style='color:red;'>❌ Error: " . $stmt->error . "</h3>";
}

$stmt->close();
$conn->close();
?>
