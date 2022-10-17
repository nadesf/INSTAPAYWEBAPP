<?php 

session_start();

#$domain = "http://localhost:8000/api/v1/";
$domain = "http://164.92.134.116/api/v1/";
$endpoints = array(
    "user_infos" => "". $domain . "users/",
    "providers_name" => $domain . "providers/",
    "payment_method" => $domain . "users/addPaymentMethod/",
);

if (isset($_POST["phone_number"]) and isset($_POST["provider"])) {
    $_SESSION["mobile_money_address"] =  htmlspecialchars($_POST["phone_number"]);
    $_SESSION["mobile_money_provider"] = htmlspecialchars($_POST["provider"]);
    
    $url = $endpoints["payment_method"];
    $method = "POST";
    $data = array(
        "provider" => $_SESSION["mobile_money_provider"],
        "phone_number" => $_SESSION["mobile_money_address"]
    );
     
    $req = send_data_to_api($url, $data, $method, 1);
    if ($req["http_code"] === 200) {
        header("Location: ../settings_payment_method.php");
    }else {
        $_SESSION["payment_method_check"] = 0;
        header("Location: ../settings_payment_method.php");
    }

} else {
    header("Location: ../settings_payment_method.php");
}

// DEMO - GET, POST, PUT, ET PATCH.
function get_data_from_api($url, $use_token) {

    # La requête
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    if ($use_token === 1) {
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $_SESSION["Authorization"]));
    }else {
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    }
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($request);
    $response = json_decode($response);
    $httpcode = curl_getinfo($request, CURLINFO_HTTP_CODE);
    curl_close($request);

    # Traitement de la réponse.
    # On retourne le résultat.
    $result = array(
        "http_code" => $httpcode,
        "response" => $response
    );
    return $result;
    #var_dump($response->success)
}

function send_data_to_api($url, $data, $request_type, $use_token) {

    # Encodage des données
    $data_json = json_encode($data);

    # Création et envoie de la requête
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    if ($use_token === 1) {
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $_SESSION["Authorization"]));
    }else {
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    }
    if ($request_type === "POST") {
        curl_setopt($request, CURLOPT_POST, 1);
    }else {
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $request_type);
    }
    curl_setopt($request, CURLOPT_POSTFIELDS,$data_json);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $response  = curl_exec($request);
    $response = json_decode($response);
    $httpcode = curl_getinfo($request, CURLINFO_HTTP_CODE);
    curl_close($request); 

    # On retourne le résultat.
    $result = array(
        "http_code" => $httpcode,
        "response" => $response
    );
    return $result;
}

?>