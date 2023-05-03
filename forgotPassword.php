<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>manager · Forgot Password</title>
    <link rel="stylesheet" href="CSS/styleAccount.css">
    <script src="eventHandlers.js"></script>
    <script src="https://kit.fontawesome.com/167b7ecae5.js" crossorigin="anonymous"></script>
    <?php
    include_once "managerFunctions.php";
    isUserLoggedIn();
    stageOfReset();
    resetHashValue();
    ?>
</head>

<body>
    <div class="Container" id="Container">
        <div class="formContainer containerLeft">
            <form method="post" accept-charset="utf-8">
                <h3>Forgot Password</h3>
                <h6>Reset Your Account Password</h6>
                <?php
                if (isset($_POST["forgotPassword"])) {
                    echo '<div class="errorsContainer">';
                    $forgottenPassword = forgotPassword(cleanUserInput($_POST["emailAddress"]));
                    if (count($forgottenPassword) > 0) {
                        foreach ($forgottenPassword as $Error) {
                            echo "<div class='errorMessage'>" . $Error . "</div>";
                        }
                    } else {
                        echo "<div class='errorMessage'>An Email Has Been Sent To Reset Your Password</div><style> .errorMessage { color: #4F8A10 !important; text-align: center; } .errorsContainer { background-color: #DFF2BF; border-color: #4F8A10; } </style>";
                    }
                    echo '</div>';
                }
                ?>
                <div class="inputClass">
                    <input name="emailAddress" type="text" id="emailAddress" onfocus='changeBorderOnFocus("emailAddress")' onblur='changeBorderOnBlur("emailAddress")' value='<?php echo isset($_POST["emailAddress"]) ? $_POST["emailAddress"] : ''  ?>'>
                    <label for="emailAddress">Email Address</label>
                </div>
                <input type="submit" value="Send Reset Link" name="forgotPassword" id="forgotPassword">
                <p><a href="Account.php">Return To Sign In</a></p>
            </form>
        </div>
        <div class="formContainer containerRight">
            <form method="post" accept-charset="utf-8">
                <h3>Reset Password</h3>
                <h6>Enter A New Password</h6>
                <?php
                if (isset($_POST["resetPassword"])) {
                    echo '<div class="errorsContainer">';
                    $resetPassword = resetPassword(cleanUserInput($_POST["newPassword"]), cleanUserInput($_POST["confirmNewPassword"]));
                    if (count($resetPassword) > 0) {
                        foreach ($resetPassword as $Error) {
                            echo "<div class='errorMessage'>" . $Error . "</div>";
                        }
                    }
                    echo '</div>';
                }
                ?>
                <div class="inputClass">
                    <input name="newPassword" type="password" id="newPassword" onfocus='changeBorderOnFocus("newPassword")' onblur='changeBorderOnBlur("newPassword")' value='<?php echo isset($_POST["newPassword"]) ? $_POST["newPassword"] : ''  ?>'>
                    <label for="newPassword">New Password</label>
                </div>
                <div class="inputClass">
                    <input name="confirmNewPassword" type="password" id="confirmNewPassword" onfocus='changeBorderOnFocus("confirmNewPassword")' onblur='changeBorderOnBlur("confirmNewPassword")' value='<?php echo isset($_POST["confirmNewPassword"]) ? $_POST["confirmNewPassword"] : ''  ?>'>
                    <label for="confirmNewPassword">Confirm New Password</label>
                </div>
                <input id="resetPassword" name="resetPassword" type="submit" value="Reset Password">
            </form>
        </div>
        <div class="overlayContainer">
            <div class="Overlays">
                <div class="overlayPanel Left">
                    <h3 class="companyLogo">manager.</h3>
                    <p>Choose A New Strong Password To Protect Your Account</p>
                </div>
                <div class="overlayPanel Right">
                    <h3 class="companyLogo">manager.</h3>
                    <p>A Link Will Be Sent To Your Email If You Have Have An Existing Account. Click That Link To Reset Your Account Password</p>
                    <i style="margin-top: 0.65em" class="fa-solid fa-house" onclick="window.location.href = 'Home.php'"></i>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    function forgottenPasswordDisplayForm() {
        var emailValid = <?php echo json_encode($emailValid, JSON_HEX_TAG); ?>;
        var resetHash = <?php echo json_encode($resetHash, JSON_HEX_TAG); ?>;
        if (resetHash != "") {
            document.getElementById('Container').classList.add('currentlyRight');
        }
        if (emailValid) {
            document.getElementById("emailAddress").value = "";
        }
    }
    forgottenPasswordDisplayForm();
</script>