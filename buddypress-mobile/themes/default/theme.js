var $j = jQuery.noConflict();

$j(document).ready(function(){

		$j('#rightnav a').live('click', function(event){
   			$j('#mobileNav').toggleClass('show');
   			$j('#loginNav').removeClass('show');
   			$j('#notifications-header').removeClass('show');		
	});   
	
		$j('#mobileNav ul li a').live('click', function(event){
   			$j(this).addClass('navLoad');
   					
	});

		$j('#leftnav-login a').live('click', function(event){
   			$j('#loginNav').toggleClass('show');	
   			$j('#mobileNav').removeClass('show');	
   			$j('#notifications-header').removeClass('show');
	}); 
	
		$j('#content').live('click', function(event){
   			$j('#mobileNav').removeClass('show');
   			$j('#loginNav').removeClass('show');
   			$j('#notificationsheader').removeClass('show');		
	});   
	
	$j('#notifications-badge').live('touchstart', function(event){
   			$j('#notifications-header').toggleClass('show');
   			$j('#loginNav').removeClass('show');
   			$j('#mobileNav').removeClass('show');	
   				
	}); 
	


		$j('#theme-switch').live('click', function(event){
			$j.cookie( 'bpthemeswitch', 'normal', {path: '/'} );			
	}); 		
	
		$j('#theme-switch-site').live('click', function(event){
			$j.cookie( 'bpthemeswitch', 'mobile', {path: '/'} );			
	});   
		
});
