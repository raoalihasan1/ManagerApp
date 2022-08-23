<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/styleMyAccount.css">
    <title>manager · My Files</title>
    <script src="https://kit.fontawesome.com/167b7ecae5.js" crossorigin="anonymous"></script>
    <?php
    include_once "accountNavigationBar.php";
    redirectUser();
    createFolder("fileUploads/");
    if (isset($_GET['id'])) {
        $_SESSION["DocumentID"] = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_URL);
        if (isset($_GET['download']) && isset($_GET['delete'])) {
            header("Location: myFiles.php");
        }
        if (isset($_GET['download']) && $_GET['download']) {
            downloadDocument($_GET['id'], $_SESSION['Email']);
        } else if (isset($_GET['delete']) && $_GET['delete']) {
            deleteDocument($_GET['id'], $_SESSION['Email']);
        } else {
            header("Location: viewMyFiles.php");
        }
    }
    ?>
</head>

<body>
    <div class="fileContainer">
        <div class="uploadFileContainer">
            <?php
            if (isset($_POST["uploadFile"]) && !empty($_FILES["fileToUpload"]['name'])) {
                $uploadFile = uploadFile($_FILES["fileToUpload"], $_SESSION["Email"]);
                if ($uploadFile != null) {
                    echo "<div class='ErrorBox'>";
                    foreach ($uploadFile as $Error) {
                        echo "<div class='ErrorMessage'>" . $Error . "</div>";
                    }
                    echo "</div>";
                }
            }
            ?>
            <form method="post" enctype="multipart/form-data">
                <button id="Btn" type="button" onclick="document.getElementById('fileToUpload').click()">Choose File To Upload<i class="fa-solid fa-upload"></i></button>
                <input type="file" style="display:none" id="fileToUpload" name="fileToUpload" onchange="replaceValue(this)" accept="image/png, image/jpg, image/jpeg, application/pdf">
                <button type="submit" name="uploadFile">Upload File</button>
            </form>
        </div>
        <div class="bottomContainer">
            <div class="uploadedFilesContainer">
                <h4>Uploaded Files</h4>
                <?php getUploadedFiles($_SESSION["Email"]) ?>
            </div>
            <div class="storageCapacity">
                <span class='storageUsedText'><?php echo $_SESSION["StorageUsed"] . "GB of 10GB" ?></span>
                <div style="height: <?php echo $_SESSION['StorageUsed'] * 10 ?>%"></div>
            </div>
        </div>
    </div>
</body>

</html>


<script src="eventHandlers.js"></script>