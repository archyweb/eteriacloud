<?php
if(isset($_GET['id'])):
	$db=$app->get_db();
	$db->query("update ".$app->intent->table." set `read`=true where id=".$_GET['id']);
endif;
?>