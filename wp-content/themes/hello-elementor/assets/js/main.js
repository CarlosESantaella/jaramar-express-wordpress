jQuery(document).ready(function($){
  const navbar = $('#navbar').offset().top;
  
  var navbar_height;

  $('a[href="#"]').on('click', function(e){
    e.preventDefault();
  })

  setTimeout(() => {
  }, 1000);
  $(window).on('scroll', function(){
    navbar_height = $('#navbar').height();
    console.log($(window).scrollTop()+'/'+navbar );
    if($(window).scrollTop() >= navbar){
      $('#navbar').addClass('sticky-nav');
      $('.next-section-nav').css('padding-bottom', navbar_height+'px');
    }else{
      console.log('entro');
      $('#navbar').removeClass('sticky-nav');
      $('.next-section-nav').css('padding-bottom', 'initial');

 
    }
  })
});