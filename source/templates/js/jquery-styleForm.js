/**
 * Form styler
 * 
 * @author Tyler Hadidon
 * @date 2012
 */
(function($) {
	
	function replaceSelects(key,val) {
		// Get the select and create new divs.
		var select = $(val);
		
		if(select.hasClass("no-style"))
			return;
		
		// Opera is annoying...
		if($.browser.opera) select.css("min-width",select.width()+10);
		
		var id = "selectForm-select"+key;
		select.wrap('<span style="position: relative;" id="selectForm-wrapper-'+key+'" />');
		var wrapper = $("#selectForm-wrapper-"+key);
		
		var selectDiv = $("<div/>").attr("id",id).addClass("selectForm-select-box");
		var dropDiv = $("<div/>").attr("id",id+"-options").addClass("selectForm-select-dropdown").hide();
		
		var selected = "";
		$.each(val, function(okey,oval) {
			dropDiv.append("<div class='selectForm-option"+((oval.selected)?"-selected":"")+"' id='"+id+"-value-"+oval.value+"'>"+oval.innerHTML+"</div>\n");
			if(oval.selected)
				selected = oval.innerHTML;
		});
		
		// Add the divs to the parent
		var widthOffset = 3;
		select.css("z-index","-100");
		selectDiv.css({left:select.position().left,top:select.position().top});
		selectDiv.css("max-width",wrapper.width()+widthOffset).css("width",wrapper.width()+widthOffset);
		var loc = {top:24}; // This was set to bottom...
		/*if(select.offset().top < 300)
			loc = {top:24};*/
		dropDiv.css(loc).css("left",-1).css("width",wrapper.width()+widthOffset);
		selectDiv.html('<div class="selectForm-select-box-left"></div><div class="selectForm-select-box-text"><div style="padding-top:5px;">'+
		selected+'</div></div><div class="selectForm-select-box-right"></div>');
		$(".selectForm-select-box-text", selectDiv).css("width",wrapper.width()-25);
		
		selectDiv.append(dropDiv);
		select.parent().append(selectDiv);
		select.css("opacity","0.0");
		select.css("filter","alpha(opacity=00)");
		
		// Add events!
		$(document).click(function() { $(".selectForm-select-dropdown").hide(); });
		selectDiv.click(function(e) {
			e.stopPropagation();
			var show = (dropDiv.css("display")=="none");
			$(".selectForm-select-dropdown").hide();
			if(show) dropDiv.show();
		});
		$("div", dropDiv).mouseover(function() { $("div", dropDiv).removeClass("selectForm-option-selected"); $(this).addClass("selectForm-option-selected"); })
		$("div", dropDiv).click(function() {
			select.val(this.id.replace(id+"-value-",""));
			$(".selectForm-select-box-text div", selectDiv).html(this.innerHTML);
			dropDiv.hide();
			return false;
		});
		$(window).resize(function() {
			selectDiv.css("left",select.position().left).css("top",select.position().top);
			selectDiv.css("max-width",wrapper.width()+widthOffset).css("width",wrapper.width()+widthOffset);
			$(".selectForm-select-box-text", selectDiv).css("width",wrapper.width()-25);
			dropDiv.css(loc).css("left",-1).css("width",wrapper.width()+widthOffset);
		});
		
		// TODO: Impliment the key presses: Up(37:38) Down(39:40) Select(13)
		// Is this even possible?
	}
	
	function replaceRadios(key,val) {
		// Get the current radio and create a new div to replace it
		var radio = $(val);
		
		if(radio.hasClass("no-style"))
			return;
		
		var id = (val.id!="")?val.id:key;
		var div = $("<div/>").attr("id", "radioForm"+id);
		if(radio.attr("checked") == "checked")
			div.addClass("selectForm-radio-checked");
		div.addClass("selectForm-radio");

		radio.css("margin","0px 5px").css("opacity","0.0").css("filter","alpha(opacity=00)");
		div.css({left:radio.position().left+4,top:radio.position().top});
		radio.parent().append(div);

		// When the div is clicked, check the radio. All other radios with the same name
		// will automaticall change as well!
		div.click(function() {
			$("[name='"+radio.attr("name")+"']").change();
			radio.attr("checked","checked");
			div.addClass("selectForm-radio-checked");
		});
		
		radio.change(function() { div.removeClass("selectForm-radio-checked"); });
		radio.keyup(function(e) { if(e.keyCode != 9) div.click(); })
		
		// Move the div with the raido button
		$(window).resize(function() {
			div.css({left:radio.position().left+4,top:radio.position().top});
		});
	}
	
	function replaceCheckboxes(key,val) {
		// Get the current checkbox and create a new div to replace it
		var check = $(val);
		
		if(check.hasClass("no-style"))
			return;
		
		var id = (val.id!="")?val.id:key;
		var div = $("<div/>").attr("id", "checkForm"+id);
		if(check.attr("checked") == "checked")
			div.addClass("selectForm-checkbox-checked");
		div.addClass("selectForm-checkbox");
		
		check.css("opacity","0.0").css("filter","alpha(opacity=00)");
		div.css({left:check.position().left,top:check.position().top});
		check.parent().append(div);
		
		// Toggle the checkbox
		div.click(function() {
			if(check.attr("checked") != "checked") {
				check.attr("checked","checked");
				div.addClass("selectForm-checkbox-checked");
			} else  {
				check.attr("checked",null);
				div.removeClass("selectForm-checkbox-checked");
			}
		});
		check.keyup(function(e) { e.preventDefault(); if(e.keyCode == 32) div.click(); return false; })
		
		// Move the div with the checkbox
		$(window).resize(function() {
			div.css({left:check.position().left,top:check.position().top});
		});
	}
	
	function watchLabel(key,val) {
		var label = $(val);
		label.click(function(e) {
			e.preventDefault();
			$("[id$='"+label.attr("for")+"']").click();
		});
	}
	
	var methods = {
		init:function(options) {
			
			// Go through each select and collect their option's keys and values..
			$.each($("select"), replaceSelects);
			$.each($("input[type='radio']"), replaceRadios);
			$.each($("input[type='checkbox']"), replaceCheckboxes);
			$.each($("label"), watchLabel);
		}
	};
	
	$.fn.styleForm = function(method) {
		if(methods[method])
			return methods[method].apply(this,Array.prototype.slice.call(arguments, 1));
		else if(typeof method === 'object' || !method)
			return methods.init.apply(this,arguments);
		else {
			$.error('Method '+method+' does not exist on jQuery.selectForm');
		}
	}
	
	$(document).ready(function() { $(document).styleForm(); setInterval(function(){$(window).trigger("resize")}, 100); });
})(jQuery);