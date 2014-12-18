// This file provides a page with the things necessary to do inline table editing

$.inlineEdit({ // from http://www.labs.mimmin.com/inlineedit

	categoryName: 'passives.php?type=name&categoryId=',
	categoryPrice: 'ajax.php?type=price&categoryId=',
	remove: 'ajax.php?remove&type=price&categoryId='
	
}, {
	
	animate: false,
	
	filterElementValue: function($o){
		if ($o.hasClass('categoryPrice')) {
			return $o.html().match(/\$(.+)/)[1];
		} else {
			return $o.html();
		}
	},
	
	afterSave: function(o){
		if (o.type == 'categoryPrice') {
			$('.categoryPrice.id' + o.id).prepend('$');
		}
	}
	
});

// Table sorter document ready function
$(document).ready(function() 
{ 
  $('#myTable').tablesorter({
    theme: 'green',
    widgets: ['stickyHeader','zebra'],
    widgetOptions: {
      saveSort: true,
      stickyHeaders: "tablesorter-stickyHeader"
    }
  });
});

$(function(){

  $.tablesorter.addParser({
    // set a unique id
    id: 'customkey',
    is: function(s) {
      // return false so this parser is not auto detected
      return false;
    },
    format: function(s, table, cell, cellIndex) {
      var $cell = $(cell);
      return $cell.attr('customkey') || s;
    },
    parsed: true,
    type: 'text'
  });
});
