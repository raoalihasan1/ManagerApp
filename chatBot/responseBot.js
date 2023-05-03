/**
    Gets the response of the bot based on the input of the user.
    @param {string} userInput - The input given by the user.
    @returns {string} - The response of the bot based on the input given by the user.
*/
function getResponseOfBot(userInput) {
  userInput = userInput.toLowerCase();
  userInput = userInput.replace(/[.,\/#!$%\^&\*;:{}=\-_`~()\']/g, "");
  userInput = userInput.replace(/\s{2,}/g, " ");
  let returnResponse = "";
  let keyNegatives =
    userInput.includes("issue") ||
    userInput.includes("struggling") ||
    userInput.includes("cant") ||
    userInput.includes("unable") ||
    userInput.includes("cannot");

  // Greetings
  if (userInput.includes("hello") || userInput.includes("hi")) {
    returnResponse += "Hello! Hope You're Doing Well. ";
  }

  // Sign In Queries
  if (
    keyNegatives &&
    (userInput.includes("logging in") ||
      userInput.includes("log in") ||
      userInput.includes("sign in") ||
      userInput.includes("entering") ||
      userInput.includes("my account"))
  ) {
    returnResponse +=
      "Are You Able To Log In To Your Account?<a href='Account.php'> Click Here To Log In. <a> If Not, Have You Tried To Reset Your Password? If You Still Cannot Sign In, Please Complete The Form On This Page So We Can Look Into This Issue. ";
  }

  // Sign Up Queries
  if (
    keyNegatives &&
    (userInput.includes("signing up") ||
      userInput.includes("creating") ||
      userInput.includes("sign in") ||
      userInput.includes("new account") ||
      userInput.includes("sign up"))
  ) {
    returnResponse +=
      "Were There Any Error Messages Displayed During Sign Up? If So, Please Sign Up Again<a href='Account.php'> By Clicking Here<a> And Make Sure That You See A Confirmation Message Of Successful Sign Up. Then, In Order To Access Your Account, You Must Activate It First By Clicking The Link Sent To Your Email To Verify Your Identity. If You Are Still Struggling, Please Fill The Form On This Page For Further Support. ";
  }

  // Email Queries
  if (
    (keyNegatives ||
      userInput.includes("sent") ||
      userInput.includes("confirmation") ||
      userInput.includes("reset") ||
      userInput.includes("recieved")) &&
    (userInput.includes("email") || userInput.includes("address"))
  ) {
    returnResponse +=
      "Are You Sure You Created An Account With A Valid Email Address? Please Create A New Account And If It Says This Email Already Exists, Please Contact Us Through The Form on This Page To Fix This Problem. ";
  }

  // Forgotten Password Queries
  if (
    (userInput.includes("forgot") ||
      userInput.includes("forgotten") ||
      userInput.includes("lost") ||
      userInput.includes("cannot")) &&
    (userInput.includes("password") || userInput.includes("account"))
  ) {
    returnResponse +=
      "Have You Tried To Reset Your Password?<a href='forgotPassword.php'> Click Here To Reset Your Password. <a> If This Issue Persists Please Fill The Form on This Page And We Would Be Happy To Help Further. ";
  }

  // No Matching Queries
  if (returnResponse == "") {
    return "Sorry, I Didn't Quite Understand That.";
  }
  return returnResponse;
}
