<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/styleMyAccount.css">
    <title>manager · My Notes</title>
    <script src="https://kit.fontawesome.com/167b7ecae5.js" crossorigin="anonymous"></script>
    <?php
    include_once "accountNavigationBar.php";
    redirectUser();
    ?>
</head>

<body>
    <div id="notesPageContainer">
        <?php getNotes($_SESSION["Email"], $_SESSION["encryptionDetails"]) ?>
        <div id="newNote" onclick="showHideNewNote(document.getElementById('newNoteContainer'), document.getElementById('navBarHeader'), document.getElementById('notesPageContainer'))"><i class="fa-solid fa-comment-dots"></i></div>
        <?php
        if (isset($_GET["closedEditMode"]) && $_GET["closedEditMode"]) {
            if (isset($_SESSION['idOfEditingNote'])) {
                unset($_SESSION['idOfEditingNote']);
            }
            header("Location: myNotes.php");
        }
        if (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] == 'max-age=0') {
            unset($_SESSION['idOfEditingNote']);
        }
        if (isset($_POST["saveNote"])) {
            addNewNote(cleanUserInput($_POST["noteTitle"]), cleanUserInput($_POST["Note"]), cleanUserInput($_POST["colorPicker"]));
        }
        if (isset($_POST["editNote"])) {
            $_SESSION["editNote"] = true;
            $_SESSION["noteDetails"] = getEditingNote($_POST["editNote"], $_SESSION["encryptionDetails"]);
        }
        if (isset($_POST["deleteNote"])) {
            deleteNote($_POST["deleteNote"], $_SESSION["encryptionDetails"]);
        }
        ?>
    </div>
    <div id="newNoteContainer">
        <div id="newNoteForm">
            <form method="post" id="noteForm" autocomplete="off">
                <input type="text" name="noteTitle" id="noteTitle" placeholder="Title" required>
                <textarea name="Note" id="Note" cols="30" rows="17" placeholder="Take a note..." required></textarea>
                <div class="btnContainer">
                    <div class="btnForStyle">
                        <input id="colorPicker" name="colorPicker" type="color" value="#242526" onchange="document.getElementById('newNoteForm').style.background = document.getElementById('colorPicker').value;">
                        <label for="colorPicker">Change Background Colour</label>
                    </div>
                    <div class="btnForForm">
                        <button type="button" onclick="showHideNewNote(document.getElementById('newNoteContainer'), document.getElementById('navBarHeader'), document.getElementById('notesPageContainer')); closeEditMode();"><i class="fa-solid fa-xmark"></i>Close</button>
                        <button name="saveNote" type="submit"><i class="fa-solid fa-floppy-disk"></i>Save</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</body>

</html>

<script src="eventHandlers.js"></script>
<script>
    let noteEdit = <?php echo json_encode($_SESSION["editNote"], JSON_HEX_TAG) ?>;
    let noteDetails = <?php echo json_encode($_SESSION["noteDetails"], JSON_HEX_TAG) ?>;
    if (noteEdit != null && noteEdit) {
        showHideNewNote(document.getElementById('newNoteContainer'), document.getElementById('navBarHeader'), document.getElementById('notesPageContainer'));
        document.getElementById('noteTitle').value = noteDetails[0];
        document.getElementById('Note').innerHTML = noteDetails[1];
        document.getElementById('colorPicker').value = noteDetails[2];
        document.getElementById('newNoteForm').style.background = noteDetails[2];
    }
</script>
<?php unset($_SESSION["editNote"], $_SESSION["noteDetails"]) ?>