<?php 
session_start();

if (isset($_SESSION["IsAuthenticate"]) && $_SESSION["IsAuthenticate"] === 1) {

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

    $result = get_user_info();

    if ($result[0] === 1) {
        $result = $result[1];

        $_SESSION["full_name"] = $result['response']->full_name;
        $_SESSION["email"] = $result['response']->email;
        #$_SESSION["phone_number"] = $result['response']->phone_number;
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

    // On récupère les informations du compte instapay de l'utilisateur 
    
    # Récupèration de la liste des transactions 
    $result = get_user_transactions_list();
           
    if ($result[0] === 1) {
        $payer = $result[1]['response']->payer;
        $payee = $result[1]['response']->payee;
    }else {
        var_dump("Problem");
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
    $_SESSION["IsAuthenticate"] = 1;

    $_SESSION["myqrcode"] = "https://api.qrserver.com/v1/create-qr-code/?size=125x125&data={$_SESSION["email"]}";

?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Instapay</title>

    <!-- Les Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    
    <!-- Liens vers le fichier de bootstrap CSS et JS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="main.css"/>
    <script src="bootstrap/js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.js"></script>

    <style>
        h4, th, td {
            color: #1f2c73;
        }

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
    
    <div class="container">
        <div class="row">

            <div class="col-md-9 mb-md-1 mt-md-2 welcome_message">
                <h6>Akwaba, <span id="username"><?php echo $_SESSION["full_name"]; ?></span></h6>
            </div> <!-- Le message de bonne arrivé -->

            <!-- <div class="col-12 col-md-3 mt-md-5">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.html" class="link_breadcrumb">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>  
                </ul>
            </div> -->

            <!-- Mon Solde -->
            <div class="col-12 d-md-flex my-md-0">
                <div class="col-md-8 col-sm-12 my_account rounded-2 px-2 shadow-sm rounded">
                    <div class="row">
                        <div class="col-md-12">
                            <br>
                            <p class="text-white fw-bold fs-6">Hi, <?php echo $_SESSION["full_name"]; ?></p>
                            <p class="text-white text-center text-md-center mt-4">Fonds disponible</p>
                            <p class="display-6 text-white text-center fw-bold"><?php echo $_SESSION["balance"]; ?> FCFA</p>
                            <p class="text-white text-center text-md-center mt-4"><img src="<?php echo $_SESSION["myqrcode"];?>"/></p>


                        </div>

                        <div class="col-12 col-md-12 d-md-flex justify-content-md-center my-2">
                            <button class="btn btn-success me-1"><i class="bi bi-cash" style="color: white; font-size:18px; margin-right: 5px;"></i><a href="guichet_client.php" style="text-decoration: none; color: white;">Paiements</a></button>
                            <button class="btn btn-warning me-1"><i class="bi bi-send" style="color: white; font-size:16px; margin-right: 5px;"></i><a href="transfer.php" style="text-decoration: none; color: #1f2c73;">Envoyer</a></button>
                            <button class="btn btn-danger me-1"><i class="bi bi-wallet" style="color: white; font-size:16px; margin-right: 5px;"></i> Autre</button>
                        </div>  
                    </div>
                </div> <!-- Compte de l'utilisateur et son QR Code  -->

                <div class="col-sm-12 col-md-4 my-2 my-md-0 my-sm-1 p-md-0 mx-md-3 bg-body card">
                    <div class="card-header bg-white">
                        <p class="bg-white fw-bold h5">Mes Instas</p>
                    </div>
                    <div class="card-body">
                        <p>Heureux de vous revoir, nous esperons que INSTAPAY vous satisfait grandement car, c'est notre priorité</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item fs-6" style="color: #1f2c73;"><i class="bi bi-check-circle-fill text-success fs-5"></i> Vérification du compte</li>
                            <li class="list-group-item fs-6" style="color: #1f2c73;">
                                <?php
                                if (isset($_SESSION["double_authentication"]) && (int) $_SESSION["double_authentication"] === 1) {
                                ?>
                                <i class="bi bi-check-circle-fill text-success fs-5"></i> 
                                <?php
                                }else {
                                // TODO
                                ?>
                                <i class="bi bi-x-circle text-danger fs-5"></i>
                                <?php
                                }
                                ?>
                                Double Authentification
                            </li>
                            
                            <li class="list-group-item fs-6" style="color: #1f2c73;">
                                <?php
                                if (isset($_SESSION["transaction_protection"]) && (int) $_SESSION["transaction_protection"] === 1) {
                                ?>
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>  
                                <?php
                                }else {
                                // TODO
                                ?>
                                <i class="bi bi-x-circle text-danger fs-5"></i>
                                <?php
                                }
                                ?>
                                Code pour les transactions
                            </li>
                        </ul>
                    </div>  
                </div> <!-- Bloc pour transferer des fonds  -->
            </div>


            <div class="col-12 bg-white my-3 shadow p-3 mb-5 bg-body rounded table-responsive">
                <h4><i class="bi bi-currency-exchange fs-4"></i> Mes Transactions</h4> 
                <table class="table align-center datatable">
                    <thead>
                        <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Adresse</th>
                        <th scope="col">Nom Complet</th>
                        <th scope="col">Montant</th>
                        <th scope="col">Type</th>
                        <th scope="col">Date</th>
                        <th scope="col">Provider</th>
                        <th scope="col">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
                        // var_dump($_SESSION["users_address"]);
                        // var_dump($_SESSION["all_transactions"]);
                        for ($i = 0; $i < count($_SESSION["all_transactions"]); $i++) {
                    ?>
                        <tr>
                            <th><?php echo $_SESSION["all_transactions"][$i]->id;?></th>
                            <td><?php echo $_SESSION["users_address"][$i]->email;?></td>
                            <td><?php echo $_SESSION["users_address"][$i]->full_name;?></td>
                            <td><?php echo $_SESSION["all_transactions"][$i]->amount;?></td>
                            <td>
                                <?php 
                                if ($_SESSION["transactions_move"][$i] === "payee" ) {
                                ?>
                                <i class="bi bi-arrow-down text-success fs-5"></i>
                                <?php
                                    // TODO
                                } else {
                                ?>
                                <i class="bi bi-arrow-up text-warning fs-5"></i>
                                <?php
                                    // TODO
                                }
                                ?>
                            </td>

                            <td><?php echo explode("T", $_SESSION["all_transactions"][$i]->datetime)[0];?></td>
                            <td class="fw-bold"><?php echo $_SESSION["providers_name"][$i]->name;?></td>
                            
                            <td>
                                <?php 
                                if ((int) $_SESSION["all_transactions"][$i]->status===1 ) {
                                ?>
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <?php
                                    // TODO
                                } else {
                                ?>
                                <i class="bi bi-hourglass-split text-warning fs-5"></i>
                                <?php
                                    // TODO
                                }
                                ?>
                            </td>
                        </tr>
                            <?php 
                            }
                        ?>
                    </tbody>
                </table>
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
    $tab = [];
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