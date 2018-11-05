//menu
$(document).ready(function(){
  
  $('li.mainlevel').mousedown(function(){
  $(this).find('ul').slideDown("1000");//you can give it a speed
  });
  $('li.mainlevel').mouseleave(function(){
  $(this).find('ul').slideUp("slow");
  });
  
});
