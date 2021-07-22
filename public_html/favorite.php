<?php

require_once('./helpers/DatabaseHelper.php');

$sql_configuration_array    = parse_ini_file("../../../../sql-config.ini", true);

// Test server config location
if ($_SERVER['SERVER_NAME'] == 'newcitybetterlife.com' || $_SERVER['HTTP_HOST'] == 'newcitybetterlife.com') {
    $sql_configuration_array    = parse_ini_file("../sql-config.ini", true);
}

$db_name                    = $sql_configuration_array['database']['database'];
$db_hostname                = $sql_configuration_array['database']['hostname'];
$db_username                = $sql_configuration_array['database']['username'];
$db_password                = $sql_configuration_array['database']['password'];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle-city-favorite']) && isset($_POST['username'])) {
    echo "<pre>";

    // Check if we have the right city rank and username selected
    $selected_city_rank = $_POST['toggle-city-favorite'];
    $selected_username = $_POST['username'];

    echo "Working with USERNAME          : " . $selected_username;
    echo "Working with SELECTED_CITY_RANK: " . $selected_city_rank;

    // Use the ULTIMATE PAO shortcut for making database changes
    $database_helper = new DatabaseHelper($db_hostname, $db_name, $db_username, $db_password);
    $selected_city_record_associative_array = $database_helper->get("SELECT * FROM cities WHERE rank = $selected_city_rank");

    echo print_r($selected_city_record_associative_array);

    echo "</pre>";
}
