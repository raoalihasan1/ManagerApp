/**
    Prevents form resubmission on refreshing page or going back
*/
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}

/**
    Dynamically changes the color of an element when it's clicked on with a transition
    @param {string} x - The ID of the element being clicked on
*/
function changeBorderOnFocus(x) {
  document.getElementById(x).style.borderColor = "#112B3C";
  document.getElementById(x).style.transition = "0.5s";
  document.getElementById(x).classList.add("currentField");
}

/**
    Dynamically changes the color of an element when it's clicked off with a transition
    @param {string} x - The ID of the element being clicked off of
*/
function changeBorderOnBlur(x) {
  document.getElementById(x).style.borderColor = "#BBB";
  if (document.getElementById(x).value == "") {
    document.getElementById(x).style.transition = "0.5s";
    document.getElementById(x).classList.remove("currentField");
  }
}

/**
    Dynamically changes the form displayed when button is clicked
    @param {string} theElement - The ID of the form element
    @param {string} className - The name of the class applied to the form element
*/
function changeForm(theElement, className) {
  if (document.getElementById(theElement).classList.contains(className)) {
    document.getElementById(theElement).classList.remove(className);
  } else {
    document.getElementById(theElement).classList.add(className);
  }
}

/**
    Shows or hides the note form when the new note button is clicked
    @param {Object} newNoteContainer - The container of the new note form
    @param {Object} navBarHeader - The header of the navigation bar
    @param {Object} notesPageContainer - The container of the notes page
*/
function showHideNewNote(newNoteContainer, navBarHeader, notesPageContainer) {
  document.getElementById("noteTitle").value = "";
  document.getElementById("Note").innerHTML = "";
  document.getElementById("colorPicker").value = "#242526";
  if (
    newNoteContainer.style.display == "none" ||
    !newNoteContainer.style.display
  ) {
    newNoteContainer.style.animation = "displayOnScreen 750ms ease both";
    newNoteContainer.style.display = "flex";
    notesPageContainer.style.opacity = "0";
    navBarHeader.style.opacity = "0";
    noteTitle.focus();
  } else {
    newNoteContainer.style.animation = "removeFromScreen 750ms ease both";
    notesPageContainer.style.opacity = "1";
    navBarHeader.style.opacity = "1";
    setTimeout(function () {
      newNoteContainer.style.display = "none";
    }, 750);
  }
}

/**
    Closes the edit mode and returns the page to "myNotes.php"
*/
function closeEditMode() {
  window.location.href = "myNotes.php?closedEditMode=true";
}

/**
    Replaces the value of the file input field with the name of the file selected for upload
    @param {Object} object - The file input object
*/
function replaceValue(object) {
  var fileVal = object.value;
  var fileName = fileVal.split("\\");
  document.getElementById("Btn").innerHTML = fileName[fileName.length - 1];
}

/**
    Sets focus to the task title field when the window loads
*/
window.onload = function () {
  document.getElementById("taskTitle").focus();
};
