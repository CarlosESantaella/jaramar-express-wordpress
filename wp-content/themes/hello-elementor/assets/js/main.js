jQuery(document).ready(function($){
  const navbar = $('#navbar').offset().top;
  var navbar_height;
  setTimeout(() => {
    navbar_height = $('#navbar').height();
  }, 500);
  $(window).on('scroll', function(){
    console.log($(window).scrollTop()+'/'+navbar );
    if($(window).scrollTop() >= navbar){
      $('#navbar').addClass('sticky-nav');
      $('.next-section-nav').css('padding-top', navbar_height+'px');
    }else{
      console.log('entro');
      $('#navbar').removeClass('sticky-nav');
      $('.next-section-nav').css('padding-top', 'initial');


    }
  })
});