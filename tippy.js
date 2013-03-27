/*
	dom_tooltip.js
	
	DOM Tooltip by Chris Roberts
	columcille@gmail.com
	http://croberts.me/
*/

// Initialize Tippy object
var Tippy = new Tippy();

function Tippy()
{
	// Initialize variables
	
	// Content holders
	this.useDivContent = false;
	this.contentTitle = '';
	this.contentText = '';

	// Title and swap
	this.title = '';
	this.swaptitle = '';
	this.isSwapped = false;
	
	// Fadeout delay. Set both default and delay to adjust for user changes.
	this.defaultDelay = 900;
	this.delay = this.defaultDelay;
	
	// The following variables track page/mouse position, etc
	this.curPageX = 0;
	this.curPageY = 0;
	this.scrollPageX = 0;
	this.scrollPageY = 0;
	this.viewPageX = 0;
	this.viewPageY = 0;
	this.viewScreenX = 0;
	this.viewScreenY = 0;
	
	// The position and height of the tippy link
	this.tipLinkX = 0;
	this.tipLinkY = 0;
	this.tipLinkHeight = 0;
	
	// Where to position the tooltip
	this.tipPosition = "mouse";
	this.tipOffsetX = 0;
	this.tipOffsetY = 0;
	
	// Do we fade in and out?
	this.fadeRate = 0;
	this.timer;
	this.hiding = false;
	this.allowFreeze = true;
	
	this.sticky = false;
	this.showClose = true;
	this.closeText = "Close";

	// Dragging settings
	this.draggable = true;
	this.dragheader = true;
	
	this.tipId = "";
	
	this.tipBox = "";
	this.tipHeader;
	this.tipBody;
	
	this.mouseoutSet = false;
	
	this.jQuery = jQuery.noConflict();

	// Initialize tooltip and settings
	this.initialize = function(tipArgs)
	{
		this.tipPosition = tipArgs.tipPosition;
		this.tipOffsetX = tipArgs.tipOffsetX;
		this.tipOffsetY = tipArgs.tipOffsetY;
		this.fadeRate = tipArgs.fadeRate;
		this.sticky = tipArgs.sticky;
		this.showClose = tipArgs.showClose;
		this.closeText = tipArgs.closeText;
		this.delay = this.defaultDelay = tipArgs.delay;
		this.draggable = tipArgs.draggable;
		this.dragheader = tipArgs.dragheader;
		this.useDivContent = tipArgs.useDivContent;
	};
	
	this.createTip = function()
	{
		// Create the tooltip singleton
		this.tipBox = this.jQuery('<div></div>')
			.hide()
			.css("display", "none")
			.css("position", "absolute")
			.css("height", "auto")
			.addClass("domTip_Tip tippy_tip")
			.attr("id", "domTip_tipBox")
			.mouseover(function() { Tippy.freeze(); })
			.appendTo('body');

		this.tipHeader = this.jQuery("<div></div>")
			.css("height", "auto")
			.addClass("domTip_tipHeader tippy_header")
			.attr("id", "this.tipHeader")
			.appendTo(this.tipBox);
		
		this.tipBody = this.jQuery("<div></div>")
			.css("height", "auto")
			.addClass("domTip_tipBody tippy_body")
			.attr("id", "this.tipBody")
			.appendTo(this.tipBox);

		if (this.draggable) {
			if (this.dragheader) {
				this.tipBox.draggable({ handle: ".domTip_tipHeader" });
				this.tipHeader.addClass("tippy_draggable");
			} else {
				this.tipBox.draggable();
				this.tipBox.addClass("tippy_draggable");
			}
		}
	};
	
	this.mouseTrigger = function(stickyTippy)
	{
		if (!stickyTippy && !this.mouseoutSet) {
			this.tipBox.mouseout(function() { Tippy.fadeTippyOut(); });
			this.mouseoutSet = true;
		} else if (stickyTippy) {
			this.tipBox.unbind('mouseout');
			this.mouseoutSet = false;
		}
	}
	
	// Initialize all position data
	this.setPositions = function(domTip_tipElement, domTip_event)
	{
		if (!domTip_event) {
			domTip_event = window.event;
		}
		
		this.scrollPageX = this.jQuery(window).scrollLeft();
		this.scrollPageY = this.jQuery(window).scrollTop();
		
		this.viewScreenX = this.jQuery(window).width();
		this.viewScreenY = this.jQuery(window).height();
		
		this.curPageX = domTip_event.clientX + this.scrollPageX;
		this.curPageY = domTip_event.clientY + this.scrollPageY;
	
		this.viewPageX = domTip_event.clientX;
		this.viewPageY = domTip_event.clientY;
		
		this.tipLinkHeight = this.jQuery("#" + domTip_tipElement).height();
		this.tipLinkX = this.jQuery("#" + domTip_tipElement).offset().left;
		this.tipLinkY = this.jQuery("#" + domTip_tipElement).offset().top;
	};
	
	this.fadeTippyOut = function()
	{
		clearTimeout(this.timer);
		this.tipId = "";
		this.hiding = true;
		
		this.timer = setTimeout(function() { Tippy.beginFadeout(); }, this.delay);
	};
	
	this.beginFadeout = function()
	{
		this.tipBox.fadeOut(this.fadeRate, 'swing', function() { Tippy.finishFadeout(); });
		
		this.doSwapTitle(false);
	}
	
	this.finishFadeout = function()
	{
		// Clear body contents so nothing is left behind.
		Tippy.tipBody.html("");
		Tippy.hiding = false;
	}
	
	this.fadeTippyFromClose = function()
	{
		this.tipId = "";
		this.hiding = true;
		this.allowFreeze = false;
		
		Tippy.tipBox.fadeOut(" + this.fadeRate + ", 'swing', function() { Tippy.hiding = false; Tippy.allowFreeze = true; Tippy.finishFadeout(); });
	};
	
	this.fadeTippyIn = function()
	{
		clearTimeout(this.timer);
		this.timer = setTimeout("Tippy.tipBox.fadeIn(" + this.fadeRate + ")", 50);
	};
	
	this.moveTip = function()
	{
		// Specify the location of the tooltip.
		
		// Get the height and width of the tooltip container. Will use this when
		// calculating tooltip position.
		this.tipHeight = this.tipBox.height();
		this.tipWidth = this.tipBox.width();
		
		/* 
		 * Calculate where the tooltip should be located
		 */
		
		var tipHorSide = "left", tipVertSide = "top";
		
		// this.tipXloc and this.tipYloc specify where the tooltip should appear.
		// By default, it is just below and to the right of the mouse pointer.
		if (this.tipPosition === "mouse") {
			// Position below the mouse cursor
			this.tipXloc = this.curPageX;
			this.tipYloc = this.curPageY;
		} else if (this.tipPosition === "link") {
			// Position below the link
			this.tipXloc = this.tipLinkX;
			this.tipYloc = this.tipLinkY + this.tipLinkHeight;
		}
		
		// Check our offsets
		if (this.manOffsetX !== undefined) {
			this.tipXloc += this.manOffsetX;
		} else {
			this.tipXloc += this.tipOffsetX;
		}
		
		if (this.manOffsetY !== undefined) {
			this.tipYloc += this.manOffsetY;
		} else {
			this.tipYloc += this.tipOffsetY;
		}
		
		/*
		 * Adjust position of tooltip to place it within window boundaries
		 */
		
		// If the tooltip extends off the right side, pull it over
		if ((this.tipXloc - this.scrollPageX) + 5 + this.tipWidth > this.viewScreenX) {
			this.pageXDiff = ((this.tipXloc - this.scrollPageX) + 5 + this.tipWidth) - this.viewScreenX;
			this.tipXloc -= this.pageXDiff;
		}
		
		// If the tooltip will extend off the bottom of the screen, pull it back up.
		if ((this.tipYloc - this.scrollPageY) + 5 + this.tipHeight > this.viewScreenY) {
			this.pageYDiff = ((this.tipYloc - this.scrollPageY) + 5 + this.tipHeight - this.viewScreenY);
			this.tipYloc -= this.pageYDiff;
		}
	
		// If the tooltip extends off the bottom and the top, line up the top of
		// the tooltip with the top of the page
		if (this.tipHeight > this.viewScreenY) {
			this.tipYloc = this.scrollPageY + 5;
		}
		
		/*
		 * Specify css position rules
		 */
		
		// Set the position in pixels.
		this.tipBox.css(tipHorSide, this.tipXloc + "px");
		this.tipBox.css(tipVertSide, this.tipYloc + "px");
	};
	
	this.freeze = function()
	{
		if (this.allowFreeze === true) {
			clearTimeout(this.timer);
			this.tipBox.stop();
			this.tipBox.css("opacity", 100);
		}
	};
	
	this.doSwapTitle = function(newSwap)
	{
		if (this.isSwapped) {
			this.jQuery(this.swapelement).html(this.title);
			this.isSwapped = false;
			this.title = '';
		}
		
		if (this.swaptitle !== '' && newSwap) {
			this.title = this.jQuery('#' + this.tippyLinkId).html();
			this.swapelement = '#' + this.tippyLinkId;
			
			this.jQuery(this.swapelement).html(this.swaptitle);
			
			this.isSwapped = true;
		} else {
			this.title = '';
		}
	}
	
	this.loadTip = function(tipArgs)
	{
		if (this.tipBox === "") {
			this.createTip();
		}
		
		this.tippyLinkId = tipArgs.id;
		domTip_newTipId = this.tippyLinkId;

		// Are we putting content in a hidden div? Set title and text accordingly.
		if (this.jQuery('#' + this.tippyLinkId + '_content').length > 0) {
			this.contentTitle = this.jQuery('#' + this.tippyLinkId + '_content span').html();
			this.contentText = this.jQuery('#' + this.tippyLinkId + '_content div').html();
		} else {
			this.contentTitle = tipArgs.title;
			this.contentText = tipArgs.text;
		}
		
		// Did the user specify a swaptitle?
		if (tipArgs.swaptitle !== undefined) {
			this.swaptitle = tipArgs.swaptitle;
		} else {
			this.swaptitle = '';
		}
		
		this.doSwapTitle(true);
		
		// Check the delay
		if (tipArgs.delay !== undefined) {
			this.delay = tipArgs.delay;
		} else {
			this.delay = this.defaultDelay;
		}
		
		// Look at our sticky status
		if (tipArgs.sticky !== undefined) {
			this.mouseTrigger(tipArgs.sticky);
		} else {
			this.mouseTrigger(this.sticky);
		}
		
		if (this.tipId !== domTip_newTipId) {
			// If we have a this.tipId then a tooltip is currently showing
			if (this.tipId !== "" || this.hiding === true) {
				this.tipBox.hide();
				this.freeze();
				this.tipId = "";
				this.hiding = false;
			}
			
			// Check class and id values
			this.tipBox.attr("id", "domTip_tipBox " + tipArgs.id + "_tip");
			
			if (tipArgs.tippyclass !== undefined) {
				this.manClass = tipArgs.tippyclass + "_tip";
				this.tipBox.addClass(this.manClass);
			} else if (this.manClass !== undefined) {
				this.tipBox.removeClass(this.manClass);
				this.manClass = undefined;
			}
			
			// Update location info
			this.setPositions(this.tippyLinkId, tipArgs.event);
			
			// See if we need to do any manual offsets
			if (tipArgs.offsetx !== undefined) {
				this.manOffsetX = tipArgs.offsetx;
			} else {
				this.manOffsetX = undefined;
			}
			
			if (tipArgs.offsety !== undefined) {
				this.manOffsetY = tipArgs.offsety;
			} else {
				this.manOffsetY = undefined;
			}
			
			if (tipArgs.top !== undefined) {
				this.top = tipArgs.top;
				this.bottom = undefined;
			} else if (tipArgs.bottom !== undefined) {
				this.bottom = tipArgs.bottom;
				this.top = undefined;
			} else {
				this.top = undefined;
				this.bottom = undefined;
			}
			
			if (tipArgs.left !== undefined) {
				this.left = tipArgs.left;
				this.right = undefined;
			} else if (tipArgs.right !== undefined) {
				this.right = tipArgs.right;
				this.left = undefined;
			} else {
				this.right = undefined;
				this.left = undefined;
			}
			
			// Change size
			
			// First, get calculated difference
			this.tipElementDifferenceHeader = this.tipBox.width() - this.tipHeader.width();
			this.tipElementDifferenceBody = this.tipBox.width() - this.tipBody.width();
			
			if (tipArgs.height !== undefined) {
				this.tipBody.css("height", tipArgs.height + "px");
				this.tipBody.css("min-height", tipArgs.height + "px");
				this.tipBody.css("max-height", tipArgs.height + "px");
			} else {
				this.tipBody.css("height", "auto");
				this.tipBody.css("min-height", "");
				this.tipBody.css("max-height", "");
			}
	
			if (tipArgs.width !== undefined) {
				this.tipBox.css("width", tipArgs.width + "px");
				this.tipHeader.css("width", tipArgs.width - this.tipElementDifferenceHeader + "px");
				this.tipBody.css("width", tipArgs.width - this.tipElementDifferenceBody + "px");
			} else {
				this.tipBox.css("width", "");
				this.tipHeader.css("width", "");
				this.tipBody.css("width", "");
			}
			
			this.tipId = domTip_newTipId;
			
			if (tipArgs.header !== undefined) {
				if (typeof tipArgs.headerText === "string") {
					domTip_headerText = tipArgs.headerText;
				} else if (this.contentTitle.length > 0) {
					domTip_headerText = this.contentTitle;
				} else if (this.jQuery("#" + this.tipId).attr('title') !== undefined) {
					domTip_headerText = this.jQuery("#" + this.tipId).attr('title');
				} else if (this.jQuery("#" + this.tipId).attr('tippyTitle') !== undefined) {
					domTip_headerText = this.jQuery("#" + this.tipId).attr('tippyTitle');
				} else {
					domTip_headerText = this.jQuery("#" + this.tipId).text();
				}
			} else {
				domTip_headerText = "";
			}
			
			if (tipArgs.headerhref !== undefined) {
				domTip_headerLink = tipArgs.headerhref;
			} else {
				domTip_headerLink = this.jQuery("#" + this.tipId).attr('href');
			}
			
			this.populateTip(this.contentText, domTip_headerText, domTip_headerLink);		
		} else {
			this.freeze();
		}
	};
	
	// Older method of loading tooltip data
	this.loadTipInfo = function(domTip_tipText, domTip_tipWidth, domTip_tipHeight, domTip_tipId, domTip_event, domTip_tipTitle)
	{
		var tipInfo = { 
			text: domTip_tipText, 
			id: domTip_tipId, 
			event: domTip_event
		};
		
		if (typeof domTip_tipTitle === "string") {
			tipInfo.title = domTip_tipTitle;
		}
		
		if (typeof domTip_tipWidth === "number" && domTip_tipWidth !== 0) {
			tipInfo.width = domTip_tipWidth;
		}
		
		if (typeof domTip_tipHeight === "number" && domTip_tipHeight !== 0) {
			tipInfo.height = domTip_tipHeight;
		}
		
		this.loadTip(tipInfo);
	};
	
	this.populateTip = function(domTip_tipText, domTip_headerText, domTip_headerLink)
	{
		// Build the tip header
		if (domTip_headerText !== "") {
			this.tipHeader.show();
			
			var headerHTML = domTip_headerText;
			
			if (domTip_headerLink !== undefined && domTip_headerLink !== "") {
				// See if the Tippy link has a target
				domTip_target = this.jQuery("#" + this.tippyLinkId).attr('target');
				
				if (domTip_target !== "") {
					headerTarget = ' target="_blank"';
				} else {
					headerTarget = '';
				}
				
				headerHTML = '<a href="' + domTip_headerLink + '"' + headerTarget + '>' + domTip_headerText + '</a>';
			}
			
			if (this.showClose) {
				headerClose = '<div class="domTip_tipCloseLink tippy_closelink" onClick="Tippy.fadeTippyFromClose();">' + this.closeText + '</div>';
				
				headerHTML = headerHTML + headerClose;
			}
			
			this.tipHeader.html(headerHTML);
		} else {
			this.tipHeader.hide();
			this.tipBody.addClass('domTip_noHeader tippy_noheader');
			
			if (this.showClose) {
				bodyClose = '<div class="domTip_tipCloseLink tippy_closelink" onClick="Tippy.fadeTippyFromClose();">' + this.closeText + '</div>';
				
				domTip_tipText = bodyClose + domTip_tipText;
			}
		}
		
		this.tipBody.html(domTip_tipText);
			
		this.moveTip();
		
		this.fadeTippyIn();
	};
}

// Functions that should exist outside the Tippy object

// domTip_toolText() provides compatibility with older hard-coded Tippy links
function domTip_toolText(domTip_newTipId, domTip_tipText, domTip_headerText, domTip_headerLink, domTip_tipWidth, domTip_tipHeight, domTip_tipElement, domTip_event)
{
	if (domTip_headerText !== "") {
		this.jQuery("#" + domTip_tipElement).attr('tippyTitle', domTip_headerText);
	}
	
	Tippy.loadTipInfo(domTip_tipText, domTip_tipWidth, domTip_tipHeight, domTip_tipElement, domTip_event);
}

// domTip_fadeTipOut() provides compatibility with older hard-coded Tippy links
function domTip_fadeTipOut()
{
	Tippy.fadeTippyOut();
}
