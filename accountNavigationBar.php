<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/styleNavBar.css">
    <?php include_once "managerFunctions.php" ?>
</head>

<body style="height: auto">
    <header id="navBarHeader">
        <a href="Home.php">
            <h3>manager.</h3>
        </a>
        <nav>
            <ul class="navBarLinks">
                <li><a class="accountNavBar" href="myTasks.php">Tasks</a></li>
                <li><a class="accountNavBar" href="myNotes.php">Notes</a></li>
                <li><a class="accountNavBar" href="myFiles.php">My Files</a>
                <li><a class="accountNavBar" href="Settings.php">Settings</a></li>
            </ul>
        </nav>
        <a href="logOut.php"><button class="logOut">Log Out</button></a>
    </header>
</body>

</html>