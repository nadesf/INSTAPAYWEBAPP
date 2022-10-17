<?php 
session_start();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instapay</title>

    <!-- Les Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="guichet.css">
</head>
<body>

    <div class="col-sm-6 col-10 shadow p-3 mb-5 bg-body rounded login">


            <div class="logoinsta">
            </div>
            <div class="title">
                <h2 class="text-center">Recu de paiement</h2>
            </div>

            <div>
                <p>
                <strong>Marchand</strong> : <?php echo $_SESSION["company_name"]; ?><br>
                <strong>Client</strong> : <?php echo $_SESSION["recu"]->payee->full_name; ?><br>
                <strong>Adresse Client</strong> : <?php echo $_SESSION["recu"]->payee->email; ?><br>
                <strong>Montant </strong> : <?php echo $_SESSION["amount"]; ?><br>
                <strong>Methode de paiement</strong> : <?php echo $_SESSION["recu"]->provider;?><br>
                <strong>ID Transaction</strong> : <?php echo $_SESSION["recu"]->ID ;?> <br>
                <strong>Date de Paiement</strong> :<?php echo explode("T", $_SESSION["recu"]->datetime)[0];?> <br>
                <strong>Status</strong> : <i class="bi bi-check-circle-fill text-success fs-5"></i>
                </p>
            </div>

    </div>
    
</body>
</html>