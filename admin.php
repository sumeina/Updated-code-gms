<?php
// start a session to store user data
session_start();

// connect to the database
$conn = mysqli_connect("localhost", "root", "", "registration");

// check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // get the username and password from the form data
    $name = $_POST["name"];
    $password = $_POST["password"];

    // query the admin table to check if the username and password are correct
    $sql = "SELECT name, password FROM admin WHERE name='$name' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    // if the query returns one row, the username and password are correct
    if (mysqli_num_rows($result) == 1) {
        // store the username in the session variable
        $_SESSION["name"] = $name;

        // redirect to the dashboard page
        header("Location: admindashboard.php");
        exit();
    } else {
        // if the username and password are incorrect, display an error message
        echo "Incorrect username or password.";
    }
}

// close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login Form</title>
	<style>
		form {
			border: 3px solid #f1f1f1;
			margin: 50px auto;
			padding: 30px;
			width: 50%;
		}
		input[type=text], input[type=password] {
			padding: 12px 20px;
			margin: 8px 0;
			display: inline-block;
			border: 1px solid #ccc;
			box-sizing: border-box;
			width: 100%;
		}
		button {
			background-color: #4CAF50;
			color: white;
			padding: 14px 20px;
			margin: 8px 0;
			border: none;
			cursor: pointer;
			width: 100%;
		}
		button:hover {
			opacity: 0.8;
		}
		.cancelbtn {
			background-color: #f44336;
		}
		.container {
			padding: 16px;
		}
		span.psw {
			float: right;
			padding-top: 16px;
		}
	</style>
</head>
<body>
	<h1>Login Form</h1>
	<form action="admin.php" method="post">
		<div class="container">
			<label for="name"><b>Username</b></label>
			<input type="text" placeholder="Enter Username" name="name" required>

			<label for="password"><b>Password</b></label>
			<input type="password" placeholder="Enter Password" name="password" required>

			<button type="submit">Login</button>
		</div>
	</form>
</body>
</html>
