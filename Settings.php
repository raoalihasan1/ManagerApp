<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>manager · Account Settings</title>
    <link rel="stylesheet" href="CSS/styleAccount.css">
    <script src="eventHandlers.js"></script>
    <?php
    include_once "managerFunctions.php";
    redirectUser();
    ?>
</head>

<body>
    <div class="Container" id="Container">
        <div class="formContainer containerLeft">
            <form method="post" accept-charset="utf-8">
                <h3>Account Settings</h3>
                <h6>Manage Your Account Details</h6>
                <?php
                if (isset($_POST["updateDetails"])) {
                    echo '<div class="errorsContainer">';
                    $updateDetails = resetPassword($_POST["changePassword"], $_POST["confirmChangePassword"]);
                    if (count($updateDetails) > 0) {
                        foreach ($updateDetails as $Error) {
                            echo "<div class='errorMessage'>" . $Error . "</div>";
                        }
                    } else {
                        echo "<div class='errorMessage'>Your Account Password Has Successfully Been Updated</div><style> .errorMessage { color: #4F8A10 !important; text-align: center; } .errorsContainer { background-color: #DFF2BF; border-color: #4F8A10; } </style>";
                    }
                    echo '</div>';
                }
                ?>
                <div class="inputClass">
                    <input type="text" name="disabledFullName" value="<?php echo  getUser($_SESSION["Email"])["Full Name"] ?>" disabled>
                    <label for="disabledFullName">Full Name</label>
                </div>
                <div class="inputClass">
                    <input type="text" name="disabledEmailAddress" value=<?php echo $_SESSION["Email"] ?> disabled>
                    <label for="disabledEmailAddress">Email Address</label>
                </div>
                <div class="inputClass">
                    <input type="password" id="changePassword" name="changePassword" onfocus='changeBorderOnFocus("changePassword")' onblur='changeBorderOnBlur("changePassword")' value='<?php echo isset($_POST["changePassword"]) ? $_POST["changePassword"] : ''  ?>'>
                    <label for="changePassword">New Password</label>
                </div>
                <div class="inputClass">
                    <input type="password" id="confirmChangePassword" name="confirmChangePassword" onfocus='changeBorderOnFocus("confirmChangePassword")' onblur='changeBorderOnBlur("confirmChangePassword")' value='<?php echo isset($_POST["confirmChangePassword"]) ? $_POST["confirmChangePassword"] : ''  ?>'>
                    <label for="confirmChangePassword">Confirm New Password</label>
                </div>
                <input type="submit" value="Update Details" name="updateDetails">
                <p><a href="myTasks.php">Return To My Account</a></p>
            </form>
        </div>
        <div class="overlayContainer">
            <div class="Overlays">
                <div class="overlayPanel Right">
                    <h3 class="companyLogo">manager.</h3>
                    <p>If You Wish, You Can Change Your Account Password Here. Make Sure To Choose A Strong And Secure Password For Extra Protection</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    var passwordUpdated = <?php echo json_encode($passwordUpdated, JSON_HEX_TAG); ?>;
    if (passwordUpdated != null && passwordUpdated) {
        document.getElementById("changePassword").value = "";
        document.getElementById("confirmChangePassword").value = "";
    }
</script>