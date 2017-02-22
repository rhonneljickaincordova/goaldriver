<style type="text/css">

    h3
    {
    	text-align:center;
    }
    .meeting-details
    {
    	margin-top:20px;
    	line-height: 30px;
    }
    .space_55
    {
    	margin-right:55px;
    }
    .space_45
    {
    	margin-right:45px;
    }
    .item-label
    {
    	font-size:12px;
    	color:red;
    	margin-left: 6%;
    }
    .item-content
    {
    	font-size:12px;
    	color:green;
    }
    .ntd_boxes
    {
    	width:95% !important;
    	height:100px;
    	border:0.5px solid black;
    }
</style>

<?php if(!empty($meetings)) :?>

<?php
	$meeting_id = $this->input->get("meetingID");
	$meeting = $meetings[0];

	$organ_id = $this->session->userdata('organ_id');
	$meeting_logo = get_organ_logo($organ_id);

	if(!empty($meeting_logo))
	{
		$logo = $meeting_logo[0];
	}

    $url = base_url()."index.php/meeting/workspace/".$meeting_id;

    $tags = unserialize($meeting['meeting_tags']);

    if(!empty($meeting['meeting_participants']))
    {
      $participants = unserialize($meeting['meeting_participants']);
    }

    if($meeting['meeting_optional'] != "NA")
    {
      $optionals = unserialize($meeting['meeting_optional']);
    }

    if($meeting['meeting_cc'] != "NA")
    {
      $ccs = unserialize($meeting['meeting_cc']);
    }
?>

<html>
	<head>
		<title><?php echo $meeting['meeting_title']?></title>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Old+Standard+TT:400,400italic,700' rel='stylesheet' type='text/css'>
		
		<style type="text/css">

			@page { margin: 0px; padding:0; }
			body{
				margin:0;
				padding: 0;
				font-family: 'Open Sans', sans-serif;
			}
			h1, h2{
				font-family: 'Old Standard TT', serif;
				font-style: italic;
				font-weight: normal;
				font-size: 50px;
			}	
			#toc, #content{
			<?php if(isset($print_settings['spacing']) AND $print_settings['spacing'] != ''): ?>
			line-height: <?php echo $print_settings['spacing']; ?>em;
			<?php endif; ?>
			}
			div#content{ padding:0; margin:0; }
			.container{
				padding: 2% 5%;
				width: 100%;
				margin: 0 auto;
			}
			.logo_header
			{
				padding: 0% 5%;
			}
			#cover-page{
				margin-bottom: 2em;
			}
			#toc{
				margin-bottom: 2em;
			}
			.header{
				background: #2eaf4a;
				padding: 20px 40px;
				color: #fff;
			}
			.footer{
				padding: 20px 40px;
				border-top:1px solid #ccc;
			}
			ul.first{
				padding: 0;
				margin: 0;
			}
			ul li{
				list-style: none;
			}
			h1.business-plan{
				font-size: 25px;
				font-family: 'Open Sans', sans-serif;
				font-style: normal;
			}	
			.detailTitle{
				font-size:16px;
				font-family: 'Open Sans', sans-serif;
				font-style: normal;
			}
			.detailContent{
				margin-left:70px;
				font-family: 'Open Sans', sans-serif;
				font-style: normal;
				margin-bottom:10px;
				
			}
			.pagebreak { page-break-before: always; }


		</style>

	</head>
	<body style="margin-top:60px;margin-bottom:60px;">

		<div id="cover-page" style="page-break-after: always;">
			<div class="logo_header">
				<?php if(!empty($logo)) :?>
					<div class="logo-container">
						<img class="img-responsive" width="150" src="uploads/user_logo_images/<?php echo $logo['image_name'] ?>" />
					</div>
				<?php endif;?>
				<br>
			</div>

			<div class="container">
				<h1 class="business-plan">Meeting Details</h1>
				<br><br>
				<div class="detailTitle" style="font-size:24px"><?php echo $meeting['meeting_title']?></div>
				<br><br> 
			    <div class="detailTitle">Date and Time</div>
			    <br> 
			    <div class="detailContent">From : <?php echo $meeting['when_from_date']?></div>
    			<div class="detailContent">To : <label style="padding-left:20px;"><?php echo $meeting['when_to_date'] ?></label></div>
				<br><br>
				<div class="detailTitle">Attendees: </div>
				<div class="detailContent">
					<?php if(!empty($participants)) :?>
						<?php 
							$total = count($participants);
							$i = 0;
						?>
		                <?php foreach($participants as $par) :?>
		                	<?php
		                		$i++;
		                	?>
		                	<?php echo user_info("first_name", $par)." ".user_info("last_name", $par) ?>
		                	<?php if($i != $total) echo ", " ;?>
		                <?php endforeach ;?>
		            <?php endif;?>
				</div>
				<br><br>
				<div class="detailTitle">Topics: </div>
				<div class="detailContent">
					<?php 
						$topic_count = 1;
						$presenter = "";
						foreach($topics as $key=>$value)
						{
							echo "<p>".$topic_count.". ".$value['topic_title'];
							echo (!empty($value['presenter'])) ? " - Presenter: ".user_info("first_name", $value['presenter'])." ".user_info("last_name", $value['presenter']) : "";
							echo (!empty($value['time'])) ? ", Duration: ".$value['time']."</p>" : "";

							$topic_count++;
						}
					?>
				</div>
			</div>
		</div>


		<div id="content">
			<div class="logo_header" style="">
				<?php if(!empty($logo)) :?>
					<div class="logo-container">
						<img class="img-responsive" width="150" src="uploads/user_logo_images/<?php echo $logo['image_name'] ?>" />
					</div>
				<?php endif;?>
			</div>
			<?php
				$topic_count = 1;
				$subtopic_count = "A";
				$presenter = "";
				foreach($topics as $key=>$value)
				{
					if(!empty($value['presenter']))
					{
						$presenter = " - Presenter: ".user_info("first_name", $value['presenter'])." ".user_info("last_name", $value['presenter']);
					}

					echo "<ul style='text-decoration:none'>";
					echo "<li style=''>" .$topic_count.". ".$value['topic_title'];
					echo (!empty($value['presenter'])) ? " - Presenter: ".user_info("first_name", $value['presenter'])." ".user_info("last_name", $value['presenter']) : "";
					echo ", Duration: " .$value['time'] ."</li>";
					echo "<li style='word-wrap:break-word'>".get_topic_ntd_pdf($value['topic_id'])."</li>";

						$subtopics = get_subtopics_for_email($value['topic_id']);

						foreach($subtopics as $sub)
						{
							echo "<ul>";
								echo "<li>";
								echo "<p style='' style=''>- ".$sub['subtopic_title']."</p>";
								echo "<p style='word-wrap:break-word;margin-left:10px !important'>".get_subtopic_ntd_pdf($sub['subtopic_id'])."</p>";
								echo "</li>";

							echo "</ul>";
						}

					if(!empty($show_boxes))
					{
						echo "<div id='ntd_boxes_cont'>";
						echo "<p>".$value['topic_title']." Notes</p>";
						echo "<div class='ntd_boxes'></div>";
						echo "<p>".$value['topic_title']." Tasks</p>";
						echo "<div class='ntd_boxes'></div>";
						echo "<p>".$value['topic_title']." Decisions</p>";
						echo "<div class='ntd_boxes'></div>";
						echo "</div>"; 
					}

					echo "</ul><br />";

					$topic_count++;
					$subtopic_count++;
				}
			?>
		</div>


	</body>
</html>
<?php endif;?>
