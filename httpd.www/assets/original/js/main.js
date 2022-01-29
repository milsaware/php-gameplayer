var fullPath = window.location.href;
var originPath = window.location.origin;
var path = fullPath.replace(originPath,'');
var loginTimer = 2000;
var homeScreen;
var defaultColor = '#c80505';
var os;
var timeoutHandle;
var timeoutPSearch;

$(document).keyup(function(e) {
	if (e.key === "Escape") {
		removeElements();
	}
});

if (typeof(Storage) !== "undefined") {
	if (localStorage.getItem("loginTimer") === null) {
		localStorage.setItem("loginTimer", 2000);
	}	
	loginTimer = localStorage.getItem("loginTimer");
}

console.log(path);

function gameIni(){
	let viewbtn = document.getElementsByClassName("icon-eye");

	for (var i = 0; i < viewbtn.length; i++) {
		let eyeId = viewbtn[i].getAttribute('id');
		if(eyeId != 'pass_plain'){
			$(viewbtn[i]).bind('click', function(){
				$(this).unbind('click', arguments.callee);
				openMessageViewer(this, 'view');
			});
		}
	}

	let replybtn = document.getElementsByClassName("icon-reply");

	for (var i = 0; i < replybtn.length; i++) {
		$(replybtn[i]).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			openMessageViewer(this, 'respond');
		});
	}

	let likebtn = document.getElementsByClassName("icon-chat-like");

	for (var i = 0; i < likebtn.length; i++) {
		let likeClass = likebtn[i].getAttribute('class');
		if(likeClass == 'icon-chat-like icon-thumbs-o-up'){
			$(likebtn[i]).bind('click', function(){
				$(this).unbind('click', arguments.callee);
				commentLike(this);
			});
		}
	}

	let commentMenuLink = document.getElementsByClassName("comment_ell_menu");	

	for (var i = 0; i < commentMenuLink.length; i++) {
		$(commentMenuLink[i]).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			commentMenu(this);
		});
	}

	$('#new_comment_submit').bind('click', function(){
		$(this).unbind('click', arguments.callee);
		sendComment(this);
	});
	
	if(document.getElementById('chatSignin')){
		$('#chatSignin').bind('click', function(){
			login_popup('login');
		});
	}
	
	if(document.getElementById('chatRegister')){
		$('#chatRegister').bind('click', function(){
			login_popup('register');
		});
	}

	$('#gameSettings').bind('click', function(){
		gameSettings();
	});
}

function navIni(){
	var logo = document.getElementsByClassName('logo')[0];
	var navSignIn = document.getElementById('navSignIn');
	logo.addEventListener("click", function(){
		let container = document.getElementById('container');
		container.innerHTML = '';
		loading('mainLoad');
		$.ajax({
			type: "POST",
			url: "/index.php",
			data: {request: 'true'},
			success: function(data) {
				history.pushState('home', 'Gamtry', '/index.php');
				container.innerHTML = data;
				$('#mainLoad').remove();
				container.style.cssText = '';
				sliderIni();
			}
		});
	});
	if(navSignIn){
		navSignIn.addEventListener("click", function(){
			login_popup('login');
		});
	}
	
	if(document.getElementById('avatarMenu')){
		avMenClick();
	}
}

function openProfileGame(id){
	$.ajax({
		type: "POST",
		url: "/index.php?route=profile",
		data: {request: 'true', page: 'live', game_id: id},
		success: function(data) {
			$('.profileContentBlock').html(data);
		}
	});
}

function openSandboxGame(id){
	$.ajax({
		type: "POST",
		url: "/index.php?route=profile&page=sandbox&action=open&game_id="+id,
		data: {request: 'true'},
		success: function(data) {
			$('.profileContentBlock').html(data);
		}
	});
}

function openProfile(h){
	window.location.href = '?route=profile&handle=' + h;
}

function profileIni(){
	$('#userMenu').remove();
	$('#icon-pmoFollowing').bind('click', function(){
		$(this).unbind('click', arguments.callee);
		profileLinkController('top', this);
		this.setAttribute('class', 'profileMenuOption pmoActive');
		profileOpen('following', this);
		init('following');
	});

	$('#icon-pmoSettings').bind('click', function(){
		$(this).unbind('click', arguments.callee);
		profileLinkController('top', this);
		this.setAttribute('class', 'profileMenuOption pmoActive');
		profileOpen('settings', this);
		init('settings');
	});

	$('#psmo-dashboard').bind('click', function(){
		$(this).unbind('click', arguments.callee);
		profileLinkController('side', this);
		profileOpen('dashboard', this);
		init('dashboard');
	});

	$('#psmo-games').bind('click', function(){
		$(this).unbind('click', arguments.callee);
		profileLinkController('side', this);
		profileOpen('games', this);
		init('profileGames');
	});

	$('#psmoGameNew').bind('click', function(){
		console.log('clicked')
		profileOpen('psmoGameNew', this);
		init('psmoGameNew');
	});

	$('#psmoSandbox').bind('click', function(){
		console.log('clicked')
		profileOpen('psmoSandbox', this);
		init('psmoSandbox');
	});

	$('#psmo-logout').bind('click', function(){
		logOut(1);
	});
}

function sliderIni(){
	let e = this;
	$('#userMenu').remove();
	let container = document.getElementById('container');
	let spinner = document.getElementById('spinner');
	let sliderThumb = document.getElementsByClassName("slider_thumb");	
	let sliderBlock = document.getElementsByClassName("slider_block");	
	for (var i = 0; i < sliderThumb.length; i++) {

		sliderBlock[i].addEventListener("click", function(){
			slideClick(this);
		});

		sliderBlock[i].addEventListener("mouseover", function(){
			shadowOptions(this);			
		});
	}


	let homeCatSect = document.getElementsByClassName("homeCatSect");
	for (var a = 0; a < homeCatSect.length; a++) {
		let parent = homeCatSect[a];
		let homeCatChildren = parent.children;
		$(parent).hover(function(){
			for (var b = 0; b < homeCatChildren.length; b++){
				var attClass = homeCatChildren[b].getAttribute('class');
				if(attClass == 'sBtn'){
					let hcc = homeCatChildren[b];
					parent.addEventListener("wheel", function(){
						hcc.setAttribute('style', 'opacity:0');
					});
					hcc.setAttribute('style', 'opacity:1');
				}
			}
		}, function(){
			for (var c = 0; c < homeCatChildren.length; c++){
				var attClass = homeCatChildren[c].getAttribute('class');
				if(attClass == 'sBtn'){
					homeCatChildren[c].setAttribute('style', 'opacity:0');
				}
			}
		});
	}

	let clickLeft = document.getElementsByClassName("slideLeft");
	for (var a = 0; a < clickLeft.length; a++) {
		clickLeft[a].addEventListener("click", function(){
			slide_menu(this, 'left');
		});
	}

	let clickRight = document.getElementsByClassName("slideRight");
	for (var a = 0; a < clickRight.length; a++) {
		clickRight[a].addEventListener("click", function(){
			slide_menu(this, 'right');
		});
	}
}

function removeElements(){
	$('#shadow_box').remove();
	$('#userMenu').remove();
	$('#loginContainer').remove();
	$('#modalContainer').remove();
	$('#message_viewer').remove();
	document.removeEventListener("wheel", removeUserMenu);
	document.removeEventListener("click", removeUserMenu);
}

function removeUserMenu(){
	$('#userMenu').remove();
	document.removeEventListener("wheel", removeUserMenu);
	document.removeEventListener("click", removeUserMenu);
}

function profileGame(game_id){
	console.log(game_id);
}

function liveRequest(id){
	$.ajax({
		type: "POST",
		url: "/index.php?route=profile&page=sandbox",
		data: {request: 'true', action: 'liveRequest', game_id: id},
		success: function(data) {
			$("#sbvrq").removeAttr("onclick");
			$("#sbvrq").css({"background-color": "green"});
			$("#sbvrq").text('request sent');
		}
	});
}

function submitGame(t=0, id=0){
	let e = this;
	let title = document.getElementById('title').value;
	let description = document.getElementById('description').value;
	let embedURL = document.getElementById('embedURL').value;
	let cat = document.getElementById('cat').value;
	var fd = new FormData();
	var files = $('#gameThumbIn')[0].files;
	let form = (t == 0)? 'submitGame' : 'updateGame';

	if(((t == 0 && files.length > 0) || t == 1) && title.length > 2 && embedURL.length > 0 && cat != '0'){
		
		fd.append('file',files[0]);
		fd.append("title", title);
		fd.append("description", description);
		fd.append("embedURL", embedURL);
		fd.append("cat", cat);
		if(t == 1){fd.append("id", id)}
		$.ajax({
			url: "/index.php?route=submit_form&form="+form,
			type: 'post',
			data: fd,
			contentType: false,
			processData: false,
			success: function(data){
				if(t == 0){
					let psmoSandbox = document.getElementById('psmoSandbox');
				//	profileOpen('psmoSandbox', psmoSandbox);
					openSandboxGame(data)
					init('psmoSandbox');
				}else if(t == 1){
					$("#sbvSubmit").removeAttr("onclick");
					$("#sbvSubmit").css({"background-color": "green"});
					$("#sbvSubmit").text('updated');
					setTimeout(function(){
					$("#sbvSubmit").attr("onclick", "submitGame('1', , "+id+")");
					$("#sbvSubmit").css({"background-color": "red"});
					$("#sbvSubmit").text('update');
					}, 2500);

					let iframe = $('.sbvPreview');
					iframe.attr('src', embedURL);
					console.log(data);
				}
			},
		});
	}
}

function updateLive(id){
	let title = document.getElementById('title').value;
	let description = document.getElementById('description').value;
	let embedURL = document.getElementById('embedURL').value;
	let cat = document.getElementById('cat').value;
	var fd = new FormData();
	var files = $('#gameThumbIn')[0].files;

	if(title.length > 2 && embedURL.length > 0 && cat != '0'){
		fd.append('file',files[0]);
		fd.append("title", title);
		fd.append("description", description);
		fd.append("embedURL", embedURL);
		fd.append("cat", cat);
		fd.append("id", id);
		$("#sbvSubmit").removeAttr("onclick");
		$.ajax({
			url: "/index.php?route=submit_form&form=updateLive",
			type: 'post',
			data: fd,
			contentType: false,
			processData: false,
			success: function(data){
				$("#sbvSubmit").css({"background-color": "green"});
				$("#sbvSubmit").text('updated');
				setTimeout(function(){
					$("#sbvSubmit").attr("onclick", "submitGame('1', "+id+")");
					$("#sbvSubmit").css({"background-color": "red"});
					$("#sbvSubmit").text('update');
				}, 2500);

				let iframe = $('.sbvPreview');
				iframe.attr('src', embedURL);
				console.log(data);
			},
		});
	}
}

function init(type){
	if(type == 'games'){
		$('#psmo-logout').bind('click', function(){
			logOut(1);
		});
	}
	else if(type == 'profileFetch'){
		$('#btnFollow').bind('click', function(){
			$(this).unbind('click', arguments.callee);
			let e = this;
			let uid = e.getAttribute('data-id');
			$.ajax({
				type: "POST",
				url: "/index.php?route=submit_form&form=followAct",
				data: {request: 'true', uid: uid},
				success: function() {
					e.setAttribute('id', 'btnUnfollow');
					e.innerText = 'following';
					$(e).bind('click', function(){
						$(this).unbind('click', arguments.callee);
						unfollowAccount(this);
					});
				}
			});
		});
		$('#btnUnfollow').bind('click', function(){
			$(this).unbind('click', arguments.callee);
			let e = this;
			let uid = e.getAttribute('data-id');
			$.ajax({
				type: "POST",
				url: "/index.php?route=submit_form&form=unfollowAct",
				data: {request: 'true', uid: uid},
				success: function() {
					e.setAttribute('id', 'btnFollow');
					e.innerText = 'follow';
					$(e).bind('click', function(){
						$(this).unbind('click', arguments.callee);
						followAccount(this);
					});
				}
			});			
		});
		$('#profileSearch').bind('keyup', function(){
			if(this.value.length > 2){
				let e = this;
				window.clearTimeout(timeoutPSearch);
				timeoutPSearch = window.setTimeout(function(){
					let q = e.value;
					let handle = e.getAttribute('data-handle');
					$.ajax({
					type: "POST",
					url: "/index.php?route=submit_form&form=profileSearch",
					data: {request: 'true', q: q, handle: handle},
					success: function(data) {
						var tochange = (document.getElementsByClassName('pcbpf')[0])? 'pcbpf' : 'pcbu';
						document.getElementsByClassName(tochange)[0].innerHTML = data;
					}
				});	
				}, 800);
				
			}		
		});
	}

	else if(type == 'profileGames'){
	}
}

function unfollowAccount(e){
	let uid = e.getAttribute('data-id');
	$.ajax({
		type: "POST",
		url: "/index.php?route=submit_form&form=unfollowAct",
		data: {request: 'true', uid: uid},
		success: function() {
			e.setAttribute('id', 'btnFollow');
			e.innerText = 'follow';
			$(e).bind('click', function(){
				$(this).unbind('click', arguments.callee);
				followAccount(this);
			});
		}
	});
}

function followAccount(e){
	let uid = e.getAttribute('data-id');
	$.ajax({
		type: "POST",
		url: "/index.php?route=submit_form&form=followAct",
		data: {request: 'true', uid: uid},
		success: function() {
			e.setAttribute('id', 'btnUnfollow');
			e.innerText = 'following';
			$(e).bind('click', function(){
				$(this).unbind('click', arguments.callee);
				unfollowAccount(this);
			});
		}
	});
}

function profileLinkController(loc, e){
	var type = '';
	let pmoClass = (loc == 'top')? 'profileMenuOption pmoActive' : 'psmOption psmActive';
	let newClass = (loc == 'top')? 'profileMenuOption' : 'psmOption';
	let pmoRep = (loc == 'top')? 'icon-pmo' : 'psmo-';
	let pmoActive = document.getElementsByClassName(pmoClass)[0];
	if(pmoActive){
		pmoActive.setAttribute('class', newClass);
		let type = pmoActive.getAttribute('id').replace(pmoRep, '').toLowerCase();
		let newLoc = (type == 'liked' || type == 'following' || type == 'settings')? 'top' : 'side';
		$(pmoActive).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			profileLinkController(newLoc, e);
			this.setAttribute('class', pmoClass);
			profileOpen(type, this);
		});
	}else{
		$(e).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			profileLinkController(loc, e);
			this.setAttribute('class', pmoClass);
			let type = this.getAttribute('id').replace(pmoRep, '').toLowerCase();
			profileOpen(type, e);
		});
	}
	if(loc == 'side'){
		$("#icon-pmoLiked").removeClass("profileMenuOption pmoActive");
		$("#icon-pmoLiked").addClass("profileMenuOption");
		$('#icon-pmoLiked').unbind('click', arguments.callee);
		$('#icon-pmoLiked').bind('click', function(){
			$(this).unbind('click', arguments.callee);
			profileLinkController('top', this);
			this.setAttribute('class', 'profileMenuOption pmoActive');
			profileOpen('liked', this);
		});

		$("#icon-pmoFollowing").removeClass("profileMenuOption pmoActive");
		$("#icon-pmoFollowing").addClass("profileMenuOption");
		$('#icon-pmoFollowing').unbind('click', arguments.callee);
		$('#icon-pmoFollowing').bind('click', function(){
			$(this).unbind('click', arguments.callee);
			profileLinkController('top', this);
			this.setAttribute('class', 'profileMenuOption pmoActive');
			profileOpen('following', this);
		});

		$("#icon-pmoSettings").removeClass("profileMenuOption pmoActive");
		$("#icon-pmoSettings").addClass("profileMenuOption");
		$('#icon-pmoSettings').unbind('click', arguments.callee);
		$('#icon-pmoSettings').bind('click', function(){
			$(this).unbind('click', arguments.callee);
			profileLinkController('top', this);
			this.setAttribute('class', 'profileMenuOption pmoActive');
			profileOpen('settings', this);
		});
	}else{
		
	}	
}

function auto_grow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight)+"px";
}

function profileOpen(type, e){
	$.ajax({
		type: "POST",
		url: "/index.php?route=profile",
		data: {request: 'true', page: type},
		success: function(data) {
			let profileContentBlock = document.getElementsByClassName('profileContentBlock')[0];
			profileContentBlock.innerHTML = data;
		}
	});
}

function avMenClick(){
	$('#avatarMenu').bind('click', function(){
		avMenuPop();
	});
}

function avMenuPop(){
	if(document.getElementById('userMenu')){
		removeElements();
	}else{
		document.addEventListener("wheel", removeUserMenu);
		setTimeout(function(){
			document.addEventListener("click", removeUserMenu);
		}, 100);
		document.body.onclick = function (e) {
			if (e && (e.which == 2 || e.button == 4 )) {
				removeUserMenu();
			}
		}
		let navHead = document.getElementsByClassName('navHead')[0];
		let userMenu = document.createElement('div');
		userMenu.setAttribute('id', 'userMenu');

		let proSpan = document.createElement('span');
		proSpan.setAttribute('class', 'userMenuOption');
		proSpan.innerText = 'Profile';
		$(proSpan).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			removeElements();
			$.ajax({
				type: "POST",
				url: "/index.php?route=profile",
				data: {request: 'origin'},
				success: function(data) {
					history.pushState('profile', 'Gamtry', '/index.php?route=profile');
					document.getElementById('container').innerHTML = data;
					profileIni();
				}
			});
		});

		userMenu.append(proSpan);

		let logSpan = document.createElement('span');
		logSpan.setAttribute('class', 'userMenuOption');
		logSpan.innerText = 'Log out';
		$(logSpan).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			removeElements();
			logOut(1);
		});
		userMenu.append(logSpan);

		navHead.append(userMenu);
	}
}

function logOut(step){
	if(step == 1){
		loginModal(1, 1);
	}else{
		$.ajax({
			type: "POST",
			url: "/index.php?route=auth&action=logout",
			data: {request: 'true'},
			success: function() {
				location.reload();
			}
		});
	}
}

function openStats(game_id){
	let modalTitle = document.getElementsByClassName('modal_title')[0];
	let modalDescription = document.getElementsByClassName('modal_description')[0];
	$(modalTitle).fadeOut(1000);
	$(modalDescription).fadeOut(1000);
}

function gameModalContent(game_id, type, e){
	let modalDescBlock = document.getElementById('modalDescBlock');
	modalDescBlock.innerHTML = '';

	let loadingDisplay = document.createElement('div');
	loadingDisplay.setAttribute('class', 'loading');
	loadingDisplay.setAttribute('id', 'gameModalLoad');
	modalDescBlock.append(loadingDisplay);

	let spanA = document.createElement('span');
	spanA.innerText = '.';
	loadingDisplay.append(spanA);

	let spanB = document.createElement('span');
	spanB.innerText = '.';
	loadingDisplay.append(spanB);

	let spanC = document.createElement('span');
	spanC.innerText = '.';
	loadingDisplay.append(spanC);

	$.ajax({
		type: "POST",
		url: "/index.php?route=profile",
		data: {request: 'true', action: 'gameAdmin', page: type, game_id: game_id},
		success: function(data) {
			let modalOptionActive = document.getElementsByClassName('modalOptionActive')[0];
			modalOptionActive.setAttribute('class', 'modalOption');
			let moaType = modalOptionActive.getAttribute('id');
			e.setAttribute('class', 'modalOptionActive');
			$(modalOptionActive).bind('click', function(){
				$(this).unbind('click', arguments.callee);
				gameModalContent(game_id, moaType, this);
			});
			modalDescBlock.innerHTML = data;
		}
	});
}

function openModal(type, game_id=0){
	$('#shadow_box').remove();
	$('#gameContainer').remove();
	$('#loginContainer').remove();
	let container = document.getElementById('container');

	let modalContainer = document.createElement('div');
	modalContainer.setAttribute('id', 'modalContainer');
	container.prepend(modalContainer);

	let modalBlock = document.createElement('div');
	modalBlock.setAttribute('class', 'modalBlock');
	modalContainer.append(modalBlock);

	let modalContent = document.createElement('div');
	modalContent.setAttribute('class', 'modalContent');
	modalBlock.append(modalContent);

	let iconClose = document.createElement('span');
	iconClose.setAttribute('class', 'modal_close');
	$(iconClose).bind('click', function(){
		$('#shadow_box').remove();
		$(modalContainer).remove();
	});
	modalContent.append(iconClose);

	let modalHead = document.createElement('div');
	modalHead.setAttribute('class', 'modalHead');
	if(game_id != 0){
		if(type == 'devGameView'){
			modalHead.innerHTML = '<img src="assets/images/games/online/'+game_id+'/thumb.png" class="mhthumb">';
		}
	}else{
		if(type == 'banner'){
			$.ajax({
				type: "POST",
				url: "/index.php?route=submit_form&form=fetchColour",
				data: {request: 'true'},
				success: function(data) {
					let modalDescBlock = document.getElementById('modalDescBlock');
					let modalBlock = document.getElementsByClassName('modalBlock')[0];
					let modalContent = document.getElementsByClassName('modalContent')[0];
					modalContent.innerHTML = data;
					modalBlock.style.cssText = 'width:180px;height:160px;';
					modalContent.style.cssText = 'width:initial;height:160px;top:-55px;left:-30px';
					let iconClose = document.createElement('span');
					iconClose.setAttribute('class', 'modal_close');
					iconClose.style.cssText = 'right:-87px;top:-2px;';
					$(iconClose).bind('click', function(){
						$('#shadow_box').remove();
						$(modalContainer).remove();
					});
					modalContent.append(iconClose);

					let colourSave = document.createElement('span');
					colourSave.setAttribute('class', 'colourSave');
					colourSave.innerText = 'Save';
					$(colourSave).bind('click', function(){
						saveBannerColour(this);
					});
					modalContent.append(colourSave);

					let colourCancel = document.createElement('span');
					colourCancel.setAttribute('class', 'colourCancel');
					colourCancel.innerText = 'Cancel';
					$(colourCancel).bind('click', function(){
						$('#shadow_box').remove();
						$(modalContainer).remove();
					});
					modalContent.append(colourCancel);
				}
			});
		}
		else if(type == 'confirmAvChange'){
			let confMes = document.createElement('span');
			confMes.setAttribute('class', 'confMes');
			confMes.style.cssText = 'margin-top:28px;';
			confMes.innerText = 'Keep changes?';
			modalContent.append(confMes);
			modalBlock.style.cssText = 'height:50px;width:170px;';
			modalContent.style.cssText = 'height:auto;width:auto;left:0;';
			iconClose.style.cssText = 'right:-55px;top:-22px;';

			let confBtnBlock = document.createElement('div');
			confBtnBlock.setAttribute('class', 'confBtnBlock');

			let confBtnDec = document.createElement('span');
			confBtnDec.setAttribute('class', 'confBtnDec');
			confBtnDec.innerText = 'Cancel';
				$(confBtnDec).bind('click', function(){
				$('.profileAvatar').attr('src', os);
				$('.pfbAvatar').attr('src', os);
				$('#avatarMenu').attr('src', os);
				$('#shadow_box').remove();
				$(modalContainer).remove();
				$('#avatarIn').val(null);
			});
			confBtnBlock.append(confBtnDec);

			let confBtnAcc = document.createElement('span');
			confBtnAcc.setAttribute('class', 'confBtnAcc');
			confBtnAcc.innerText = 'Confirm';
			$(confBtnAcc).bind('click', function(){

        var fd = new FormData();
        var files = $('#avatarIn')[0].files;

        if(files.length > 0 ){
           fd.append('file',files[0]);
           $.ajax({
              url: "/index.php?route=submit_form&form=saveAvatar",
              type: 'post',
              data: fd,
              contentType: false,
              processData: false,
              success: function(data){
								$('#shadow_box').remove();
								$('#modalContainer').remove();
              },
           });
        }
			});
			confBtnBlock.append(confBtnAcc);
			modalContent.append(confBtnBlock);
		}
	}

	let modalDescBlock = document.createElement('div');
	modalDescBlock.setAttribute('id', 'modalDescBlock');
	modalContent.append(modalDescBlock);

	if(type == 'devGameView'){

		let modalOptionsBlock = document.createElement('div');
		modalOptionsBlock.setAttribute('class', 'modalOptionsBlock');
		modalContent.append(modalOptionsBlock);

		let moGeneral = document.createElement('span');
		moGeneral.setAttribute('id', 'general');
		moGeneral.setAttribute('class', 'modalOptionActive');
		moGeneral.innerText = 'general';
		modalOptionsBlock.append(moGeneral);

		let moStats = document.createElement('span');
		moStats.setAttribute('id', 'stats');
		moStats.setAttribute('class', 'modalOption');
		moStats.innerText = 'stats';
		$(moStats).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			gameModalContent(game_id, 'stats', this);
		});
		modalOptionsBlock.append(moStats);

		let moUpdate = document.createElement('span');
		moUpdate.setAttribute('id', 'update');
		moUpdate.setAttribute('class', 'modalOption');
		moUpdate.innerText = 'update';
		$(moUpdate).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			gameModalContent(game_id, 'update', this);
		});
		modalOptionsBlock.append(moUpdate);

		let moSell = document.createElement('span');
		moSell.setAttribute('id', 'sell');
		moSell.setAttribute('class', 'modalOption');
		moSell.innerText = 'sell';
		$(moSell).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			gameModalContent(game_id, 'sell', this);
		});
		modalOptionsBlock.append(moSell);

		let moDelete = document.createElement('span');
		moDelete.setAttribute('id', 'delete');
		moDelete.setAttribute('class', 'modalOption');
		moDelete.innerText = 'delete';
		$(moDelete).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			gameModalContent(game_id, 'delete', this);
		});
		modalOptionsBlock.append(moDelete);
	}else if(type == 'devGameView'){

	}

	let shadowBox = document.createElement('div');
	shadowBox.setAttribute('id', 'shadow_box');
	shadowBox.setAttribute('style', 'display:block');
	container.prepend(shadowBox);
	
	fetchGameContent(game_id, 'general');
}

function updateBio(){
	window.clearTimeout(timeoutHandle);
	timeoutHandle = window.setTimeout(function(){
		bioSave();
	}, 2000);
	console.log('done')
}

function bioSave(){
	let pfbBio = document.getElementById('pfbBio');
	let bioContent = pfbBio.innerText;
	$.ajax({
		type: "POST",
		url: "/index.php?route=submit_form&form=bioSave",
		data: {request: 'true', bioContent: bioContent},
		success: function(data) {
			if(data == 1){
				pfbBio.style.cssText = 'color:green';
				window.setTimeout(function(){
					pfbBio.style.cssText = '';
				}, 500);
			}else{
				console.log(data);
			}
		}
	});
}

function saveBannerColour(){
	let selectedColour = document.getElementById('selectedhexagon').style.backgroundColor;
	$.ajax({
		type: "POST",
		url: "/index.php?route=submit_form&form=saveBannerColour",
		data: {request: 'true', colour: selectedColour},
		success: function(data) {
			console.log(data);
			$('#shadow_box').remove();
			$(modalContainer).remove();
		}
	});
}

function changeAvatar(e){
	var input = e;
	var url = $(e).val();
	var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
	if (input.files && input.files[0]&& (ext == "png")){
		var reader = new FileReader();

		reader.onload = function (e) {
			os = $('.profileAvatar').attr('src');
			$('.profileAvatar').attr('src', e.target.result);
			$('.pfbAvatar').attr('src', e.target.result);
			$('#avatarMenu').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
		openModal('confirmAvChange');
	}else{
		if(document.getElementById('error_msg')){}else{
			let navHead = document.getElementsByClassName('navHead')[0];
			let errorMsg = document.createElement('div');
			errorMsg.setAttribute('id', 'error_msg');
			errorMsg.innerText = 'Error processing file.';
			navHead.append(errorMsg);

			setTimeout(function(){
				$(errorMsg).fadeOut();
			}, 1500);

			setTimeout(function(){
				$(errorMsg).remove();
			}, 2500);
		}
	}
}

function changeGameThumb(e){
	var input = e;
	var url = $(e).val();
	var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
	if (input.files && input.files[0]&& (ext == "png")){
		var reader = new FileReader();

		reader.onload = function (e) {
			$('.ngThumb').attr('src', e.target.result);
			$('.sbvThumb').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}else{
		if(document.getElementById('error_msg')){}else{
			let navHead = document.getElementsByClassName('navHead')[0];
			let errorMsg = document.createElement('div');
			errorMsg.setAttribute('id', 'error_msg');
			errorMsg.innerText = 'Error processing file.';
			navHead.append(errorMsg);

			setTimeout(function(){
				$(errorMsg).fadeOut();
			}, 1500);

			setTimeout(function(){
				$(errorMsg).remove();
			}, 2500);
		}
	}
}

function mouseOverColor(e){
	let selectedColour = document.getElementById('selectedhexagon');
	selectedColour.style.cssText = 'background-color:' + e;
}

function clickColor(e){
	let selectedColour = document.getElementById('selectedhexagon');
	selectedColour.style.cssText = 'background-color:'+e;
	defaultColor = e;
	let profileBanner = document.getElementsByClassName('profileBanner')[0];
	profileBanner.style.cssText = 'background-color:'+e;
}

function mouseOutMap(){
	let selectedColour = document.getElementById('selectedhexagon');
	selectedColour.style.cssText = 'background-color: ' + defaultColor;
}

function loadColourView(){
	let selectedColour = document.getElementById('selectedhexagon');
	selectedColour.style.cssText = defaultColor;
}

function fetchGameContent(game_id, type){
	$.ajax({
		type: "POST",
		url: "/index.php?route=profile",
		data: {request: 'true', action: 'gameAdmin', page: type, game_id: game_id},
		success: function(data) {
			let modalDescBlock = document.getElementById('modalDescBlock');
			if(modalDescBlock){
				modalDescBlock.innerHTML = data;
			}
		}
	});
}

function loginModal(a, b){
	if(a == 0){
		$('#loginContainer').remove();
		$('#shadow_box').remove();
	}else{
		$('#shadow_box').remove();
		$('#loginContainer').remove();
		let container = document.getElementById('container');

		let loginContainer = document.createElement('div');
		loginContainer.setAttribute('id', 'loginContainer');
		container.prepend(loginContainer);

		let loginBlock = document.createElement('div');
		loginBlock.setAttribute('class', 'loginBlock confirmBlock');
		loginContainer.append(loginBlock);

		let loginContent = document.createElement('div');
		loginContent.setAttribute('class', 'loginContent');
		loginBlock.append(loginContent);

		let loginHead = document.createElement('div');
		loginHead.setAttribute('class', 'loginHead');
		loginHead.innerText = 'Confirm';
		loginContent.append(loginHead);

		let iconClose = document.createElement('span');
		iconClose.setAttribute('class', 'icon_close');
		$(iconClose).bind('click', function(){
			$('#shadow_box').remove();
			$(loginContainer).remove();
		});
		loginContent.append(iconClose);

		let confMes = document.createElement('span');
		confMes.setAttribute('class', 'confMes');
		confMes.innerText = 'Are you sure you want to log out?';
		loginContent.append(confMes);

		let confBtnBlock = document.createElement('div');
		confBtnBlock.setAttribute('class', 'confBtnBlock');

		let confBtnDec = document.createElement('span');
		confBtnDec.setAttribute('class', 'confBtnDec');
		confBtnDec.innerText = 'Cancel';
		$(confBtnDec).bind('click', function(){
			loginModal(0, b);
		});
		confBtnBlock.append(confBtnDec);

		let confBtnAcc = document.createElement('span');
		confBtnAcc.setAttribute('class', 'confBtnAcc');
		confBtnAcc.innerText = 'Log out';
		$(confBtnAcc).bind('click', function(){
			logOut(2);
		});
		confBtnBlock.append(confBtnAcc);

		loginContent.append(confBtnBlock);

		let shadowBox = document.createElement('div');
		shadowBox.setAttribute('id', 'shadow_box');
		shadowBox.setAttribute('style', 'display:block');
		container.prepend(shadowBox);
	}
}

function rebind(e){
	$(e).unbind('click', arguments.callee);
	$(e).bind('click', function(){
		$(this).unbind('click', arguments.callee);
		avMenuPop('open', this);
	});
}

function loading(id){
	let navHead = document.getElementsByClassName('navHead')[0];
	let loadingDisplay = document.createElement('div');
	loadingDisplay.setAttribute('class', 'loading');
	loadingDisplay.setAttribute('id', id);
	navHead.append(loadingDisplay);

	for(var i = 1; i <= 11; i++){
		var span = document.createElement('span');
		span.innerText = '.';
		loadingDisplay.append(span);
	}
}

function openGame(id){
	container.innerHTML = '';
	loading('mainLoad');
	$.ajax({
		type: "POST",
		url: "/index.php?route=game&id=" + id,
		data: {request: 'true'},
		success: function(data) {
			history.pushState('game', 'game title', '?route=game&id=' + id);
			homeScreen = document.getElementById('homeScreen');
			setTimeout(function(){
				let loading = document.getElementsByClassName('loading')[0];
				$(loading).remove();
				container.innerHTML = data;
				gameIni();
			}, 300);
		}
	});
}

function slideClick(e){
	let parent = e.parentElement;
	let id = e.getAttribute("data");
	let sbvuSet = (document.getElementById('sbvu'))? 1 : 0;
		if((sbvuSet == 1 && $('#sbvu').is(":hover") == false && $('#sbvd').is(":hover") == false) || sbvuSet == 0){
			container.innerHTML = '';
			loading('mainLoad');
			$.ajax({
				type: "POST",
				url: "/index.php?route=game&id=" + id,
				data: {request: 'true'},
				success: function(data) {
					history.pushState('game', 'game title', '?route=game&id=' + id);
					homeScreen = document.getElementById('homeScreen');
					setTimeout(function(){
						let loading = document.getElementsByClassName('loading')[0];
						$(loading).remove();
						container.innerHTML = data;
						gameIni();
					}, 300);
				}
			});
		}
}

function shadowOptions(e){
	let id = e.getAttribute("data");
	var likeShow;
	$.ajax({
		type: "POST",
			url: "/index.php?route=auth&action=checkSession",
			data: {request: 'true'},
			success: function(data) {
				if(data == 1){
					$.ajax({
						type: "POST",
						url: "/index.php?route=submit_form&form=fetchGameLike",
						data: {request: 'true', game_id: id},
						success: function(data) {
							likeShow = (data == 0)? '<span id="sbvu" class="icon-thumbs-o-up sbv_up"></span><span id="sbvd" class="icon-thumbs-o-down sbv_down"></span>' : ((data == 1)? '<span id="sbvu" class="icon-thumbs-up sbv_up"></span><span id="sbvd" class="icon-thumbs-o-down sbv_down"></span>' : '<span id="sbvu" class="icon-thumbs-o-up sbv_up"></span><span id="sbvd" class="icon-thumbs-down sbv_down"></span>');

							if(e.getElementsByClassName('slider_mask')[0]){}else{
								var mask = document.createElement('div');
								mask.setAttribute("class", "slider_mask");
								mask.setAttribute("id", "slider_mask");
								mask.innerHTML = '<div class="slider_mask_o"></div>' + likeShow;
								e.appendChild(mask);

								setTimeout(function(){
									displayMask(mask, e);
								}, 450);

								mask.addEventListener("mouseout", function(){
									if($('#sbvu').is(":hover") == false && $('#sbvd').is(":hover") == false && $(mask).is(":hover") == false){  
										$(mask).remove();
									}
								});			

								mask.addEventListener("wheel", function(){  
									$(mask).remove();
								});	

								$('#sbvu').bind('click', function(){
									$(this).unbind('click', arguments.callee);
									let stat = (data == 0)? 'set' : 'unset';
									setFav(this, stat, id, 'like', e);
								});

								$('#sbvd').bind('click', function(){
									$(this).unbind('click', arguments.callee);
									let stat = (data == 0)? 'set' : 'unset';
									setFav(this, stat, id, 'dislike', e);
								});
							}
						}
					});
				}
			}
	});
		
	
}

function setFav(e, stat, id, type, sliderBlock){
	$.ajax({
		type: "POST",
		url: "/index.php?route=submit_form&form=preference&action=setFav",
		data: {type: type, id: id, stat: stat},
		success: function(data) {
			let newClass = (type == 'like')? ((e.getAttribute('class') == 'icon-thumbs-o-up sbv_up')? 'icon-thumbs-up sbv_up' : 'icon-thumbs-o-up sbv_up') : ((e.getAttribute('class') == 'icon-thumbs-o-down sbv_down')? 'icon-thumbs-down sbv_down' : 'icon-thumbs-o-down sbv_down');
			e.setAttribute('class', newClass);

			let unsetId = (type == 'like')? '#sbvd' : '#sbvu';
			let unsetClass = (type == 'like')? 'icon-thumbs-o-down sbv_down' : 'icon-thumbs-o-up sbv_up';
			$(unsetId).attr('class', unsetClass);
		}
	});
}

function displayMask(mask, sliderBlock){
	var sbw = Math.floor(parseInt(getComputedStyle(sliderBlock).width));
	if(sbw >= 325){
		mask.style.cssText = 'opacity:1';
	}else{
		setTimeout(function(){
			displayMask(mask, sliderBlock);
		}, 10);
	}
}

function scrollScreen(){
	var y = $(window).scrollTop();
	$('html, body').animate({ scrollTop: y + 150 })
}

function slide_menu(e, dir){
	let winX = window.innerWidth;
	let parent = e.parentElement;
	let gParent = parent.parentElement
	let gParentChildren = gParent.children;
	var sliderW = 0;
	var sliderBlockWidth = 0;
	for (var b = 0; b < gParentChildren.length; b++) {
		var attClass = gParentChildren[b].getAttribute('class');
		if(attClass == 'slider'){
			var sliderEl = gParentChildren[b];
			var sliderBlocks = sliderEl.getElementsByClassName('slider_block');

			for (var c = 0; c < sliderBlocks.length; c++) {
				var sliderBlock = sliderBlocks[c];
				sliderBlockWidth = parseInt(getComputedStyle(sliderBlock).width) + 4;
				sliderW = sliderW + sliderBlockWidth;
			}
			var sliderWidth = sliderW;
		}

		if(attClass == 'count'){
			var slideCounter = gParentChildren[b];
			var slideCount = slideCounter.getAttribute('data');
		}
	}

	var	marginLeftPop;
	var blockX = sliderBlockWidth;
	var maxScroll = Math.floor(winX / blockX);
	var slideSpeed = blockX * maxScroll;

	lastBlockCount = sliderBlocks.length - 1;
	lastBlock = sliderBlocks[lastBlockCount];
	var rect = lastBlock.getBoundingClientRect();	

	if(dir == 'left'){
		if(slideCount != 'n'){
			slideCount++;
		}

		nextScroll = parseInt(slideCount) * (slideSpeed + 3);
		if(slideCount == 'n'){
			slideCounter.setAttribute('data', 0);
			sliderEl.style.cssText = 'margin-left: 0px';
		}

		else if(nextScroll < sliderW){
			slideCounter.setAttribute('data', slideCount);
			marginLeftPop = nextScroll;
			sliderEl.style.cssText = 'margin-left: -' + marginLeftPop + 'px';
		}

		else if(nextScroll > sliderW){
			slideCounter.setAttribute('data', 'n');
			marginLeftPop = sliderW - blockX;
			if((((parseInt(slideCount) - 1) * slideSpeed) + blockX) != sliderW){
				slideCounter.setAttribute('data', 'n');
				sliderEl.style.cssText = 'margin-left: -' + marginLeftPop + 'px';
			}else{
				slideCounter.setAttribute('data', 0);
				sliderEl.style.cssText = 'margin-left: 0px';
			}
		}

		else{
			slideCounter.setAttribute('data', 0);
			sliderEl.style.cssText = 'margin-left: 0px';
		}
	}

	if(dir == 'right'){
		if(slideCount > 0){
			marginLeftPop = parseInt(slideCount) * slideSpeed;
			slideCount--;
			slideCounter.setAttribute("data", slideCount);
			sliderEl.style.cssText = 'margin-left: -' + (marginLeftPop - slideSpeed) + 'px';
		}else{
			slideCounter.setAttribute("data", 0);
		}
	}
}

function gameSettings(){
	let gameInfoColumn = document.getElementsByClassName('game_info_column')[0];
	if(document.getElementById('gsMenu')){
		$('#gsMenu').remove();
	}else{
		let gsMenu = document.createElement('div');
		gsMenu.setAttribute('id', 'gsMenu');

		let iconEnlarge = document.createElement('span');
		iconEnlarge.setAttribute('class', 'gsmOption icon-enlarge');
		$(iconEnlarge).bind('click', function(){
			fullScreen('game_column');
		});
		gsMenu.append(iconEnlarge);

		let iconCloseChat = document.createElement('span');
		iconCloseChat.setAttribute('id', 'icon-closeChat');
		iconCloseChat.setAttribute('class', 'gsmOption');
		$(iconCloseChat).bind('click', function(){
			$(this).unbind('click', arguments.callee);
			switchChat(this, 'hide');
		});
		gsMenu.append(iconCloseChat);

		let iconThumbsUp = document.createElement('span');
		iconThumbsUp.setAttribute('class', 'gsmOption icon-thumbs-up');
		gsMenu.append(iconThumbsUp);

		let iconThumbsDown = document.createElement('span');
		iconThumbsDown.setAttribute('class', 'gsmOption icon-thumbs-down');
		gsMenu.append(iconThumbsDown);
		
		gameInfoColumn.append(gsMenu);
	}
}

function fullScreen(e){
	var src = document.getElementsByClassName(e)[0];
	var navHead = document.getElementsByClassName('navHead')[0];
	var container = document.getElementById('container');
	src.style.cssText = 'width: 100%;height: 100%;left: 0;top: 0;z-index: 3;';
	navHead.style.cssText = 'z-index: 0';
	let decreaseMsg = document.createElement('div');
	decreaseMsg.setAttribute('class', 'decreaseMsg');
	decreaseMsg.innerText = 'Press escape or double click right corner area of the screen to reset the screen size';

	let decreaseBtn = document.createElement('div');
	decreaseBtn.setAttribute('id', 'decreaseBtn');
	$(decreaseBtn).bind('dblclick', function(){
		$(this).unbind('dblclick', arguments.callee);
		screenReset();
	});

	let dot = document.createElement('div');
	dot.setAttribute('class', 'dot');
	decreaseBtn.append(dot);

	let pulse = document.createElement('div');
	pulse.setAttribute('class', 'pulse');
	decreaseBtn.append(pulse);
	decreaseMsg.append(decreaseBtn);
	container.append(decreaseMsg);

	setTimeout(function(){
		$(decreaseMsg).fadeOut();
	}, 5000);

	setTimeout(function(){
		decreaseMsg.style.cssText = 'opacity:0';
	}, 6000);

	$(document).keyup(function(e) {
		if (e.key === "Escape") {
			screenReset();
		}
	});
}

function screenReset(){
	console.log('reset')
	var src = document.getElementsByClassName('game_column')[0];
	var navHead = document.getElementsByClassName('navHead')[0];
	src.style.cssText = '';
	navHead.style.cssText = '';
	iconCloseChat = document.getElementById('icon-closeChat');
	switchChat(iconCloseChat, 'show');
	$('.decreaseMsg').remove();
}

function switchChat(e, funct){
	let nxtFnct = (funct == 'hide')? 'show' : 'hide';
	$(e).bind('click', function(){
		$(this).unbind('click', arguments.callee);
		switchChat(this, nxtFnct);
	});
	let display = (funct == 'hide')? 'display:none' : '';
	let width = (funct == 'hide')? 'width: calc(100% - 60px)' : '';
	document.getElementById('chat_column').style.cssText = display;
	document.getElementsByClassName('comment_in')[0].style.cssText = display;
	document.getElementsByClassName('game_column')[0].style.cssText = width;
	document.getElementsByClassName('game_info_column')[0].style.cssText = width;
}

function login_popup(type){
	$('#shadow_box').remove();
	$('#loginContainer').remove();
	let container = document.getElementById('container');

	let loginContainer = document.createElement('div');
	loginContainer.setAttribute('id', 'loginContainer');
	container.prepend(loginContainer);

	let loginBlock = document.createElement('div');
	let lbClass = (type == 'register')? 'registerBlock' : 'loginBlock';
	loginBlock.setAttribute('class', lbClass);
	loginContainer.append(loginBlock);

	let loginContent = document.createElement('div');
	loginContent.setAttribute('class', 'loginContent');
	loginBlock.append(loginContent);

	let loginHead = document.createElement('div');
	loginHead.setAttribute('class', 'loginHead');
	loginHead.innerText = (type == 'login')? 'Sign in' : ((type == 'passRec')? 'Recover' : 'Sign up');
	loginContent.append(loginHead);
	
	let iconClose = document.createElement('span');
	iconClose.setAttribute('class', 'icon_close');
	$(iconClose).bind('click', function(){
		$('#shadow_box').remove();
		$(loginContainer).remove();
	});
	loginContent.append(iconClose);

	var focusId = 'username';
	if(type == 'register'){
		focusId = 'name';
		let loginInputName = '<input type="text" id="name" class="login_input" placeholder="name" value="">';
		$(loginContent).append(loginInputName);
	}

	let loginInputUsername = '<input type="text" id="username" class="login_input" placeholder="username" value="">';
	let loginInputPass = (type == 'passRec')? '<input type="text" id="recKey" class="login_input" placeholder="recovery key" value="">' : '<input type="password" id="password" class="login_input" placeholder="password" value="">';
	$(document).on('keypress',function(e) {
		if(e.which == 13) {
			if($('#password').is(':focus')){
				(type == 'login')? authSubmit('login') : ((type == 'passRec')? authSubmit('passRec') : authSubmit('register'));
			}
		}
	});
	
	$(loginContent).append(loginInputUsername);
	$(loginContent).append(loginInputPass);
	document.getElementById(focusId).focus();

	if(type != 'passRec'){
		let passPlain = document.createElement('span');
		let passId = (type == 'register')? 'passPlainReg' : 'passPlain';
		passPlain.setAttribute('class', 'icon-eye-plain');
		passPlain.setAttribute('id', passId);
		$(passPlain).bind('click', function(){
			if(this.getAttribute('class') == 'icon-eye-plain'){
				document.getElementById('password').setAttribute('type', 'text');
				this.setAttribute('class', 'icon-eye-blocked');
			}else{
				document.getElementById('password').setAttribute('type', 'password');
				this.setAttribute('class', 'icon-eye-plain');
			}
		});
		loginContent.append(passPlain);
	}

	let logBtn = document.createElement('span');
	logBtn.setAttribute('id', 'login');
	logBtn.setAttribute('class', 'btn_small_dark');
	logBtn.innerText = (type == 'login')? 'Sign in' : ((type == 'passRec')? 'Submit' : 'Sign up');
	$(logBtn).bind('click', function(){
		(type == 'login')? authSubmit('login') : ((type == 'passRec')? authSubmit('passRec') : authSubmit('register'));
	});
	loginContent.append(logBtn);
	
	let newMesBlock = document.createElement('div');
	newMesBlock.setAttribute('class', 'newMesBlock');

	let newMes = document.createElement('span');
	newMes.setAttribute('class', 'newMes');
	newMes.innerText = (type == 'login')? 'New to Gamtry?' : 'Already have an account?';
	newMesBlock.append(newMes);

	let newMesLink = document.createElement('span');
	newMesLink.setAttribute('class', 'newMesLink');
	newMesLink.innerText = (type == 'login')? 'Sign up for free' : 'Sign in now';
	$(newMesLink).bind('click', function(){
		(type == 'login')? login_popup('register') : login_popup('login');
	});
	newMesBlock.append(newMesLink);

	if(type == 'login'){
		let passRec = document.createElement('span');
		passRec.setAttribute('class', 'passRec');
		passRec.innerText = 'Forgot password?';
		$(passRec).bind('click', function(){
			login_popup('passRec');
		});
		newMesBlock.append(passRec);
	}

	loginContent.append(newMesBlock);

	let shadowBox = document.createElement('div');
	shadowBox.setAttribute('id', 'shadow_box');
	shadowBox.setAttribute('style', 'display:block');
	container.prepend(shadowBox);
}

function authSubmit(type){
	let name = document.getElementById('name');
	let uname = document.getElementById('username');
	let pass = (type == 'passRec')? document.getElementById('recKey') : document.getElementById('password');	
	let loginContainer = document.getElementById('loginContainer');
	let passLength = (type == 'passRec')? 70 : 7;
	if(pass.value.length > passLength && uname.value.length > 3){
		uname.style.cssText = 'border-bottom: 1px solid green;';
		pass.style.cssText = 'border-bottom: 1px solid green;';
		let username = uname.value;
		let pw = pass.value;

		if(type == 'register' && name.value < 4){
			console.log(type);
			name.style.cssText = 'border-bottom: 1px solid red;';
			return;
		}

		let nameIn = (type == 'register')? name.value : '';

		$.ajax({
			type: "POST",
			url: "/index.php?route=auth&action=authSubmit",
			data: {type: type, name: nameIn, username: username, pass: pw},
			success: function(data) {
				console.log(data);
				if(type == 'passRec' && data == 0){
					loginFail(name, pass);
				}

				if(type == 'passRec' && data == 1){
					recSuccess(username, pw);
				}

				if(type == 'login' && data == 0){
					console.log(data);
					loginFail(name, pass);
				}

				else if(type == 'login' && data == 1){
					console.log(data);
					loginSuccess();					
				}

				else if(type == 'register' && data == 0){
					let navHead = document.getElementsByClassName('navHead')[0];
					let passCss = (pass.value.length < 8)? 'border: 1px solid red;' : 'border: 1px solid green;';
					uname.style.cssText = 'border: 1px solid red;';
					pass.style.cssText = passCss;
					if(document.getElementById('error_msg')){}else{
						let errorMsg = document.createElement('div');
						errorMsg.setAttribute('id', 'error_msg');
						errorMsg.innerText = 'Handle already exists';
						navHead.append(errorMsg);

						setTimeout(function(){
							$(errorMsg).fadeOut();
						}, 1500);

						setTimeout(function(){
							$(errorMsg).remove();
						}, 2500);
					}
				}

				else if(type == 'register' && data == 1){
					downloadKey('reg');
				}
			}
		});
	}else{
		let passCss = (pass.value.length < 8)? 'border-bottom: 1px solid red;' : 'border-bottom: 1px solid green;';
		let unameCss = (uname.value.length < 4)? 'border-bottom: 1px solid red;' : 'border-bottom: 1px solid green;';
		pass.style.cssText = passCss;
		uname.style.cssText = unameCss;
	}
}

function downloadKey(type){
	$.ajax({
		type: "POST",
		url: "/index.php?route=auth&action=downloadKey",
		data: {},
		success: function(data) {
			let loginContent = document.getElementsByClassName('loginContent')[0];
			let pkWrapper = document.createElement('div');
			pkWrapper.setAttribute('class', 'pkey_wrapper');

			let pkHeader = document.createElement('div');
			pkHeader.setAttribute('class', 'pkey_header');
			pkHeader.innerText = 'Copy password recovery key and store it somewhere safe. You will only be shown this once.';
			pkWrapper.append(pkHeader);

			let p = document.createElement('p');
			let textArea = document.createElement('textarea');
			textArea.setAttribute('class', 'pwr');
			textArea.setAttribute('value', data);
			textArea.innerHTML = data;
			$(textArea).bind('click', function(){
				copyToClipboard(textArea, type);
			});
			p.append(textArea);
			pkWrapper.append(p);
							
			let pkCopy = document.createElement('div');
			pkCopy.setAttribute('class', 'pkey_copy');
			pkCopy.innerText = 'copy to clipboard';
			$(pkCopy).bind('click', function(){
				copyToClipboard(textArea, type);
			});
			pkWrapper.append(pkCopy);
			loginContent.innerHTML = '';
			loginContent.append(pkWrapper);
		}
	});
}

function countDown(s){
	var x = setInterval(function() {
	s--;
	document.getElementById('countdown').innerHTML = s;

	if (s == 0) {
		clearInterval(x);
	}
}, 1000);
}

function copyToClipboard(textArea, type){
	let navHead = document.getElementsByClassName('navHead')[0];
	textArea.select();
	textArea.setSelectionRange(0, 99999);
	document.execCommand("copy");
	document.getElementsByClassName('pkey_copy')[0].innerHTML = 'closing in <span id="countdown">3</span> seconds';
	let errorMsg = document.createElement('div');
	errorMsg.setAttribute('id', 'error_msg');
	errorMsg.innerText = 'Copied to clipboard';
	navHead.append(errorMsg);
	countDown(3);
	setTimeout(function(){
		$(errorMsg).fadeOut();
	}, 1500);

	setTimeout(function(){
		$(errorMsg).remove();
	}, 2500);

	setTimeout(function(){
		if(type == 'passC'){
			login_popup('login');
		}else{
			loginSuccess();
		}
	}, 3000);
}

function loginFail(name, pass){
	let loginContainer = document.getElementById('loginContainer');
	loading('mainLoad');
	loginContainer.style.cssText = 'display:none';
	loginTimer = (loginTimer < 10000)? loginTimer * 2 : 10000;
	loginTimer = (loginTimer < 10000)? loginTimer : 10000;
	localStorage.setItem("loginTimer", loginTimer);

	setTimeout(function(){
		let loading = document.getElementsByClassName('loading')[0];
		$(loading).remove();
		loginContainer.style.cssText = '';
	}, loginTimer);

	if(name && pass){
		name.style.cssText = 'border-bottom: 1px solid red;';
		pass.style.cssText = 'border-bottom: 1px solid red;';
	}
}

function recSuccess(name, key){
	let loginBlock = document.getElementsByClassName('loginBlock')[0];

	let loginContent = document.createElement('div');
	loginContent.setAttribute('class', 'loginContent');
	loginBlock.append(loginContent);

	let loginHead = document.createElement('div');
	loginHead.setAttribute('class', 'loginHead');
	loginHead.innerText = 'New password';
	loginContent.append(loginHead);
	
	let iconClose = document.createElement('span');
	iconClose.setAttribute('class', 'icon_close');
	$(iconClose).bind('click', function(){
		$('#shadow_box').remove();
		$(loginContainer).remove();
	});
	loginContent.append(iconClose);

	let loginInputName = document.createElement('div');
	loginInputName.setAttribute('class', 'login_input');
	loginInputName.innerText = name;
	$(loginContent).append(loginInputName);

	let loginInputPass = document.createElement('input');
	loginInputPass.setAttribute('type', 'password');
	loginInputPass.setAttribute('id', 'password');
	loginInputPass.setAttribute('class', 'login_input');
	loginInputPass.setAttribute('placeholder', 'new password');
	loginInputPass.setAttribute('value', '');
	$(loginContent).append(loginInputPass);

	let passPlain = document.createElement('span');
	passPlain.setAttribute('class', 'icon-eye-plain');
	passPlain.setAttribute('id', 'pass_plain');
	$(passPlain).bind('click', function(){
		if(this.getAttribute('class') == 'icon-eye-plain'){
			document.getElementById('password').setAttribute('type', 'text');
			this.setAttribute('class', 'icon-eye-blocked');
		}else{
			document.getElementById('password').setAttribute('type', 'password');
			this.setAttribute('class', 'icon-eye-plain');
		}
	});
	loginContent.append(passPlain);
	
	let logBtn = document.createElement('span');
	logBtn.setAttribute('id', 'login');
	logBtn.setAttribute('class', 'btn_small_dark');
	logBtn.innerText = 'Change password';
	$(logBtn).bind('click', function(){
		let newPass = document.getElementById('password').value;
		changePassword(name, key, newPass);
	});
	loginContent.append(logBtn);

	let newMesBlock = document.createElement('div');
	newMesBlock.setAttribute('class', 'newMesBlock');

	let newMes = document.createElement('span');
	newMes.setAttribute('class', 'newMes');
	newMes.innerText = 'Already have an account?';
	newMesBlock.append(newMes);

	let newMesLink = document.createElement('span');
	newMesLink.setAttribute('class', 'newMesLink');
	newMesLink.innerText = 'Sign in now';
	$(newMesLink).bind('click', function(){
		login_popup('login');
	});
	newMesBlock.append(newMesLink);
	loginContent.append(newMesBlock);

	loginBlock.innerHTML = '';
	loginBlock.append(loginContent);
}

function changePassword(name, key, newPass){
	let loginContainer = document.getElementById('loginContainer');
	$.ajax({
		type: "POST",
		url: "/index.php?route=auth&action=changePassword",
		data: {username: name, key: key, pass: newPass},
		success: function(data) {
			let navHead = document.getElementsByClassName('navHead')[0];
			let errorMsg = document.createElement('div');
			errorMsg.setAttribute('id', 'error_msg');
			errorMsg.innerText = 'Password changed';
			navHead.append(errorMsg);
			setTimeout(function(){
				$(errorMsg).fadeOut();
			}, 1500);

			setTimeout(function(){
				$(errorMsg).remove();
			}, 2500);
			downloadKey('passC');
		}
	});
}

function loginSuccess(){
	localStorage.setItem("loginTimer", 2000);
	location.reload();
}

function openMessageViewer(e, type, cid=0){
	let subparent = e.parentElement;
	let parent = subparent.parentElement;
	let id = (cid == 0)? parent.getAttribute('data-id') : cid;
	let gameId = document.getElementById('game_id').value;
	let openForm = (type == 'respond')? 'openResponder' : 'openView';
	let elClass = (type == 'respond')? '' : 'vmView';
	$.ajax({
		type: "POST",
		url: "/index.php?route=submit_form&form=" + openForm,
		data: {request: 'true', game_id: gameId, comment_id: id},
		success: function(data) {
			removeElements();
			let container = document.getElementById('container');
			let divShadowBox = document.createElement('div');
			divShadowBox.setAttribute('id', 'shadow_box');
			let messageViewer = document.createElement('div');
			messageViewer.setAttribute('id', 'message_viewer');
			messageViewer.setAttribute('class', elClass);
			messageViewer.setAttribute('data-game-id', gameId);
			messageViewer.setAttribute('data-comment-id', id);
			messageViewer.innerHTML = data;
			container.prepend(messageViewer);
			container.prepend(divShadowBox);
			$(e).bind('click', function(){
				$(this).unbind('click', arguments.callee);
				openMessageViewer(this, type);
			});
		}
	});
}

function sendComment(e){
	let container = document.getElementById('container');
	let chatColumn = document.getElementById('chat_column');
	let game_id = document.getElementById('game_id');
	let messageArea = document.getElementById('comment_text');
	if(chatColumn && messageArea && game_id){
		let gameId = game_id.value;
		let message = messageArea.value;
		let messageStripped = message.replace(/\s/g, '');
		if(messageStripped.length < 5){
			$(e).bind('click', function(){
				$(this).unbind('click', arguments.callee);
				sendComment(this);
			});
			if(document.getElementById('error_msg')){}else{
				let navHead = document.getElementsByClassName('navHead')[0];
				let errorMsg = document.createElement('div');
				errorMsg.setAttribute('id', 'error_msg');
				errorMsg.innerText = 'Minimum 5 characters not including spaces';
				navHead.append(errorMsg);
				messageArea.style.cssText = 'border:2px solid red; width:395px; height:61px;';

				setTimeout(function(){
					$(errorMsg).fadeOut();
				}, 1500);

				setTimeout(function(){
					$(errorMsg).remove();
				}, 2500);
			}
		}else{
			messageArea.style.cssText = 'border:2px solid green; width:395px; height:61px;';
			e.style.cssText = 'background-color:green;';
			e.innerText = 'sending';
			e.removeAttribute('onclick');
			$.ajax({
				type: "POST",
				url: "/index.php?route=submit_form&form=newComment",
				data: {request: 'true', game_id: gameId, message: message},
				success: function(data) {
					if(data != 'error'){
						console.log(data)
						messageArea.value = '';
						e.innerText = 'sent';
						let newCommentWrapper = document.createElement('div');
						newCommentWrapper.setAttribute('class', 'comment_block');
						newCommentWrapper.innerHTML = data;
						chatColumn.prepend(newCommentWrapper);
						setTimeout(function(){
							messageArea.style.cssText = 'border-bottom: 1px solid #3b3939;';
							e.innerText = 'send';
							e.style.cssText = 'background-color:#c80707;';
							$(e).bind('click', function(){
								$(this).unbind('click', arguments.callee);
								sendComment(this);
							});
						}, 750);
					}else{
						if(document.getElementsByClassName('comment_in')[0]){
							let loginContainer = document.createElement('div');
							loginContainer.setAttribute('class', 'login_container');

							let chatSignIn = document.createElement('span');
							chatSignIn.setAttribute('class', 'click');
							chatSignIn.setAttribute('id', 'chatSignIn');
							chatSignIn.innerText = 'Sign in';
							$(chatSignIn).bind('click', function(){
								login_popup('login');
							});
							loginContainer.append(chatSignIn);

							var span = document.createElement('span');
							span.innerHTML = '&nbsp;or&nbsp;';
							loginContainer.append(span);

							let chatReg = document.createElement('span');
							chatReg.setAttribute('class', 'click');
							chatReg.setAttribute('id', 'chatRegister');
							chatReg.innerText = 'register';
							$(chatReg).bind('click', function(){
								login_popup('register');
							});
							loginContainer.append(chatReg);

							var span = document.createElement('span');
							span.innerHTML = '&nbsp;to chat';
							loginContainer.append(span);

							$('#comment_text').remove();
							$('#new_comment_submit').remove();
							document.getElementsByClassName('comment_in')[0].innerHTML = '';
							document.getElementsByClassName('comment_in')[0].append(loginContainer);
						}else{
							console.log('function not recognised');
						}
					}
				}
			});
		}
	}else{
		console.log('function not recognised');
	}
}

function getLineHeight(el) {
    var temp = document.createElement(el.nodeName), ret;
    temp.setAttribute("style", "margin:0; padding:0; "
        + "font-family:" + (el.style.fontFamily || "inherit") + "; "
        + "font-size:" + (el.style.fontSize || "inherit"));
    temp.innerHTML = "A";

    el.parentNode.appendChild(temp);
    ret = temp.clientHeight;
    temp.parentNode.removeChild(temp);

    return ret;
}

function sendResponse(e){
	let container = document.getElementById('container');
	let shadowBox = document.getElementById('shadow_box');
	let messageViewer = document.getElementById('message_viewer');
	let commentId = messageViewer.getAttribute('data-comment-id');
	let gameId = messageViewer.getAttribute('data-game-id');
	let messageArea = document.getElementById('message_responder');
	let message = messageArea.value;
	let messageStripped = message.replace(/\s/g, '');
	if(messageStripped.length < 5){
		if(document.getElementById('error_msg')){}else{
			let navHead = document.getElementsByClassName('navHead')[0];
			let errorMsg = document.createElement('div');
			errorMsg.setAttribute('id', 'error_msg');
			errorMsg.innerText = 'Minimum 5 characters not including spaces';
			navHead.append(errorMsg);
			messageArea.style.cssText = 'border-bottom:1px solid red; width:calc(100% - 24px); height:148px;';

			setTimeout(function(){
				$(errorMsg).fadeOut();
			}, 1500);

			setTimeout(function(){
				$(errorMsg).remove();
			}, 2500);
		}
	}else{
		messageArea.style.cssText = 'border-bottom:1px solid green; width:calc(100% - 24px); height:148px;';
		e.style.cssText = 'background-color:green;';
		e.innerText = 'sending';
		e.removeAttribute('onclick');
		$.ajax({
			type: "POST",
			url: "/index.php?route=submit_form&form=commentRespond",
			data: {request: 'true', game_id: gameId, parent_id: commentId, message: message},
			success: function(data) {
				messageArea.value = data;
				console.log(data);
				e.innerText = 'sent';
				setTimeout(function(){
					$(shadowBox).fadeOut();
					$(messageViewer).fadeOut();
				}, 500);
				setTimeout(function(){
					$(shadowBox).remove();
					$(messageViewer).remove();
				}, 1500);
			}
		});
	}
}

function closeMessageViewer(){
	$('#message_viewer').remove();
	$('#shadow_box').remove();
}

function commentLike(e, gid=0, cid=0){
	let subparent = e.parentElement;
	let parent = subparent.parentElement;
	let id = (cid == 0)? parent.getAttribute('data-id') : cid;
	let gameId = (gid == 0)? document.getElementById('game_id').value : gid;
	$.ajax({
		type: "POST",
		url: "/index.php?route=submit_form&form=likeComment",
		data: {request: 'true', game_id: gameId, id: id},
		success: function(data) {
			e.setAttribute('class', 'icon-chat-liked icon-thumbs-up');
		}
	});
}

function removeLike(e, gid=0, cid=0){
	let subparent = e.parentElement;
	let parent = subparent.parentElement;
	let id = (cid == 0)? parent.getAttribute('data-id') : cid;
	let gameId = (gid == 0)? document.getElementById('game_id').value : gid;
	$.ajax({
		type: "POST",
		url: "/index.php?route=submit_form&form=likeCommentRemove",
		data: {request: 'true', game_id: gameId, id: id},
		success: function(data) {
			e.setAttribute('class', 'icon-chat-liked icon-thumbs-o-up');
		}
	});
}

function commentMenu(e){
	$(e).bind('click', function(){
		$(this).unbind('click', arguments.callee);
		RemoveCommentMenu(this);
	});
	let commMenu = document.getElementsByClassName('comment_menu');
	if(commMenu[0]){
		let cMPar = commMenu[0].parentElement;
		let cMParChildren = cMPar.children;
		
		for (var b = 0; b < cMParChildren.length; b++){
			var attClass = cMParChildren[b].getAttribute('class');
			if(attClass == 'comment_ell_menu icon-ellipsis-h'){
				$(cMParChildren[b]).unbind('click', arguments.callee);
				$(cMParChildren[b]).bind('click', function(){
					$(this).unbind('click', arguments.callee);
					commentMenu(this);
				});
			}
		}
	}
	$(commMenu[0]).remove();
	let parent = e.parentElement;
	let id = parent.getAttribute('data-id');
	let divMenu = document.createElement('div');
	divMenu.setAttribute('class', 'comment_menu');
	let span = document.createElement('span');
	span.setAttribute('class', 'comment_menu_opt');
	$(span).bind('click', function(){
		$(this).unbind('click', arguments.callee);
		removeComment(id, parent);
	});
	span.innerText = 'hide';
	divMenu.append(span);
	parent.prepend(divMenu);
}

function removeComment(id, el){
	let gameId = document.getElementById('game_id').value;
	$.ajax({
		type: "POST",
		url: "/index.php?route=submit_form&form=removeComment",
		data: {request: 'true', game_id: gameId, id: id},
		success: function(data) {
			$(el).remove();
		}
	});
}

function RemoveCommentMenu(e){
	$(e).bind('click', function(){
		$(this).unbind('click', arguments.callee);
		commentMenu(this);
	});
	let commMenu = document.getElementsByClassName('comment_menu');
	if(commMenu[0]){
		let cMPar = commMenu[0].parentElement;
		let cMParChildren = cMPar.children;
		
		for (var b = 0; b < cMParChildren.length; b++){
			var attClass = cMParChildren[b].getAttribute('class');
			if(attClass == 'comment_ell_menu icon-ellipsis-h'){
				$(cMParChildren[b]).unbind('click', arguments.callee);
				$(cMParChildren[b]).bind('click', function(){
					$(this).unbind('click', arguments.callee);
					commentMenu(this);
				});
			}
		}
	}
	$(commMenu[0]).remove();
}
