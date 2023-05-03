<?php

session_start();
include_once "sendEmailFunctions.php";

$connectToDB = new mysqli('localhost', 'root', '', 'manager') or die("Connection Failed: " . $connectToDB->error);
$signUpFailed = $signUpSucceeded = $emailValid = $passwordUpdated = false;
$resetHash = "";


/**
 * Creates a new folder at the specified directory if it does not already exist.
 *
 * @param string $theDirectory The path to the directory to create the folder in.
 * 
 * @return void
 */
function createFolder(String $theDirectory): void
{
    if (!is_dir($theDirectory)) {
        mkdir($theDirectory, 0777, true);
    }
}


/**
 * Redirects the user to the "Account.php" page if the "Email" session variable is not set.
 *
 * @return void
 */
function redirectUser(): void
{
    if (!isset($_SESSION["Email"])) {
        header("Location: Account.php");
    }
}


/**
 * Redirects the user to the "myTasks.php" page if the "Email" session variable is set instead of showing the login page.
 *
 * @return void
 */
function isUserLoggedIn(): void
{
    if (isset($_SESSION["Email"])) {
        header("Location: myTasks.php");
    }
}


/**
 * Cleans the user input by removing leading and trailing white space, backslashes, and special HTML characters.
 *
 * @param string $Data The user input to be cleaned.
 * 
 * @return string The cleaned user input.
 */
function cleanUserInput(string $Data): string
{
    return stripslashes(trim(htmlspecialchars($Data)));
}


/**
 * Logs in the user with Google authentication and redirects them to the appropriate page.
 *
 * @global $connectToDB The global database connection object.
 * 
 * @return void
 */
function logInWithGoogle(): void
{
    global $connectToDB;
    require_once 'vendor/autoload.php';
    $googleClient = new Google_Client();
    $googleClient->setClientId('63739942999-phs0v5qec0q87f970glblhrkd1fvimqb.apps.googleusercontent.com');
    $googleClient->setClientSecret('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');
    $googleClient->setRedirectUri('http://localhost/manager/Account.php');
    $googleClient->addScope('email');
    $googleClient->addScope('profile');

    if (!isset($_GET["code"])) {
        echo "<h2><span>OR</span></h2><a id='googleLogInBtn' href=" . $googleClient->createAuthUrl() . "><img src='Images/signInWithGoogle.png' alt='Sign In With Google Button' /></a>";
    } else {
        $getToken = $googleClient->fetchAccessTokenWithAuthCode($_GET["code"]);
        if (isset($getToken["error"])) {
            header("Location: Account.php");
        }
        $googleClient->setAccessToken($getToken["access_token"]);
        $getProfile = new Google_Service_Oauth2($googleClient);
        $googleInfo = $getProfile->userinfo->get();
        $fullName = $googleInfo["given_name"] . " " . $googleInfo["family_name"];
        $userEmail = $googleInfo["email"];
        $getUserIfExists = mysqli_query($connectToDB, "SELECT * FROM Users WHERE Email = '$userEmail' LIMIT 1");
        if (mysqli_num_rows($getUserIfExists)  == 1) {
            while ($Row = mysqli_fetch_assoc($getUserIfExists)) {
                $_SESSION["Email"] = $userEmail;
                header("Location: myTasks.php");
                if ($Row["Activated"] == 'N') {
                    mysqli_query($connectToDB, "UPDATE Users SET Activated = 'Y' WHERE Email = '$userEmail'");
                }
            }
        } else {
            $Hash = getHash();
            if (mysqli_query($connectToDB, "INSERT INTO Users VALUES('$fullName', '$userEmail', '', '$Hash', 'Y', 'Y')")) {
                $_SESSION["Email"] = $userEmail;
                header("Location: myTasks.php");
            }
        }
    }
}


/**
 * Attempts to log in the user using the provided email address and password.
 *
 * @param string $Email The email address of the user.
 * @param string $Password The password of the user.
 * 
 * @return array|null Returns an array of error messages if there were any login errors,
 *                    or null if the login was successful.
 */
function logIn(string $Email, string $Password): ?array
{
    global $connectToDB;
    $logInErrors = array();

    if (empty($Email)) {
        array_push($logInErrors, "Please Enter Your Email Address");
        echo "<style> input[name='existingEmail'] { border-color: #E74C3C !important } </style>";
    } else {
        echo '<style> label[for="existingEmail"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (empty($Password)) {
        array_push($logInErrors, "Please Enter Your Password");
        echo "<style> input[name='existingPassword'] { border-color: #E74C3C !important } </style>";
    } else {
        echo '<style> label[for="existingPassword"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (count($logInErrors) == 0) {
        $getUser = mysqli_query($connectToDB, "SELECT Email, Password, Activated FROM Users WHERE Email = '$Email' LIMIT 1");
        if (mysqli_num_rows($getUser) == 1) {
            $queryResult = mysqli_fetch_array($getUser, MYSQLI_ASSOC);
            if (password_verify($Password, $queryResult["Password"])) {
                if ($queryResult["Activated"] == 'Y') {
                    $_SESSION["Email"] = $queryResult["Email"];
                    header("Location: myTasks.php");
                    return null;
                } else {
                    array_push($logInErrors, "Please Activate Your Account Before Signing In");
                }
            } else {
                array_push($logInErrors, "Email Address Or Password Is Invalid");
                echo "<style> input[name='existingEmail'], input[name='existingPassword'] { border-color: #E74C3C !important } </style>";
            }
        } else {
            array_push($logInErrors, "Email Address Or Password Is Invalid");
            echo "<style> input[name='existingEmail'], input[name='existingPassword'] { border-color: #E74C3C !important } </style>";
        }
    }

    return $logInErrors;
}


/**
 * Validates user input for signing up, inserts a new user into the database if validation succeeds, and returns an array of error messages if validation fails.
 *
 * @param string $Name The user's full name.
 * @param string $Email The user's email address.
 * @param string $Password The user's chosen password.
 * @param string $confirmPassword The user's confirmation of their chosen password.
 *
 * @return array An array of error messages, empty if validation succeeds.
 */
function signUp(string $Name, string $Email, string $Password, string $confirmPassword): array
{
    global $connectToDB, $signUpFailed, $signUpSucceeded;
    $signUpErrors = array();

    if (empty($Name)) {
        array_push($signUpErrors, "Please Enter Your Full Name");
        echo "<style> input[name='fullName'] { border-color: #E74C3C !important } </style>";
    } else {
        $invalidChars = "/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i";
        if (preg_match($invalidChars, $Name)) {
            array_push($signUpErrors, "Your Name Contains Invalid Characters");
            echo "<style> input[name='fullName'] { border-color: #E74C3C !important } </style>";
        }
        echo '<style> label[for="fullName"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (empty($Email)) {
        array_push($signUpErrors, "Please Enter Your Email Address");
        echo "<style> input[name='newEmail'] { border-color: #E74C3C !important } </style>";
    } else {
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            array_push($signUpErrors, "Your Email Address Is Invalid");
            echo "<style> input[name='newEmail'] { border-color: #E74C3C !important } </style>";
        }
        if (mysqli_num_rows(mysqli_query($connectToDB, "SELECT * FROM Users WHERE Email = '$Email' LIMIT 1")) == 1) {
            array_push($signUpErrors, "An Account With This Email Already Exists");
            echo "<style> input[name='newEmail'] { border-color: #E74C3C !important } </style>";
        }
        echo '<style> label[for="newEmail"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (empty($Password)) {
        array_push($signUpErrors, "Please Enter Your Password");
        echo "<style> input[name='newPassword'] { border-color: #E74C3C !important } </style>";
    } else {
        $upperCaseLetter = preg_match('@[A-Z]@', $Password);
        $lowerCaseLetter = preg_match('@[a-z]@', $Password);
        $Number = preg_match('@[0-9]@', $Password);
        if (strlen($Password) < 8 || !$upperCaseLetter || !$lowerCaseLetter || !$Number) {
            array_push($signUpErrors, "Password Must Be At Least 8 Characters Length With At Least One Number, One Upper Case And One Lower Case Letter");
            echo "<style> input[name='newPassword'] { border-color: #E74C3C !important } </style>";
        }
        echo '<style> label[for="newPassword"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (empty($confirmPassword)) {
        array_push($signUpErrors, "Please Confirm Your Password");
        echo "<style> input[name='confirmNewPassword'] { border-color: #E74C3C !important } </style>";
    } else {
        if ($confirmPassword != $Password) {
            array_push($signUpErrors, "Both Passwords Don't Match");
            echo "<style> input[name='newPassword'] { border-color: #E74C3C !important } </style>";
            echo "<style> input[name='confirmNewPassword'] { border-color: #E74C3C !important } </style>";
        }
        echo '<style> label[for="confirmNewPassword"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (count($signUpErrors) == 0) {
        $Password = password_hash($Password, PASSWORD_BCRYPT);
        $Hash = getHash();
        if (mysqli_query($connectToDB, "INSERT INTO Users VALUES('$Name', '$Email', '$Password', '$Hash', 'N', 'N')")) {
            $signUpSucceeded = true;
            $_SESSION["Hash"] = $Hash;
            $_SESSION["fullName"] = $Name;
            if (!sendVerificationEmail($Email, $_SESSION["fullName"])) {
                array_push($signUpErrors, "Error: Failed To Create Your Account");
            }
        } else {
            array_push($signUpErrors, "Error: Failed To Create Your Account");
        }
    }

    if (count($signUpErrors) != 0) {
        $signUpFailed = true;
    }

    return $signUpErrors;
}


/**
 * Activates a new user account by updating the corresponding record in the database and setting
 * the user's activation status to "Y". Also sets a new hash for the user's account and updates
 * the session variable accordingly. Redirects the user to their account page.
 *
 * @param string $hashCode The activation hash associated with the user's account
 *
 * @return void
 */
function activateNewAccount(string $hashCode): void
{
    global $connectToDB;
    $Hash = getHash();
    mysqli_query($connectToDB, "UPDATE Users SET Activated = 'Y', Hash = '$Hash' WHERE Hash = '$hashCode'");
    if (mysqli_affected_rows($connectToDB) > 0) {
        $_SESSION["Activated"] = true;
    } else {
        $_SESSION["Activated"] = false;
    }
    header("Location: Account.php");
}


/**
 * This function handles the forgot password process, sending a reset password email if the user's email exists and their account is activated.
 *
 * @param string $emailAddress The user's email address.
 * 
 * @return array The array of errors, if any occurred during the forgot password process.
 */
function forgotPassword(string $emailAddress): array
{
    global $connectToDB, $emailValid;
    $forgotPasswordErrors = array();

    if (empty($emailAddress)) {
        array_push($forgotPasswordErrors, "Please Enter Your Email Address");
        echo "<style> input[name='emailAddress'] { border-color: #E74C3C !important } </style>";
        return $forgotPasswordErrors;
    }

    $accountExists = mysqli_query($connectToDB, "SELECT * FROM Users WHERE Email = '$emailAddress' LIMIT 1");
    if (mysqli_num_rows($accountExists) != 1) {
        array_push($forgotPasswordErrors, "An Account With This Email Does Not Exist");
        echo "<style> input[name='emailAddress'] { border-color: #E74C3C !important } label[for='emailAddress'] { font-size: 0.65em; top: 10px; opacity: 0.5; }</style>";
    } else {
        $queryResult = mysqli_fetch_array($accountExists, MYSQLI_ASSOC);
        if ($queryResult["Activated"] != "Y") {
            array_push($forgotPasswordErrors, "You Cannot Reset Your Password As You Haven't Activated Your Account. Please Contact Us To Resolve This Issue");
            echo '<style> label[for="emailAddress"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
        } else {
            $Hash = getHash();
            $_SESSION["newHash"] = $Hash;
            if (!sendResetPasswordEmail($queryResult["Email"], $queryResult["Name"], $_SESSION["newHash"])) {
                array_push($forgotPasswordErrors, "Failed To Send Reset Password Email! Please Try Again");
                echo '<style> label[for="emailAddress"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
            } else {
                $emailValid = true;
            }
        }
    }

    return $forgotPasswordErrors;
}


/**
 * This function checks the reset password hash provided in the URL against the database to verify it is valid. If it is, it sets the reset password hash in the session and redirects to the forgotPassword.php page.

 * @return void
 */
function stageOfReset(): void
{
    global $connectToDB;
    $Hash = $_GET["resetPassword"];
    if (isset($Hash) && !empty($Hash) && (mysqli_num_rows(mysqli_query($connectToDB, "SELECT * FROM Users WHERE Hash = '$Hash' LIMIT 1")) == 1)) {
        $_SESSION["resetPassword"] = $Hash;
        header("Location: forgotPassword.php");
    }
}


/**
 * This function handles the reset password process, updating the user's password if the new password and confirm password fields match and meet the required criteria.
 *
 * @param string $Password The new password.
 * @param string $confirmPassword The confirmed new password.
 * 
 * @return array The array of errors, if any occurred during the reset password process.
 */
function resetPassword(string $Password, string $confirmPassword): array
{
    global $connectToDB, $passwordUpdated, $resetHash;
    $resetPasswordErrors = array();

    if (empty($Password)) {
        array_push($resetPasswordErrors, "Please Enter Your New Password");
        echo "<style> input[name='changePassword'] { border-color: #E74C3C !important } </style>";
        echo "<style> input[name='newPassword'] { border-color: #E74C3C !important } </style>";
    } else {
        $upperCaseLetter = preg_match('@[A-Z]@', $Password);
        $lowerCaseLetter = preg_match('@[a-z]@', $Password);
        $Number = preg_match('@[0-9]@', $Password);
        if (strlen($Password) < 8 || !$upperCaseLetter || !$lowerCaseLetter || !$Number) {
            array_push($resetPasswordErrors, "Password Must Be At Least 8 Characters Length With At Least One Number, One Upper Case And One Lower Case Letter");
            echo "<style> input[name='changePassword'] { border-color: #E74C3C !important } </style>";
            echo "<style> input[name='newPassword'] { border-color: #E74C3C !important } </style>";
        }
        echo '<style> label[for="changePassword"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
        echo '<style> label[for="newPassword"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (empty($confirmPassword)) {
        array_push($resetPasswordErrors, "Please Confirm Your New Password");
        echo "<style> input[name='confirmChangePassword'] { border-color: #E74C3C !important } </style>";
        echo "<style> input[name='confirmNewPassword'] { border-color: #E74C3C !important } </style>";
    } else {
        if ($confirmPassword != $Password) {
            array_push($resetPasswordErrors, "Both Passwords Don't Match");
            echo "<style> input[name='changePassword'] { border-color: #E74C3C !important } </style>";
            echo "<style> input[name='confirmChangePassword'] { border-color: #E74C3C !important } </style>";
            echo "<style> input[name='newPassword'] { border-color: #E74C3C !important } </style>";
            echo "<style> input[name='confirmNewPassword'] { border-color: #E74C3C !important } </style>";
        }
        echo '<style> label[for="confirmChangePassword"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
        echo '<style> label[for="confirmNewPassword"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (count($resetPasswordErrors) == 0) {
        $Password = password_hash($Password, PASSWORD_BCRYPT);
        $Hash = getHash();
        if ($_SERVER["REQUEST_URI"] == "/manager/Settings.php") {
            $Email = $_SESSION["Email"];
            mysqli_query($connectToDB, "UPDATE Users SET Password = '$Password', Hash = '$Hash' WHERE Email = '$Email'");
        } else if ($_SERVER["REQUEST_URI"] == "/manager/forgotPassword.php") {
            mysqli_query($connectToDB, "UPDATE Users SET Password = '$Password', Hash = '$Hash' WHERE Hash = '$resetHash'");
        }
        if (mysqli_affected_rows($connectToDB) > 0) {
            $_SESSION["updatedPassword"] = true;
            unset($_SESSION["resetPassword"]);
            if ($_SERVER["REQUEST_URI"] == "/manager/Settings.php") {
                $passwordUpdated = true;
                return $resetPasswordErrors;
            }
            header("Location: Account.php");
        } else {
            $_SESSION["updatedPassword"] = false;
        }
    }

    return $resetPasswordErrors;
}


/**
 * Generate a unique hash for a user.
 *
 * @global mysqli $connectToDB The database connection.
 *
 * @return string A unique hash value.
 */
function getHash(): string
{
    global $connectToDB;
    $Hash = md5(uniqid(rand(), true));
    while (mysqli_num_rows(mysqli_query($connectToDB, "SELECT * FROM Users WHERE Hash = '$Hash'")) > 0) {
        $Hash = md5(uniqid(rand(), true));
    }
    return $Hash;
}


/**
 * Set the global $resetHash variable to the value stored in the $_SESSION array.
 *
 * @global string $resetHash The reset password hash value.
 * 
 * @return void
 */
function resetHashValue(): void
{
    global $resetHash;
    if (isset($_SESSION["resetPassword"])) {
        $resetHash = $_SESSION["resetPassword"];
    }
}


/**
 * Log out the current user by destroying the session and un-setting the "Email" $S_SESSION variable.
 * Redirects the user to "Home.php" if they were logged in, otherwise redirects them to "Account.php".
 * 
 * @return void
 */
function logOut(): void
{
    if (isset($_SESSION["Email"])) {
        session_destroy();
        unset($_SESSION["Email"]);
        header("Location: Home.php");
    } else {
        header("Location: Account.php");
    }
}


/**
 * Get user data from the database for the given email address.
 *
 * @global mysqli $connectToDB The database connection.
 *
 * @param string $Email The email address of the user to retrieve.
 *
 * @return array|null An associative array of user data, or null if the user was not found.
 */
function getUser(string $Email): ?array
{
    global $connectToDB;
    return mysqli_fetch_array(mysqli_query($connectToDB, "SELECT * FROM Users WHERE Email = '$Email'"), MYSQLI_ASSOC);
}


/**
 * Validate and process the user-submitted contact form data.
 *
 * @param string $myName The user's name from the contact form.
 * @param string $myEmail The user's email address from the contact form.
 * @param string $messageSubject The subject of the user's message from the contact form.
 * @param string $messageContent The content of the user's message from the contact form.
 *
 * @return array An array of errors, or an empty array if there were no errors.
 */
function contactUs(String $myName, String $myEmail, String $messageSubject, String $messageContent): array
{
    $contactFormErrors = array();

    if (empty($myName)) {
        array_push($contactFormErrors, "Please Enter Your Full Name");
        echo "<style> input[name='myName'] { border-color: #E74C3C !important } </style>";
    } else {
        $invalidChars = "/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i";
        if (preg_match($invalidChars, $myName)) {
            array_push($contactFormErrors, "Your Name Contains Invalid Characters");
            echo "<style> input[name='myName'] { border-color: #E74C3C !important } </style>";
        }
        echo '<style> label[for="myName"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (empty($myEmail)) {
        array_push($contactFormErrors, "Please Enter Your Email Address");
        echo "<style> input[name='myEmail'] { border-color: #E74C3C !important } </style>";
    } else {
        if (!filter_var($myEmail, FILTER_VALIDATE_EMAIL)) {
            array_push($contactFormErrors, "Your Email Address Is Invalid");
            echo "<style> input[name='myEmail'] { border-color: #E74C3C !important } </style>";
        }
        echo '<style> label[for="myEmail"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (empty($messageSubject)) {
        array_push($contactFormErrors, "Please Enter The Subject Of The Message");
        echo "<style> input[name='messageSubject'] { border-color: #E74C3C !important } </style>";
    } else {
        echo '<style> label[for="messageSubject"] { font-size: 0.65em; top: 10px; opacity: 0.5; } </style>';
    }

    if (empty($messageContent)) {
        array_push($contactFormErrors, "Please Enter The Message");
        echo "<style> textarea[name='messageContent'] { border-color: #E74C3C !important } </style>";
    } else {
        echo '<style> label[for="messageContent"] { font-size: 0.65em; top: 8.875px; opacity: 0.5; } </style>';
    }

    if (count($contactFormErrors) == 0) {
        $_SESSION["myName"] = $myName;
        $_SESSION["myEmail"] = $myEmail;
        $_SESSION["messageContent"] = $messageContent;
        if (!sendContactUsEmail($myName, $messageSubject)) {
            array_push($contactFormErrors, "Failed To Send Message! Please Try Again Later.");
        }
    }

    return $contactFormErrors;
}


/**
 * Formats the last updated time of a note
 *
 * @param String $lastUpdated the last updated time of the note
 * 
 * @return string the formatted last updated time
 */
function timeNoteLastUpdated(String $lastUpdated): string
{
    $displayLastUpdated = null;
    date_default_timezone_set('Europe/London');
    $timeDiffInMinutes = round((time() - strtotime($lastUpdated)) / 60);
    if ($timeDiffInMinutes > 10080) {
        $displayLastUpdated = substr($lastUpdated, 0, 10);
    } elseif ($timeDiffInMinutes > 1440) {
        $timeDifferenceDays = substr(($timeDiffInMinutes / 1440), 0, 1);
        $displayLastUpdated = $timeDifferenceDays . "d";
    } elseif ($timeDiffInMinutes >= 60) {
        if (($timeDiffInMinutes / 60) >= 10) {
            $displayLastUpdated = substr(($timeDiffInMinutes / 60), 0, 2) . "h";
        } else {
            $displayLastUpdated = substr(($timeDiffInMinutes / 60), 0, 1) . "h";
        }
    } else {
        $displayLastUpdated = $timeDiffInMinutes . "m";
    }
    return $displayLastUpdated;
}


/**
 * Encrypts a value using AES-128-CBC encryption
 *
 * @param String $valueToEncrypt the value to encrypt
 * @param String $randomKey the random key used for encryption
 * @param String $randomNum the random number used for encryption
 * 
 * @return string the encrypted value
 */
function encryptNoteKey(String $valueToEncrypt, String $randomKey, String $randomNum): string
{
    return openssl_encrypt($valueToEncrypt, 'AES-128-CBC', $randomKey, $options = 0, $randomNum);
}


/**
 * Decrypts a note key using AES-128-CBC encryption
 *
 * @param int $arrayPosition the position of the encrypted note key in the array
 * @param array $encryptionDetails the encryption details
 * 
 * @return string the decrypted note key
 */
function decryptNoteKey(int $arrayPosition, array $encryptionDetails): string
{
    return openssl_decrypt($encryptionDetails[$arrayPosition][0], 'AES-128-CBC', $encryptionDetails[$arrayPosition][1], $options = 0, $encryptionDetails[$arrayPosition][2]);
}


/**
 * Stores the encrypted keys for the notes of a specific user in a session variable
 *
 * @param String $Email the email address of the user
 * 
 * @return void
 */
function storeEncryptedKeys(String $Email): void
{
    global $connectToDB;
    $encryptionDetails = array();
    $getNotes = mysqli_query($connectToDB, "SELECT NoteID FROM Notes WHERE Email = '$Email' ORDER BY LastUpdated DESC");
    if ($getNotes && mysqli_num_rows($getNotes) > 0) {
        while ($Row = mysqli_fetch_assoc($getNotes)) {
            $randomKey = random_bytes(16);
            $randomNum = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-128-CBC'));
            $encryptedKey = encryptNoteKey($Row["NoteID"], $randomKey, $randomNum);
            array_push($encryptionDetails, array($encryptedKey, $randomKey, $randomNum));
        }
    }
    $_SESSION["encryptionDetails"] = $encryptionDetails;
}


/**
 * Adds a new note to the database. If a note with the same note ID already exists, update the existing note.
 * 
 * @param string $noteTitle The title of the note
 * @param string $Note The note to be added
 * @param string $bgColor The background color of the note. If not provided, the default color will be used.
 * 
 * @return void
 */
function addNewNote(String $noteTitle, String $Note, String $bgColor): void
{
    global $connectToDB;

    if (empty($bgColor)) {
        $bgColor = "#242526";
    }

    $bgColor = strtoupper($bgColor);

    date_default_timezone_set('Europe/London');
    $currentDateTime = date('Y-m-d H:i:s');
    $encryptedNote = encryptNote($Note);
    $myEmail = $_SESSION["Email"];
    if (isset($_SESSION['noteID'])) {
        $getID = $_SESSION['noteID'];
        if (mysqli_query($connectToDB, "UPDATE encryptNoteDetails SET encryptKey = '$encryptedNote[0]', encryptIV = '$encryptedNote[1]' WHERE NoteID ='$getID'")) {
            if (mysqli_query($connectToDB, "UPDATE Notes SET Title = '$noteTitle', Note = '$encryptedNote[2]', LastUpdated = '$currentDateTime', Background = '$bgColor' WHERE NoteID = '$getID'")) {
                unset($_SESSION['noteID']);
                header("Location: myTasks.php");
            }
        }
    } else {
        if (mysqli_query($connectToDB, "INSERT INTO Notes (Title, Note, LastUpdated, Background, Email) VALUES ('$noteTitle', '$encryptedNote[2]', '$currentDateTime', '$bgColor', '$myEmail')")) {
            $getNoteID = mysqli_insert_id($connectToDB);
            if (mysqli_query($connectToDB, "INSERT INTO encryptNoteDetails (NoteID, encryptKey, encryptIV) VALUES ('$getNoteID', '$encryptedNote[0]', '$encryptedNote[1]')")) {
                header("Location: myTasks.php");
            }
        }
    }
}


/**
 * Retrieves all notes belonging to the logged in user.
 * 
 * @param string $Email The email address of the user whose notes are to be retrieved.
 * @param array $encryptionDetails An array containing the encryption details of the notes.
 * 
 * @return void
 */
function getNotes(String $Email, array $encryptionDetails): void
{
    global $connectToDB;
    $getNotes = mysqli_query($connectToDB, "SELECT Notes.NoteID, Notes.Title, Notes.Note, Notes.LastUpdated, Notes.Background, encryptNoteDetails.encryptKey, encryptNoteDetails.encryptIV FROM Notes INNER JOIN encryptNoteDetails ON Notes.NoteID = encryptNoteDetails.NoteID WHERE Notes.Email = '$Email' ORDER BY LastUpdated DESC");
    if ($getNotes && mysqli_num_rows($getNotes) > 0) {
        $X = 0;
        echo "<div class=notesControl>";
        while ($Row = mysqli_fetch_assoc($getNotes)) {
            $decryptedNote = decryptNote($Row["Note"], $Row["encryptKey"], $Row["encryptIV"]);
            echo '<div id=myNote style=background:' . $Row['Background'] . '><p class=noteTitle>' . $Row['Title'] . '<span class=lastUpdated>' . timeNoteLastUpdated($Row["LastUpdated"]) . '</span></p><p class=theNote>' . nl2br($decryptedNote) . '</p><form method=post><button type=submit name=editNote class=editNote value=' . $encryptionDetails[$X][0] . '><i class="fa-solid fa-keyboard"></i>Edit</button><button type=submit class=deleteNote value=' . $encryptionDetails[$X][0] . ' name=deleteNote><i class="fa-solid fa-delete-left"></i>Delete</button></form></div>';
            $X++;
        }
        echo "</div>";
    } else {
        echo "<div id=emptyNotes>Create Your First Note <i class='fa-solid fa-note-sticky'></i></div>";
    }
}


/**
 * Deletes a note from the database.
 * 
 * @param string $btnValue The ID of the note to be deleted.
 * @param array $encryptionDetails An array containing the encryption details of the notes.
 * 
 * @return void
 */
function deleteNote(String $btnValue, array $encryptionDetails): void
{
    global $connectToDB;
    for ($X = 0; $X < count($encryptionDetails); $X++) {
        if ($encryptionDetails[$X][0] == $btnValue) {
            $decryptedKey = decryptNoteKey($X, $encryptionDetails);
            mysqli_query($connectToDB, "DELETE FROM Notes WHERE NoteID = '$decryptedKey'");
            header("Location: myTasks.php");
        }
    }
}


/**
 * Encrypts a given note using AES-128-CBC encryption.
 * 
 * @param string $valueToEncrypt The value to be encrypted.
 * 
 * @return array An array containing the encryption key, initialization vector, and encrypted text.
 */
function encryptNote(String $valueToEncrypt): array
{
    $randomKey = random_bytes(16);
    $randomIV = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-128-CBC'));
    $cipherText = openssl_encrypt($valueToEncrypt, 'AES-128-CBC', $randomKey, $options = 0, $randomIV);
    return array($randomKey, $randomIV, $cipherText);
}


/**
 * Decrypts a given encrypted note using AES-128-CBC decryption.
 * 
 * @param string $valueToDecrypt The value to be decrypted.
 * @param string $randomKey The encryption key.
 * @param string $randomIV The initialization vector.
 * 
 * @return string The decrypted note.
 */
function decryptNote(String $valueToDecrypt, String $randomKey, String $randomIV): string
{
    return openssl_decrypt($valueToDecrypt, 'AES-128-CBC', $randomKey, $options = 0, $randomIV);
}


/**
 * Retrieves a single note from the database for editing.
 * 
 * @param string $noteID The ID of the note to be retrieved.
 * @param array $encryptionDetails An array containing the encryption details of the notes.
 * 
 * @return array An array containing the note details.
 */
function getEditingNote(String $noteID, array $encryptionDetails): array
{
    global $connectToDB;
    for ($X = 0; $X < count($encryptionDetails); $X++) {
        if ($encryptionDetails[$X][0] == $noteID) {
            $noteID = decryptNoteKey($X, $encryptionDetails);
            $getNote =  mysqli_query($connectToDB, "SELECT Notes.NoteID, Notes.Title, Notes.Note, Notes.Background, encryptNoteDetails.encryptKey, encryptNoteDetails.encryptIV FROM Notes INNER JOIN encryptNoteDetails ON Notes.NoteID = encryptNoteDetails.NoteID WHERE Notes.NoteID = '$noteID'");
            if (mysqli_num_rows($getNote) > 0) {
                while ($Row = mysqli_fetch_assoc($getNote)) {
                    $decryptedNote = decryptNote($Row["Note"], $Row["encryptKey"], $Row["encryptIV"]);
                    $_SESSION["noteID"] = $noteID;
                    return array($Row["Title"], $decryptedNote, $Row["Background"]);
                }
            }
        }
    }
}


/**
 * Add a task to the database for the user.
 *
 * @param string $Title The title of the task.
 * @param string $dateTime The date and time the task is scheduled for.
 * @param string $Description The description of the task.
 * @param string $Priority The priority level of the task.
 *
 * @return void
 */
function addTask(String $Title, String $dateTime, String $Description, String $Priority): void
{
    global $connectToDB;
    date_default_timezone_set('Europe/London');
    $dateTime = str_replace("T", " ", $dateTime);
    $currentDateTime = date("Y-m-d H:i");
    $myEmail = $_SESSION["Email"];
    if ($dateTime > $currentDateTime) {
        if (mysqli_query($connectToDB, "INSERT INTO Tasks (Title, Scheduled, Description, Priority, Email) VALUES ('$Title', '$dateTime', '$Description', '$Priority', '$myEmail')")) {
            header("Location: myTasks.php");
        }
    }
}


/**
 * Get the next upcoming task for the given email address.
 *
 * @param string $myEmail The email address to search for.
 *
 * @return array|null The next upcoming task as an associative array, or null if there are no upcoming tasks.
 */
function upComingTask(String $myEmail): ?array
{
    global $connectToDB;
    return mysqli_fetch_array(mysqli_query($connectToDB, "SELECT * FROM Tasks WHERE Email = '$myEmail' AND Scheduled > Now() ORDER BY Scheduled ASC LIMIT 1"), MYSQLI_ASSOC);
}


/**
 * Convert a task priority string to a visual representation.
 *
 * @param string $Priority The priority level of the task.
 * @param int $Count The count of the task to add the style to.
 *
 * @return string The visual representation of the priority.
 */
function convertPriority(String $Priority, int $Count): string
{
    if ($Priority == "Low") {
        $Priority = "!";
        echo "<style> span[count='$Count'] { color: green } </style>";
    } else if ($Priority == "Medium") {
        $Priority = "!!";
        echo "<style> span[count='$Count'] { color: orange } </style>";
    } else {
        $Priority = "!!!";
        echo "<style> span[count='$Count'] { color: red } </style>";
    }
    return $Priority;
}


/**
 * Print all tasks associated with the given email address.
 *
 * @param string $myEmail The email address to search for.
 *
 * @return void
 */
function allTasks(String $myEmail): void
{
    global $connectToDB;
    $Count = 1;
    $getTasks = mysqli_query($connectToDB, "SELECT * FROM Tasks WHERE Email = '$myEmail' ORDER BY Scheduled ASC");

    while ($Row = mysqli_fetch_assoc($getTasks)) {
        list($Date, $Time) = explode(" ", $Row["Scheduled"]);
        if ($Time >= "12:00:00") {
            $timeOfDay = "pm";
        } else {
            $timeOfDay = "am";
        }
        $Time = date("h:i", strtotime($Time));
        echo "<p id=upcomingTitle><span id=upcomingPriority count=$Count>" . convertPriority($Row["Priority"], $Count) . "</span>" . $Row["Title"] . "</p><p id=upcomingDate>" . date("d-m-Y", strtotime($Date)) . " at " . substr($Time, 0, 5) . $timeOfDay . "</p><p id=upcomingDescription>" . $Row["Description"] . "</p><br>";
        $Count++;
    }
}


/**
 * Upload a file to the server and add it to the database.
 *
 * @param array $theFile The file data from the $_FILES super-global.
 * @param string $Email The email address associated with the file.
 *
 * @return array|null An array of error messages if the upload failed, or null if successful.
 */
function uploadFile($theFile, String $Email): ?array
{
    global $connectToDB;

    $uploadError = array();

    $fileName = $theFile["name"];
    $fileSize = $theFile["size"];
    $fileTmpName = $theFile["tmp_name"];
    $fileType = $theFile["type"];
    $fileError = $theFile["error"];

    $fileSizeGB = round(($fileSize / (1024 * 1024 * 1024)), 2);
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'pdf');

    if (in_array($fileActualExt, $allowedExtensions)) {
        if ($fileError != 0) {
            array_push($uploadError, "An Error Occurred Uploading The File");
        }
        if ($fileSize > 52428800) {
            array_push($uploadError, "File Cannot Be Greater Than 50MB");
        }
        if (($_SESSION["StorageUsed"] + $fileSizeGB) > 10) {
            array_push($uploadError, "You Have Reached Your Limit Of 10GB Free Storage");
        }
    } else {
        array_push($uploadError, "You Cannot Upload Files Of This Type");
    }

    if (count($uploadError) == 0) {
        if (mysqli_num_rows(mysqli_query($connectToDB, "SELECT * FROM Files WHERE FileName = '$fileName' AND Email = '$Email'")) > 0) {
            array_push($uploadError, "You Have Already Uploaded A File With This Name");
            return $uploadError;
        }
        $getUniqueCode = uniqid('', true);
        $fileNameNew = $getUniqueCode . "." . $fileActualExt;
        $fileDestination = "fileUploads/" . $fileNameNew;
        copy($fileTmpName, $fileDestination);
        if (mysqli_query($connectToDB, "INSERT INTO Files(FileName, OriginalFileName, FileSize, FileType, Email) VALUES ('$fileNameNew','$fileName','$fileSize','$fileType','$Email')")) {
            header("Location: myFiles.php");
            return null;
        } else {
            array_push($uploadError, "An Error Occurred Uploading The File");
        }
    }

    return $uploadError;
}


/**
 * Get all uploaded files associated with the given email address.
 *
 * @param string $Email The email address to search for.
 *
 * @return void
 */
function getUploadedFiles(String $Email): void
{
    global $connectToDB;
    $TotalSizeInGB = 0;
    $getFiles = mysqli_query($connectToDB, "SELECT * FROM Files WHERE Email = '$Email'");
    if (mysqli_num_rows($getFiles) > 0) {
        while ($Row = mysqli_fetch_assoc($getFiles)) {
            $FileSize = round(($Row["FileSize"] / (1024 * 1024)), 2);
            $TotalSizeInGB += $FileSize;
            $fileNameWithoutExtension = str_replace('.pdf', '', $Row["FileName"]);
            $fileNameWithoutExtension = str_replace('.jpg', '', $fileNameWithoutExtension);
            $fileNameWithoutExtension = str_replace('.jpeg', '', $fileNameWithoutExtension);
            $fileNameWithoutExtension = str_replace('.png', '', $fileNameWithoutExtension);
            echo "<div class='File'><button onclick=window.location.href='myFiles.php?id=$fileNameWithoutExtension' class='FileName'>" . $Row["OriginalFileName"] . "</button><div class=statsAndButtons><span class='FileSize'>" . $FileSize . "MB</span><button class='Download' onclick=window.location.href='myFiles.php?id=$fileNameWithoutExtension&download=true'><i class='fa-solid fa-download'></i></button><button class='Delete' onclick=window.location.href='myFiles.php?id=$fileNameWithoutExtension&delete=true'><i class='fa-solid fa-trash-can'></i></button></div></div>";
        }
        $_SESSION["StorageUsed"] = round($TotalSizeInGB / 1024, 2);
    } else {
        echo "<div class=emptyUploads><p>Begin Uploading Now<i class='fa-solid fa-upload'></i></p></div><style> .uploadedFilesContainer { overflow: visible !important; } </style>";
        $_SESSION["StorageUsed"] = 0;
    }
}


/**
 * Retrieves the specified document and displays it in an iframe or as an image.
 *
 * @param string $documentID The ID of the document to retrieve.
 * @param string $Email The email address of the user who owns the document.
 *
 * @return void
 */
function getDocument(String $documentID, String $Email): void
{
    global $connectToDB;
    $getDocument = mysqli_query($connectToDB, "SELECT * FROM Files WHERE FileName LIKE '%$documentID%' AND Email = '$Email'");
    if (mysqli_num_rows($getDocument) > 0) {
        while ($Row = mysqli_fetch_assoc($getDocument)) {
            $fileName = $Row["FileName"];
            if ($Row["FileType"] == "application/pdf") {
                echo "<iframe src=\"fileUploads/$fileName\" width=\"100%\" style=\"height:89.375vh\"></iframe>";
            } else {
                echo "<div class=imgContainer><p class=imgName>" . $Row['OriginalFileName'] . "</p><img class=imgView src=\"fileUploads/$fileName\"><a class=returnToFiles href=myFiles.php>Go Back</a></div>";
            }
        }
    } else {
        header("Location: myFiles.php");
    }
}


/**
 * Downloads the specified document.
 *
 * @param string $documentID The ID of the document to download.
 * @param string $Email The email address of the user who owns the document.
 *
 * @return void
 */
function downloadDocument(String $documentID, String $Email): void
{
    global $connectToDB;
    $getDocument = mysqli_query($connectToDB, "SELECT FileName, OriginalFileName FROM Files WHERE FileName LIKE '%$documentID%' AND Email = '$Email'");
    if (mysqli_num_rows($getDocument) > 0) {
        $getDocument = mysqli_fetch_array($getDocument, MYSQLI_ASSOC);
        $originalFileName = $getDocument["OriginalFileName"];
        $filePath = "fileUploads/" . $getDocument["FileName"];
        if (file_exists($filePath)) {
            print "<a href='$filePath' download='$originalFileName' id=downloadLink></a><script type='text/javascript'>document.getElementById('downloadLink').click(); window.location.href='myFiles.php'</script>";
        }
    }
}


/**
 * Deletes the specified document from the database and from the server.
 *
 * @param string $documentID The ID of the document to delete.
 * @param string $Email The email address of the user who owns the document.
 *
 * @return void
 */
function deleteDocument(String $documentID, String $Email): void
{
    global $connectToDB;
    $getDocument = mysqli_query($connectToDB, "SELECT FileName FROM Files WHERE FileName LIKE '%$documentID%' AND Email = '$Email'");
    if (mysqli_num_rows($getDocument) > 0) {
        $getDocument = mysqli_fetch_array($getDocument, MYSQLI_ASSOC);
        $fileName = $getDocument["FileName"];
        $filePath = "fileUploads/" . $fileName;
        if (mysqli_query($connectToDB, "DELETE FROM Files WHERE FileName = '$fileName' AND Email = '$Email'") && unlink($filePath)) {
            header("Location: myFiles.php");
        }
    }
}
