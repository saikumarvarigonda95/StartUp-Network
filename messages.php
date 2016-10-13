<?php 
session_start();
include("includes/connection.php");
include("functions/functions.php");

if(!isset($_SESSION['user_email'])){
	
	header("location: index.php"); 
}
else {
?>
<!DOCTYPE html> 
<html>
	<head>
		<title>Welcome User!</title> 
	<link rel="stylesheet" href="styles/home_style.css" media="all"/>
	</head> 
	
<body>
	<!--Container starts--> 
	<div class="container">
		<!--Header Wrapper Starts-->
		<div id="head_wrap">
			<!--Header Starts-->
			<div id="header">
				<ul id="menu">
					<li><a href="home.php">Home</a></li>
					<li><a href="members.php">Members</a></li>
					<li><a href="jobs.php">Jobs</a></li>
					<li><a href="news.php">News</a></li>
					<li><a href="incubation_cells.php">I_cells</a></li>
					<?php 
					
					$get_topics = "select * from topics"; 
					$run_topics = mysqli_query($con,$get_topics);
					
					while($row=mysqli_fetch_array($run_topics)){
						
						$topic_id = $row['topic_id'];
						$topic_title = $row['topic_title'];
					
					
					}
					
					?>
				</ul>
				<form method="get" action="results.php" id="form1">
					<input type="text" name="user_query" placeholder="Search a topic"/> 
					<input type="submit" name="search" value="Search"/>
				</form>
			</div>
			<!--Header ends-->
		</div>
		<!--Header Wrapper ends-->
			<!--Content area starts-->
			<div class="content">
				<!--user timeline starts-->
				<div id="user_timeline">
					<div id="user_details">
					<?php 
					$user = $_SESSION['user_email'];
					$get_user = "select * from users where user_email='$user'"; 
					$run_user = mysqli_query($con,$get_user);
					$row=mysqli_fetch_array($run_user);
					
					$user_id = $row['user_id']; 
					$user_name = $row['user_name'];
					$user_country = $row['user_country'];
					$user_image = $row['user_image'];
					$register_date = $row['register_date'];
					$last_login = $row['last_login'];
					
					$user_posts = "select * from posts where user_id='$user_id'"; 
					$run_posts = mysqli_query($con,$user_posts); 
					$posts = mysqli_num_rows($run_posts);
					
					//getting the number of unread messages 
					$sel_msg = "select * from messages where receiver='$user_id' AND status='unread' ORDER by 1 DESC"; 
					$run_msg = mysqli_query($con,$sel_msg);		
		
					$count_msg = mysqli_num_rows($run_msg);
					
					echo "
						<center>
						<img src='user/user_images/$user_image' width='200' height='200'/>
						</center>
						<div id='user_mention'>
						<p><strong>Name:</strong> $user_name</p>
						<p><strong>Country:</strong> $user_country</p>
						<p><strong>Last Login:</strong> $last_login</p>
						<p><strong>Member Since:</strong> $register_date</p>
						
						<p><a href='my_messages.php?u_id=$user_id'>Messages ($count_msg)</a></p>
						<p><a href='my_posts.php?u_id=$user_id'>My Posts ($posts)</a></p>
						<p><a href='edit_profile.php?u_id=$user_id'>Edit My Account</a></p>
						<p><a href='logout.php'>Logout</a></p>
						</div>
					";
					?>
					</div>
				</div>
				
				<!--user timeline ends-->
				<!--Content timeline starts-->
				<div id="content_timeline">
					
			<?php 
			
			if(isset($_GET['u_id'])){
			
			$u_id = $_GET['u_id'];
			
			$sel = "select * from users where user_id='$u_id'"; 
			$run = mysqli_query($con,$sel); 
			$row=mysqli_fetch_array($run); 
			
			$user_name = $row['user_name'];
			$user_image = $row['user_image'];
			$reg_date = $row['register_date'];
			}
		
		?>
			
		<h2>Send a message to <span style='color:red;'><?php echo $user_name; ?></span></h2>
			
			<form action="messages.php?u_id=<?php echo $u_id;?>" method="post" id="f">
				<input type="text" name="msg_title" placeholder="Message Subject..." size="49"/>
				<textarea name="msg" cols="50" rows="5" placeholder="Message Topic..."/></textarea><br/>
				<input type="submit" name="message" value="Send Message"/>
			</form><br/>
			
			<img style="border:2px solid blue; border-radius:5px;" src="user/user_images/<?php echo $user_image;?>" width="100" height="100"/>
			
			<p><strong><?php echo $user_name;?> </strong>is member of this site since: <?php echo $reg_date;?></p>
			
			</div>
			<!--Content timeline ends-->
		<?php 
	if(isset($_POST['message'])){
	
		$msg_title = $_POST['msg_title']; 
		$msg = $_POST['msg']; 
		
		$insert = "insert into messages (sender,receiver,msg_sub,msg_topic,reply,status,msg_date) values ('$user_id','$u_id','$msg_title','$msg','no_reply','unread',NOW())"; 
		
		$run_insert = mysqli_query($con,$insert); 
		
		if($run_insert){
		echo "<center><h2>Message was sent to ". $user_name . " successfully</h2></center>";
		}
		else {
		
		echo "<center><h2>Message was not sent...!</h2></center>";
		}
		
	}
	
	?>
	</div>
	<!--Container ends-->

</body>
</html>
<?php } ?>