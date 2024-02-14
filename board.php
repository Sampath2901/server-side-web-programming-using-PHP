<html>
<head>
	<title> Board page </title>
	<style>
		input{
            margin: 11px ;
		}
		.TopRight{
            padding: 17px;
            background: #128fc8;
		}
		table{
            margin: 1px 12px;
			width: calc(99% - 50px);	
		}
		th{
			border: 1.8px groove #86DAD9;
            text-align: center;
		}
		td{
            padding: 1px 4.9px;
			border: 2px ridge #DA8698;	
		}
        body {
            background-image: url("pic.jpg");
        }
	</style>
</head>
<body class="main">

<?php
	session_start();
    date_default_timezone_set('America/Mexico_City');
	if(isset($_GET["signout"])){   
        $clear = session_destroy();           	
        if($clear) {                            
		  header("Location: login.php");
		}
	}	
	if(!isset($_SESSION['SignedBy'])){
      header("location:login.php");
    }
    $ask = $_SERVER["REQUEST_METHOD"];     
	if($ask == "POST") {                  
		try{
            $array= array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION); 
			$data_base = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",$array); 
			$data_base->beginTransaction();
			$msg_id = uniqid();
			$answeredto = null;
			if(isset($_GET["replyto"])){
				$answeredto = $_GET["replyto"];
			}			
			$timeStamp = date("Y-m-d h:i:sa");
            $answeredby = $_SESSION['SignedBy'];    
            $msg = $_POST["msg"];
			$sentence = $data_base->prepare("INSERT INTO posts(id, replyto, postedby, datetime, message) VALUES(:id, :replyto, :postedby, :datetime, :message)");
			$sentence->bindparam(":id", $msg_id);
			$sentence->bindparam(":replyto", $answeredto);
			$sentence->bindparam(":postedby", $answeredby);            
			$sentence->bindparam(":datetime", $timeStamp);            
			$sentence->bindparam(":message", $msg);            
			$sentence->execute();
			$data_base->commit();
			
		} catch(PDOException $Err){
            $Err_r = $Err->getMessage();                    
			print "MessageErr!: " . $Err_r . "<br/>";       
			die();
		}
	}
?>
<div class="TopRight" align="right">
<span >Welcome <?php echo $_SESSION["f_Name"];?>!</span>
<button class="signout" onclick="window.location='board.php?signout=1'" style="background-color:#A5B5E3;color:#FBFBFB" >signout</button>
</div>
<center><form class="board_inside" method = "post">
	<div class="centered">
		<h3>Message Box</h3>
		<textarea name="msg" rows="5" cols="49" placeholder="Type the message " required></textarea><br>
		<input type="submit" value="Send Message" formAction="board.php">
	</div>
<?php 

	error_reporting(E_ALL);
	ini_set('display_errors','On');
	try {
		$data_base = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$data_base->beginTransaction();
		$sentence = $data_base->prepare('select id, username, fullname, datetime, replyto, message from users, posts where users.username = posts.postedby ORDER BY datetime DESC');
		$sentence->execute();
		if($sentence->rowCount() >= 1){
			$showbox = "<table><thead><tr><th>Message Id</th><th>Username</th><th>Full Name</th><th>Posted Date</th><th>Reply to</th><th>Message</th><th>Reply</th></tr></thead><tbody>";
			while ($line = $sentence->fetch()) {
				$showbox .= "<tr><td>". $line["id"] . "</td><td>" . $line["username"] . "</td><td>" . $line["fullname"] . "</td><td>" . $line["datetime"] . "</td><td>" . $line["replyto"] . "</td><td>" . $line["message"] . "</td><td><input type='submit' value='Reply' formAction='board.php?replyto=" . $line["id"] . "'></td></tr>";	
			}
			$showbox .= "</tbody></table>";
			echo $showbox;
		}
	} catch (PDOException $Err) {
        $Err_r =$Err->getMessage();                  
		print "MessageErr!: " . $Err_r . "<br/>";   
		die();
	}
?>
</form></center>
</body>
</html>