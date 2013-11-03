/**
 * Thank You MyBB System + MyAlerts + rep xD v 2.2.
 * Upgrade for MyBB 1.6.x (actually 1.6.11)
 * www.soportemybb.com
 * Author: Dark Neo
 * bassed on Saeedgh thanks 3.9.1 plugin
 */

var pid=-1;
var spinner=null;
function thx_common(response)
{
	try
	{
		xml=response.responseXML;
		remove=xml.getElementsByTagName('del').item(0).firstChild.data=="1";	
		lin=document.getElementById('a'+pid);
		if (remove) {				 
			table = document.getElementById('thx' + pid);
			table.style.display = xml.getElementsByTagName('display').item(0).firstChild.data != 0 ?
				 '' : 'none';
			list = document.getElementById('thx_list' + pid);
			list.innerHTML = xml.getElementsByTagName('list').item(0).firstChild.data;
			button = document.getElementById('sp' + pid);
			button.innerHTML = xml.getElementsByTagName('button').item(0).firstChild.data;			
            post = document.getElementById('pid_' + pid);
			post.innerHTML = xml.getElementsByTagName('post').item(0).firstChild.data;
		}
		else if(!remove)
		{		
			lin.onclick=null;
			lin.href="";
			lin = null;	
		    table = document.getElementById('thx' + pid);		
			table.style.display = xml.getElementsByTagName('display').item(0).firstChild.data != 0 ?
				'' : 'none';		
			list = document.getElementById('thx_list' + pid);
			list.innerHTML = xml.getElementsByTagName('list').item(0).firstChild.data;	
			button = document.getElementById('sp' + pid);
			button.innerHTML = xml.getElementsByTagName('button').item(0).firstChild.data;	
            post = document.getElementById('pid_' + pid);
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
	spinner = new ActivityIndicator("body", {image: "images/spinner_big.gif"});
	pid=id;
	pb="pid="+pid;
	new Ajax.Request('xmlhttp.php?action=thankyou&my_post_key='+my_post_key,{method: 'post',postBody:pb, onComplete:thx_action});
	return false;
}

function rthx(id)
{
	if(spinner)
		return false;
	spinner = new ActivityIndicator("body", {image: "images/spinner_big.gif"});
	pid=id;
	b="pid="+pid;
	new Ajax.Request('xmlhttp.php?action=remove_thankyou&my_post_key='+my_post_key,{method: 'post',postBody:b,onComplete:rthx_action});
	return false;
}
