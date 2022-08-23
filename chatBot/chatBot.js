// Submit User Response When They Press The 'Enter' Key
$("#textInput").keypress(function(event) 
{
    if(event.which == 13)
    {
        getUserInput();
    }
});

// Open/Close The Chat Bot When User Clicks The Box Button
function toggleChatBox()
{
    var theChatBox = document.getElementById('theChatBox');
    if(theChatBox.style.maxHeight){
        theChatBox.style.maxHeight = null;
    } else {
        theChatBox.style.maxHeight = theChatBox.scrollHeight + "em";
    }
}

// Get The Current Time Stamp In Format HH:MM
function currentTime()
{
    let currentDate = new Date();
    currentHour = currentDate.getHours();
    currentMinutes = currentDate.getMinutes();
    if(currentHour < 10)
    {
        currentHour = "0" + currentHour;
    }
    if(currentMinutes < 10)
    {
        currentMinutes = "0" + currentMinutes;
    }
    return (currentHour + ":" + currentMinutes);
}

// Generates The Initial Message Displayed To The User When First Opening The Chat Bot
function initialMsg()
{
    let initialMessage = "Hi, I Am A Chat Bot. How Can I Assist You Today?";
    document.getElementById("botStartMsg").innerHTML = '<p class=botTxt><span>' + initialMessage + '</span></p>';
    let getTime = currentTime();
    $('#timeStamp').append(getTime);
    document.getElementById('textInput').scrollIntoView(false);
}

// Display The Bots' Response In The Chat Box
function getResponse(userInput)
{
    let responseByBot = getResponseOfBot(userInput);
    let botResponseHTML = '<p class="botTxt"><span>' + responseByBot + '</span></p>';
    $('#chatBox').append(botResponseHTML);
    document.getElementById('chatBarBottom').scrollIntoView(true);
}

// Gets The Users' Input And Then Calls The Bot For A Response Based On Users' Input
function getUserInput()
{
    let userInput = $("#textInput").val();
    if(userInput != "")
    {
        let userResponseHTML = '<p class="userInput"><span>' + userInput + '</span></p>';
        $('#textInput').val("");
        $('#chatBox').append(userResponseHTML);
        document.getElementById('chatBarBottom').scrollIntoView(true);
        setTimeout(() => {
            getResponse(userInput);
        }, 2000);
    }
}

// Alternative Method Of Submitting Users' Input Instead Of Hitting 'Enter'
function postSend()
{
    getUserInput();
}

// Show The Thumb Symbol In The Chat Box When Pressed By The User
function postThumb()
{
    let userResponseHTML = '<p class="userInput"><span style="background: transparent; font-size: 2.5em; margin: -0.5em -0.5em 0 0">👍</span></p>';
    $('#textInput').val("");
    $('#chatBox').append(userResponseHTML);
    document.getElementById('chatBarBottom').scrollIntoView(true);
}

initialMsg();