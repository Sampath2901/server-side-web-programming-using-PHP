<html>
<head>
	<title>Home Page</title> 
	<style>
		.center{
            padding: 29.5px;
			display:  inline-grid;
			border: 4.01px double; 
            color: #CBD9B0;
            margin: 70px;
		}
		.button_sub{
			padding: 2px;
            margin: 10px;
			border-radius: 9px;
		}
        body {
            background-image: url("pic2.jpg");
        }
	</style>
</head>
<body>
 <?php
   session_start();
   $MessageErr = "";
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		try {
			$array = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);                      
			$data_base = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",$array);  
			$data_base->beginTransaction();
            $pst = $_POST['password'];
			$pswd = md5($pst);
            $usr =$_POST['username'];
			$usrnm = $usr;
			$sentence = $data_base->prepare('select * from users where username="'. $usrnm . '" and password ="' . $pswd . '"');
			$sentence->execute();
			$number = $sentence->rowCount();
			while ($line = $sentence->fetch()) {
			  $f_Name = $line["fullname"];
			}
			if($number == 1) { 
				$_SESSION['f_Name'] = $f_Name;
				$_SESSION['SignedBy'] = $usrnm;

				header("location: board.php");
			}else{
				$MessageErr = "Incorrect details! Enter valid Username&Password";
			}
		} catch (PDOException $Err) {
            $Err_r=$Err->getMessage();
			print "MessageErr!: " . $Err_r . "<br/>";
			die();
		}
	}
 ?>
<center><form class="log_form" method = "post">
	<div class="center">
		<h3>Login page</h3>
		<input type="text" name="username" placeholder="Enter Username" ><br>
		<input type="password" name="password" placeholder="Enter Password" >
		<?php echo $MessageErr; ?><br>
		<input class="button_sub" type="submit" value="Submit">
	</div>
</form></center>
</body>
</html>