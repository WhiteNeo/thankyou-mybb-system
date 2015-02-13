<?php

/************************************************
 *
 *  Thank you mybb system + rep + myalerts
 *  Author: Dark Neo
 *  Copyright: © 2014 DNT
 *  Version: 2.4.2
 *  Website: http://www.mybb.com
 ************************************************/

 function task_thx($task)
{
	global $db;
	
	$db->write_query("UPDATE ".TABLE_PREFIX."users SET thx_ammount='0' WHERE thx_ammount > 0");
	
	add_task_log($task, "Thanks system task...");	
}	
?>