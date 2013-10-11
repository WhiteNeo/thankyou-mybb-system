<?php
/**
  * Thank you 2.2
  * Upgrade for MyBB 1.6.x (actually 1.6.10)
  * www.soportemybb.com
  * Autor: Dark Neo
*/

if(!defined("IN_MYBB"))
{
	die("No se permite la inicialización directa de este archivo.");
}

if(isset($GLOBALS['templatelist']))
{
	$GLOBALS['templatelist'] .= ", thanks_postbit_count";
}

$plugins->add_hook("postbit", "thx");
$plugins->add_hook("postbit_announcement", "thx_code");
$plugins->add_hook("postbit_prev", "thx_code");
$plugins->add_hook("parse_message", "thx_code");
$plugins->add_hook("parse_quoted_message", "thx_quote");
$plugins->add_hook("xmlhttp", "do_action");
$plugins->add_hook("showthread_start", "direct_action");
$plugins->add_hook("class_moderation_delete_post", "deletepost_edit");
$plugins->add_hook('admin_tools_action_handler', 'thx_admin_action');
$plugins->add_hook('admin_tools_menu', 'thx_admin_menu');
$plugins->add_hook('admin_tools_permissions', 'thx_admin_permissions');
$plugins->add_hook('admin_load', 'thx_admin');

function thx_info()
{
	global $mybb, $cache, $db, $lang;

	$thx_config_link = '';
	if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}else{echo "You have to add lang files propertly";}
	
	$query = $db->simple_select('settinggroups', '*', "name='Gracias'");

	if (count($db->fetch_array($query)))
	{
		$thx_config_link = '(<a href="index.php?module=config&action=change&search=Gracias" style="color:#035488; background: url(../images/usercp/options.gif) no-repeat 0px 18px; padding: 18px; text-decoration: none;"> '. $db->escape_string($lang->thx_config) . '</a>)';
	}

	return array(
		'name'			=>	$db->escape_string($lang->thx_title),
		'description'	=>	$db->escape_string($lang->thx_desc) . $thx_config_link,
		'website'		=>	'http://www.soportemybb.com',
		'author'		=>	'Dark Neo',
		'authorsite'	=>	'http://darkneo.skn1.com',
		'version'		=>	'2.2',
		'guid'		    =>	'',
        'compatibility' =>	'16*'
	);
}


function thx_install()
{
	global $db;
	
	$db->query("CREATE TABLE IF NOT EXISTS ".TABLE_PREFIX."thx (
		txid INT UNSIGNED NOT NULL AUTO_INCREMENT, 
		uid int(10) UNSIGNED NOT NULL, 
		adduid int(10) UNSIGNED NOT NULL, 
		pid int(10) UNSIGNED NOT NULL, 
		time bigint(30) NOT NULL DEFAULT '0', 
		PRIMARY KEY (`txid`), 
		INDEX (`adduid`, `pid`, `time`) 
		);"
	);
	
	if(!$db->field_exists("thx", "users"))
	{
		$sq[] = "ALTER TABLE ".TABLE_PREFIX."users ADD `thx` INT NOT NULL, ADD `thxcount` INT NOT NULL, ADD `thxpost` INT NOT NULL";
	}
	elseif (!$db->field_exists("thxpost", "users"))		
	{
		$sq[] = "ALTER TABLE ".TABLE_PREFIX."users ADD `thxpost` INT NOT NULL";
	}
	
	if($db->field_exists("thx", "posts"))
	{
		$sq[] = "ALTER TABLE ".TABLE_PREFIX."posts DROP thx";
	}
	
	if(!$db->field_exists("pthx", "posts"))
	{
		$sq[] = "ALTER TABLE ".TABLE_PREFIX."posts ADD `pthx` INT(10) NOT NULL DEFAULT '0'";
	}
	
	if(is_array($sq))
	{
		foreach($sq as $q)
		{
			$db->query($q);
		}
	}
}


function thx_is_installed()
{
	global $db;
	if($db->field_exists('thxpost', "users"))
	{
		return true;
	}
	return false;
}


function thx_activate()
{
	global $db, $lang;
	
	$thx_tbl_keys = $db->query("SHOW KEYS FROM ".TABLE_PREFIX."thx WHERE Key_name='adduid'");
	
	if(!$db->fetch_field($thx_tbl_keys, "Key_name"))
	{
		$db->query("ALTER TABLE ".TABLE_PREFIX."thx ADD INDEX (`adduid`, `pid`, `time`)");
	}
    if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}else{echo "You have to add lang files propertly";}

	$query_tid = $db->write_query("SELECT tid FROM ".TABLE_PREFIX."themes");
	$themetid = $db->fetch_array($query_tid);
	$style = array(
			'name'         => 'thx_buttons.css',
			'tid'          => $themetid['tid'],
			'stylesheet'   => $db->escape_string('.thx_buttons{
		background: #F1F1F1;
		border: 1px solid #DCDCDC;
		padding: 10px 5px 10px 5px;
		margin: 10px;
		border-radius: 2px;
		-moz-border-radius: 2px;
		-webkit-border-radius: 2px;
}

#gracias a{
		color: #4F8A10;
		text-decoration: none;	   
}

#egracias a{
		color: #D8000C;
		text-decoration: none;
}

.bad_thx{
		color: #D8000C;
		font-family: \'Lucida Console\';
		font-size: 12px;
		font-weight: bold;
		text-decoration: none;
		background: none repeat scroll 0% 0% rgb(216, 227, 237);
		border: 2px solid rgb(189, 225, 253);
		box-shadow: 0px 0px 1em rgb(182, 182, 182);
		border-radius: 4px;
		padding: 3px 5px;
}

.neutral_thx{
		color: #424242;
		font-family: \'Lucida Console\';
		font-size: 12px;
		font-weight: bold;
		text-decoration: none;
		background: none repeat scroll 0% 0% rgb(216, 227, 237);
		border: 2px solid rgb(189, 225, 253);
		box-shadow: 0px 0px 1em rgb(182, 182, 182);
		border-radius: 4px;
		padding: 3px 5px;
}

.good_thx{
		color: #4F8A10;
		font-family: \'Lucida Console\';
		font-size: 12px;
		font-weight: bold;
		text-decoration: none;
		background: none repeat scroll 0% 0% rgb(216, 227, 237);
		border: 2px solid rgb(189, 225, 253);
		box-shadow: 0px 0px 1em rgb(182, 182, 182);
		border-radius: 4px;
		padding: 3px 5px;	
}

.info_thx, .exito_thx, .alerta_thx, .error_thx {
       font-family:Arial, Helvetica, sans-serif;
       font-size:13px;
       border: 1px solid;
       margin: 10px 0px;
       padding:10px 8px 10px 50px;
       background-repeat: no-repeat;
       background-position: 10px center;
	   text-align: center;
	   font-weight: bold;
	   border-radius: 5px;
}

.info_thx {
       color: #00529B;
       background-color: #BDE5F8;
       background-image: url(images/info.png);
}

.exito_thx {
       background-color: #DFF2BF;
       background-image:url(images/exito.png);
}

.alerta_thx {
       color: #9F6000;
       background-color: #FEEFB3;
       background-image: url(images/alerta.png);
}

.error_thx {
       color: #D8000C;
       background-color: #FFBABA;
       background-image: url(images/error.png);
}

.thx_hideshow_btn{
	   background-color:  rgb(0, 102, 140);
	   background-image:linear-gradient(top, rgb(0, 102, 140), rgb(239, 242, 250));
	   background-image:-o-linear-gradient(top, rgb(0, 102, 140), rgb(239, 242, 250));
	   color: #fff;
	   border:1px solid #dcdcdc;
	   border:1px solid rgba(0, 0, 0, 0.1);
	   border-radius:2px;
	   font-family:verdana,sans-serif,arial;
	   font-size:11px;
	   font-weight:bold;
	   height:29px;
	   line-height:27px;
	   margin:11px 6px;
	   min-width:54px;
	   padding:0 8px;
	   text-align:center;
}

.thx_hideshow_btn:hover {
	   background-color:rgb(0, 90, 134);
	   background-image:linear-gradient(top,rgb(0, 90, 134),rgb(0, 90, 114));
	   background-image:-o-linear-gradient(top,rgb(0, 90, 134),rgb(0, 100, 114));
	   color: #fff;
	   cursor: pointer;
}'),
			'lastmodified' => TIME_NOW
		);
		$sid = $db->insert_query('themestylesheets', $style);
		$db->update_query('themestylesheets', array('cachefile' => "css.php?stylesheet={$sid}"), "sid='{$sid}'", 1);
		$query = $db->simple_select('themes', 'tid');
		while($theme = $db->fetch_array($query))
		{
			require_once MYBB_ADMIN_DIR.'inc/functions_themes.php';
			update_theme_stylesheet_list($theme['tid']);
		}
	
	require MYBB_ROOT."inc/adminfunctions_templates.php";	
	if(!find_replace_templatesets("postbit", '#'.preg_quote('{$seperator}').'#', '{$post[\'thxdsp_inline\']}{$seperator}{$post[\'thxdsp_outline\']}'))
	{
		find_replace_templatesets("postbit", '#button_delete_pm(.*)<\/tr>(.*)<\/table>#is', 'button_delete_pm$1</tr>{\$post[\'thxdsp_inline\']}$2</table>{$post[\'thxdsp_outline\']}');
	}
	find_replace_templatesets("postbit", '#'.preg_quote('{$post[\'button_rep\']}').'#', '{$post[\'button_rep\']}{$post[\'thanks\']}');
	find_replace_templatesets("postbit_classic", '#button_delete_pm(.*)<\/tr>(.*)<\/table>#is', 'button_delete_pm$1</tr>{\$post[\'thxdsp_inline\']}$2</table>{$post[\'thxdsp_outline\']}');
	find_replace_templatesets("postbit_classic", '#'.preg_quote('{$post[\'button_rep\']}').'#', '{$post[\'button_rep\']}{$post[\'thanks\']}');		
	find_replace_templatesets("headerinclude", "#".preg_quote('{$newpmmsg}').'#','<script type="text/javascript" src="jscripts/thx.js"></script>
{$newpmmsg}');
	
	$templatearray = array(
		'title' => 'thanks_postbit_count',
		'template' => "<div><span class=\"smalltext\">{\$lang->thx_thank} {\$post[\'thank_count\']}<br />
	{\$post[\'thanked_count\']}<br /></span></div>",
		'sid' => '-1',
		);
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => 'thanks_postbit_inline',
		'template' => "<tr id=\"thx{\$post[\'pid\']}\" style=\"{\$display_style}\" class=\"trow2 tnx_style tnx_newstl\"><td colspan=\"2\"><span class=\"smalltext\">{\$lang->thx_givenby}</span>&nbsp;<span id=\"thx_list{\$post[\'pid\']}\">\$entries</span></td></tr>",
		'sid' => '-1',
		);	
	$db->insert_query("templates", $templatearray);
	
	$templatearray = array(
		'title' => 'thanks_postbit_inline_classic',
		'template' => "<tr id=\"thx{\$post[\'pid\']}\" style=\"{\$display_style}\" class=\"trow2 tnx_style tnx_classic\"><td><span class=\"smalltext\">{\$lang->thx_givenby}</span></td><td class=\"trow2 tnx_style\" id=\"thx_list{\$post[\'pid\']}\">\$entries</td></tr>",
		'sid' => '-1',
		);	
	$db->insert_query("templates", $templatearray);

	$templatearray = array(
		'title' => 'thanks_postbit_outline',
		'template' => "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" id=\"thx{\$post[\'pid\']}\" style=\"{\$display_style};margin-top:5px;\"><tr><td>
        <div id=\"dn_thx_list{\$post[\'pid\']}\"><div class=\"smallfont\" align=\"center\"><input type=\"button\" value=\"{\$lang->thx_show_thanks}\" class=\"thx_hideshow_btn\" onClick=\"if (this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display != \'\') { this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display = \'\'; this.innerText = \'\'; this.value = \'{\$lang->thx_hide_thanks}\'; } else { this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display = \'none\'; this.innerText = \'\'; this.value = \'{\$lang->thx_show_thanks}\'; }\"></div><div class=\"alt2\"><div style=\"display: none;\">
        <table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder thxdsp_outline\"><tr class=\"trow1 tnx_style\"><td valign=\"top\" width=\"1%\" nowrap=\"nowrap\"><img src=\"{\$mybb->settings[\'bburl\']}/images/gracias.png\" align=\"absmiddle\" /> &nbsp;<span class=\"smalltext\">{\$lang->thx_givenby}</span></td><td class=\"trow2 tnx_style\" id=\"thx_list{\$post[\'pid\']}\" align=\"left\">\$entries</td></tr></table></div></div></div>
        </td></tr></table>",
		'sid' => '-1',
		);
	$db->insert_query("templates", $templatearray);

		$templatearray = array(
		'title' => 'thanks_hide_tag',
		'template' => "<div class=\"alerta_thx message\">{\$msg}</div>",
		'sid' => '-1',
		);

	$db->insert_query("templates", $templatearray);	
	
	$templatearray = array(
		'title' => 'thanks_unhide_tag',
		'template' => "<div class=\"exito_thx message\">{\$msg}</div>",
		'sid' => '-1',
		);
				
	$db->insert_query("templates", $templatearray);	

	$templatearray = array(
		'title' => 'thanks_guests_tag',
		'template' => "<div class=\"error_thx message\">{\$msg}</div>",
		'sid' => '-1',
		);
				
	$db->insert_query("templates", $templatearray);	
	$templatearray = array(
		'title' => 'thanks_admins_tag',
		'template' => "<div class=\"info_thx message\">{\$msg}</div>",
		'sid' => '-1',
		);
				
	$db->insert_query("templates", $templatearray);	

	$thx_group = array(
		"name"			=> "Gracias",
		"title"			=> $db->escape_string($lang->thx_opt_title),
		"description"	=> $db->escape_string($lang->thx_opt_desc),
		"disporder"		=> "3",
		"isdefault"		=> "1"
	);	
	$db->insert_query("settinggroups", $thx_group);
	$gid = $db->insert_id();
	
	$thx[]= array(
		"name"			=> "thx_active",
		"title"			=> $db->escape_string($lang->thx_opt_enable),
		"description"	=> $db->escape_string($lang->thx_opt_enable_desc),
		"optionscode" 	=> "onoff",
		"value"			=> '1',
		"disporder"		=> '1',
		"gid"			=> intval($gid),
	);
	
	$thx[] = array(
		"name"			=> "thx_count",
		"title"			=> $db->escape_string($lang->thx_count_title),
		"description"	=> $db->escape_string($lang->thx_count_desc),
		"optionscode" 	=> "onoff",
		"value"			=> '1',
		"disporder"		=> '2',
		"gid"			=> intval($gid),
	);

	$thx[] = array(
		"name"			=> "thx_counter",
		"title"			=> $db->escape_string($lang->thx_counter_title),
		"description"	=> $db->escape_string($lang->thx_counter_desc),
		"optionscode" 	=> "onoff",
		"value"			=> '1',
		"disporder"		=> '3',
		"gid"			=> intval($gid),
	);	
	$thx[] = array(
		"name"			=> "thx_del",
		"title"			=> $db->escape_string($lang->thx_del_title),
		"description"	=> $db->escape_string($lang->thx_del_desc),
		"optionscode" 	=> "onoff",
		"value"			=> '1',
		"disporder"		=> '4',
		"gid"			=> intval($gid),
	);
	
	$thx[] = array(
		"name"			=> "thx_hidemode",
		"title"			=> $db->escape_string($lang->thx_date_title),
		"description"	=> $db->escape_string($lang->thx_date_desc),
		"optionscode" 	=> "onoff",
		"value"			=> '1',
		"disporder"		=> '5',
		"gid"			=> intval($gid),
	);
	
	$thx[] = array(
		"name"			=> "thx_autolayout",
		"title"			=> $db->escape_string($lang->thx_temp_title),
		"description"	=> $db->escape_string($lang->thx_temp_desc),
		"optionscode" 	=> "onoff",
		"value"			=> '1',
		"disporder"		=> '6',
		"gid"			=> intval($gid),
	);

	$thx[] = array(
		"name"			=> "thx_outline",
		"title"			=> $db->escape_string($lang->thx_table_title),
		"description"	=> $db->escape_string($lang->thx_table_desc),
		"optionscode"	=> "onoff",
		"value"			=> '1',
		"disporder"		=> '7',
		"gid"			=> intval($gid),
	);

	$thx[] = array(
		"name"			=> "thx_hidesystem",
		"title"			=> $db->escape_string($lang->thx_hide_title),
		'description'   => $db->escape_string($lang->thx_hide_desc),
		"optionscode" 	=> "yesno",
		"value"			=> '1',
		"disporder"		=> '8',
		"gid"			=> intval($gid),
	);

	$thx[] = array(
		"name"			=> "thx_hidesystem_tag",
		"title"			=> $db->escape_string($lang->thx_hidetag_title),
		'description'   => $db->escape_string($lang->thx_hidetag_desc),
		"optionscode" 	=> "text",
		"value"			=> $db->escape_string($lang->thx_hidetag_value),
		"disporder"		=> '9',
		"gid"			=> intval($gid),
	);	
	
	$thx[] = array(
		"name"			=> "thx_hidesystem_fid",
		"title"			=> $db->escape_string($lang->thx_fig_title),
		"description"   => $db->escape_string($lang->thx_fid_desc),
		"optionscode" 	=> "text",
		"value"			=> '2',
		"disporder"		=> '10',
		"gid"			=> intval($gid),
	);
	
	$thx[] = array(
		"name"			=> "thx_hidesystem_gid",
		"title"			=> $db->escape_string($lang->thx_gid_title),
		"description"   => $db->escape_string($lang->thx_gid_desc),
		"optionscode" 	=> "text",
		"value"			=> '4',
		"disporder"		=> '11',
		"gid"			=> intval($gid),
	);	

	$thx[] = array(
		"name"			=> "thx_hidesystem_notgid",
		"title"			=> $db->escape_string($lang->thx_ngid_title),
		"description"   => $db->escape_string($lang->thx_ngid_desc),
		"optionscode" 	=> "text",
		"value"			=> '1,5,7',
		"disporder"		=> '12',
		"gid"			=> intval($gid),
	);	
		
    $thx[] = array(
        'name' 			=> "thx_reputation",
        'title' 		=> $db->escape_string($lang->thx_rep_title),
        'description' 	=> $db->escape_string($lang->thx_rep_desc),
        'optionscode' 	=> 'select \n 1='.$db->escape_string($lang->thx_rep_op1).' \n 2='.$db->escape_string($lang->thx_rep_op2).' \n 3='.$db->escape_string($lang->thx_rep_op3),
        'value' 		=> '2',
        'disporder' 	=> '13',
        'gid' 			=> intval($gid)
    );  	
	
	foreach($thx as $t)
	{
		$db->insert_query("settings", $t);
	}
	
	rebuild_settings();
}


function thx_deactivate()
{
	global $db;
    	$db->delete_query('themestylesheets', "name='thx_buttons.css'");
		$query = $db->simple_select('themes', 'tid');
		while($theme = $db->fetch_array($query))
		{
			require_once MYBB_ADMIN_DIR.'inc/functions_themes.php';
			update_theme_stylesheet_list($theme['tid']);
		}

	require '../inc/adminfunctions_templates.php';
	find_replace_templatesets("postbit", '#'.preg_quote('{$post[\'thxdsp_inline\']}').'#', '', 0);
	find_replace_templatesets("postbit", '#'.preg_quote('{$post[\'thxdsp_outline\']}').'#', '', 0);
	find_replace_templatesets("postbit", '#'.preg_quote('{$post[\'thanks\']}').'#', '', 0);
	find_replace_templatesets("postbit_classic", '#'.preg_quote('{$post[\'thxdsp_inline\']}').'#', '', 0);
	find_replace_templatesets("postbit_classic", '#'.preg_quote('{$post[\'thxdsp_outline\']}').'#', '', 0);
	find_replace_templatesets("postbit_classic", '#'.preg_quote('{$post[\'thanks\']}').'#', '', 0);
	find_replace_templatesets("headerinclude", "#".preg_quote('<script type="text/javascript" src="jscripts/thx.js"></script>').'#', '', 0);

	$db->delete_query("settings", "name IN ('thx_active', 'thx_count', 'thx_counter', 'thx_del', 'thx_hidemode', 'thx_autolayout', 'thx_outline', 'thx_hidesystem', 'thx_hidesystem_tag', 'thx_hidesystem_fid', 'thx_hidesystem_gid', 'thx_hidesystem_notgid', 'thx_reputation')");
	$db->delete_query("settinggroups", "name='Gracias'");
	$db->delete_query("templates", "title='thanks_postbit_count'");
	$db->delete_query("templates", "title='thanks_postbit_inline'");
	$db->delete_query("templates", "title='thanks_postbit_inline_classic'");
	$db->delete_query("templates", "title='thanks_postbit_outline'");
	$db->delete_query("templates", "title='thanks_hide_tag'");	
	$db->delete_query("templates", "title='thanks_unhide_tag'");	
	$db->delete_query("templates", "title='thanks_guests_tag'");
	$db->delete_query("templates", "title='thanks_admins_tag'");
	
	rebuild_settings();
}


function thx_uninstall()
{
	global $db;

	if($db->field_exists("thx", "users"))
	{
		$db->query("ALTER TABLE ".TABLE_PREFIX."users DROP thx, DROP thxcount, DROP thxpost");
	}
	
	if($db->field_exists("pthx", "posts"))
	{
		$db->query("ALTER TABLE ".TABLE_PREFIX."posts DROP pthx");
	}
}

function thx_code(&$message)
{
    global $db, $post, $mybb, $lang, $session, $theme, $altbg, $templates, $thx_cache, $forum, $fid, $pid, $announcement, $postrow, $hide_tag;

    if (!$mybb->settings['thx_hidesystem']  || !empty($session->is_spider))
        {
          return false;
        }

		$forum_fid = explode(',', $mybb->settings['thx_hidesystem_fid']);
        $hide_tag = $mybb->settings['thx_hidesystem_tag'];

		if(THIS_SCRIPT == "syndication.php"){
		   $msg = $lang->thx_hide_sindycation; 
		   eval("\$caja = \"".$templates->get("thanks_guests_tag",1,0)."\";");		  
		   $message = preg_replace("#\[$hide_tag\](.*?)\[/$hide_tag\]#is",$caja,$message);	
		}
		
		if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
		{
			$lang->load("thx");
		}
		else{
		echo 'You have to add lang files propertly';}

        if($forum['fid'] == 0 || $forum['fid'] == ''){$forum['fid'] = $fid;}
		if($post['pid'] == 0 || $post['pid'] == ''){
		switch(THIS_SCRIPT)
		{
		case "printthread.php" : $post['pid'] = $postrow['pid'];break;
		case "portal.php" : $post['pid'] = $announcement['pid'];break;
		default: $post['pid'] = $pid;
		}
		}
        if(!in_array($forum['fid'],$forum_fid)){return false;}
		
        $must_thanks = $mybb->settings['thx_hidesystem_code'];
        $forum_gid = explode(',', $mybb->settings['thx_hidesystem_gid']);
		$forum_notgid = explode(',', $mybb->settings['thx_hidesystem_notgid']);
		$url = $mybb->settings['bburl'];
			
      if(in_array($mybb->user['usergroup'], $forum_gid))
      {
	   $msg = "$1";
	   eval("\$caja = \"".$templates->get("thanks_admins_tag",1,0)."\";");		  
       $message = preg_replace("#\[$hide_tag\](.*?)\[/$hide_tag\]#is",$caja,$message);      
	  }
      
     else if(in_array($mybb->user['usergroup'], $forum_notgid) || $mybb->user['uid'] == 0)
      {	 
	   $msg = $lang->thx_hide_register; 
	   eval("\$caja = \"".$templates->get("thanks_guests_tag",1,0)."\";");		  
	   $message = preg_replace("#\[$hide_tag\](.*?)\[/$hide_tag\]#is",$caja,$message);
      }
      else{

	  if ($mybb->user['uid'] == $post['uid'])
       {
	   $msg = "$1";
	   eval("\$caja = \"".$templates->get("thanks_unhide_tag",1,0)."\";");		  
       $message = preg_replace("#\[$hide_tag\](.*?)\[/$hide_tag\]#is",$caja,$message);
       }

     if($mybb->user['uid'] != $post['uid'])
     {
     $thx_user = $mybb->user['uid'];
	 $query=$db->query("SELECT th.txid, th.uid, th.adduid, th.pid, th.time, u.username, u.usergroup, u.displaygroup, u.avatar
		FROM ".TABLE_PREFIX."thx th
		JOIN ".TABLE_PREFIX."users u
		ON th.adduid=u.uid
		WHERE th.pid='$post[pid]' AND th.adduid ='$thx_user'
		ORDER BY th.time ASC"
	);

	while($record = $db->fetch_array($query))
	{
	if($record['adduid'] == $mybb->user['uid'])
	{
	   $msg = "$1";
	   eval("\$caja = \"".$templates->get("thanks_unhide_tag",1,0)."\";");		  
       $message = preg_replace("#\[$hide_tag\](.*?)\[/$hide_tag\]#is",$caja,$message);
	}
	else
	{
		$msg = $lang->thx_hide_text;  
	    eval("\$caja = \"".$templates->get("thanks_hide_tag",1,0)."\";");		 
        $message = preg_replace("#\[$hide_tag\](.*?)\[/$hide_tag\]#is",$caja,$message);
	}
    $done = true;
    }
		$msg = $lang->thx_hide_text;  
	    eval("\$caja = \"".$templates->get("thanks_hide_tag",1,0)."\";");		 
        $message = preg_replace("#\[$hide_tag\](.*?)\[/$hide_tag\]#is",$caja,$message);
		}
	}
}

function thx_quote(&$quoted_post)
{
    global $mybb, $session, $templates, $lang, $hide_tag;

    if (!$mybb->settings['thx_hidesystem']  || !empty($session->is_spider))
        {
          return false;
        }

        if ($mybb->settings['thx_hidesystem'] == '1'){
		  $hide_tag = $mybb->settings['thx_hidesystem_tag'];	
          $quoted_post['message'] = preg_replace("#\[$hide_tag\](.*?)\[/$hide_tag\]#is","", $quoted_post['message']);
        }
}

function thx(&$post)
{
	global $db, $mybb, $lang ,$session, $theme, $altbg, $templates, $thx_cache, $forum, $message;
	
	if(!$mybb->settings['thx_active'] || !empty($session->is_spider))
	{
		return false;
	}
	
        $forum_fid = explode(',', $mybb->settings['thx_hidesystem_fid']);
        
        if(!in_array($forum['fid'],$forum_fid))
        {
			return false;
		}    		

	if ($mybb->settings['thx_hidesystem'] != 0)

	{

	if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}
	else{
	echo 'You have to add lang files propertly';}

	if($b = $post['pthx'])
	{
		$entries = build_thank($post['pid'], $b);
	}
	else
	{
		$entries = "";
	}
	
	if($mybb->settings['thx_counter'] == 1){
	$count = 0;
	$total = explode(',',$entries);
	$count = count($total) - 1;
	if ($count == 0){$count="<span class=\"neutral_thx\">".$count."</span>";}
	else if ($count >= 1){$count="<span class=\"good_thx\">".$count."</span>";}
	else {$count="<span class=\"bad_thx\"".$count."</span>";}
	}
	else{$count="";}
	if($mybb->user['uid'] == $post['uid']){
	$post['thanks'] = $count;
	$display_style = $entries ?  "" : "display:none; border:0;";
	$playout = $mybb->settings['postlayout'];	
	if(!$mybb->settings['thx_outline'])
	{
		eval("\$post['thxdsp_inline'] .= \"".$templates->get("thanks_postbit_inline")."\";");							
		if($mybb->settings['thx_autolayout'] && $playout == "classic")
		{
			eval("\$post['thxdsp_inline'] .= \"".$templates->get("thanks_postbit_inline_classic")."\";");
		}
	}
	else
		{	
			eval("\$post['thxdsp_outline'] .= \"".$templates->get("thanks_postbit_outline")."\";");
		}

	}
 	if($mybb->user['uid'] != 0 && $mybb->user['uid'] != $post['uid'])
	{
	    $post['button_rep'] = "";
		if(!$b)
		{
			$post['thanks'] = "<span id=\"gracias\" class=\"buttons\"><a id=\"a{$post['pid']}\" onclick=\"javascript: ThankYou.thx({$post['pid']}); \" href=\"showthread.php?action=thank&tid={$post['tid']}&pid={$post['pid']}\" class=\"positive\">
			 $count {$lang->thx_button_add}</a></span>";
		}
		else if($mybb->settings['thx_del'] == "1")
		{
			$post['thanks'] = "<span id=\"egracias\" class=\"buttons\"><a id=\"a{$post['pid']}\" onclick=\"javascript: ThankYou.rthx({$post['pid']}); \" href=\"showthread.php?action=remove_thank&tid={$post['tid']}&pid={$post['pid']}\" class=\"negative\">
			$count {$lang->thx_button_del}</a></span>";
			$display_style = $entries ?  "" : "display:none; border:0;";
			$playout = $mybb->settings['postlayout'];
		
			if(!$mybb->settings['thx_outline'])
			{
				eval("\$post['thxdsp_inline'] .= \"".$templates->get("thanks_postbit_inline")."\";");
											
				if($mybb->settings['thx_autolayout'] && $playout == "classic")
				{
					eval("\$post['thxdsp_inline'] .= \"".$templates->get("thanks_postbit_inline_classic")."\";");
				}
			}
			else
			{	
				eval("\$post['thxdsp_outline'] .= \"".$templates->get("thanks_postbit_outline")."\";");
			}
			 
	    }	
		else
		{
			$post['thanks'] = $count;
			$display_style = $entries ?  "" : "display:none; border:0;";
			$playout = $mybb->settings['postlayout'];
		
			if(!$mybb->settings['thx_outline'])
			{
				eval("\$post['thxdsp_inline'] .= \"".$templates->get("thanks_postbit_inline")."\";");
											
				if($mybb->settings['thx_autolayout'] && $playout == "classic")
				{
					eval("\$post['thxdsp_inline'] .= \"".$templates->get("thanks_postbit_inline_classic")."\";");
				}
			}
			else
			{	
				eval("\$post['thxdsp_outline'] .= \"".$templates->get("thanks_postbit_outline")."\";");
			}
		}
	}
    }
		
	if($mybb->settings['thx_count'] == "1")
	{
		if(!isset($thx_cache['postbit'][$post['uid']]))
		{
			$post['thank_count'] = $post['thx'];
			$post['thanked_count'] = $lang->sprintf($lang->thx_thanked_count, $post['thxcount'], $post['thxpost']);
			eval("\$x = \"".$templates->get("thanks_postbit_count")."\";");
			$thx_cache['postbit'][$post['uid']] = $x;
		}
		
		$post['user_details'] .= $thx_cache['postbit'][$post['uid']];
	}
}

function do_action()
{
	global $mybb, $lang, $theme, $templates, $thread, $post, $attachcache, $pid,$tid;
	
	if(($mybb->input['action'] != "thankyou"  &&  $mybb->input['action'] != "remove_thankyou") || $mybb->request_method != "post")
	{
		return false;
	}
		
	if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}
	else{
	echo 'You have to add lang files propertly';}
	
	$pid = intval($mybb->input['pid']);
	$tid = intval($mybb->input['tid']);
	
	if ($mybb->input['action'] == "thankyou" )
	{
		do_thank($pid);
	}
	else if($mybb->settings['thx_del'] == "1")
	{
		del_thank($pid);
	}
	
	$nonead = 0;
	$list = build_thank($pid, $nonead);
	header('Content-Type: text/xml');
	$output = "<thankyou>
				<list><![CDATA[$list]]></list>
				<display>".($list ? "1" : "0")."</display>
			  <del>{$mybb->settings['thx_del']}</del>	
			 </thankyou>";
	echo $output;
}

function direct_action()
{
	global $mybb, $lang, $tid, $pid;
	
	if($mybb->input['action'] != "thank"  &&  $mybb->input['action'] != "remove_thank")
	{
		return false;
	}
		
	if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}
	else{
	echo 'You have to add lang files propertly';}
	
	$pid=intval($mybb->input['pid']);
	
	if($mybb->input['action'] == "thank" )
	{
		do_thank($pid);
	}
	else if($mybb->settings['thx_del'] == "1")
	{
		del_thank($pid);
	}
	 redirect(get_post_link($pid, $tid)."#pid{$pid}");
}

function build_thank(&$pid, &$is_thx)
{
	global $db, $mybb, $lang, $thx_cache, $message;
	$is_thx = 0;
	
	$pid = intval($pid);
	
	if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}
	else{
	echo 'You have to add lang files propertly';}

	$dir = $lang->thx_dir;
	
	$query=$db->query("SELECT th.txid, th.uid, th.adduid, th.pid, th.time, u.username, u.usergroup, u.displaygroup, u.avatar
		FROM ".TABLE_PREFIX."thx th
		JOIN ".TABLE_PREFIX."users u
		ON th.adduid=u.uid
		WHERE th.pid='$pid'
		ORDER BY th.time ASC"
	);

	while($record = $db->fetch_array($query))
	{
		if($record['adduid'] == $mybb->user['uid'])
		{
			$is_thx++;
		}
		$date = my_date($mybb->settings['dateformat'].' '.$mybb->settings['timeformat'], $record['time']);
		if(!isset($thx_cache['showname'][$record['username']]))
		{
			$url = get_profile_link($record['adduid']);
			$name = format_name($record['username'], $record['usergroup'], $record['displaygroup']) . ", ";
            $avatar = $record['avatar'];
            if($avatar != '')
            {
			$thx_cache['showname'][$record['username']] = "<a href=\"$url\" dir=\"$dir\"><img src=\"$avatar\" style=\"width: 19px; height: 19px; border-style: double; color: #D8DFEA; padding: 2px; background-color: #FCFDFD; border-radius: 4px; -ms-border-radius: 4px; -moz-border-radius: 4px; webkit-border-radius: 4px;\"> $name</a>";
            }
            else
            {
            $thx_cache['showname'][$record['username']] = "<a href=\"$url\" dir=\"$dir\"><img src=\"images/default_avatar.gif\" style=\"width: 19px; height: 19px; border-style: double; color: #D8DFEA; padding: 2px; background-color: #FCFDFD; border-radius: 4px; -ms-border-radius: 4px; -moz-border-radius: 4px; webkit-border-radius: 4px;\">$name</a>";
            }
		}

		if($mybb->settings['thx_hidemode'])
		{
			$entries .= "<span title=\"".$date."\">".$thx_cache['showname'][$record['username']]."</span>";
		}
		else
		{
			$entries .= $thx_cache['showname'][$record['username']]." <span class=\"smalltext\">(".$date.")</span>";
		}
	}
	
	return $entries;
}

function do_thank(&$pid)
{
	global $db, $mybb, $lang;
	
	$pid = intval($pid);
	if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}
	else{
	echo 'You have to add lang files propertly';}

	$check_query = $db->simple_select("thx", "count(*) as c" ,"adduid='{$mybb->user['uid']}' AND pid='$pid'", array("limit"=>"1"));
			
	$tmp=$db->fetch_array($check_query);
	if($tmp['c'] != 0)
	{
		return false;
	}
		
	$check_query = $db->simple_select("posts", "uid", "pid='$pid'", array("limit"=>1));
	if($db->num_rows($check_query) == 1)
	{
		
		$tmp=$db->fetch_array($check_query);
		
		if($tmp['uid'] == $mybb->user['uid'])
		{
			return false;
		}		
			
		$database = array (
			"uid" =>$tmp['uid'],
			"adduid" => $mybb->user['uid'],
			"pid" => $pid,
			"time" => time()
		);
		
		$time = time();
		if($mybb->settings['thx_reputation'] == 2){
		$sq = array (
			"UPDATE ".TABLE_PREFIX."users SET thx=thx+1 WHERE uid='{$mybb->user['uid']}' LIMIT 1",
			"UPDATE ".TABLE_PREFIX."users SET thxcount=thxcount+1, reputation = reputation+1,thxpost=CASE( SELECT COUNT(*) FROM ".TABLE_PREFIX."thx WHERE pid='{$pid}' LIMIT 1) WHEN 0 THEN thxpost+1 ELSE thxpost END WHERE uid='{$database['uid']}' LIMIT 1",					
	        "UPDATE ".TABLE_PREFIX."posts SET pthx=pthx+1 WHERE pid='{$pid}' LIMIT 1",
            "INSERT INTO ".TABLE_PREFIX."reputation (uid, adduid, pid, reputation, dateline, comments) VALUES ('{$tmp['uid']}', '{$mybb->user['uid']}', '{$pid}', 1, '{$time}', '{$lang->thx_thankyou}')"			
			);
		}else if($mybb->settings['thx_reputation'] == 3){
		$sq = array (
			"UPDATE ".TABLE_PREFIX."users SET thx=thx+1 WHERE uid='{$mybb->user['uid']}' LIMIT 1",
			"UPDATE ".TABLE_PREFIX."users SET thxcount=thxcount+1, reputation = reputation+1,thxpost=CASE( SELECT COUNT(*) FROM ".TABLE_PREFIX."thx WHERE pid='{$pid}' LIMIT 1) WHEN 0 THEN thxpost+1 ELSE thxpost END WHERE uid='{$database['uid']}' LIMIT 1",					
	        "UPDATE ".TABLE_PREFIX."posts SET pthx=pthx+1 WHERE pid='{$pid}' LIMIT 1",
            "INSERT INTO ".TABLE_PREFIX."reputation (uid, adduid, pid, reputation, dateline, comments) VALUES ('{$tmp['uid']}', '{$mybb->user['uid']}', '{$pid}', 1, '{$time}', '{$lang->thx_thankyou}')",			
			"INSERT INTO ".TABLE_PREFIX."alerts (uid, from_id, unread, alert_type, dateline) VALUES ('{$tmp['uid']}', '{$mybb->user['uid']}', 1, 'rep', '{$time}')"			
			);
		}else{
		$sq = array (
			"UPDATE ".TABLE_PREFIX."users SET thx=thx+1 WHERE uid='{$mybb->user['uid']}' LIMIT 1",
			"UPDATE ".TABLE_PREFIX."users SET thxcount=thxcount+1, thxpost=CASE( SELECT COUNT(*) FROM ".TABLE_PREFIX."thx WHERE pid='{$pid}' LIMIT 1) WHEN 0 THEN thxpost+1 ELSE thxpost END WHERE uid='{$database['uid']}' LIMIT 1",					
	        "UPDATE ".TABLE_PREFIX."posts SET pthx=pthx+1 WHERE pid='{$pid}' LIMIT 1"
			);		
		}				
		
	    unset($tmp);
				  
		foreach($sq as $q)
		{
			$db->query($q);
		}
		$db->insert_query("thx", $database);
	}	
}

function del_thank(&$pid)
{
	global $mybb, $db;
	
	$pid = intval($pid);
	if($mybb->settings['thx_del'] != "1")
	{
		return false;
	}

	$check_query = $db->simple_select("thx", "`uid`, `txid`" ,"adduid='{$mybb->user['uid']}' AND pid='$pid'", array("limit"=>"1"));		
	
	if($db->num_rows($check_query))
	{
		$data = $db->fetch_array($check_query);
		$uid = intval($data['uid']);
		$thxid = intval($data['txid']);
		unset($data);
		
		$time = time();

		if($mybb->settings['thx_reputation'] == 2){
		$sq = array (
			"UPDATE ".TABLE_PREFIX."users SET thx=thx-1 WHERE uid='{$mybb->user['uid']}' LIMIT 1",
			"UPDATE ".TABLE_PREFIX."users SET thxcount=thxcount-1, reputation=reputation-1, thxpost=CASE(SELECT COUNT(*) FROM ".TABLE_PREFIX."thx WHERE pid='{$pid}' LIMIT 1) WHEN 0 THEN thxpost-1 ELSE thxpost END WHERE uid='{$uid}' LIMIT 1",
			"UPDATE ".TABLE_PREFIX."posts SET pthx=pthx-1 WHERE pid='{$pid}' LIMIT 1"
		);
		$db->delete_query("reputation", "adduid='{$mybb->user['uid']}' && pid='{$pid}'");
		$db->delete_query("thx", "txid='{$thxid}'", "1");
	    }else if($mybb->settings['thx_reputation'] == 3){
		$sq = array (
			"UPDATE ".TABLE_PREFIX."users SET thx=thx-1 WHERE uid='{$mybb->user['uid']}' LIMIT 1",
			"UPDATE ".TABLE_PREFIX."users SET thxcount=thxcount-1, reputation=reputation-1, thxpost=CASE(SELECT COUNT(*) FROM ".TABLE_PREFIX."thx WHERE pid='{$pid}' LIMIT 1) WHEN 0 THEN thxpost-1 ELSE thxpost END WHERE uid='{$uid}' LIMIT 1",
			"UPDATE ".TABLE_PREFIX."posts SET pthx=pthx-1 WHERE pid='{$pid}' LIMIT 1"
		);
		$db->delete_query("reputation", "adduid='{$mybb->user['uid']}' && pid='{$pid}'");
		$db->delete_query("alerts", "from_id='{$mybb->user['uid']}' && unread='1' && alert_type='rep'");		
		$db->delete_query("thx", "txid='{$thxid}'", "1");		
	    }else{
		$sq = array (
			"UPDATE ".TABLE_PREFIX."users SET thx=thx-1 WHERE uid='{$mybb->user['uid']}' LIMIT 1",
			"UPDATE ".TABLE_PREFIX."users SET thxcount=thxcount-1, thxpost=CASE(SELECT COUNT(*) FROM ".TABLE_PREFIX."thx WHERE pid='{$pid}' LIMIT 1) WHEN 0 THEN thxpost-1 ELSE thxpost END WHERE uid='{$uid}' LIMIT 1",
			"UPDATE ".TABLE_PREFIX."posts SET pthx=pthx-1 WHERE pid='{$pid}' LIMIT 1"
		);
		$db->delete_query("thx", "txid='{$thxid}'", "1");
		}
		
		foreach($sq as $q)
		{
			$db->query($q);
		}
	}
}

function deletepost_edit(&$pid)
{
	global $db;
	
	$pid = intval($pid);
	$q = $db->simple_select("thx", "uid, adduid", "pid='{$pid}'");
	
	$postnum = $db->num_rows($q);
	if($postnum <= 0)
	{
		return false;
	}
	
	$adduids = array();
	
	while($r = $db->fetch_array($q))
	{
		$uid = intval($r['uid']);
		$adduids[] = $r['adduid'];
	}
	
	$adduids = implode(", ", $adduids);
	
	$sq = array();
	$sq[] = "UPDATE ".TABLE_PREFIX."users SET thxcount=thxcount-1, thxpost=thxpost-1 WHERE uid='{$uid}'";
	$sq[] = "UPDATE ".TABLE_PREFIX."users SET thx=thx-1 WHERE uid IN ({$adduids})";
	
	foreach($sq as $q)
	{
		$db->query($q);
	}
	
	$db->delete_query("thx", "pid={$pid}", $postnum);	
}

function thx_admin_action(&$action)
{
	$action['recount_thanks'] = array ('active'=>'recount_thanks');
}

function thx_admin_menu(&$sub_menu)
{
    global $db, $lang;
	if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
	$lang->load("thx");
	}else{echo "You have to add lang files propertly";}
	$sub_menu['45'] = array	(
		'id'	=> 'recount_thanks',
		'title'	=> $db->escape_string($lang->thx_recount),
		'link'	=> 'index.php?module=tools/recount_thanks'
	);
}

function thx_admin_permissions(&$admin_permissions)
{
    global $db,$lang;
	if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}else{echo "You have to add lang files propertly";}
	$admin_permissions['recount_thanks'] = $db->escape_string($lang->thx_can_recount);
}

function thx_admin()
{
	global $mybb, $page, $db, $lang;
	require_once MYBB_ROOT.'inc/functions_rebuild.php';
	if($page->active_action != 'recount_thanks')
	{
		return false;
	}

	if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}else{echo "You have to add lang files propertly";}
	
	if($mybb->request_method == "post")
	{
		if(!isset($mybb->input['page']) || intval($mybb->input['page']) < 1)
		{
			$mybb->input['page'] = 1;
		}
		if(isset($mybb->input['do_recountthanks']))
		{
			if(!intval($mybb->input['thx_chunk_size']))
			{
				$mybb->input['thx_chunk_size'] = 500;
			}

			do_recount();
		}
		else if(isset($mybb->input['do_recountposts']))
		{
			if(!intval($mybb->input['post_chunk_size']))
			{
				$mybb->input['post_chunk_size'] = 500;
			}

			do_recount_post();
		}
	}

	$page->add_breadcrumb_item($db->escape_string($lang->thx_recount), "index.php?module=tools/recount_thanks");
	$page->output_header($db->escape_string($lang->thx_recount));

	$sub_tabs['thankyoulike_recount'] = array(
		'title'			=> $db->escape_string($lang->thx_recount_do),
		'link'			=> "index.php?module=tools/recount_thanks",
		'description'	=> $db->escape_string($lang->thx_upgrade_do)
	);

	$page->output_nav_tabs($sub_tabs, 'thankyoulike_recount');

	$form = new Form("index.php?module=tools/recount_thanks", "post");

	$form_container = new FormContainer($db->escape_string($lang->thx_recount));
	$form_container->output_row_header($db->escape_string($lang->thx_recount_task));
	$form_container->output_row_header($db->escape_string($lang->thx_recount_send), array('width' => 50));
	$form_container->output_row_header("&nbsp;");

	$form_container->output_cell("<label>".$db->escape_string($lang->thx_recount_update)."</label>
	<div class=\"description\">".$db->escape_string($lang->thx_recount_update_desc)."</div>");
	$form_container->output_cell($form->generate_text_box("thx_chunk_size", 100, array('style' => 'width: 150px;')));
	$form_container->output_cell($form->generate_submit_button($db->escape_string($lang->thx_recount_update_button), array("name" => "do_recountthanks")));
	$form_container->construct_row();

	$form_container->output_cell("<label>".$db->escape_string($lang->thx_counter_update)."</label>
	<div class=\"description\">".$db->escape_string($lang->thx_counter_update_desc).".</div>");
	$form_container->output_cell($form->generate_text_box("post_chunk_size", 500, array('style' => 'width: 150px;')));
	$form_container->output_cell($form->generate_submit_button($db->escape_string($lang->thx_recount_update_button), array("name" => "do_recountposts")));
	$form_container->construct_row();

	$form_container->end();

	$form->end();

	$page->output_footer();

	exit;
}

function do_recount()
{
	global $db, $mybb, $lang;

		if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}else{echo "You have to add lang files propertly";}
	
	$cur_page = intval($mybb->input['page']);
	$per_page = intval($mybb->input['thx_chunk_size']);
	$start = ($cur_page-1) * $per_page;
	$end = $start + $per_page;

	if ($cur_page == 1)
	{
		$db->write_query("UPDATE ".TABLE_PREFIX."users SET thx='0', thxcount='0'");
		$db->write_query("UPDATE ".TABLE_PREFIX."posts SET pthx='0'");
	}

	$query = $db->simple_select("thx", "COUNT(txid) AS thx_count");
	$thx_count = $db->fetch_field($query, 'thx_count');

	$query = $db->query("
		SELECT uid, adduid, pid
		FROM ".TABLE_PREFIX."thx
		ORDER BY time ASC
		LIMIT $start, $per_page
	");

	$post_thx = array();
	$user_thx = array();
	$user_thx_to = array();

	while($thx = $db->fetch_array($query))
	{
		if($post_thx[$thx['pid']])
		{
			$post_thx[$thx['pid']]++;
		}
		else
		{
			$post_thx[$thx['pid']] = 1;
		}
		if($user_thx[$thx['adduid']])
		{
			$user_thx[$thx['adduid']]++;
		}
		else
		{
			$user_thx[$thx['adduid']] = 1;
		}
		if($user_thx_to[$thx['uid']])
		{
			$user_thx_to[$thx['uid']]++;
		}
		else
		{
			$user_thx_to[$thx['uid']] = 1;
		}
	}

	if(is_array($post_thx))
	{
		foreach($post_thx as $pid => $change)
		{
			$db->write_query("UPDATE ".TABLE_PREFIX."posts SET pthx=pthx+$change WHERE pid='$pid'");
		}
	}
	if(is_array($user_thx))
	{
		foreach($user_thx as $adduid => $change)
		{
			$db->write_query("UPDATE ".TABLE_PREFIX."users SET thx=thx+$change WHERE uid='$adduid'");
		}
	}
	if(is_array($user_thx_to))
	{
		foreach($user_thx_to as $uid => $change)
		{
			$db->write_query("UPDATE ".TABLE_PREFIX."users SET thxcount=thxcount+$change WHERE uid='$uid'");
		}
	}
	my_check_proceed($thx_count, $end, $cur_page+1, $per_page, "thx_chunk_size", "do_recountthanks", $db->escape_string($lang->thx_update_psuccess));
}

function do_recount_post()
{
	global $db, $mybb, $lang;

	$cur_page = intval($mybb->input['page']);
	$per_page = intval($mybb->input['post_chunk_size']);
	$start = ($cur_page-1) * $per_page;
	$end = $start + $per_page;
	if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}else{echo "You have to add lang files propertly";}
	
	if ($cur_page == 1)
	{
		$db->write_query("UPDATE ".TABLE_PREFIX."users SET thxpost='0'");
	}

	$query = $db->simple_select("thx", "COUNT(distinct pid) AS post_count");
	$post_count = $db->fetch_field($query, 'post_count');

	$query = $db->query("
		SELECT uid, pid
		FROM ".TABLE_PREFIX."thx
		GROUP BY pid
		ORDER BY pid ASC
		LIMIT $start, $per_page
	");

	while($thx = $db->fetch_array($query))
	{
		$db->write_query("UPDATE ".TABLE_PREFIX."users SET thxpost=thxpost+1 WHERE uid='{$thx['uid']}'");
	}

	my_check_proceed($post_count, $end, $cur_page+1, $per_page, "post_chunk_size", "do_recountposts", $db->escape_string($lang->thx_update_tsuccess));
}

function my_check_proceed($current, $finish, $next_page, $per_page, $name_chunk, $name_submit, $message)
{
	global $db, $page, $lang;
	
    if(file_exists($lang->path."/".$lang->language."/thx.lang.php"))
	{
		$lang->load("thx");
	}else{echo "You have to add lang files propertly";}
	

	if($finish >= $current)
	{
		flash_message($message, 'success');
		admin_redirect("index.php?module=tools/recount_thanks");
	}
	else
	{
		$page->output_header();

		$form = new Form("index.php?module=tools/recount_thanks", 'post');
        $total = $current - $finish;
		echo $form->generate_hidden_field("page", $next_page);
		echo $form->generate_hidden_field($name_chunk, $per_page);
		echo $form->generate_hidden_field($name_submit, "Actualizar");
		echo "<div class=\"confirm_action\">\n";
		echo $db->escape_string($lang->thx_confirm_next);
		echo "<br />\n";
		echo "<br />\n";
		echo "<p class=\"buttons\">\n";
		echo $form->generate_submit_button($db->escape_string($lang->thx_confirm_button), array('class' => 'button_yes'));
		echo "</p>\n";
		echo "<div style=\"float: right; color: #424242;\">".$db->escape_string($lang->thx_confirm_page)." $next_page\n";
		echo "<br />\n";
		echo $db->escape_string($lang->thx_confirm_elements)." $total</div>";
		echo "<br />\n";
	    echo "<br />\n";
		echo "</div>\n";		
		$form->end();
		$page->output_footer();
		exit;
	}
}

?>
