<?php

session_start();

if (isset($_SESSION["IsAuthenticate"]) && (int) $_SESSION["IsAuthenticate"] === 1) {
    header("Location: home.php");
} else {
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
                <br><br><br><br>
                <p class="text-center"><img src="assets/img/4.png" alt=""/></p>
                <h4 class="text-center  mb-3">Connectez-vous </h4>
                <form action="php/signuser.php" method="POST">
                    <div class="shadow p-3 mb-5 bg-body rounded">
                        <label for="exampleFormControlInput1" class="form-label">Email</label>
                        <input type="email" class="form-control" id="exampleFormControlInput1" name="email" placeholder="example@gmail.com" required>
                        <label for="exampleFormControlInput1" class="form-label mt-4">Mot de passe</label>
                        <input type="password" class="form-control  mb-3" id="exampleFormControlInput1" name="password" placeholder="*************" required>
                        <?php 
                        if (isset($_SESSION['check_login']) && $_SESSION["check_login"] === 0) {
                        ?>
                        <p class="text-danger text-center fw-bold">Email ou Mot de passe incorrect</p><br>
                        <?php
                        } else {
                        ?>                        
                        <?php
                        }
                        $_SESSION["check_login"] = NULL;
                        ?>
                        <div class="d-grid gap-2">
                            <button class="btn mt-4 ">Connexion</button>
                        </div><br>
                        <p class="text-center login_link fs-6">Pas membre ? <a href="sign-march.php">Inscrivez-vous</a></p>
                        <p class="text-center login_link fs-6"><a href="reset_password.php">Mot de passe oublié ?</a></a></p>
                    </div>
                </form>

            </div>
        </div>
    </div>
    
</body>
</html>

<?php
}
?>