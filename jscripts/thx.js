/**
  * Thank you 2.2
  * Upgrade for MyBB 1.6.x (actually 1.6.6)
  * www.soportemybb.com
*/
var pid=-1;
var spinner='';
var ThankYou = {
thx_action: function(response) {
    xml=response.responseXML;	
	table=document.getElementById("thx"+pid);	
	list=document.getElementById("thx_list"+pid);
    text.style.display='block';
	table.style.display=xml.getElementsByTagName("display").item(0).firstChild.data!=0 ? '' : 'none';
	list.innerHTML=xml.getElementsByTagName('list').item(0).firstChild.data;
	lin=document.getElementById('a'+pid);	
	lin.onclick= new Function("","return ThankYou.rthx("+pid+");");	
	lin.href='showthread.php?action=thank&tid'+tid+'pid='+pid;    
	document.body.style.cursor = 'default';	
	spinner='';
},

rthx_action: function (response) {	
    xml=response.responseXML;	
	table=document.getElementById("thx"+pid);	
	list=document.getElementById("thx_list"+pid);	
	table.style.display=xml.getElementsByTagName("display").item(0).firstChild.data!=0 ? '' : 'none';	
	list.innerHTML=xml.getElementsByTagName("list").item(0).firstChild.data;	
	lin=document.getElementById("a"+pid);	
	lin.onclick= new Function("","return ThankYou.thx("+pid+");");	
	lin.href='showthread.php?action=remove_thank&tid='+tid+'pid='+pid;	
	document.body.style.cursor = 'default';	
	spinner='';
},
thx: function (id) {
	this.spinner = new ActivityIndicator("body", {	
	image: imagepath + "/spinner_big.gif",
	});	
	document.body.style.cursor = 'wait';	
	b="#pid"+pid;	
	new Ajax.Request('xmlhttp.php?action=thankyou',{	
	method: 'post', 	
	postBody:b,	
	onComplete: function(request) { 
	ThankYou.thx_action(request);}	
	});	
	spinner.destroy();return false;},

rthx: function rthx(id) {	
    this.spinner = new ActivityIndicator("body", {	
	image: imagepath + "/spinner_big.gif",
	});	
	document.body.style.cursor = 'wait';	
	b="#pid"+pid;	
	new Ajax.Request('xmlhttp.php?action=remove_thankyou',{	
	method: 'post', 	
	postBody:b ,	
	onComplete: function(request) { 	
	ThankYou.rthx_action(request);}
	});
	spinner.destroy();return false;},
}