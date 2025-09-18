/*--------------------------------------------------------------------------*
 *  SmoothScroll targetBlank browser
 *--------------------------------------------------------------------------*/
 	    
		$(function(){
			$("#topTo a").click(function(){
			$('html,body').animate({ scrollTop: $($(this).attr("href")).offset().top }, 'slow','swing');
			return false;
		})

	
		$('.main-inner a[target=_blank]').click( function() {
			window.open(this.href);
			return false;
			});

	
	if ( $.browser.msie && $.browser.version <= 6 ) {
	$('body').prepend('<div class="errors">あなたは旧式ブラウザをご利用中です。このウェブサイトを快適に閲覧するにはブラウザをアップグレードしてください。</div>');
	}
	});