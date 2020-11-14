$.tabs = function(selector, start) {
	$(selector).each(function(i, element) {
		$($(element).attr('tab')).css({'height':'0px', 'overflow':'hidden','position':'relative'});
		
		$(element).click(function() {
			$(selector).each(function(i, element) {
				$(element).removeClass('selected');
				
				$($(element).attr('tab')).css({'height':'0px', 'overflow':'hidden','position':'relative'});
			});
			
			$(this).addClass('selected');
			
			$($(this).attr('tab')).css({'height':'auto', 'overflow':'visible','position':'relative'});
		});
	});
	
	if (!start) {
		start = $(selector + ':first').attr('tab');
	}

	$(selector + '[tab=\'' + start + '\']').trigger('click');
};