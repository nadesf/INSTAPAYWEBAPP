<?php 

# Récurpération des données
$full_name = "Jean Luc";
$email = "jeanluc84@yopmail.com";
$password = "12345678";

# Préparation des données 
$url = 'http://164.92.134.116/api/v1/signup/';
$data = array(
    "full_name" => $full_name,
    "email" => $email,
    "password" => $password,
);

# ENVOIE DE DONNES POST
$data_json = json_encode($data);
$request = curl_init();
curl_setopt($request, CURLOPT_URL, $url);
curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($request, CURLOPT_POST, 1);
curl_setopt($request, CURLOPT_POSTFIELDS,$data_json);
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
$response  = curl_exec($request);

$response = json_decode($response);
$httpcode = curl_getinfo($request, CURLINFO_HTTP_CODE);
curl_close($request);

var_dump($response);


?>