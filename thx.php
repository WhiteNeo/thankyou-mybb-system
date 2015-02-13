<?php

/**
 * Thank You MyBB System + MyAlerts + rep xD v 2.4.1
 * Upgrade for MyBB 1.6.x Testes since 1.6.3 - (actually 1.6.13)
 * contact: neogeoman@gmail.com
 * Website: http://www.mybb.com
 * Author:  Dark Neo
 */
 
define("IN_MYBB", 1);
$filename = substr($_SERVER['SCRIPT_NAME'], -strpos(strrev($_SERVER['SCRIPT_NAME']), "/"));
define('THIS_SCRIPT', $filename);
$templatelist = "thanks_results, thanks_results_none,thanks_content,thanks_page";
require_once "./global.php";

$forum_notgid = explode(',', $mybb->settings['thx_hidesystem_notgid']);
if(!$mybb->user['uid'] || $mybb->settings['thx_active'] != 1 || !function_exists('thx_is_installed') || in_array($mybb->user['usergroup'], $forum_notgid))
{
	error_no_permission();
}

require_once MYBB_ROOT.'inc/plugins/thx.php';

$lang->load("thx");

$plugins->run_hooks("thx_start");

add_breadcrumb($lang->thx_title, THIS_SCRIPT);

if($mybb->input['thanked'])
{
    if(!verify_post_check($mybb->input['my_post_key'])){
		error($lang->thx_cant_see);
	}

	$mybb->input['thanked'] = intval($mybb->input['thanked']);
	$mybb->input['thanked'] = $db->escape_string($mybb->input['thanked']);
	
	$query = $db->simple_select('thx', '*', "uid='{$mybb->input['thanked']}' ORDER BY txid");
	$thx = $db->fetch_array($query);
	$db->free_result($query);

	if(!$thx['uid'])
	{
		error($lang->thx_not_received);
	}

	add_breadcrumb($thx[''], THIS_SCRIPT."?thanked={$thx['uid']}&my_post_key={$mybb->post_code}");
	
	$page = intval($mybb->input['page']);
	if($page < 1) $page = 1;
	$numann = $db->fetch_field($db->simple_select('thx', 'COUNT(*) AS numann', "uid='{$mybb->input['thanked']}'"), 'numann');
	$perpage = 10;
	$multipage = multipage($numann, $perpage, $page, $_SERVER['PHP_SELF']."?thanked={$thx['uid']}&my_post_key={$mybb->post_code}");
	
	$query = $db->query("
		SELECT t.*, u.uid, u.username, u.usergroup, u.displaygroup, u.avatar, ug.username as ugname, ug.usergroup as uguserg, ug.displaygroup as ugdisp, ug.avatar as ugavatar
		FROM ".TABLE_PREFIX."thx t
		LEFT JOIN ".TABLE_PREFIX."users u ON (t.uid=u.uid)
		LEFT JOIN ".TABLE_PREFIX."users ug ON (t.adduid=ug.uid)		
		WHERE t.uid='".intval($thx['uid'])."'
		ORDER BY t.time DESC
		LIMIT ".(($page-1)*$perpage).", {$perpage}		
	");

	$users_list = '';
	while($gived = $db->fetch_array($query))
	{
		$trow = alt_trow();
        $gived['txid'] = intval($gived['txid']);		
        $gived['pid'] = intval($gived['pid']);
		$gived['url'] = htmlspecialchars_uni($mybb->settings['bburl'] . "/showthread.php?pid=" . $gived['pid'] . "#pid" . $gived['pid']);
		if($gived['avatar'] != ""){ 
		$gived['avatar'] = "<img src=".htmlspecialchars_uni($gived['avatar'])." class=\"thx_avatar\" alt=\"avatar\" />";
		}
		else{
		$gived['avatar'] = '<img src="images/default_avatar.gif" class="thx_avatar" alt="no avatar" />';
		}
		if($gived['ugavatar'] != ""){ 		
		$gived['ugavatar'] = "<img src=".htmlspecialchars_uni($gived['ugavatar'])." class=\"thx_avatar\" alt=\"avatar\" />";		
		}
		else{
		$gived['ugavatar'] = '<img src="images/default_avatar.gif" class="thx_avatar" alt="no avatar" />';
		}
		$gived['username'] = htmlspecialchars_uni($gived['username']);
		$gived['username'] = format_name($gived['username'], $gived['usergroup'], $gived['displaygroup']);
		$gived['username'] = build_profile_link($gived['username'], $gived['uid']);
		$gived['ugname'] = htmlspecialchars_uni($gived['ugname']);
		$gived['ugname'] = format_name($gived['ugname'], $gived['uguserg'], $gived['ugdisp']);
		$gived['ugname'] = build_profile_link($gived['ugname'], $gived['adduid']);		
		$gived['time'] = my_date($mybb->settings['dateformat'], $gived['time']);

		eval("\$users_list .= \"".$templates->get("thanks_results")."\";");
	}

	$db->free_result($query);

	if(!$users_list)
	{
		eval("\$users_list = \"".$templates->get("thanks_results_none")."\";");
	}

	eval("\$content = \"".$templates->get("thanks_content")."\";");
	eval("\$page = \"".$templates->get("thanks_page")."\";");

	output_page($page);
	exit;
}

else if($mybb->input['thanks'])
{
    if(!verify_post_check($mybb->input['my_post_key'])){
		error($lang->thx_cant_see);
	}

	$mybb->input['thanks'] = intval($mybb->input['thanks']);
	$mybb->input['thanks'] = $db->escape_string($mybb->input['thanks']);
	
	$query = $db->simple_select('thx', '*', "adduid='{$mybb->input['thanks']}' ORDER BY txid");
	$thx = $db->fetch_array($query);
	$db->free_result($query);

	if(!$thx['uid'])
	{
		error($lang->thx_not_given);
	}

	add_breadcrumb($thx[''], THIS_SCRIPT."?thanks={$thx['adduid']}&my_post_key={$mybb->post_code}");
	
	$page = intval($mybb->input['page']);
	if($page < 1) $page = 1;
	$numann = $db->fetch_field($db->simple_select('thx', 'COUNT(*) AS numann', "adduid='{$mybb->input['thanks']}'"), 'numann');
	$perpage = 10;
	$multipage = multipage($numann, $perpage, $page, $_SERVER['PHP_SELF']."?thanks={$thx['adduid']}&my_post_key={$mybb->post_code}");
	
	$query = $db->query("
		SELECT t.*, u.uid, u.username, u.usergroup, u.displaygroup, u.avatar, ug.username as ugname, ug.usergroup as uguserg, ug.displaygroup as ugdisp, ug.avatar as ugavatar
		FROM ".TABLE_PREFIX."thx t
		LEFT JOIN ".TABLE_PREFIX."users u ON (t.uid=u.uid)
		LEFT JOIN ".TABLE_PREFIX."users ug ON (t.adduid=ug.uid)		
		WHERE t.adduid='".intval($thx['adduid'])."'
		ORDER BY t.time DESC
		LIMIT ".(($page-1)*$perpage).", {$perpage}		
	");

	$users_list = '';
	while($gived = $db->fetch_array($query))
	{
		$trow = alt_trow();
        $gived['txid'] = intval($gived['txid']);		
        $gived['pid'] = intval($gived['pid']);
		$gived['url'] = htmlspecialchars_uni($mybb->settings['bburl'] . "/showthread.php?pid=" . $gived['pid'] . "#pid" . $gived['pid']);
		if($gived['avatar'] != ""){ 
		$gived['avatar'] = "<img src=".htmlspecialchars_uni($gived['avatar'])." class=\"thx_avatar\" alt=\"avatar\" />";
		}
		else{
		$gived['avatar'] = '<img src="images/default_avatar.gif" class="thx_avatar" alt="no avatar" />';
		}
		if($gived['ugavatar'] != ""){ 		
		$gived['ugavatar'] = "<img src=".htmlspecialchars_uni($gived['ugavatar'])." class=\"thx_avatar\" alt=\"avatar\" />";		
		}
		else{
		$gived['ugavatar'] = '<img src="images/default_avatar.gif" class="thx_avatar" alt="no avatar" />';
		}
		$gived['username'] = htmlspecialchars_uni($gived['username']);
		$gived['username'] = format_name($gived['username'], $gived['usergroup'], $gived['displaygroup']);
		$gived['username'] = build_profile_link($gived['username'], $gived['uid']);
		$gived['ugname'] = htmlspecialchars_uni($gived['ugname']);
		$gived['ugname'] = format_name($gived['ugname'], $gived['uguserg'], $gived['ugdisp']);
		$gived['ugname'] = build_profile_link($gived['ugname'], $gived['adduid']);		
		$gived['time'] = my_date($mybb->settings['dateformat'], $gived['time']);

		eval("\$users_list .= \"".$templates->get("thanks_results")."\";");
	}
	$db->free_result($query);

	if(!$users_list)
	{
		eval("\$users_list = \"".$templates->get("thanks_results_none")."\";");
	}

	eval("\$content = \"".$templates->get("thanks_content")."\";");
	eval("\$page = \"".$templates->get("thanks_page")."\";");

	output_page($page);
	exit;
}

else if($mybb->input['thanked_pid'])
{
    if(!verify_post_check($mybb->input['my_post_key'])){
		error($lang->thx_cant_see);
	}
	
	$mybb->input['thanked_pid'] = intval($mybb->input['thanked_pid']);
	$mybb->input['thanked_pid'] = $db->escape_string($mybb->input['thanked_pid']);
	
	$query = $db->simple_select('thx', '*', "pid='{$mybb->input['thanked_pid']}' ORDER BY txid");
	$thx = $db->fetch_array($query);
	$db->free_result($query);

	if(!$thx['uid'])
	{
		error($lang->thx_not_post);
	}

	add_breadcrumb($thx[''], THIS_SCRIPT."?thanked_pid={$thx['pid']}&my_post_key={$mybb->post_code}");
	
	$page = intval($mybb->input['page']);
	if($page < 1) $page = 1;
	$numann = $db->fetch_field($db->simple_select('thx', 'COUNT(*) AS numann', "pid='{$mybb->input['thanked_pid']}'"), 'numann');
	$perpage = 10;
	$multipage = multipage($numann, $perpage, $page, $_SERVER['PHP_SELF']."?thanked_pid={$thx['pid']}&my_post_key={$mybb->post_code}");
	
	$query = $db->query("
		SELECT t.*, u.uid, u.username, u.usergroup, u.displaygroup, u.avatar, ug.username as ugname, ug.usergroup as uguserg, ug.displaygroup as ugdisp, ug.avatar as ugavatar
		FROM ".TABLE_PREFIX."thx t
		LEFT JOIN ".TABLE_PREFIX."users u ON (t.uid=u.uid)
		LEFT JOIN ".TABLE_PREFIX."users ug ON (t.adduid=ug.uid)		
		WHERE t.pid='".intval($thx['pid'])."'
		ORDER BY t.time DESC
		LIMIT ".(($page-1)*$perpage).", {$perpage}		
	");

	$users_list = '';
	while($gived = $db->fetch_array($query))
	{
		$trow = alt_trow();
        $gived['txid'] = intval($gived['txid']);		
        $gived['pid'] = intval($gived['pid']);
		$gived['url'] = htmlspecialchars_uni($mybb->settings['bburl'] . "/showthread.php?pid=" . $gived['pid'] . "#pid" . $gived['pid']);
		if($gived['avatar'] != ""){ 
		$gived['avatar'] = "<img src=".htmlspecialchars_uni($gived['avatar'])." class=\"thx_avatar\" alt=\"avatar\" />";
		}
		else{
		$gived['avatar'] = '<img src="images/default_avatar.gif" class="thx_avatar" alt="no avatar" />';
		}
		if($gived['ugavatar'] != ""){ 		
		$gived['ugavatar'] = "<img src=".htmlspecialchars_uni($gived['ugavatar'])." class=\"thx_avatar\" alt=\"avatar\" />";		
		}
		else{
		$gived['ugavatar'] = '<img src="images/default_avatar.gif" class="thx_avatar" alt="no avatar" />';
		}
		$gived['username'] = htmlspecialchars_uni($gived['username']);
		$gived['username'] = format_name($gived['username'], $gived['usergroup'], $gived['displaygroup']);
		$gived['username'] = build_profile_link($gived['username'], $gived['uid']);
		$gived['ugname'] = htmlspecialchars_uni($gived['ugname']);
		$gived['ugname'] = format_name($gived['ugname'], $gived['uguserg'], $gived['ugdisp']);
		$gived['ugname'] = build_profile_link($gived['ugname'], $gived['adduid']);		
		$gived['time'] = my_date($mybb->settings['dateformat'], $gived['time']);

		eval("\$users_list .= \"".$templates->get("thanks_results")."\";");
	}
	$db->free_result($query);

	if(!$users_list)
	{
		eval("\$users_list = \"".$templates->get("thanks_results_none")."\";");
	}

	eval("\$content = \"".$templates->get("thanks_content")."\";");
	eval("\$page = \"".$templates->get("thanks_page")."\";");

	output_page($page);
	exit;
}

?>
