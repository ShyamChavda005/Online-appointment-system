$(document).ready(function(){
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        items: 1,               // Show one image at a time
      loop: true,             // Infinite loop
      nav: true,              // Enable navigation arrows
      navText: false,    // Customize the text or icons of the nav buttons
      dots: false,             // Show dots for navigation
      autoplay: true,         // Enable autoplay (optional)
      autoplayTimeout: 2000,  // Set the autoplay timeout (optional)
      smartSpeed: 900 ,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            // 1000: {
            //     items: 5
            // }
        }
    })
});






