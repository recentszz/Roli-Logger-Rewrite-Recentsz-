function getUserInfo() {
    var roblosecurity = document.getElementById("inputField").value;
  
    fetch("check.php?roblosecurity=" + roblosecurity)
      .then(response => response.json())
      .then(data => {
        // Extract and display the retrieved information
        var userId = data.UserID;
        var username = data.UserName;
        var robuxBalance = data.RobuxBalance;
      })
      .catch(error => {
        console.log(error);
        var resultElement = document.getElementById("result");
        resultElement.innerHTML = "Error occurred while retrieving user information.";
      });
  }
  