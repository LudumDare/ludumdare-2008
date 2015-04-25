<style type="text/css"><!--
#sabre_menu li 
{ 
display: inline; 
list-style-type: none; 
list-style-image: none; 
list-style-position: outside; 
text-align: center;
background-color:#fff;
color: #000;
margin: 0pt;
padding: 0px 5px 5px 5px;
line-height: 170%;
border: 1px solid #ccc;
}

#sabre_menu 
{ 
margin: 0px;
padding: 0px 0px 0px 10px;
}

#sabre_menu li.current 
{ 
font-weight: bold; 
border-bottom: 0px;
}

#sabre_menu a 
{
background-color:#fff;
color: #69c; 
padding: 0px 3px 0px 0px;
font-size: 12px; 
}

#sabre_menu a:hover 
{
background: #69c;
color: #fff; 
}

.sabre_notopmargin
{
margin-top: 0px;
}

.form-table
{
border: 1px solid #ccc;
}

input.unseen
{
border: none;
color: #000;
}

-->
</style>

<?php
wp_register_script('jquery', '/wp-includes/js/jquery/jquery.js');
wp_print_scripts('jquery');
?>

<script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery("#sabre_opt_accordion> table").hide();
    var $active_option = jQuery("#active_option").val();
    jQuery("#" + $active_option).slideDown('normal');
    jQuery("#sabre_opt_accordion> h3").click(function()  
		{
			jQuery("#sabre_opt_accordion> table").hide();
			var $cur_table = jQuery(this).next('table');
			$cur_table.slideDown('normal');
			jQuery("#active_option").val($cur_table.attr("id"));
		} );
  });
</script>
