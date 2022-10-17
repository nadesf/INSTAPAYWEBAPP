<?php 

session_start();

# L'URL et les endpoints de notre API.
#$domain = "http://localhost:8000/api/v1/";
$domain = "http://164.92.134.116/api/v1/";
$endpoints = array(
    "dotransactions" => "". $domain . "users/transactionsFromMerchant/",
    "active_account" => "". $domain . "users/transactions/",
    "client_pay" => "". $domain . "users/transactionsFromClient/",
);

if (isset($_POST["payer_address"]) && isset($_POST["amount"])) { #Lorsque le marchand doit recevoir l'argent

    $_SESSION["payer_address"] = htmlspecialchars($_POST["payer_address"]);
    $_SESSION["amount"] = htmlspecialchars($_POST["amount"]);

    header("Location: ../code_confirmation.html");
} else if (isset($_POST["confirmation_code"])) {

    $_SESSION["confirmation_code"] = htmlspecialchars($_POST["confirmation_code"]);

    if (strpos($_SESSION["payer_address"], "@") !== false) {
        $provider = "INSTAPAY";
    } else {
        $string = $_SESSION["payer_address"][0] . "" . $_SESSION["payer_address"][1];
        if ($string === "01") {
            $provider = "MOOV";
        }else if ($string === "05") {
            $provider = "MTN";
        }else if ($string === "07" or $string === "77") {
            $provider = "ORANGE";
        }
    }

    $url = $endpoints["dotransactions"];
    $method = "POST";
    $data = array(
        "provider" => $provider,
        "payer_address" => $_SESSION["payer_address"],
        "amount" => $_SESSION["amount"],
        "code" => $_SESSION["confirmation_code"]
    );

    $req = send_data_to_api($url, $data, $method, 1);
    $http_code = $req["http_code"];

    if ($http_code === 200) {
        #($req["response"]);
        $_SESSION["recu"] = $req["response"];

        if ($provider === "ORANGE") {
            $link_to_pay = $req["response"]->go_to_pay;
            header("Location: ". $link_to_pay . "");
        } else {
            header("Location: ../recu.php");
        }
    }else {
        var_dump($res["response"]);
        header("Location: ../guichet.php");
    }
} else if (isset($_POST["payee"]) and isset($_POST["amount"])) { #Lorsque l'utilisateur veur payer un marchand ou envoyer de l'argent à un autre client.
    
    # Récupération des données
    $_SESSION["payee_from_clientapp"] = htmlspecialchars($_POST["payee"]);
    $_SESSION["amount_from_clientapp"] = htmlspecialchars($_POST["amount"]);

    if (isset($_SESSION["transaction_protection"]) and (int) $_SESSION["transaction_protection"] === 1 and isset($_POST["transaction_protection_code"])) {
        $transaction_code = htmlspecialchars($_POST["transaction_protection_code"]);
    }else {
        $transaction_code = "11";
    }

    if (isset($_POST["instapay_provider"])) {
        $provider = "INSTAPAY";
    }else if (isset($_POST["mtn_provider"])) {
        $provider = "MTN";
    }else if (isset($_POST["orange_provider"])) {
        $provider = "ORANGE";
    }else if (isset($_POST["moov_provider"])) {
        $provider = "MOOV";
    }else{
        var_dump("No Provider Select !");
    }

    $url = $endpoints["client_pay"];
    $method = "POST";
    $data = array(
        "provider" => $provider,
        "payee" => $_SESSION["payee_from_clientapp"],
        "amount" => (int) $_SESSION["amount_from_clientapp"],
        "note" => "Payment Test01",
        "transaction_protection_code" => $transaction_code,
    );

    $req = send_data_to_api($url, $data, $method, 1);
    if ($req["http_code"] === 200) {
        #TODO
        $_SESSION["transaction_state"] = 1;

        if ($provider === "ORANGE") {
            var_dump($req);
            $link_to_pay = $req["response"]->go_to_pay;
            header("Location: ". $link_to_pay . "");
        }else {
            header("Location: ../guichet_client.php");
        }
    } else {
        var_dump($req);
    }

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

?>