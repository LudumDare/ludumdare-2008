function view( id ) {
	var vote = document.getElementById(id).value;
	unselect(id);
    document.getElementById(''+ vote + id).className='s';
}

function set(id, value) {
    document.getElementById(id).value = value;
	view(id);
}

function unselect(id) {    
    document.getElementById('1'+ id).className='us';
    document.getElementById('0'+ id).className='us';
    document.getElementById('-1'+ id).className='us';
}

//Loop all themes
function initvote(themecount) {    
	for (var i = 0; i < themecount; i++) {
		try{
			view( 'vote_'+ i);
		}catch(e){}
	}
// 	alert('voted '+ themecount);
}