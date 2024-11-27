<?php
    $file = "../storage/doctors.json";
    if(!isset($_GET["name"]) || !isset($_GET["latitude"]) || !isset($_GET["longitude"]))
    {
        if(!file_exists($file))
        {
            return json_encode(array());
        }
        else
        {
            return file_get_contents($file);
        }
    }

    $name       = $_GET["name"];
    $latitude   = $_GET["latitude"];
    $longitude  = $_GET["longitude"];

    $doctor = array(
        "id"        => uniqid(),
        "name"      => $name,
        "latitude"  => $latitude,
        "longitude" => $longitude
    );

    if(!file_exists($file))
    {
        $doctors = array();
    }
    else
    {
        $doctors = json_decode(file_get_contents($file), true);
    }

    $doctors[] = $doctor;
    file_put_contents($file, json_encode($doctors, JSON_PRETTY_PRINT));

    return json_encode($doctor);
?>