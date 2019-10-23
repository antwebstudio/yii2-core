
(function($){

if (typeof Ant == 'undefined')
{
	Ant = {};
}

$.extend(Ant,
{
	left:                  "left",
	right:                 "right",

	/**
	 * Returns a hidden CSRF token input, if CSRF protection is enabled.
	 *
	 * @return string
	 */
	getCsrfInput: function()
	{
		if (Ant.csrfTokenName)
		{
			return '<input type="hidden" name="'+Ant.csrfTokenName+'" value="'+Ant.csrfTokenValue+'"/>';
		}
		else
		{
			return '';
		}
	},

	/**
	 * Posts an action request to the server.
	 *
	 * @param string action
	 * @param object|null data
	 * @param function|null callback
	 * @param object|null options
	 * @return jqXHR
	 */
	postActionRequest: function(action, data, callback, options)
	{
		// Make 'data' optional
		if (typeof data == 'function')
		{
			options = callback;
			callback = data;
			data = {};
		}

		if (Ant.csrfTokenValue && Ant.csrfTokenName)
		{
			if (typeof data == 'string')
			{
				if (data) { data += '&' }
				data += Ant.csrfTokenName + '=' + Ant.csrfTokenValue
			}
			else
			{
				if (typeof data !== 'object')
				{
					data = {};
				}

				data[Ant.csrfTokenName] = Ant.csrfTokenValue;
			}
		}

		var jqXHR = $.ajax($.extend({
			url:      Ant.getActionUrl(action),
			type:     'POST',
			data:     data,
			success:  callback,
			error:    function(jqXHR, textStatus, errorThrown)
			{
				if (callback)
				{
					callback(null, textStatus, jqXHR);
				}
			},
			complete: function(jqXHR, textStatus)
			{
				if (textStatus != 'success')
				{
					if (typeof Ant.cp != 'undefined')
					{
						Ant.cp.displayError();
					}
					else
					{
						alert(Ant.t('An unknown error occurred.'));
					}
				}
			}
		}, options));

		// Call the 'send' callback
		if (options && typeof options.send == 'function')
		{
			options.send(jqXHR);
		}

		return jqXHR;
	},

	_waitingOnAjax: false,
	_ajaxQueue: [],

	/**
	 * Queues up an action request to be posted to the server.
	 */
	queueActionRequest: function(action, data, callback, options)
	{
		// Make 'data' optional
		if (typeof data == 'function')
		{
			options = callback;
			callback = data;
			data = undefined;
		}

		Ant._ajaxQueue.push([action, data, callback, options]);

		if (!Ant._waitingOnAjax)
		{
			Ant._postNextActionRequestInQueue();
		}
	},

	_postNextActionRequestInQueue: function()
	{
		Ant._waitingOnAjax = true;

		var args = Ant._ajaxQueue.shift();

		Ant.postActionRequest(args[0], args[1], function(data, textStatus, jqXHR)
		{
			if (args[2] && typeof args[2] == 'function')
			{
				args[2](data, textStatus, jqXHR);
			}

			if (Ant._ajaxQueue.length)
			{
				Ant._postNextActionRequestInQueue();
			}
			else
			{
				Ant._waitingOnAjax = false;
			}
		}, args[3]);
	},

	/**
	 * Compares two variables and returns whether they are equal in value.
	 * Recursively compares array and object values.
	 *
	 * @param mixed obj1
	 * @param mixed obj2
	 * @return bool
	 */
	compare: function(obj1, obj2)
	{
		// Compare the types
		if (typeof obj1 != typeof obj2)
		{
			return false;
		}

		if (typeof obj1 == 'object')
		{
			// Compare the lengths
			if (obj1.length != obj2.length)
			{
				return false;
			}

			// Is one of them an array but the other is not?
			if ((obj1 instanceof Array) != (obj2 instanceof Array))
			{
				return false;
			}

			// If they're actual objects (not arrays), compare the keys
			if (!(obj1 instanceof Array))
			{
				if (!Ant.compare(Ant.getObjectKeys(obj1), Ant.getObjectKeys(obj2)))
				{
					return false;
				}
			}

			// Compare each value
			for (var i in obj1)
			{
				if (!Ant.compare(obj1[i], obj2[i]))
				{
					return false;
				}
			}

			// All clear
			return true;
		}
		else
		{
			return (obj1 === obj2);
		}
	},

	/**
	 * Returns an array of an object's keys.
	 *
	 * @param object obj
	 * @return string
	 */
	getObjectKeys: function(obj)
	{
		var keys = [];

		for (var key in obj)
		{
			keys.push(key);
		}

		return keys;
	},

	/**
	 * Takes an array or string of chars, and places a backslash before each one, returning the combined string.
	 *
	 * Userd by ltrim() and rtrim()
	 *
	 * @param string|array chars
	 * @return string
	 */
	escapeChars: function(chars)
	{
		if (!Helper.isArray(chars))
		{
			chars = chars.split();
		}

		var escaped = '';

		for (var i = 0; i < chars.length; i++)
		{
			escaped += "\\"+chars[i];
		}

		return escaped;
	},

	/**
	 * Trim characters off of the beginning of a string.
	 *
	 * @param string str
	 * @param string|array|null The characters to trim off. Defaults to a space if left blank.
	 * @return string
	 */
	ltrim: function(str, chars)
	{
		if (!str) return str;
		if (chars === undefined) chars = ' \t\n\r\0\x0B';
		var re = new RegExp('^['+Ant.escapeChars(chars)+']+');
		return str.replace(re, '');
	},

	/**
	 * Trim characters off of the end of a string.
	 *
	 * @param string str
	 * @param string|array|null The characters to trim off. Defaults to a space if left blank.
	 * @return string
	 */
	rtrim: function(str, chars)
	{
		if (!str) return str;
		if (chars === undefined) chars = ' \t\n\r\0\x0B';
		var re = new RegExp('['+Ant.escapeChars(chars)+']+$');
		return str.replace(re, '');
	},

	/**
	 * Trim characters off of the beginning and end of a string.
	 *
	 * @param string str
	 * @param string|array|null The characters to trim off. Defaults to a space if left blank.
	 * @return string
	 */
	trim: function(str, chars)
	{
		str = Ant.ltrim(str, chars);
		str = Ant.rtrim(str, chars);
		return str;
	},

	/**
	 * Filters an array.
	 *
	 * @param array    arr
	 * @param function callback A user-defined callback function. If null, we'll just remove any elements that equate to false.
	 * @return array
	 */
	filterArray: function(arr, callback)
	{
		var filtered = [];

		for (var i = 0; i < arr.length; i++)
		{
			if (typeof callback == 'function')
			{
				var include = callback(arr[i], i);
			}
			else
			{
				var include = arr[i];
			}

			if (include)
			{
				filtered.push(arr[i]);
			}
		}

		return filtered;
	},

	/**
	 * Returns whether an element is in an array (unlike jQuery.inArray(), which returns the element's index, or -1).
	 *
	 * @param mixed elem
	 * @param mixed arr
	 * @return bool
	 */
	inArray: function(elem, arr)
	{
		return ($.inArray(elem, arr) != -1);
	},

	/**
	 * Removes an element from an array.
	 *
	 * @param mixed elem
	 * @param array arr
	 * @return bool Whether the element could be found or not.
	 */
	removeFromArray: function(elem, arr)
	{
		var index = $.inArray(elem, arr);
		if (index != -1)
		{
			arr.splice(index, 1);
			return true;
		}
		else
		{
			return false;
		}
	},

	/**
	 * Returns the last element in an array.
	 *
	 * @param array
	 * @return mixed
	 */
	getLast: function(arr)
	{
		if (!arr.length)
			return null;
		else
			return arr[arr.length-1];
	},

	/**
	 * Makes the first character of a string uppercase.
	 *
	 * @param string str
	 * @return string
	 */
	uppercaseFirst: function(str)
	{
		return str.charAt(0).toUpperCase() + str.slice(1);
	},

	/**
	 * Makes the first character of a string lowercase.
	 *
	 * @param string str
	 * @return string
	 */
	lowercaseFirst: function(str)
	{
		return str.charAt(0).toLowerCase() + str.slice(1);
	},

	/**
	 * Converts a number of seconds into a human-facing time duration.
	 */
	secondsToHumanTimeDuration: function(seconds, showSeconds)
	{
		if (typeof showSeconds == 'undefined')
		{
			showSeconds = true;
		}

		var secondsInWeek   = 604800,
			secondsInDay    = 86400,
			secondsInHour   = 3600,
			secondsInMinute = 60;

		var weeks = Math.floor(seconds / secondsInWeek);
		seconds = seconds % secondsInWeek;

		var days = Math.floor(seconds / secondsInDay);
		seconds = seconds % secondsInDay;

		var hours = Math.floor(seconds / secondsInHour);
		seconds = seconds % secondsInHour;

		if (showSeconds)
		{
			var minutes = Math.floor(seconds / secondsInMinute);
			seconds = seconds % secondsInMinute;
		}
		else
		{
			var minutes = Math.round(seconds / secondsInMinute);
			seconds = 0;
		}

		timeComponents = [];

		if (weeks)
		{
			timeComponents.push(weeks+' '+(weeks == 1 ? Ant.t('week') : Ant.t('weeks')));
		}

		if (days)
		{
			timeComponents.push(days+' '+(days == 1 ? Ant.t('day') : Ant.t('days')));
		}

		if (hours)
		{
			timeComponents.push(hours+' '+(hours == 1 ? Ant.t('hour') : Ant.t('hours')));
		}

		if (minutes || (!showSeconds && !weeks && !days && !hours))
		{
			timeComponents.push(minutes+' '+(minutes == 1 ? Ant.t('minute') : Ant.t('minutes')));
		}

		if (seconds || (showSeconds && !weeks && !days && !hours && !minutes))
		{
			timeComponents.push(seconds+' '+(seconds == 1 ? Ant.t('second') : Ant.t('seconds')));
		}

		return timeComponents.join(', ');
	},

	/**
	 * Converts extended ASCII characters to ASCII.
	 *
	 * @param string str
	 * @return string
	 */
	asciiString: function(str)
	{
		var asciiStr = '';

		for (var c = 0; c < str.length; c++)
		{
			var ascii = str.charCodeAt(c);

			if (ascii >= 32 && ascii < 128)
			{
				asciiStr += str.charAt(c);
			}
			else if (typeof Ant.asciiCharMap[ascii] != 'undefined')
			{
				asciiStr += Ant.asciiCharMap[ascii];
			}
		}

		return asciiStr;
	},

	/**
	 * Prevents the outline when an element is focused by the mouse.
	 *
	 * @param mixed elem Either an actual element or a jQuery collection.
	 */
	preventOutlineOnMouseFocus: function(elem)
	{
		var $elem = $(elem),
			namespace = '.preventOutlineOnMouseFocus';

		$elem.on('mousedown'+namespace, function() {
			$elem.addClass('no-outline');
			$elem.focus();
		})
		.on('keydown'+namespace+' blur'+namespace, function(event) {
			if (event.keyCode != Helper.SHIFT_KEY && event.keyCode != Helper.CTRL_KEY && event.keyCode != Helper.CMD_KEY)
				$elem.removeClass('no-outline');
		});
	},

	/**
	 * Creates a validation error list.
	 *
	 * @param array errors
	 * @return jQuery
	 */
	createErrorList: function(errors)
	{
		var $ul = $(document.createElement('ul')).addClass('errors');

		for (var i = 0; i < errors.length; i++)
		{
			var $li = $(document.createElement('li'));
			$li.appendTo($ul);
			$li.html(errors[i]);
		}

		return $ul;
	},

	/**
	 * Initializes any common UI elements in a given container.
	 *
	 * @param jQuery $container
	 */
	initUiElements: function($container)
	{
		$('.grid', $container).grid();
		$('.pane', $container).pane();
		$('.info', $container).infoicon();
		$('.checkbox-select', $container).checkboxselect();
		$('.fieldtoggle', $container).fieldtoggle();
		$('.lightswitch', $container).lightswitch();
		$('.nicetext', $container).nicetext();
		$('.pill', $container).pill();
		$('.formsubmit', $container).formsubmit();
		$('.menubtn', $container).menubtn();

		// Make placeholders work for IE9, too.
		//$('input[type!=password], textarea', $container).placeholder();
	},

	_elementIndexClasses: {},
	_elementSelectorModalClasses: {},

	/**
	 * Registers an element index class for a given element type.
	 *
	 * @param string elementType
	 * @param function func
	 */
	registerElementIndexClass: function(elementType, func)
	{
		if (typeof this._elementIndexClasses[elementType] != 'undefined')
		{
			throw 'An element index class has already been registered for the element type “'+elementType+'”.';
		}

		this._elementIndexClasses[elementType] = func;
	},


	/**
	 * Registers an element selector modal class for a given element type.
	 *
	 * @param string elementType
	 * @param function func
	 */
	registerElementSelectorModalClass: function(elementType, func)
	{
		if (typeof this._elementSelectorModalClasses[elementType] != 'undefined')
		{
			throw 'An element selector modal class has already been registered for the element type “'+elementType+'”.';
		}

		this._elementSelectorModalClasses[elementType] = func;
	},

	/**
	 * Creates a new element index for a given element type.
	 *
	 * @param string elementType
	 * @param mixed  $container
	 * @param object settings
	 * @return BaseElementIndex
	 */
	createElementIndex: function(elementType, $container, settings)
	{
		if (typeof this._elementIndexClasses[elementType] != 'undefined')
		{
			var func = this._elementIndexClasses[elementType];
		}
		else
		{
			var func = Ant.BaseElementIndex;
		}

		return new func(elementType, $container, settings);
	},

	/**
	 * Creates a new element selector modal for a given element type.
	 *
	 * @param string elementType
	 * @param object settings
	 */
	createElementSelectorModal: function(elementType, settings)
	{
		if (typeof this._elementSelectorModalClasses[elementType] != 'undefined')
		{
			var func = this._elementSelectorModalClasses[elementType];
		}
		else
		{
			var func = Ant.BaseElementSelectorModal;
		}

		return new func(elementType, settings);
	},

	/**
	 * Retrieves a value from localStorage if it exists.
	 *
	 * @param string key
	 * @param mixed defaultValue
	 */
	getLocalStorage: function(key, defaultValue)
	{
		key = 'Ant-'+Ant.siteUid+'.'+key;

		if (typeof localStorage != 'undefined' && typeof localStorage[key] != 'undefined')
		{
			return JSON.parse(localStorage[key]);
		}
		else
		{
			return defaultValue;
		}
	},

	/**
	 * Saves a value to localStorage.
	 *
	 * @param string key
	 * @param mixed value
	 */
	setLocalStorage: function(key, value)
	{
		if (typeof localStorage != 'undefined')
		{
			key = 'Ant-'+Ant.siteUid+'.'+key;

			// localStorage might be filled all the way up.
			// Especially likely if this is a private window in Safari 8+, where localStorage technically exists,
			// but has a max size of 0 bytes.
			try
			{
				localStorage[key] = JSON.stringify(value);
			}
			catch(e) {}
		}
	},

	/**
	 * Returns element information from it's HTML.
	 *
	 * @param element
	 * @returns object
	 */
	getElementInfo: function(element)
	{
		var $element = $(element);

		if (!$element.hasClass('element'))
		{
			$element = $element.find('.element:first');
		}

		var info = {
			id:       $element.data('id'),
			locale:   $element.data('locale'),
			label:    $element.data('label'),
			status:   $element.data('status'),
			url:      $element.data('url'),
			hasThumb: $element.hasClass('hasthumb'),
			$element: $element
		};

		return info;
	},

	/**
	 * Shows an element editor HUD.
	 *
	 * @param object $element
	 */
	showElementEditor: function($element)
	{
		if (Helper.hasAttr($element, 'data-editable') && !$element.hasClass('disabled') && !$element.hasClass('loading'))
		{
			new Ant.ElementEditor($element);
		}
	}
});


/**
 * Element index class
 */
Ant.BaseElementIndex = Helper.Base.extend(
{
	// Properties
	// =========================================================================

	initialized: false,
	elementType: null,

	instanceState: null,
	sourceStates: null,
	sourceStatesStorageKey: null,

	searchTimeout: null,
	elementSelect: null,
	sourceSelect: null,
	structureTableSort: null,

	isIndexBusy: false,

	selectable: false,
	multiSelect: false,
	actions: null,
	actionsHeadHtml: null,
	actionsFootHtml: null,
	showingActionTriggers: false,
	_$triggers: null,

	$container: null,
	$main: null,
	$scroller: null,
	$toolbar: null,
	$toolbarTableRow: null,
	toolbarOffset: null,
	$selectAllContainer: null,
	$selectAllCheckbox: null,
	$search: null,
	searching: false,
	$clearSearchBtn: null,
	$mainSpinner: null,

	$statusMenuBtn: null,
	statusMenu: null,
	status: null,

	$localeMenuBtn: null,
	localeMenu: null,
	locale: null,

	$sortMenuBtn: null,
	sortMenu: null,
	$sortAttributesList: null,
	$sortDirectionsList: null,
	$scoreSortAttribute: null,
	$structureSortAttribute: null,

	$viewModeBtnTd: null,
	$viewModeBtnContainer: null,
	viewModeBtns: null,
	viewMode: null,

	$loadingMoreSpinner: null,
	$sidebar: null,
	$sidebarButtonContainer: null,
	showingSidebar: null,
	sourceKey: null,
	sourceViewModes: null,
	$source: null,
	$elements: null,
	$table: null,
	$elementContainer: null,
	$checkboxes: null,

	_totalVisible: null,
	_morePending: false,
	_totalVisiblePostStructureTableDraggee: null,
	_morePendingPostStructureTableDraggee: false,
	loadingMore: false,

	// Public methods
	// =========================================================================

	/**
	 * Constructor
	 */
	init: function(elementType, $container, settings)
	{
		this.elementType = elementType;
		this.$container = $container;
		this.setSettings(settings, Ant.BaseElementIndex.defaults);

		// Set the state objects
		this.instanceState = {
			selectedSource: null
		};

		this.sourceStates = {};

		// Instance states (selected source) are stored by a custom storage key defined in the settings
		if (this.settings.storageKey)
		{
			$.extend(this.instanceState, Ant.getLocalStorage(this.settings.storageKey), {});
		}

		// Source states (view mode, etc.) are stored by the element type and context
		this.sourceStatesStorageKey = 'BaseElementIndex.'+this.elementType+'.'+this.settings.context;
		$.extend(this.sourceStates, Ant.getLocalStorage(this.sourceStatesStorageKey, {}));

		// Find the DOM elements
		this.$main = this.$container.find('.main');
		this.$toolbar = this.$container.find('.toolbar:first');
		this.$toolbarTableRow = this.$toolbar.children('table').children('tbody').children('tr');
		this.$statusMenuBtn = this.$toolbarTableRow.find('.statusmenubtn:first');
		this.$localeMenuBtn = this.$toolbarTableRow.find('.localemenubtn:first');
		this.$sortMenuBtn = this.$toolbarTableRow.find('.sortmenubtn:first');
		this.$search = this.$toolbarTableRow.find('.search:first input:first');
		this.$clearSearchBtn = this.$toolbarTableRow.find('.search:first > .clear');
		this.$mainSpinner = this.$toolbar.find('.spinner:first');
		this.$loadingMoreSpinner = this.$container.find('.spinner.loadingmore')
		//this.$sidebar = jQuery('.sidebar2:first');
		//this.$sidebarButtonContainer = this.$sidebar.children('.buttons');
		this.$elements = this.$container.find('.elements:first');

		/*if (!this.$sidebarButtonContainer.length)
		{
			this.$sidebarButtonContainer = $('<div class="buttons"/>').prependTo(this.$sidebar);
		}*/

		//this.showingSidebar = (this.$sidebar.length && !this.$sidebar.hasClass('hidden'));

		this.$viewModeBtnTd = this.$toolbarTableRow.find('.viewbtns:first');
		this.$viewModeBtnContainer = $('<div class="btngroup fullwidth"/>').appendTo(this.$viewModeBtnTd);

		/*if (this.settings.context == 'index' && !Helper.isMobileBrowser(true))
		{
			this.addListener(Helper.$win, 'scroll resize', 'updateFixedToolbar');
		}*/

		// Initialize the sources
		// ---------------------------------------------------------------------

		/*var $sources = this._getSourcesInList(this.$sidebar.children('nav').children('ul'));

		// No source, no party.
		if ($sources.length == 0)
		{
			return;
		}

		// The source selector
		this.sourceSelect = new Helper.Select(null, {
			multi:             false,
			allowEmpty:        false,
			vertical:          true,
			onSelectionChange: $.proxy(this, 'onSourceSelectionChange')
		});*/
		
		//this._initSources($sources);
		//this.initSource();

		// Initialize the locale menu button
		// ---------------------------------------------------------------------

		// Is there a locale menu?
		/*if (this.$localeMenuBtn.length)
		{
			this.localeMenu = this.$localeMenuBtn.menubtn().data('menubtn').menu;

			// Figure out the initial locale
			var $option = this.localeMenu.$options.filter('.sel:first');

			if (!$option.length)
			{
				$option = this.localeMenu.$options.first();
			}

			if ($option.length)
			{
				this.locale = $option.data('locale');
			}
			else
			{
				// No locale options -- they must not have any locale permissions
				this.settings.criteria = { id: '0' };
			}

			this.localeMenu.on('optionselect', $.proxy(this, 'onLocaleChange'));

			if (this.locale)
			{
				// Do we have a different locale stored in localStorage?
				var storedLocale = Ant.getLocalStorage('BaseElementIndex.locale');

				if (storedLocale && storedLocale != this.locale)
				{
					// Is that one available here?
					var $storedLocaleOption = this.localeMenu.$options.filter('[data-locale="'+storedLocale+'"]:first');

					if ($storedLocaleOption.length)
					{
						// Todo: switch this to localeMenu.selectOption($storedLocaleOption) once Menu is updated to support that
						$storedLocaleOption.trigger('click');
					}
				}
			}
		}*/

		// Is there a sort menu?
		/*if (this.$sortMenuBtn.length)
		{
			this.sortMenu = this.$sortMenuBtn.menubtn().data('menubtn').menu;
			this.$sortAttributesList = this.sortMenu.$container.children('.sort-attributes');
			this.$sortDirectionsList = this.sortMenu.$container.children('.sort-directions');

			this.sortMenu.on('optionselect', $.proxy(this, 'onSortChange'));
		}*/

		this.onAfterHtmlInit();
/*
		if (this.settings.context == 'index')
		{
			this.$scroller = Helper.$win;
		}
		else
		{
			this.$scroller = this.$main;
		}

		// Select the initial source
		var source = this.getDefaultSourceKey();

		if (source)
		{
			var $source = this.getSourceByKey(source);

			if ($source)
			{
				// Expand any parent sources
				var $parentSources = $source.parentsUntil('.sidebar', 'li');
				$parentSources.not(':first').addClass('expanded');
			}
		}

		if (!source || !$source)
		{
			// Select the first source by default
			var $source = this.$sources.first();
		}*/

		// Load up the elements!
		this.initialized = true;
	
		//this.sourceSelect.selectItem();
		$.proxy(this, 'onSourceSelectionChange')();
		//this.sourceSelect.onSelectionChange();
/*
		// Status changes
		if (this.$statusMenuBtn.length)
		{
			this.statusMenu = this.$statusMenuBtn.menubtn().data('menubtn').menu;
			this.statusMenu.on('optionselect', $.proxy(this, 'onStatusChange'));
		}

		this.addListener(this.$search, 'textchange', $.proxy(function()
		{
			if (!this.searching && this.$search.val())
			{
				this.onStartSearching();
			}
			else if (this.searching && !this.$search.val())
			{
				this.onStopSearching();
			}

			if (this.searchTimeout)
			{
				clearTimeout(this.searchTimeout);
			}

			this.searchTimeout = setTimeout($.proxy(this, 'updateElements'), 500);
		}, this));

		this.addListener(this.$clearSearchBtn, 'click', $.proxy(function()
		{
			this.$search.val('');

			if (this.searchTimeout)
			{
				clearTimeout(this.searchTimeout);
			}

			if (!Helper.isMobileBrowser(true))
			{
				this.$search.focus();
			}

			this.onStopSearching();

			this.updateElements();

		}, this))

		// Auto-focus the Search box
		if (!Helper.isMobileBrowser(true))
		{
			this.$search.focus();
		}*/
	},

	get $sources()
	{
		if (!this.sourceSelect)
		{
			return undefined;
		}

		return this.sourceSelect.$items;
	},

	get totalVisible()
	{
		if (this._isStructureTableDraggingLastElements())
		{
			return this._totalVisiblePostStructureTableDraggee;
		}
		else
		{
			return this._totalVisible;
		}
	},

	get morePending()
	{
		if (this._isStructureTableDraggingLastElements())
		{
			return this._morePendingPostStructureTableDraggee;
		}
		else
		{
			return this._morePending;
		}
	},

	updateFixedToolbar: function()
	{
		return;
		if (!this.toolbarOffset)
		{
			this.toolbarOffset = this.$toolbar.offset().top;

			if (!this.toolbarOffset)
			{
				return;
			}
		}

		this.updateFixedToolbar._scrollTop = Helper.$win.scrollTop();

		if (this.updateFixedToolbar._scrollTop > this.toolbarOffset - 7)
		{
			if (!this.$toolbar.hasClass('fixed'))
			{
				this.$elements.css('padding-top', (this.$toolbar.outerHeight() + 24));
				this.$toolbar.addClass('fixed');
			}

			this.$toolbar.css('width', this.$main.width());
		}
		else
		{
			if (this.$toolbar.hasClass('fixed'))
			{
				this.$toolbar.removeClass('fixed');
				this.$toolbar.css('width', '');
				this.$elements.css('padding-top', '');
			}
		}
	},

	initSource: function($source)
	{
		this.sourceSelect.addItems($source);
		//this.initSourceToggle($source);
	},

	initSourceToggle: function($source)
	{
		var $toggle = this._getSourceToggle($source);

		if ($toggle.length)
		{
			this.addListener($toggle, 'click', '_onToggleClick');
		}
	},

	deinitSource: function($source)
	{
		this.sourceSelect.removeItems($source);
		this.deinitSourceToggle($source);
	},

	deinitSourceToggle: function($source)
	{
		var $toggle = this._getSourceToggle($source);

		if ($toggle.length)
		{
			this.removeListener($toggle, 'click');
		}
	},

	getDefaultSourceKey: function()
	{
		return this.instanceState.selectedSource;
	},

	onSourceSelectionChange: function()
	{
		// If the selected source was just removed (maybe because its parent was collapsed),
		// there won't be a selected source
		/*if (!this.sourceSelect.totalSelected)
		{
			this.sourceSelect.selectItem(this.$sources.first());
			return;
		}*/

		//if (this.selectSource(this.sourceSelect.$selectedItems))
		{
			this.updateElements();
		}
	},

	onStartSearching: function()
	{
		// Show the clear button and add/select the Score sort option
		this.$clearSearchBtn.removeClass('hidden');

		if (!this.$scoreSortAttribute)
		{
			this.$scoreSortAttribute = $('<li><a data-attr="score">'+Ant.t('Score')+'</a></li>');
			this.sortMenu.addOptions(this.$scoreSortAttribute.children());
		}

		this.$scoreSortAttribute.prependTo(this.$sortAttributesList);
		this.setSortAttribute('score');
		this.getSortAttributeOption('structure').addClass('disabled');

		this.searching = true;
	},

	onStopSearching: function()
	{
		// Hide the clear button and Score sort option
		this.$clearSearchBtn.addClass('hidden');

		this.$scoreSortAttribute.detach();
		this.getSortAttributeOption('structure').removeClass('disabled');
		this.setStoredSortOptionsForSource();

		this.searching = false;
	},

	setInstanceState: function(key, value)
	{
		if (typeof key == 'object')
		{
			$.extend(this.instanceState, key);
		}
		else
		{
			this.instanceState[key] = value;
		}

		// Store it in localStorage too?
		if (this.settings.storageKey)
		{
			Ant.setLocalStorage(this.settings.storageKey, this.instanceState);
		}
	},

	getSourceState: function(source, key, defaultValue)
	{
		if (typeof this.sourceStates[source] == 'undefined')
		{
			// Set it now so any modifications to it by whoever's calling this will be stored.
			this.sourceStates[source] = {};
		}

		if (typeof key == 'undefined')
		{
			return this.sourceStates[source];
		}
		else if (typeof this.sourceStates[source][key] != 'undefined')
		{
			return this.sourceStates[source][key];
		}
		else
		{
			return (typeof defaultValue != 'undefined' ? defaultValue : null);
		}
	},

	getSelectedSourceState: function(key, defaultValue)
	{
		return this.getSourceState(this.instanceState.selectedSource, key, defaultValue);
	},

	setSelecetedSourceState: function(key, value)
	{
		var viewState = this.getSelectedSourceState();

		if (typeof key == 'object')
		{
			$.extend(viewState, key);
		}
		else
		{
			viewState[key] = value;
		}

		this.sourceStates[this.instanceState.selectedSource] = viewState;

		// Store it in localStorage too
		Ant.setLocalStorage(this.sourceStatesStorageKey, this.sourceStates);
	},

	/**
	 * Returns the data that should be passed to the elementIndex/getElements controller action
	 * when loading the first batch of elements.
	 */
	getControllerData: function()
	{
		var data = {
			context:             this.settings.context,
			elementType:         this.elementType,
			criteria:            $.extend({ status: this.status, locale: this.locale }, this.settings.criteria),
			disabledElementIds:  this.settings.disabledElementIds,
			source:              this.instanceState.selectedSource,
			status:              this.status,
			viewState:           this.getSelectedSourceState(),
			search:              (this.$search ? this.$search.val() : null)
		};

		// Possible that the order/sort isn't entirely accurate if we're sorting by Score
		data.viewState.order = this.getSelectedSortAttribute();
		data.viewState.sort = this.getSelectedSortDirection();

		if (
			this.getSelectedSourceState('mode') == 'table' &&
			this.getSelectedSortAttribute() == 'structure'
		)
		{
			data.collapsedElementIds = this.instanceState.collapsedElementIds;
		}

		return data;
	},

	updateElements: function()
	{
		// Ignore if we're not fully initialized yet
		if (!this.initialized)
		{
			return;
		}

		// Prep the UI
		// -------------------------------------------------------------

		this.setIndexBusy();
		this.removeListener(this.$scroller, 'scroll');

		if (this.getSelectedSourceState('mode') == 'table' && this.$table)
		{
			Ant.cp.$collapsibleTables = Ant.cp.$collapsibleTables.not(this.$table);
		}

		// Fetch the elements
		// -------------------------------------------------------------

		var data = this.getControllerData();

		//Ant.postActionRequest('elementIndex/getElements', data, $.proxy(function(response, textStatus)
		//{
			this.setIndexAvailable();

			//if (textStatus == 'success')
			//{
				// Cleanup
				// -------------------------------------------------------------

				this._prepForNewElements();

				// Selectable setup
				// -------------------------------------------------------------
/*
				if (this.settings.context == 'index' && response.actions && response.actions.length)
				{
					this.actions = response.actions;
					this.actionsHeadHtml = response.actionsHeadHtml;
					this.actionsFootHtml = response.actionsFootHtml;
				}
				else
				{
					this.actions = this.actionsHeadHtml = this.actionsFootHtml = null;
				}*/

				this.selectable = (this.actions || this.settings.selectable);

				// Update the view with the new container + elements HTML
				// -------------------------------------------------------------

				//this.$elements.html(response.html);
				//this.$scroller.scrollTop(0);

				//if (this.getSelectedSourceState('mode') == 'table')
				//{
					this.$table = this.$elements;//.find('table:first');
					//Ant.cp.$collapsibleTables = Ant.cp.$collapsibleTables.add(this.$table);
				//}

				// Find the new container
				this.$elementContainer = this.getElementContainer();

				// Get the new elements
				var $newElements = this.$elementContainer.children();

				// Initialize the selector stuff and the structure table sorter
				this._setupNewElements($newElements);

				//this._onUpdateElements(response, false, $newElements);

				if (
					this.getSelectedSourceState('mode') == 'table' &&
					this.getSelectedSortAttribute() == 'structure'
				)
				{
					// Listen for toggle clicks
					this.addListener(this.$elementContainer, 'click', function(ev)
					{
						var $target = $(ev.target);

						if ($target.hasClass('toggle'))
						{
							if (this._collapseElement($target) === false)
							{
								this._expandElement($target);
							}
						}
					});
				}

				// Listen for double-clicks
				if (this.settings.context == 'index')
				{
					this.addListener(this.$elementContainer, 'dblclick', function(ev)
					{
						var $target = $(ev.target);

						if ($target.prop('nodeName') == 'A')
						{
							// Let the link do its thing
							return;
						}

						if ($target.hasClass('element'))
						{
							var $element = $target;
						}
						else
						{
							var $element = $target.closest('.element');

							if (!$element.length)
							{
								return;
							}
						}

						if (Helper.hasAttr($element, 'data-editable'))
						{
							new Ant.ElementEditor($element);
						}
					});
				}
			//}

		//}, this));
	},

	showActionTriggers: function()
	{
		// Ignore if they're already shown
		if (this.showingActionTriggers)
		{
			return;
		}

		// Hide any toolbar inputs
		this.$toolbarTableRow.children().not(this.$selectAllContainer).addClass('hidden');

		if (!this._$triggers)
		{
			this._createTriggers();
		}
		else
		{
			this._$triggers.insertAfter(this.$selectAllContainer);
		}

		this.showingActionTriggers = true;
	},

	handleActionTriggerSubmit: function(ev)
	{
		ev.preventDefault();

		var $form = $(ev.currentTarget);

		// Make sure Ant.ElementActionTrigger isn't overriding this
		if ($form.hasClass('disabled') || $form.data('custom-handler'))
		{
			return;
		}

		var actionHandle = $form.data('action'),
			params = Helper.getPostData($form);

		this.submitAction(actionHandle, params);
	},

	handleMenuActionTriggerSubmit: function(ev)
	{
		var $option = $(ev.option);

		// Make sure Ant.ElementActionTrigger isn't overriding this
		if ($option.hasClass('disabled') || $option.data('custom-handler'))
		{
			return;
		}

		var actionHandle = $option.data('action');
		this.submitAction(actionHandle);
	},

	submitAction: function(actionHandle, params)
	{
		// Make sure something's selected
		var totalSelected = this.elementSelect.totalSelected,
			totalItems = this.elementSelect.$items.length;

		if (totalSelected == 0)
		{
			return;
		}

		// Find the action
		for (var i = 0; i < this.actions.length; i++)
		{
			if (this.actions[i].handle == actionHandle)
			{
				var action = this.actions[i];
				break;
			}
		}

		if (!action || (action.confirm && !confirm(action.confirm)))
		{
			return;
		}

		// Get ready to submit
		var data = $.extend(this.getControllerData(), params, {
			elementAction: actionHandle,
			elementIds:    this.getSelectedElementIds()
		});

		// Do it
		this.setIndexBusy();

		Ant.postActionRequest('elementIndex/performAction', data, $.proxy(function(response, textStatus)
		{
			this.setIndexAvailable();

			if (textStatus == 'success')
			{
				if (response.success)
				{
					this._prepForNewElements();
					this.$elementContainer.html('');
					this.elementSelect = this.createElementSelect();

					var $newElements = $(response.html).appendTo(this.$elementContainer);

					// Initialize the selector stuff and the structure table sorter
					this._setupNewElements($newElements);

					// There may be less elements now if some had been lazy-loaded before. If that's the case and all of
					// the elements were selected, we don't want to give the user the impression that all of the same
					// elements are still selected.
					if (totalItems <= 50 || totalSelected < totalItems)
					{
						for (var i = 0; i < data.elementIds.length; i++)
						{
							var $element = this.getElementById(data.elementIds[i]);

							if ($element)
							{
								this.elementSelect.selectItem($element);
							}
						}
					}

					this._onUpdateElements(response, false, $newElements);

					if (response.message)
					{
						Ant.cp.displayNotice(response.message);
					}
				}
				else
				{
					Ant.cp.displayError(response.message);
				}
			}
		}, this));
	},

	hideActionTriggers: function()
	{
		// Ignore if there aren't any
		if (!this.showingActionTriggers)
		{
			return;
		}

		this._$triggers.detach();

		this.$toolbarTableRow.children().not(this.$selectAllContainer).removeClass('hidden');

		this.showingActionTriggers = false;
	},

	updateActionTriggers: function()
	{
		// Do we have an action UI to update?
		if (this.actions)
		{
			var totalSelected = this.elementSelect.totalSelected;

			if (totalSelected != 0)
			{
				if (totalSelected == this.elementSelect.$items.length)
				{
					this.$selectAllCheckbox.removeClass('indeterminate');
					this.$selectAllCheckbox.addClass('checked');
				}
				else
				{
					this.$selectAllCheckbox.addClass('indeterminate');
					this.$selectAllCheckbox.removeClass('checked');
				}

				this.showActionTriggers();
			}
			else
			{
				this.$selectAllCheckbox.removeClass('indeterminate checked');
				this.hideActionTriggers();
			}
		}
	},

	/**
	 * Checks if the user has reached the bottom of the scroll area, and if so, loads the next batch of elemets.
	 */
	maybeLoadMore: function()
	{
		if (this.canLoadMore())
		{
			this.loadMore();
		}
	},

	/**
	 * Returns whether the user has reached the bottom of the scroll area.
	 */
	canLoadMore: function()
	{
		if (!this.morePending)
		{
			return false;
		}

		// Check if the user has reached the bottom of the scroll area
		if (this.$scroller[0] == Helper.$win[0])
		{
			var winHeight = Helper.$win.innerHeight(),
				winScrollTop = Helper.$win.scrollTop(),
				bodHeight = Helper.$bod.height();

			return (winHeight + winScrollTop >= bodHeight);
		}
		else
		{
			var containerScrollHeight = this.$scroller.prop('scrollHeight'),
				containerScrollTop = this.$scroller.scrollTop(),
				containerHeight = this.$scroller.outerHeight();

			return (containerScrollHeight - containerScrollTop <= containerHeight + 15);
		}
	},

	/**
	 * Loads the next batch of elements.
	 */
	loadMore: function()
	{
		if (!this.morePending || this.loadingMore)
		{
			return;
		}

		this.loadingMore = true;
		this.$loadingMoreSpinner.removeClass('hidden');
		this.removeListener(this.$scroller, 'scroll');

		var data = this.getLoadMoreData();

		Ant.postActionRequest('elementIndex/getMoreElements', data, $.proxy(function(response, textStatus)
		{
			this.loadingMore = false;
			this.$loadingMoreSpinner.addClass('hidden');

			if (textStatus == 'success')
			{
				var $newElements = $(response.html).appendTo(this.$elementContainer);

				if (this.actions || this.settings.selectable)
				{
					this.elementSelect.addItems($newElements.filter(':not(.disabled)'));
					this.updateActionTriggers();
				}

				if (this.structureTableSort)
				{
					this.structureTableSort.addItems($newElements);
				}

				this._onUpdateElements(response, true, $newElements);
			}

		}, this));
	},

	/**
	 * Returns the data that should be passed to the elementIndex/getMoreElements controller action
	 * when loading a subsequent batch of elements.
	 */
	getLoadMoreData: function()
	{
		var data = this.getControllerData();
		data.offset = this.totalVisible;

		// If we are dragging the last elements on the page,
		// tell the controller to only load elements positioned after the draggee.
		if (this._isStructureTableDraggingLastElements())
		{
			data.criteria.positionedAfter = this.structureTableSort.$targetItem.data('id');
		}

		return data;
	},

	/**
	 * Returns the element container.
	 */
	getElementContainer: function()
	{
		//if (this.getSelectedSourceState('mode') == 'table')
		//{
			return this.$table.children('tbody:first');
		//}
		//else
		//{
		//	return this.$elements.children('ul');
		//}
	},

	createElementSelect: function()
	{
		return new Helper.Select(this.$elementContainer, {
			multi:             (this.actions || this.settings.multiSelect),
			vertical:          (this.getSelectedSourceState('mode') != 'thumbs'),
			handle:            (this.settings.context == 'index' ? '.checkbox, .element' : null),
			filter:            ':not(a):not(.toggle)',
			checkboxMode:      (this.settings.context == 'index' && this.actions),
			onSelectionChange: $.proxy(this, 'onSelectionChange')
		});
	},

	getSelectedElementIds: function()
	{
		var $selectedItems = this.elementSelect.$selectedItems,
			ids = [];

		for (var i = 0; i < $selectedItems.length; i++)
		{
			ids.push($selectedItems.eq(i).data('id'));
		}

		return ids;
	},

	onUpdateElements: function(append, $newElements)
	{
		this.settings.onUpdateElements(append, $newElements);
	},

	onStatusChange: function(ev)
	{
		this.statusMenu.$options.removeClass('sel');
		var $option = $(ev.selectedOption).addClass('sel');
		this.$statusMenuBtn.html($option.html());

		this.status = $option.data('status');
		this.updateElements();
	},

	onLocaleChange: function(ev)
	{
		this.localeMenu.$options.removeClass('sel');
		var $option = $(ev.selectedOption).addClass('sel');
		this.$localeMenuBtn.html($option.html());

		this.locale = $option.data('locale');

		if (this.initialized)
		{
			// Remember this locale for later
			Ant.setLocalStorage('BaseElementIndex.locale', this.locale);

			// Update the elements
			this.updateElements();
		}
	},

	getSortAttributeOption: function(attr)
	{
		return this.$sortAttributesList.find('a[data-attr="'+attr+'"]:first');
	},

	getSelectedSortAttribute: function()
	{
//		return this.$sortAttributesList.find('a.sel:first').data('attr');
	},

	setSortAttribute: function(attr)
	{
		// Find the option (and make sure it actually exists)
		var $option = this.getSortAttributeOption(attr);

		if ($option.length)
		{
			this.$sortAttributesList.find('a.sel').removeClass('sel');
			$option.addClass('sel');

			var label = $option.text();
			this.$sortMenuBtn.attr('title', Ant.t('Sort by {attribute}', { attribute: label }));
			this.$sortMenuBtn.text(label);

			this.setSortDirection('asc');

			if (attr == 'score' || attr == 'structure')
			{
				this.$sortDirectionsList.find('a').addClass('disabled');
			}
			else
			{
				this.$sortDirectionsList.find('a').removeClass('disabled');
			}
		}
	},

	getSortDirectionOption: function(dir)
	{
		//return this.$sortDirectionsList.find('a[data-dir='+dir+']:first');
	},

	getSelectedSortDirection: function()
	{
//		return this.$sortDirectionsList.find('a.sel:first').data('dir');
	},

	setSortDirection: function(dir)
	{
		if (dir != 'desc')
		{
			dir = 'asc';
		}

		this.$sortMenuBtn.attr('data-icon', dir);
		//this.$sortDirectionsList.find('a.sel').removeClass('sel');
	//	this.getSortDirectionOption(dir).addClass('sel');
	},

	onSortChange: function(ev)
	{
		var $option = $(ev.selectedOption);

		if ($option.hasClass('disabled') || $option.hasClass('sel'))
		{
			return;
		}

		// Is this an attribute or a direction?
		if ($option.parent().parent().is(this.$sortAttributesList))
		{
			this.setSortAttribute($option.data('attr'));
		}
		else
		{
			this.setSortDirection($option.data('dir'));
		}

		// Save it to localStorage (unless we're sorting by score)
		var attr = this.getSelectedSortAttribute();

		if (attr != 'score')
		{
			this.setSelecetedSourceState({
				order: attr,
				sort: this.getSelectedSortDirection()
			});
		}

		this.updateElements();
	},

	getSourceByKey: function(key)
	{
		if (this.$sources)
		{
			var $source = this.$sources.filter('[data-key="'+key+'"]:first');

			if ($source.length)
			{
				return $source;
			}
		}
	},

	selectSource: function($source)
	{
	
		return true;
	},

	setStoredSortOptionsForSource: function()
	{
		// Default to whatever's first
		//this.setSortAttribute(this.$sortAttributesList.find('a:first').data('attr'));
		this.setSortDirection('asc');

		var storedSortAttr = this.getSelectedSourceState('order'),
			storedSortDir = this.getSelectedSourceState('sort');

		if (storedSortAttr)
		{
			this.setSortAttribute(storedSortAttr);
		}

		if (storedSortDir)
		{
			this.setSortDirection(storedSortDir);
		}
	},

	getViewModesForSource: function()
	{
		var viewModes = [
			{ mode: 'table', title: Ant.t('Display in a table'), icon: 'list' }
		];

		if (Helper.hasAttr(this.$source, 'data-has-thumbs'))
		{
			viewModes.push({ mode: 'thumbs', title: Ant.t('Display as thumbnails'), icon: 'grid' });
		}

		return viewModes;
	},

	onSelectSource: function()
	{
		this.settings.onSelectSource(this.sourceKey);
	},

	onAfterHtmlInit: function()
	{
		this.settings.onAfterHtmlInit()
	},

	onSelectionChange: function()
	{
		this.updateActionTriggers();
		this.settings.onSelectionChange();
	},

	doesSourceHaveViewMode: function(viewMode)
	{
		for (var i = 0; i < this.sourceViewModes.length; i++)
		{
			if (this.sourceViewModes[i].mode == viewMode)
			{
				return true;
			}
		}

		return false;
	},

	selectViewMode: function(viewMode, force)
	{
		// Make sure that the current source supports it
		if (!force && !this.doesSourceHaveViewMode(viewMode))
		{
			viewMode = this.sourceViewModes[0].mode;
		}

		// Has anything changed?
		if (viewMode == this.viewMode)
		{
			return;
		}

		// Deselect the previous view mode
		if (this.viewMode && typeof this.viewModeBtns[this.viewMode] != 'undefined')
		{
			this.viewModeBtns[this.viewMode].removeClass('active');
		}

		this.viewMode = viewMode;
		this.setSelecetedSourceState('mode', this.viewMode);

		if (typeof this.viewModeBtns[this.viewMode] != 'undefined')
		{
			this.viewModeBtns[this.viewMode].addClass('active');
		}
	},

	rememberDisabledElementId: function(elementId)
	{
		var index = $.inArray(elementId, this.settings.disabledElementIds);

		if (index == -1)
		{
			this.settings.disabledElementIds.push(elementId);
		}
	},

	forgetDisabledElementId: function(elementId)
	{
		var index = $.inArray(elementId, this.settings.disabledElementIds);

		if (index != -1)
		{
			this.settings.disabledElementIds.splice(index, 1);
		}
	},

	enableElements: function($elements)
	{
		$elements.removeClass('disabled').parents('.disabled').removeClass('disabled');

		for (var i = 0; i < $elements.length; i++)
		{
			var elementId = $($elements[i]).data('id');
			this.forgetDisabledElementId(elementId);
		}

		this.settings.onEnableElements($elements);
	},

	disableElements: function($elements)
	{
		$elements.removeClass('sel').addClass('disabled');

		for (var i = 0; i < $elements.length; i++)
		{
			var elementId = $($elements[i]).data('id');
			this.rememberDisabledElementId(elementId);
		}

		this.settings.onDisableElements($elements);
	},

	getElementById: function(elementId)
	{
		return this.$elementContainer.find('[data-id='+elementId+']:first');
	},

	enableElementsById: function(elementIds)
	{
		elementIds = $.makeArray(elementIds);

		for (var i = 0; i < elementIds.length; i++)
		{
			var elementId = elementIds[i],
				$element = this.getElementById(elementId);

			if ($element.length)
			{
				this.enableElements($element);
			}
			else
			{
				this.forgetDisabledElementId(elementId);
			}
		}
	},

	disableElementsById: function(elementIds)
	{
		elementIds = $.makeArray(elementIds);

		for (var i = 0; i < elementIds.length; i++)
		{
			var elementId = elementIds[i],
				$element = this.getElementById(elementId);

			if ($element.length)
			{
				this.disableElements($element);
			}
			else
			{
				this.rememberDisabledElementId(elementId);
			}
		}
	},

	addButton: function($button)
	{
		if (this.showingSidebar)
		{
			this.$sidebarButtonContainer.append($button);
		}
		else
		{
			$('<td class="thin"/>').prependTo(this.$toolbarTableRow).append($button);
		}
	},

	addCallback: function(currentCallback, newCallback)
	{
		return $.proxy(function() {
			if (typeof currentCallback == 'function')
			{
				currentCallback.apply(this, arguments);
			}
			newCallback.apply(this, arguments);
		}, this);
	},

	setIndexBusy: function()
	{
		this.$mainSpinner.removeClass('hidden');
		this.isIndexBusy = true;
	},

	setIndexAvailable: function()
	{
		this.$mainSpinner.addClass('hidden');
		this.isIndexBusy = false;
	},

	disable: function()
	{
		this.sourceSelect.disable();

		if (this.elementSelect)
		{
			this.elementSelect.disable();
		}

		this.base();
	},

	enable: function()
	{
		this.sourceSelect.enable();

		if (this.elementSelect)
		{
			this.elementSelect.enable();
		}

		this.base();
	},

	_getSourcesInList: function($list)
	{
		return $list.children('li').children('a');
	},

	_getChildSources: function($source)
	{
		var $list = $source.siblings('ul');
		return this._getSourcesInList($list);
	},

	_getSourceToggle: function($source)
	{
		return $source.siblings('.toggle');
	},

	_initSources: function($sources)
	{
		for (var i = 0; i < $sources.length; i++)
		{
			this.initSource($($sources[i]));
		}
	},

	_deinitSources: function($sources)
	{
		for (var i = 0; i < $sources.length; i++)
		{
			this.deinitSource($($sources[i]));
		}
	},

	_onToggleClick: function(ev)
	{
		this._toggleSource($(ev.currentTarget).prev('a'));
		ev.stopPropagation();
	},

	_toggleSource: function($source)
	{
		if ($source.parent('li').hasClass('expanded'))
		{
			this._collapseSource($source);
		}
		else
		{
			this._expandSource($source);
		}
	},

	_expandSource: function($source)
	{
		$source.parent('li').addClass('expanded');

		this.$sidebar.trigger('resize');

		var $childSources = this._getChildSources($source);
		this._initSources($childSources);
	},

	_collapseSource: function($source)
	{
		$source.parent('li').removeClass('expanded');

		this.$sidebar.trigger('resize');

		var $childSources = this._getChildSources($source);
		this._deinitSources($childSources);
	},

	_prepForNewElements: function()
	{
		if (this.actions)
		{
			// Get rid of the old action triggers regardless of whether the new batch has actions or not
			this.hideActionTriggers();
			this._$triggers = null;
		}

		// Reset the element select
		if (this.elementSelect)
		{
			this.elementSelect.destroy();
			delete this.elementSelect;
		}

		if (this.$selectAllContainer)
		{
			// Git rid of the old select all button
			this.$selectAllContainer.detach();
		}
	},

	_setupNewElements: function($newElements)
	{
		if (this.selectable)
		{
			// Initialize the element selector
			this.elementSelect = this.createElementSelect();
			this.elementSelect.addItems($newElements.filter(':not(.disabled)'));

			if (this.actions)
			{
				// First time?
				if (!this.$selectAllContainer)
				{
					// Create the select all button
					this.$selectAllContainer = $('<td class="selectallcontainer thin"/>');
					this.$selectAllBtn = $('<div class="btn"/>').appendTo(this.$selectAllContainer);
					this.$selectAllCheckbox = $('<div class="checkbox"/>').appendTo(this.$selectAllBtn);

					this.addListener(this.$selectAllBtn, 'click', function()
					{
						if (this.elementSelect.totalSelected == 0)
						{
							this.elementSelect.selectAll();
						}
						else
						{
							this.elementSelect.deselectAll();
						}
					});
				}
				else
				{
					// Reset the select all button
					this.$selectAllCheckbox.removeClass('indeterminate checked');
				}

				// Place the select all button at the beginning of the toolbar
				this.$selectAllContainer.prependTo(this.$toolbarTableRow);
			}
		}
		{
			// Create the sorter
			this.structureTableSort = new Ant.StructureTableSorter(this, $newElements, {
				moveElementUrl: this.settings.moveElementUrl,
				onSortChange: $.proxy(this, '_onStructureTableSortChange')
			});
		}
	},

	_onUpdateElements: function(response, append, $newElements)
	{
		$('head').append(response.headHtml);
		Helper.$bod.append(response.footHtml);

		if (this._isStructureTableDraggingLastElements())
		{
			this._totalVisiblePostStructureTableDraggee = response.totalVisible;
			this._morePendingPostStructureTableDraggee = response.more;
		}
		else
		{
			this._totalVisible = response.totalVisible;
			this._morePending = this._morePendingPostStructureTableDraggee = response.more;
		}

		if (this.morePending)
		{
			// Is there room to load more right now?
			if (this.canLoadMore())
			{
				this.loadMore();
			}
			else
			{
				this.addListener(this.$scroller, 'scroll', 'maybeLoadMore');
			}
		}

		if (this.getSelectedSourceState('mode') == 'table')
		{
			Ant.cp.updateResponsiveTables();
		}

		this.onUpdateElements(append, $newElements);
	},

	_collapseElement: function($toggle, force)
	{
		if (!force && !$toggle.hasClass('expanded'))
		{
			return false;
		}

		$toggle.removeClass('expanded');

		// Find and remove the descendant rows
		var $row = $toggle.parent().parent(),
			id = $row.data('id'),
			level = $row.data('level'),
			$nextRow = $row.next();

		while ($nextRow.length)
		{
			if (!Helper.hasAttr($nextRow, 'data-spinnerrow'))
			{
				if ($nextRow.data('level') <= level)
				{
					break;
				}

				if (this.elementSelect)
				{
					this.elementSelect.removeItems($nextRow);
				}

				if (this.structureTableSort)
				{
					this.structureTableSort.removeItems($nextRow)
				}

				this._totalVisible--;
			}

			var $nextNextRow = $nextRow.next();
			$nextRow.remove();
			$nextRow = $nextNextRow;
		}

		// Remember that this row should be collapsed
		if (!this.instanceState.collapsedElementIds)
		{
			this.instanceState.collapsedElementIds = [];
		}

		this.instanceState.collapsedElementIds.push(id);
		this.setInstanceState('collapsedElementIds', this.instanceState.collapsedElementIds);

		// Bottom of the index might be viewable now
		this.maybeLoadMore();
	},

	_expandElement: function($toggle, force)
	{
		if (!force && $toggle.hasClass('expanded'))
		{
			return false;
		}

		$toggle.addClass('expanded');

		// Remove this element from our list of collapsed elements
		if (this.instanceState.collapsedElementIds)
		{
			var $row = $toggle.parent().parent(),
				id = $row.data('id'),
				index = $.inArray(id, this.instanceState.collapsedElementIds);

			if (index != -1)
			{
				this.instanceState.collapsedElementIds.splice(index, 1);
				this.setInstanceState('collapsedElementIds', this.instanceState.collapsedElementIds);

				// Add a temporary row
				var $spinnerRow = this._createSpinnerRowAfter($row);

				// Update the elements
				var data = this.getControllerData();
				data.criteria.descendantOf = id;

				Ant.postActionRequest('elementIndex/getMoreElements', data, $.proxy(function(response, textStatus)
				{
					// Do we even care about this anymore?
					if (!$spinnerRow.parent().length)
					{
						return;
					}

					if (textStatus == 'success')
					{
						// Are there more descendants we didn't get in this batch?
						if (response.more)
						{
							// Remove all the elements after it
							var $nextRows = $spinnerRow.nextAll();

							if (this.elementSelect)
							{
								this.elementSelect.removeItems($nextRows);
							}

							if (this.structureTableSort)
							{
								this.structureTableSort.removeItems($nextRows)
							}

							$nextRows.remove();
							this._totalVisible -= $nextRows.length;
						}
						else
						{
							// Maintain the current 'more' status so
							response.more = this._morePending;
						}

						var $newElements = $(response.html);
						$spinnerRow.replaceWith($newElements);

						if (this.actions || this.settings.selectable)
						{
							this.elementSelect.addItems($newElements.filter(':not(.disabled)'));
							this.updateActionTriggers();
						}

						if (this.structureTableSort)
						{
							this.structureTableSort.addItems($newElements);
						}

						// Tweak response.totalVisible to account for the elements that come before them
						response.totalVisible += this._totalVisible;

						this._onUpdateElements(response, true, $newElements);
					}

				}, this));
			}
		}
	},

	_createSpinnerRowAfter: function($row)
	{
		return $(
			'<tr data-spinnerrow>' +
				'<td class="centeralign" colspan="'+$row.children().length+'">' +
					'<div class="spinner"/>' +
				'</td>' +
			'</tr>'
		).insertAfter($row);
	},

	_isStructureTableDraggingLastElements: function()
	{
		return (this.structureTableSort && this.structureTableSort.dragging && this.structureTableSort.draggingLastElements);
	},

	_createTriggers: function()
	{
		var triggers = [],
			safeMenuActions = [],
			destructiveMenuActions = [];

		for (var i = 0; i < this.actions.length; i++)
		{
			var action = this.actions[i];

			if (action.trigger)
			{
				var $form = $('<form id="'+action.handle+'-actiontrigger"/>')
					.data('action', action.handle)
					.append(action.trigger);

				this.addListener($form, 'submit', 'handleActionTriggerSubmit');
				triggers.push($form);
			}
			else
			{
				if (!action.destructive)
				{
					safeMenuActions.push(action);
				}
				else
				{
					destructiveMenuActions.push(action);
				}
			}
		}

		if (safeMenuActions.length || destructiveMenuActions.length)
		{
			var $menuTrigger = $('<form/>'),
				$btn = $('<div class="btn menubtn" data-icon="settings" title="'+Ant.t('Actions')+'"/>').appendTo($menuTrigger),
				$menu = $('<ul class="menu"/>').appendTo($menuTrigger),
				$safeList = this._createMenuTriggerList(safeMenuActions),
				$destructiveList = this._createMenuTriggerList(destructiveMenuActions);

			if ($safeList)
			{
				$safeList.appendTo($menu);
			}

			if ($safeList && $destructiveList)
			{
				$('<hr/>').appendTo($menu);
			}

			if ($destructiveList)
			{
				$destructiveList.appendTo($menu);
			}

			triggers.push($menuTrigger);
		}

		// Add a filler TD
		triggers.push('');

		this._$triggers = $();

		for (var i = 0; i < triggers.length; i++)
		{
			var $td = $('<td class="'+(i < triggers.length - 1 ? 'thin' : '')+'"/>').append(triggers[i]);
			this._$triggers = this._$triggers.add($td);
		}

		this._$triggers.insertAfter(this.$selectAllContainer);
		$('head').append(this.actionsHeadHtml);
		Helper.$bod.append(this.actionsFootHtml);

		Ant.initUiElements(this._$triggers);

		if ($btn)
		{
			$btn.data('menubtn').on('optionSelect', $.proxy(this, 'handleMenuActionTriggerSubmit'));
		}
	},

	_createMenuTriggerList: function(actions)
	{
		if (actions && actions.length)
		{
			var $ul = $('<ul/>');

			for (var i = 0; i < actions.length; i++)
			{
				var handle = actions[i].handle;
				$('<li><a id="'+handle+'-actiontrigger" data-action="'+handle+'">'+actions[i].name+'</a></li>').appendTo($ul);
			}

			return $ul;
		}
	}
},

// Static Properties
// =============================================================================

{
	defaults: {
		context: 'index',
		storageKey: null,
		criteria: null,
		disabledElementIds: [],
		selectable: false,
		multiSelect: false,
		onUpdateElements: $.noop,
		onSelectionChange: $.noop,
		onEnableElements: $.noop,
		onDisableElements: $.noop,
		onSelectSource: $.noop,
		onAfterHtmlInit: $.noop
	}
});


/**
 * Element Select input
 */
Ant.BaseElementSelectInput = Helper.Base.extend(
{
	elementSelect: null,
	elementSort: null,
	modal: null,

	$container: null,
	$elementsContainer: null,
	$elements: null,
	$addElementBtn: null,

	_initialized: false,

	init: function(settings)
	{
		// Normalize the settings and set them
		// ---------------------------------------------------------------------

		// Are they still passing in a bunch of arguments?
		if (!$.isPlainObject(settings))
		{
			// Loop through all of the old arguments and apply them to the settings
			var normalizedSettings = {},
				args = ['id', 'name', 'elementType', 'sources', 'criteria', 'sourceElementId', 'limit', 'modalStorageKey', 'fieldId'];

			for (var i = 0; i < args.length; i++)
			{
				if (typeof arguments[i] != typeof undefined)
				{
					normalizedSettings[args[i]] = arguments[i];
				}
				else
				{
					break;
				}
			}

			settings = normalizedSettings;
		}

		this.setSettings(settings, Ant.BaseElementSelectInput.defaults);

		// Apply the storage key prefix
		if (this.settings.modalStorageKey)
		{
			this.modalStorageKey = 'BaseElementSelectInput.'+this.settings.modalStorageKey;
		}

		// No reason for this to be sortable if we're only allowing 1 selection
		if (this.settings.limit == 1)
		{
			this.settings.sortable = false;
		}

		this.$container = this.getContainer();
		this.$elementsContainer = this.getElementsContainer();
		this.$addElementBtn = this.getAddElementsBtn();

		if (this.$addElementBtn && this.settings.limit == 1)
		{
			this.$addElementBtn
				.css('position', 'absolute')
				.css('top', 0)
				.css(Ant.left, 0);
		}

		this.initElementSelect();
		this.initElementSort();
		this.resetElements();

		if (this.$addElementBtn)
		{
			this.addListener(this.$addElementBtn, 'activate', 'showModal');
		}

		this._initialized = true;
	},

	get totalSelected()
	{
		return this.$elements.length;
	},

	getContainer: function()
	{
		return $('#'+this.settings.id);
	},

	getElementsContainer: function()
	{
		return this.$container.children('.elements');
	},

	getElements: function()
	{
		return this.$elementsContainer.children();
	},

	getAddElementsBtn: function()
	{
		return this.$container.children('.btn.add');
	},

	initElementSelect: function()
	{
		if (this.settings.selectable)
		{
			this.elementSelect = new Helper.Select({
				multi: this.settings.sortable,
				filter: ':not(.delete)'
			});
		}
	},

	initElementSort: function()
	{
		if (this.settings.sortable)
		{
			this.elementSort = new Helper.DragSort({
				container: this.$elementsContainer,
				filter: (this.settings.selectable ? $.proxy(function()
				{
					// Only return all the selected items if the target item is selected
					if (this.elementSort.$targetItem.hasClass('sel'))
					{
						return this.elementSelect.getSelectedItems();
					}
					else
					{
						return this.elementSort.$targetItem;
					}
				}, this) : null),
				ignoreHandleSelector: '.delete',
				collapseDraggees: true,
				magnetStrength: 4,
				helperLagBase: 1.5,
				onSortChange: (this.settings.selectable ? $.proxy(function() {
					this.elementSelect.resetItemOrder();
				}, this) : null)
			});
		}
	},

	canAddMoreElements: function()
	{
		return (!this.settings.limit || this.$elements.length < this.settings.limit);
	},

	updateAddElementsBtn: function()
	{
		if (this.canAddMoreElements())
		{
			this.enableAddElementsBtn();
		}
		else
		{
			this.disableAddElementsBtn();
		}
	},

	disableAddElementsBtn: function()
	{
		if (this.$addElementBtn && !this.$addElementBtn.hasClass('disabled'))
		{
			this.$addElementBtn.addClass('disabled');

			if (this.settings.limit == 1)
			{
				if (this._initialized)
				{
					this.$addElementBtn.velocity('fadeOut', Ant.BaseElementSelectInput.ADD_FX_DURATION);
				}
				else
				{
					this.$addElementBtn.hide();
				}
			}
		}
	},

	enableAddElementsBtn: function()
	{
		if (this.$addElementBtn && this.$addElementBtn.hasClass('disabled'))
		{
			this.$addElementBtn.removeClass('disabled');

			if (this.settings.limit == 1)
			{
				if (this._initialized)
				{
					this.$addElementBtn.velocity('fadeIn', Ant.BaseElementSelectInput.REMOVE_FX_DURATION);
				}
				else
				{
					this.$addElementBtn.show();
				}
			}
		}
	},

	resetElements: function()
	{
		this.$elements = $();
		this.addElements(this.getElements());
	},

	addElements: function($elements)
	{
		if (this.settings.selectable)
		{
			this.elementSelect.addItems($elements);
		}

		if (this.settings.sortable)
		{
			this.elementSort.addItems($elements);
		}

		if (this.settings.editable)
		{
			this.addListener($elements, 'dblclick', function(ev)
			{
				Ant.showElementEditor($(ev.currentTarget));
			});
		}

		$elements.find('.delete').on('click', $.proxy(function(ev)
		{
			this.removeElement($(ev.currentTarget).closest('.element'));
		}, this));

		this.$elements = this.$elements.add($elements);
		this.updateAddElementsBtn();
	},

	removeElements: function($elements)
	{
		if (this.settings.selectable)
		{
			this.elementSelect.removeItems($elements);
		}

		if (this.modal)
		{
			var ids = [];

			for (var i = 0; i < $elements.length; i++)
			{
				var id = $elements.eq(i).data('id');

				if (id)
				{
					ids.push(id);
				}
			}

			if (ids.length)
			{
				this.modal.elementIndex.enableElementsById(ids);
			}
		}

		// Disable the hidden input in case the form is submitted before this element gets removed from the DOM
		$elements.children('input').prop('disabled', true);

		this.$elements = this.$elements.not($elements);
		this.updateAddElementsBtn();

		this.onRemoveElements();
	},

	removeElement: function($element)
	{
		this.removeElements($element);
		this.animateElementAway($element, function() {
			$element.remove();
		});
	},

	animateElementAway: function($element, callback)
	{
		$element.css('z-index', 0);

		var animateCss = {
			opacity: -1
		};
		animateCss['margin-'+Ant.left] = -($element.outerWidth() + parseInt($element.css('margin-'+Ant.right)));

		$element.velocity(animateCss, Ant.BaseElementSelectInput.REMOVE_FX_DURATION, callback);
	},

	showModal: function()
	{
		// Make sure we haven't reached the limit
		if (!this.canAddMoreElements())
		{
			return;
		}

		if (!this.modal)
		{
			this.modal = this.createModal();
		}
		else
		{
			this.modal.show();
		}
	},

	createModal: function()
	{
		return Ant.createElementSelectorModal(this.settings.elementType, this.getModalSettings());
	},

	getModalSettings: function()
	{
		return $.extend({
			storageKey:         this.modalStorageKey,
			sources:            this.settings.sources,
			criteria:           this.settings.criteria,
			multiSelect:        (this.settings.limit != 1),
			disabledElementIds: this.getDisabledElementIds(),
			onSelect:           $.proxy(this, 'onModalSelect')
		}, this.settings.modalSettings);
	},

	getSelectedElementIds: function()
	{
		var ids = [];

		for (var i = 0; i < this.$elements.length; i++)
		{
			ids.push(this.$elements.eq(i).data('id'));
		}

		return ids;
	},

	getDisabledElementIds: function()
	{
		var ids = this.getSelectedElementIds();

		if (this.settings.sourceElementId)
		{
			ids.push(this.settings.sourceElementId);
		}

		return ids;
	},

	onModalSelect: function(elements)
	{
		if (this.settings.limit)
		{
			// Cut off any excess elements
			var slotsLeft = this.settings.limit - this.$elements.length;

			if (elements.length > slotsLeft)
			{
				elements = elements.slice(0, slotsLeft);
			}
		}

		this.selectElements(elements);
		this.updateDisabledElementsInModal();
	},

	selectElements: function(elements)
	{
		for (var i = 0; i < elements.length; i++)
		{
			var element = elements[i],
				$element = this.createNewElement(element);

			this.appendElement($element);
			this.addElements($element);
			this.animateElementIntoPlace(element.$element, $element);
		}

		this.onSelectElements(elements);
	},

	createNewElement: function(elementInfo)
	{
		var $element = elementInfo.$element.clone();

		// Make a couple tweaks
		$element.addClass('removable');
		$element.prepend('<input type="hidden" name="'+this.settings.name+'[]" value="'+elementInfo.id+'">' +
			'<a class="delete icon" title="'+Ant.t('Remove')+'"></a>');

		return $element;
	},

	appendElement: function($element)
	{
		$element.appendTo(this.$elementsContainer);
	},

	animateElementIntoPlace: function($modalElement, $inputElement)
	{
		var origOffset = $modalElement.offset(),
			destOffset = $inputElement.offset(),
			$helper = $inputElement.clone().appendTo(Helper.$bod);

		$inputElement.css('visibility', 'hidden');

		$helper.css({
			position: 'absolute',
			zIndex: 10000,
			top: origOffset.top,
			left: origOffset.left
		});

		var animateCss = {
			top: destOffset.top,
			left: destOffset.left
		};

		$helper.velocity(animateCss, Ant.BaseElementSelectInput.ADD_FX_DURATION, function() {
			$helper.remove();
			$inputElement.css('visibility', 'visible');
		});
	},

	updateDisabledElementsInModal: function()
	{
		if (this.modal.elementIndex)
		{
			this.modal.elementIndex.disableElementsById(this.getDisabledElementIds());
		}
	},

	getElementById: function(id)
	{
		for (var i = 0; i < this.$elements.length; i++)
		{
			var $element = this.$elements.eq(i);

			if ($element.data('id') == id)
			{
				return $element;
			}
		}
	},

	onSelectElements: function(elements)
	{
		this.trigger('selectElements', { elements: elements });
		this.settings.onSelectElements(elements);
	},

	onRemoveElements: function()
	{
		this.trigger('removeElements');
		this.settings.onRemoveElements();
	}
},
{
	ADD_FX_DURATION: 200,
	REMOVE_FX_DURATION: 200,

	defaults: {
		id: null,
		name: null,
		fieldId: null,
		elementType: null,
		sources: null,
		criteria: {},
		sourceElementId: null,
		limit: null,
		modalStorageKey: null,
		modalSettings: {},
		onSelectElements: $.noop,
		onRemoveElements: $.noop,
		sortable: true,
		selectable: true,
		editable: true
	}
});



/**
 * Admin table class
 */
Ant.AdminTable = Helper.Base.extend(
{
	settings: null,
	totalObjects: null,
	sorter: null,

	$noObjects: null,
	$table: null,
	$tbody: null,
	$deleteBtns: null,

	init: function(settings)
	{
		this.setSettings(settings, Ant.AdminTable.defaults);

		if (!this.settings.allowDeleteAll)
		{
			this.settings.minObjects = 1;
		}

		this.$noObjects = $(this.settings.noObjectsSelector);
		this.$table = $(this.settings.tableSelector);
		this.$tbody  = this.$table.children('tbody');
		this.totalObjects = this.$tbody.children().length;

		if (this.settings.sortable)
		{
			this.sorter = new Ant.DataTableSorter(this.$table, {
				onSortChange: $.proxy(this, 'reorderObjects')
			});
		}

		this.$deleteBtns = this.$table.find('.delete');
		this.addListener(this.$deleteBtns, 'click', 'handleDeleteBtnClick');

		this.updateUI();
	},

	addRow: function(row)
	{
		if (this.settings.maxObjects && this.totalObjects >= this.settings.maxObjects)
		{
			// Sorry pal.
			return;
		}

		var $row = $(row).appendTo(this.$tbody),
			$deleteBtn = $row.find('.delete');

		if (this.settings.sortable)
		{
			this.sorter.addItems($row);
		}

		this.$deleteBtns = this.$deleteBtns.add($deleteBtn);

		this.addListener($deleteBtn, 'click', 'handleDeleteBtnClick');
		this.totalObjects++;

		this.updateUI();
	},

	reorderObjects: function()
	{
		if (!this.settings.sortable)
		{
			return false;
		}

		// Get the new field order
		var ids = [];

		for (var i = 0; i < this.sorter.$items.length; i++)
		{
			var id = $(this.sorter.$items[i]).attr(this.settings.idAttribute);
			ids.push(id);
		}

		// Send it to the server
		var data = {
			ids: JSON.stringify(ids)
		};

		Ant.postActionRequest(this.settings.reorderAction, data, $.proxy(function(response, textStatus)
		{
			if (textStatus == 'success')
			{
				if (response.success)
				{
					Ant.cp.displayNotice(Ant.t(this.settings.reorderSuccessMessage));
				}
				else
				{
					Ant.cp.displayError(Ant.t(this.settings.reorderFailMessage));
				}
			}

		}, this));
	},

	handleDeleteBtnClick: function(event)
	{
		if (this.settings.minObjects && this.totalObjects <= this.settings.minObjects)
		{
			// Sorry pal.
			return;
		}

		var $row = $(event.target).closest('tr');

		if (this.confirmDeleteObject($row))
		{
			this.deleteObject($row);
		}
	},

	confirmDeleteObject: function($row)
	{
		var name = this.getObjectName($row);
		return confirm(Ant.t(this.settings.confirmDeleteMessage, { name: name }));
	},

	deleteObject: function($row)
	{
		var data = {
			id: this.getObjectId($row)
		};

		Ant.postActionRequest(this.settings.deleteAction, data, $.proxy(function(response, textStatus)
		{
			if (textStatus == 'success')
			{
				this.handleDeleteObjectResponse(response, $row);
			}
		}, this));
	},

	handleDeleteObjectResponse: function(response, $row)
	{
		var id = this.getObjectId($row),
			name = this.getObjectName($row);

		if (response.success)
		{
			$row.remove();
			this.totalObjects--;
			this.updateUI();
			this.onDeleteObject(id);

			Ant.cp.displayNotice(Ant.t(this.settings.deleteSuccessMessage, { name: name }));
		}
		else
		{
			Ant.cp.displayError(Ant.t(this.settings.deleteFailMessage, { name: name }));
		}
	},

	onDeleteObject: function(id)
	{
		this.settings.onDeleteObject(id);
	},

	getObjectId: function($row)
	{
		return $row.attr(this.settings.idAttribute);
	},

	getObjectName: function($row)
	{
		return $row.attr(this.settings.nameAttribute);
	},

	updateUI: function()
	{
		// Show the "No Whatever Exists" message if there aren't any
		if (this.totalObjects == 0)
		{
			this.$table.hide();
			this.$noObjects.removeClass('hidden');
		}
		else
		{
			this.$table.show();
			this.$noObjects.addClass('hidden');
		}

		// Disable the sort buttons if there's only one row
		if (this.settings.sortable)
		{
			var $moveButtons = this.$table.find('.move');

			if (this.totalObjects == 1)
			{
				$moveButtons.addClass('disabled');
			}
			else
			{
				$moveButtons.removeClass('disabled');
			}
		}

		// Disable the delete buttons if we've reached the minimum objects
		if (this.settings.minObjects && this.totalObjects <= this.settings.minObjects)
		{
			this.$deleteBtns.addClass('disabled');
		}
		else
		{
			this.$deleteBtns.removeClass('disabled');
		}

		// Hide the New Whatever button if we've reached the maximum objects
		if (this.settings.newObjectBtnSelector)
		{
			if (this.settings.maxObjects && this.totalObjects >= this.settings.maxObjects)
			{
				$(this.settings.newObjectBtnSelector).addClass('hidden');
			}
			else
			{
				$(this.settings.newObjectBtnSelector).removeClass('hidden');
			}
		}
	}
},
{
	defaults: {
		tableSelector: null,
		noObjectsSelector: null,
		newObjectBtnSelector: null,
		idAttribute: 'data-id',
		nameAttribute: 'data-name',
		sortable: false,
		allowDeleteAll: true,
		minObjects: 0,
		maxObjects: null,
		reorderAction: null,
		deleteAction: null,
		reorderSuccessMessage: 'New order saved.',
		reorderFailMessage:    'Couldn’t save new order.',
		confirmDeleteMessage:  'Are you sure you want to delete “{name}”?',
		deleteSuccessMessage:  '“{name}” deleted.',
		deleteFailMessage:     'Couldn’t delete “{name}”.',
		onDeleteObject: $.noop
	}
});


/**
 * Asset index class
 */
Ant.AssetIndex = Ant.BaseElementIndex.extend(
{
	$uploadButton: null,
	$uploadInput: null,
	$progressBar: null,
	$folders: null,

	uploader: null,
	promptHandler: null,
	progressBar: null,

	_uploadTotalFiles: 0,
	_uploadFileProgress: {},
	_uploadedFileIds: [],
	_currentUploaderSettings: {},

	_fileDrag: null,
	_folderDrag: null,
	_expandDropTargetFolderTimeout: null,
	_tempExpandedFolders: [],

	init: function(elementType, $container, settings)
	{
		this.base(elementType, $container, settings);

		if (this.settings.context == 'index')
		{
			this._initIndexPageMode();
		}
	},

	initSource: function($source)
	{
		//this.base($source);
/*
		this._createFolderContextMenu($source);

		if (this.settings.context == 'index')
		{
			if (this._folderDrag && this._getSourceLevel($source) > 1)
			{
				this._folderDrag.addItems($source.parent());
			}

			if (this._fileDrag)
			{
				this._fileDrag.updateDropTargets();
			}
		}*/
	},

	deinitSource: function($source)
	{
		this.base($source);

		// Does this source have a context menu?
		var contextMenu = $source.data('contextmenu');

		if (contextMenu)
		{
			contextMenu.destroy();
		}

		if (this.settings.context == 'index')
		{
			if (this._folderDrag && this._getSourceLevel($source) > 1)
			{
				this._folderDrag.removeItems($source.parent());
			}

			if (this._fileDrag)
			{
				this._fileDrag.updateDropTargets();
			}
		}
	},

	_getSourceLevel: function($source)
	{
		return $source.parentsUntil('nav', 'ul').length;
	},

	/**
	 * Initialize the index page-specific features
	 */
	_initIndexPageMode: function()
	{
		// Make the elements selectable
		this.settings.selectable = true;
		this.settings.multiSelect = true;

		var onDragStartProxy = $.proxy(this, '_onDragStart')
			onDropTargetChangeProxy = $.proxy(this, '_onDropTargetChange');

		// File dragging
		// ---------------------------------------------------------------------

		this._fileDrag = new Helper.DragDrop({
			activeDropTargetClass: 'sel',
			helperOpacity: 0.75,

			filter: $.proxy(function()
			{
				return this.elementSelect.getSelectedItems();
			}, this),

			helper: $.proxy(function($file)
			{
				return this._getFileDragHelper($file);
			}, this),

			dropTargets: $.proxy(function()
			{
				var targets = [];

				for (var i = 0; i < this.$sources.length; i++)
				{
					targets.push($(this.$sources[i]));
				}

				return targets;
			}, this),

			onDragStart: onDragStartProxy,
			onDropTargetChange: onDropTargetChangeProxy,
			onDragStop: $.proxy(this, '_onFileDragStop')
		});

		// Folder dragging
		// ---------------------------------------------------------------------

		this._folderDrag = new Helper.DragDrop(
		{
			activeDropTargetClass: 'sel',
			helperOpacity: 0.75,

			filter: $.proxy(function()
			{
				// Return each of the selected <a>'s parent <li>s, except for top level drag attempts.
				var $selected = this.sourceSelect.getSelectedItems(),
					draggees = [];

				for (var i = 0; i < $selected.length; i++)
				{
					var $source = $($selected[i]).parent();

					if ($source.hasClass('sel') && this._getSourceLevel($source) > 1)
					{
						draggees.push($source[0]);
					}
				}

				return $(draggees);
			}, this),

			helper: $.proxy(function($draggeeHelper)
			{
				var $helperSidebar = $('<div class="sidebar" style="padding-top: 0; padding-bottom: 0;"/>'),
					$helperNav = $('<nav/>').appendTo($helperSidebar),
					$helperUl = $('<ul/>').appendTo($helperNav);

				$draggeeHelper.appendTo($helperUl).removeClass('expanded');
				$draggeeHelper.children('a').addClass('sel');

				// Match the style
				$draggeeHelper.css({
					'padding-top':    this._folderDrag.$draggee.css('padding-top'),
					'padding-right':  this._folderDrag.$draggee.css('padding-right'),
					'padding-bottom': this._folderDrag.$draggee.css('padding-bottom'),
					'padding-left':   this._folderDrag.$draggee.css('padding-left')
				});

				return $helperSidebar;
			}, this),

			dropTargets: $.proxy(function()
			{
				var targets = [];

				// Tag the dragged folder and it's subfolders
				var draggedSourceIds = [];
				this._folderDrag.$draggee.find('a[data-key]').each(function()
				{
					draggedSourceIds.push($(this).data('key'));
				});

				for (var i = 0; i < this.$sources.length; i++)
				{
					var $source = $(this.$sources[i]);
					if (!Ant.inArray($source.data('key'), draggedSourceIds))
					{
						targets.push($source);
					}
				}

				return targets;
			}, this),

			onDragStart: onDragStartProxy,
			onDropTargetChange: onDropTargetChangeProxy,
			onDragStop: $.proxy(this, '_onFolderDragStop')
		});
	},

	/**
	 * On file drag stop
	 */
	_onFileDragStop: function()
	{
		if (this._fileDrag.$activeDropTarget && this._fileDrag.$activeDropTarget[0] != this.$source[0])
		{
			// Keep it selected
			var originatingSource = this.$source;

			var targetFolderId = this._getFolderIdFromSourceKey(this._fileDrag.$activeDropTarget.data('key')),
				originalFileIds = [],
				newFileNames = [];

			// For each file, prepare array data.
			for (var i = 0; i < this._fileDrag.$draggee.length; i++)
			{
				var originalFileId = Ant.getElementInfo(this._fileDrag.$draggee[i]).id,
					fileName = Ant.getElementInfo(this._fileDrag.$draggee[i]).url.split('/').pop();

				if (fileName.indexOf('?') !== -1)
				{
					fileName = fileName.split('?').shift();
				}

				originalFileIds.push(originalFileId);
				newFileNames.push(fileName);
			}

			// Are any files actually getting moved?
			if (originalFileIds.length)
			{
				this.setIndexBusy();

				this._positionProgressBar();
				this.progressBar.resetProgressBar();
				this.progressBar.setItemCount(originalFileIds.length);
				this.progressBar.showProgressBar();


				// For each file to move a separate request
				var parameterArray = [];
				for (i = 0; i < originalFileIds.length; i++)
				{
					parameterArray.push({
						fileId: originalFileIds[i],
						folderId: targetFolderId,
						fileName: newFileNames[i]
					});
				}

				// Define the callback for when all file moves are complete
				var onMoveFinish = $.proxy(function(responseArray)
				{
					this.promptHandler.resetPrompts();

					// Loop trough all the responses
					for (var i = 0; i < responseArray.length; i++)
					{
						var data = responseArray[i];

						// Push prompt into prompt array
						if (data.prompt)
						{
							this.promptHandler.addPrompt(data);
						}

						if (data.error)
						{
							alert(data.error);
						}
					}

					this.setIndexAvailable();
					this.progressBar.hideProgressBar();

					var performAfterMoveActions = function ()
					{
						// Select original source
						this.sourceSelect.selectItem(originatingSource);

						// Make sure we use the correct offset when fetching the next page
						this._totalVisible -= this._fileDrag.$draggee.length;

						// And remove the elements that have been moved away
						for (var i = 0; i < originalFileIds.length; i++)
						{
							$('[data-id=' + originalFileIds[i] + ']').remove();
						}

						this._collapseExtraExpandedFolders(targetFolderId);
					};

					if (this.promptHandler.getPromptCount())
					{
						// Define callback for completing all prompts
						var promptCallback = $.proxy(function(returnData)
						{
							var newParameterArray = [];

							// Loop trough all returned data and prepare a new request array
							for (var i = 0; i < returnData.length; i++)
							{
								if (returnData[i].choice == 'cancel')
								{
									continue;
								}

								// Find the matching request parameters for this file and modify them slightly
								for (var ii = 0; ii < parameterArray.length; ii++)
								{
									if (parameterArray[ii].fileName == returnData[i].fileName)
									{
										parameterArray[ii].action = returnData[i].choice;
										newParameterArray.push(parameterArray[ii]);
									}
								}
							}

							// Nothing to do, carry on
							if (newParameterArray.length == 0)
							{
								performAfterMoveActions.apply(this);
							}
							else
							{
								// Start working
								this.setIndexBusy();
								this.progressBar.resetProgressBar();
								this.progressBar.setItemCount(this.promptHandler.getPromptCount());
								this.progressBar.showProgressBar();

								// Move conflicting files again with resolutions now
								this._moveFile(newParameterArray, 0, onMoveFinish);
							}
						}, this);

						this._fileDrag.fadeOutHelpers();
						this.promptHandler.showBatchPrompts(promptCallback);
					}
					else
					{
						performAfterMoveActions.apply(this);
						this._fileDrag.fadeOutHelpers();
					}
				}, this);

				// Initiate the file move with the built array, index of 0 and callback to use when done
				this._moveFile(parameterArray, 0, onMoveFinish);

				// Skip returning dragees
				return;
			}
		}
		else
		{
			// Add the .sel class back on the selected source
			this.$source.addClass('sel');

			this._collapseExtraExpandedFolders();
		}

		this._fileDrag.returnHelpersToDraggees();
	},

	/**
	 * On folder drag stop
	 */
	_onFolderDragStop: function()
	{
		// Only move if we have a valid target and we're not trying to move into our direct parent
		if (
			this._folderDrag.$activeDropTarget &&
			this._folderDrag.$activeDropTarget.siblings('ul').children('li').filter(this._folderDrag.$draggee).length == 0
		)
		{
			var targetFolderId = this._getFolderIdFromSourceKey(this._folderDrag.$activeDropTarget.data('key'));

			this._collapseExtraExpandedFolders(targetFolderId);

			// Get the old folder IDs, and sort them so that we're moving the most-nested folders first
			var folderIds = [];

			for (var i = 0; i < this._folderDrag.$draggee.length; i++)
			{
				var $a = this._folderDrag.$draggee.eq(i).children('a'),
					folderId = this._getFolderIdFromSourceKey($a.data('key')),
					$source = this._getSourceByFolderId(folderId);

				// Make sure it's not already in the target folder
				if (this._getFolderIdFromSourceKey(this._getParentSource($source).data('key')) != targetFolderId)
				{
					folderIds.push(folderId);
				}
			}

			if (folderIds.length)
			{
				folderIds.sort();
				folderIds.reverse();

				this.setIndexBusy();
				this._positionProgressBar();
				this.progressBar.resetProgressBar();
				this.progressBar.setItemCount(folderIds.length);
				this.progressBar.showProgressBar();

				var responseArray = [];
				var parameterArray = [];

				for (var i = 0; i < folderIds.length; i++)
				{
					parameterArray.push({
						folderId: folderIds[i],
						parentId: targetFolderId
					});
				}

				// Increment, so to avoid displaying folder files that are being moved
				this.requestId++;

				/*
				 Here's the rundown:
				 1) Send all the folders being moved
				 2) Get results:
				   a) For all conflicting, receive prompts and resolve them to get:
				   b) For all valid move operations: by now server has created the needed folders
					  in target destination. Server returns an array of file move operations
				   c) server also returns a list of all the folder id changes
				   d) and the data-id of node to be removed, in case of conflict
				   e) and a list of folders to delete after the move
				 3) From data in 2) build a large file move operation array
				 4) Create a request loop based on this, so we can display progress bar
				 5) when done, delete all the folders and perform other maintenance
				 6) Champagne
				 */

				// This will hold the final list of files to move
				var fileMoveList = [];

				// These folders have to be deleted at the end
				var folderDeleteList = [];

				// This one tracks the changed folder ids
				var changedFolderIds = {};

				var removeFromTree = [];

				var onMoveFinish = $.proxy(function(responseArray)
				{
					this.promptHandler.resetPrompts();

					// Loop trough all the responses
					for (var i = 0; i < responseArray.length; i++)
					{
						var data = responseArray[i];

						// If succesful and have data, then update
						if (data.success)
						{
							if (data.transferList && data.deleteList && data.changedFolderIds)
							{
								for (var ii = 0; ii < data.transferList.length; ii++)
								{
									fileMoveList.push(data.transferList[ii]);
								}

								for (var ii = 0; ii < data.deleteList.length; ii++)
								{
									folderDeleteList.push(data.deleteList[ii]);
								}

								for (var oldFolderId in data.changedFolderIds)
								{
									changedFolderIds[oldFolderId] = data.changedFolderIds[oldFolderId];
								}

								removeFromTree.push(data.removeFromTree);
							}
						}

						// Push prompt into prompt array
						if (data.prompt)
						{
							this.promptHandler.addPrompt(data);
						}

						if (data.error)
						{
							alert(data.error);
						}
					}

					if (this.promptHandler.getPromptCount())
					{
						// Define callback for completing all prompts
						var promptCallback = $.proxy(function(returnData)
						{
							this.promptHandler.resetPrompts();
							this.setNewElementDataHtml('');

							var newParameterArray = [];

							// Loop trough all returned data and prepare a new request array
							for (var i = 0; i < returnData.length; i++)
							{
								if (returnData[i].choice == 'cancel')
								{
									continue;
								}

								parameterArray[0].action = returnData[i].choice;
								newParameterArray.push(parameterArray[0]);
							}

							// Start working on them lists, baby
							if (newParameterArray.length == 0)
							{
								$.proxy(this, '_performActualFolderMove', fileMoveList, folderDeleteList, changedFolderIds, removeFromTree)();
							}
							else
							{
								// Start working
								this.setIndexBusy();
								this.progressBar.resetProgressBar();
								this.progressBar.setItemCount(this.promptHandler.getPromptCount());
								this.progressBar.showProgressBar();

								// Move conflicting files again with resolutions now
								moveFolder(newParameterArray, 0, onMoveFinish);
							}
						}, this);

						this.promptHandler.showBatchPrompts(promptCallback);

						this.setIndexAvailable();
						this.progressBar.hideProgressBar();
					}
					else
					{
						$.proxy(this, '_performActualFolderMove', fileMoveList, folderDeleteList, changedFolderIds, removeFromTree, targetFolderId)();
					}
				}, this);

				var moveFolder = $.proxy(function(parameterArray, parameterIndex, callback)
				{
					if (parameterIndex == 0)
					{
						responseArray = [];
					}

					Ant.postActionRequest('assets/moveFolder', parameterArray[parameterIndex], $.proxy(function(data, textStatus)
					{
						parameterIndex++;
						this.progressBar.incrementProcessedItemCount(1);
						this.progressBar.updateProgressBar();

						if (textStatus == 'success')
						{
							responseArray.push(data);
						}

						if (parameterIndex >= parameterArray.length)
						{
							callback(responseArray);
						}
						else
						{
							moveFolder(parameterArray, parameterIndex, callback);
						}
					}, this));
				}, this);

				// Initiate the folder move with the built array, index of 0 and callback to use when done
				moveFolder(parameterArray, 0, onMoveFinish);

				// Skip returning dragees until we get the Ajax response
				return;
			}
		}
		else
		{
			// Add the .sel class back on the selected source
			this.$source.addClass('sel');

			this._collapseExtraExpandedFolders();
		}

		this._folderDrag.returnHelpersToDraggees();
	},

	/**
	 * Really move the folder. Like really. For real.
	 */
	_performActualFolderMove: function(fileMoveList, folderDeleteList, changedFolderIds, removeFromTree, targetFolderId)
	{
		this.setIndexBusy();
		this.progressBar.resetProgressBar();
		this.progressBar.setItemCount(1);
		this.progressBar.showProgressBar();

		var moveCallback = $.proxy(function(folderDeleteList, changedFolderIds, removeFromTree)
		{
			//Move the folders around in the tree
			var topFolderLi = $();
			var folderToMove = $();
			var topMovedFolderId = 0;

			// Change the folder ids
			for (var previousFolderId in changedFolderIds)
			{
				folderToMove = this._getSourceByFolderId(previousFolderId);

				// Change the id and select the containing element as the folder element.
				folderToMove = folderToMove
									.attr('data-key', 'folder:' + changedFolderIds[previousFolderId].newId)
									.data('key', 'folder:' + changedFolderIds[previousFolderId].newId).parent();

				if (topFolderLi.length == 0 || topFolderLi.parents().filter(folderToMove).length > 0)
				{
					topFolderLi = folderToMove;
					topFolderMovedId = changedFolderIds[previousFolderId].newId;
				}
			}

			if (topFolderLi.length == 0)
			{
				this.setIndexAvailable();
				this.progressBar.hideProgressBar();
				this._folderDrag.returnHelpersToDraggees();

				return;
			}

			var topFolder = topFolderLi.children('a');

			// Now move the uppermost node.
			var siblings = topFolderLi.siblings('ul, .toggle');
			var parentSource = this._getParentSource(topFolder);

			var newParent = this._getSourceByFolderId(targetFolderId);
			this._prepareParentForChildren(newParent);
			this._appendSubfolder(newParent, topFolderLi);

			topFolder.after(siblings);

			this._cleanUpTree(parentSource);
			this.$sidebar.find('ul>ul, ul>.toggle').remove();

			// Delete the old folders
			for (var i = 0; i < folderDeleteList.length; i++)
			{
				Ant.postActionRequest('assets/deleteFolder', {folderId: folderDeleteList[i]});
			}

			this.setIndexAvailable();
			this.progressBar.hideProgressBar();
			this._folderDrag.returnHelpersToDraggees();
			this._selectSourceByFolderId(topFolderMovedId);

		}, this);

		if (fileMoveList.length > 0)
		{
			this._moveFile(fileMoveList, 0, $.proxy(function()
			{
				moveCallback(folderDeleteList, changedFolderIds, removeFromTree);
			}, this));
		}
		else
		{
			moveCallback(folderDeleteList, changedFolderIds, removeFromTree);
		}
	},

	/**
	 * Get parent source for a source.
	 *
	 * @param $source
	 * @returns {*}
	 * @private
	 */
	_getParentSource: function($source)
	{
		if (this._getSourceLevel($source) > 1)
		{
			return $source.parent().parent().siblings('a');
		}
	},

	/**
	 * Move a file using data from a parameter array.
	 *
	 * @param parameterArray
	 * @param parameterIndex
	 * @param callback
	 * @private
	 */
	_moveFile: function(parameterArray, parameterIndex, callback)
	{
		if (parameterIndex == 0)
		{
			this.responseArray = [];
		}

		Ant.postActionRequest('assets/moveFile', parameterArray[parameterIndex], $.proxy(function(data, textStatus)
		{
			this.progressBar.incrementProcessedItemCount(1);
			this.progressBar.updateProgressBar();

			if (textStatus == 'success')
			{
				this.responseArray.push(data);

				// If assets were just merged we should get the referece tags updated right away
				Ant.cp.runPendingTasks();
			}

			parameterIndex++;

			if (parameterIndex >= parameterArray.length)
			{
				callback(this.responseArray);
			}
			else
			{
				this._moveFile(parameterArray, parameterIndex, callback);
			}

		}, this));
	},

	_selectSourceByFolderId: function(targetFolderId)
	{
		var $targetSource = this._getSourceByFolderId(targetFolderId);

		// Make sure that all the parent sources are expanded and this source is visible.
		var $parentSources = $targetSource.parent().parents('li');

		for (var i = 0; i < $parentSources.length; i++)
		{
			var $parentSource = $($parentSources[i]);

			if (!$parentSource.hasClass('expanded'))
			{
				$parentSource.children('.toggle').click();
			}
		};

		this.sourceSelect.selectItem($targetSource);

		this.$source = $targetSource;
		this.sourceKey = $targetSource.data('key');
		this.setInstanceState('selectedSource', this.sourceKey);

		this.updateElements();
	},

	/**
	 * Initialize the uploader.
	 *
	 * @private
	 */
	onAfterHtmlInit: function()
	{
		if (!this.$uploadButton)
		{
			this.$uploadButton = $('<div class="btn submit" data-icon="upload" style="position: relative; overflow: hidden;" role="button">' + Ant.t('Upload files') + '</div>');
			this.addButton(this.$uploadButton);

			this.$uploadInput = $('<input type="file" multiple="multiple" name="assets-upload" />').hide().insertBefore(this.$uploadButton);
		}

		this.promptHandler = new Ant.PromptHandler();
		this.progressBar = new Ant.ProgressBar(this.$main, true);

		var options = {
			url: Ant.getActionUrl('assets/uploadFile'),
			fileInput: this.$uploadInput,
			dropZone: this.$main
		};

		options.events = {
			fileuploadstart:       $.proxy(this, '_onUploadStart'),
			fileuploadprogressall: $.proxy(this, '_onUploadProgress'),
			fileuploaddone:        $.proxy(this, '_onUploadComplete')
		};

		if (typeof this.settings.criteria.kind != "undefined")
		{
			options.allowedKinds = this.settings.criteria.kind;
		}

		this._currentUploaderSettings = options;

		this.uploader = new Ant.Uploader (this.$uploadButton, options);

		this.$uploadButton.on('click', $.proxy(function()
		{
			if (this.$uploadButton.hasClass('disabled'))
			{
				return;
			}
			if (!this.isIndexBusy)
			{
				this.$uploadButton.parent().find('input[name=assets-upload]').click();
			}
		}, this));

		this.base();
	},

	onSelectSource: function()
	{
		this.uploader.setParams({folderId: this._getFolderIdFromSourceKey(this.sourceKey)});
		if (!this.$source.attr('data-upload'))
		{
			this.$uploadButton.addClass('disabled');
		}
		else
		{
			this.$uploadButton.removeClass('disabled');
		}
		this.base();
	},

	_getFolderIdFromSourceKey: function(sourceKey)
	{
		return sourceKey.split(':')[1];
	},

	/**
	 * React on upload submit.
	 *
	 * @param id
	 * @private
	 */
	_onUploadStart: function(event)
	{
		this.setIndexBusy();

		// Initial values
		this._positionProgressBar();
		this.progressBar.resetProgressBar();
		this.progressBar.showProgressBar();
	},

	/**
	 * Update uploaded byte count.
	 */
	_onUploadProgress: function(event, data)
	{
		var progress = parseInt(data.loaded / data.total * 100, 10);
		this.progressBar.setProgressPercentage(progress);
	},

	/**
	 * On Upload Complete.
	 */
	_onUploadComplete: function(event, data)
	{
		var response = data.result;
		var fileName = data.files[0].name;

		var doReload = true;

		if (response.success || response.prompt)
		{
			// Add the uploaded file to the selected ones, if appropriate
			this._uploadedFileIds.push(response.fileId);

			// If there is a prompt, add it to the queue
			if (response.prompt)
			{
				this.promptHandler.addPrompt(response);
			}
		}
		else
		{
			if (response.error)
			{
				alert(Ant.t('Upload failed for {filename}. The error message was: ”{error}“', { filename: fileName, error: response.error }));
			}
			else
			{
				alert(Ant.t('Upload failed for {filename}.', { filename: fileName }));
			}

			doReload = false;
		}

		// For the last file, display prompts, if any. If not - just update the element view.
		if (this.uploader.isLastUpload())
		{
			this.setIndexAvailable();
			this.progressBar.hideProgressBar();

			if (this.promptHandler.getPromptCount())
			{
				this.promptHandler.showBatchPrompts($.proxy(this, '_uploadFollowup'));
			}
			else
			{
				if (doReload)
				{
					this.updateElements();
				}
			}
		}
	},

	/**
	 * Follow up to an upload that triggered at least one conflict resolution prompt.
	 *
	 * @param returnData
	 * @private
	 */
	_uploadFollowup: function(returnData)
	{
		this.setIndexBusy();
		this.progressBar.resetProgressBar();

		this.promptHandler.resetPrompts();

		var finalCallback = $.proxy(function()
		{
			this.setIndexAvailable();
			this.progressBar.hideProgressBar();
			this.updateElements();
		}, this);

		this.progressBar.setItemCount(returnData.length);

		var doFollowup = $.proxy(function(parameterArray, parameterIndex, callback)
		{
			var postData = {
				newFileId:    parameterArray[parameterIndex].fileId,
				fileName:     parameterArray[parameterIndex].fileName,
				userResponse: parameterArray[parameterIndex].choice
			};

			Ant.postActionRequest('assets/uploadFile', postData, $.proxy(function(data, textStatus)
			{
				if (textStatus == 'success' && data.fileId)
				{
					this._uploadedFileIds.push(data.fileId);
				}
				parameterIndex++;
				this.progressBar.incrementProcessedItemCount(1);
				this.progressBar.updateProgressBar();

				if (parameterIndex == parameterArray.length)
				{
					callback();
				}
				else
				{
					doFollowup(parameterArray, parameterIndex, callback);
				}
			}, this));

		}, this);

		this.progressBar.showProgressBar();
		doFollowup(returnData, 0, finalCallback);
	},

	/**
	 * Perform actions after updating elements
	 * @private
	 */
	onUpdateElements: function(append, $newElements)
	{
		if (this.settings.context == 'index')
		{
			if (!append)
			{
				this._fileDrag.removeAllItems();
			}

			this._fileDrag.addItems($newElements);
		}

		// See if we have freshly uploaded files to add to selection
		if (this._uploadedFileIds.length)
		{
			var $item = null;
			for (var i = 0; i < this._uploadedFileIds.length; i++)
			{
				$item = this.$main.find('.element[data-id=' + this._uploadedFileIds[i] + ']:first').parent();
				if (this.getSelectedSourceState('mode') == 'table')
				{
					$item = $item.parent();
				}

				if (this.elementSelect)
				{
					this.elementSelect.selectItem($item);
				}
			}

			// Reset the list.
			this._uploadedFileIds = [];
		}

		this.base(append, $newElements)
	},

	/**
	 * On Drag Start
	 */
	_onDragStart: function()
	{
		this._tempExpandedFolders = [];
	},

	/**
	 * Get File Drag Helper
	 */
	_getFileDragHelper: function($element)
	{
		var currentView = this.getSelectedSourceState('mode');

		switch (currentView)
		{
			case 'table':
			{
				var $outerContainer = $('<div class="elements datatablesorthelper"/>').appendTo(Helper.$bod),
					$innerContainer = $('<div class="tableview"/>').appendTo($outerContainer),
					$table = $('<table class="data"/>').appendTo($innerContainer),
					$tbody = $('<tbody/>').appendTo($table);

				$element.appendTo($tbody);

				// Copy the column widths
				this._$firstRowCells = this.$elementContainer.children('tr:first').children();
				var $helperCells = $element.children();

				for (var i = 0; i < $helperCells.length; i++)
				{
					// Hard-set the cell widths
					var $helperCell = $($helperCells[i]);

					// Skip the checkbox cell
					if (Helper.hasAttr($helperCell, 'data-checkboxcell'))
					{
						$helperCell.remove();
						$outerContainer.css('margin-'+Ant.left, 19); // 26 - 7
						continue;
					}

					var $firstRowCell = $(this._$firstRowCells[i]),
						width = $firstRowCell.width();

					$firstRowCell.width(width);
					$helperCell.width(width);
				}

				return $outerContainer;
			}
			case 'thumbs':
			{
				var $outerContainer = $('<div class="elements thumbviewhelper"/>').appendTo(Helper.$bod),
					$innerContainer = $('<ul class="thumbsview"/>').appendTo($outerContainer);

				$element.appendTo($innerContainer);

				return $outerContainer;
			}
		}

		return $();
	},

	/**
	 * On Drop Target Change
	 */
	_onDropTargetChange: function($dropTarget)
	{
		clearTimeout(this._expandDropTargetFolderTimeout);

		if ($dropTarget)
		{
			var folderId = this._getFolderIdFromSourceKey($dropTarget.data('key'));

			if (folderId)
			{
				this.dropTargetFolder = this._getSourceByFolderId(folderId);

				if (this._hasSubfolders(this.dropTargetFolder) && ! this._isExpanded(this.dropTargetFolder))
				{
					this._expandDropTargetFolderTimeout = setTimeout($.proxy(this, '_expandFolder'), 500);
				}
			}
			else
			{
				this.dropTargetFolder = null;
			}
		}

		if ($dropTarget && $dropTarget[0] != this.$source[0])
		{
			// Temporarily remove the .sel class on the active source
			this.$source.removeClass('sel');
		}
		else
		{
			this.$source.addClass('sel');
		}
	},

	/**
	 * Collapse Extra Expanded Folders
	 */
	_collapseExtraExpandedFolders: function(dropTargetFolderId)
	{
		clearTimeout(this._expandDropTargetFolderTimeout);

		// If a source ID is passed in, exclude its parents
		if (dropTargetFolderId)
		{
			var excluded = this._getSourceByFolderId(dropTargetFolderId).parents('li').children('a');
		}

		for (var i = this._tempExpandedFolders.length-1; i >= 0; i--)
		{
			var $source = this._tempExpandedFolders[i];

			// Check the parent list, if a source id is passed in
			if (! dropTargetFolderId || excluded.filter('[data-key="' + $source.data('key') + '"]').length == 0)
			{
				this._collapseFolder($source);
				this._tempExpandedFolders.splice(i, 1);
			}
		}
	},

	_getSourceByFolderId: function(folderId)
	{
		return this.$sources.filter('[data-key="folder:' + folderId + '"]');
	},

	_hasSubfolders: function($source)
	{
		return $source.siblings('ul').find('li').length;
	},

	_isExpanded: function($source)
	{
		return $source.parent('li').hasClass('expanded');
	},

	_expandFolder: function()
	{
		// Collapse any temp-expanded drop targets that aren't parents of this one
		this._collapseExtraExpandedFolders(this._getFolderIdFromSourceKey(this.dropTargetFolder.data('key')));

		this.dropTargetFolder.siblings('.toggle').click();

		// Keep a record of that
		this._tempExpandedFolders.push(this.dropTargetFolder);
	},

	_collapseFolder: function($source)
	{
		if ($source.parent().hasClass('expanded'))
		{
			$source.siblings('.toggle').click();
		}
	},

	_createFolderContextMenu: function($source)
	{
		var menuOptions = [{ label: Ant.t('New subfolder'), onClick: $.proxy(this, '_createSubfolder', $source) }];

		// For all folders that are not top folders
		if (this.settings.context == 'index' && this._getSourceLevel($source) > 1)
		{
			menuOptions.push({ label: Ant.t('Rename folder'), onClick: $.proxy(this, '_renameFolder', $source) });
			menuOptions.push({ label: Ant.t('Delete folder'), onClick: $.proxy(this, '_deleteFolder', $source) });
		}

		new Helper.ContextMenu($source, menuOptions, {menuClass: 'menu'});
	},

	_createSubfolder: function($parentFolder)
	{
		var subfolderName = prompt(Ant.t('Enter the name of the folder'));

		if (subfolderName)
		{
			var params = {
				parentId:  this._getFolderIdFromSourceKey($parentFolder.data('key')),
				folderName: subfolderName
			};

			this.setIndexBusy();

			Ant.postActionRequest('assets/createFolder', params, $.proxy(function(data, textStatus)
			{
				this.setIndexAvailable();

				if (textStatus == 'success' && data.success)
				{
					this._prepareParentForChildren($parentFolder);

					var $subFolder = $(
						'<li>' +
							'<a data-key="folder:'+data.folderId+'"' +
								(Helper.hasAttr($parentFolder, 'data-has-thumbs') ? ' data-has-thumbs' : '') +
								' data-upload="'+$parentFolder.attr('data-upload')+'"' +
							'>' +
								data.folderName +
							'</a>' +
						'</li>'
					);

					var $a = $subFolder.children('a:first');
					this._appendSubfolder($parentFolder, $subFolder);
					this.initSource($a);
				}

				if (textStatus == 'success' && data.error)
				{
					alert(data.error);
				}
			}, this));
		}
	},

	_deleteFolder: function($targetFolder)
	{
		if (confirm(Ant.t('Really delete folder “{folder}”?', {folder: $.trim($targetFolder.text())})))
		{
			var params = {
				folderId: this._getFolderIdFromSourceKey($targetFolder.data('key'))
			}

			this.setIndexBusy();

			Ant.postActionRequest('assets/deleteFolder', params, $.proxy(function(data, textStatus)
			{
				this.setIndexAvailable();

				if (textStatus == 'success' && data.success)
				{
					var $parentFolder = this._getParentSource($targetFolder);

					// Remove folder and any trace from its parent, if needed
					this.deinitSource($targetFolder);

					$targetFolder.parent().remove();
					this._cleanUpTree($parentFolder);

					this.$sidebar.trigger('resize');
				}

				if (textStatus == 'success' && data.error)
				{
					alert(data.error);
				}
			}, this));
		}
	},

	/**
	 * Rename
	 */
	_renameFolder: function($targetFolder)
	{
		var oldName = $.trim($targetFolder.text()),
			newName = prompt(Ant.t('Rename folder'), oldName);

		if (newName && newName != oldName)
		{
			var params = {
				folderId: this._getFolderIdFromSourceKey($targetFolder.data('key')),
				newName: newName
			};

			this.setIndexBusy();

			Ant.postActionRequest('assets/renameFolder', params, $.proxy(function(data, textStatus)
			{
				this.setIndexAvailable();

				if (textStatus == 'success' && data.success)
				{
					$targetFolder.text(data.newName);
				}

				if (textStatus == 'success' && data.error)
				{
					alert(data.error);
				}

			}, this), 'json');
		}
	},

	/**
	 * Prepare a source folder for children folder.
	 *
	 * @param $parentFolder
	 * @private
	 */
	_prepareParentForChildren: function($parentFolder)
	{
		if (!this._hasSubfolders($parentFolder))
		{
			$parentFolder.parent().addClass('expanded').append('<div class="toggle"></div><ul></ul>');
			this.initSourceToggle($parentFolder);
		}
	},

	/**
	 * Appends a subfolder to the parent folder at the correct spot.
	 *
	 * @param $parentFolder
	 * @param $subFolder
	 * @private
	 */
	_appendSubfolder: function($parentFolder, $subFolder)
	{
		var $subfolderList = $parentFolder.siblings('ul'),
			$existingChildren = $subfolderList.children('li'),
			subfolderLabel = $.trim($subFolder.children('a:first').text()),
			folderInserted = false;

		for (var i = 0; i < $existingChildren.length; i++)
		{
			var $existingChild = $($existingChildren[i]);

			if ($.trim($existingChild.children('a:first').text()) > subfolderLabel)
			{
				$existingChild.before($subFolder);
				folderInserted = true;
				break;
			}
		};

		if (!folderInserted)
		{
			$parentFolder.siblings('ul').append($subFolder);
		}

		this.$sidebar.trigger('resize');
	},

	_cleanUpTree: function($parentFolder)
	{
		if ($parentFolder !== null && $parentFolder.siblings('ul').children('li').length == 0)
		{
			this.deinitSourceToggle($parentFolder);
			$parentFolder.siblings('ul').remove();
			$parentFolder.siblings('.toggle').remove();
			$parentFolder.parent().removeClass('expanded');
		}
	},

	_positionProgressBar: function()
	{
		var $container = $(),
			offset = 0;

		if (this.settings.context == 'index')
		{
			$container = this.progressBar.$progressBar.closest('#content');
		}
		else
		{
			$container = this.progressBar.$progressBar.closest('.main');
		}

		var containerTop = $container.offset().top;
		var scrollTop = Helper.$doc.scrollTop();
		var diff = scrollTop - containerTop;
		var windowHeight = Helper.$win.height();

		if ($container.height() > windowHeight)
		{
			offset = (windowHeight / 2) - 6 + diff;
		}
		else
		{
			offset = ($container.height() / 2) - 6;
		}

		this.progressBar.$progressBar.css({
			top: offset
		});
	}

});

// Register it!
Ant.registerElementIndexClass('Asset', Ant.AssetIndex);


/**
 * Asset Select input
 */
Ant.AssetSelectInput = Ant.BaseElementSelectInput.extend(
{
	requestId: 0,
	hud: null,
	uploader: null,
	progressBar: null,

	init: function()
	{
		this.base.apply(this, arguments);
		this._attachUploader();
	},

	/**
	 * Attach the uploader with drag event handler
	 */
	_attachUploader: function()
	{
		this.progressBar = new Ant.ProgressBar($('<div class="progress-shade"></div>').appendTo(this.$container));

		var options = {
			url: Ant.getActionUrl('assets/expressUpload'),
			dropZone: this.$container,
			formData: {
				fieldId: this.settings.fieldId,
				elementId: this.settings.sourceElementId
			}
		};

		// If CSRF protection isn't enabled, these won't be defined.
		if (typeof Ant.csrfTokenName !== 'undefined' && typeof Ant.csrfTokenValue !== 'undefined')
		{
			// Add the CSRF token
			options.formData[Ant.csrfTokenName] = Ant.csrfTokenValue;
		}

		if (typeof this.settings.criteria.kind != "undefined")
		{
			options.allowedKinds = this.settings.criteria.kind;
		}

		options.canAddMoreFiles = $.proxy(this, 'canAddMoreFiles');

		options.events = {};
		options.events.fileuploadstart = $.proxy(this, '_onUploadStart');
		options.events.fileuploadprogressall = $.proxy(this, '_onUploadProgress');
		options.events.fileuploaddone = $.proxy(this, '_onUploadComplete');

		this.uploader = new Ant.Uploader(this.$container, options);
	},

	/**
	 * Add the freshly uploaded file to the input field.
	 */
	selectUploadedFile: function(element)
	{
		// Check if we're able to add new elements
		if (!this.canAddMoreElements())
		{
			return;
		}

		var $newElement = element.$element;

		// Make a couple tweaks
		$newElement.addClass('removable');
		$newElement.prepend('<input type="hidden" name="'+this.settings.name+'[]" value="'+element.id+'">' +
			'<a class="delete icon" title="'+Ant.t('Remove')+'"></a>');

		$newElement.appendTo(this.$elementsContainer);

		var margin = -($newElement.outerWidth()+10);

		this.$addElementBtn.css('margin-'+Ant.left, margin+'px');

		var animateCss = {};
		animateCss['margin-'+Ant.left] = 0;
		this.$addElementBtn.velocity(animateCss, 'fast');

		this.addElements($newElement);

		delete this.modal;
	},

	/**
	 * On upload start.
	 */
	_onUploadStart: function(event)
	{
		this.progressBar.$progressBar.css({
			top: Math.round(this.$container.outerHeight() / 2) - 6
		});

		this.$container.addClass('uploading');
		this.progressBar.resetProgressBar();
		this.progressBar.showProgressBar();
	},

	/**
	 * On upload progress.
	 */
	_onUploadProgress: function(event, data)
	{
		var progress = parseInt(data.loaded / data.total * 100, 10);
		this.progressBar.setProgressPercentage(progress);
	},

	/**
	 * On a file being uploaded.
	 */
	_onUploadComplete: function(event, data)
	{
		if (data.result.error)
		{
			alert(data.result.error);
		}
		else
		{
			var html = $(data.result.html);
			$('head').append(data.result.css);
			this.selectUploadedFile(Ant.getElementInfo(html));
		}

		// Last file
		if (this.uploader.isLastUpload())
		{
			this.progressBar.hideProgressBar();
			this.$container.removeClass('uploading');
		}
	},

	/**
	 * We have to take into account files about to be added as well
	 */
	canAddMoreFiles: function (slotsTaken)
	{
		return (!this.settings.limit || this.$elements.length  + slotsTaken < this.settings.limit);
	}
});



(function($) {


/**
 * AuthManager class
 */
Ant.AuthManager = Helper.Base.extend(
{
	checkAuthTimeoutTimer: null,
	showLoginModalTimer: null,
	decrementLogoutWarningInterval: null,

	showingLogoutWarningModal: false,
	showingLoginModal: false,

	logoutWarningModal: null,
	loginModal: null,

	$logoutWarningPara: null,
	$passwordInput: null,
	$passwordSpinner: null,
	$loginBtn: null,
	$loginErrorPara: null,

	/**
	 * Init
	 */
	init: function()
	{
		this.updateAuthTimeout(Ant.authTimeout);
	},

	/**
	 * Sets a timer for the next time to check the auth timeout.
	 */
	setCheckAuthTimeoutTimer: function(seconds)
	{
		if (this.checkAuthTimeoutTimer)
		{
			clearTimeout(this.checkAuthTimeoutTimer);
		}

		this.checkAuthTimeoutTimer = setTimeout($.proxy(this, 'checkAuthTimeout'), seconds*1000);
	},

	/**
	 * Pings the server to see how many seconds are left on the current user session, and handles the response.
	 */
	checkAuthTimeout: function(extendSession)
	{
		$.ajax({
			url: Ant.getActionUrl('users/getAuthTimeout', (extendSession ? null : 'dontExtendSession=1')),
			type: 'GET',
			complete: $.proxy(function(jqXHR, textStatus)
			{
				if (textStatus == 'success' && !isNaN(jqXHR.responseText))
				{
					this.updateAuthTimeout(jqXHR.responseText);
				}
				else
				{
					this.updateAuthTimeout(-1);
				}
			}, this)
		});
	},

	/**
	 * Updates our record of the auth timeout, and handles it.
	 */
	updateAuthTimeout: function(authTimeout)
	{
		this.authTimeout = parseInt(authTimeout);

		// Are we within the warning window?
		if (this.authTimeout != -1 && this.authTimeout < Ant.AuthManager.minSafeAuthTimeout)
		{
			// Is there still time to renew the session?
			if (this.authTimeout)
			{
				if (!this.showingLogoutWarningModal)
				{
					// Show the warning modal
					this.showLogoutWarningModal();
				}

				// Will the session expire before the next checkup?
				if (this.authTimeout < Ant.AuthManager.checkInterval)
				{
					if (this.showLoginModalTimer)
					{
						clearTimeout(this.showLoginModalTimer);
					}

					this.showLoginModalTimer = setTimeout($.proxy(this, 'showLoginModal'), this.authTimeout*1000);
				}
			}
			else if (!this.showingLoginModal)
			{
				// Show the login modal
				this.showLoginModal();
			}

			this.setCheckAuthTimeoutTimer(Ant.AuthManager.checkInterval);
		}
		else
		{
			// Everything's good!
			this.hideLogoutWarningModal();
			this.hideLoginModal();

			// Will be be within the minSafeAuthTimeout before the next update?
			if (this.authTimeout != -1 && this.authTimeout < (Ant.AuthManager.minSafeAuthTimeout + Ant.AuthManager.checkInterval))
			{
				this.setCheckAuthTimeoutTimer(this.authTimeout - Ant.AuthManager.minSafeAuthTimeout + 1);
			}
			else
			{
				this.setCheckAuthTimeoutTimer(Ant.AuthManager.checkInterval);
			}
		}
	},

	/**
	 * Shows the logout warning modal.
	 */
	showLogoutWarningModal: function()
	{
		if (this.showingLoginModal)
		{
			this.hideLoginModal(true);
			var quickShow = true;
		}
		else
		{
			var quickShow = false;
		}

		this.showingLogoutWarningModal = true;

		if (!this.logoutWarningModal)
		{
			var $form = $('<form id="logoutwarningmodal" class="modal alert fitted"/>'),
				$body = $('<div class="body"/>').appendTo($form),
				$buttons = $('<div class="buttons right"/>').appendTo($body),
				$logoutBtn = $('<div class="btn">'+Ant.t('Log out now')+'</div>').appendTo($buttons),
				$renewSessionBtn = $('<input type="submit" class="btn submit" value="'+Ant.t('Keep me logged in')+'" />').appendTo($buttons);

			this.$logoutWarningPara = $('<p/>').prependTo($body);

			this.logoutWarningModal = new Helper.Modal($form, {
				autoShow: false,
				closeOtherModals: false,
				hideOnEsc: false,
				hideOnShadeClick: false,
				shadeClass: 'modal-shade dark',
				onFadeIn: function()
				{
					if (!Helper.isMobileBrowser(true))
					{
						// Auto-focus the renew button
						setTimeout(function() {
							$renewSessionBtn.focus();
						}, 100);
					}
				}
			});

			this.addListener($logoutBtn, 'activate', 'logout');
			this.addListener($form, 'submit', 'renewSession');
		}

		if (quickShow)
		{
			this.logoutWarningModal.quickShow();
		}
		else
		{
			this.logoutWarningModal.show();
		}

		this.updateLogoutWarningMessage();

		this.decrementLogoutWarningInterval = setInterval($.proxy(this, 'decrementLogoutWarning'), 1000);
	},

	/**
	 * Updates the logout warning message indicating that the session is about to expire.
	 */
	updateLogoutWarningMessage: function()
	{
		this.$logoutWarningPara.text(Ant.t('Your session will expire in {time}.', {
			time: Ant.secondsToHumanTimeDuration(this.authTimeout)
		}));

		this.logoutWarningModal.updateSizeAndPosition();
	},

	decrementLogoutWarning: function()
	{
		if (this.authTimeout > 0)
		{
			this.authTimeout--;
			this.updateLogoutWarningMessage();
		}

		if (this.authTimeout == 0)
		{
			clearInterval(this.decrementLogoutWarningInterval);
		}
	},

	/**
	 * Hides the logout warning modal.
	 */
	hideLogoutWarningModal: function(quick)
	{
		this.showingLogoutWarningModal = false;

		if (this.logoutWarningModal)
		{
			if (quick)
			{
				this.logoutWarningModal.quickHide();
			}
			else
			{
				this.logoutWarningModal.hide();
			}

			if (this.decrementLogoutWarningInterval)
			{
				clearInterval(this.decrementLogoutWarningInterval);
			}
		}
	},

	/**
	 * Shows the login modal.
	 */
	showLoginModal: function()
	{
		if (this.showingLogoutWarningModal)
		{
			this.hideLogoutWarningModal(true);
			var quickShow = true;
		}
		else
		{
			var quickShow = false;
		}

		this.showingLoginModal = true;

		if (!this.loginModal)
		{
			var $form = $('<form id="loginmodal" class="modal alert fitted"/>'),
				$body = $('<div class="body"><h2>'+Ant.t('Your session has ended.')+'</h2><p>'+Ant.t('Enter your password to log back in.')+'</p></div>').appendTo($form),
				$inputContainer = $('<div class="inputcontainer">').appendTo($body),
				$inputsTable = $('<table class="inputs fullwidth"/>').appendTo($inputContainer),
				$inputsRow = $('<tr/>').appendTo($inputsTable),
				$passwordCell = $('<td/>').appendTo($inputsRow),
				$buttonCell = $('<td class="thin"/>').appendTo($inputsRow),
				$passwordWrapper = $('<div class="passwordwrapper"/>').appendTo($passwordCell);

			this.$passwordInput = $('<input type="password" class="text password fullwidth" placeholder="'+Ant.t('Password')+'"/>').appendTo($passwordWrapper);
			this.$passwordSpinner = $('<div class="spinner hidden"/>').appendTo($inputContainer);
			this.$loginBtn = $('<input type="submit" class="btn submit disabled" value="'+Ant.t('Login')+'" />').appendTo($buttonCell);
			this.$loginErrorPara = $('<p class="error"/>').appendTo($body);

			this.loginModal = new Helper.Modal($form, {
				autoShow: false,
				closeOtherModals: false,
				hideOnEsc: false,
				hideOnShadeClick: false,
				shadeClass: 'modal-shade dark',
				onFadeIn: $.proxy(function()
				{
					if (!Helper.isMobileBrowser(true))
					{
						// Auto-focus the password input
						setTimeout($.proxy(function() {
							this.$passwordInput.focus();
						}, this), 100);
					}
				}, this),
				onFadeOut: $.proxy(function()
				{
					this.$passwordInput.val('');
				}, this)
			});

			new Ant.PasswordInput(this.$passwordInput, {
				onToggleInput: $.proxy(function($newPasswordInput) {
					this.$passwordInput = $newPasswordInput;
				}, this)
			});

			this.addListener(this.$passwordInput, 'textchange', 'validatePassword');
			this.addListener($form, 'submit', 'login');
		}

		if (quickShow)
		{
			this.loginModal.quickShow();
		}
		else
		{
			this.loginModal.show();
		}
	},

	/**
	 * Hides the login modal.
	 */
	hideLoginModal: function(quick)
	{
		this.showingLoginModal = false;

		if (this.loginModal)
		{
			if (quick)
			{
				this.loginModal.quickHide();
			}
			else
			{
				this.loginModal.hide();
			}
		}
	},

	logout: function()
	{
		var url = Ant.getActionUrl('users/logout');

		$.get(url, $.proxy(function()
		{
			Ant.redirectTo('');
		}, this));
	},

	renewSession: function(ev)
	{
		if (ev)
		{
			ev.preventDefault();
		}

		this.hideLogoutWarningModal()
		this.checkAuthTimeout(true);
	},

	validatePassword: function()
	{
		if (this.$passwordInput.val().length >= 6)
		{
			this.$loginBtn.removeClass('disabled');
			return true;
		}
		else
		{
			this.$loginBtn.addClass('disabled');
			return false;
		}
	},

	login: function(ev)
	{
		if (ev)
		{
			ev.preventDefault();
		}

		if (this.validatePassword())
		{
			this.$passwordSpinner.removeClass('hidden');
			this.clearLoginError();

			var data = {
				loginName: Ant.username,
				password: this.$passwordInput.val()
			};

			Ant.postActionRequest('users/login', data, $.proxy(function(response, textStatus)
			{
				this.$passwordSpinner.addClass('hidden');

				if (textStatus == 'success')
				{
					if (response.success)
					{
						this.hideLoginModal();
						this.checkAuthTimeout();
					}
					else
					{
						this.showLoginError(response.error);
						Helper.shake(this.loginModal.$container);

						if (!Helper.isMobileBrowser(true))
						{
							this.$passwordInput.focus();
						}
					}
				}
				else
				{
					this.showLoginError();
				}

			}, this));
		}
	},

	showLoginError: function(error)
	{
		if (error === null || typeof error == 'undefined')
		{
			error = Ant.t('An unknown error occurred.');
		}

		this.$loginErrorPara.text(error);
		this.loginModal.updateSizeAndPosition();
	},

	clearLoginError: function()
	{
		this.showLoginError('');
	}
},
{
	checkInterval: 60,
	minSafeAuthTimeout: 120
});


})(jQuery);


/**
 * Category index class
 */
Ant.CategoryIndex = Ant.BaseElementIndex.extend(
{
	$newCategoryBtn: null,

	onAfterHtmlInit: function()
	{
		// Get the New Category button
		//this.$newCategoryBtn = this.$sidebar.find('> .buttons > .btn');

		this.base();
	},

	getDefaultSourceKey: function()
	{
		// Did they request a specific category group in the URL?
		if (this.settings.context == 'index' && typeof defaultGroupHandle != typeof undefined)
		{
			for (var i = 0; i < this.$sources.length; i++)
			{
				var $source = $(this.$sources[i]);

				if ($source.data('handle') == defaultGroupHandle)
				{
					return $source.data('key');
				}
			}
		}

		return this.base();
	},

	onSelectSource: function()
	{
		if (this.settings.context == 'index')
		{
			// Get the handle of the selected source
			var handle = this.$source.data('handle');

			// Update the URL
			if (typeof history != typeof undefined)
			{
				var uri = 'categories';

				if (handle)
				{
					uri += '/'+handle;
				}

//				history.replaceState({}, '', Ant.getUrl(uri));
			}

			// Update the New Category button
			this.$newCategoryBtn.attr('href', Ant.getUrl('categories/'+handle+'/new'));
		}

		this.base();
	}

});

// Register it!
Ant.registerElementIndexClass('Category', Ant.CategoryIndex);


/**
 * Category Select input
 */
Ant.CategorySelectInput = Ant.BaseElementSelectInput.extend(
{
	setSettings: function()
	{
		this.base.apply(this, arguments);
		this.settings.sortable = false;
	},

	getModalSettings: function()
	{
		var settings = this.base();
		settings.hideOnSelect = false;
		return settings;
	},

	getElements: function()
	{
		return this.$elementsContainer.find('.element');
	},

	onModalSelect: function(elements)
	{
		// Disable the modal
		this.modal.disable();
		this.modal.disableCancelBtn();
		this.modal.disableSelectBtn();
		this.modal.showFooterSpinner();

		// Get the new category HTML
		var selectedCategoryIds = this.getSelectedElementIds();

		for (var i = 0; i < elements.length; i++)
		{
			selectedCategoryIds.push(elements[i].id);
		}

		var data = {
			categoryIds: selectedCategoryIds,
			locale:      elements[0].locale,
			id:          this.settings.id,
			name:        this.settings.name,
			limit:       this.settings.limit,
		};

		Ant.postActionRequest('elements/getCategoriesInputHtml', data, $.proxy(function(response, textStatus)
		{
			this.modal.enable();
			this.modal.enableCancelBtn();
			this.modal.enableSelectBtn();
			this.modal.hideFooterSpinner();

			if (textStatus == 'success')
			{
				var $newInput = $(response.html),
					$newElementsContainer = $newInput.children('.elements');

				this.$elementsContainer.replaceWith($newElementsContainer);
				this.$elementsContainer = $newElementsContainer;
				this.resetElements();

				for (var i = 0; i < elements.length; i++)
				{
					var element = elements[i],
						$element = this.getElementById(element.id);

					if ($element)
					{
						this.animateElementIntoPlace(element.$element, $element);
					}
				}

				this.updateDisabledElementsInModal();
				this.modal.hide();
				this.onSelectElements();
			}
		}, this));
	},

	removeElement: function($element)
	{
		// Find any descendants this category might have
		var $allCategories = $element.add($element.parent().siblings('ul').find('.element'));

		// Remove our record of them all at once
		this.removeElements($allCategories);

		// Animate them away one at a time
		for (var i = 0; i < $allCategories.length; i++)
		{
			this._animateCategoryAway($allCategories, i);
		}
	},

	_animateCategoryAway: function($allCategories, i)
	{
		// Is this the last one?
		if (i == $allCategories.length - 1)
		{
			var callback = $.proxy(function()
			{
				var $li = $allCategories.first().parent().parent(),
					$ul = $li.parent();

				if ($ul[0] == this.$elementsContainer[0] || $li.siblings().length)
				{
					$li.remove();
				}
				else
				{
					$ul.remove();
				}
			}, this);
		}
		else
		{
			callback = null;
		}

		var func = $.proxy(function() {
			this.animateElementAway($allCategories.eq(i), callback);
		}, this);

		if (i == 0)
		{
			func();
		}
		else
		{
			setTimeout(func, 100 * i);
		}
	}
});


/**
 * DataTableSorter
 */
Ant.DataTableSorter = Helper.DragSort.extend(
{
	$table: null,

	init: function(table, settings)
	{
		this.$table = $(table);
		var $rows = this.$table.children('tbody').children(':not(.filler)');

		settings = $.extend({}, Ant.DataTableSorter.defaults, settings);

		settings.container = this.$table.children('tbody');
		settings.helper = $.proxy(this, 'getHelper');
		settings.caboose = '<tr/>';
		settings.axis = Helper.Y_AXIS;
		settings.magnetStrength = 4;
		settings.helperLagBase = 1.5;

		this.base($rows, settings);
	},

	getHelper: function($helperRow)
	{
		var $helper = $('<div class="'+this.settings.helperClass+'"/>').appendTo(Helper.$bod),
			$table = $('<table/>').appendTo($helper),
			$tbody = $('<tbody/>').appendTo($table);

		$helperRow.appendTo($tbody);

		// Copy the table width and classes
		$table.width(this.$table.width());
		$table.prop('className', this.$table.prop('className'));

		// Copy the column widths
		var $firstRow = this.$table.find('tr:first'),
			$cells = $firstRow.children(),
			$helperCells = $helperRow.children();

		for (var i = 0; i < $helperCells.length; i++)
		{
			$($helperCells[i]).width($($cells[i]).width());
		}

		return $helper;
	}

},
{
	defaults: {
		handle: '.move',
		helperClass: 'datatablesorthelper'
	}
});


(function($) {


Ant.DeleteUserModal = Helper.Modal.extend(
{
	id: null,
	userId: null,

	$deleteActionRadios: null,
	$deleteSpinner: null,

	currentPasswordModal: null,
	userSelect: null,
	_deleting: false,

	init: function(userId, settings)
	{
		this.id = Math.floor(Math.random()*1000000000);
		this.userId = userId;
		settings = $.extend(Ant.DeleteUserModal.defaults, settings);

		var $form = $(
				'<form class="modal fitted deleteusermodal" method="post" accept-charset="UTF-8">' +
					Ant.getCsrfInput() +
					'<input type="hidden" name="action" value="users/deleteUser"/>' +
					(!Helper.isArray(this.userId) ? '<input type="hidden" name="userId" value="'+this.userId+'"/>' : '') +
					'<input type="hidden" name="redirect" value="'+(Ant.edition == Ant.Pro ? 'users' : 'dashboard')+'"/>' +
				'</form>'
			).appendTo(Helper.$bod),
			$body = $(
				'<div class="body">' +
					'<p>'+Ant.t('What do you want to do with the their content?')+'</p>' +
					'<div class="options">' +
						'<label><input type="radio" name="contentAction" value="transfer"/> '+Ant.t('Transfer it to:')+'</label>' +
						'<div id="transferselect'+this.id+'" class="elementselect">' +
							'<div class="elements"></div>' +
							'<div class="btn add icon dashed">'+Ant.t('Choose a user')+'</div>' +
						'</div>' +
					'</div>' +
					'<div>' +
						'<label><input type="radio" name="contentAction" value="delete"/> '+Ant.t('Delete it')+'</label>' +
					'</div>' +
				'</div>'
			).appendTo($form),
			$buttons = $('<div class="buttons right"/>').appendTo($body),
			$cancelBtn = $('<div class="btn">'+Ant.t('Cancel')+'</div>').appendTo($buttons);

		this.$deleteActionRadios = $body.find('input[type=radio]');
		this.$deleteSubmitBtn = $('<input type="submit" class="btn submit disabled" value="'+(Helper.isArray(this.userId) ? Ant.t('Delete users') : Ant.t('Delete user'))+'" />').appendTo($buttons);
		this.$deleteSpinner = $('<div class="spinner hidden"/>').appendTo($buttons);

		if (Helper.isArray(this.userId))
		{
			var idParam = ['and'];

			for (var i = 0; i < this.userId.length; i++)
			{
				idParam.push('not '+this.userId[i]);
			}
		}
		else
		{
			var idParam = 'not '+this.userId;
		}

		this.userSelect = new Ant.BaseElementSelectInput({
			id: 'transferselect'+this.id,
			name: 'transferContentTo',
			elementType: 'User',
			criteria: {
				id: idParam
			},
			limit: 1,
			modalSettings: {
				closeOtherModals: false
			},
			onSelectElements: $.proxy(function()
			{
				if (!this.$deleteActionRadios.first().prop('checked'))
				{
					this.$deleteActionRadios.first().click();
				}
				else
				{
					this.validateDeleteInputs();
				}
			}, this),
			onRemoveElements: $.proxy(this, 'validateDeleteInputs'),
			selectable: false,
			editable: false
		});

		this.addListener($cancelBtn, 'click', 'hide');

		this.addListener(this.$deleteActionRadios, 'change', 'validateDeleteInputs');
		this.addListener($form, 'submit', 'handleSubmit');

		this.base($form, settings);
	},

	validateDeleteInputs: function()
	{
		var validates = false;

		if (this.$deleteActionRadios.eq(0).prop('checked'))
		{
			validates = !!this.userSelect.totalSelected;
		}
		else if (this.$deleteActionRadios.eq(1).prop('checked'))
		{
			validates = true;
		}

		if (validates)
		{
			this.$deleteSubmitBtn.removeClass('disabled')
		}
		else
		{
			this.$deleteSubmitBtn.addClass('disabled')
		}

		return validates;
	},

	handleSubmit: function(ev)
	{
		if (this._deleting || !this.validateDeleteInputs())
		{
			ev.preventDefault();
			return;
		}

		this.$deleteSubmitBtn.addClass('active');
		this.$deleteSpinner.removeClass('hidden');
		this.disable();
		this.userSelect.disable();
		this._deleting = true;

		// Let the onSubmit callback prevent the form from getting submitted
		if (this.settings.onSubmit() === false)
		{
			ev.preventDefault();
		}
	},

	onFadeIn: function()
	{
		// Auto-focus the first radio
		if (!Helper.isMobileBrowser(true))
		{
			this.$deleteActionRadios.first().focus();
		}

		this.base();
	}
},
{
	defaults: {
		onSubmit: $.noop
	}
});


})(jQuery)


/**
 * Editable table class
 */
Ant.EditableTable = Helper.Base.extend(
{
	id: null,
	baseName: null,
	columns: null,
	sorter: null,
	biggestId: -1,

	$table: null,
	$tbody: null,
	$addRowBtn: null,

	init: function(id, baseName, columns, settings)
	{
		this.id = id;
		this.baseName = baseName;
		this.columns = columns;
		this.setSettings(settings, Ant.EditableTable.defaults);

		this.$table = $('#'+id);
		this.$tbody = this.$table.children('tbody');

		this.sorter = new Ant.DataTableSorter(this.$table, {
			helperClass: 'editabletablesorthelper',
			copyDraggeeInputValuesToHelper: true
		});

		var $rows = this.$tbody.children();

		for (var i = 0; i < $rows.length; i++)
		{
			new Ant.EditableTable.Row(this, $rows[i]);
		}

		this.$addRowBtn = this.$table.next('.add');
		this.addListener(this.$addRowBtn, 'activate', 'addRow');
	},

	addRow: function()
	{
		var rowId = this.settings.rowIdPrefix+(this.biggestId+1),
			rowHtml = Ant.EditableTable.getRowHtml(rowId, this.columns, this.baseName, {}),
			$tr = $(rowHtml).appendTo(this.$tbody);

		new Ant.EditableTable.Row(this, $tr);
		this.sorter.addItems($tr);

		// Focus the first input in the row
		$tr.find('input,textarea,select').first().focus();

		// onAddRow callback
		this.settings.onAddRow($tr);
	}
},
{
	textualColTypes: ['singleline', 'multiline', 'number'],
	defaults: {
		rowIdPrefix: '',
		onAddRow: $.noop,
		onDeleteRow: $.noop
	},

	getRowHtml: function(rowId, columns, baseName, values)
	{
		var rowHtml = '<tr data-id="'+rowId+'">';

		for (var colId in columns)
		{
			var col = columns[colId],
				name = baseName+'['+rowId+']['+colId+']',
				value = (typeof values[colId] != 'undefined' ? values[colId] : ''),
				textual = Ant.inArray(col.type, Ant.EditableTable.textualColTypes);

			rowHtml += '<td class="'+(textual ? 'textual' : '')+' '+(typeof col['class'] != 'undefined' ? col['class'] : '')+'"' +
			              (typeof col['width'] != 'undefined' ? ' width="'+col['width']+'"' : '') +
			              '>';

			switch (col.type)
			{
				case 'select':
				{
					rowHtml += '<div class="select small"><select name="'+name+'">';

					var hasOptgroups = false;

					for (var key in col.options)
					{
						var option = col.options[key];

						if (typeof option.optgroup != 'undefined')
						{
							if (hasOptgroups)
							{
								rowHtml += '</optgroup>';
							}
							else
							{
								hasOptgroups = true;
							}

							rowHtml += '<optgroup label="'+option.optgroup+'">';
						}
						else
						{
							var optionLabel = (typeof option.label != 'undefined' ? option.label : option),
								optionValue = (typeof option.value != 'undefined' ? option.value : key),
								optionDisabled = (typeof option.disabled != 'undefined' ? option.disabled : false);

							rowHtml += '<option value="'+optionValue+'"'+(optionValue == value ? ' selected' : '')+(optionDisabled ? ' disabled' : '')+'>'+optionLabel+'</option>';
						}
					}

					if (hasOptgroups)
					{
						rowHtml += '</optgroup>';
					}

					rowHtml += '</select></div>';

					break;
				}

				case 'checkbox':
				{
					rowHtml += '<input type="hidden" name="'+name+'">' +
					           '<input type="checkbox" name="'+name+'" value="1"'+(value ? ' checked' : '')+'>';

					break;
				}

				default:
				{
					rowHtml += '<textarea name="'+name+'" rows="1">'+value+'</textarea>';
				}
			}

			rowHtml += '</td>';
		}

		rowHtml += '<td class="thin action"><a class="move icon" title="'+Ant.t('Reorder')+'"></a></td>' +
				'<td class="thin action"><a class="delete icon" title="'+Ant.t('Delete')+'"></a></td>' +
			'</tr>';

		return rowHtml;
	}
});

/**
 * Editable table row class
 */
Ant.EditableTable.Row = Helper.Base.extend(
{
	table: null,
	id: null,
	niceTexts: null,

	$tr: null,
	$tds: null,
	$textareas: null,
	$deleteBtn: null,

	init: function(table, tr)
	{
		this.table = table;
		this.$tr = $(tr);
		this.$tds = this.$tr.children();

		// Get the row ID, sans prefix
		var id = parseInt(this.$tr.attr('data-id').substr(this.table.settings.rowIdPrefix.length));

		if (id > this.table.biggestId)
		{
			this.table.biggestId = id;
		}

		this.$textareas = $();
		this.niceTexts = [];
		var textareasByColId = {};

		var i = 0;

		for (var colId in this.table.columns)
		{
			var col = this.table.columns[colId];

			if (Ant.inArray(col.type, Ant.EditableTable.textualColTypes))
			{
				$textarea = $('textarea', this.$tds[i]);
				this.$textareas = this.$textareas.add($textarea);

				this.addListener($textarea, 'focus', 'onTextareaFocus');
				this.addListener($textarea, 'mousedown', 'ignoreNextTextareaFocus');

				this.niceTexts.push(new Helper.NiceText($textarea, {
					onHeightChange: $.proxy(this, 'onTextareaHeightChange')
				}));

				if (col.type == 'singleline' || col.type == 'number')
				{
					this.addListener($textarea, 'keypress', { type: col.type }, 'validateKeypress');
				}

				textareasByColId[colId] = $textarea;
			}

			i++;
		}

		// Now that all of the text cells have been nice-ified, let's normalize the heights
		this.onTextareaHeightChange();

		// Now look for any autopopulate columns
		for (var colId in this.table.columns)
		{
			var col = this.table.columns[colId];

			if (col.autopopulate && typeof textareasByColId[col.autopopulate] != 'undefined' && !textareasByColId[colId].val())
			{
				if (col.autopopulate == 'handle')
				{
					new Ant.HandleGenerator(textareasByColId[colId], textareasByColId[col.autopopulate]);
				}
				else
				{
					new Ant.BaseInputGenerator(textareasByColId[colId], textareasByColId[col.autopopulate]);
				}
			}
		}

		var $deleteBtn = this.$tr.children().last().find('.delete');
		this.addListener($deleteBtn, 'click', 'deleteRow');
	},

	onTextareaFocus: function(ev)
	{
		this.onTextareaHeightChange();

		var $textarea = $(ev.currentTarget);

		if ($textarea.data('ignoreNextFocus'))
		{
			$textarea.data('ignoreNextFocus', false);
			return;
		}

		setTimeout(function()
		{
			var val = $textarea.val();

			// Does the browser support setSelectionRange()?
			if (typeof $textarea[0].setSelectionRange != 'undefined')
			{
				// Select the whole value
				var length = val.length * 2;
				$textarea[0].setSelectionRange(0, length);
			}
			else
			{
				// Refresh the value to get the cursor positioned at the end
				$textarea.val(val);
			}
		}, 0);
	},

	ignoreNextTextareaFocus: function(ev)
	{
		$.data(ev.currentTarget, 'ignoreNextFocus', true);
	},

	validateKeypress: function(ev)
	{
		var keyCode = ev.keyCode ? ev.keyCode : ev.charCode;

		if (!ev.metaKey && !ev.ctrlKey && (
			(keyCode == Helper.RETURN_KEY) ||
			(ev.data.type == 'number' && !Ant.inArray(keyCode, Ant.EditableTable.Row.numericKeyCodes))
		))
		{
			ev.preventDefault();
		}
	},

	onTextareaHeightChange: function()
	{
		// Keep all the textareas' heights in sync
		var tallestTextareaHeight = -1;

		for (var i = 0; i < this.niceTexts.length; i++)
		{
			if (this.niceTexts[i].height > tallestTextareaHeight)
			{
				tallestTextareaHeight = this.niceTexts[i].height;
			}
		}

		this.$textareas.css('min-height', tallestTextareaHeight);

		// If the <td> is still taller, go with that insted
		var tdHeight = this.$textareas.first().parent().height();

		if (tdHeight > tallestTextareaHeight)
		{
			this.$textareas.css('min-height', tdHeight);
		}
	},

	deleteRow: function()
	{
		this.table.sorter.removeItems(this.$tr);
		this.$tr.remove();

		// onDeleteRow callback
		this.table.settings.onDeleteRow(this.$tr);
	}
},
{
	numericKeyCodes: [9 /* (tab) */ , 8 /* (delete) */ , 37,38,39,40 /* (arrows) */ , 45,91 /* (minus) */ , 46,190 /* period */ , 48,49,50,51,52,53,54,55,56,57 /* (0-9) */ ]
});


(function($) {


Ant.ElementActionTrigger = Helper.Base.extend(
{
	maxLevels: null,
	newChildUrl: null,
	$trigger: null,
	$selectedItems: null,
	triggerEnabled: true,

	init: function(settings)
	{
		this.setSettings(settings, Ant.ElementActionTrigger.defaults);

		this.$trigger = $('#'+settings.handle+'-actiontrigger');

		// Do we have a custom handler?
		if (this.settings.activate)
		{
			// Prevent the element index's click handler
			this.$trigger.data('custom-handler', true);

			// Is this a custom trigger?
			if (this.$trigger.prop('nodeName') == 'FORM')
			{
				this.addListener(this.$trigger, 'submit', 'handleTriggerActivation');
			}
			else
			{
				this.addListener(this.$trigger, 'click', 'handleTriggerActivation');
			}
		}

		this.updateTrigger();
		Ant.elementIndex.elementSelect.on('selectionChange', $.proxy(this, 'updateTrigger'));
	},

	updateTrigger: function()
	{
		// Ignore if the last element was just unselected
		if (Ant.elementIndex.elementSelect.totalSelected == 0)
		{
			return;
		}

		if (this.validateSelection())
		{
			this.enableTrigger();
		}
		else
		{
			this.disableTrigger();
		}
	},

	/**
	 * Determines if this action can be performed on the currently selected elements.
	 *
	 * @return bool
	 */
	validateSelection: function()
	{
		var valid = true;
		this.$selectedItems = Ant.elementIndex.elementSelect.$selectedItems;

		if (!this.settings.batch && this.$selectedItems.length > 1)
		{
			valid = false;
		}
		else if (typeof this.settings.validateSelection == 'function')
		{
			valid = this.settings.validateSelection(this.$selectedItems);
		}

		return valid;
	},

	enableTrigger: function()
	{
		if (this.triggerEnabled)
		{
			return;
		}

		this.$trigger.removeClass('disabled');
		this.triggerEnabled = true;
	},

	disableTrigger: function()
	{
		if (!this.triggerEnabled)
		{
			return;
		}

		this.$trigger.addClass('disabled');
		this.triggerEnabled = false;
	},

	handleTriggerActivation: function(ev)
	{
		ev.preventDefault();
		ev.stopPropagation();

		if (this.triggerEnabled)
		{
			this.settings.activate(this.$selectedItems);
		}
	}
},
{
	defaults: {
		handle: null,
		batch: true,
		validateSelection: null,
		activate: null
	}
});


})(jQuery)


/**
 * Element editor
 */
Ant.ElementEditor = Helper.Base.extend(
{
	$element: null,
	elementId: null,
	locale: null,

	$form: null,
	$fieldsContainer: null,
	$cancelBtn: null,
	$saveBtn: null,
	$spinner: null,

	$localeSelect: null,
	$localeSpinner: null,

	hud: null,

	init: function($element)
	{
		this.$element = $element;
		this.elementId = $element.data('id');

		this.$element.addClass('loading');

		var data = {
			elementId:      this.elementId,
			locale:         this.$element.data('locale'),
			includeLocales: true
		};

		Ant.postActionRequest('elements/getEditorHtml', data, $.proxy(this, 'showHud'));
	},

	showHud: function(response, textStatus)
	{
		this.$element.removeClass('loading');

		if (textStatus == 'success')
		{
			var $hudContents = $();

			if (response.locales)
			{
				var $localesContainer = $('<div class="header"/>'),
					$localeSelectContainer = $('<div class="select"/>').appendTo($localesContainer);

				this.$localeSelect = $('<select/>').appendTo($localeSelectContainer);
				this.$localeSpinner = $('<div class="spinner hidden"/>').appendTo($localesContainer);

				for (var i = 0; i < response.locales.length; i++)
				{
					var locale = response.locales[i];
					$('<option value="'+locale.id+'"'+(locale.id == response.locale ? ' selected="selected"' : '')+'>'+locale.name+'</option>').appendTo(this.$localeSelect);
				}

				this.addListener(this.$localeSelect, 'change', 'switchLocale');

				$hudContents = $hudContents.add($localesContainer);
			}

			this.$form = $('<form/>');
			this.$fieldsContainer = $('<div class="fields"/>').appendTo(this.$form);

			this.updateForm(response);

			var $buttonsOuterContainer = $('<div class="footer"/>').appendTo(this.$form);

			this.$spinner = $('<div class="spinner hidden"/>').appendTo($buttonsOuterContainer);

			var $buttonsContainer = $('<div class="buttons right"/>').appendTo($buttonsOuterContainer);
			this.$cancelBtn = $('<div class="btn">'+Ant.t('Cancel')+'</div>').appendTo($buttonsContainer);
			this.$saveBtn = $('<input class="btn submit" type="submit" value="'+Ant.t('Save')+'"/>').appendTo($buttonsContainer);

			$hudContents = $hudContents.add(this.$form);

			this.hud = new Helper.HUD(this.$element, $hudContents, {
				bodyClass: 'body elementeditor',
				closeOtherHUDs: false
			});

			this.hud.on('hide', $.proxy(function() {
				delete this.hud;
			}, this));

			this.addListener(this.$form, 'submit', 'saveElement');
			this.addListener(this.$cancelBtn, 'click', function() {
				this.hud.hide()
			});
		}
	},

	switchLocale: function()
	{
		var newLocale = this.$localeSelect.val();

		if (newLocale == this.locale)
		{
			return;
		}

		this.$localeSpinner.removeClass('hidden');

		var data = {
			elementId: this.elementId,
			locale:    newLocale
		};

		Ant.postActionRequest('elements/getEditorHtml', data, $.proxy(function(response, textStatus)
		{
			this.$localeSpinner.addClass('hidden');

			if (textStatus == 'success')
			{
				this.updateForm(response);
			}
			else
			{
				this.$localeSelect.val(this.locale);
			}
		}, this));
	},

	updateForm: function(response)
	{
		this.locale = response.locale;

		this.$fieldsContainer.html(response.html)
		Ant.initUiElements(this.$fieldsContainer);
	},

	saveElement: function(ev)
	{
		ev.preventDefault();

		this.$spinner.removeClass('hidden');

		var data = this.$form.serialize();

		Ant.postActionRequest('elements/saveElement', data, $.proxy(function(response, textStatus)
		{
			this.$spinner.addClass('hidden');

			if (textStatus == 'success')
			{
				if (textStatus == 'success' && response.success)
				{
					if (this.locale == this.$element.data('locale'))
					{
						// Update the label
						var $title = this.$element.find('.title'),
							$a = $title.find('a');

						if ($a.length && response.cpEditUrl)
						{
							$a.attr('href', response.cpEditUrl);
							$a.text(response.newTitle);
						}
						else
						{
							$title.text(response.newTitle);
						}
					}

					// Update Live Preview
					if (typeof Ant.livePreview != 'undefined')
					{
						Ant.livePreview.updateIframe(true);
					}

					this.closeHud();
				}
				else
				{
					this.updateForm(response);
					Helper.shake(this.hud.$hud);
				}
			}
		}, this));
	},

	closeHud: function()
	{
		this.hud.hide();
		delete this.hud;
	}
});


/**
 * Entry index class
 */
Ant.EntryIndex = Ant.BaseElementIndex.extend(
{
	$newEntryBtnGroup: null,
	$newEntryMenuBtn: null,
	newEntryLabel: null,

	onAfterHtmlInit: function()
	{
		// Figure out if there are multiple sections that entries can be created in
		this.$newEntryBtnGroup = this.$sidebar.find('> .buttons > .btngroup');

		if (this.$newEntryBtnGroup.length)
		{
			this.$newEntryMenuBtn = this.$newEntryBtnGroup.children('.menubtn');
			this.newEntryLabel = this.$newEntryMenuBtn.text();
		}

		this.base();
	},

	getDefaultSourceKey: function()
	{
		// Did they request a specific section in the URL?
		if (this.settings.context == 'index' && typeof defaultSectionHandle != typeof undefined)
		{
			if (defaultSectionHandle == 'singles')
			{
				return 'singles';
			}
			else
			{
				for (var i = 0; i < this.$sources.length; i++)
				{
					var $source = $(this.$sources[i]);

					if ($source.data('handle') == defaultSectionHandle)
					{
						return $source.data('key');
					}
				}
			}
		}

		return this.base();
	},

	onSelectSource: function()
	{
		if (this.settings.context == 'index')
		{
			// Get the handle of the selected source
			if (this.$source.data('key') == 'singles')
			{
				var handle = 'singles';
			}
			else
			{
				var handle = this.$source.data('handle');
			}

			// Update the URL
			if (typeof history != typeof undefined)
			{
				var uri = 'entries';

				if (handle)
				{
					uri += '/'+handle;
				}

				history.replaceState({}, '', Ant.getUrl(uri));
			}

			// Update the New Entry button
			if (this.$newEntryBtnGroup.length)
			{
				if (handle == 'singles' || !handle)
				{
					if (this.$newEntryBtn)
					{
						this.$newEntryBtn.remove();
						this.$newEntryBtn = null;
						this.$newEntryMenuBtn.addClass('add icon').text(this.newEntryLabel);
					}
				}
				else
				{
					if (this.$newEntryBtn)
					{
						this.$newEntryBtn.remove();
					}
					else
					{
						this.$newEntryMenuBtn.removeClass('add icon').text('');
					}

					this.$newEntryBtn = $('<a class="btn submit add icon"/>').text(this.newEntryLabel).prependTo(this.$newEntryBtnGroup);
					this.$newEntryBtn.attr('href', Ant.getUrl('entries/'+handle+'/new'));
				}
			}
		}

		this.base();
	}

});


Ant.FieldLayoutDesigner = Helper.Base.extend(
{
	$container: null,
	$tabContainer: null,
	$unusedFieldContainer: null,
	$newTabBtn: null,
	$allFields: null,

	tabGrid: null,
	unusedFieldGrid: null,

	tabDrag: null,
	fieldDrag: null,

	init: function(container, settings)
	{
		this.$container = $(container);
		this.setSettings(settings, Ant.FieldLayoutDesigner.defaults);

		this.$tabContainer = this.$container.children('.fld-tabs');
		this.$unusedFieldContainer = this.$container.children('.unusedfields');
		this.$newTabBtn = this.$container.find('> .newtabbtn-container > .btn');
		this.$allFields = this.$unusedFieldContainer.find('.fld-field');

		// Set up the layout grids
		this.tabGrid = new Ant.Grid(this.$tabContainer, Ant.FieldLayoutDesigner.gridSettings);
		this.unusedFieldGrid = new Ant.Grid(this.$unusedFieldContainer, Ant.FieldLayoutDesigner.gridSettings);

		var $tabs = this.$tabContainer.children();
		for (var i = 0; i < $tabs.length; i++)
		{
			this.initTab($($tabs[i]));
		}

		this.fieldDrag = new Ant.FieldLayoutDesigner.FieldDrag(this);

		if (this.settings.customizableTabs)
		{
			this.tabDrag = new Ant.FieldLayoutDesigner.TabDrag(this);

			this.addListener(this.$newTabBtn, 'activate', 'addTab');
		}
	},

	initTab: function($tab)
	{
		if (this.settings.customizableTabs)
		{
			var $editBtn = $tab.find('.tabs .settings'),
				$menu = $('<div class="menu" data-align="center"/>').insertAfter($editBtn),
				$ul = $('<ul/>').appendTo($menu);

			$('<li><a data-action="rename">'+Ant.t('Rename')+'</a></li>').appendTo($ul);
			$('<li><a data-action="delete">'+Ant.t('Delete')+'</a></li>').appendTo($ul);

			new Helper.MenuBtn($editBtn, {
				onOptionSelect: $.proxy(this, 'onTabOptionSelect')
			});
		}

		// Don't forget the fields!
		var $fields = $tab.children('.fld-tabcontent').children();

		for (var i = 0; i < $fields.length; i++)
		{
			this.initField($($fields[i]));
		}
	},

	initField: function($field)
	{
		var $editBtn = $field.find('.settings'),
			$menu = $('<div class="menu" data-align="center"/>').insertAfter($editBtn),
			$ul = $('<ul/>').appendTo($menu);

		if ($field.hasClass('fld-required'))
		{
			$('<li><a data-action="toggle-required">'+Ant.t('Make not required')+'</a></li>').appendTo($ul);
		}
		else
		{
			$('<li><a data-action="toggle-required">'+Ant.t('Make required')+'</a></li>').appendTo($ul);
		}

		$('<li><a data-action="remove">'+Ant.t('Remove')+'</a></li>').appendTo($ul);

		new Helper.MenuBtn($editBtn, {
			onOptionSelect: $.proxy(this, 'onFieldOptionSelect')
		});
	},

	onTabOptionSelect: function(option)
	{
		if (!this.settings.customizableTabs)
		{
			return;
		}

		var $option = $(option),
			$tab = $option.data('menu').$trigger.parent().parent().parent(),
			action = $option.data('action');

		switch (action)
		{
			case 'rename':
			{
				this.renameTab($tab);
				break;
			}
			case 'delete':
			{
				this.deleteTab($tab);
				break;
			}
		}
	},

	onFieldOptionSelect: function(option)
	{
		var $option = $(option),
			$field = $option.data('menu').$trigger.parent(),
			action = $option.data('action');

		switch (action)
		{
			case 'toggle-required':
			{
				this.toggleRequiredField($field, $option);
				break;
			}
			case 'remove':
			{
				this.removeField($field);
				break;
			}
		}
	},

	renameTab: function($tab)
	{
		if (!this.settings.customizableTabs)
		{
			return;
		}

		var $labelSpan = $tab.find('.tabs .tab span'),
			oldName = $labelSpan.text(),
			newName = prompt(Ant.t('Give your tab a name.'), oldName);

		if (newName && newName != oldName)
		{
			$labelSpan.text(newName);
			$tab.find('.id-input').attr('name', this.getFieldInputName(newName));
		}
	},

	deleteTab: function($tab)
	{
		if (!this.settings.customizableTabs)
		{
			return;
		}

		// Find all the fields in this tab
		var $fields = $tab.find('.fld-field');

		for (var i = 0; i < $fields.length; i++)
		{
			var fieldId = $($fields[i]).attr('data-id');
			this.removeFieldById(fieldId);
		}

		this.tabGrid.removeItems($tab);
		this.tabDrag.removeItems($tab);

		$tab.remove();
	},

	toggleRequiredField: function($field, $option)
	{
		if ($field.hasClass('fld-required'))
		{
			$field.removeClass('fld-required');
			$field.find('.required-input').remove();

			setTimeout(function() {
				$option.text(Ant.t('Make required'));
			}, 500);
		}
		else
		{
			$field.addClass('fld-required');
			$('<input class="required-input" type="hidden" name="'+this.settings.requiredFieldInputName+'" value="'+$field.data('id')+'">').appendTo($field);

			setTimeout(function() {
				$option.text(Ant.t('Make not required'));
			}, 500);
		}
	},

	removeField: function($field)
	{
		var fieldId = $field.attr('data-id');

		$field.remove();

		this.removeFieldById(fieldId);
		this.tabGrid.refreshCols(true);
	},

	removeFieldById: function(fieldId)
	{
		var $field = this.$allFields.filter('[data-id='+fieldId+']:first'),
			$group = $field.closest('.fld-tab');

		$field.removeClass('hidden');

		if ($group.hasClass('hidden'))
		{
			$group.removeClass('hidden');
			this.unusedFieldGrid.addItems($group);

			if (this.settings.customizableTabs)
			{
				this.tabDrag.addItems($group);
			}
		}
		else
		{
			this.unusedFieldGrid.refreshCols(true);
		}
	},

	addTab: function()
	{
		if (!this.settings.customizableTabs)
		{
			return;
		}

		var $tab = $('<div class="fld-tab">' +
						'<div class="tabs">' +
							'<div class="tab sel draggable">' +
								'<span>Tab '+(this.tabGrid.$items.length+1)+'</span>' +
								'<a class="settings icon" title="'+Ant.t('Rename')+'"></a>' +
							'</div>' +
						'</div>' +
						'<div class="fld-tabcontent"></div>' +
					'</div>').appendTo(this.$tabContainer);

		this.tabGrid.addItems($tab);
		this.tabDrag.addItems($tab);

		this.initTab($tab);
	},

	getFieldInputName: function(tabName)
	{
		return this.settings.fieldInputName.replace(/__TAB_NAME__/g, Ant.encodeUriComponent(tabName));
	}
},
{
	gridSettings: {
		itemSelector: '.fld-tab:not(.hidden)',
		minColWidth: 240,
		percentageWidths: false,
		fillMode: 'grid',
		snapToGrid: 30
	},
	defaults: {
		customizableTabs: true,
		fieldInputName: 'fieldLayout[__TAB_NAME__][]',
		requiredFieldInputName: 'requiredFields[]'
	}
});


Ant.FieldLayoutDesigner.BaseDrag = Helper.Drag.extend(
{
	designer: null,
	$insertion: null,
	showingInsertion: false,
	$caboose: null,
	draggingUnusedItem: false,
	addToTabGrid: false,

	/**
	 * Constructor
	 */
	init: function(designer, settings)
	{
		this.designer = designer;

		// Find all the items from both containers
		var $items = this.designer.$tabContainer.find(this.itemSelector)
			.add(this.designer.$unusedFieldContainer.find(this.itemSelector));

		this.base($items, settings);
	},

	/**
	 * On Drag Start
	 */
	onDragStart: function()
	{
		this.base();

		// Are we dragging an unused item?
		this.draggingUnusedItem = this.$draggee.hasClass('unused');

		// Create the insertion
		this.$insertion = this.getInsertion();

		// Add the caboose
		this.addCaboose();
		this.$items = $().add(this.$items.add(this.$caboose));

		if (this.addToTabGrid)
		{
			this.designer.tabGrid.addItems(this.$caboose);
		}

		// Swap the draggee with the insertion if dragging a selected item
		if (this.draggingUnusedItem)
		{
			this.showingInsertion = false;
		}
		else
		{
			// Actually replace the draggee with the insertion
			this.$insertion.insertBefore(this.$draggee);
			this.$draggee.detach();
			this.$items = $().add(this.$items.not(this.$draggee).add(this.$insertion));
			this.showingInsertion = true;

			if (this.addToTabGrid)
			{
				this.designer.tabGrid.removeItems(this.$draggee);
				this.designer.tabGrid.addItems(this.$insertion);
			}
		}

		this.setMidpoints();
	},

	/**
	 * Append the caboose
	 */
	addCaboose: $.noop,

	/**
	 * Returns the item's container
	 */
	getItemContainer: $.noop,

	/**
	 * Tests if an item is within the tab container.
	 */
	isItemInTabContainer: function($item)
	{
		return (this.getItemContainer($item)[0] == this.designer.$tabContainer[0]);
	},

	/**
	 * Sets the item midpoints up front so we don't have to keep checking on every mouse move
	 */
	setMidpoints: function()
	{
		for (var i = 0; i < this.$items.length; i++)
		{
			var $item = $(this.$items[i]);

			// Skip the unused tabs
			if (!this.isItemInTabContainer($item))
			{
				continue;
			}

			var offset = $item.offset();

			$item.data('midpoint', {
				left: offset.left + $item.outerWidth() / 2,
				top:  offset.top + $item.outerHeight() / 2
			});
		}
	},

	/**
	 * On Drag
	 */
	onDrag: function()
	{
		// Are we hovering over the tab container?
		if (this.draggingUnusedItem && !Helper.hitTest(this.mouseX, this.mouseY, this.designer.$tabContainer))
		{
			if (this.showingInsertion)
			{
				this.$insertion.remove();
				this.$items = $().add(this.$items.not(this.$insertion));
				this.showingInsertion = false;

				if (this.addToTabGrid)
				{
					this.designer.tabGrid.removeItems(this.$insertion);
				}
				else
				{
					this.designer.tabGrid.refreshCols(true);
				}

				this.setMidpoints();
			}
		}
		else
		{
			// Is there a new closest item?
			this.onDrag._closestItem = this.getClosestItem();

			if (this.onDrag._closestItem != this.$insertion[0])
			{
				if (this.showingInsertion &&
					($.inArray(this.$insertion[0], this.$items) < $.inArray(this.onDrag._closestItem, this.$items)) &&
					($.inArray(this.onDrag._closestItem, this.$caboose) == -1)
				)
				{
					this.$insertion.insertAfter(this.onDrag._closestItem);
				}
				else
				{
					this.$insertion.insertBefore(this.onDrag._closestItem);
				}

				this.$items = $().add(this.$items.add(this.$insertion));
				this.showingInsertion = true;

				if (this.addToTabGrid)
				{
					this.designer.tabGrid.addItems(this.$insertion);
				}
				else
				{
					this.designer.tabGrid.refreshCols(true);
				}

				this.setMidpoints();
			}
		}

		this.base();
	},

	/**
	 * Returns the closest item to the cursor.
	 */
	getClosestItem: function()
	{
		this.getClosestItem._closestItem = null;
		this.getClosestItem._closestItemMouseDiff = null;

		for (this.getClosestItem._i = 0; this.getClosestItem._i < this.$items.length; this.getClosestItem._i++)
		{
			this.getClosestItem._$item = $(this.$items[this.getClosestItem._i]);

			// Skip the unused tabs
			if (!this.isItemInTabContainer(this.getClosestItem._$item))
			{
				continue;
			}

			this.getClosestItem._midpoint = this.getClosestItem._$item.data('midpoint');
			this.getClosestItem._mouseDiff = Helper.getDist(this.getClosestItem._midpoint.left, this.getClosestItem._midpoint.top, this.mouseX, this.mouseY);

			if (this.getClosestItem._closestItem === null || this.getClosestItem._mouseDiff < this.getClosestItem._closestItemMouseDiff)
			{
				this.getClosestItem._closestItem = this.getClosestItem._$item[0];
				this.getClosestItem._closestItemMouseDiff = this.getClosestItem._mouseDiff;
			}
		}

		return this.getClosestItem._closestItem;
	},

	/**
	 * On Drag Stop
	 */
	onDragStop: function()
	{
		if (this.showingInsertion)
		{
			this.$insertion.replaceWith(this.$draggee);
			this.$items = $().add(this.$items.not(this.$insertion).add(this.$draggee));

			if (this.addToTabGrid)
			{
				this.designer.tabGrid.removeItems(this.$insertion);
				this.designer.tabGrid.addItems(this.$draggee);
			}
		}

		// Drop the caboose
		this.$items = this.$items.not(this.$caboose);
		this.$caboose.remove();

		if (this.addToTabGrid)
		{
			this.designer.tabGrid.removeItems(this.$caboose);
		}

		// "show" the drag items, but make them invisible
		this.$draggee.css({
			display:    this.draggeeDisplay,
			visibility: 'hidden'
		});

		this.designer.tabGrid.refreshCols(true);
		this.designer.unusedFieldGrid.refreshCols(true);

		// return the helpers to the draggees
		this.returnHelpersToDraggees();

		this.base();
	}
});


Ant.FieldLayoutDesigner.TabDrag = Ant.FieldLayoutDesigner.BaseDrag.extend(
{
	itemSelector: '> div.fld-tab',
	addToTabGrid: true,

	/**
	 * Constructor
	 */
	init: function(designer)
	{
		var settings = {
			handle: '.tab'
		};

		this.base(designer, settings);
	},

	/**
	 * Append the caboose
	 */
	addCaboose: function()
	{
		this.$caboose = $('<div class="fld-tab fld-tab-caboose"/>').appendTo(this.designer.$tabContainer);
	},

	/**
	 * Returns the insertion
	 */
	getInsertion: function()
	{
		var $tab = this.$draggee.find('.tab');

		return $('<div class="fld-tab fld-insertion" style="height: '+this.$draggee.height()+'px;">' +
					'<div class="tabs"><div class="tab sel draggable" style="width: '+$tab.width()+'px; height: '+$tab.height()+'px;"></div></div>' +
					'<div class="fld-tabcontent" style="height: '+this.$draggee.find('.fld-tabcontent').height()+'px;"></div>' +
				'</div>');
	},

	/**
	 * Returns the item's container
	 */
	getItemContainer: function($item)
	{
		return $item.parent();
	},

	/**
	 * On Drag Stop
	 */
	onDragStop: function()
	{
		if (this.draggingUnusedItem && this.showingInsertion)
		{
			// Create a new tab based on that field group
			var $tab = this.$draggee.clone().removeClass('unused'),
				tabName = $tab.find('.tab span').text();

			$tab.find('.fld-field').removeClass('unused');

			// Add the edit button
			$tab.find('.tabs .tab').append('<a class="settings icon" title="'+Ant.t('Edit')+'"></a>');

			// Remove any hidden fields
			var $fields = $tab.find('.fld-field'),
				$hiddenFields = $fields.filter('.hidden').remove();

			$fields = $fields.not($hiddenFields);
			$fields.prepend('<a class="settings icon" title="'+Ant.t('Edit')+'"></a>');

			for (var i = 0; i < $fields.length; i++)
			{
				var $field = $($fields[i]),
					inputName = this.designer.getFieldInputName(tabName);

				$field.append('<input class="id-input" type="hidden" name="'+inputName+'" value="'+$field.data('id')+'">');
			}

			this.designer.fieldDrag.addItems($fields);

			this.designer.initTab($tab);

			// Set the unused field group and its fields to hidden
			this.$draggee.css({ visibility: 'inherit', display: 'field' }).addClass('hidden');
			this.$draggee.find('.fld-field').addClass('hidden');

			// Set this.$draggee to the clone, as if we were dragging that all along
			this.$draggee = $tab;

			// Remember it for later
			this.addItems($tab);

			// Update the grids
			this.designer.tabGrid.addItems($tab);
			this.designer.unusedFieldGrid.removeItems(this.$draggee);
		}

		this.base();
	}
});


Ant.FieldLayoutDesigner.FieldDrag = Ant.FieldLayoutDesigner.BaseDrag.extend(
{
	itemSelector: '> div.fld-tab .fld-field',

	/**
	 * Append the caboose
	 */
	addCaboose: function()
	{
		this.$caboose = $();

		var $fieldContainers = this.designer.$tabContainer.children().children('.fld-tabcontent');

		for (var i = 0; i < $fieldContainers.length; i++)
		{
			var $caboose = $('<div class="fld-tab fld-tab-caboose"/>').appendTo($fieldContainers[i]);
			this.$caboose = this.$caboose.add($caboose);
		}
	},

	/**
	 * Returns the insertion
	 */
	getInsertion: function()
	{
		return $('<div class="fld-field fld-insertion" style="height: '+this.$draggee.height()+'px;"/>');
	},

	/**
	 * Returns the item's container
	 */
	getItemContainer: function($item)
	{
		return $item.parent().parent().parent();
	},

	/**
	 * On Drag Stop
	 */
	onDragStop: function()
	{
		if (this.draggingUnusedItem && this.showingInsertion)
		{
			// Create a new field based on that one
			var $field = this.$draggee.clone().removeClass('unused');
			$field.prepend('<a class="settings icon" title="'+Ant.t('Edit')+'"></a>');
			this.designer.initField($field);

			// Hide the unused field
			this.$draggee.css({ visibility: 'inherit', display: 'field' }).addClass('hidden');

			// Hide the group too?
			if (this.$draggee.siblings(':not(.hidden)').length == 0)
			{
				var $group = this.$draggee.parent().parent();
				$group.addClass('hidden');
				this.designer.unusedFieldGrid.removeItems($group);
			}

			// Set this.$draggee to the clone, as if we were dragging that all along
			this.$draggee = $field;

			// Remember it for later
			this.addItems($field);
		}

		if (this.showingInsertion)
		{
			// Find the field's new tab name
			var tabName = this.$insertion.parent().parent().find('.tab span').text(),
				inputName = this.designer.getFieldInputName(tabName);

			if (this.draggingUnusedItem)
			{
				this.$draggee.append('<input class="id-input" type="hidden" name="'+inputName+'" value="'+this.$draggee.data('id')+'">');
			}
			else
			{
				this.$draggee.find('.id-input').attr('name', inputName);
			}
		}

		this.base();
	}
});


/**
 * FieldToggle
 */
Ant.FieldToggle = Helper.Base.extend(
{
	$toggle: null,
	targetPrefix: null,
	targetSelector: null,
	reverseTargetSelector: null,

	_$target: null,
	_$reverseTarget: null,
	type: null,

	init: function(toggle)
	{
		this.$toggle = $(toggle);

		// Is this already a field toggle?
		if (this.$toggle.data('fieldtoggle'))
		{
			Helper.log('Double-instantiating a field toggle on an element');
			this.$toggle.data('fieldtoggle').destroy();
		}

		this.$toggle.data('fieldtoggle', this);

		this.type = this.getType();

		if (this.type == 'select')
		{
			this.targetPrefix = (this.$toggle.attr('data-target-prefix') || '');
		}
		else
		{
			this.targetSelector = this.normalizeTargetSelector(this.$toggle.data('target'));
			this.reverseTargetSelector = this.normalizeTargetSelector(this.$toggle.data('reverse-target'));
		}

		this.findTargets();

		if (this.type == 'link')
		{
			this.addListener(this.$toggle, 'click', 'onToggleChange');
		}
		else
		{
			this.addListener(this.$toggle, 'change', 'onToggleChange');
		}
	},

	normalizeTargetSelector: function(selector)
	{
		if (selector && !selector.match(/^[#\.]/))
		{
			selector = '#'+selector;
		}

		return selector;
	},

	getType: function()
	{
		if (this.$toggle.prop('nodeName') == 'INPUT' && this.$toggle.attr('type').toLowerCase() == 'checkbox')
		{
			return 'checkbox';
		}
		else if (this.$toggle.prop('nodeName') == 'SELECT')
		{
			return 'select';
		}
		else if (this.$toggle.prop('nodeName') == 'A')
		{
			return 'link';
		}
		else if (this.$toggle.prop('nodeName') == 'DIV' && this.$toggle.hasClass('lightswitch'))
		{
			return 'lightswitch';
		}
	},

	findTargets: function()
	{
		if (this.type == 'select')
		{
			this._$target = $(this.normalizeTargetSelector(this.targetPrefix+this.getToggleVal()));
		}
		else
		{
			if (this.targetSelector)
			{
				this._$target = $(this.targetSelector);
			}

			if (this.reverseTargetSelector)
			{
				this._$reverseTarget = $(this.reverseTargetSelector);
			}
		}
	},

	getToggleVal: function()
	{
		if (this.type == 'lightswitch')
		{
			return this.$toggle.children('input').val();
		}
		else
		{
			return Helper.getInputPostVal(this.$toggle);
		}
	},

	onToggleChange: function()
	{
		if (this.type == 'select')
		{
			this.hideTarget(this._$target);
			this.findTargets();
			this.showTarget(this._$target);
		}
		else
		{
			if (this.type == 'link')
			{
				this.onToggleChange._show = this.$toggle.hasClass('collapsed') || !this.$toggle.hasClass('expanded');
			}
			else
			{
				this.onToggleChange._show = !!this.getToggleVal();
			}

			if (this.onToggleChange._show)
			{
				this.showTarget(this._$target);
				this.hideTarget(this._$reverseTarget);
			}
			else
			{
				this.hideTarget(this._$target);
				this.showTarget(this._$reverseTarget);
			}

			delete this.onToggleChange._show;
		}
	},

	showTarget: function($target)
	{
		if ($target && $target.length)
		{
			this.showTarget._currentHeight = $target.height();

			$target.removeClass('hidden');

			if (this.type != 'select')
			{
				if (this.type == 'link')
				{
					this.$toggle.removeClass('collapsed');
					this.$toggle.addClass('expanded');
				}

				$target.height('auto');
				this.showTarget._targetHeight = $target.height();
				$target.css({
					height: this.showTarget._currentHeight,
					overflow: 'hidden'
				});

				$target.velocity('stop');

				$target.velocity({ height: this.showTarget._targetHeight }, 'fast', function()
				{
					$target.css({
						height: '',
						overflow: ''
					});
				});

				delete this.showTarget._targetHeight;
			}

			delete this.showTarget._currentHeight;

			// Trigger a resize event in case there are any grids in the target that need to initialize
			Helper.$win.trigger('resize');
		}
	},

	hideTarget: function($target)
	{
		if ($target && $target.length)
		{
			if (this.type == 'select')
			{
				$target.addClass('hidden');
			}
			else
			{
				if (this.type == 'link')
				{
					this.$toggle.removeClass('expanded');
					this.$toggle.addClass('collapsed');
				}

				$target.css('overflow', 'hidden');
				$target.velocity('stop');
				$target.velocity({ height: 0 }, 'fast', function()
				{
					$target.addClass('hidden');
				});
			}
		}
	}
});

/**
 * Structure class
 */
Ant.Structure = Helper.Base.extend(
{
	id: null,

	$container: null,
	state: null,
	structureDrag: null,

	/**
	 * Init
	 */
	init: function(id, container, settings)
	{
		this.id = id;
		this.$container = $(container);
		this.setSettings(settings, Ant.Structure.defaults);

		// Is this already a structure?
		if (this.$container.data('structure'))
		{
			Helper.log('Double-instantiating a structure on an element');
			this.$container.data('structure').destroy();
		}

		this.$container.data('structure', this);

		this.state = {};

		if (this.settings.storageKey)
		{
			$.extend(this.state, Ant.getLocalStorage(this.settings.storageKey, {}));
		}

		if (typeof this.state.collapsedElementIds == 'undefined')
		{
			this.state.collapsedElementIds = [];
		}

		var $parents = this.$container.find('ul').prev('.row');

		for (var i = 0; i < $parents.length; i++)
		{
			var $row = $($parents[i]),
				$li = $row.parent(),
				$toggle = $('<div class="toggle" title="'+Ant.t('Show/hide children')+'"/>').prependTo($row);

			if ($.inArray($row.children('.element').data('id'), this.state.collapsedElementIds) != -1)
			{
				$li.addClass('collapsed');
			}

			this.initToggle($toggle);
		}

		if (this.settings.sortable)
		{
			this.structureDrag = new Ant.StructureDrag(this, this.settings.maxLevels);
		}

		if (this.settings.newChildUrl)
		{
			this.initNewChildMenus(this.$container.find('.add'));
		}
	},

	initToggle: function($toggle)
	{
		$toggle.click($.proxy(function(ev)
		{
			var $li = $(ev.currentTarget).closest('li'),
				elementId = $li.children('.row').find('.element:first').data('id'),
				viewStateKey = $.inArray(elementId, this.state.collapsedElementIds);

			if ($li.hasClass('collapsed'))
			{
				$li.removeClass('collapsed');

				if (viewStateKey != -1)
				{
					this.state.collapsedElementIds.splice(viewStateKey, 1);
				}
			}
			else
			{
				$li.addClass('collapsed');

				if (viewStateKey == -1)
				{
					this.state.collapsedElementIds.push(elementId);
				}
			}

			if (this.settings.storageKey)
			{
				Ant.setLocalStorage(this.settings.storageKey, this.state);
			}

		}, this));
	},

	initNewChildMenus: function($addBtns)
	{
		this.addListener($addBtns, 'click', 'onNewChildMenuClick');
	},

	onNewChildMenuClick: function(ev)
	{
		var $btn = $(ev.currentTarget);

		if (!$btn.data('menubtn'))
		{
			var elementId = $btn.parent().children('.element').data('id'),
				newChildUrl = Ant.getUrl(this.settings.newChildUrl, 'parentId='+elementId),
				$menu = $('<div class="menu"><ul><li><a href="'+newChildUrl+'">'+Ant.t('New child')+'</a></li></ul></div>').insertAfter($btn);

			var menuBtn = new Helper.MenuBtn($btn);
			menuBtn.showMenu();
		}
	},

	getIndent: function(level)
	{
		return Ant.Structure.baseIndent + (level-1) * Ant.Structure.nestedIndent;
	},

	addElement: function($element)
	{
		var $li = $('<li data-level="1"/>').appendTo(this.$container),
			$row = $('<div class="row" style="margin-'+Ant.left+': -'+Ant.Structure.baseIndent+'px; padding-'+Ant.left+': '+Ant.Structure.baseIndent+'px;">').appendTo($li);

		$row.append($element);

		if (this.settings.sortable)
		{
			$row.append('<a class="move icon" title="'+Ant.t('Move')+'"></a>');
			this.structureDrag.addItems($li);
		}

		if (this.settings.newChildUrl)
		{
			var $addBtn = $('<a class="add icon" title="'+Ant.t('New Child')+'"></a>').appendTo($row);
			this.initNewChildMenus($addBtn);
		}

		$row.css('margin-bottom', -30);
		$row.velocity({ 'margin-bottom': 0 }, 'fast');
	},

	removeElement: function($element)
	{
		var $li = $element.parent().parent();

		if (this.settings.sortable)
		{
			this.structureDrag.removeItems($li);
		}

		if (!$li.siblings().length)
		{
			var $parentUl = $li.parent();
		}

		$li.css('visibility', 'hidden').velocity({ marginBottom: -$li.height() }, 'fast', $.proxy(function()
		{
			$li.remove();

			if (typeof $parentUl != 'undefined')
			{
				this._removeUl($parentUl);
			}
		}, this));
	},

	_removeUl: function($ul)
	{
		$ul.siblings('.row').children('.toggle').remove();
		$ul.remove();
	}
},
{
	baseIndent: 8,
	nestedIndent: 35,

	defaults: {
		storageKey:  null,
		sortable:    false,
		newChildUrl: null,
		maxLevels:   null
	}
});


/**
 * Structure drag class
 */
Ant.StructureDrag = Helper.Drag.extend(
{
	structure: null,
	maxLevels: null,
	draggeeLevel: null,

	$helperLi: null,
	$targets: null,
	draggeeHeight: null,

	init: function(structure, maxLevels)
	{
		this.structure = structure;
		this.maxLevels = maxLevels;

		this.$insertion = $('<li class="draginsertion"/>');

		var $items = this.structure.$container.find('li');

		this.base($items, {
			handle: '.element:first, .move:first',
			helper: $.proxy(this, 'getHelper')
		});
	},

	getHelper: function($helper)
	{
		this.$helperLi = $helper;
		var $ul = $('<ul class="structure draghelper"/>').append($helper);
		$helper.css('padding-'+Ant.left, this.$draggee.css('padding-'+Ant.left));
		$helper.find('.move').removeAttr('title');
		return $ul;
	},

	onDragStart: function()
	{	
		this.$targets = $();

		// Recursively find each of the targets, in the order they appear to be in
		this.findTargets(this.structure.$container);

		// How deep does the rabbit hole go?
		this.draggeeLevel = 0;
		var $level = this.$draggee;
		do {
			this.draggeeLevel++;
			$level = $level.find('> ul > li');
		} while($level.length);

		// Collapse the draggee
		this.draggeeHeight = this.$draggee.height();
		this.$draggee.velocity({
			height: 0
		}, 'fast', $.proxy(function() {
			this.$draggee.addClass('hidden');
		}, this));
		this.base();

		this.addListener(Helper.$doc, 'keydown', function(ev)
		{
			if (ev.keyCode == Helper.ESC_KEY)
			{
				this.cancelDrag();
			}
		});
	},

	findTargets: function($ul)
	{
		var $lis = $ul.children().not(this.$draggee);

		for (var i = 0; i < $lis.length; i++)
		{
			var $li = $($lis[i]);
			this.$targets = this.$targets.add($li.children('.row'));

			if (!$li.hasClass('collapsed'))
			{
				this.findTargets($li.children('ul'));
			}
		}
	},

	onDrag: function()
	{
		if (this._.$closestTarget)
		{
			this._.$closestTarget.removeClass('draghover');
			this.$insertion.remove();
		}

		// First let's find the closest target
		this._.$closestTarget = null;
		this._.closestTargetPos = null;
		this._.closestTargetYDiff = null;
		this._.closestTargetOffset = null;
		this._.closestTargetHeight = null;

		for (this._.i = 0; this._.i < this.$targets.length; this._.i++)
		{
			this._.$target = $(this.$targets[this._.i]);
			this._.targetOffset = this._.$target.offset();
			this._.targetHeight = this._.$target.outerHeight();
			this._.targetYMidpoint = this._.targetOffset.top + (this._.targetHeight / 2);
			this._.targetYDiff = Math.abs(this.mouseY - this._.targetYMidpoint);

			if (this._.i == 0 || (this.mouseY >= this._.targetOffset.top + 5 && this._.targetYDiff < this._.closestTargetYDiff))
			{
				this._.$closestTarget = this._.$target;
				this._.closestTargetPos = this._.i;
				this._.closestTargetYDiff = this._.targetYDiff;
				this._.closestTargetOffset = this._.targetOffset;
				this._.closestTargetHeight = this._.targetHeight;
			}
			else
			{
				// Getting colder
				break;
			}
		}

		if (!this._.$closestTarget)
		{
			return;
		}

		// Are we hovering above the first row?
		if (this._.closestTargetPos == 0 && this.mouseY < this._.closestTargetOffset.top + 5)
		{
			this.$insertion.prependTo(this.structure.$container);
		}
		else
		{
			this._.$closestTargetLi = this._.$closestTarget.parent();
			this._.closestTargetLevel = this._.$closestTargetLi.data('level');

			// Is there a next row?
			if (this._.closestTargetPos < this.$targets.length - 1)
			{
				this._.$nextTargetLi = $(this.$targets[this._.closestTargetPos+1]).parent();
				this._.nextTargetLevel = this._.$nextTargetLi.data('level');
			}
			else
			{
				this._.$nextTargetLi = null;
				this._.nextTargetLevel = null;
			}

			// Are we hovering between this row and the next one?
			this._.hoveringBetweenRows = (this.mouseY >= this._.closestTargetOffset.top + this._.closestTargetHeight - 5);

			/**
			 * Scenario 1: Both rows have the same level.
			 *
			 *     * Row 1
			 *     ----------------------
			 *     * Row 2
			 */

			if (this._.$nextTargetLi && this._.nextTargetLevel == this._.closestTargetLevel)
			{
				if (this._.hoveringBetweenRows)
				{
					if (!this.maxLevels || this.maxLevels >= (this._.closestTargetLevel + this.draggeeLevel - 1))
					{
						// Position the insertion after the closest target
						this.$insertion.insertAfter(this._.$closestTargetLi);
					}

				}
				else
				{
					if (!this.maxLevels || this.maxLevels >= (this._.closestTargetLevel + this.draggeeLevel))
					{
						this._.$closestTarget.addClass('draghover');
					}
				}
			}

			/**
			 * Scenario 2: Next row is a child of this one.
			 *
			 *     * Row 1
			 *     ----------------------
			 *         * Row 2
			 */

			else if (this._.$nextTargetLi && this._.nextTargetLevel > this._.closestTargetLevel)
			{
				if (!this.maxLevels || this.maxLevels >= (this._.nextTargetLevel + this.draggeeLevel - 1))
				{
					if (this._.hoveringBetweenRows)
					{
						// Position the insertion as the first child of the closest target
						this.$insertion.insertBefore(this._.$nextTargetLi);
					}
					else
					{
						this._.$closestTarget.addClass('draghover');
						this.$insertion.appendTo(this._.$closestTargetLi.children('ul'));
					}
				}
			}

			/**
			 * Scenario 3: Next row is a child of a parent node, or there is no next row.
			 *
			 *         * Row 1
			 *     ----------------------
			 *     * Row 2
			 */

			else
			{
				if (this._.hoveringBetweenRows)
				{
					// Determine which <li> to position the insertion after
					this._.draggeeX = this.mouseX - this.targetItemMouseDiffX;

					if (Ant.orientation == 'rtl')
					{
						this._.draggeeX += this.$helperLi.width();
					}

					this._.$parentLis = this._.$closestTarget.parentsUntil(this.structure.$container, 'li');
					this._.$closestParentLi = null;
					this._.closestParentLiXDiff = null;
					this._.closestParentLevel = null;

					for (this._.i = 0; this._.i < this._.$parentLis.length; this._.i++)
					{
						this._.$parentLi = $(this._.$parentLis[this._.i]);
						this._.parentLiX = this._.$parentLi.offset().left;

						if (Ant.orientation == 'rtl')
						{
							this._.parentLiX += this._.$parentLi.width();
						}

						this._.parentLiXDiff = Math.abs(this._.parentLiX - this._.draggeeX);
						this._.parentLevel = this._.$parentLi.data('level');

						if ((!this.maxLevels || this.maxLevels >= (this._.parentLevel + this.draggeeLevel - 1)) && (
							!this._.$closestParentLi || (
								this._.parentLiXDiff < this._.closestParentLiXDiff &&
								(!this._.$nextTargetLi || this._.parentLevel >= this._.nextTargetLevel)
							)
						))
						{
							this._.$closestParentLi = this._.$parentLi;
							this._.closestParentLiXDiff = this._.parentLiXDiff;
							this._.closestParentLevel = this._.parentLevel;
						}
					}

					if (this._.$closestParentLi)
					{
						this.$insertion.insertAfter(this._.$closestParentLi);
					}
				}
				else
				{
					if (!this.maxLevels || this.maxLevels >= (this._.closestTargetLevel + this.draggeeLevel))
					{
						this._.$closestTarget.addClass('draghover');
					}
				}
			}
		}
	},

	cancelDrag: function()
	{
		this.$insertion.remove();

		if (this._.$closestTarget)
		{
			this._.$closestTarget.removeClass('draghover');
		}

		this.onMouseUp();
	},

	onDragStop: function()
	{
		// Are we repositioning the draggee?
		if (this._.$closestTarget && (this.$insertion.parent().length || this._.$closestTarget.hasClass('draghover')))
		{
			// Are we about to leave the draggee's original parent childless?
			if (!this.$draggee.siblings().length)
			{
				var $draggeeParent = this.$draggee.parent();
			}
			else
			{
				var $draggeeParent = null;
			}

			if (this.$insertion.parent().length)
			{
				// Make sure the insertion isn't right next to the draggee
				var $closestSiblings = this.$insertion.next().add(this.$insertion.prev());

				if ($.inArray(this.$draggee[0], $closestSiblings) == -1)
				{
					this.$insertion.replaceWith(this.$draggee);
					var moved = true;
				}
				else
				{
					this.$insertion.remove();
					var moved = false;
				}
			}
			else
			{
				var $ul = this._.$closestTargetLi.children('ul');

				// Make sure this is a different parent than the draggee's
				if (!$draggeeParent || !$ul.length || $ul[0] != $draggeeParent[0])
				{
					if (!$ul.length)
					{
						var $toggle = $('<div class="toggle" title="'+Ant.t('Show/hide children')+'"/>').prependTo(this._.$closestTarget);
						this.structure.initToggle($toggle);

						$ul = $('<ul>').appendTo(this._.$closestTargetLi);
					}
					else if (this._.$closestTargetLi.hasClass('collapsed'))
					{
						this._.$closestTarget.children('.toggle').trigger('click');
					}

					this.$draggee.appendTo($ul);
					var moved = true;
				}
				else
				{
					var moved = false;
				}
			}

			// Remove the class either way
			this._.$closestTarget.removeClass('draghover');

			if (moved)
			{
				// Now deal with the now-childless parent
				if ($draggeeParent)
				{
					this.structure._removeUl($draggeeParent);
				}

				// Has the level changed?
				var newLevel = this.$draggee.parentsUntil(this.structure.$container, 'li').length + 1;

				if (newLevel != this.$draggee.data('level'))
				{
					// Correct the helper's padding if moving to/from level 1
					if (this.$draggee.data('level') == 1)
					{
						var animateCss = {};
						animateCss['padding-'+Ant.left] = 38;
						this.$helperLi.velocity(animateCss, 'fast');
					}
					else if (newLevel == 1)
					{
						var animateCss = {};
						animateCss['padding-'+Ant.left] = Ant.Structure.baseIndent;
						this.$helperLi.velocity(animateCss, 'fast');
					}

					this.setLevel(this.$draggee, newLevel);
				}

				// Make it real
				var $element = this.$draggee.children('.row').children('.element');

				var data = {
					structureId: this.structure.id,
					elementId:   $element.data('id'),
					locale:      $element.data('locale'),
					prevId:      this.$draggee.prev().children('.row').children('.element').data('id'),
					parentId:    this.$draggee.parent('ul').parent('li').children('.row').children('.element').data('id')
				};

				Ant.postActionRequest('structures/moveElement', data, function(response, textStatus)
				{
					if (textStatus == 'success')
					{
						Ant.cp.displayNotice(Ant.t('New order saved.'));
					}

				});
			}
		}

		// Animate things back into place
		this.$draggee.velocity('stop').removeClass('hidden').velocity({
			height: this.draggeeHeight
		}, 'fast', $.proxy(function() {
			this.$draggee.css('height', 'auto');
		}, this));

		this.returnHelpersToDraggees();

		this.base();
	},

	setLevel: function($li, level)
	{
		$li.data('level', level);

		var indent = this.structure.getIndent(level);

		var css = {};
		css['margin-'+Ant.left] = '-'+indent+'px';
		css['padding-'+Ant.left] = indent+'px';
		this.$draggee.children('.row').css(css);

		var $childLis = $li.children('ul').children();

		for (var i = 0; i < $childLis.length; i++)
		{
			this.setLevel($($childLis[i]), level+1);
		}
	}

});


Ant.StructureTableSorter = Helper.DragSort.extend({

	// Properties
	// =========================================================================

	elementIndex: null,
	structureId: null,
	maxLevels: null,

	_helperMargin: null,

	_$firstRowCells: null,
	_$titleHelperCell: null,

	_titleHelperCellOuterWidth: null,

	_ancestors: null,
	_updateAncestorsFrame: null,
	_updateAncestorsProxy: null,

	_draggeeLevel: null,
	_draggeeLevelDelta: null,
	draggingLastElements: null,
	_loadingDraggeeLevelDelta: false,

	_targetLevel: null,
	_targetLevelBounds: null,

	_positionChanged: null,

	// Public methods
	// =========================================================================

	/**
	 * Constructor
	 */
	init: function(elementIndex, $elements, settings)
	{
		this.elementIndex = elementIndex;
		//this.structureId = this.elementIndex.$table.data('structure-id');
		
		this.structureId = $('#settings-grid').data('structure-id');
		
		
		this.maxLevels = parseInt(this.elementIndex.$table.attr('data-max-levels'));

		settings = $.extend({}, Ant.StructureTableSorter.defaults, settings, {
			handle:           '.move',
			collapseDraggees: true,
			singleHelper:     true,
			helperSpacingY:   2,
			magnetStrength:   4,
			helper:           $.proxy(this, 'getHelper'),
			helperLagBase:    1.5,
			axis:             Helper.Y_AXIS
		});
		
		this.base($elements, settings);
		
		var self = this;
		
		$elements.each(function(event) {
			var level = $(this).data('level');
			var padding = Ant.StructureTableSorter.BASE_PADDING + (self.elementIndex.actions ? 14 : 0) + self._getLevelIndent(level);
			$(this).children('[data-titlecell]:first').css('padding-'+Ant.left, padding);
		});

	},

	/**
	 * Start Dragging
	 */
	startDragging: function()
	{
		this._helperMargin = Ant.StructureTableSorter.HELPER_MARGIN + (this.elementIndex.actions ? 24 : 0);
		this.base();
	},

	/**
	 * Returns the draggee rows (including any descendent rows).
	 */
	findDraggee: function()
	{
		this._draggeeLevel = this._targetLevel = this.$targetItem.data('level');
		this._draggeeLevelDelta = 0;

		var $draggee = $(this.$targetItem),
			$nextRow = this.$targetItem.next();

		while ($nextRow.length)
		{
			// See if this row is a descendant of the draggee
			var nextRowLevel = $nextRow.data('level');

			if (nextRowLevel <= this._draggeeLevel)
			{
				break;
			}

			// Is this the deepest descendant we've seen so far?
			var nextRowLevelDelta = nextRowLevel - this._draggeeLevel;

			if (nextRowLevelDelta > this._draggeeLevelDelta)
			{
				this._draggeeLevelDelta = nextRowLevelDelta;
			}

			// Add it and prep the next row
			$draggee = $draggee.add($nextRow);
			$nextRow = $nextRow.next();
		}

		// Are we dragging the last elements on the page?
		this.draggingLastElements = !$nextRow.length;

		// Do we have a maxLevels to enforce,
		// and does it look like this draggee has descendants we don't know about yet?
		if (
			this.maxLevels &&
			this.draggingLastElements &&
			this.elementIndex.morePending
		)
		{
			// Only way to know the true descendant level delta is to ask PHP
			this._loadingDraggeeLevelDelta = true;

			var data = this._getAjaxBaseData(this.$targetItem);

			Ant.postActionRequest('structures/getElementLevelDelta', data, $.proxy(function(response, textStatus)
			{
				if (textStatus == 'success')
				{
					this._loadingDraggeeLevelDelta = false;

					if (this.dragging)
					{
						this._draggeeLevelDelta = response.delta;
						this.drag(false);
					}
				}
			}, this));
		}

		return $draggee;
	},

	/**
	 * Returns the drag helper.
	 */
	getHelper: function($helperRow)
	{
		var $outerContainer = $('<div class="elements datatablesorthelper"/>').appendTo(Helper.$bod),
			$innerContainer = $('<div class="tableview"/>').appendTo($outerContainer),
			$table = $('<table class="data"/>').appendTo($innerContainer),
			$tbody = $('<tbody/>').appendTo($table);

		$helperRow.appendTo($tbody);

		// Copy the column widths
		this._$firstRowCells = this.elementIndex.$elementContainer.children('tr:first').children();
		var $helperCells = $helperRow.children();

		for (var i = 0; i < $helperCells.length; i++)
		{
			var $helperCell = $($helperCells[i]);

			// Skip the checkbox cell
			if (Helper.hasAttr($helperCell, 'data-checkboxcell'))
			{
				$helperCell.remove();
				continue;
			}

			// Hard-set the cell widths
			var $firstRowCell = $(this._$firstRowCells[i]),
				width = $firstRowCell.width();

			$firstRowCell.width(width);
			$helperCell.width(width);

			// Is this the title cell?
			if (Helper.hasAttr($firstRowCell, 'data-titlecell'))
			{
				this._$titleHelperCell = $helperCell;

				var padding = parseInt($firstRowCell.css('padding-'+Ant.left));
				this._titleHelperCellOuterWidth = width + padding - (this.elementIndex.actions ? 12 : 0);

				$helperCell.css('padding-'+Ant.left, Ant.StructureTableSorter.BASE_PADDING);
			}
		}

		return $outerContainer;
	},

	/**
	 * Returns whether the draggee can be inserted before a given item.
	 */
	canInsertBefore: function($item)
	{
		if (this._loadingDraggeeLevelDelta)
		{
			return false;
		}

		return (this._getLevelBounds($item.prev(), $item) !== false);
	},

	/**
	 * Returns whether the draggee can be inserted after a given item.
	 */
	canInsertAfter: function($item)
	{
		if (this._loadingDraggeeLevelDelta)
		{
			return false;
		}

		return (this._getLevelBounds($item, $item.next()) !== false);
	},

	// Events
	// -------------------------------------------------------------------------

	/**
	 * On Drag Start
	 */
	onDragStart: function()
	{
		// Get the initial set of ancestors, before the item gets moved
		this._ancestors = this._getAncestors(this.$targetItem, this.$targetItem.data('level'));

		// Set the initial target level bounds
		this._setTargetLevelBounds();

		// Check to see if we should load more elements now
		this.elementIndex.maybeLoadMore();

		this.base();
	},

	/**
	 * On Drag
	 */
	onDrag: function()
	{
		this.base();
		this._updateIndent();
	},

	/**
	 * On Insertion Point Change
	 */
	onInsertionPointChange: function()
	{
		this._setTargetLevelBounds();
		this._updateAncestorsBeforeRepaint();
		this.base();
	},

	/**
	 * On Drag Stop
	 */
	onDragStop: function()
	{
		this._positionChanged = false;
		this.base();

		// Update the draggee's padding if the position just changed
		// ---------------------------------------------------------------------

		if (this._targetLevel != this._draggeeLevel)
		{
			var levelDiff = this._targetLevel - this._draggeeLevel;

			for (var i = 0; i < this.$draggee.length; i++)
			{
				var $draggee = $(this.$draggee[i]),
					oldLevel = $draggee.data('level'),
					newLevel = oldLevel + levelDiff,
					padding = Ant.StructureTableSorter.BASE_PADDING + (this.elementIndex.actions ? 14 : 0) + this._getLevelIndent(newLevel);

				$draggee.data('level', newLevel);
				$draggee.find('.element').data('level', newLevel);
				$draggee.children('[data-titlecell]:first').css('padding-'+Ant.left, padding);
			}

			this._positionChanged = true;
		}

		// Keep in mind this could have also been set by onSortChange()
		if (this._positionChanged)
		{
			// Tell the server about the new position
			// -----------------------------------------------------------------

			var data = this._getAjaxBaseData(this.$draggee);

			// Find the previous sibling/parent, if there is one
			var $prevRow = this.$draggee.first().prev();

			while ($prevRow.length)
			{
				var prevRowLevel = $prevRow.data('level');

				if (prevRowLevel == this._targetLevel)
				{
					data.prevId = $prevRow.data('id');
					break;
				}

				if (prevRowLevel < this._targetLevel)
				{
					data.parentId = $prevRow.data('id');

					// Is this row collapsed?
					var $toggle = $prevRow.find('> td > .toggle');

					if (!$toggle.hasClass('expanded'))
					{
						// Make it look expanded
						$toggle.addClass('expanded');

						// Add a temporary row
						var $spinnerRow = this.elementIndex._createSpinnerRowAfter($prevRow);

						// Remove the target item
						//if (this.elementIndex.elementSelect)
						//{
							//this.elementIndex.elementSelect.removeItems(this.$targetItem);
						//}

						//this.removeItems(this.$targetItem);
						//this.$targetItem.remove();
						//this.elementIndex._totalVisible--;
					}

					break;
				}

				$prevRow = $prevRow.prev();
			}
			
			var self = this;
			
			$.ajax({
				data: data,
				url: this.settings.moveElementUrl,
				success: function(response, textStatus) {
					if (textStatus == 'success') {
						//Ant.cp.displayNotice(Ant.t('New position saved.'));
						self.onPositionChange();

						// Were we waiting on this to complete so we can expand the new parent?
						if ($spinnerRow && $spinnerRow.parent().length)
						{
							$spinnerRow.remove();
							self.elementIndex._expandElement($toggle, true);
						}
					}
				}
			});
			//}, this));
		}
	},

	onSortChange: function()
	{
		if (this.elementIndex.elementSelect)
		{
			this.elementIndex.elementSelect.resetItemOrder();
		}

		this._positionChanged = true;
		this.base();
	},

	onPositionChange: function()
	{
		Helper.requestAnimationFrame($.proxy(function()
		{
			this.trigger('positionChange');
			this.settings.onPositionChange();
		}, this));
	},

	onReturnHelpersToDraggees: function()
	{
		this._$firstRowCells.css('width', '');

		// If we were dragging the last elements on the page and ended up loading any additional elements in,
		// there could be a gap between the last draggee item and whatever now comes after it.
		// So remove the post-draggee elements and possibly load up the next batch.
		if (this.draggingLastElements && this.elementIndex.morePending)
		{
			// Update the element index's record of how many items are actually visible
			this.elementIndex._totalVisible += (this.newDraggeeIndexes[0] - this.oldDraggeeIndexes[0]);

			var $postDraggeeItems = this.$draggee.last().nextAll();

			if ($postDraggeeItems.length)
			{
				this.removeItems($postDraggeeItems);
				$postDraggeeItems.remove();
				this.elementIndex.maybeLoadMore();
			}
		}

		this.base();
	},

	// Private methods
	// =========================================================================

	/**
	 * Returns the min and max levels that the draggee could occupy between
	 * two given rows, or false if it’s not going to work out.
	 */
	_getLevelBounds: function($prevRow, $nextRow)
	{
		// Can't go any lower than the next row, if there is one
		if ($nextRow && $nextRow.length)
		{
			this._getLevelBounds._minLevel = $nextRow.data('level');
		}
		else
		{
			this._getLevelBounds._minLevel = 1;
		}

		// Can't go any higher than the previous row + 1
		if ($prevRow && $prevRow.length)
		{
			this._getLevelBounds._maxLevel = $prevRow.data('level') + 1;
		}
		else
		{
			this._getLevelBounds._maxLevel = 1;
		}

		// Does this structure have a max level?
		if (this.maxLevels)
		{
			// Make sure it's going to fit at all here
			if (
				this._getLevelBounds._minLevel != 1 &&
				this._getLevelBounds._minLevel + this._draggeeLevelDelta > this.maxLevels
			)
			{
				return false;
			}

			// Limit the max level if we have to
			if (this._getLevelBounds._maxLevel + this._draggeeLevelDelta > this.maxLevels)
			{
				this._getLevelBounds._maxLevel = this.maxLevels - this._draggeeLevelDelta;

				if (this._getLevelBounds._maxLevel < this._getLevelBounds._minLevel)
				{
					this._getLevelBounds._maxLevel = this._getLevelBounds._minLevel;
				}
			}
		}

		return {
			min: this._getLevelBounds._minLevel,
			max: this._getLevelBounds._maxLevel
		};
	},

	/**
	 * Determines the min and max possible levels at the current draggee's position.
	 */
	_setTargetLevelBounds: function()
	{
		this._targetLevelBounds = this._getLevelBounds(
			this.$draggee.first().prev(),
			this.$draggee.last().next()
		);
	},

	/**
	 * Determines the target level based on the current mouse position.
	 */
	_updateIndent: function(forcePositionChange)
	{
		// Figure out the target level
		// ---------------------------------------------------------------------

		// How far has the cursor moved?
		this._updateIndent._mouseDist = this.realMouseX - this.mousedownX;

		// Flip that if this is RTL
		if (Ant.orientation == 'rtl')
		{
			this._updateIndent._mouseDist *= -1;
		}

		// What is that in indentation levels?
		this._updateIndent._indentationDist = Math.round(this._updateIndent._mouseDist / Ant.StructureTableSorter.LEVEL_INDENT);

		// Combine with the original level to get the new target level
		this._updateIndent._targetLevel = this._draggeeLevel + this._updateIndent._indentationDist;

		// Contain it within our min/max levels
		if (this._updateIndent._targetLevel < this._targetLevelBounds.min)
		{
			this._updateIndent._indentationDist += (this._targetLevelBounds.min - this._updateIndent._targetLevel);
			this._updateIndent._targetLevel = this._targetLevelBounds.min;
		}
		else if (this._updateIndent._targetLevel > this._targetLevelBounds.max)
		{
			this._updateIndent._indentationDist -= (this._updateIndent._targetLevel - this._targetLevelBounds.max);
			this._updateIndent._targetLevel = this._targetLevelBounds.max;
		}

		// Has the target level changed?
		if (this._targetLevel !== (this._targetLevel = this._updateIndent._targetLevel))
		{
			// Target level is changing, so update the ancestors
			this._updateAncestorsBeforeRepaint();
		}

		// Update the UI
		// ---------------------------------------------------------------------

		// How far away is the cursor from the exact target level distance?
		this._updateIndent._targetLevelMouseDiff = this._updateIndent._mouseDist - (this._updateIndent._indentationDist * Ant.StructureTableSorter.LEVEL_INDENT);

		// What's the magnet impact of that?
		this._updateIndent._magnetImpact = Math.round(this._updateIndent._targetLevelMouseDiff / 15);

		// Put it on a leash
		if (Math.abs(this._updateIndent._magnetImpact) > Ant.StructureTableSorter.MAX_GIVE)
		{
			this._updateIndent._magnetImpact = (this._updateIndent._magnetImpact > 0 ? 1 : -1) * Ant.StructureTableSorter.MAX_GIVE;
		}

		// Apply the new margin/width
		this._updateIndent._closestLevelMagnetIndent = this._getLevelIndent(this._targetLevel) + this._updateIndent._magnetImpact;
		this.helpers[0].css('margin-'+Ant.left, this._updateIndent._closestLevelMagnetIndent + this._helperMargin);
		this._$titleHelperCell.width(this._titleHelperCellOuterWidth - (this._updateIndent._closestLevelMagnetIndent + Ant.StructureTableSorter.BASE_PADDING));
	},

	/**
	 * Returns the indent size for a given level
	 */
	_getLevelIndent: function(level)
	{
		return (level - 1) * Ant.StructureTableSorter.LEVEL_INDENT;
	},

	/**
	 * Returns the base data that should be sent with StructureController Ajax requests.
	 */
	_getAjaxBaseData: function($row)
	{
		return {
			structureId: this.structureId,
			id:   $row.data('id'),
			//locale:      $row.find('.element:first').data('locale')
		};
	},

	/**
	 * Returns a row's ancestor rows
	 */
	_getAncestors: function($row, targetLevel)
	{
		this._getAncestors._ancestors = [];

		if (targetLevel != 0)
		{
			this._getAncestors._level = targetLevel;
			this._getAncestors._$prevRow = $row.prev();

			while (this._getAncestors._$prevRow.length)
			{
				if (this._getAncestors._$prevRow.data('level') < this._getAncestors._level)
				{
					this._getAncestors._ancestors.unshift(this._getAncestors._$prevRow);
					this._getAncestors._level = this._getAncestors._$prevRow.data('level');

					// Did we just reach the top?
					if (this._getAncestors._level == 0)
					{
						break;
					}
				}

				this._getAncestors._$prevRow = this._getAncestors._$prevRow.prev();
			}
		}

		return this._getAncestors._ancestors;
	},

	/**
	 * Prepares to have the ancestors updated before the screen is repainted.
	 */
	_updateAncestorsBeforeRepaint: function()
	{
		if (this._updateAncestorsFrame)
		{
			Helper.cancelAnimationFrame(this._updateAncestorsFrame);
		}

		if (!this._updateAncestorsProxy)
		{
			this._updateAncestorsProxy = $.proxy(this, '_updateAncestors');
		}

		this._updateAncestorsFrame = Helper.requestAnimationFrame(this._updateAncestorsProxy);
	},

	_updateAncestors: function()
	{
		this._updateAncestorsFrame = null;

		// Update the old ancestors
		// -----------------------------------------------------------------

		for (this._updateAncestors._i = 0; this._updateAncestors._i < this._ancestors.length; this._updateAncestors._i++)
		{
			this._updateAncestors._$ancestor = this._ancestors[this._updateAncestors._i];

			// One less descendant now
			this._updateAncestors._$ancestor.data('descendants', this._updateAncestors._$ancestor.data('descendants') - 1);

			// Is it now childless?
			if (this._updateAncestors._$ancestor.data('descendants') == 0)
			{
				// Remove its toggle
				this._updateAncestors._$ancestor.find('> td > .toggle:first').remove();
			}
		}

		// Update the new ancestors
		// -----------------------------------------------------------------

		this._updateAncestors._newAncestors = this._getAncestors(this.$targetItem, this._targetLevel);

		for (this._updateAncestors._i = 0; this._updateAncestors._i < this._updateAncestors._newAncestors.length; this._updateAncestors._i++)
		{
			this._updateAncestors._$ancestor = this._updateAncestors._newAncestors[this._updateAncestors._i];

			// One more descendant now
			this._updateAncestors._$ancestor.data('descendants', this._updateAncestors._$ancestor.data('descendants') + 1);

			// Is this its first child?
			if (this._updateAncestors._$ancestor.data('descendants') == 1)
			{
				// Create its toggle
				$('<span class="toggle expanded" title="'+Ant.t('Show/hide children')+'"></span>')
					.insertAfter(this._updateAncestors._$ancestor.find('> td .move:first'));

			}
		}

		this._ancestors = this._updateAncestors._newAncestors;

		delete this._updateAncestors._i;
		delete this._updateAncestors._$ancestor;
		delete this._updateAncestors._newAncestors;
	}
},


{
	BASE_PADDING: 10,
	HELPER_MARGIN: -7,
	LEVEL_INDENT: 44,
	MAX_GIVE: 22,

	defaults: {
		onPositionChange: $.noop
	}
});


})(jQuery);
