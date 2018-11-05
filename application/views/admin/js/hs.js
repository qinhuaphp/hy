$(document).ready(function(){
	$("tr:gt(2)").hover(function(){$(this).css("background","blue");},
		function(){$(this).css("background","");}	
	);

});