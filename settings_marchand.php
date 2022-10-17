<?php 

session_start();

if (isset($_SESSION["IsAuthenticate"]) && $_SESSION["IsAuthenticate"] === 1) {

    # L'URL et les endpoints de notre API.
    #$domain = "http://localhost:8000/api/v1/";
    $domain = "http://164.92.134.116/api/v1/";
    $endpoints = array(

    "signup" => "". $domain . "users/signup/",
    
    "login" => "". $domain . "users/login/",

    "active_account" => "". $domain . "users/active_my_account/",

    "edit_profile" => "". $domain . "users/edit_profile/",

    "second_authentication" => "". $domain . "users/login/second_authentication/",

    "ask_reset_password" => "". $domain . "users/ask_for_reset_password/",

    "reset_password" => "". $domain . "users/reset_password/",

    "double_authentication" => "". $domain . "users/securityoption/?double_authentication=1",

    "transaction_protection" => "". $domain . "users/securityoption/?transaction_protection=0",

    "change_password" => "". $domain . "users/change_password/",

    "transactions_client" => "". $domain . "users/transactionsFromClient/",

    "transactions_merchant" => "". $domain . "users/transactionsFromMerchant/",

    "temporary_code" => "". $domain . "users/getTemporaryCode/",

    "api_key" => "". $domain . "users/generateAPIKey/",

    "payment_method" => "". $domain . "users/addPaymentMethod/",

    "logout" => "". $domain . "users/logout/",

    "user_infos" => "". $domain . "users/",

    "payment_request" => "". $domain . "users/payment_request/",

    "accounts" => "". $domain . "users/accounts/",

    "transactions" => "". $domain . "users/transactions/",
    );

    // On récupère les informations du user 

    $result = get_user_info();
    if ($result[0] === 1) {

    $result = $result[1];
    $_SESSION["full_name"] = $result['response']->full_name;
    $_SESSION["email"] = $result['response']->email;
    $_SESSION["password"] = $result['response']->password;
    $_SESSION["phone_number"] = $result['response']->phone_number;
    $_SESSION["company_name"] = $result['response']->company_name;
    $_SESSION["area_activity"] = $result['response']->area_activity;
    $_SESSION["status"]  = $result['response']->status;
    $_SESSION["double_authentication"]  = $result['response']->double_authentication;

    }

    
    $result = get_user_accounts_info();
    if ($result[0] === 1) {

        $result = $result[1];
        $_SESSION["status_account"] = $result['response']->status;
        $_SESSION["amount"] = $result['response']->amount;
        $_SESSION["date_created"] = $result['response']->date_created;
        $_SESSION["account_protection"]  = $result['response']->account_protection;
        $_SESSION["provider"]  = $result['response']->provider;

    }
?>

<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta content="width=device-width, initial-scale=1.0" name="viewport">
      <title>Users / Profile - Admin Bootstrap Template</title>
      <meta name="robots" content="noindex, nofollow">
      <meta content="" name="description">
      <meta content="" name="keywords">
      <link href="https://fonts.gstatic.com" rel="preconnect">
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
      <link href="assets/css/bootstrap.min.css" rel="stylesheet">
      <link href="assets/css/bootstrap-icons.css" rel="stylesheet">
      <link href="assets/css/boxicons.min.css" rel="stylesheet">
      <link href="assets/css/quill.snow.css" rel="stylesheet">
      <link href="assets/css/quill.bubble.css" rel="stylesheet">
      <link href="assets/css/remixicon.css" rel="stylesheet">
      <link href="assets/css/simple-datatables.css" rel="stylesheet">
      <link href="assets/css/style.css" rel="stylesheet">

      <!-- Liens vers le fichier de bootstrap CSS et JS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="main.css"/>
    <script src="assets/js/bootstrap.bundle.js"></script>
   </head>
   <body>

    <!-- Header de la page -->
    <div class="header bg-light">
        <div class="container">
            <div class="row">
                <div class="header-content d-flex justify-content-between">
                    <div class="header-left">
                        <h1 style="color: #613de6;"><img src="assets/img/4-removebg-preview.png" alt=""/>Instapay</h1>
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
                                <li><a class="dropdown-item name fw-bold" href="home_marchand.php"><?php echo $_SESSION["full_name"]; ?></a></li>
                                <li><a class="dropdown-item text-muted" href="settings_marchand.php"><?php echo $_SESSION["email"]; ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fs-6" href="home_marchand.php"><i class="bi bi-cash mx-2 fs-6"></i>Portefeuille</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fs-6" href="#"><i class="bi bi-wallet2 mx-2 fs-6"></i>Compte</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fs-6" href="settings_marchand.php"><i class="bi bi-gear mx-2 fs-6"></i>Paramètre</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fs-6 text-danger" href="php/signup-march.php?logout=1"><i class="bi bi-box-arrow-left mx-2 fs-6 text-danger"></i>Deconnexion</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Header de la page -->
      <main id="main" class="main">
         <div class="pagetitle ">
            <div class="col-12 col-md-3 mt-md-3">
                <ul class="breadcrumb align-self-center">
                    <li class="breadcrumb-item"><a href="home.php" class="link_breadcrumb">Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="link_breadcrumb">Paramètre</a></li>
                    <li class="breadcrumb-item active">Mes Infos</li>  
                </ul>
            </div>
         </div>
         <section class="section profile">
            <div class="row justify-content-center">
               <div class="col-xl-10">
                  <div class="card">
                     <div class="card-body pt-3">
                        <ul class="nav nav-tabs nav-tabs-bordered">
                           <li class="nav-item"> <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Mon Profil</button></li>
                           <li class="nav-item"> <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Modifier Profil</button></li>
                           <li class="nav-item"> <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Sécurité</button></li>
                           <li class="nav-item"> <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Modifier Mot de passe</button></li>
                        </ul>
                        <!-- tab-pane de la partie "affichage des infos du profil" -->
                        <div class="tab-content pt-2">
                           <div class="tab-pane fade show active profile-overview pt-3" id="profile-overview">
                              <form>
                                <div class="row">
                                 <div class="col-lg-3 col-md-4 label ">Nom et Prénom(s)</div>
                                 <div class="col-lg-9 col-md-8">luffy monkey<?php echo $_SESSION["full_name"];?></div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-3 col-md-4 label">Email</div>
                                 <div class="col-lg-9 col-md-8">luffy@yopmail.com<?php echo $_SESSION["email"];?></div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-3 col-md-4 label">password</div>
                                 <div class="col-lg-9 col-md-8">*******<?php echo $_SESSION["password"];?></div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-3 col-md-4 label">Numero</div>
                                 <div class="col-lg-9 col-md-8">0102030405<?php echo $_SESSION["phone_number"];?></div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-3 col-md-4 label">Entreprise</div>
                                 <div class="col-lg-9 col-md-8">Cie<?php echo $_SESSION["company_name"];?></div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-3 col-md-4 label">Secteur d'activité</div>
                                 <div class="col-lg-9 col-md-8">Energie<?php echo $_SESSION["activity_sector"];?></div>
                              </div>    
                              </form>
                           </div>

                            <!-- tab-pane de la partie "edit_profil" -->

                           <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                              <form>
                                
                                 <div class="row mb-3">
                                    <label for="full_Name" class="col-md-4 col-lg-3 col-form-label">Nom et Prenom(s)</label>
                                    <div class="col-md-8 col-lg-6"> <input name="full_Name" type="text" class="form-control" id="full_Name" placeholder="<?php echo $_SESSION["full_name"];?>"></div>
                                 </div>
                                 
                                 <div class="row mb-3">
                                    <label for="email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                    <div class="col-md-8 col-lg-6"> <input name="email" type="email" class="form-control" id="email" placeholder="<?php echo $_SESSION["email"];?>"></div>
                                 </div>
                                 <div class="row mb-3">
                                    <label for="password" class="col-md-4 col-lg-3 col-form-label">Mot de passe</label>
                                    <div class="col-md-8 col-lg-6"> <input name="password" type="password" class="form-control" id="password" placeholder="<?php echo $_SESSION["password"];?>"></div>
                                 </div>
                                 <div class="row mb-3">
                                    <label for="phone_number" class="col-md-4 col-lg-3 col-form-label">Numero</label>
                                    <div class="col-md-8 col-lg-6"> <input name="phone_number" type="text" class="form-control" id="phone_number" placeholder="<?php echo $_SESSION["phone_number"];?>"></div>
                                 </div>
                                 
                                 <div class="row mb-3">
                                    <label for="company_name" class="col-md-4 col-lg-3 col-form-label">Entreprise</label>
                                    <div class="col-md-8 col-lg-6"> <input name="company_name" type="text" class="form-control" id="company_name" placeholder="<?php echo $_SESSION["company_name"];?>"></div>
                                 </div>
                                 <div class="row mb-3">
                                    <label for="activity_sector " class="col-md-4 col-lg-3 col-form-label">Secteur d'activité</label>
                                    <div class="col-md-8 col-lg-6"> <input name="activity_sector" type="text" class="form-control" id="activity_sector" placeholder="<?php echo $_SESSION["activity_sector"];?>"></div>
                                 </div><br>
                                 
                                 <div> <button type="submit" class="btn btn-success">Valider</button></div>
                              </form>
                              <?php 
                                if (isset($_SESSION["check_editprofil"]) && (int) $_SESSION["check_editprofil"] === 1) {
                                ?>
                                <p class="text-success fw-bold text-center">Vos informations ont été mises à jour</p>
                                <?php    
                                }else if(isset($_SESSION["check_payreq"]) && (int) $_SESSION["check_payreq"] === 0) {
                                ?> 
                                <p class="text-danger fw-bold text-center"> Impossible de mettre à jour.</p>
                                <?php  
                                }
                                $_SESSION["check_editprofil"] = NULL;
                              ?>
                           </div>

                           <!-- tab-pane de la partie "sécurité" -->

                          <div class="tab-pane fade pt-3" id="profile-settings">
                              <form action="php/edit_march.php" method="POST">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name= "email_alert" role="switch" id="flexSwitchCheckChecked" checked>
                                        <label class="form-check-label" for="flexSwitchCheckChecked">M'avertir à chaque nouvelle connexion</label>
                                    </div><br>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name= "double_authentication" role="switch" id="flexSwitchCheckChecked" 
                                        <?php
                                        if (isset($_SESSION["double_authentication"]) && (int) $_SESSION["double_authentication"] === 1) {
                                        ?>
                                        checked
                                        <?php
                                        }
                                        ?>
                                        >
                                        <label class="form-check-label" for="flexSwitchCheckChecked">Authentification à double facteur</label>
                                    </div><br>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="account_protection" id="flexSwitchCheckChecked"
                                        <?php
                                        if (isset($_SESSION["account_protection"]) && (int) $_SESSION["account_protection"] === 1) {
                                        ?>
                                        checked
                                        <?php
                                        }
                                        ?>
                                        >
                                        <label class="form-check-label" for="flexSwitchCheckChecked">Protéger les transactions</label>
                                    </div><br>
                                    <div class="row mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Code PIN pour les transactions</label>
                                        <div class="col-md-8 col-lg-6"> <input type="password" class="form-control" id="confirm_password" name= "protection_code"></div>
                                    </div><br>
                                    <button type="submit" class="btn btn-success">Envoyer</button>
                                </form>
                           </div> 
                           <!-- tab-pane de la partie "edit_password" -->
                           <div class="tab-pane fade pt-3" id="profile-change-password">
                              <form>
                                 <div class="row mb-3">
                                    <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Ancien mot de passe</label>
                                    <div class="col-md-8 col-lg-6"> <input name="old_password" type="password" class="form-control" required></div>
                                 </div>
                                 <div class="row mb-3">
                                    <label for="New Password" class="col-md-4 col-lg-3 col-form-label">Nouveau Mot de passe</label>
                                    <div class="col-md-8 col-lg-6"> <input name="new_password" type="password" class="form-control" required></div>
                                 </div>
                                 <div class="row mb-3">
                                    <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Comfirmation Mot de passe</label>
                                    <div class="col-md-8 col-lg-6"> <input name="confirm_password" type="password" class="form-control" required></div>
                                 </div><br>
                                 <div> <button type="submit" class="btn btn-success">Envoyer</button></div>
                              </form>
                              <?php 
                                if (isset($_SESSION["check_changepassword"]) && (int) $_SESSION["check_changepassword"] === 1) {
                                ?>
                                <p class="text-success fw-bold text-center">Votre mot de passe à été mis à jour</p>
                                <?php    
                                }else if(isset($_SESSION["check_changepassword"]) && (int) $_SESSION["check_changepassword"] === 0) {
                                ?> 
                                <p class="text-danger fw-bold text-center"> Impossible de mettre à jour le mot de passe.</p>
                                <?php  
                                }
                                $_SESSION["check_changepassword"] = NULL;
                                ?>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </section>
      </main>
     <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>  
       <script src="assets/js/apexcharts.min.js"></script>
       <script src="assets/js/bootstrap.bundle.min.js"></script>
       <script src="assets/js/chart.min.js"></script>
       <script src="assets/js/echarts.min.js"></script>
       <script src="assets/js/quill.min.js"></script>
       <script src="assets/js/simple-datatables.js"></script>
       <script src="assets/js/tinymce.min.js"></script>
       <script src="assets/js/validate.js"></script>
       <script src="assets/js/main.js"></script> 
            
  </body>
</html>

<?php
} else {
    header("Location: login-march.php");
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

$_SESSION["check"] = NULL;
$_SESSION["msg"] = NULL; 


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