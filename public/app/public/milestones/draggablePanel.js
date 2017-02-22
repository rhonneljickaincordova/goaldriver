jQuery(document).ready(function(){
	jQuery(function($) {
        var panelList_priority = $('#draggablePanelList_priority');

        panelList_priority.sortable({
            handle: '.panel-heading', 
            update: function() {
                $('.panel', panelList_priority).each(function(index, elem) {
                     var $listItem = $(elem),
                         newIndex = $listItem.index();
                });
            }
        });
    });

  jQuery(function($) {
        var panelList_milestone = $('#draggablePanelList_milestone');

        panelList_milestone.sortable({
            handle: '.panel-heading', 
            update: function() {
                $('.panel', panelList_milestone).each(function(index, elem) {
                     var $listItem = $(elem),
                         newIndex = $listItem.index();
                });
            }
        });
    });

   jQuery(function($) {
        var panelList_percentage = $('#draggablePanelList_percentage');

        panelList_percentage.sortable({
            handle: '.panel-heading', 
            update: function() {
                $('.panel', panelList_percentage).each(function(index, elem) {
                     var $listItem = $(elem),
                         newIndex = $listItem.index();
                });
            }
        });
    });
});