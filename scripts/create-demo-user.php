<?php

if (PHP_SAPI !== "cli")
{
    http_response_code(404);
    exit;
}

// Read the password from the terminal environment so it stays out of Git.
$login = $argv[1] ?? "student";
$firstName = $argv[2] ?? "Student";
$lastName = $argv[3] ?? "Demo";
$password = getenv("DEMO_PASSWORD");

if ($password === false || $password === "")
{
    fwrite(STDERR, "Set DEMO_PASSWORD before running this script.\n");
    exit(1);
}

foreach (array($login, $firstName, $lastName, $password) as $value)
{
    if (strlen($value) > 50)
    {
        fwrite(STDERR, "Use values of 50 bytes or fewer for the lab's fields.\n");
        exit(1);
    }
}

require_once __DIR__ . "/../api/connection.php";
$conn = connectToDatabase();

$stmt = $conn->prepare("SELECT ID FROM Users WHERE Login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$exists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($exists)
{
    fwrite(STDERR, "That login already exists. Choose a different login.\n");
    $conn->close();
    exit(1);
}

$stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstName, $lastName, $login, $password);
$stmt->execute();
$stmt->close();
$conn->close();

echo "Demo user created. Log in and add colors from the website.\n";
