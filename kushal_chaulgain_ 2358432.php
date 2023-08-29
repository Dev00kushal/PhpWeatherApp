<?php
$city_name = "KNOWSLEY"; // Set the city name

// Function to fetch weather data from API
function fetch_weather_data()
{
  global $city_name;
  $api_key = "bdd64c736f51fb2ffa936eaf1baa8bb3";
  $url = "https://api.openweathermap.org/data/2.5/weather?q=$city_name&appid=$api_key&units=metric";

  // Fetch JSON data from API
  $json_data = file_get_contents($url);

  // Decode JSON data to PHP object
  $response_data = json_decode($json_data);

  // Check if the API request was successful
  if ($response_data->cod === 200) {
    // Extract weather information
    $day_of_week = date('l');
    $day_and_date = date('M j, Y');
    $weather_condition = $response_data->weather[0]->description;
    $weather_icon = $response_data->weather[0]->icon;
    $temperature = $response_data->main->temp;
    $pressure = $response_data->main->pressure;
    $wind_speed = $response_data->wind->speed;
    $humidity = $response_data->main->humidity;

    // Construct the complete URL for the weather icon
    $icon_url = "https://openweathermap.org/img/w/$weather_icon.png";

    // Return weather data as an array
    return [
      $day_of_week, $day_and_date, $weather_condition, $weather_icon,
      $temperature, $pressure, $wind_speed, $humidity
    ];
  } else {
    echo "Error: Unable to fetch weather data.";
  }
}

// Function to create the database
function create_DB($servername, $username, $password, $dbname)
{
  // Create connection
  $conn = new mysqli($servername, $username, $password);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Create database if not exists
  $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
  if ($conn->query($sql) !== TRUE) {
    echo "Error creating database: " . $conn->error;
  }

  $conn->close();
}

// Function to create the table
function create_table($servername, $username, $password, $dbname)
{
  global $city_name;
  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // SQL to create table if not exists
  $sql = "CREATE TABLE IF NOT EXISTS $city_name(
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Week_Day VARCHAR(15),
    Day_Date VARCHAR(20),
    Weather VARCHAR(50),
    Weather_Icon VARCHAR(100),
    Temperature INT(5),
    Pressure INT(6),
    Wind_Speed DECIMAL(5, 2),
    Humidity INT(5)
  )";

  if ($conn->query($sql) !== TRUE) {
    echo "Error creating table: " . $conn->error;
  }

  $conn->close();
}

// Function to insert or update data
function insert_update_data($servername, $username, $password, $dbname)
{
  global $city_name;
  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Fetch weather data from API
  list($day_of_week, $day_and_date, $weather_condition, $weather_icon, $temperature, $pressure, $wind_speed, $humidity) = fetch_weather_data();

  // Check if there are less than 7 records in the table
  $sql = "SELECT * FROM $city_name";
  $result = $conn->query($sql);

  if ($result->num_rows !== 7) {
    // Insert new data
    $sql_ = "INSERT INTO $city_name (Week_Day, Day_Date, Weather,Weather_Icon, Temperature, Pressure, Wind_Speed, Humidity)
            VALUES ('$day_of_week', '$day_and_date', '$weather_condition', '$weather_icon', $temperature, $pressure, $wind_speed, $humidity)";

    if ($conn->query($sql_) !== TRUE) {
      echo "Error: " . $sql_ . "<br>" . $conn->error;
    }
  } else {
    // Update existing data
    $sql_ = "UPDATE $city_name 
            SET 
                Day_Date = '$day_and_date',
                Weather = '$weather_condition',
                Weather_Icon = '$weather_icon',
                Temperature = $temperature,
                Pressure = $pressure,
                Wind_Speed = $wind_speed,
                Humidity = $humidity
            WHERE Week_Day = '$day_of_week'";

    if ($conn->query($sql_) !== TRUE) {
      echo "Error: " . $sql_ . "<br>" . $conn->error;
    }
  }

  $conn->close();
}

// Function to display data in HTML
function display_data($servername, $username, $password, $dbname)
{
  global $city_name;
  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Fetch data from the table
  $sql = "SELECT * FROM $city_name";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    echo '<div class="week-grid">'; // Assuming you have a CSS class "week-grid" for styling

    while ($row = $result->fetch_assoc()) {
        echo '<div class="week-box">';
        echo '<div class="week-content">';
        echo '<p>' .$row["Week_Day"]. '</p>';
        echo '<figure><img src="./images/' . $row["Weather_Icon"] . '.png" alt="weather-icon" /></figure>';
        echo '<p>' . $row["Temperature"] . '°C</p>';
        echo '<p>' . $row["Pressure"] . ' Pa</p>';
        echo '<p>' . $row["Wind_Speed"] . ' m/s</p>';
        echo '<p>' . $row["Humidity"] . ' %</p>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
  } else {
    echo "0 results";
  }

  $conn->close();
}

// Function to connect to the database and perform necessary actions
function connect_DB()
{
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "weather_db";

  // Create database
  create_DB($servername, $username, $password, $dbname);

  // Create table
  create_table($servername, $username, $password, $dbname);

  // Insert or update data
  insert_update_data($servername, $username, $password, $dbname);

  // Display weather data
  display_data($servername, $username, $password, $dbname);
}
?>
  
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Weather App</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="kushal_chaulgain_ 2358432.css">
</head>
<body>
<div class="wrapper">
  <div class="container">
    <div class="search-container">
      <input
        type="text"
        placeholder="Enter a city name"
        id="city"
        value="Knowsley"
      />
      <button id="search-btn">Search</button>
    </div>
    <div id="result"></div>
  </div>

  <section class="right">
    <h1>
      <?php echo $city_name . " Weather Data"; ?>
    </h1>
    <div class="content">
      <div class="week-box">
      <div class="week-container">
        <?php connect_DB(); ?>
      </div>
    </div>

    </div>
    </section>
</div>
<script src="kushal_chaulgain_ 2358432.js" defer></script>
</body>
</html>
