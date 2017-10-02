<?php 
		try	{
			$sql_show_some_ans = "select distinct  a.ans_id,
												   a.ans_desc,
												   a.up_votes,
												   a.down_votes,
												   a.posted_by,
												   a.created_ts 
										   from answers a
										   inner join questions b 
										   on a.qstn_id=b.qstn_id
										   left outer join comments c
										   on a.ans_id=c.ans_id
										   where b.qstn_id=".$qid."
										   order by a.created_ts desc,c.created_ts desc
										   limit 3";
			$sql_count_ans = "select count(1) from answers where qstn_id = ".$qid;
			
			$stmt_show_some_ans=$conn->prepare($sql_show_some_ans);
			$stmt_show_some_ans->execute();
			
			if($stmt_show_some_ans->rowCount() > 0)	{
				
				while($row_ans = $stmt_show_some_ans->fetch())	{
					
					$ansid=$row_ans['ans_id'];
					$ans = $row_ans['ans_desc'];
					$ans_user=$row_ans['posted_by'];
					$ans_ts=$row_ans['created_ts'];
					$upvotes=$row_ans["up_votes"];
					$downvotes=$row_ans["down_votes"];
					$sql_get_user_pic = "select pro_img_url from users where user_id='".$ans_user."'";
					
					$stmt_get_user_pic = $conn->prepare($sql_get_user_pic);
					$stmt_get_user_pic->execute();
					$row_pic = $stmt_get_user_pic->fetch();
					$ans_user_pic = $slashes.$row_pic['pro_img_url'];
					?>
					<div class="ans-front-hidden-sec" id="ans-front-sec-<?php echo $ansid; ?>">
						<div class="photo-ans-sec" style="background-image:url('<?php echo $ans_user_pic; ?>'); background-size:cover;"></div>
							
						<div class="auth-text-section">
							<?php echo '<strong>'.$ans_user.'</strong> - <span class="time-sec">'.get_user_date(convert_utc_to_local($ans_ts)).'</span>'; ?></br>
						</div></br>
						<div class="ans-text-section"><?php echo $ans."</br>"; ?></div></br>
						<?php 
							$sql_check_up_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' and post_type='A' and vote_type=0 and post_id=".$ansid;
							$sql_check_down_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
												and post_type='A' and vote_type=1 and post_id=".$ansid;
							$stmt_check_up_vote = $conn->prepare($sql_check_up_vote);
							$stmt_check_up_vote->execute();
							$sql_row_0 = $stmt_check_up_vote->fetch();
							$count_row_0 = $sql_row_0['vote_count'];
							$stmt_check_down_vote = $conn->prepare($sql_check_down_vote);
							$stmt_check_down_vote->execute();
							$sql_row_1 = $stmt_check_down_vote->fetch();
							$count_row_1 = $sql_row_1['vote_count'];
								
							?>
							<input type="hidden" id="upvote-front-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_0; ?>" />
							<input type="hidden" id="downvote-front-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_1; ?>" />
							
						<div class="voting-links">
							<span class="vote-sec">
						<?php 
									if($ans_user != $_SESSION['user'])	{
								?>
							<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
								onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,0,'<?php echo $slashes; ?>', document.getElementById('upvote-front-value-ans-<?php echo $ansid; ?>').value,2)">
								<span id="glyph-front-up-ans-<?php echo $ansid; ?>" class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-ans-upvoted":"";  ?>"></span>
							<span id="up-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span></span>
							<?php } 
										else	{
									?>
								<span class="glyphicon glyphicon-thumbs-up"></span>
								<span id="up-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
								<?php } ?>
						</span>
						<span class="vote-sec">
						<?php 
									if($ans_user != $_SESSION['user'])	{
								?>
							<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
								onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,1,'<?php echo $slashes; ?>',document.getElementById('downvote-front-value-ans-<?php echo $ansid; ?>').value,2)">
								<span id="glyph-front-down-ans-<?php echo $ansid; ?>"  class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-ans-downvoted":"";  ?>"></span>
							<span id="down-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span></span>
						<?php } 
									else	{
								?>
							<span class="glyphicon glyphicon-thumbs-down"></span>
							<span id="down-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
							<?php } ?>
						</span>
						&nbsp;&nbsp;
						<a id="comment-link-<?php echo $ansid; ?>" class="comment-link" href="javascript:void(0)" onclick="showComment(2,<?php echo $ansid; ?>)">Show comments</a>
						</div>
						<div class="comment-section" id="comment-front-<?php echo $ansid; ?>">
						</br>
						<input type="text" class="form-control comment-inp" id="comment-front-ans-<?php echo $ansid; ?>" placeholder="Leave comment" 
						onkeypress=""/>
						
						</br>
						<button type="button" class="btn btn-primary" style="padding: 1px 2px;" 
						onclick="addComment(2,<?php echo "'".$slashes."',".$ansid.",'".$ans_user."',".$qid.",'".$posted_by."'"; ?>)">Comment</button></br></br>
						
						<div class="comments-list" id="comment-area-front-<?php echo $ansid; ?>">
						<?php
							try	{
											$comment_array=array();
											$comment_id_str="";
											$sql_fetch_comment_ids="select comment_id from comments where ans_id=".$ansid." order by created_ts desc";
											$stmt_fetch_comment_ids=$conn->prepare($sql_fetch_comment_ids);
											$stmt_fetch_comment_ids->execute();
											if($stmt_fetch_comment_ids->rowCount() > 0)	{
												while($row = $stmt_fetch_comment_ids->fetch())	{
													$cmt_id=$row['comment_id'];
													array_push($comment_array,$cmt_id);
												}
												$comment_id_str=implode("|",$comment_array);
											}
											
											$sql_fetch_comment="select comment_id,comment_desc,posted_by,created_ts from comments where 	ans_id=".$ansid." order by created_ts desc limit 5";
											$stmt_fetch_comment=$conn->prepare($sql_fetch_comment);
											$stmt_fetch_comment->execute();
											
											if($stmt_fetch_comment->rowCount() > 0)	{
												while($row_cmnt = $stmt_fetch_comment->fetch())	{
													$comment_id=$row_cmnt['comment_id'];
													$comment=$row_cmnt['comment_desc'];
													$posted_by=$row_cmnt['posted_by'];
													$created_ts = $row_cmnt['created_ts'];
													echo '<div class="user-comment-sec" id="comment-list-front-'.$comment_id.'">'.$comment.' - <strong>'.$posted_by.'</strong>&nbsp;&nbsp;<span class="time-sec">'.get_user_date(convert_utc_to_local($created_ts)).'</span></div>';
												}
											}
											else	{
												echo "No comments in this answer yet";
											}
										}
										catch(PDOException $e)	{
											echo "Internal server error";
										}
									?>
									</div></br>
									<?php
									$comment_count = $stmt_fetch_comment_ids->rowCount();
									if($comment_count > 5)
										echo "<span id='comment-load-front-text-".$ansid."' href='javascript:void(0)' onclick='loadMoreComments(2,\"".$slashes."\",".$ansid.")' class='show-comment-text'>View more comments</span>";
									?>
									<input id="cid-front-section-<?php echo $ansid; ?>" type="hidden" value="<?php echo $comment_id_str; ?>"/>
					</div></br>									
					</div>
					<?php
				}
			}
			else	{
				echo "<div class='no-ans-section'>No answers to this question yet. Be the first one to answer.
						<a href='".$slashes."qstn_ans.php?qid=".$qid."'>Click here</a></div>";
			}
				
		}
		catch(PDOException $e) {
			echo "Some error occured. We are working on it and will get back to you. Sorry for the inconvenience caused ";
		}
	?>
