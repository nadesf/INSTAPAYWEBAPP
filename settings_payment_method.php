<?php 

session_start();

if (isset($_SESSION["IsAuthenticate"]) && $_SESSION["IsAuthenticate"] === 1) {
    # L'URL et les endpoints de notre API.
    #$domain = "http://localhost:8000/api/v1/";
    $domain = "http://164.92.134.116/api/v1/";
    $endpoints = array(
        "user_infos" => "". $domain . "users/",
        "providers_name" => $domain . "providers/",
        "payment_method" => $domain . "users/addPaymentMethod/",
    );

    // On récupère les informations du user 
    $result = get_user_info();
    
    if ($result[0] === 1) {
        $result = $result[1];
    
        $_SESSION["full_name"] = $result['response']->full_name;
        $_SESSION["email"] = $result['response']->email;
        $_SESSION["phone_number"] = $result['response']->phone_number;
        $_SESSION["status"]  = $result['response']->status;
        $_SESSION["balance"] = $result['response']->balance;
        $_SESSION["is_active"] = $result['response']->is_active;
        $_SESSION["double_authentication"]  = $result['response']->double_authentication;
        $_SESSION["transaction_protection"] = $result['response']->transaction_protection;
    
        if ($_SESSION["status"] === "merchant") {
            $_SESSION["company_name"]  = $result['response']->company_name;
            $_SESSION["area_activity"] = $result['response']->area_activity;
        }
    }
    
    if ((int) $_SESSION["is_active"] === 0) {
        header("Location: ../login.php");
    }

    $url = $endpoints["payment_method"];
    $method = "POST";
    $req = get_data_from_api($url, 1);
    $_SESSION["myPaymentMethod"] = $req["response"];
    $providers_id = [];
    for ($i = 0; $i < count($_SESSION["myPaymentMethod"]); $i++) {
        $providers_id[] = $_SESSION["myPaymentMethod"][$i]->provider;
    }

    $url = $endpoints["providers_name"];
    $method = "POST";
    $data = array(
        "providers_id" => $providers_id
    );
    $req = send_data_to_api($url, $data, $method, 1);
    $_SESSION["paymentMethodProvider"] = $req["response"];

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instapay</title>

    <!-- Les Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    
    <!-- Liens vers le fichier de bootstrap CSS et JS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="main.css"/>
    <script src="bootstrap/js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.js"></script>

    <style>
        h1 {
            color: #613de6;
        }
    </style>
</head>
<body>

        <!-- Header de la page -->>
        <div class="header bg-light">
        <div class="container">
            <div class="row">
                <div class="header-content d-flex justify-content-between">
                    <div class="header-left">
                        <h1><img src="4-removebg-preview.png" alt=""/>Instapay</h1>
                    </div>
                    <div class="right d-flex">

                        <div class="dropdown align-self-center">
                            <span class="mx-2 mt-1 mt-3 fs-4 dropdown-toggle" id="notification" data-bs-toggle="dropdown" aria-expanded="False">
                                <i class="bi bi-bell"></i>
                            </span>
                            
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Aucun message</a></li>
                            </ul>
                        </div>

                        <div class="dropdown align-self-center">
                            <span class="rounded-circle dropdown-toggle account" id="notification" data-bs-toggle="dropdown" aria-expanded="False">
                                <i class="bi bi-person-fill text-white"></i>
                            </span>
                            <ul class="dropdown-menu" style="width: 270px;">
                                <li><a class="dropdown-item name fw-bold" href="home_client.php"><?php echo $_SESSION["full_name"]; ?></a></li>
                                <li><a class="dropdown-item text-muted" href="#"><?php echo $_SESSION["email"]; ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fs-6" href="home_client.php"><i class="bi bi-person mx-2 fs-6"></i>Portefeuille</a></li>
                                <li><a class="dropdown-item fs-6" href="#"><i class="bi bi-wallet2 mx-2 fs-6"></i>Mes comptes</a></li>
                                <li><a class="dropdown-item fs-6" href="settings.php"><i class="bi bi-gear mx-2 fs-6"></i>Paramêtre</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fs-6 text-danger" href="php/signuser.php?logout=1"><i class="bi bi-box-arrow-left mx-2 fs-6 text-danger"></i>Deconnexion</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Header de la page -->

      
    
    <div class="container"> <!-- Div principale -->
        <div class="row">
            <div class="col-md-6 mt-md-3 mb-md-2 welcome_message">
                <h4>Methode de paiement</h4>
            </div> <!-- Le message de bonne arrivé -->

            <div class="col-12 col-md-6 mt-md-3">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php" class="link_breadcrumb">Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="link_breadcrumb">paramètre</a></li>
                    <li class="breadcrumb-item active">Methode de paiement</li>  
                </ul>
            </div>

            <div class="col-12 shadow p-3 mb-5 bg-body rounded">
                <div class="card" style="border: 1px solid white;">
                    <div class="card-header bg-white">
                        <nav class="nav">
                        <ul class="list-unstyled d-flex">
                                <a href="settings.php" class="nav-link fs-5">Profil</a>
                                <a href="settings_security.php" class="nav-link fs-5">Securité</a>
                                <a href="settings_payment_method.php" class="nav-link fs-5 active">Methode de paiment</a>
                                <a href="settings_generate_code.php" class="nav-link fs-5">Code de paiement</a>
                            </ul>
                        </nav>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6 my-2"> <!-- Modifier Son Email -->
                                <h4>Mobile Money</h4>
                                <hr>
                                <ol class="list-group list-group-numbered">
                                <?php 
                                if (isset($_SESSION["myPaymentMethod"]) and count($_SESSION["myPaymentMethod"])>0) {
                                for ($i = 0; $i < count($_SESSION["myPaymentMethod"]); $i++) {
                                ?>   
                                <li class="list-group-item"><?php echo $_SESSION["myPaymentMethod"][$i]->address . " - " . $_SESSION["paymentMethodProvider"][$i]->name; ?></li>            
                                <?php
                                    }                     
                                }else {
                                ?>
                                <p>Aucun Moyen de paiemnt enrégistrer pour le moment</p>
                                <?php
                                }
                                ?>
                                </ol>
                                <p></p>
                                <button type="button" class="btn btn-primary submit" data-bs-toggle="modal" data-bs-target="#exampleModal">Ajouter</button>

                                <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Compte Mobile Money</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
    <div class="modal-body">
        <form action="php/add_payment_method.php" method="POST">
        <label for="exampleFormControlInput1" class="form-label">Opérateur</label>
      <select class="form-select" name="provider" aria-label="Default select example">
        <option selected>Sélectionner un opérateur</option>
        <option value="MTN">MTN MoMo</option>
        <option value="ORANGE">Orange</option>
        <option value="MOOV">Moov</option>
      </select>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Numéro de téléphone</label>
            <input type="text" class="form-control" name="phone_number" id="exampleFormControlInput1" placeholder="Ex : 2250102030105">
        </div>
        <button type="submit" class="btn btn-primary submit">Enrégistré</button>
        </form>
    </div>
    </div>
  </div>
</div>

                            </div>
                            <div class="col-12 col-md-6 my-2"> <!-- Modifier Son Email -->
                                <h4>Compte Bancaire</h4>
                                <hr>
                                <form action="#" method="POST">
                                    <button type="submit" class="btn btn-success submit">Ajouter</button>
                                </form>
                            </div>
                    </div>

                    
                    </div>
                  </div>
            </div>

            
        </div>
    </div> <!-- div princiaple -->

    <!-- Ajout de notre propre script JS -->
</body>
</html>

<?php
} else {
    header("Location: login.php");
}
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
    $url = $endpoints['transactions'];
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
function concatenate_table($tab1, $tab2) {
    for ($i = 0; $i < count($tab1); $i++) {
        $tab[] = $tab1[$i];
    }
    for ($i = 0; $i < count($tab2); $i++) {
        $tab[] = $tab2[$i];
    }
    
    return $tab;
    
}

$_SESSION["check"] = NULL;
$_SESSION["msg"] = NULL; 
?>