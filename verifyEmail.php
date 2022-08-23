<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="CSS/styleEmail.css">
</head>

<body style="background-color: #F4F4F4; margin: 0 !important; padding: 0 !important;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#18191A" align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td align="center" valign="top" style="padding: 40px 10px 40px 10px;"> </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#18191A" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#FFF" align="center" valign="top" style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Poppins',  sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                            <h1 style="font-size: 0.75em; font-weight: 700; margin: 2;">Welcome To Manager!</h1> <img src="https://i.imgur.com/BsvwNwO.png" width="125" height="120" style="display: block; border-radius: 50%;" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#F4F4F4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <?php
                        $fullName = $_SESSION["fullName"];
                        unset($_SESSION["fullName"]);
                        ?>
                        <td bgcolor="#FFF" align="center" style="padding: 20px 50px; color: #666666; font-family: 'Poppins',  sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <p style="margin: 0;">Hello <?php echo $fullName ?>, Start Using Manager Now By Verifying Your Email To Activate Your Account.</p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#FFF" align="left">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td bgcolor="#FFF" align="center" style="padding: 20px 30px 55px;">
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <?php
                                                $verifyHash = $_SESSION['Hash'];
                                                unset($_SESSION["Hash"]);
                                                ?>
                                                <td align="center" style="box-shadow: 2px 2px 7.5px -2px rgba(50, 50, 50, 0.75); border-radius: 10px;" bgcolor="#18191A"><a href="localhost/manager/activateAccount.php?myCode=<?php echo $verifyHash ?>" style="font-weight: 500; font-size: 1.25em; letter-spacing: 2.5px; font-family: Helvetica, Arial, sans-serif; color: #FFF; text-decoration: none; color: #FFF; text-decoration: none; padding: 15px 25px; border-radius: 10px; border: 1px solid #18191A; display: inline-block;">Confirm Account</a></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="center" style="padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: 'Poppins',  sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <p style="margin: 0;">Welcome Onboard,<br>The Manager Team</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>