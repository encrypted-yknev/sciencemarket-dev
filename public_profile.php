<?php
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:index.php");
}

include "connectDb.php";

function get_first_name($x)	{
	if(strpos($x," "))	
		return substr($x,0,strpos($x," "));
	else
		return $x;
}

function get_profile_comp_percent()	{
	global $conn;
	$tot_values=7;
	$user_count=0;
	try	{
		$sql_fetch_user_dtls="select disp_name,email_addr,ph_num,age,location,description,pro_img_url 
		from users where user_id='".$_SESSION['user']."'";
		$stmt=$conn->prepare($sql_fetch_user_dtls);
		$stmt->execute();
		$result_user=$stmt->fetch();
		
		if(!empty($result_user['disp_name']))
			$user_count+=1;
		if(!empty($result_user['email_addr']))
			$user_count+=1;
		if(!empty($result_user['ph_num']))
			$user_count+=1;
		if(!empty($result_user['age']))
			$user_count+=1;
		if(!empty($result_user['location']))
			$user_count+=1;
		if(!empty($result_user['description']))
			$user_count+=1;
		if(($result_user['pro_img_url']) != '\uploads\man.jpg')
			$user_count+=1;
		return (int)($user_count/$tot_values * 100);
		
	}
	catch(PDOException $e)	{
		
	}
}
?>
<html>
<head>
<title>Science Market - User Dashboard. Edit your profile.</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="styles/profile.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<script src="js/bootstrap.min.js"></script>
<script src="js/header.js"></script>
<script type="text/javascript" src="js/profile.js"></script>
</head>
<body>
<div id="block"></div>
<?php include "header.php"; ?>
<!--<div id="block"></div>-->

	<?php
		$img_url="";
		try	{																			#fetch user details.
			$sql="select disp_name,email_addr,ph_num,location,age,description,pro_img_url,up_votes,down_votes from users where user_id='".$_SESSION["user"]."'";			
			$stmt=$conn->prepare($sql);
			$stmt->execute();
			$row=$stmt->fetch();
			$disp_name=$row["disp_name"];
			$email=$row["email_addr"];
			$mob=$row["ph_num"];
			$location=$row["location"];
			$age=$row["age"];
			$desc=$row["description"];
			$img_url=$row["pro_img_url"];
			$up_votes=$row["up_votes"];
			$down_votes=$row["down_votes"];
		}
		catch(PDOException $e)	{
			echo "Some error occured";
		}
	?>
	</br>
	<div class="container">
		<div id="side-nav">
			<table border="0">
				<tr>
					<td>
						<div id="nav-id">
							<div class="side-bar"></div>
							<div class="side-bar"></div>
							<div class="side-bar"></div>
						</div>
					</td>
					<td>
						<div id="media-image"><img src="img/logo.jpg" width="200" height="50"/></div>
					</td>
				</tr>
			</table></br>
			<div id="page-title"><span>Profile</span></div></br>
			<div class="row">
				<div class="col-sm-3">
					<div id="row-one">
						<a href="qstn.php" class="btn btn-info">Ask Questions</a>
					</div>
				</div>
				<div class="col-sm-6">
					<div id="row-two">
						<input type="text" class="form-control" id="srch-box-media" placeholder="Search questions" />
					</div>
				</div>
			</div>
		</div>
		<div id="options-menu">
			<div id="pro-section-media" > 
				<div id="proimg">
					<img id="propic" src="<?php echo $img_url; ?>" />
					<a id="pic-link" href="upload.php"><span id="change-image-section">Change photo</span></a>
				</div></br></br>
				<ul class="nav nav-pills nav-stacked">
					<li><a href="profile.php" id="profile-link"><span class="glyphicon glyphicon-user" ></span>&nbsp;My Profile</a></li>
					<li><a href="dashboard.php" id="dashboard-link"><span class="glyphicon glyphicon-home" ></span>&nbsp;Dashboard</a></li>
					<li><a href="forum" id="forum-link"><span class="glyphicon glyphicon-question-sign"></span>&nbsp;Q/A Forum</a></li>
					<li><a href="" id="connect-link"><span class="glyphicon glyphicon-transfer" ></span>&nbsp;Expert Connect</a></li>
					<li><a href="" id="collab-link"><span class="glyphicon glyphicon-refresh" ></span>&nbsp;Collaborate</a></li>
					<li><a href="" id="favours-link"><span class="glyphicon glyphicon-gift" ></span>&nbsp;Favours</a></li>
					<li><a href="logout.php" id="logout-link"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a></li>
				</ul>
			    
			</div></br>
		</div>
		<div class="row">
			<div class="col-sm-3" id="pro-section" > 
				<div id="proimg">
					<img id="propic" src="<?php echo $img_url; ?>" />
					<a id="pic-link" href="upload.php"><span id="change-image-section"><span class="glyphicon glyphicon-camera"></span>&nbsp;&nbsp;Change photo</span></a>
				</div></br></br>
				<ul class="nav nav-pills nav-stacked">
					<li class="active"><a href="profile.php">Profile Settings</a></li>
					<li><a href="user_notifications.php">Notifications</a></li>
					<li><a href="logout.php">Logout</a></li>
			    </ul>
			</div>
			<div class="col-sm-9" id="detl-section">
				<div id="profile-stats-section">
					<h2>Welcome<?php echo ' '.get_first_name($disp_name); ?></h2>
					<!--
					<h4>Profile Completeness</h4>
					<div class="progress">
					  <div class="progress-bar" style="width:<?php #echo get_profile_comp_percent().'%'; ?>">
						<?php #echo get_profile_comp_percent().'%'; ?>
					  </div>
					</div>
					-->
					<h5><strong>Profile Stats</strong></h5>
					<div class="row">
						<div class="col-sm-6">
							<ul class="list-group">
							  <li class="list-group-item">Questions asked <span class="badge">
								<?php
									$sql_fetch_qstn_count = "select count(1) as qstn_cnt from questions where posted_by = '".$_SESSION['user']."'";
									$stmt_qstn=$conn->prepare($sql_fetch_qstn_count);
									$stmt_qstn->execute();
									$res_qstn=$stmt_qstn->fetch();
									$question_count = $res_qstn['qstn_cnt'];
									echo $question_count;
									
									
									
								?>
							  
							  </span></li>
							  <li class="list-group-item">Questions answered<span class="badge">
								<?php
									$sql_fetch_ans_count = "select count(1) as ans_cnt from answers where posted_by = '".$_SESSION['user']."'";
									$stmt_ans=$conn->prepare($sql_fetch_ans_count);
									$stmt_ans->execute();
									$res_ans=$stmt_ans->fetch();
									$answer_count = $res_ans['ans_cnt'];
									echo $answer_count;
								?>
							  </span></li> 
							  <li class="list-group-item">Total upvotes gained<span class="badge"><?php echo $up_votes; ?></span></li> 
							  <li class="list-group-item">Total downvotes<span class="badge"><?php echo $down_votes;?></span></li> 
							</ul>
						</div>
						<div class="col-sm-6">
						<div class="panel panel-default">
						  <div class="panel-body">You are following 10 people including A, B,...</div>
						  <div class="panel-body">20 people are following you including Q, W,...</div>
						</div>
						</div>
					</div>
				</div>
				<div class="row" id="row-4">
					<div class="col-sm-6 col-1">
						<h5 class="header-group"><span class="glyphicon glyphicon-pencil profile-edit"></span><strong>Edit Profile</strong></h5>
						Display Name: <input class="form-control" id="name" type="text" placeholder="" value="<?php echo $disp_name; ?>" onfocus="showTip(1)"/></br>
						Email: <input class="form-control" id="mail" type="text" placeholder="" value="<?php echo $email; ?>" onfocus="showTip(2)"/></br>
						Mobile: <input class="form-control" id="mob" type="text" placeholder="" value="<?php echo $mob; ?>" onfocus="showTip(3)"/></br>
						Location: <input class="form-control" id="location" type="text" placeholder="" value="<?php echo $location; ?>" onfocus="showTip(4)"/></br>
						About me: <textarea class="form-control" id="desc" rows="5" id="comment" onfocus="showTip(5)"><?php echo $desc; ?></textarea></br>
						<button type="button" class="btn btn-primary" onclick=
						"updateUser(document.getElementById('name').value,document.getElementById('mail').value,
						document.getElementById('mob').value,document.getElementById('location').value,
						document.getElementById('desc').value)">Save</button></br>
						<span id="message-section-1">
					</div>
					<div class="col-sm-6 col-2">
						<h5 class="header-group"><span class="glyphicon glyphicon-tags profile-edit"></span><strong>Update your interests</strong></h5></br>
						<?php
							$tags_html="";
							try	{
								$sql_check_interests = "select b.tag_name 
														from tags b 
														inner join user_tags a 
														on b.tag_id = a.tag_id
														where a.user_id = '".$_SESSION['user']."'";
								$stmt_check_interests = $conn->prepare($sql_check_interests);
								$stmt_check_interests->execute();
								
								if($stmt_check_interests->rowCount() <= 0)	{
									echo "You haven't added your interests yet";
								}
								else	{
									echo "<span>Your interests : ";
									
									while($row_interests = $stmt_check_interests->fetch())	{
										echo "<span class='badge disp-tags'>".$row_interests['tag_name']."</span>&nbsp;&nbsp;";
										$tags_html.="<span class='tag-name'>".$row_interests['tag_name']."</span>";
									}
								}
							}
							catch(PDOException $e)	{
								echo "Some error occured. Please try again after some time.";
							}
						?>
						</br></br>
						<span><strong><a href="javascript:void(0)" onclick="$('#tag').toggle()">Click here</a></strong> to add/remove interests</span>
						<div id="tag" class="">
							<input class="q-tags" type="text" name="q_tags" placeholder="Add interests+ENTER" />&emsp;
							<button type="button" class="btn btn-primary" onclick="addInterests(getTagsName())">Update</button></br></br>
							<div id="tag-res">
								<?php
									echo $tags_html;
								?>
							</div></br>
							<span id="message-section-2">
						</div>
					</div>
				</div>
				<div class="row" id="row-2">
					<div class="col-sm-6 col-1">
						<h5 class="header-group"><span class="glyphicon glyphicon-lock profile-edit" ></span><strong>Reset Password</strong></h5>
						Existing Password: <input type="password" class="form-control" id="pwd"></br>
						New Password: <input type="password" class="form-control" id="new-pwd"></br>
						Confirm Password: <input type="password" class="form-control" id="conf-pwd"></br>
						<button type="button" class="btn btn-primary" onclick="resetPwd(document.getElementById('pwd').value,document.getElementById('new-pwd').value,document.getElementById('conf-pwd').value)">Save</button></br>
						<span id="message-section-3"></span>
					</div>
					<div class="col-sm-6 col-2">
						<h5 class="header-group"><span class="glyphicon glyphicon-ban-circle profile-edit"></span><strong>De-activate account</strong></h5>
						Enter account password: <input type="password" class="form-control" id="pwd"></br>
						<button type="button" class="btn btn-primary" onclick="">Go</button></br>
						<span id="message-section-4"></span>
					</div>
				</div>
			</div>		
		</div>
	</div>
	
	
	<!--<div></div>-->
																<!-- logout	-->
	 <?php																						#display user details.
		/* echo "<h2>Welcome ".$disp_name."</h2></br>";
		echo "About me - ".$desc."</br>";
		echo "Location - ".$location."</br>";
		echo "Upvotes - ".$up_votes."</br>";
		echo "Downvotes - ".$down_votes."</br>"; */
	?> 
	</br></br>
	

<?php
	include "footer.php";
?>
</body>
</html>
