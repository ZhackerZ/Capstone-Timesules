/****
 * Timesules main javascript
 *
 * Tyler Hadidon 2012
 */
 var Timesules = {};
 var Tc = Timesules;

 var VIEW_CAPSULE = 0;
 var VIEW_GROUP_CAPSULE = 1;

// Global settings
$(document).ready(function() {
	$.ajaxSetup({
		cache:false,
		data:{ajaxCall:true},
		timeout:5000,
		datatype:"html"
	});

	$(".sideContent").jScrollPane().mouseover(function() {
		$(".jspTrack,.jspDrag", this).show();
	}).mouseout(function() {
		$(".jspTrack,.jspDrag", this).hide();
	});
	$(".sideContentWrapper.hidden").hide();

	$(".sidebarHeader").click(function() {
		Timesules.sideBar.toggle($(this));
	});

	$(".fade").delay(5000).fadeOut(2500);

	$("#account-open img").click(function() {
		var img = $(this);
		var dropdown = $("#account-dropdown");
		if(img.hasClass("opened")) {
			img.removeClass("opened");
			dropdown.slideUp(200);
		} else {
			img.addClass("opened");
			dropdown.slideDown(200);
		}
	});

	Timesules.dialog = $("#page-dialog");
	Timesules.dialog.open = function(options) {
		if(options) {
			if(options.html) {
				Timesules.dialog.html(options.html);
				options.html = null;
			}
			Timesules.dialog.dialog("option", options);
		}
		Timesules.dialog.dialog("open");
	};
	Timesules.dialog.close = function() { Timesules.dialog.dialog("close"); };
	Timesules.dialog.option = function(props) { Timesules.dialog.dialog("option", props); };
	Timesules.dialog.dialog({
		autoOpen:false,
		draggable:false,
		modal:true,
		resizable:false
	});
	Timesules.d = Timesules.dialog;
});

// Side-Bar handler
Timesules.sideBar = {
	isOpened:function(obj) { return !obj.hasClass("hidden"); },
	open:function(obj) {
		if(obj.hasClass("hidden")) {
			obj.removeClass("hidden");
			$(".sideContentWrapper", obj.parent()).slideDown(200);
		}
	},
	close:function(obj) {
		if(!obj.hasClass("hidden")) {
			obj.addClass("hidden");
			$(".sideContentWrapper", obj.parent()).slideUp(200);
		}
	},
	toggle:function(obj) {
		if(obj.hasClass("hidden")) {
			obj.removeClass("hidden");
			$(".sideContentWrapper", obj.parent()).slideDown(200);
		} else {
			obj.addClass("hidden");
			$(".sideContentWrapper", obj.parent()).slideUp(200);
		}
	}
};

Timesules.lastViewedPrompt = null;
Timesules.viewPrompt = function(_id, _type) {
	if(!Timesules.dialog.dialog("isOpen"))
		Timesules.dialog.open({title:"Loading",html:"Loading... Please wait!"});
	$.ajax({
		data:{"view":true,"id":_id,"type":_type},
		url:"/load.php",
		type:"POST",
		datatype:"json",
		success:function(result) {
			Timesules.lastViewedPrompt = result;
			//result.code = 200;
			var title = "Error!";
			var HTML;
			//var HTML = "times testing";
			//var HTML = "An error occurred while loading the capsule. Please try again.";
			
			if(result.code == 200) {
				HTML = '<div id="view-capsule-info"><div>Author: '+result.author+'</div><div>Locked: '+result.ldate+'</div><div>Released: '+result.rdate+'</div></div>';
				HTML += '<div id="view-capsule-prompt">'+result.msg;
				if(result.attachments != "") {
					HTML += "<hr />";
					result.attachments = result.attachments.split(";");
					for(var i in result.attachments) {
						var icon = "file";
						var m = result.attachments[i].split("|");
						var type = m[0].substring(m[0].lastIndexOf(".")+1);
						switch(type) {
							case"png":case"jpg":case"jpeg":case"pjpg":case"pjpeg":icon="image";break;
							case"doc":case"docx":icon="doc";break;
							case"pdf":icon="pdf";break;
							default:icon="file";break;
						}
						var link = '<img src="/source/templates/images/'+icon+'.png" style="width:16px;height:16px;border:0px;margin-right:5px" />'+m[1];
						if(icon=="image") {
							link = '<img src="/thumbnail.php?f=uploads/'+m[0]+'&s=128" style="border:0px;" />';
						}
						HTML += '<span><a href="/uploads/'+m[0]+'" target="_blank" title="'+m[1]+'">'+link+'</a></span><br />';
					}
				}
				HTML += '</div>';
				if(result.comments){
					for(var i in result.comments) {
						var comment = result.comments[i];
						HTML += '<div class="view-capsule-comment"><div>User: '+comment.name+' Date: '+comment.date+'</div><div class="sep"></div><div>'+comment.msg+'</div></div>';
					}
					HTML += '<div>Comment</div><div id="commentError"></div><textarea class="view-capsule-textarea" id="leaveAComment"></textarea><br /><input type="button" id="sendComment" class="submit-button" value="ADD A COMMENT" />';
				}
				//title = result.prompt;
			} 
			else if(result.code == 404) {
				title = "Not Found";
				HTML = "The capsule you requested was not found.";
			} 
			else if(result.code == 401) {
				title = "Not Authorized";
				HTML = "You are not authorized to view that capsule.";
			}
			Timesules.dialog.open({title:title,html:HTML,position:["center",100]});
			$("#sendComment").click(function() { Timesules.sendComment(); });
		},
		error:function(xhr, status, error){
			Timesules.dialog.open({title:"Error!",html:"An error occurred while loading the capsule. Please try again."});
		}
	});
};

Timesules.sendComment = function() {
	var comment = $("#leaveAComment").val();
	if(Timesules.lastViewedPrompt == null || comment == "")
		return;
	$("#commentError").html('<img src="/source/templates/images/loading.gif" style="width:20px;height:20px;" /> Sending...');
	$("#commentError").removeClass("error");
	$.ajax({
		data:{addComment:true,pid:Timesules.lastViewedPrompt.id,gid:Timesules.lastViewedPrompt.gid,msg:comment},
		url:"/load.php",
		type:"POST",
		datatype:"json",
		success:function(result) {
			if(result.code == 200) {
				Timesules.viewPrompt(Timesules.lastViewedPrompt.id,Timesules.lastViewedPrompt.type);
				$("#commentError").html('<img src="/source/templates/images/loading.gif" style="width:20px;height:20px;" /> Sent! Reloading...');
				$("#commentError").removeClass("error");
			} else {
				var msg = (result.code==404)?"The capsule requested could not be found.":((result.code==401)?"You are not authorized to add a comment to this capsule.":"An error occurred while adding a comment.");
				$("#commentError").html("Oops! "+msg+" Please try again.");
				$("#commentError").addClass("error");
			}
		},
		error:function(xhr, status, error) {
			$("#commentError").html("Oops! An error occurred while adding a comment. Please try again.");
			$("#commentError").addClass("error");
		}
	});
};

/**********************************************************/
/***************** Notifications **************************/
/**********************************************************/
Timesules.notifications = function() {
	$(document).ready(function() {
		$("[id^='requestID']").click(function() {
			Tc.d.open({html:"Processing request...",title:"Please wait..."});
			var res = (this.id.search("accept")!=-1)?"accept":"deny";
			var id = this.id.split("-")[2];
			var noteId = this.id.split("-")[3];
			var me = $(this);

			console.dir(res);
			console.dir(id);
			console.dir(noteId);
			console.dir(me);

			$.ajax({
				data:{"respond":res,"id":id,"notification_id":noteId},
				type:"POST",
				url:"/contacts.php",
				dataType:"json",
				success:function(result) {
					if(result.code == 200) {
						Tc.d.html(result.msg);
						Tc.d.option({title:result.title});
						me.parent().parent().remove();
					} else {
						Tc.d.html(result.msg);
						Tc.d.option({title:"Error: "+result.code});
					}
				},
				error:function(xhr, status, error) { Tc.d.html("There was an error processing your request. Please try again."); Tc.d.option({title:"Error!"}); }
			});
		});
	});
};

/**********************************************************/
/***************** User Home ******************************/
/**********************************************************/
Timesules.user_home = {
	loading:false,
	loadingFailed:false,
	loaded:0,
	loadMoreCapsules1:function() {
		if(Timesules.user_home.loaded == -1)
			return;

		Timesules.user_home.loading = true;
		$("#loadingCapsulesText").html("Loading More Capsules...").css({cursor:"default"});
		$("#loadingCapsulesImg").css({backgroundImage:'url("/source/templates/images/loading.gif")',width:32,height:32});
		$("#loadingCapsules").show();
		$.ajax({
			data:{loadCapsules:Timesules.user_home.loaded+1},
			url:"/index.php",
			success:function(result) {
				Timesules.user_home.loadingFailed = false;
				if(result) {
					var match = result.match(/capsuleBlock/g);
					if(match && match.length) {
						Timesules.user_home.loaded += match.length;
						Timesules.user_home.loading = false;
						$("#newestReleasedTimesules").append(result);
						$("#loadingMoreCapsules").hide();
					} else {
						$("#loadingCapsules").html("There are no more capsules to load.");
					}
				} else {
					$("#loadingCapsules").html("There are no more capsules to load.");
				}
				$("[id^='view-cap-']").unbind("click").click(function(e) {
					e.preventDefault();
					Timesules.viewPrompt(this.id.replace("view-cap-", ""), VIEW_CAPSULE);
				});
			},
			error:function(){
				Timesules.user_home.loading = false;
				Timesules.user_home.loadingFailed = true;
				$("#loadingCapsulesText").html("There was an error fetching more capsules. Try again?").css({cursor:"pointer"});
				$("#loadingCapsulesImg").css({backgroundImage:'url("/source/templates/images/refresh-button.png")',width:28,height:32});
			}
		});
},
newPosts:function(count) { $("#newPosts").html(count+" NEW!"); },
init:function(loadedCount) {
	Timesules.user_home.loaded = loadedCount;
	$(document).scroll(function(e) {
		var fromBottom = $(document).height()-($(document).scrollTop()+$(window).height());
		if(fromBottom<200 && !Timesules.user_home.loading && !Timesules.user_home.loadingFailed) {
			Timesules.user_home.loadMoreCapsules();
		}
	});
	$(document).ready(function() {
		$("#loadingCapsules").click(function() {
			if(!Timesules.user_home.loading) Timesules.user_home.loadMoreCapsules();
		});
		$("[id^='view-cap-']").click(function() {
			Timesules.viewPrompt(this.id.replace("view-cap-", ""), VIEW_CAPSULE);
		});
	});
}
};

/*******************************************************/
/***************** Prompt ******************************/
/*******************************************************/
Timesules.prompt = {
	contacts:[],
	init:function() {
		$(document).ready(function() {
			$(".rsContact").draggable({
				addClasses:false,
				helper:"clone",
				appendTo:"body",
				containment:"document",
				scope:"promptDrag",
				create:function() {
					var me = $(this);
					me.data({
						src:this.src,
						id:me.parent().attr("id").replace("rsContactID-",""),
						name:$("a", me.parent()).html(),
						content:me.parent().html(),
						dropped:false
					});
				}
			});
			var current = $("[id^='promptCT']").draggable({
				revert:true,
				scope:"promptDrag",
				stop:function() {
					if($(this).data("remove")) {
						$(this).remove();
						Tc.prompt.removeContact(this.id.replace("promptCT-",""));
					}
				}
			})
			.data({dropped:true,remove:false});
			$.each(current, function(key,val) {
				var me = $(val);
				var id = me.attr("id").replace("promptCT-","");
				if(Tc.prompt.contacts.indexOf(id) != -1) return;
				Tc.prompt.addContact(id, true); // true prevents it from erasing the container!
			});

			$("#contactsDrop").droppable({
				scope:"promptDrag",
				tolerance:"pointer",
				drop:function(event, ui) {
					var me = $(this);

					var draggable = ui.draggable;
					var id = draggable.data("id");

					// Check and see if the contact already exists. If it does, skip
					if(Tc.prompt.contacts.indexOf(id) != -1 || draggable.data("dropped")) {
						return;
					}

					// Create the new input
					var input = $("<span/>");
					input
					.draggable({
						revert:true,
						scope:"promptDrag",
						stop:function() {
							if(input.data("remove")) {
								input.remove();
								Tc.prompt.removeContact(id);
							}
						}
					})
					.data({dropped:true,remove:false})
					.attr("id","promptCT-"+id)
					.html('<img src="'+draggable.data("src")+'" class="avatar32" /> '+draggable.data("name"));

					// Otherwise, add the ID to the contacts array and append it to the droppable
					Tc.prompt.addContact(id);
					me.append(input);
				},
				out:function(event, ui) {
					if(!ui.draggable.data("dropped")) return;
					ui.draggable.draggable("option", "revert", false).data("remove", true);
				}
			});
});
},
addContact:function(id, keep) {
		// If we are adding the first contact, remove the placeholder text
		if(Tc.prompt.contacts.length == 0 && !keep) {
			$("#contactsDrop").html("").css("color","#000");
		}

		Tc.prompt.contacts.push(id);
		$("#contactsDropField").val(Tc.prompt.contacts.join());
	},
	removeContact:function(id) {
		if(Tc.prompt.contacts.indexOf(id) == -1) return;
		Tc.prompt.contacts.splice(Tc.prompt.contacts.indexOf(id),1);
		$("#contactsDropField").val(Tc.prompt.contacts.join());

		// Reset it back to the begining if there are no contacts left
		if(Tc.prompt.contacts.length == 0) {
			$("#contactsDrop").html("Drag and drop contacts here...").css("color","#CCC");
		}
	}
};

/*********************************************************/
/***************** Time Cap ******************************/
/*********************************************************/
Timesules.timecap = {
	mode:-1,
	selected:[],
	init:function(_mode) {
		Tc.timecap.mode = _mode;
		$(document).ready(function() {
			$("[id^='view-cap-']").click(function(e) {
				Timesules.viewPrompt(this.id.replace("view-cap-", ""), VIEW_CAPSULE);
				e.preventDefault();
				return false;
			});
			$(".timecapLink").click(function (e) { e.stopPropagation(); });
			$(".timecapBlock").click(function (e) {
				var me = $(this);
				var id = me.attr("id").replace("capsuleID-", "");

				// If it is locked, you cannot select it
				if(me.hasClass("locked")) return;

				if(me.hasClass("selected")) {
					me.removeClass("selected");
					Timesules.timecap.selected.splice(Timesules.timecap.selected.indexOf(id),1);
				} else {
					me.addClass("selected");
					Timesules.timecap.selected.push(id);
				}
				if(Timesules.timecap.selected.length > 0)
					$(".trashcan").addClass("trashcan-hl");
				else
					$(".trashcan").removeClass("trashcan-hl");
			});
			$("[id^='show-']").click(function() {
				$(".toggle-button").removeClass("active");
				$("#noCapsulesNotice").hide();
				switch(this.id.substr(5)) {
					case "pending":
					$(".timecapBlock").hide();
					$(".timecapBlock.active").show();
					if($(".timecapBlock.active").length <= 0)
						$("#noCapsulesNotice").show();
					$("#show-pending").addClass("active");
					break;
					case "locked":
					$(".timecapBlock").hide();
					$(".timecapBlock.locked").show();
					if($(".timecapBlock.locked").length <= 0)
						$("#noCapsulesNotice").show();
					$("#show-locked").addClass("active");
					break;
					case "releaesd":
					$(".timecapBlock").show();
					$(".timecapBlock.locked").hide();
					$(".timecapBlock.active").hide();
					if(($(".timecapBlock").length-$(".timecapBlock.locked").length-$(".timecapBlock.active").length) <= 0)
						$("#noCapsulesNotice").show();
					$("#show-releaesd").addClass("active");
					break;
					default:
					$(".timecapBlock").show();
					$("#show-all").addClass("active");
					break;
				}
			});
$("#delete_send").click(function() { Tc.timecap.confirmDelete(); });
});
},
confirmDelete:function() {
	if(Tc.timecap.selected.length <= 0) return;
	var prompts = "<table style=\"width:100%;\"><tr><th style=\"text-align: left;\">Prompt</th><th style=\"text-align: left;\">User</th><th style=\"text-align: left;\">Date</th></tr>";
	for(var i in Tc.timecap.selected) {
		var capsule = $("#capsuleID-"+Tc.timecap.selected[i]);
		var info = $(".timecapInfo", capsule).html();
		var author = info.substr(6, info.indexOf("Date:")-7);
		var date = info.substr(info.indexOf("Date:")+5);
		prompts += "<tr><td style=\"font-size:13px;\">"+$(".timecapPrompt", capsule).html()+"</td>" +
		"<td>"+author+"</td><td>"+date+"</td></tr>";
	}
	prompts += "</table>";

	Tc.d.open({
		title:"Confirm Delete",
		html:"Are you sure you want to "+((Tc.timecap.mode!=2)?"remove* these capsules":"delete these drafts")+"?<br /><br />"+prompts+
		((Tc.timecap.mode!=2)?"<br /><br /><span style=\"font-size: 9px;\">*Please note that already released capsules are not deleted but hidden from your lists since other contacts may still have the capsule visible.</span>":""),
		closeOnEscape:false,
		buttons:[
		{text:"Cancel",click:function() { Tc.d.close(); },create:function() { $(this).addClass("ui-priority-primary"); }},
		{
			text:"Remove Selected Timecapsules",
			click:function() { Tc.d.close(); Tc.timecap.sendDelete(); },
			create:function() { $(this).addClass("submit-button-red"); }
		}
		]
	});
},
sendDelete:function() {
	if(Tc.timecap.selected.length <= 0) return;
	$.ajax({
		data:{'delete':true,list:Tc.timecap.selected,t:Tc.timecap.mode},
		url:"/timecap.php",
		type:"POST",
		datatype:"json",
		success:function(result) {
			if(result.code == 200) {
				$("<div/>", {class:"ui-warn",text:result.msg}).
				appendTo("#deleteStatus").
				delay(5000).fadeOut(2500);
				for(var i in Tc.timecap.selected) Tc.timecap.selected[i].remove();
					Tc.timecap.selected = [];
				$(".trashcan").removeClass("trashcan-hl").css({cursor:"not-allowed"});
			} else {
				$("<div/>", {class:"ui-error"}).
				appendTo("#deleteStatus").
				html("Oops! An error occurred while deleteing the selected capsules. Please try again.").
				delay(10000).fadeOut(2500);
			}
		},
		error:function(xhr, status, error) {
			$("<div/>",{class:"ui-error",text:"Oops! An error occurred while deleteing the selected capsules. Please try again."}).
			appendTo("#deleteStatus").
			delay(10000).fadeOut(2500);
		}
	});
}
};

/**********************************************************/
/******************** Groups ******************************/
/**********************************************************/
Timesules.groups = {
	groupID:-1,
	contacts:[],
	activeBlock:null,
	openedContactsMenu:false,
	init:function(groupData) {
		Timesules.groups.groupID = groupData.id;

		// Function to toggle the group tabs: Details and Members
		var swap = function(self) {
			var me = $(self);

			if(me.hasClass("active")) return; // If this tab is already active, skip

			var meBlock = $("#"+self.id.replace("Tab","Block"));
			var allTabs = $("[id$='Tab']");

			allTabs.removeClass("active");
			me.addClass("active");
			Timesules.groups.activeBlock.slideUp(200);
			meBlock.slideDown(200);
			Timesules.groups.activeBlock = meBlock;

			// Check if we need to open the contacts menu
			var contactMenu = $("#contactsSideBar");
			if(groupData.isAdmin && self.id.replace("Tab","") == "members" && !Timesules.sideBar.isOpened(contactMenu)) {
				Timesules.groups.openedContactsMenu = true;
				Timesules.sideBar.open(contactMenu);
			} else if(Timesules.groups.openedContactsMenu) {
				Timesules.groups.openedContactsMenu = false;
				Timesules.sideBar.close(contactMenu);
			}
		};

		$(document).ready(function() {
			$("#capsuleDetailsTab").click(function() { swap(this); });
			$("#membersTab").click(function() { swap(this); });
			Timesules.groups.activeBlock = $("#capsuleDetailsBlock");

			$("[id^='view-cap-']").click(function() {
				Timesules.viewPrompt(this.id.replace("view-cap-", ""), VIEW_GROUP_CAPSULE);
			});

			// Only enable the drag&drop if we are an admin
			if(!groupData.isAdmin) return;

			$(".rsContact").draggable({
				addClasses:false,
				helper:"clone",
				appendTo:"body",
				containment:"document",
				scope:"promptDrag",
				create:function() {
					var me = $(this);
					me.data({
						src:this.src,
						id:me.parent().attr("id").replace("rsContactID-",""),
						name:$("a", me.parent()).html(),
						content:me.parent().html(),
						dropped:false
					});
				}
			});
			var current = $("[id^='promptCT']").draggable({
				helper:"clone",
				scope:"promptDrag",
				stop:function(event, ui) {
					if($(this).data("remove")) {
						Tc.groups.removeContact(this,this.id.replace("promptCT-",""));
					}
				}
			})
			.data({dropped:true,remove:false});
			$.each(current, function(key,val) {
				var me = $(val);
				var id = me.attr("id").replace("promptCT-","");
				if(Tc.groups.contacts.indexOf(id) != -1) return;
				Tc.groups.contacts.push(id);
			});
			$(".groupAdmin").draggable("option", "disabled", true);

			$("#contactsDrop").droppable({
				scope:"promptDrag",
				tolerance:"pointer",
				drop:function(event, ui) {
					if(Timesules.groups.groupID==-1) return;

					var me = $(this);
					var draggable = ui.draggable;
					var id = draggable.data("id");

					// Check and see if the contact already exists. If it does, skip
					if(Tc.groups.contacts.indexOf(id) != -1 || draggable.data("dropped")) {
						return;
					}

					// Create the new input
					var input = $("<span/>");
					input
					.draggable({
						helper:"clone",
						scope:"promptDrag",
						stop:function(event, ui) {
							if(input.data("remove")) {
								Tc.groups.removeContact(this, id);
							}
						}
					})
					.data({dropped:true,remove:false})
					.attr("id","promptCT-"+id)
					.html('<img src="'+draggable.data("src")+'" class="avatar32" /> '+draggable.data("name"));

					// Otherwise, add the ID to the contacts array and append it to the droppable
					$.ajax({
						type:"POST",
						data:{manageUser:true,gid:Tc.groups.groupID,user:id},
						url:"/groups.php",
						success:function(result) {
							if(result.code == 200) {
								Tc.groups.contacts.push(id);
								me.append(input);
								$("#groupDetails,#contactsDrop")
								.css("background-color", "#E1F2E3")
								.stop().animate({backgroundColor:"#FEFEFE"},1000);
							} else if(result.code == 401 || result.code == 500) {
								$("#errorBlock").append($("<div/>")
									.html(result.msg)
									.addClass("ui-error")
									.delay(5000).fadeOut(2500)
									);
							}
						},
						error:function(xhr, status, error) {
							$("#errorBlock").append($("<div/>")
								.html(error)
								.addClass("ui-error")
								.delay(5000).fadeOut(2500)
								);
						}
					});
				},
				out:function(event, ui) {
					if(!ui.draggable.data("dropped")) return;
					ui.draggable.css({display:"none"}).data("remove", true);
				}
			});
});
},
removeContact:function(obj, id) {
	if(Timesules.groups.groupID==-1 || Tc.groups.contacts.indexOf(id) == -1) return;
	$.ajax({
		type:"POST",
		data:{manageUser:true,remove:"true",gid:Tc.groups.groupID,user:id},
		url:"/groups.php",
		success:function(result) {
			if(result.code == 200) {
				Tc.groups.contacts.splice(Tc.groups.contacts.indexOf(id),1);
				$(obj).remove();
				$("#groupDetails,#contactsDrop")
				.css("background-color", "#F2E1E1")
				.stop().animate({backgroundColor:"#FEFEFE"},1000);
			} else if(result.code == 401 || result.code == 500) {
				$(obj).css({display:"inline"});
				$("#errorBlock").append($("<div/>")
					.html(result.msg)
					.addClass("ui-error")
					.delay(5000).fadeOut(2500)
					);
			}
		},
		error:function(xhr, status, error) {
			$(obj).css({display:"inline"});
			$("#errorBlock").append($("<div/>")
				.html(error)
				.addClass("ui-error")
				.delay(5000).fadeOut(2500)
				);
		}
	});
}
};

/*********************************************************/
/***************** Calendar ******************************/
/*********************************************************/
Timesules.cal = {
	init:function() {
		$(document).ready(function() {
			$("[id^='dot-']")
			.mouseover(Tc.cal.showDesp).mouseout(Tc.cal.hideDesp)
			.click(function() {
				if(!$(this).hasClass("released")) return;
				var type = (this.id.indexOf("gr")>-1)?VIEW_GROUP_CAPSULE:VIEW_CAPSULE;
				var id = this.id.substring(this.id.lastIndexOf("-")+1);
				Timesules.viewPrompt(id, type);
			});
		});
	},
	showDesp:function() {
		var id = this.id.replace("dot-","");
		var me = $(this);
		var left = me.parent().position().left+me.position().left+25;
		var top = me.parent().position().top+me.position().top;
		$("#desp-"+id).css({display:"block",left:left,top:top});
	},
	hideDesp:function() {
		$("[id^='desp-']").css({display:"none"});
	}
};

/*********************************************************/
/***************** Contacts ******************************/
/*********************************************************/
Timesules.contacts = {
	selectedContacts:[],
	selectedNew:null,
	searchTO:null,
	init:function() {
		$(document).ready(function() {
			Timesules.contacts.addListeners();

			$("#requestButton").click(function() {
				if(Tc.contacts.selectedNew == null) {
					console.log("Empty");
					return;
				}else{
					console.log("Here: ");
					console.log(Tc.contacts.selectedNew);
				}
				$("#requestError").hide();
				$.ajax({
					data:{"add":true,"contact":Tc.contacts.selectedNew.attr("id").replace("contactID-", "")},
					url:"/contacts.php",
					type:"POST",
					datatype:"json",
					success:function(result) {
						if(result.code == "200") {
							console.log(result.code);
							Tc.contacts.selectedNew.remove();
							Tc.contacts.selectedNew = null;
							$("#requestButton").addClass("disabled");
							$("#requestSent").html(result.msg).show().delay(5000).fadeOut(2500);
						} else {
							console.log(result.code);
							// $("#requestError").show();
							$("#requestError").html(result.msg).show().delay(5000).fadeOut(2500);
						}
					},
					error:function(xhr, status, error) { $("#requestError").show(); }
				});
			});

			$("#addToCapsule").click(function() {
				if(Tc.contacts.selectedContacts.length < 1) return;
				location.href = '/capsule.php?c='+Tc.contacts.selectedContacts.join();
			});

			$("#delete_send").click(function() {
				if(Tc.contacts.selectedContacts.length != 1) return;
				Tc.d.open({html:"Awaiting confirmation...",title:"Please wait..."});
				$.ajax({
					data:{remove:Tc.contacts.selectedContacts[0]},
					url:"/contacts.php",
					type:"POST",
					datatype:"json",
					success:function(result) {
						if(result.code == 201) {
							Tc.d.html(result.msg);
							Tc.d.option({
								title:"Confirm Contact Removal",
								buttons:[
								{text:"Cancel",click:function(){ $(this).dialog("close"); }},
								{text:"Remove Contact",click:function() {
									Tc.d.html("Contact being removed. Please wait...");
									Tc.d.option({buttons:null});
										//--------------------------------------
										$.ajax({
											data:{"remove":result.id,"conf":"true"},
											type:"POST",
											url:"/contacts.php",
											dataType:"json",
											success:function(result2) {
												if(result2.code == 200) {
													Tc.d.close();
													$("#contactID-"+result.id).remove();
												} else {
													Tc.d.html(result2.msg);
													Tc.d.option({title:"Error: "+result2.code});
												}
											},
											error:function(xhr, status, error) {
												Tc.d.html("An error has occured. Please try again");
												Tc.d.option({title:"Error!"});
											}
										});
										//--------------------------------------
									},create:function() { $(this).addClass("submit-button-red"); }}
									]
								});
} else {
	Tc.d.html(result.msg);
	Tc.d.option({title:"Error: "+result.code});
}
},
error:function(xhr, status, error) { Tc.d.html("There was an error obtaining a confirmation."); Tc.d.option({title:"Error!"}); }
});
});
});
},
initResponse:function(id) {
	if(id != "" && id != -1) {
		$(document).ready(function() {
			$("#acceptButton, #ignoreButton").click(function() {
				Tc.d.open({html:"Processing request...",title:"Please wait..."});
				var res = (this.id.search("accept")!=-1)?"accept":"deny";
				$.ajax({
					data:{"respond":res,"id":id},
					type:"POST",
					url:"/contacts.php",
					dataType:"json",
					success:function(result) {
						if(result.code == 200) {
							Tc.d.html(result.msg);
							Tc.d.option({
								buttons:[{text:"Leave Page",click:function() {location.href = '/contacts.php';}}],
								title:result.title
							});
						} else {
							Tc.d.html(result.msg);
							Tc.d.option({title:"Error: "+result.code});
						}
					},
					error:function(xhr, status, error) { Tc.d.html("There was an error processing your request. Please try again."); Tc.d.option({title:"Error!"}); }
				});
			});
		});
	}
},
addListeners:function() {
	$("#contactsList .contactBlock").click(function (e) {
		var me = $(this);
		var id = me.attr("id").replace("contactID-", "");
		if(me.hasClass("selected")) {
			me.removeClass("selected");
			Tc.contacts.selectedContacts.splice(Tc.contacts.selectedContacts.indexOf(id),1);
		} else {
			me.addClass("selected");
			Tc.contacts.selectedContacts.push(id);
		}
		if(Tc.contacts.selectedContacts.length > 0) {
			$("#addToCapsule").removeClass("disabled");
			if(Tc.contacts.selectedContacts.length == 1)
				$(".trashcan").addClass("trashcan-hl");
			else
				$(".trashcan").removeClass("trashcan-hl");
		} else {
			$(".trashcan").removeClass("trashcan-hl");
			$("#addToCapsule").addClass("disabled");
		}
	});
	$("#newContacts .contactBlock").click(function (e) {
		var me = $(this);
		if(me.hasClass("selected")) {
			me.removeClass("selected");
			Tc.contacts.selectedNew = null;
			$("#requestButton").addClass("disabled");
		} else {
			if(Tc.contacts.selectedNew != null) Tc.contacts.selectedNew.removeClass("selected");
			me.addClass("selected");
			Tc.contacts.selectedNew = me;
			$("#requestButton").removeClass("disabled");
		}
	});
},
search:function() {
	var query = $("#searchBox").val();
	if(Tc.contacts.searchTO) clearTimeout(Tc.contacts.searchTO);
	Tc.contacts.searchTO = setTimeout(function() {
		$.ajax({
			data:{search:query},
			url:"/contacts.php",
			type:"GET",
			datatype:"json",
			success:function(result) {
					// Reset variables and hide the errorBlock
					Tc.contacts.selectedContacts = [];
					Tc.contacts.selectedNew = null;
					Tc.contacts.searchTO = null;
					$("#errorBlock").hide();

					// Reset the trashcan and buttons
					$(".trashcan").removeClass("trashcan-hl");
					$("#addToCapsule").addClass("disabled");
					$("#requestButton").addClass("disabled");

					// // Print out current contacts
					// var HTML = "";
					// for(var i in result.contacts) {
					// 	var cont = result.contacts[i];
					// 	HTML += '<div class="contactBlock" id="contactID-'+cont.id+'">'+
					// 		'<div class="contactAvatar"><img src="'+cont.avatar+'" /></div>'+
					// 		'<div class="contactName">'+cont.first+' '+cont.last+'</div>'+
					// 		'<div class="contactInfo">E-mail: <span title="'+cont.email+'">'+cont.email.substr(0,20)+'</span></div>'+
					// 		'</div>';
					// }
					// if(result.contacts.length <= 0) HTML = '<div class="ui-notice">No contacts found! Please try a different search term.</div>';
					// $("#contactsList").html(HTML);

					// Print out new contacts
					HTML = "";
					for(var i in result.newContacts) {
						var cont = result.newContacts[i];
						HTML += '<div class="contactBlock" id="contactID-'+cont.id+'">'+
						'<div class="contactAvatar"><img src="'+cont.avatar+'" /></div>'+
						'<div class="contactName">'+cont.first+' '+cont.last+'</div>'+
						'<div class="contactInfo">E-mail: <span title="'+cont.email+'">'+cont.email.substr(0,20)+'</span></div>'+
						'</div>';
					}
					var searchCont = $("#searchContainer");
					if(result.newContacts.length > 0 && !searchCont.is(":visible"))
						searchCont.slideDown(200);
					else if(result.newContacts.length <= 0)
						searchCont.slideUp(200);
					$("#newContacts").html(HTML);

					// Finally, reset the listeners on all of the contacts
					Tc.contacts.addListeners();
				},
				error:function(xhr, status, error) { $("#errorBlock").show(); }
			});
},1000);
}
};

/****************************************************************/
/******************** User Profile ******************************/
/****************************************************************/
Timesules.user_profile = {
	loading:false,
	loadingFailed:false,
	loaded:0,
	userID:0,
	loadMoreCapsules:function() {
		if(Timesules.user_profile.loaded == -1)
			return;

		Timesules.user_profile.loading = true;
		$("#loadingCapsulesText").html("Loading More Capsules...").css({cursor:"default"});
		$("#loadingCapsulesImg").css({backgroundImage:'url("/source/templates/images/loading.gif")',width:32,height:32});
		$("#loadingCapsules").show();
		$.ajax({
			data:{loadCapsules:Timesules.user_profile.loaded+1,u:Timesules.user_profile.userID},
			url:"/index.php",
			success:function(result) {
				Timesules.user_profile.loadingFailed = false;
				if(result) {
					var match = result.match(/capsuleBlock/g);
					if(match && match.length) {
						Timesules.user_profile.loaded += match.length;
						Timesules.user_profile.loading = false;
						$("#newestReleasedTimesules").append(result);
						$("#loadingMoreCapsules").hide();
					} else {
						$("#loadingCapsules").html("There are no more capsules to load.");
					}
				} else {
					$("#loadingCapsules").html("There are no more capsules to load.");
				}
				$("[id^='view-cap-']").unbind("click").click(function(e) {
					e.preventDefault();
					Timesules.viewPrompt(this.id.replace("view-cap-", ""), VIEW_CAPSULE);
				});
			},
			error:function(){
				Timesules.user_profile.loading = false;
				Timesules.user_profile.loadingFailed = true;
				$("#loadingCapsulesText").html("There was an error fetching more capsules. Try again?").css({cursor:"pointer"});
				$("#loadingCapsulesImg").css({backgroundImage:'url("/source/templates/images/refresh-button.png")',width:28,height:32});
			}
		});
},
newPosts:function(count) { $("#newPosts").html(count+" NEW!"); },
init:function(loadedCount, userid) {
	Timesules.user_profile.userID = userid;
	Timesules.user_profile.loaded = loadedCount;
	$(document).scroll(function(e) {
		var fromBottom = $(document).height()-($(document).scrollTop()+$(window).height());
		if(fromBottom<200 && !Timesules.user_profile.loading && !Timesules.user_profile.loadingFailed) {
			Timesules.user_profile.loadMoreCapsules();
		}
	});
	$(document).ready(function() {
		$("#loadingCapsules").click(function() {
			if(!Timesules.user_profile.loading) Timesules.user_profile.loadMoreCapsules();
		});
		$("[id^='view-cap-']").click(function() {
			Timesules.viewPrompt(this.id.replace("view-cap-", ""), VIEW_CAPSULE);
		});
		$("#nowContacts").delay(5000).fadeOut(2500);
	});
}
};
