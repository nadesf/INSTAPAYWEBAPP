<?php

session_start();
// ======================================================
//  CE FICHIER PHP CONTIENT TOUTES LES FONCTIONS
//         D'ECHANGE DE DONNEES AVEC L'API
// ======================================================

# L'URL et les endpoints de notre API.
#$domain = "http://localhost:8000/api/v1/";
$domain = "http://164.92.134.116/api/v1/";
$endpoints = array(
    "signup" => "". $domain . "users/signup/",
    "active_account" => "". $domain . "users/active_my_account/",
    "login_token" => "". $domain . "users/login/",
    "second_authentication" => "". $domain . "users/login/second_authentication/",
    "ask_reset_password" => "". $domain . "users/ask_for_reset_password/",
    "reset_password" => "". $domain . "users/reset_password/",
    "change_password" => "". $domain . "users/change_password/",
    "edit_profil" => "". $domain . "users/edit_profile/",
    "logout" => "". $domain . "users/logout/",
    "edit_security" => "". $domain . "users/securityoption/",
    "getalltransactions" => "". $domain . "users/transactions/",
    "client_transaction" => "". $domain . "users/transactionsFromClient/",
    "merchant_transaction" => "". $domain . "users/transactionsFromMerchant/",
    "payment_request" => "". $domain . "users/payment_request/",
    "user_infos" => "". $domain . "users/",
    "providers_name" => $domain . "providers/",
);


/*  Création des variables de session 
$_SESSION["check"] = 1;
$_SESSION["msg"] = 1;

$_SESSION["full_name"] = "";
$_SESSION["email"] = "";
$_SESSION["phone_number"] = "";
$_SESSION["status_user"]  = "";
$_SESSION["double_authentication"]  = "";

$_SESSION["status_account"] = "";
$_SESSION["amount"] = "";
$_SESSION["date_created"] = "";
$_SESSION["account_protection"]  = "";
$_SESSION["provider"]  = "";

$_SESSION["sender"] = "";
$_SESSION["recipient"] = "";
*/

// ------------- INSCRIPTIONS DES UTILISATEURS ----------- V

if (isset($_POST["full_name"]) and isset($_POST["email"]) and isset($_POST["password"])) {

    if (isset($_POST["company_name"]) and isset($_POST["area_activity"])) {
        $company_name = htmlspecialchars($_POST["company_name"]);
        $area_activity = htmlspecialchars($_POST["area_activity"]);
        $status = "merchant";
    }else {
        $company_name = "";
        $area_activity = "";
        $status = "client";
    }

    # Récupèration et préparation des données pour l'envoie
    $full_name = htmlspecialchars($_POST["full_name"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);

    
    $url = $endpoints['signup'];
    $method = "POST";
    $use_token = 0;
    $data = array(
        "full_name" => $full_name,
        "email" => $email,
        "password" => $password,
        "status" => $status,
        "company_name" => $company_name,
        "area_activity" => $area_activity,
    );

    # Envoie des données
    $result = send_data_to_api($url, $data, $method, $use_token);
    $http_code = (int) $result['http_code'];
    if ($http_code === 201) {

        header("Location:../account_created.html");

    }else {
        $_SESSION["check"] = 0;
        $_SESSION["msg"] = "Cette adresse mail est déjà utilisé";
        header("Location:../sign.php");
    }
} else if (isset($_POST["email"]) and isset($_POST["password"])) { // ------------- CONNEXION DES UTILISATEURS -------------- V

    # Récupération des informations 
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    $url = $endpoints['login_token'];
    $method = "POST";
    $data = "email=".$email."&password=".$password."";

    $result = login_user($url, $data, $method);
    $http_code = (int) $result["http_code"];
    #var_dump($http_code);
    #var_dump($result);
    if ($http_code === 200) {
        $_SESSION["refresh_token"] = $result['response']->refresh;
        $_SESSION["access_token"] = $result['response']->access;

        $_SESSION["Authorization"] = "Authorization: Bearer ".$_SESSION["access_token"];

        $result = get_user_info();

        if ($result[0] === 1) {
            $result = $result[1];

            $_SESSION["full_name"] = $result['response']->full_name;
            $_SESSION["email"] = $result['response']->email;
            $_SESSION["phone_number"] = $result['response']->phone_number;
            $_SESSION["user_status"]  = $result['response']->status;
            $_SESSION["balance"] = $result['response']->balance;
            $_SESSION["is_active"] = $result['response']->is_active;
            $_SESSION["double_authentication"]  = $result['response']->double_authentication;
            $_SESSION["transaction_protection"] = $result['response']->transaction_protection;

            if ($_SESSION["user_status"] === "merchant") {
                $_SESSION["company_name"]  = $result['response']->company_name;
                $_SESSION["area_activity"] = $result['response']->area_activity;
            }
        }

        if ((int) $_SESSION["is_active"] === 0) {
            header("Location: ../login.php");
        }

        $do_second_authentication = (int) $_SESSION["double_authentication"];
        if ($do_second_authentication === 1) {

            $_SESSION["second_auth"] = 1;

            $url = $endpoints["second_authentication"];
            $use_token = 1;
            $result = get_data_from_api($url, $use_token);
            //var_dump($result);
        
            $http_code = $result["http_code"];

            if ($http_code === 200) {
                header("Location:../second_authentication.php");  
            } else {
                $_SESSION["check"] = 0;
                $_SESSION["msg"] = "Impossible de faire la seconde authentification";
                header("Location:../login.php");
            }

        } else {

            # Récupèration de la liste des transactions 
            $result = get_user_transactions_list();
           
            if ($result[0] === 1) {
                $payer = $result[1]['response']->payer;
                $payee = $result[1]['response']->payee;
            }

            $transactions = concatenate_table($payer, $payee);
            $transactions_move = [];

            for ($i = 0; $i < count($payer); $i++) {
                $transactions_move[] = "payer";
            }
            for ($i = 0; $i < count($payee); $i++) {
                $transactions_move[] = "payee";
            }

            # Récupération des ID de transaction
            $tab_id = [];
            for ($i = 0; $i < count($transactions); $i++) {
                $tab_id[] = $transactions[$i]->id;
            }

            $users_info = [];
            for ($i = 0; $i < count($transactions); $i++) {
                if ($transactions_move[$i] === "payer") {
                    $users_info[] = $transactions[$i]->payee;
                } else {
                    $users_info[] = $transactions[$i]->payer;
                }
            }
            # Récupération des ID des providers
            $providers_id = [];
            for ($i = 0; $i < count($transactions); $i++) {
                $providers_id[] = $transactions[$i]->provider;
            }
            $url = $endpoints["providers_name"];
            $method = "POST";
            $data = array(
                "providers_id" => $providers_id
            );
            $req = send_data_to_api($url, $data, $method, 1);
            if ($req["http_code"] === 200) {
                $providers_name = $req["response"];
            }else {
                var_dump($req);
                exit();
            }


            # Récupération des données de l'utilisateur
            $url = $endpoints["user_infos"];
            $method = "POST";
            $data = array(
                "users_id" => $users_info
            );
            $req = send_data_to_api($url, $data, $method, 1);
            $users_info = $req['response'];
            $tabs_id = [];
            for ($i = 0; $i < count($tab_id); $i++) {
                $str = explode("TID", $tab_id[$i])[1];
                $str = explode("_", $str)[0];
                $tabs_id[] = $str;
            }

            # Les providers associé à chaque transaction 


            sort($tabs_id);
            $tabs_id = array_reverse($tabs_id);
            for ($i = 0; $i < count($tabs_id); $i++) {
                for ($j = 0; $j < count($tabs_id); $j++) {
                    $val = $transactions[$j]->id;
                    $str = explode("TID", $val)[1];
                    $val = explode("_", $str)[0];

                    if ($tabs_id[$i] === $val) {
                        $aux = $transactions[$i];
                        $transactions[$i] = $transactions[$j];
                        $transactions[$j] = $aux;

                        $aux = $users_info[$i];
                        $users_info[$i] = $users_info[$j];
                        $users_info[$j] = $aux;

                        $aux = $providers_name[$i];
                        $providers_name[$i] = $providers_name[$j];
                        $providers_name[$j] = $aux;

                        $aux = $transactions_move[$i];
                        $transactions_move[$i] = $transactions_move[$j];
                        $transactions_move[$j] = $aux;
                    }
                }
            }
            $_SESSION["all_transactions"] = $transactions;
            $_SESSION["transactions_move"] = $transactions_move;
            $_SESSION["users_address"] = $users_info;
            $_SESSION["providers_name"] = $providers_name;

            $_SESSION["IsAuthenticate"] = 1;

            #var_dump($transactions);
            #var_dump($transactions_move);
            #var_dump($users_info);

            if ($_SESSION["user_status"] === "client") {
                header("Location: ../home_client.php");
            }else {
                header("Location: ../home_marchand.php");
            }

            #sort($tabs_id);
            #$tabs_id = array_reverse($tabs_id);
            #var_dump($tabs_id);
        
        }

    } else {
        $_SESSION["check_login"] = 0;
        header("Location:../login.php"); #http://164.92.134.116:12000/login.php
    }

} else if (isset($_GET["logout"])) {

    # Récupération des informations 
    $refresh = $_SESSION["refresh_token"];
    $url = $endpoints['logout'];
    $method = "POST";
    $data = array(
        "refresh" => $refresh
    );

    $result = send_data_to_api($url, $data, $method, 1);
    $http_code = (int) $result["http_code"];

    session_destroy();
    header("Location:../login.php");

} else if (isset($_POST["second_authentication"])) {

    # Récupération des informations 
    $code = htmlspecialchars($_POST["second_authentication"]);
    $url = $endpoints['second_authentication'];
    $method = "POST";
    $data = array(
        "second_authentication_code" => $code
    );

    $result = send_data_to_api($url, $data, $method, 1);
    $http_code = (int) $result["http_code"];

    if ($http_code === 200) {
        
        $result = get_user_accounts_info();
        if ($result[0] === 1) {
            $result = $result[1];
            $_SESSION["status_account"] = $result['response']->status;
            $_SESSION["amount"] = $result['response']->amount;
            $_SESSION["date_created"] = $result['response']->date_created;
            $_SESSION["account_protection"]  = $result['response']->account_protection;
            $_SESSION["provider"]  = $result['response']->provider;
        } else {
            header("Location: ../login.php");
        }

        $result = get_user_transactions_list();
        if ($result[0] === 1) {
            $result = $result[1];
            $_SESSION["sender"] = $result['response']->sender;
            $_SESSION["recipient"] = $result['response']->recipient; 

            // On différencie les transactions où l'on à été l'émetteur et ou l'on à été le recepteur.
            for ($i = 0; $i < count($_SESSION["sender"]); $i++) {
                $_SESSION["transactions_move"][] = "s";
            }
            for ($i = 0; $i < count($_SESSION["recipient"]); $i++) {
                $_SESSION["transactions_move"][] = "r";
            } 
            $_SESSION["all_transactions"] = concatenate_table($_SESSION["sender"], $_SESSION["recipient"]);
            $tab = [];
            for ($i = 0; $i < count($_SESSION["sender"]); $i++) {
                $tab[] = $_SESSION["sender"][$i]->recipient;
                #$_SESSION["users_id"][] = $_SESSION["sender"][$i]->recipient;
            }
            for ($i = 0; $i < count($_SESSION["recipient"]); $i++) {
                $tab[] = $_SESSION["recipient"][$i]->sender;
                #$_SESSION["users_id"][] = $_SESSION["recipient"][$i]->sender;
            }

            // Nous allons trier les tableaux avant de les envoyers.
            # on récupère les ID des transactions.
            $tab_id = [];
            for ($i = 0; $i < count($_SESSION["all_transactions"]); $i++) {
                $tab_id[] = (int) $_SESSION["all_transactions"][$i]->id;
            }
            sort($tab_id);
            $tab_id = array_reverse($tab_id);
            $transactions_list = $_SESSION["all_transactions"];
            $transactions_move = $_SESSION["transactions_move"];
            for ($i = 0; $i < count($tab_id); $i++) {
                for ($j = 0; $j < count($tab_id); $j++) {
                    $val = (int) $transactions_list[$j]->id;
                    if ($tab_id[$i] === $val) {
                        $aux = $transactions_list[$i];
                        $transactions_list[$i] = $transactions_list[$j];
                        $transactions_list[$j] = $aux;

                        $aux = $tab[$i];
                        $tab[$i] = $tab[$j];
                        $tab[$j] = $aux;

                        $aux = $transactions_move[$i];
                        $transactions_move[$i] = $transactions_move[$j];
                        $transactions_move[$j] = $aux;
                    }
                }
            }
            $_SESSION["all_transactions"] = $transactions_list;
            $_SESSION["transactions_move"] = $transactions_move;

            // Envoie de la requête
            $url = $endpoints["user_infos"];
            $method = "POST";
            $use_token = 1;
            $data = array(
                "get_user_info" => $tab
            );
            $res = send_data_to_api($url, $data, $method, $use_token);
            $http_code = $res["http_code"];

            if ($http_code === 200) {
                $_SESSION["users_address"] = $res["response"]->response;
                $_SESSION["IsAuthenticate"] = 1;
                header("Location: ../home.php");
            } else {
                header("Location: ../login.php");
            }

            //var_dump($_SESSION["user_id_info"]);
            //var_dump($_SESSION["all_transactions"]);
        } else {

            header("Location: ../login.php");
        }

        #header("Location: ../home.php");

    }else {
        $_SESSION["check"] = 0;
        $_SESSION["msg"] = "Le code pour la seconde authentification est incorrecte";
        header("Location:../login.php");
    }

}

# -----------------------------------------------------------------

function get_user_info() {

    # Les données
    global $endpoints;
    $url = $endpoints['user_infos'];
    $use_token = 1;

    # Envoie et Analyse de la réponse
    $result = get_data_from_api($url, $use_token);
    $http_code = (int) $result["http_code"];
    if ($http_code === 200) {

        $res = [1, $result];
        return $res;

    } else {
        $res = [0];
        return $res;
    }
}

function get_user_accounts_info() {

    # Les données
    global $endpoints;
    $url = $endpoints['accounts'];
    $use_token = 1;

    # Envoie et Analyse de la réponse
    $result = get_data_from_api($url, $use_token);
    $http_code = (int) $result["http_code"];

    if ($http_code === 200) {

        $res = [1, $result];
        return $res;

    } else {
        $res = [0];
        return $res;
    }
}

function get_user_transactions_list() {
    global $endpoints;
    $url = $endpoints['getalltransactions'];
    $use_token = 1;

    $result = get_data_from_api($url, $use_token);

    $http_code = (int) $result["http_code"];
    if ($http_code === 200) { # Opération réussie

        $res = [1, $result];
        return $res;

    }else {
        $res = [0];
        return $res;
    }
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

function login_user($url, $data) {

    #$data = http_build_query($data);

    # Création et envoie de la requête
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($request, CURLOPT_POST, 1);
    curl_setopt($request, CURLOPT_POSTFIELDS,$data);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $response  = curl_exec($request);
    $response = json_decode($response);
    $httpcode = curl_getinfo($request, CURLINFO_HTTP_CODE);
    curl_close($request); 

    # On retourne le résultat.
    $results = array(
        "http_code" => $httpcode,
        "response" => $response
    );
    return $results;
}

function ask_for_second_authentication() {

    # Récupération et Préparation des données
    $url = $endpoints["double_authentication"];
    $use_token = 1;

    # Envoie de la requête pour la double authentication
    $result = get_data_from_api($url, $use_token);

    $http_code = (int) $result["http_code"];
    if ($http_code === 200) { # Opération réussie
        // Traitement en cas de succès 
        var_dump($result["response"]);
    }else if ($http_code === 401) { # Le Token n'est plus valide
        echo "Le token n'est plus valide !";
    } else { # Une erreur survenu lors du traitement
        var_dump($result["response"]);
    }

}

function concatenate_table($tab1, $tab2) {
    $tab = [];
    for ($i = 0; $i < count($tab1); $i++) {
        $tab[] = $tab1[$i];
    }
    for ($i = 0; $i < count($tab2); $i++) {
        $tab[] = $tab2[$i];
    }
    return $tab;  
}

?>