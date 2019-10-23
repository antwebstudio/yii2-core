Helper = {
	// jQuery objects for common elements
	$win: $(window),
	$doc: $(document),
	$bod: $(document.body)

};

Helper.rtl = Helper.$bod.hasClass('rtl');
Helper.ltr = !Helper.rtl;

Helper = $.extend(Helper, {
	isArray: function(val)
	{
		return (val instanceof Array);
	},
	
	_isMobileBrowser: null,
	_isMobileOrTabletBrowser: null,

	/**
	 * Returns whether this is a mobile browser.
	 * Detection script courtesy of http://detectmobilebrowsers.com
	 *
	 * Last updated: 2014-11-24
	 *
	 * @param bool detectTablets
	 * @return bool
	 */
	isMobileBrowser: function(detectTablets)
	{
		var key = detectTablets ? '_isMobileOrTabletBrowser' : '_isMobileBrowser';

		if (Helper[key] === null)
		{
			var a = navigator.userAgent || navigator.vendor || window.opera;
			Helper[key] = ((new RegExp('(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino'+(detectTablets ? '|android|ipad|playbook|silk' : ''), 'i')).test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)));
		}

		return Helper[key];
	},
	cancelAnimationFrame: (
		function()
		{
			var cancel = (
				window.cancelAnimationFrame ||
				window.mozCancelAnimationFrame ||
				window.webkitCancelAnimationFrame ||
				window.clearTimeout
			);

			return function(id){ return cancel(id); };
		}
	)(),
});


/**
 * Helper base class
 */
Helper.Base = Base.extend({

	settings: null,

	_eventHandlers: null,
	_namespace: null,
	_$listeners: null,
	_disabled: false,

	constructor: function()
	{
		this._eventHandlers = [];
		this._namespace = '.Helper'+Math.floor(Math.random()*1000000000);
		this._listeners = [];
		this.init.apply(this, arguments);
	},

	init: $.noop,

	setSettings: function(settings, defaults)
	{
		var baseSettings = (typeof this.settings == 'undefined' ? {} : this.settings);
		this.settings = $.extend({}, baseSettings, defaults, settings);
	},

	on: function(events, data, handler)
	{
		if (typeof data == 'function')
		{
			handler = data;
			data = {};
		}

		var events = this._normalizeEvents(events);

		for (var i = 0; i < events.length; i++)
		{
			var ev = events[i];

			this._eventHandlers.push({
				type: ev[0],
				namepsace: ev[1],
				data: data,
				handler: handler
			});
		}
	},

	off: function(events)
	{
		var events = this._normalizeEvents(events);

		for (var i = 0; i < events; i++)
		{
			var ev = events[i];

			for (var j = this._eventHandlers.length - 1; j >= 0; i--)
			{
				var handler = this._eventHandlers[j];

				if (handler.type == ev[0] && (!ev[1] || handler.namespace == ev[1]))
				{
					this._eventHandlers.splice(j, 1);
				}
			}
		}
	},

	trigger: function(type, data)
	{
		var ev = {
			type: type,
			target: this
		};

		if (typeof params == 'undefined')
		{
			params = [];
		}

		for (var i = 0; i < this._eventHandlers.length; i++)
		{
			var handler = this._eventHandlers[i];

			if (handler.type == type)
			{
				var _ev = $.extend({ data: handler.data }, data, ev);
				handler.handler(_ev)
			}
		}
	},

	_normalizeEvents: function(events)
	{
		if (typeof events == 'string')
		{
			events = events.split(' ');
		}

		for (var i = 0; i < events.length; i++)
		{
			if (typeof events[i] == 'string')
			{
				events[i] = events[i].split('.');
			}
		}

		return events;
	},

	_splitEvents: function(events)
	{
		if (typeof events == 'string')
		{
			events = events.split(',');

			for (var i = 0; i < events.length; i++)
			{
				events[i] = $.trim(events[i]);
			}
		}

		return events;
	},

	_formatEvents: function(events)
	{
		var events = this._splitEvents(events).slice(0);

		for (var i = 0; i < events.length; i++)
		{
			events[i] += this._namespace;
		}

		return events.join(' ');
	},

	addListener: function(elem, events, data, func)
	{
		var $elem = $(elem);

		// Ignore if there aren't any elements
		if (!$elem.length)
		{
			return;
		}

		events = this._splitEvents(events);

		// Param mapping
		if (typeof func == typeof undefined && typeof data != 'object')
		{
			// (elem, events, func)
			func = data;
			data = {};
		}

		if (typeof func == 'function')
		{
			func = $.proxy(func, this);
		}
		else
		{
			func = $.proxy(this, func);
		}

		$elem.on(this._formatEvents(events), data, $.proxy(function()
		{
			if (!this._disabled)
			{
				func.apply(this, arguments);
			}
		}, this));

		// Remember that we're listening to this element
		if ($.inArray(elem, this._listeners) == -1)
		{
			this._listeners.push(elem);
		}

		// Prep for activate event?
		if ($.inArray('activate', events) != -1 && !$elem.data('Helper-activatable'))
		{
			var activateNamespace = this._namespace+'-activate';

			// Prevent buttons from getting focus on click
			$elem.on('mousedown'+activateNamespace, function(ev)
			{
				ev.preventDefault();
			});

			$elem.on('click'+activateNamespace, function(ev)
			{
				ev.preventDefault();

				var elemIndex = $.inArray(ev.currentTarget, $elem),
					$evElem = $(elem[elemIndex]);

				if (!$evElem.hasClass('disabled'))
				{
					$evElem.trigger('activate');
				}
			});

			$elem.on('keydown'+activateNamespace, function(ev)
			{
				var elemIndex = $.inArray(ev.currentTarget, $elem);
				if (elemIndex != -1 && ev.keyCode == Helper.SPACE_KEY)
				{
					ev.preventDefault();
					var $evElem = $elem.eq(elemIndex);

					if (!$evElem.hasClass('disabled'))
					{
						$evElem.addClass('active');

						Helper.$doc.on('keyup'+activateNamespace, function(ev)
						{
							$elem.removeClass('active');
							if (ev.keyCode == Helper.SPACE_KEY)
							{
								ev.preventDefault();
								$evElem.trigger('activate');
							}
							Helper.$doc.off('keyup'+activateNamespace);
						});
					}
				}
			});

			if (!$elem.hasClass('disabled'))
			{
				$elem.attr('tabindex', '0');
			}
			else
			{
				$elem.removeAttr('tabindex');
			}

			$elem.data('Helper-activatable', true);
		}

		// Prep for chanegtext event?
		if ($.inArray('textchange', events) != -1)
		{
			// Store the initial values
			for (var i = 0; i < $elem.length; i++)
			{
				var _$elem = $elem.eq(i);
				_$elem.data('Helper-textchangeValue', _$elem.val());

				if (!_$elem.data('Helper-textchangeable'))
				{
					var textchangeNamespace = this._namespace+'-textchange',
						events = 'keypress'+textchangeNamespace +
							' keyup'+textchangeNamespace +
							' change'+textchangeNamespace +
							' blur'+textchangeNamespace;

					_$elem.on(events, function(ev)
					{
						var _$elem = $(ev.currentTarget),
							val = _$elem.val();

						if (val != _$elem.data('Helper-textchangeValue'))
						{
							_$elem.data('Helper-textchangeValue', val);
							_$elem.trigger('textchange');
						}
					});

					_$elem.data('Helper-textchangeable', true);
				}
			}
		}

		// Prep for resize event?
		if ($.inArray('resize', events) != -1)
		{
			// Resize detection technique adapted from http://www.backalleycoder.com/2013/03/18/cross-browser-event-based-element-resize-detection/ -- thanks!
			for (var i = 0; i < $elem.length; i++)
			{
				(function(elem)
				{
					// window is the only element that natively supports a resize event
					if (elem == window)
					{
						return;
					}

					// IE < 11 had a proprietary 'resize' event and 'attachEvent' method.
					// Conveniently both dropped in 11.
					if (document.attachEvent)
					{
						return;
					}

					// Is this the first resize listener added to this element?
					if (!elem.__resizeTrigger__)
					{
						// The element must be relative, absolute, or fixed
						if (getComputedStyle(elem).position == 'static')
						{
							elem.style.position = 'relative';
						}

						var obj = elem.__resizeTrigger__ = document.createElement('object');
						obj.className = 'resize-trigger';
						obj.setAttribute('style', 'display: block; position: absolute; top: 0; left: 0; height: 100%; width: 100%; overflow: hidden; pointer-events: none; z-index: -1;');
						obj.__resizeElement__ = $(elem);
						obj.__resizeElement__.data('initialWidth', obj.__resizeElement__.prop('offsetWidth'));
						obj.__resizeElement__.data('initialHeight', obj.__resizeElement__.prop('offsetHeight'));
						obj.onload = objectLoad;
						obj.type = 'text/html';
						obj.__resizeElement__.prepend(obj);
						obj.data = 'about:blank';

						// Listen for window resizes too
						Helper.$win.on('resize', function()
						{
							// Has the object been loaded yet?
							if (obj.contentDocument)
							{
								$(obj.contentDocument.defaultView).trigger('resize');
							}
						});

						// Avoid a top margin on the next element
						$(obj).next().addClass('first');
					}
				})($elem[i]);
			}
		}
	},

	removeListener: function(elem, events)
	{
		$(elem).off(this._formatEvents(events));
	},

	removeAllListeners: function(elem)
	{
		$(elem).off(this._namespace);
	},

	disable: function()
	{
		this._disabled = true;
	},

	enable: function()
	{
		this._disabled = false;
	},

	destroy: function()
	{
		this.removeAllListeners(this._listeners);
	}
});