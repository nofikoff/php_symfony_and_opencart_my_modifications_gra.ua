$(function(){
	
	var searchField = $("#filter_keyword");
	
			if(searchField.offset().left == 0) {
				searchField = $( ".search2 input[name='search']" );
			}


	$('body').append('<div id="suggest_container"></div>').css({"position": "relative"});
	

	var suggestContainer = $('#suggest_container');
	var suggestTimer;               
	var suggestTime = 500;  

	suggestContainer.before('<div style="display: none" id="cross_suggest"><a style="color:#e47364; z-index: 9999; position: absolute; width:20px; height:20px;" id="clear_suggest"><i class="fa fa-times"></i></a></div>');
	
	
	$('#container').click(function( event ){
		
		var eventInDropDown = $( event.target ).parents('#search');
		 if( !eventInDropDown.length ){
				setTimeout(function() {
					suggestContainer.hide(300).text('');
				}, 500);

			}	
	})			
	
	searchField		
		.focus(function(){ 
		//repositionLivesearch(); i.show(); 
			setPosition(searchField, suggestContainer);
		})
		.keydown(function () {
			clearTimeout(suggestTimer);
		})
		.keyup(function (e) {
			clearTimeout(suggestTimer);
			
			inputText = (searchField.val());
			if (inputText.length > 2) {
		
			query  = encodeURIComponent(inputText);
			
				suggestContainer.text('');

				suggestTimer = setTimeout(function() {
				
					$.get('https://gra.ua/index.php?route=module/sphinxpro/suggest&search=' + query, function(json) {
						
						var data = JSON.parse(json);
					
						renderSuggestContainer(data) 
		
								
					}, "json");
				
				}, suggestTime);
				
			}

		});
	

		function setPosition(searchField, suggestContainer) {
			
			$( window ).resize(function() {
			   setPosition(searchField, suggestContainer);
			});
	
			
			var offset = searchField.offset();
			inputH = searchField.outerHeight() + 1;
			inputW = searchField.parent().innerWidth();
			suggestContainer.css({"display": "none", "position": "absolute", "width": inputW, "top": offset.top + inputH + "px", "left": offset.left + "px"});
						
			
		}
		
		
		function renderSuggestContainer(data) {
			
			suggestContainer.text('');
		
						
				if(data.products.length || data.categories.length) {
						
					var ul = document.createElement('ul');
					
					if(data.suggested) {
						var div = document.createElement('div');
						$(div).html(data.suggested)
							.addClass('suggest_header')
							.appendTo(suggestContainer);
					}
						
					
					if(data.categories.length) {
						var li = document.createElement('li');
						$(li).text('Найдено в категориях:')
							.appendTo(ul);
							
						$.each(data.categories, function( i, el ){
							var li = document.createElement('li');
							var a = document.createElement('a');
							$(a).appendTo(li);
							$(a).attr('href', el.href);
						//	$(a).html('<span class="sugg_image"><img src=\"' + el.image + '\"></span><span class="sugg_text">' + el.name + '</span>');
							$(a).html('<div class="sugg_text">' + el.name + '<span class="sugg_total"> (' +  el.qty + ') <span></div>');
							$(li).addClass('sugg_hovered');
							$(li).appendTo(ul);
							
						});

						
					}
					
					if(data.products.length) {
					
							var li = document.createElement('li');
							$(li).text('Найденные товары:');
							$(li).appendTo(ul);
						
						$.each(data.products, function( i, el ){
							var li = document.createElement('li');
							var a = document.createElement('a');
							$(a).appendTo(li);
							$(a).attr('href', el.href);
						//	$(a).html('<span class="sugg_image"><img src=\"' + el.image + '\"></span><span class="sugg_text">' + el.name + '</span>');
							$(a).html('<div class="sugg_text">' + el.name + '</div><div class="sugg_sku" > код товара: ' + el.sku + '</div>');
							$(li).addClass('sugg_hovered');
							$(li).appendTo(ul);
							
						});
						
			
						if(data.products.length > 1) {
							
							var li = document.createElement('li');
							var a = document.createElement('a');
							$(a).attr('href', '/index.php?route=product/search&filter_name=' + query)
								.text(data.more.text)
								.appendTo(li);
							$(li).addClass('sugg_hovered')
								 .appendTo(ul);
					
						}
					
					}
					
					$(ul).appendTo(suggestContainer);
										
				};
			
			suggestContainer.show(100);
				
		}
		
		
		$('.search_button').live('click', function(e) {
			e.preventDefault();
			
			query = '';
			inputText = (searchField.val());
			if (inputText.length > 2) {
				
				var url = location.protocol + "//" + location.host + '/' + 'index.php?route=product/search&keyword=' + encodeURIComponent(inputText);
				location = (url);
			}
			
			
		});

					

});