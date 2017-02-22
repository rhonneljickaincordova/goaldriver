<?php 
foreach ($coverpage[0] as $key => $value) {
	$$key = $value;
}

$print_settings = unserialize($print_options);

$path = base_url('uploads/'.$company_logo);
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
?>
<html>
<head>
	<title><?php echo $company_name; ?></title>
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
		font-size: 30px;
		font-family: 'Open Sans', sans-serif;
		font-weight: bold;
		color: #2eaf4a;
		font-style: normal;
	}
	</style>
</head>
<body>
	
		<div id="cover-page" style="page-break-after: always;">
			<div style="padding:50px 0 100px 0;background:#2eaf4a;text-align:left;color:#fff;">
				<div class="container">
					<?php if($company_logo != ''): ?>
					<div style="margin-bottom:10em;"><img src="<?php echo $base64; ?>" width="200"></div>
					<?php endif; ?>
					<?php echo (@$print_settings['confidentiality_msg'] != '') ? '<p>'.$confidentiality_message.'</p>' : ''; ?>

					<span style="font-size:60px;font-family: 'Old Standard TT', serif;font-weight:bold;"><?php echo $company_name; ?></span>
				</div>
			</div>

			<div class="container">
				
				<h1 class="business-plan">Business Plan</h1>
				<h3 style="margin-top:150px;">Prepared <?php echo date('F').' '.date('Y'); ?></h3>
				<strong>Contact Information</strong><br />
				<strong><?php echo $contact_name; ?><br><a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a></strong>
			</div>
		</div>

		<?php if(isset($print_settings['is_toc']) && $print_settings['is_toc'] == 1): ?>
		<div id="toc" style="page-break-after: always;">
			<div class="container">
				<h1>Table of Contents</h1>
				<?php 
				$count = 1;
				foreach ($chapters as $chapter): ?>
					<h4 style="margin-bottom:-10px;"><?php echo $chapter->title; ?></h4>
					<div style="text-align:right;border-bottom:1px solid #ccc;"><?php echo $count; ?></div>

					<ul>
						<?php 
						$sections = $this->section->get_ordered_section($chapter->chapter_id);
						foreach ($sections as $section): 
							$subsections = $this->subsection->get_many_by('section_id', $section->section_id);
							?>
							<li>
								<?php echo $section->title; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php $count++; endforeach; ?>
			</div>
		</div>
		<?php endif; ?>

		<div id="content">
			<?php 
			$count = 1;
			foreach ($chapters as $chapter): ?>
				<div id="page<?php echo $count; ?>" <?php echo ($count < count($chapters)) ? 'style="page-break-after: always;"' : ''; ?>>
					<div class="header" style="position:fixed:top:0;">
						<div style="text-align:right;">
							<?php 
							if(isset($print_settings['is_paging']) AND $print_settings['is_paging'] == 1){
								$page = $print_settings['page'];

								switch ($page) {
									case '1':
										echo $count;
										break;
									case 'p1':
										echo 'Page '.$count;
										break;
									case '1-10':
										echo $count.' of '.count($chapters);
										break;
									case 'p1-p10':
										echo 'Page '.$count.' of '.count($chapters);
										break;
								}
							}
							?>
						</div>
						<?php echo $company_name; ?>
					</div>
					<div class="container">
						<h2><?php echo $chapter->title; ?></h2>
						<ul class="first">
							<?php 
							$sections = $this->section->get_ordered_section($chapter->chapter_id);
							foreach ($sections as $section): 
								$subsections = $this->subsection->get_many_by('section_id', $section->section_id);
								?>
								<li>
									<h3><?php echo $section->title; ?></h3>
									<ul>
									<?php foreach ($subsections as $subsection): ?>
										<li>
											<strong><?php echo $subsection->title; ?></strong>
											<p style="color:#888;"><?php echo html_entity_decode($subsection->data); ?></p>
										</li>
									<?php endforeach; ?>
									</ul>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php if(@$print_settings['is_confidential_msg'] == 1): ?>
					<div class="footer" style="position:fixed:bottom:0;">
						<?php echo @$print_settings['confidentiality_msg']; ?>
					</div>
					<?php endif; ?>
				</div>
			<?php $count++; endforeach; ?>
		</div>
	</body>
</html>