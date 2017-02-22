<div class="col-sm-3" id="sidebar">
	<ul id="plan" class="list-group">
		<?php 
		$current_subsection_position = decrypt($this->uri->segment(5));

		// print_r($chapters); return;
		$current_chapter = decrypt($this->uri->segment(3));
		$current_section = decrypt($this->uri->segment(4));
		$chapter_html = '';
		if(count($chapters)){
			foreach ($chapters as $chapter) {
				$chapter_html .= '<li class="list-group-item '.($current_chapter == $chapter->chapter_id ? 'active' : '').'" id="item-'.$chapter->chapter_id.'"><a href="'.site_url('plan/chapter/'.encrypt($chapter->chapter_id)).'"><i class="fa fa-bars sort-chapter" aria-hidden="true"></i> '.$chapter->title.' <span class="toggle-sections pull-right"><i class="'.($current_chapter == $chapter->chapter_id ? 'fa fa-minus' : 'fa fa-plus').'"></i></span></a> 
						<span class="chapter-option">
							<span data-toggle="tooltip" data-placement="bottom" title="Edit chapter name"><i chapter="'.$chapter->chapter_id.'" chapter_title="'.$chapter->title.'" data-target="#edit-chapter" data-toggle="modal" class="edit-chapter fa fa-pencil-square-o"></i></span>
							<span data-toggle="tooltip" data-placement="bottom" title="Delete chapter"><i chapter="'.$chapter->chapter_id.'" chapter_title="'.$chapter->title.'" class="delete-chapter fa fa-trash-o"></i></span>
						</span>';

				$style = 'style=""';
				if($current_chapter == $chapter->chapter_id){
					$style = 'style="display:block"';
				}

				//if($current_chapter == $chapter->chapter_id){
					$chapter_section = $this->section->get_ordered_section($chapter->chapter_id);
					if(count($chapter_section)){
						$count_section = 1;
						$chapter_html .= '<ul id="section" class="sub-menu" '.$style.'>';
						foreach ($chapter_section as $section) {
							$subsections = $this->subsection->get_subsections($section->section_id);
							
							$chapter_html .= '<li class="list-item" id="item-'.$section->section_id.'" '.($current_section == $section->section_id ? 'selected' : '').'"><a href="'.site_url('plan/chapter/'.encrypt($chapter->chapter_id).'/'.encrypt($section->section_id)).'"><i class="fa fa-bars sort-section" aria-hidden="true"></i>&nbsp;&nbsp;'.$section->title.'</a>';
							if(count($subsections)){
								$count_sub_section = 1;
								$chapter_html .= '<ul id="subsection-'.$count_sub_section.'" class="subsection">';
								foreach ($subsections as $subsection) {
									$chapter_html .= '<li id="item-'.$subsection->subsection_id.'" class="'.($subsection->subsection_id == $current_subsection_position ? 'current' : '').'"><a href="'.site_url('plan/chapter/'.encrypt($chapter->chapter_id).'/'.encrypt($section->section_id)).'/'.encrypt($subsection->subsection_id).'"><i class="fa fa-bars sort-subsection" aria-hidden="true"></i>&nbsp;&nbsp;'.$subsection->title.'</a></li>';
									$count_sub_section++;
								}
								$chapter_html .= '</ul>';
							}
							$chapter_html .= '</li>';


							$count_section++;
						}
						$chapter_html .= '<li><a href="#new-section" data-toggle="modal"><i class="fa fa-plus"></i> Add more</a></li>';
						
						$chapter_html .= '</ul>';
					}
				//}

				$chapter_html .= '</li>';
			}

		}
		else{
			$chapter_html = '<li class="list-group-item">No Chapter created yet.</li>';
		}
		echo $chapter_html;
		?>
	</ul>
	<a href="#new-chapter" data-toggle="modal" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> Add chapter</a>
</div>

