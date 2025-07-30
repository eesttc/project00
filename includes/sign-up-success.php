
<?php include("../includes/check.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wireless Device Store</title>
</head>
<body>
  <?php include("../public/assets/css/styles.php") ?>
  <?php include('../includes/header.php') ?>

  <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include("../includes/config.php");
  ?>

  <h1 class="sign-up-success">Sign up Success!</h1>

  <?php include('../includes/footer.php') ?>
</body>
</html>