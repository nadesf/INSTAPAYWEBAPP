<?php 

session_start();

#$domain = "http://localhost:8000/api/v1/";
$domain = "http://164.92.134.116/api/v1/";

$endpoints = array(
    "getPaymentCode" => $domain . "users/getTemporaryCode/"
);

$url = $endpoints["getPaymentCode"];
$req = get_data_from_api($url, 1);
if ($req["http_code"] === 200) {
    $_SESSION["myTemporaryCode"] = $req["response"]->code;
}


header("Location: ../settings_generate_code.php");

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

?>