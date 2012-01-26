//rc_admin_js ver. 1.2 by redcocker 2012/1/20

function showhide(id){
	if(document.getElementById){
		if(document.getElementById(id).style.display == "block"){
			document.getElementById(id).style.display = "none";
		}else{
			document.getElementById(id).style.display = "block";
		}
	}
}

function confirmreset(){
	var flag = window.confirm ( 'Click "OK" to restore all settings?');
	return flag;
}

function confirmdelete(){
	var flag = window.confirm ( 'Click "OK" to delete PDFs?');
	return flag;
}
