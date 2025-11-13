jQuery(document).ready(function($){
  const navbar = $('#navbar').offset().top;
  
  var navbar_height;

  $('a[href="#"]').on('click', function(e){
    e.preventDefault();
  })

  $(window).on('scroll', function(){
    navbar_height = $('#navbar').height();
    if($(window).scrollTop() >= navbar){
      $('#navbar').addClass('sticky-nav');
      $('.next-section-nav').css('padding-bottom', navbar_height+'px');
    }else{
      $('#navbar').removeClass('sticky-nav');
      $('.next-section-nav').css('padding-bottom', 'initial');

 
    }
  })
});