$(document).ready(function(){
	window.Organisation_list = $("#organisation_table").DataTable({
	"aoColumnDefs":[
		{	
			"targets": [ 0 ], 
			className: "organ_id hidden", 
			sortable:false	
		},
		{
			"aTargets": [ 1 ],  
			className: "org_name", 
			// "mRender": function(data, type, row){
			// 	var text_re = '<a href="'+base_url+'index.php/user-settings/organisations/change_organisation/'+row[0]+'">'+data+'</a>';
			// 	return text_re;
			// }
		},
		{ 
			"targets": [ 2 ], 
			className: "updated" 
		},
		{ 
			"targets": [ 3 ], 
			className: "access" ,
			"mRender": function(data, type, row){
				if(data.toLowerCase() == "admin"){
					var text_re = '<p style="color:#E85151;">'+data+'</p>';
				}else{
					var text_re = '<p style="color:#3E981A;">'+data+'</p>';
				}
				
				return text_re;
			}		
			
			 
		},
		{ 
			"targets": [ 4 ], 
			className: "edit_delete", 
			"sortable":false ,
			"mRender": function(data, type, row){
				var access = row[3];
				var text_re = '-----'
				if(access.toLowerCase() == "admin")
				{
					text_re = "<a href='#' data-toggle='modal' data-target='#editOrganisationModal' class='edit_org' style='margin-right:10px;text-decoration: none;'>";
					text_re += "<i class='fa fa-pencil' style='font-size:15px;'></i>";
					text_re += "</a>";
					text_re += "<a href='#' data-toggle='modal' data-target='#deleteOrganisationModal' class='delete_org' style='text-decoration: none;'>";
					text_re += "<i class='fa fa-trash-o'></i>";
					text_re += "</a>";			
				}
				
				return text_re;
			}
		}
  ],
  "paginate" :false,
  "dom" : "t",
   "order": [[ 1, "asc" ]]
  /* 
  "bLengthChange" :false,
  "processing" :false,
  "filter" :false, */
  
		
	});
	
});








