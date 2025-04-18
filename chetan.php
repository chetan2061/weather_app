<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Database connection details
$serverName = "sql102.infinityfree.com";
$userName = "if0_38289739";
$password = "fGhJm5nuEqq5";

// Connect to the database
$conn = mysqli_connect($serverName, $userName, $password);
if (!$conn) {
    die(json_encode(["error" => "Failed to connect to database: " . mysqli_connect_error()]));
}

// Create the database if it doesn't exist
$createDatabase = "CREATE DATABASE IF NOT EXISTS if0_38289739_chetan";
if (!mysqli_query($conn, $createDatabase)) {
    // die(json_encode(["error" => "Failed to create database: " . mysqli_error($conn)]));
}

// Select the database
mysqli_select_db($conn, 'if0_38289739_chetan');

// Create the table if it doesn't exist
$createTable = "CREATE TABLE IF NOT EXISTS weather (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(255) NOT NULL,
    temp FLOAT NOT NULL,
    humidity FLOAT NOT NULL,
    wind FLOAT NOT NULL,
    pressure FLOAT NOT NULL,
    samaya varchar(100)
);";

if (!mysqli_query($conn, $createTable)) {
    // die(json_encode(["error" => "Failed to create table: " . mysqli_error($conn)]));
}


$current_time=time();
$time_two_hours_ago=$current_time-7200;
$delete_two_our_old_data="DELETE FROM weather WHERE samaya <$time_two_hours_ago";
mysqli_query($conn,$delete_two_our_old_data);


// Get the city name from the query parameter and sanitize it
$cityName = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : "Bharatpur";

// Check if the city exists in the database
$selectAllData = "SELECT * FROM weather WHERE city = '$cityName' ";
$result = mysqli_query($conn, $selectAllData);

$rows = []; // Initialize $rows to store query results
if (mysqli_num_rows($result) == 0) {
    // Fetch data from OpenWeather API if not found in the database
    $apiKey = "13595f2a401633aa3a9a3ab66b55bb8d"; 
    $url = "https://api.openweathermap.org/data/2.5/weather?q=$cityName&appid=$apiKey&units=metric";

    $response = file_get_contents($url);
    if (!$response) {
        die(json_encode(["error" => "Failed to fetch data from OpenWeather API."]));
    }

    $data = json_decode($response, true);

    // Validate API response
    if (!isset($data['main']['temp'], $data['main']['humidity'], $data['wind']['speed'], $data['main']['pressure'])) {
        die(json_encode(["error" => "Invalid data from OpenWeather API."]));
    }

    // Extract required fields from the API response
    $temp = $data['main']['temp'];
    $humidity = $data['main']['humidity'];
    $wind = $data['wind']['speed'];
    $pressure = $data['main']['pressure'];
    $date=$data['dt'];
    // Insert the fetched data into the database
    $insertData = "INSERT INTO weather (city, temp, humidity, wind, pressure,samaya)
                   VALUES ('$cityName', '$temp', '$humidity', '$wind', '$pressure','$date')";

    if (!mysqli_query($conn, $insertData)) {
        die(json_encode(["error" => "Failed to insert data: " . mysqli_error($conn)]));
    }

    // Fetch the newly inserted data
    $result = mysqli_query($conn, $selectAllData);
}

// Fetch all rows from the result and encode them as JSON
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

// Close the database connection
mysqli_close($conn);

// Return the JSON-encoded data
echo json_encode($rows);
?>

