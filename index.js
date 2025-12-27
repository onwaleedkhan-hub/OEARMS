 function validateForm() {
            let username = document.forms["loginForm"]["username"].value;
            let password = document.forms["loginForm"]["password"].value;

            if (username === "" || password === "") {
                alert("Both fields are required!");
                return false;
            }
            return true;
        }