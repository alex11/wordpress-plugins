
jQuery(document).ready(function($) {
	var $was_open = $.cookie('tabbed-widget-open');
	
	$('.tabbed-widget').each(function() {
		var $this_widget = this;
		
		var $widgetid = $(this).attr("id"); // tabbed-widget-1
		$widgetid = $widgetid.split("-", 3)[2];
		
		var $widgetstyle = $rotateoptions[$widgetid]["style"];
		var $do_rotate = $rotateoptions[$widgetid]["rotate"];
		var $rotate_interval = $rotateoptions[$widgetid]["interval"];
		var $random_start = $rotateoptions[$widgetid]["random_start"];
		var $start_tab = $rotateoptions[$widgetid]["start_tab"];
		
		if ($was_open) {
			if ($was_open.split('-')[0] == $widgetid) {
				$start_tab = parseInt($was_open.split('-')[1]);
				var $open_previous = true;
			}
		} else if ($random_start && !$open_previous) {
			$start_tab = Math.floor($tab_count * Math.random());
		}
		
		if ($widgetstyle == 'tabs') {
			var $tab_count = 0;
			
			// Build  tab navigation
			var $tabbed_nav = '<ul class="tw-tabbed-nav">';
			$('.tw-title', this).each(function(i) {
				$tabbed_nav += '<li id="tab-link-'+ $widgetid +'-'+ i +'"><a href="#tw-content-'+ $widgetid +'-'+ i +'">' + $(this).text() + '</a></li>';
				$tab_count++;
			}).hide();
			$tabbed_nav += '</ul>';
			
			$('.tw-tabs', this).prepend($tabbed_nav);
			
			var $options = { cookie: { expires: 30 } };
			jQuery.extend($options, { active: $start_tab });
			
			if ($do_rotate) {
				$('.tw-tabs', this).tabs($options).tabs('rotate', $rotate_interval);
				$('.tw-tabs', this).hover(function() {
					$(this).tabs('rotate', null);
				}, function() {
					$(this).tabs($options).tabs('rotate', $rotate_interval);
				});
			} else {
				$('.tw-tabs', this).tabs($options);
			}
			
		} else if ($widgetstyle == 'accordion') {
			var $tab_count = 0;
			var $acco = $('.tw-accordion', this);
			
			$('.tw-title', this).each(function(i) {
				$(this).html('<a href="#">'+$(this).text()+'</a>');
				$tab_count++;
			});
		
			var $options = { autoHeight: false, header: '.tw-title' };
			
			jQuery.extend($options, { active: $start_tab });
		
			$acco.accordion($options);
			
			$('.tw-content:first', this).addClass('tw-widget-first');
			$('.tw-content:last', this).addClass('tw-widget-last');
			
			if ($do_rotate) {				
				var $cleared = false;
				var $wasstopped = false;
				
				(function() {
				    var t = 0;
					var $step = 0;
					var $saverotation;
					
					function dorotate() {
						t = ++t;
						if (t == $tab_count) { $step = -2; t = t + $step;  }
						else if (t == 1) { t = t; $step = 0; }
						else { t = t + $step; }
						$acco.accordion('activate', t);
				    }
					
				    if (!$cleared) 
						var rotation = setInterval(function(){ dorotate(); }, $rotate_interval);
					
					$acco.bind("mouseenter", function(){
						clearInterval(rotation);
						rotation = null;
						$cleared = true;
					}).bind("mouseleave",function(){
						if (!$wasstopped) rotation = setInterval(function(){ dorotate(); }, $rotate_interval);
					}).bind("click",function(){
						$wasstopped = true;
						clearInterval(rotation); rotation = null;
					});
					
				})();
			}			
		}		
	});
	
	$('.ui-accordion-content').click(function() {
		var $id = $(this).parent().attr('id').replace('tw-content-', '');
		console.log($id);
		if ($id.length > 0)
			$.cookie('tabbed-widget-open', $id);
	});
	
});