<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/styleMyAccount.css">
    <title>manager · My Tasks</title>
    <script src="https://kit.fontawesome.com/167b7ecae5.js" crossorigin="anonymous"></script>
    <?php
    include_once "accountNavigationBar.php";
    redirectUser();
    storeEncryptedKeys($_SESSION["Email"]);
    ?>
</head>

<body>
    <div class="allTasksContainer">
        <div class="leftContainer">
            <div class="newTasks">
                <form method="post">
                    <div class="inputTask">
                        <input name="taskTitle" type="text" placeholder="Add Title" id="taskTitle" required value="<?php echo isset($_POST["taskTitle"]) ? $_POST["taskTitle"] : ''  ?>">
                    </div>
                    <div class="inputTask">
                        <label for="taskDate"><i class="fa-solid fa-clock" style="margin-left: 0.45em"></i></label>
                        <input type="datetime-local" id="taskDate" name="taskDate" required value="<?php echo isset($_POST["taskDate"]) ? $_POST["taskDate"] : ''  ?>">
                    </div>
                    <div class="inputTask">
                        <label for="Description"><i class="fa-solid fa-circle-info"></i></label>
                        <textarea rows="3" name="Description" placeholder="Add Description"><?php echo isset($_POST["Description"]) ? $_POST["Description"] : ''  ?></textarea>
                    </div>
                    <div class="inputTask">
                        <div class="multipleInput">
                            <label for="selectPriority">Priority</label>
                            <div class="selectOptions">
                                <select name="selectPriority">
                                    <option value="Low" selected>Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                                <span class="customArrow"></span>
                            </div>
                        </div>
                    </div>
                    <div class="inputTask">
                        <button type="submit" name="addTask">Add Task</button>
                        <?php
                        if (isset($_POST["addTask"])) {
                            addTask(cleanUserInput($_POST["taskTitle"]), cleanUserInput($_POST["taskDate"]), cleanUserInput($_POST["Description"]), cleanUserInput($_POST["selectPriority"]));
                        }
                        ?>
                    </div>
                </form>
            </div>
            <div class="nextTask">
                <h4>Upcoming Task</h4>
                <?php
                $Task = upComingTask($_SESSION["Email"]);
                if (!empty($Task)) {
                    list($Date, $Time) = explode(" ", $Task["Scheduled"]);
                    if ($Time >= "12:00:00") {
                        $timeOfDay = "pm";
                    } else {
                        $timeOfDay = "am";
                    }
                    $Time = date("h:i", strtotime($Time));
                    if (!empty($Task["Description"])) {
                        echo "<p id=upcomingTitle><span id=upcomingPriority Count=0>" . convertPriority($Task["Priority"], 0) . "</span>" . $Task["Title"] . "</p><p id=upcomingDate>" . date("d-m-Y", strtotime($Date)) . " at " . substr($Time, 0, 5) . $timeOfDay . "</p><p id=upcomingDescription>" . $Task["Description"] . "</p>";
                    } else {
                        echo "<p id=upcomingTitle><span id=upcomingPriority Count=0>" . convertPriority($Task["Priority"], 0) . "</span>" . $Task["Title"] . "</p><p id=upcomingDate>" . date("d-m-Y", strtotime($Date)) . " at " . substr($Time, 0, 5) . $timeOfDay . "</p>";
                    }
                }
                ?>
            </div>
        </div>
        <div class="allTasks">
            <h4>Scheduled Tasks</h4>
            <div class="scheduledTasks">
                <?php allTasks($_SESSION["Email"]); ?>
            </div>
        </div>
    </div>

</body>

</html>

<script src="eventHandlers.js"></script>