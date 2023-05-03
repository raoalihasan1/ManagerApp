<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>manager · Contact Us</title>
    <link rel="stylesheet" href="CSS/styleContactUs.css">
    <script src="https://kit.fontawesome.com/167b7ecae5.js" crossorigin="anonymous"></script>
    <?php
    include_once "navigationBar.php";
    include_once "managerFunctions.php";
    ?>
</head>

<body>
    <div class="linksContainer">
        <div class="allLinks">
            <div class="innerContainer"><a href="mailto:managerlocalhost@gmail.com?subject=Contact Us: "><i class="fa-solid fa-at"></i></a></div>
            <div class="innerContainer"><a href="https://discord.gg/QaXFtjaw"><i class="fa-brands fa-discord"></i></a></div>
            <div class="innerContainer"><a href="sms:01613069280"><i class="fa-solid fa-mobile-button"></i></a></div>
            <div class="innerContainer"><a href="https://www.google.com/maps/place/Kilburn+Building/@53.4670673,-2.2363695,17z/data=!3m2!4b1!5s0x487bb1927d8cb3c9:0x866f19bee8515cd6!4m5!3m4!1s0x487bb192808a387b:0x9a2896298908a5f2!8m2!3d53.4670673!4d-2.2341808"><i class="fa-solid fa-location-dot"></i></a></div>
        </div>
        <div class="formContainer">
            <form method="post" accept-charset="utf-8" autocomplete="off">
                <h3>Send Us A Message</h3>
                <h6>Feel Free To Drop Us A Line Below</h6>
                <?php
                if (isset($_POST["contactUs"])) {
                    echo '<div class="errorsContainer">';
                    $contactUs = contactUs(cleanUserInput($_POST["myName"]), cleanUserInput($_POST["myEmail"]), cleanUserInput($_POST["messageSubject"]), cleanUserInput($_POST["messageContent"]));
                    if (count($contactUs) > 0) {
                        foreach ($contactUs as $Error) {
                            echo "<div class='errorMessage'>" . $Error . "</div>";
                        }
                    } else {
                        echo "<div class='errorMessage'>Your Message Has Successfully Been Sent! We Will Get In Touch With You Shortly If Needed.</div><style> .errorMessage { color: #4F8A10 !important; text-align: center; } .errorsContainer { background-color: #DFF2BF; border-color: #4F8A10; } </style>";
                    }
                    echo '</div>';
                }
                ?>
                <div class="inputClass">
                    <input type="text" name="myName" id="myName" onfocus='changeBorderOnFocus("myName")' onblur='changeBorderOnBlur("myName")' value='<?php echo isset($_POST["myName"]) ? $_POST["myName"] : ''  ?>'>
                    <label for="myName">Full Name</label>
                </div>
                <div class="inputClass">
                    <input type="text" name="myEmail" id="myEmail" onfocus='changeBorderOnFocus("myEmail")' onblur='changeBorderOnBlur("myEmail")' value='<?php echo isset($_POST["myEmail"]) ? $_POST["myEmail"] : ''  ?>'>
                    <label for="myEmail">Email Address</label>
                </div>
                <div class="inputClass">
                    <input type="text" name="messageSubject" id="messageSubject" onfocus='changeBorderOnFocus("messageSubject")' onblur='changeBorderOnBlur("messageSubject")' value='<?php echo isset($_POST["messageSubject"]) ? $_POST["messageSubject"] : ''  ?>'>
                    <label for="messageSubject">Subject</label>
                </div>
                <div class="inputClass">
                    <textarea name="messageContent" id="messageContent" cols="20" rows="1" onfocus='changeBorderOnFocus("messageContent")' onblur='changeBorderOnBlur("messageContent")'><?php echo isset($_POST["messageContent"]) ? $_POST["messageContent"] : ''  ?></textarea>
                    <label for="messageContent">Message</label>
                </div>
                <input type="submit" value="Contact Us" name="contactUs">
            </form>
        </div>
    </div>
    <div class="chatBotContainer">
        <div class="chatBoxCollapse">
            <button id="chatBtn" class="collapseBtn" onclick="toggleChatBox()">Chat Here <p><i style="color: #FFF" id="chatIcon" class="fa fa-fw fa-comments-o"></i></p>
            </button>
            <div class="chatBoxContent" id="theChatBox">
                <div class="fullChatBox">
                    <div class="outerContainer">
                        <div class="chatContainer">
                            <div id="chatBox">
                                <h5 id="timeStamp"></h5>
                                <p id="botStartMsg" class="botTxt"><span>Loading...</span></p>
                            </div>
                            <div class="userInputBlock">
                                <div id="userInput">
                                    <input type="text" name="userMsg" id="textInput" class="inputBox" placeholder="Press Enter To Send Message">
                                </div>
                                <div class="chatBarIcons">
                                    <span style="display: inline;" id="chatIcon" onclick="postThumb()">👍</span>
                                    <i style="color: #333; padding-top: 5px" id="chatIcon" class="fa fa-fw fa-send" onclick="postSend()"></i>
                                </div>
                            </div>
                            <div id="chatBarBottom">
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="chatBot/chatBot.js"></script>
<script src="chatBot/responseBot.js"></script>
<script src="eventHandlers.js"></script>
<script>
    $('#inputClass').delegate('textarea', 'keydown', function() {
        $(this).height(0);
        $(this).height(this.scrollHeight);
    });
    $('#inputClass').find('textarea').keydown();
</script>