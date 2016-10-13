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
						
						<p><a href='my_messages.php?inbox&u_id=$user_id'>Messages ($count_msg)</a></p>
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
				<div id="msg">
		<p align="center">
			<a href="my_messages.php?inbox">My Inbox</a> || 
			<a href="my_messages.php?sent">Sent Items</a>
			
		</p>
		
		<?php 
		if(isset($_GET['sent'])){
		include("sent.php");
		}
		?> 
		<?php if(isset($_GET['inbox'])){?>
		<table width="700">
			<tr>
				<th>Sender:</th>
				<th>Subject</th>
				<th>Date</th>
				<th>Reply</th>
			</tr>
			
			<?php 
		
		$sel_msg = "select * from messages where receiver='$user_id' ORDER by 1 DESC"; 
		$run_msg = mysqli_query($con,$sel_msg);		
		
		$count_msg = mysqli_num_rows($run_msg);
		
		while($row_msg=mysqli_fetch_array($run_msg))
		{
		
		$msg_id = $row_msg['msg_id']; 
		$msg_receiver= $row_msg['receiver'];
		$msg_sender= $row_msg['sender'];
		$msg_sub = $row_msg['msg_sub'];
		$msg_topic = $row_msg['msg_topic'];
		$msg_date = $row_msg['msg_date'];
		
		$get_sender = "select * from users where user_id='$msg_sender'"; 
		$run_sender = mysqli_query($con,$get_sender); 
		$row=mysqli_fetch_array($run_sender); 
		
		$sender_name = $row['user_name'];
		
		
		?>
			
			<tr align="center">
				<td>
				<a href="user_profile.php?u_id=<?php echo $msg_sender;?>" target="blank">
				<?php echo $sender_name;?>
				</a>
				</td>
				<td>
				<a href="my_messages.php?inbox&msg_id=<?php echo $msg_id;?>">
				<?php echo $msg_sub;?>
				</a>
				</td>
				<td><?php echo $msg_date;?></td>
				<td><a href="my_messages.php?inbox&msg_id=<?php echo $msg_id;?>">Reply</a></td>
			</tr>
		<?php } ?>
		</table>
		
		<?php 
			if(isset($_GET['msg_id'])){
			
			$get_id = $_GET['msg_id'];
			
			$sel_message = "select * from messages where msg_id='$get_id'";
			$run_message = mysqli_query($con,$sel_message); 
			
			$row_message=mysqli_fetch_array($run_message); 
			
			$msg_subject = $row_message['msg_sub']; 
			$msg_topic = $row_message['msg_topic']; 
			$reply_content = $row_message['reply'];
			
			//updating the unread message to read
			$update_unread="update messages set status='read' where msg_id='$get_id'"; 
			$run_unread = mysqli_query($con,$update_unread);
			
			echo "<center><br/><hr>
			<h2>$msg_subject</h2>
			<p><b>Message:</b> $msg_topic</p>
			<p><b>My Reply:</b> $reply_content</p>
			
			<form action='' method='post'>
				<textarea cols='30' rows='5' name='reply'></textarea><br/>
				<input type='submit' name='msg_reply' value='Reply to this'/> 
			</form>
			</center>
			";
			}
			
			if(isset($_POST['msg_reply'])){
			
				$user_reply = $_POST['reply'];
				
				if($reply_content!='no_reply'){
				echo "<h2 align='center'>This message was already replied!</h2>";
				exit();
				}
				else {
				$update_msg = "update messages set reply='$user_reply' where msg_id='$get_id' AND reply='no_reply'";
				
				$run_update = mysqli_query($con,$update_msg);
				
				echo "<h2 align='center'> Message was replied!</h2>";
				}
				
			}
		}
		?>
		
		</div>
	</div>
	<!--Container ends-->

</body>
</html>
<?php } ?>