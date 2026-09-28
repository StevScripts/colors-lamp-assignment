<?php

function connectToDatabase()
{
    $host = getenv("DB_HOST") ?: "localhost";
    $database = getenv("DB_NAME") ?: "COP4331";
    $username = getenv("DB_USER");
    $password = getenv("DB_PASSWORD");

    if ($username === false || $username === "" || $password === false || $password === "")
    {
        http_response_code(500);
        header("Content-Type: application/json");
        echo json_encode(array("error" => "Database settings are missing."));
        exit;
    }

    try
    {
        $connection = new mysqli($host, $username, $password, $database);
        $connection->set_charset("utf8mb4");
        return $connection;
    }
    catch (mysqli_sql_exception $error)
    {
        http_response_code(500);
        header("Content-Type: application/json");
        echo json_encode(array("error" => "Could not connect to the database."));
        exit;
    }
}
