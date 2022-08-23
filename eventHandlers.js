// Prevent Form Resubmission On Refreshing Page Or Going Back
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// Dynamically Change Colour Of Element When Clicked Onto With A Transition
function changeBorderOnFocus(x)
{
    document.getElementById(x).style.borderColor = "#112B3C";
    document.getElementById(x).style.transition = "0.5s";
    document.getElementById(x).classList.add("currentField");
}

// Dynamically Change Colour Of Element When Clicked Off With A Transition
function changeBorderOnBlur(x)
{
    document.getElementById(x).style.borderColor = "#BBB";
    if(document.getElementById(x).value == "") {
        document.getElementById(x).style.transition = "0.5s";
        document.getElementById(x).classList.remove("currentField");
    }
}

// Dynamically Change The Form Displayed When Button Is Clicked
function changeForm(theElement, className)
{
    if(document.getElementById(theElement).classList.contains(className)) {
        document.getElementById(theElement).classList.remove(className);
    } else {
        document.getElementById(theElement).classList.add(className);
    } 
}

// Show And Hide The Note Form When The New Note Button Is Clicked
function showHideNewNote(newNoteContainer, navBarHeader, notesPageContainer) {
    document.getElementById('noteTitle').value = "";
    document.getElementById('Note').innerHTML = "";
    document.getElementById('colorPicker').value = "#242526";
    if (newNoteContainer.style.display == "none" || !newNoteContainer.style.display) {
        newNoteContainer.style.animation = "displayOnScreen 750ms ease both";
        newNoteContainer.style.display = "flex";
        notesPageContainer.style.opacity = "0";
        navBarHeader.style.opacity = "0";
        noteTitle.focus();    
    } else {
        newNoteContainer.style.animation = "removeFromScreen 750ms ease both";
        notesPageContainer.style.opacity = "1";
        navBarHeader.style.opacity = "1";
        setTimeout(function(){
            newNoteContainer.style.display = "none";
          }, 750);
    }
}

function closeEditMode()
{
    window.location.href = 'myNotes.php?closedEditMode=true';
}

function replaceValue(object) {
    var fileVal = object.value;
    var fileName = fileVal.split("\\");
    document.getElementById("Btn").innerHTML = fileName[fileName.length - 1];
}

window.onload = function() {
    document.getElementById("taskTitle").focus();
};