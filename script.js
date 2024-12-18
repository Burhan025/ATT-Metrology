$(document).foundation({
	equalizer : {
	  // Specify if Equalizer should make elements equal height once they become stacked.
	  equalize_on_stack: false
	}
});

// Portfolio slider
document.addEventListener('DOMContentLoaded', function () {
	var portfolioSlider = document.querySelector('.splide');
	if (!portfolioSlider) return;

	//new Splide('.splide').mount();
  
    var splide = new Splide( '.splide', {
     	type    : 'loop',
      autoplay: 'play',
      perPage : 1   
    });
    splide.mount();
    
});

// Cookie terms banner
document.addEventListener('DOMContentLoaded', function () {
	var termsBanner = document.getElementById('terms-banner');
	if (!termsBanner) return;

	var cookies = new Map(document.cookie.split('; ').map(v => v.split('=').map(decodeURIComponent)))
	// Abort if already accepted cookie terms
	if (cookies.get('att_accept_cookies')) return;

	// Show cookie banner
	termsBanner.style.display = 'block';

	// Handle accepting cookie terms
	var acceptTerms = document.querySelector('#terms-banner button[data-accept]');
	acceptTerms.addEventListener('click', function (e) {
		e.preventDefault();

		// Set accepted cookie terms state for 1 year
		var expirationDate = new Date(new Date().setFullYear(new Date().getFullYear() + 1));
		document.cookie = "att_accept_cookies=yes; expires=" + expirationDate.toGMTString() + "; path=/";

		document.getElementById('terms-banner').remove();
	})
});

$(".hn-submenu-3").hover.click(function(){
    window.location.replace("/about-us/mission-statement/");
});