<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/styleHome.css">
    <title>manager · Home</title>
    <?php include_once "navigationBar.php" ?>
</head>

<body>
    <div class="homeContainer">
        <div class="upperContainer slideShow">
            <div class="fadeSlide imageSlide">
                <img src="Images/collaborateWork.jpg" style="width:100%">
            </div>
            <div class="fadeSlide imageSlide">
                <img src="Images/multiTaskingWomen.jpg" style="width:100%">
            </div>
            <div class="fadeSlide imageSlide">
                <img src="Images/cloudStorage.jpg" style="width:100%">
            </div>
            <div class="fadeSlide imageSlide">
                <img src="Images/taskScheduling.jpg" style="width:100%">
            </div>
            <div class="Indicators">
                <span class="Indicator"></span>
                <span class="Indicator"></span>
                <span class="Indicator"></span>
                <span class="Indicator"></span>
            </div>
        </div>
        <div class="upperContainer aboutUs">
            <h4>About Us</h4>
            <hr>
            <p>At manager, we aim to increase the productivity and efficiency of our users whether they are students or adults. We created this website to provide people with an all-in-one platform where they can store files, manage their events and tasks through our comprehensive calendar, as well as take easily notes to reduce the hassle of switching between websites and accounts.</p>
            <h3 class="companyLogo">manager.</h3>
        </div>
    </div>
    <div class="homeContainer">
        <div class="lowerContainer" style="order: 1; background-image: url('Images/productivityImage.png'); background-repeat: no-repeat; background-size: 120px 92px; background-position: 50% 120%;">
            Manage Your Time Productively By Scheduling Tasks And Events
        </div>
        <div class="lowerContainer" style="order: 4">
            Manager Is As Minimal Or As Powerful As You Need It To Be
            <button class="getStarted" onclick="window.location.href = 'Account.php'"><span>Get Started</span></button>
        </div>
        <div class="lowerContainer" style="order: 3; background-image: url('Images/storageImage.png'); background-repeat: no-repeat; background-size: 110px; background-position: 50% 120%;">
            Store Your Files Altogether In One Location For Free Up To 20GB Storage
        </div>
        <div class="lowerContainer" style="order: 2; background-image: url('Images/noteImage.png'); background-repeat: no-repeat; background-size: 110px; background-position: 50% 99.125%;">
            Create, Change And Delete Your Notes To Make Learning And Working Easier
        </div>
    </div>
</body>

</html>

<script>
    var currentSlide = 0;

    // Dynamically Changes The Image Being Displayed On The Slideshow On The Homepage
    function displaySlide() {
        var Slides = document.getElementsByClassName("fadeSlide");
        var Indicator = document.getElementsByClassName("Indicator");

        for (X = 0; X < Slides.length; X++) {
            Slides[X].style.display = "none";
        }

        currentSlide++;

        if (currentSlide > Slides.length) {
            currentSlide = 1;
        }

        for (X = 0; X < Indicator.length; X++) {
            Indicator[X].style.backgroundColor = "#BBB";
        }

        Slides[currentSlide - 1].style.display = "block";
        Indicator[currentSlide - 1].style.backgroundColor = "#717171";

        setTimeout(displaySlide, 4000);
    }

    displaySlide();
</script>