/**
 * Thank You MyBB System + MyAlerts + rep xD v 2.3.2
 * Upgrade for MyBB 1.6.x (actually 1.6.12)
 * darkneo.skn1.com
 * Author: Dark Neo
 */

var pid=-1;
var spinner=null;
function thx_common(response)
{
	try
	{
		xml=response.responseXML;
		lin=document.getElementById('a'+pid);
		if (lin) {				 
			list = document.getElementById('thx_list' + pid);
			list.innerHTML = xml.getElementsByTagName('list').item(0).firstChild.data;
			thxcount = document.getElementById('thx_thanked_' + pid);
			thxcount.innerHTML = xml.getElementsByTagName('thxcount').item(0).firstChild.data;															
			button = document.getElementById('sp_' + pid);
			button.innerHTML = xml.getElementsByTagName('button').item(0).firstChild.data;	
			counter = document.getElementById('counter' + pid);
			counter.innerHTML = xml.getElementsByTagName('count').item(0).firstChild.data;
            post = document.getElementById('thxpid_' + pid);
			post.innerHTML = xml.getElementsByTagName('post').item(0).firstChild.data;
		}
		else
		{
			lin.innerHTML="";
			lin.onclick=null;
			lin.href="";
			lin = null;		
		}
	}
	catch(err)
	{
		alert("an error has ocurred")
		alert(err);
	}
	finally
	{
		spinner.destroy();
		spinner=null;
		return lin;
	}
	
}
function thx_action(response)
{
	lin=thx_common(response)
	if(lin!=null)
	{
		lin.onclick= new Function("","return rthx("+pid+");");
		lin.href='showthread.php?action=remove_thank&pid='+pid;
	}
}

function rthx_action(response)
{
	lin=thx_common(response)
	if (lin!=null) 
	{
		lin.onclick = new Function("", "return thx(" + pid + ");");
		lin.href = 'showthread.php?action=thank&pid=' + pid;
	}
	
	
}

function thx(id)
{
	if(spinner)
		return false;
	spinner = new ActivityIndicator("body", {image: imagepath + "/spinner_big.gif"});
	pid=id;
	pb="pid="+pid;
	new Ajax.Request('xmlhttp.php?action=thankyou&my_post_key='+my_post_key,{method: 'post',postBody:pb, onComplete:thx_action});
	return false;
}

function rthx(id)
{
	if(spinner)
		return false;
	spinner = new ActivityIndicator("body", {image: imagepath + "/spinner_big.gif"});
	pid=id;
	b="pid="+pid;
	new Ajax.Request('xmlhttp.php?action=remove_thankyou&my_post_key='+my_post_key,{method: 'post',postBody:b,onComplete:rthx_action});
	return false;
}