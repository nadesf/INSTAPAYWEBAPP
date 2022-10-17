<?php 

session_start();

?>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <!-- Les Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    
    <!-- Liens vers le fichier de bootstrap CSS et JS -->
    <link rel="stylesheet" href="assets/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/css/sign.css"/>
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.bundle.js"></script>
    <title>Instapay - Connectez vous </title>

</head>
<body style="background-color: white;">

    <div class="container">
        <div class="row">
            <div class="bloc">
                <!-- <br><br><br><br> -->
                <p class="text-center"><img src="assets/img/4.png" alt=""/></p>
                <h4 class="text-center mb-2 fs-3">Inscrivez-vous</h4>
                <div class="shadow p-3 mb-5 bg-body rounded">
                    <form action="php/signuser.php" method="POST">  
                        <label for="exampleFormControlInput1" class="form-label ">Nom et Prénom(s)</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1" name="full_name" placeholder="Nom & prénom" required>
                        <label for="exampleFormControlInput1" class="form-label mt-2">Email</label>
                        <input type="email" class="form-control" id="exampleFormControlInput1" name="email" placeholder="example@gmail.com" required>
                        <label for="exampleFormControlInput1" class="form-label mt-2">Mot de passe</label>
                        <input type="password" class="form-control" id="exampleFormControlInput1" name="password" placeholder="*************" required>
                        <!-- <label for="exampleFormControlInput1" class="form-label mt-2">Numero</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1" name="number" placeholder="0142214251" required> -->
                        <label for="exampleFormControlInput1" class="form-label mt-2">Nom de l'entreprise</label> 
                        <input type="text" class="form-control" id="exampleFormControlInput1" name="company_name" placeholder="Nom entreprise" required>
                        <label for="exampleFormControlInput1" class="form-label mt-2">Secteur d'activité</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1" name="area_activity" placeholder="ex: energie" required>
                        <?php 
                        if (isset($_SESSION['check']) && $_SESSION["check"] === 0) {
                        ?>
                        <p class="text-danger text-center fw-bold">Cette adresse mail est déja utilisée</p> <br>
                        <?php
                        } 
                        $_SESSION["check"] = NULL;
                        $_SESSION["msg"] = NULL; 
                        ?>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn mt-4 submit">S'inscrire</button>
                        </div><br>
                        <p class="text-center login_link fs-6">Êtes-vous un membre ? <a href="login-march.php">Connectez-vous</a></p>
                        
                    </form>   
                </div>

            </div>
        </div>
    </div>
    
</body>
</html>