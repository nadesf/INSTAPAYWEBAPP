<?php 
session_start();

if (isset($_SESSION["IsAuthenticate"]) && $_SESSION["IsAuthenticate"] === 1) {
    
?>

<html lang="en">
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
                                <li><a class="dropdown-item fs-6" href="settings.php"><i class="bi bi-gear mx-2 fs-6"></i>Param??tre</a></li>
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
                <h4>Code de paiement</h4>
            </div> <!-- Le message de bonne arriv?? -->

            <div class="col-12 col-md-6 mt-md-3">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php" class="link_breadcrumb">Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="link_breadcrumb">param??tre</a></li>
                    <li class="breadcrumb-item active">profil</li>  
                </ul>
            </div>

            <div class="col-12 shadow p-3 mb-5 bg-body rounded">
                <div class="card" style="border: 1px solid white;">
                    <div class="card-header bg-white">
                        <nav class="nav">
                            <ul class="list-unstyled d-flex">
                                <a href="settings.php" class="nav-link fs-5">Profil</a>
                                <a href="settings_security.php" class="nav-link fs-5">Securit??</a>
                                <a href="settings_payment_method.php" class="nav-link fs-5">Methode de paiment</a>
                                <a href="settings_generate_code.php" class="nav-link fs-5 active">Code de paiement</a>
                            </ul>
                        </nav>
                    </div>

                    <div class="card-body">

                        <p class="display-2 text-center" style="color: #613de6; letter-spacing: 10px;">
                        <?php 
                        if (isset($_SESSION["myTemporaryCode"])) {
                            echo $_SESSION["myTemporaryCode"];
                        }else {
                            echo "******";
                        }
                        $_SESSION["myTemporaryCode"] = NULL;
                        ?>
                        <br>
                        </p>
                        <p><br></p>
                        <form action="php/generate_code.php">
                            <div class="text-center">
                                <button class="btn btn-primary btn-lg submit">G??n??rer</button>
                            </div>
                        </form>
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

    # Les donn??es
    global $endpoints;
    $url = $endpoints['user_infos'];
    $use_token = 1;

    # Envoie et Analyse de la r??ponse
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

function get_data_from_api($url, $use_token) {

    # La requ??te
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

    # Traitement de la r??ponse.
    # On retourne le r??sultat.
    $result = array(
        "http_code" => $httpcode,
        "response" => $response
    );
    return $result;
    #var_dump($response->success)
}

$_SESSION["check"] = NULL;
$_SESSION["msg"] = NULL; 
?>