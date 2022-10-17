<?php 

session_start();
if (!isset($_SESSION["IsAuthenticate"]) or $_SESSION["IsAuthenticate"] !== 1) {
    header("Location: login.php");
}else {
?>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <!-- Les Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    
    <!-- Liens vers le fichier de bootstrap CSS et JS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="sign.css"/>
    <link rel="stylesheet" href="guichet.css">
    <script src="bootstrap/js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <title>Instapay - Payer plus facilement </title>

</head>
<body>
    
    <body>
        <div class="col-sm-6 col-10 shadow p-3 mb-5 bg-body rounded login">
            <form action="php/requestopay.php" method="POST">
    
                <div class="logoinsta">
                </div>
                <div class="title">
                    <h2 class="text-center">Payez avec </h2>
                    <br>
                </div>
    
                <div class="d-flex justify-content-center">
                    <div class="col-md-6 logomtn">
                    </div>
                    <div class="col-md-6 logoorange">
                    </div>
                    <div class="col-md-6 logoinsta">
                    </div>
                    <div class="col-md-6 logomoov">
                    </div>
                </div>
    
                <div class="mb-3">
                    <p></p>
                    <label for="payer_address" class="form-label">Adresse de paiement</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" name="payee" placeholder="example@instapay.com">
                </div>
                <div class="mb-3">
                    <p></p>
                    <label for="payer_address" class="form-label">Montant</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" name="amount" placeholder="Ex : 100">
                </div>

                <?php 
                
                if (isset($_SESSION["transaction_protection"]) and (int) $_SESSION["transaction_protection"] === 1) {
                ?>
                <div class="mb-3">
                    <p></p>
                    <label for="payer_address" class="form-label">Pour pour les transactions</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" name="transaction_protection_code" placeholder="Ex : 100">
                </div>
                <?php
                }
                ?>
                <!-- Bouton radio : Choisir le provider -->
                <div class="d-flex justify-content-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="instapay_provider" id="flexRadioDefault1" checked>
                        <label class="form-check-label me-3" for="instaoay">
                          Instapay
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="mtn_provider">
                        <label class="form-check-label me-3" for="mtn">
                          MTN
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="orange_provider" id="flexRadioDefault1">
                        <label class="form-check-label me-3" for="flexRadioDefault1">
                            Orange
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="moov_provider" id="flexRadioDefault2">
                        <label class="form-check-label me-3" for="flexRadioDefault2">
                            MOOV
                        </label>
                    </div>

                </div>
            
                <p></p>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary submit" type="submit">Suivant</button>
                </div>
            </form>
            <p></p>
            <?php 
            if(isset($_SESSION["transaction_state"]) && (int) $_SESSION["transaction_state"] === 1) {
            ?>
            <p class="text-success text-center fw-bold"> Transaction effectué avec succès </p>
            <?php
            }else if (isset($_SESSION["transaction_state"]) && (int) $_SESSION["transaction_state"] === 0) {
            ?>
            <p class="text-danger text-center fw-bold"> Echec de la transaction </p>
            <?php
            }
            $_SESSION["transaction_state"] = NULL;
            ?>
        </div>
        
    </body>

</body>
</html>

<?php
}
?>