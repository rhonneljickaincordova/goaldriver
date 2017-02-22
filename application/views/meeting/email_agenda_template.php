<body class="custom-body" bgcolor="#FFFFFF" style="background-color:#f5f5f5;margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;-webkit-font-smoothing: antialiased;-webkit-text-size-adjust: none;height: 100%;width: 100%!important;">

<?php if(!empty($informations)) :?>

<?php
	$information = $informations[0];

	$organ_id = $this->session->userdata('organ_id');
	$meeting_logo = get_organ_logo($organ_id);

	if(!empty($meeting_logo))
	{
		$logo = $meeting_logo[0];
	}

	$user_email = $this->session->userdata('email');
    $tags = unserialize($information['meeting_tags']);
    $url = base_url()."index.php/meeting/workspace/".encrypt($meeting_id)."/".encrypt($information['organ_id']);

    if(!empty($information['meeting_participants']))
    {
      $participants = unserialize($information['meeting_participants']);
    }

    if($information['meeting_optional'] != "NA")
    {
      $optionals = unserialize($information['meeting_optional']);
    }

    if($information['meeting_cc'] != "NA")
    {
      $ccs = unserialize($information['meeting_cc']);
    }
?>

<!-- HEADER -->

<table class="head-wrap" bgcolor="" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 100%;">

<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">

<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"></td>

<td class="header container" style="margin: 0 auto!important;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;display: block!important;max-width: 600px!important;clear: both!important;">



<div class="content" style="margin: 0 auto;padding: 15px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;max-width: 600px;display: block;">

	<table bgcolor="" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 100%;">

	<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">

		<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
			<img src="" class="custom-img" style="margin: 0 auto;margin-left: 27%;margin-top: 5%;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;max-width: 100%;">
		</td>
		<td align="right" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"><h6 class="collapse" style="margin: 0!important;padding: 0;font-family: &quot;HelveticaNeue-Light&quot;, &quot;Helvetica Neue Light&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Lucida Grande&quot;, sans-serif;line-height: 1.1;margin-bottom: 15px;color: #444;font-weight: 900;font-size: 14px;text-transform: uppercase;"></h6></td>

	</tr>

</table>

</div>



</td>

<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"></td>

</tr>

</table><!-- /HEADER -->





<!-- BODY -->

<table class="body-wrap" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 100%;">

<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">

<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"></td>

<td class="container" bgcolor="#FFFFFF" style="margin: 0 auto!important;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;display: block!important;max-width: 600px!important;clear: both!important;">



<div class="content" style="margin: 0 auto;padding: 15px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;max-width: 600px;display: block;">





<table style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 100%;">

<?php if(!empty($logo)) :?>
	<img class="img-responsive" width="150" src="<?php echo base_url("uploads/user_logo_images/".$logo['image_name']) ?>"  />
<?php endif;?>

<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">

	<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">

		<h4 style="color: #181A18;font-weight:bold;margin: 0;padding: 0;font-family: &quot;HelveticaNeue-Light&quot;, &quot;Helvetica Neue Light&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Lucida Grande&quot;, sans-serif;line-height: 1.1;margin-bottom: 15px;font-size: 27px;text-align:center">Meeting Agenda<br /> <small style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;font-size: 60%;color: #6f6f6f;line-height: 0;text-transform: none;"><?php echo $information['meeting_title']." - ".$information['when_from_date'] ?></small></h4>

		<h5 style="color: #85C879;margin: 0;padding: 0;font-family: &quot;HelveticaNeue-Light&quot;, &quot;Helvetica Neue Light&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Lucida Grande&quot;, sans-serif;line-height: 1.1;margin-bottom: 15px;font-weight: 900;font-size: 17px;">Details</h5>

		<ul style="list-style-type: none;margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-bottom: 10px;font-weight: normal;font-size: 14px;line-height: 1.6;">
			<li style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-left: 5px;list-style-position: inside;"><label style="font-weight: bold;margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">Date and Time: </label> <small style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"><?php echo $information['when_from_date']." to ".$information['when_to_date'] ?></small></li>

			<li style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-left: 5px;list-style-position: inside;"><label style="font-weight: bold;margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">Participants: </label>
				<small style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">

				<?php if(!empty($participants)) :?>
                    <?php foreach($participants as $par) :?>
                    	<?php echo user_info("first_name", $par)." ".user_info("last_name", $par) ?>,
                    <?php endforeach ;?>
                <?php endif;?>

				</small></li>

			<li style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-left: 5px;list-style-position: inside;"><label style="font-weight: bold;margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">Location: </label><small style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"><?php echo $information['meeting_location'] ?></small></li>

			<li style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-left: 5px;list-style-position: inside;"><label style="font-weight: bold;margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">Projects/Tags: </label> <small style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">

				<?php echo $tags ?>

			</small>
			</li>

			<li style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-left: 5px;list-style-position: inside;"><label style="font-weight: bold;margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">Access meeting at:</label> <small style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"><a href="<?php echo $url ?>"><?php echo $url ?></a></small></li>


				<li style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-left: 5px;list-style-position: inside;"><label style="font-weight: bold;margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">Message :</label> <small style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">

					<?php if(!empty($message)) :?>
						<?php echo $message ?>
					<?php else:?>
						No additional message.
					<?php endif;?>


					</small>
			</li>


		</ul>


		<strong>Topic(s)<strong>


            <?php
				foreach($topics as $key=>$value)
				{
					echo "<ul style='text-decoration:none'>";
					echo "<li>".$value['topic_title'];

					echo "<p>Presenter: ".user_info("first_name", $value['presenter'])." ".user_info("last_name", $value['presenter'])."</p>";
					echo "<p>Duration: ".$value['time']."</p>";

						$subtopics = get_subtopics_for_email($value['topic_id']);

						foreach($subtopics as $sub)
						{
							echo "<ul>";

								echo "<li>";
								echo $sub['subtopic_title'];
								echo "</li>";

							echo "</ul>";
						}

					echo "</ul><br />";
				}
			?>


		<strong>You are invited to this meeting. Would you like to attend?</strong>

		<div>
			<div>

			<?php
				$reci = urlencode($recipients);
				$organizer = urlencode($this->session->userdata("email"));
				$encrypted_meetingID = encrypt($meeting_id);
				$encrypted_organizer = urlencode(encrypt($this->session->userdata("email")));
				$encrypted_reci = urlencode(encrypt($recipients));
			?>

			<a href="<?php echo base_url("index.php/attendance/record-meeting-attendees?organizer=".$encrypted_organizer."&meetingID=".$encrypted_meetingID."&status=yes&email=".$encrypted_reci) ?>" style="cursor:pointer;background-color:#1e8dfa;border:1px solid #1e8dfa;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:30px;text-align:center;text-decoration:none;width:80px;-webkit-text-size-adjust:none;mso-hide:all;">Accept</a>

			<a href="<?php echo base_url("index.php/attendance/record-meeting-attendees?organizer=".$encrypted_organizer."&meetingID=".$encrypted_meetingID."&status=no&email=".$encrypted_reci) ?>" style="cursor:pointer;background-color:#fa1e28;border:1px solid #fa1e28;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:30px;text-align:center;text-decoration:none;width:80px;-webkit-text-size-adjust:none;mso-hide:all;">Decline</a>

			</div>
		</div>


	</td>

</tr>

</table>

</div>



</td>

<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"></td>

</tr>

</table><!-- /BODY -->



<!-- FOOTER -->

<table class="footer-wrap" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 100%;clear: both!important;">

<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">

<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"></td>

<td class="container" style="margin: 0 auto!important;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;display: block!important;max-width: 600px!important;clear: both!important;">


<?php endif;?>

</td>

<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"></td>

</tr>

</table><!-- /FOOTER --></body>
