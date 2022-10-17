<?php 
session_start();

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instapay</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="guichet.css">
</head>
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
                <input type="text" class="form-control" id="exampleFormControlInput1" name="payer_address" placeholder="2250102030405 ou example@instapay.com">
            </div>
            <div class="mb-3">
                <p></p>
                <label for="payer_address" class="form-label">Montant</label>
                <input type="text" class="form-control" id="exampleFormControlInput1" name="amount" placeholder="Ex : 100">
            </div>

            <div class="d-grid gap-2">
                <button class="btn submit" type="submit">Suivant</button>
            </div>
        </form>

        <?php
        if (isset($_SESSION["transaction_state"]) && $_SESSION["transaction_state"] === 0) {
        ?>
        <p class="text-danger fw-bold">Echec de la transaction </p>
        <?php 
        }
        ?>
    </div>
    
</body>
</html>