/**
    Submits user response when they press the 'Enter' key
    @param {Event} event - The keypress event
*/
$("#textInput").keypress(function (event) {
  if (event.which == 13) {
    getUserInput();
  }
});

/**
    Toggles the chat box open/close when the user clicks the box button
*/
function toggleChatBox() {
  var theChatBox = document.getElementById("theChatBox");
  if (theChatBox.style.maxHeight) {
    theChatBox.style.maxHeight = null;
  } else {
    theChatBox.style.maxHeight = theChatBox.scrollHeight + "em";
  }
}

/**
    Gets the current time stamp in format HH:MM
    @returns {String} - The current time in HH:MM format
*/
function currentTime() {
  let currentDate = new Date();
  currentHour = currentDate.getHours();
  currentMinutes = currentDate.getMinutes();
  if (currentHour < 10) {
    currentHour = "0" + currentHour;
  }
  if (currentMinutes < 10) {
    currentMinutes = "0" + currentMinutes;
  }
  return currentHour + ":" + currentMinutes;
}

/**
    Generates the initial message displayed to the user when first opening the chat bot
*/
function initialMsg() {
  let initialMessage = "Hi, I Am A Chat Bot. How Can I Assist You Today?";
  document.getElementById("botStartMsg").innerHTML =
    "<p class=botTxt><span>" + initialMessage + "</span></p>";
  let getTime = currentTime();
  $("#timeStamp").append(getTime);
  document.getElementById("textInput").scrollIntoView(false);
}

/**
    Displays the bots' response in the chat box
    @param {String} userInput - The user's input
*/
function getResponse(userInput) {
  let responseByBot = getResponseOfBot(userInput);
  let botResponseHTML =
    '<p class="botTxt"><span>' + responseByBot + "</span></p>";
  $("#chatBox").append(botResponseHTML);
  document.getElementById("chatBarBottom").scrollIntoView(true);
}

/**
    Gets the users' input and then calls the bot for a response based on the users' input
*/
function getUserInput() {
  let userInput = $("#textInput").val();
  if (userInput != "") {
    let userResponseHTML =
      '<p class="userInput"><span>' + userInput + "</span></p>";
    $("#textInput").val("");
    $("#chatBox").append(userResponseHTML);
    document.getElementById("chatBarBottom").scrollIntoView(true);
    setTimeout(() => {
      getResponse(userInput);
    }, 2000);
  }
}

/**
    Alternative method of submitting users' input instead of hitting 'Enter'
*/
function postSend() {
  getUserInput();
}

/**
    Shows the thumb symbol in the chat box when pressed by the user
*/
function postThumb() {
  let userResponseHTML =
    '<p class="userInput"><span style="background: transparent; font-size: 2.5em; margin: -0.5em -0.5em 0 0">👍</span></p>';
  $("#textInput").val("");
  $("#chatBox").append(userResponseHTML);
  document.getElementById("chatBarBottom").scrollIntoView(true);
}

initialMsg();
