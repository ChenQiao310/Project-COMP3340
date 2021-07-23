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

    $selected_city_rank     = $_POST['toggle-city-favorite'];
    $selected_username      = $_POST['username'];

    $database_helper = new DatabaseHelper($db_hostname, $db_name, $db_username, $db_password);
    $selected_city_record_array = $database_helper->get("SELECT * FROM cities WHERE rank = $selected_city_rank");
    $selected_city_name = $selected_city_record_array[0]['city_town'] . ", " . $selected_city_record_array[0]['province'];

    if ($database_helper->is_this_table_created("favorites") == false) {
        $database_helper->set("CREATE TABLE `favorites` (`username` VARCHAR(255) NOT NULL, `favorite_city_rank` INT, `favorite_city_name` VARCHAR(255))");
    }

    $user_favorite_record = $database_helper->get("SELECT * FROM favorites WHERE username = '$selected_username' AND favorite_city_rank = $selected_city_rank");

    if (count($user_favorite_record) == 0) {
        $database_helper->set("INSERT INTO favorites(username, favorite_city_rank, favorite_city_name) VALUES ('$selected_username', $selected_city_rank, '$selected_city_name');");
        $new_user_favorite_record = $database_helper->get("SELECT * FROM favorites WHERE username = '$selected_username' AND favorite_city_rank = $selected_city_rank");
    } else {
        // Else, delete the record if delete mode was detected from the city.php page)
        if (isset($_POST['undo-favorite'])) {
            $database_helper->set("DELETE FROM favorites WHERE username = '$selected_username' AND favorite_city_rank = $selected_city_rank");
        }
    }

    echo "<form id='form-favorite-return' method='GET' action='city.php' hidden>";
    echo "    <input type='hidden' name='rk' value='$selected_city_rank' hidden>";
    echo "</form>";
    echo "<script type='text/javascript'>";
    echo "    document.getElementById('form-favorite-return').submit();";
    echo "</script>";
}
