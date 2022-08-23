<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>manager · Access Account</title>
    <link rel="stylesheet" href="CSS/styleAccount.css">
    <script src="eventHandlers.js"></script>
    <script src="https://kit.fontawesome.com/167b7ecae5.js" crossorigin="anonymous"></script>
    <?php
    include_once "managerFunctions.php";
    isUserLoggedIn();
    ?>
</head>

<body>
    <div class="Container" id="Container">
        <div class="formContainer containerLeft">
            <form method="post" accept-charset="utf-8" autocomplete="off">
                <h3>Welcome Back</h3>
                <h6>Sign In To Your Account</h6>
                <?php
                if (isset($_POST["logIn"])) {
                    echo '<div class="errorsContainer">';
                    $logInResult = logIn(cleanUserInput($_POST["existingEmail"]), cleanUserInput($_POST["existingPassword"]));
                    if ($logInResult != null && count($logInResult) > 0) {
                        foreach ($logInResult as $Error) {
                            echo "<div class='errorMessage'>" . $Error . "</div>";
                        }
                    }
                    echo '</div>';
                }
                if (isset($_SESSION["Activated"]) && ($_SESSION["Activated"])) {
                    echo '<div class="errorsContainer"><div class="errorMessage">Congratulations! Your Account Has Successfully Been Activated.</div></div><style> .errorMessage { color: #4F8A10 !important; text-align: center; } .errorsContainer { background-color: #DFF2BF; border-color: #4F8A10; } </style>';
                    unset($_SESSION["Activated"]);
                } else if (isset($_SESSION["Activated"]) && !($_SESSION["Activated"])) {
                    echo '<div class="errorsContainer"><div class="errorMessage" style="text-align: center;">Failed To Activate Account! Please Try Again Or Contact Us If The Issue Is Not Resolved.</div></div>';
                    unset($_SESSION["Activated"]);
                }
                if (isset($_SESSION["updatedPassword"]) && ($_SESSION["updatedPassword"])) {
                    echo '<div class="errorsContainer"><div class="errorMessage">Your Password Has Successfully Been Reset.</div></div><style> .errorMessage { color: #4F8A10 !important; text-align: center; } .errorsContainer { background-color: #DFF2BF; border-color: #4F8A10; } </style>';
                    unset($_SESSION["updatedPassword"]);
                } else if (isset($_SESSION["updatedPassword"]) && !($_SESSION["updatedPassword"])) {
                    echo '<div class="errorsContainer"><div class="errorMessage" style="text-align: center;">Failed To Reset Your Password! Please Try Again Or Contact Us If The Issue Is Not Resolved.</div></div>';
                    unset($_SESSION["updatedPassword"]);
                }
                ?>
                <div class="inputClass">
                    <input name="existingEmail" type="text" id="existingEmail" onfocus='changeBorderOnFocus("existingEmail")' onblur='changeBorderOnBlur("existingEmail")' value='<?php echo $_POST["existingEmail"] ?>'>
                    <label for="existingEmail">Email Address</label>
                </div>
                <div class="inputClass">
                    <input name="existingPassword" type="password" id="existingPassword" onfocus='changeBorderOnFocus("existingPassword")' onblur='changeBorderOnBlur("existingPassword")' value='<?php echo $_POST["existingPassword"] ?>'>
                    <label for="existingPassword">Password</label>
                </div>
                <input type="submit" value="Sign In" name="logIn" id="logIn">
                <p><a href="forgotPassword.php">Forgotten Your Password?</a></p>
                <?php logInWithGoogle() ?>
            </form>
        </div>
        <div class="formContainer containerRight">
            <form method="post" accept-charset="utf-8" autocomplete="off">
                <h3>Get Started</h3>
                <h6>Create A New Account</h6>
                <?php
                if (isset($_POST["signUp"])) {
                    echo '<div class="errorsContainer">';
                    $signUpResult = signUp(cleanUserInput($_POST["fullName"]), cleanUserInput($_POST["newEmail"]), cleanUserInput($_POST["newPassword"]), cleanUserInput($_POST["confirmNewPassword"]));
                    if (count($signUpResult) > 0) {
                        foreach ($signUpResult as $Error) {
                            echo "<div class='errorMessage'>" . $Error . "</div>";
                        }
                    } else {
                        echo "<div class='errorMessage'>Please Activate Your Account By Verifying The Email Sent</div><style> .errorMessage { color: #4F8A10 !important; text-align: center; } .errorsContainer { background-color: #DFF2BF; border-color: #4F8A10; } </style>";
                    }
                    echo '</div>';
                }
                ?>
                <div class="inputClass">
                    <input name="fullName" type="text" id="fullName" onfocus='changeBorderOnFocus("fullName")' onblur='changeBorderOnBlur("fullName")' value='<?php echo $_POST["fullName"] ?>'>
                    <label for="fullName">Full Name</label>
                </div>
                <div class="inputClass">
                    <input name="newEmail" type="text" id="newEmail" onfocus='changeBorderOnFocus("newEmail")' onblur='changeBorderOnBlur("newEmail")' value='<?php echo $_POST["newEmail"] ?>'>
                    <label for="newEmail">Email Address</label>
                </div>
                <div class="inputClass">
                    <input name="newPassword" type="password" id="newPassword" onfocus='changeBorderOnFocus("newPassword")' onblur='changeBorderOnBlur("newPassword")' value='<?php echo  $_POST["newPassword"] ?>'>
                    <label for="newPassword">Password</label>
                </div>
                <div class="inputClass">
                    <input name="confirmNewPassword" type="password" id="confirmNewPassword" onfocus='changeBorderOnFocus("confirmNewPassword")' onblur='changeBorderOnBlur("confirmNewPassword")' value='<?php echo $_POST["confirmNewPassword"] ?>'>
                    <label for="confirmNewPassword">Confirm Password</label>
                </div>
                <input id="signUp" name="signUp" type="submit" value="Sign Up">
            </form>
        </div>
        <div class="overlayContainer">
            <div class="Overlays">
                <div class="overlayPanel Left">
                    <h3 class="companyLogo">manager.</h3>
                    <p>Already Have An Account? <br> Sign In And Get Back To Learning And Working.</p>
                    <button onclick="changeForm('Container', 'currentlyRight')">Enter Account</button>
                    <message>By Clicking Sign Up, You Agree To Our Privacy Policy & Cookie Policy</message>
                </div>
                <div class="overlayPanel Right">
                    <h3 class="companyLogo">manager.</h3>
                    <p>Don't Have An Account? Get Started Today And Boost Your Productivity With Manager.</p>
                    <button onclick="changeForm('Container', 'currentlyRight')">Create Account</button>
                    <i class="fa-solid fa-house" onclick="window.location.href = 'Home.php'"></i>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    function signUpFailedOrSucceeded() {

        var signUpFailed = <?php echo json_encode($signUpFailed, JSON_HEX_TAG); ?>;
        var signUpSucceeded = <?php echo json_encode($signUpSucceeded, JSON_HEX_TAG); ?>;
        const Container = document.getElementById('Container');
        const SignUpBtn = document.getElementById('signUp');
        const LogInBtn = document.getElementById('logIn');

        if (signUpFailed || signUpSucceeded) {
            Container.classList.add('currentlyRight');
            SignUpBtn.addEventListener('click', () =>
                Container.classList.add('currentlyRight')
            );
            LogInBtn.addEventListener('click', () =>
                Container.classList.remove('currentlyRight')
            );
        } else {
            SignUpBtn.addEventListener('click', () =>
                Container.classList.add('currentlyRight')
            );
            LogInBtn.addEventListener('click', () =>
                Container.classList.remove('currentlyRight')
            );
        }

        if (signUpSucceeded) {
            document.getElementById("fullName").value = "";
            document.getElementById("newEmail").value = "";
            document.getElementById("newPassword").value = "";
            document.getElementById("confirmNewPassword").value = "";
        }

    }
    signUpFailedOrSucceeded();
</script>