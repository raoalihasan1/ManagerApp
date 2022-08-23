<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/styleMyAccount.css">
    <title>manager · View My Files</title>
    <script src="https://kit.fontawesome.com/167b7ecae5.js" crossorigin="anonymous"></script>
    <?php
    include_once "accountNavigationBar.php";
    redirectUser();
    ?>
</head>

<body>
    <?php getDocument($_SESSION["DocumentID"], $_SESSION["Email"]) ?>
</body>

</html>