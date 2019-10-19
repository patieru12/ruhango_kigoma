// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS

/**
 * @license Highcharts JS v4.2.1 (2015-12-21)
 *
 * (c) 2009-2014 Torstein Honsi
 *
 * License: www.highcharts.com/license
 */

(function (root, factory) {
    if (typeof module === 'object' && module.exports) {
        module.exports = root.document ?
            factory(root) : 
            factory;
    } else {
        root.Highcharts = factory(root);
    }
}(typeof window !== 'undefined' ? window : this, function (win) { // eslint-disable-line no-undef
// encapsulated variables
    var UNDEFINED,
        doc = win.document,
        math = Math,
        mathRound = math.round,
        mathFloor = math.floor,
        mathCeil = math.ceil,
        mathMax = math.max,
        mathMin = math.min,
        mathAbs = math.abs,
        mathCos = math.cos,
        mathSin = math.sin,
        mathPI = math.PI,
        deg2rad = mathPI * 2 / 360,


        // some variables
        userAgent = (win.navigator && win.navigator.userAgent) || '',
        isOpera = win.opera,
        isMS = /(msie|trident|edge)/i.test(userAgent) && !isOpera,
        docMode8 = doc && doc.documentMode === 8,
        isWebKit = !isMS && /AppleWebKit/.test(userAgent),
        isFirefox = /Firefox/.test(userAgent),
        isTouchDevice = /(Mobile|Android|Windows Phone)/.test(userAgent),
        SVG_NS = 'http://www.w3.org/2000/svg',
        hasSVG = doc && doc.createElementNS && !!doc.createElementNS(SVG_NS, 'svg').createSVGRect,
        hasBidiBug = isFirefox && parseInt(userAgent.split('Firefox/')[1], 10) < 4, // issue #38
        useCanVG = doc && !hasSVG && !isMS && !!doc.createElement('canvas').getContext,
        Renderer,
        hasTouch,
        symbolSizes = {},
        idCounter = 0,
        garbageBin,
        defaultOptions,
        dateFormat, // function
        pathAnim,
        timeUnits,
        noop = function () {},
        charts = [],
        chartCount = 0,
        PRODUCT = 'Highcharts',
        VERSION = '4.2.1',

        // some constants for frequently used strings
        DIV = 'div',
        ABSOLUTE = 'absolute',
        RELATIVE = 'relative',
        HIDDEN = 'hidden',
        PREFIX = 'highcharts-',
        VISIBLE = 'visible',
        PX = 'px',
        NONE = 'none',
        M = 'M',
        L = 'L',
        numRegex = /^[0-9]+$/,
        NORMAL_STATE = '',
        HOVER_STATE = 'hover',
        SELECT_STATE = 'select',
        marginNames = ['plotTop', 'marginRight', 'marginBottom', 'plotLeft'],

        // Object for extending Axis
        AxisPlotLineOrBandExtension,

        // constants for attributes
        STROKE_WIDTH = 'stroke-width',

        // time methods, changed based on whether or not UTC is used
        Date,  // Allow using a different Date class
        makeTime,
        timezoneOffset,
        getTimezoneOffset,
        getMinutes,
        getHours,
        getDay,
        getDate,
        getMonth,
        getFullYear,
        setMilliseconds,
        setSeconds,
        setMinutes,
        setHours,
        setDate,
        setMonth,
        setFullYear,


        // lookup over the types and the associated classes
        seriesTypes = {},
        Highcharts;

    /**
     * Provide error messages for debugging, with links to online explanation
     */
    function error(code, stop) {
        var msg = 'Highcharts error #' + code + ': www.highcharts.com/errors/' + code;
        if (stop) {
            throw new Error(msg);
        }
        // else ...
        if (win.console) {
            console.log(msg); // eslint-disable-line no-console
        }
    }

    // The Highcharts namespace
    Highcharts = win.Highcharts ? error(16, true) : { win: win };

    Highcharts.seriesTypes = seriesTypes;
    var timers = [],
        getStyle,

        // Previous adapter functions
        inArray,
        each,
        grep,
        offset,
        map,
        addEvent,
        removeEvent,
        fireEvent,
        animate,
        stop;

    /**
     * An animator object. One instance applies to one property (attribute or style prop) 
     * on one element.
     * 
     * @param {object} elem    The element to animate. May be a DOM element or a Highcharts SVGElement wrapper.
     * @param {object} options Animation options, including duration, easing, step and complete.
     * @param {object} prop    The property to animate.
     */
    function Fx(elem, options, prop) {
        this.options = options;
        this.elem = elem;
        this.prop = prop;
    }
    Fx.prototype = {
    
        /**
         * Animating a path definition on SVGElement
         * @returns {undefined} 
         */
        dSetter: function () {
            var start = this.paths[0],
                end = this.paths[1],
                ret = [],
                now = this.now,
                i = start.length,
                startVal;

            if (now === 1) { // land on the final path without adjustment points appended in the ends
                ret = this.toD;

            } else if (i === end.length && now < 1) {
                while (i--) {
                    startVal = parseFloat(start[i]);
                    ret[i] =
                        isNaN(startVal) ? // a letter instruction like M or L
                                start[i] :
                                now * (parseFloat(end[i] - startVal)) + startVal;

                }
            } else { // if animation is finished or length not matching, land on right value
                ret = end;
            }
            this.elem.attr('d', ret);
        },

        /**
         * Update the element with the current animation step
         * @returns {undefined}
         */
        update: function () {
            var elem = this.elem,
                prop = this.prop, // if destroyed, it is null
                now = this.now,
                step = this.options.step;

            // Animation setter defined from outside
            if (this[prop + 'Setter']) {
                this[prop + 'Setter']();

            // Other animations on SVGElement
            } else if (elem.attr) {
                if (elem.element) {
                    elem.attr(prop, now);
                }

            // HTML styles, raw HTML content like container size
            } else {
                elem.style[prop] = now + this.unit;
            }
        
            if (step) {
                step.call(elem, now, this);
            }

        },

        /**
         * Run an animation
         */
        run: function (from, to, unit) {
            var self = this,
                timer = function (gotoEnd) {
                    return timer.stopped ? false : self.step(gotoEnd);
                },
                i;

            this.startTime = +new Date();
            this.start = from;
            this.end = to;
            this.unit = unit;
            this.now = this.start;
            this.pos = 0;

            timer.elem = this.elem;

            if (timer() && timers.push(timer) === 1) {
                timer.timerId = setInterval(function () {
                
                    for (i = 0; i < timers.length; i++) {
                        if (!timers[i]()) {
                            timers.splice(i--, 1);
                        }
                    }

                    if (!timers.length) {
                        clearInterval(timer.timerId);
                    }
                }, 13);
            }
        },
    
        /**
         * Run a single step in the animation
         * @param   {Boolean} gotoEnd Whether to go to then endpoint of the animation after abort
         * @returns {Boolean} True if animation continues
         */
        step: function (gotoEnd) {
            var t = +new Date(),
                ret,
                done,
                options = this.options,
                elem = this.elem,
                complete = options.complete,
                duration = options.duration,
                curAnim = options.curAnim,
                i;
        
            if (elem.attr && !elem.element) { // #2616, element including flag is destroyed
                ret = false;

            } else if (gotoEnd || t >= duration + this.startTime) {
                this.now = this.end;
                this.pos = 1;
                this.update();

                curAnim[this.prop] = true;

                done = true;
                for (i in curAnim) {
                    if (curAnim[i] !== true) {
                        done = false;
                    }
                }

                if (done && complete) {
                    complete.call(elem);
                }
                ret = false;

            } else {
                this.pos = options.easing((t - this.startTime) / duration);
                this.now = this.start + ((this.end - this.start) * this.pos);
                this.update();
                ret = true;
            }
            return ret;
        },

        /**
         * Prepare start and end values so that the path can be animated one to one
         */
        initPath: function (elem, fromD, toD) {
            fromD = fromD || '';
            var shift = elem.shift,
                bezier = fromD.indexOf('C') > -1,
                numParams = bezier ? 7 : 3,
                endLength,
                slice,
                i,
                start = fromD.split(' '),
                end = [].concat(toD), // copy
                startBaseLine,
                endBaseLine,
                sixify = function (arr) { // in splines make move points have six parameters like bezier curves
                    i = arr.length;
                    while (i--) {
                        if (arr[i] === M) {
                            arr.splice(i + 1, 0, arr[i + 1], arr[i + 2], arr[i + 1], arr[i + 2]);
                        }
                    }
                };

            if (bezier) {
                sixify(start);
                sixify(end);
            }

            // pull out the base lines before padding
            if (elem.isArea) {
                startBaseLine = start.splice(start.length - 6, 6);
                endBaseLine = end.splice(end.length - 6, 6);
            }

            // if shifting points, prepend a dummy point to the end path
            if (shift <= end.length / numParams && start.length === end.length) {
                while (shift--) {
                    end = [].concat(end).splice(0, numParams).concat(end);
                }
            }
            elem.shift = 0; // reset for following animations

            // copy and append last point until the length matches the end length
            if (start.length) {
                endLength = end.length;
                while (start.length < endLength) {

                    //bezier && sixify(start);
                    slice = [].concat(start).splice(start.length - numParams, numParams);
                    if (bezier) { // disable first control point
                        slice[numParams - 6] = slice[numParams - 2];
                        slice[numParams - 5] = slice[numParams - 1];
                    }
                    start = start.concat(slice);
                }
            }

            if (startBaseLine) { // append the base lines for areas
                start = start.concat(startBaseLine);
                end = end.concat(endBaseLine);
            }
            return [start, end];
        }
    }; // End of Fx prototype


    /**
     * Extend an object with the members of another
     * @param {Object} a The object to be extended
     * @param {Object} b The object to add to the first one
     */
    var extend = Highcharts.extend = function (a, b) {
        var n;
        if (!a) {
            a = {};
        }
        for (n in b) {
            a[n] = b[n];
        }
        return a;
    };

    /**
     * Deep merge two or more objects and return a third object. If the first argument is
     * true, the contents of the second object is copied into the first object.
     * Previously this function redirected to jQuery.extend(true), but this had two limitations.
     * First, it deep merged arrays, which lead to workarounds in Highcharts. Second,
     * it copied properties from extended prototypes.
     */
    function merge() {
        var i,
            args = arguments,
            len,
            ret = {},
            doCopy = function (copy, original) {
                var value, key;

                // An object is replacing a primitive
                if (typeof copy !== 'object') {
                    copy = {};
                }

                for (key in original) {
                    if (original.hasOwnProperty(key)) {
                        value = original[key];

                        // Copy the contents of objects, but not arrays or DOM nodes
                        if (value && typeof value === 'object' && Object.prototype.toString.call(value) !== '[object Array]' &&
                                key !== 'renderTo' && typeof value.nodeType !== 'number') {
                            copy[key] = doCopy(copy[key] || {}, value);

                        // Primitives and arrays are copied over directly
                        } else {
                            copy[key] = original[key];
                        }
                    }
                }
                return copy;
            };

        // If first argument is true, copy into the existing object. Used in setOptions.
        if (args[0] === true) {
            ret = args[1];
            args = Array.prototype.slice.call(args, 2);
        }

        // For each argument, extend the return
        len = args.length;
        for (i = 0; i < len; i++) {
            ret = doCopy(ret, args[i]);
        }

        return ret;
    }

    /**
     * Shortcut for parseInt
     * @param {Object} s
     * @param {Number} mag Magnitude
     */
    function pInt(s, mag) {
        return parseInt(s, mag || 10);
    }

    /**
     * Check for string
     * @param {Object} s
     */
    function isString(s) {
        return typeof s === 'string';
    }

    /**
     * Check for object
     * @param {Object} obj
     */
    function isObject(obj) {
        return obj && typeof obj === 'object';
    }

    /**
     * Check for array
     * @param {Object} obj
     */
    function isArray(obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
    }

    /**
     * Check for number
     * @param {Object} n
     */
    function isNumber(n) {
        return typeof n === 'number';
    }

    function log2lin(num) {
        return math.log(num) / math.LN10;
    }
    function lin2log(num) {
        return math.pow(10, num);
    }

    /**
     * Remove last occurence of an item from an array
     * @param {Array} arr
     * @param {Mixed} item
     */
    function erase(arr, item) {
        var i = arr.length;
        while (i--) {
            if (arr[i] === item) {
                arr.splice(i, 1);
                break;
            }
        }
        //return arr;
    }

    /**
     * Returns true if the object is not null or undefined.
     * @param {Object} obj
     */
    function defined(obj) {
        return obj !== UNDEFINED && obj !== null;
    }

    /**
     * Set or get an attribute or an object of attributes. Can't use jQuery attr because
     * it attempts to set expando properties on the SVG element, which is not allowed.
     *
     * @param {Object} elem The DOM element to receive the attribute(s)
     * @param {String|Object} prop The property or an abject of key-value pairs
     * @param {String} value The value if a single property is set
     */
    function attr(elem, prop, value) {
        var key,
            ret;

        // if the prop is a string
        if (isString(prop)) {
            // set the value
            if (defined(value)) {
                elem.setAttribute(prop, value);

            // get the value
            } else if (elem && elem.getAttribute) { // elem not defined when printing pie demo...
                ret = elem.getAttribute(prop);
            }

        // else if prop is defined, it is a hash of key/value pairs
        } else if (defined(prop) && isObject(prop)) {
            for (key in prop) {
                elem.setAttribute(key, prop[key]);
            }
        }
        return ret;
    }
    /**
     * Check if an element is an array, and if not, make it into an array.
     */
    function splat(obj) {
        return isArray(obj) ? obj : [obj];
    }

    /**
     * Set a timeout if the delay is given, otherwise perform the function synchronously
     * @param   {Function} fn      The function to perform
     * @param   {Number}   delay   Delay in milliseconds
     * @param   {Ojbect}   context The context
     * @returns {Nubmer}           An identifier for the timeout
     */
    function syncTimeout(fn, delay, context) {
        if (delay) {
            return setTimeout(fn, delay, context);
        }
        fn.call(0, context);
    }


    /**
     * Return the first value that is defined.
     */
    var pick = Highcharts.pick = function () {
        var args = arguments,
            i,
            arg,
            length = args.length;
        for (i = 0; i < length; i++) {
            arg = args[i];
            if (arg !== UNDEFINED && arg !== null) {
                return arg;
            }
        }
    };

    /**
     * Set CSS on a given element
     * @param {Object} el
     * @param {Object} styles Style object with camel case property names
     */
    function css(el, styles) {
        if (isMS && !hasSVG) { // #2686
            if (styles && styles.opacity !== UNDEFINED) {
                styles.filter = 'alpha(opacity=' + (styles.opacity * 100) + ')';
            }
        }
        extend(el.style, styles);
    }

    /**
     * Utility function to create element with attributes and styles
     * @param {Object} tag
     * @param {Object} attribs
     * @param {Object} styles
     * @param {Object} parent
     * @param {Object} nopad
     */
    function createElement(tag, attribs, styles, parent, nopad) {
        var el = doc.createElement(tag);
        if (attribs) {
            extend(el, attribs);
        }
        if (nopad) {
            css(el, { padding: 0, border: 'none', margin: 0 });
        }
        if (styles) {
            css(el, styles);
        }
        if (parent) {
            parent.appendChild(el);
        }
        return el;
    }

    /**
     * Extend a prototyped class by new members
     * @param {Object} parent
     * @param {Object} members
     */
    function extendClass(Parent, members) {
        var object = function () {
        };
        object.prototype = new Parent();
        extend(object.prototype, members);
        return object;
    }

    /**
     * Pad a string to a given length by adding 0 to the beginning
     * @param {Number} number
     * @param {Number} length
     */
    function pad(number, length) {
        return new Array((length || 2) + 1 - String(number).length).join(0) + number;
    }

    /**
     * Return a length based on either the integer value, or a percentage of a base.
     */
    function relativeLength(value, base) {
        return (/%$/).test(value) ? base * parseFloat(value) / 100 : parseFloat(value);
    }

    /**
     * Wrap a method with extended functionality, preserving the original function
     * @param {Object} obj The context object that the method belongs to
     * @param {String} method The name of the method to extend
     * @param {Function} func A wrapper function callback. This function is called with the same arguments
     * as the original function, except that the original function is unshifted and passed as the first
     * argument.
     */
    var wrap = Highcharts.wrap = function (obj, method, func) {
        var proceed = obj[method];
        obj[method] = function () {
            var args = Array.prototype.slice.call(arguments);
            args.unshift(proceed);
            return func.apply(this, args);
        };
    };


    function getTZOffset(timestamp) {
        return ((getTimezoneOffset && getTimezoneOffset(timestamp)) || timezoneOffset || 0) * 60000;
    }

    /**
     * Based on http://www.php.net/manual/en/function.strftime.php
     * @param {String} format
     * @param {Number} timestamp
     * @param {Boolean} capitalize
     */
    dateFormat = function (format, timestamp, capitalize) {
        if (!defined(timestamp) || isNaN(timestamp)) {
            return defaultOptions.lang.invalidDate || '';
        }
        format = pick(format, '%Y-%m-%d %H:%M:%S');

        var date = new Date(timestamp - getTZOffset(timestamp)),
            key, // used in for constuct below
            // get the basic time values
            hours = date[getHours](),
            day = date[getDay](),
            dayOfMonth = date[getDate](),
            month = date[getMonth](),
            fullYear = date[getFullYear](),
            lang = defaultOptions.lang,
            langWeekdays = lang.weekdays,

            // List all format keys. Custom formats can be added from the outside.
            replacements = extend({

                // Day
                'a': langWeekdays[day].substr(0, 3), // Short weekday, like 'Mon'
                'A': langWeekdays[day], // Long weekday, like 'Monday'
                'd': pad(dayOfMonth), // Two digit day of the month, 01 to 31
                'e': dayOfMonth, // Day of the month, 1 through 31
                'w': day,

                // Week (none implemented)
                //'W': weekNumber(),

                // Month
                'b': lang.shortMonths[month], // Short month, like 'Jan'
                'B': lang.months[month], // Long month, like 'January'
                'm': pad(month + 1), // Two digit month number, 01 through 12

                // Year
                'y': fullYear.toString().substr(2, 2), // Two digits year, like 09 for 2009
                'Y': fullYear, // Four digits year, like 2009

                // Time
                'H': pad(hours), // Two digits hours in 24h format, 00 through 23
                'k': hours, // Hours in 24h format, 0 through 23
                'I': pad((hours % 12) || 12), // Two digits hours in 12h format, 00 through 11
                'l': (hours % 12) || 12, // Hours in 12h format, 1 through 12
                'M': pad(date[getMinutes]()), // Two digits minutes, 00 through 59
                'p': hours < 12 ? 'AM' : 'PM', // Upper case AM or PM
                'P': hours < 12 ? 'am' : 'pm', // Lower case AM or PM
                'S': pad(date.getSeconds()), // Two digits seconds, 00 through  59
                'L': pad(mathRound(timestamp % 1000), 3) // Milliseconds (naming from Ruby)
            }, Highcharts.dateFormats);


        // do the replaces
        for (key in replacements) {
            while (format.indexOf('%' + key) !== -1) { // regex would do it in one line, but this is faster
                format = format.replace('%' + key, typeof replacements[key] === 'function' ? replacements[key](timestamp) : replacements[key]);
            }
        }

        // Optionally capitalize the string and return
        return capitalize ? format.substr(0, 1).toUpperCase() + format.substr(1) : format;
    };

    /**
     * Format a single variable. Similar to sprintf, without the % prefix.
     */
    function formatSingle(format, val) {
        var floatRegex = /f$/,
            decRegex = /\.([0-9])/,
            lang = defaultOptions.lang,
            decimals;

        if (floatRegex.test(format)) { // float
            decimals = format.match(decRegex);
            decimals = decimals ? decimals[1] : -1;
            if (val !== null) {
                val = Highcharts.numberFormat(
                    val,
                    decimals,
                    lang.decimalPoint,
                    format.indexOf(',') > -1 ? lang.thousandsSep : ''
                );
            }
        } else {
            val = dateFormat(format, val);
        }
        return val;
    }

    /**
     * Format a string according to a subset of the rules of Python's String.format method.
     */
    function format(str, ctx) {
        var splitter = '{',
            isInside = false,
            segment,
            valueAndFormat,
            path,
            i,
            len,
            ret = [],
            val,
            index;

        while ((index = str.indexOf(splitter)) !== -1) {

            segment = str.slice(0, index);
            if (isInside) { // we're on the closing bracket looking back

                valueAndFormat = segment.split(':');
                path = valueAndFormat.shift().split('.'); // get first and leave format
                len = path.length;
                val = ctx;

                // Assign deeper paths
                for (i = 0; i < len; i++) {
                    val = val[path[i]];
                }

                // Format the replacement
                if (valueAndFormat.length) {
                    val = formatSingle(valueAndFormat.join(':'), val);
                }

                // Push the result and advance the cursor
                ret.push(val);

            } else {
                ret.push(segment);

            }
            str = str.slice(index + 1); // the rest
            isInside = !isInside; // toggle
            splitter = isInside ? '}' : '{'; // now look for next matching bracket
        }
        ret.push(str);
        return ret.join('');
    }

    /**
     * Get the magnitude of a number
     */
    function getMagnitude(num) {
        return math.pow(10, mathFloor(math.log(num) / math.LN10));
    }

    /**
     * Take an interval and normalize it to multiples of 1, 2, 2.5 and 5
     * @param {Number} interval
     * @param {Array} multiples
     * @param {Number} magnitude
     * @param {Object} options
     */
    function normalizeTickInterval(interval, multiples, magnitude, allowDecimals, preventExceed) {
        var normalized,
            i,
            retInterval = interval;

        // round to a tenfold of 1, 2, 2.5 or 5
        magnitude = pick(magnitude, 1);
        normalized = interval / magnitude;

        // multiples for a linear scale
        if (!multiples) {
            multiples = [1, 2, 2.5, 5, 10];

            // the allowDecimals option
            if (allowDecimals === false) {
                if (magnitude === 1) {
                    multiples = [1, 2, 5, 10];
                } else if (magnitude <= 0.1) {
                    multiples = [1 / magnitude];
                }
            }
        }

        // normalize the interval to the nearest multiple
        for (i = 0; i < multiples.length; i++) {
            retInterval = multiples[i];
            if ((preventExceed && retInterval * magnitude >= interval) || // only allow tick amounts smaller than natural
                    (!preventExceed && (normalized <= (multiples[i] + (multiples[i + 1] || multiples[i])) / 2))) {
                break;
            }
        }

        // multiply back to the correct magnitude
        retInterval *= magnitude;

        return retInterval;
    }


    /**
     * Utility method that sorts an object array and keeping the order of equal items.
     * ECMA script standard does not specify the behaviour when items are equal.
     */
    function stableSort(arr, sortFunction) {
        var length = arr.length,
            sortValue,
            i;

        // Add index to each item
        for (i = 0; i < length; i++) {
            arr[i].safeI = i; // stable sort index
        }

        arr.sort(function (a, b) {
            sortValue = sortFunction(a, b);
            return sortValue === 0 ? a.safeI - b.safeI : sortValue;
        });

        // Remove index from items
        for (i = 0; i < length; i++) {
            delete arr[i].safeI; // stable sort index
        }
    }

    /**
     * Non-recursive method to find the lowest member of an array. Math.min raises a maximum
     * call stack size exceeded error in Chrome when trying to apply more than 150.000 points. This
     * method is slightly slower, but safe.
     */
    function arrayMin(data) {
        var i = data.length,
            min = data[0];

        while (i--) {
            if (data[i] < min) {
                min = data[i];
            }
        }
        return min;
    }

    /**
     * Non-recursive method to find the lowest member of an array. Math.min raises a maximum
     * call stack size exceeded error in Chrome when trying to apply more than 150.000 points. This
     * method is slightly slower, but safe.
     */
    function arrayMax(data) {
        var i = data.length,
            max = data[0];

        while (i--) {
            if (data[i] > max) {
                max = data[i];
            }
        }
        return max;
    }

    /**
     * Utility method that destroys any SVGElement or VMLElement that are properties on the given object.
     * It loops all properties and invokes destroy if there is a destroy method. The property is
     * then delete'ed.
     * @param {Object} The object to destroy properties on
     * @param {Object} Exception, do not destroy this property, only delete it.
     */
    function destroyObjectProperties(obj, except) {
        var n;
        for (n in obj) {
            // If the object is non-null and destroy is defined
            if (obj[n] && obj[n] !== except && obj[n].destroy) {
                // Invoke the destroy
                obj[n].destroy();
            }

            // Delete the property from the object.
            delete obj[n];
        }
    }


    /**
     * Discard an element by moving it to the bin and delete
     * @param {Object} The HTML node to discard
     */
    function discardElement(element) {
        // create a garbage bin element, not part of the DOM
        if (!garbageBin) {
            garbageBin = createElement(DIV);
        }

        // move the node and empty bin
        if (element) {
            garbageBin.appendChild(element);
        }
        garbageBin.innerHTML = '';
    }

    /**
     * Fix JS round off float errors
     * @param {Number} num
     */
    function correctFloat(num, prec) {
        return parseFloat(
            num.toPrecision(prec || 14)
        );
    }

    /**
     * Set the global animation to either a given value, or fall back to the
     * given chart's animation option
     * @param {Object} animation
     * @param {Object} chart
     */
    function setAnimation(animation, chart) {
        chart.renderer.globalAnimation = pick(animation, chart.animation);
    }

    /**
     * The time unit lookup
     */
    timeUnits = {
        millisecond: 1,
        second: 1000,
        minute: 60000,
        hour: 3600000,
        day: 24 * 3600000,
        week: 7 * 24 * 3600000,
        month: 28 * 24 * 3600000,
        year: 364 * 24 * 3600000
    };


    /**
     * Format a number and return a string based on input settings
     * @param {Number} number The input number to format
     * @param {Number} decimals The amount of decimals
     * @param {String} decPoint The decimal point, defaults to the one given in the lang options
     * @param {String} thousandsSep The thousands separator, defaults to the one given in the lang options
     */
    Highcharts.numberFormat = function (number, decimals, decPoint, thousandsSep) {
        var lang = defaultOptions.lang,
            // http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_number_format/
            n = +number || 0,
            c = decimals === -1 ?
                    Math.min((n.toString().split('.')[1] || '').length, 20) : // Preserve decimals. Not huge numbers (#3793).
                    (isNaN(decimals = Math.abs(decimals)) ? 2 : decimals),
            d = decPoint === undefined ? lang.decimalPoint : decPoint,
            t = thousandsSep === undefined ? lang.thousandsSep : thousandsSep,
            s = n < 0 ? '-' : '',
            i = String(pInt(n = mathAbs(n).toFixed(c))),
            j = i.length > 3 ? i.length % 3 : 0;

        return (s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) +
                (c ? d + mathAbs(n - i).toFixed(c).slice(2) : ''));
    };

    /**
     * Easing definition
     * @param   {Number} pos Current position, ranging from 0 to 1
     */
    Math.easeInOutSine = function (pos) {
        return -0.5 * (Math.cos(Math.PI * pos) - 1);
    };

    /**
     * Internal method to return CSS value for given element and property
     */
    getStyle = function (el, prop) {
        var style = win.getComputedStyle(el, undefined);
        return style && pInt(style.getPropertyValue(prop));
    };

    /**
     * Return the index of an item in an array, or -1 if not found
     */
    inArray = function (item, arr) {
        return arr.indexOf ? arr.indexOf(item) : [].indexOf.call(arr, item);
    };

    /**
     * Filter an array
     */
    grep = function (elements, callback) {
        return [].filter.call(elements, callback);
    };

    /**
     * Map an array
     */
    map = function (arr, fn) {
        var results = [], i = 0, len = arr.length;

        for (; i < len; i++) {
            results[i] = fn.call(arr[i], arr[i], i, arr);
        }

        return results;
    };

    /**
     * Get the element's offset position, corrected by overflow:auto.
     */
    offset = function (el) {
        var docElem = doc.documentElement,
            box = el.getBoundingClientRect();

        return {
            top: box.top  + (win.pageYOffset || docElem.scrollTop)  - (docElem.clientTop  || 0),
            left: box.left + (win.pageXOffset || docElem.scrollLeft) - (docElem.clientLeft || 0)
        };
    };

    /**
     * Stop running animation.
     * A possible extension to this would be to stop a single property, when
     * we want to continue animating others. Then assign the prop to the timer
     * in the Fx.run method, and check for the prop here. This would be an improvement
     * in all cases where we stop the animation from .attr. Instead of stopping
     * everything, we can just stop the actual attributes we're setting.
     */
    stop = function (el) {

        var i = timers.length;

        // Remove timers related to this element (#4519)
        while (i--) {
            if (timers[i].elem === el) {
                timers[i].stopped = true; // #4667
            }
        }
    };

    /**
     * Utility for iterating over an array.
     * @param {Array} arr
     * @param {Function} fn
     */
    each = function (arr, fn) { // modern browsers
        return Array.prototype.forEach.call(arr, fn);
    };

    /**
     * Add an event listener
     */
    addEvent = function (el, type, fn) {
    
        var events = el.hcEvents = el.hcEvents || {};

        function wrappedFn(e) {
            e.target = e.srcElement || win; // #2820
            fn.call(el, e);
        }

        // Handle DOM events in modern browsers
        if (el.addEventListener) {
            el.addEventListener(type, fn, false);

        // Handle old IE implementation
        } else if (el.attachEvent) {

            if (!el.hcEventsIE) {
                el.hcEventsIE = {};
            }

            // Link wrapped fn with original fn, so we can get this in removeEvent
            el.hcEventsIE[fn.toString()] = wrappedFn;

            el.attachEvent('on' + type, wrappedFn);
        }

        if (!events[type]) {
            events[type] = [];
        }

        events[type].push(fn);
    };

    /**
     * Remove event added with addEvent
     */
    removeEvent = function (el, type, fn) {
    
        var events,
            hcEvents = el.hcEvents,
            index;

        function removeOneEvent(type, fn) {
            if (el.removeEventListener) {
                el.removeEventListener(type, fn, false);
            } else if (el.attachEvent) {
                fn = el.hcEventsIE[fn.toString()];
                el.detachEvent('on' + type, fn);
            }
        }

        function removeAllEvents() {
            var types,
                len,
                n;

            if (!el.nodeName) {
                return; // break on non-DOM events
            }

            if (type) {
                types = {};
                types[type] = true;
            } else {
                types = hcEvents;
            }

            for (n in types) {
                if (hcEvents[n]) {
                    len = hcEvents[n].length;
                    while (len--) {
                        removeOneEvent(n, hcEvents[n][len]);
                    }
                }
            }
        }

        if (hcEvents) {
            if (type) {
                events = hcEvents[type] || [];
                if (fn) {
                    index = inArray(fn, events);
                    if (index > -1) {
                        events.splice(index, 1);
                        hcEvents[type] = events;
                    }
                    removeOneEvent(type, fn);

                } else {
                    removeAllEvents();
                    hcEvents[type] = [];
                }
            } else {
                removeAllEvents();
                el.hcEvents = {};
            }
        }
    };

    /**
     * Fire an event on a custom object
     */
    fireEvent = function (el, type, eventArguments, defaultFunction) {
        var e,
            hcEvents = el.hcEvents,
            events,
            len,
            i,
            preventDefault,
            fn;

        eventArguments = eventArguments || {};

        if (doc.createEvent && (el.dispatchEvent || el.fireEvent)) {
            e = doc.createEvent('Events');
            e.initEvent(type, true, true);
            e.target = el;

            extend(e, eventArguments);

            if (el.dispatchEvent) {
                el.dispatchEvent(e);
            } else {
                el.fireEvent(type, e);
            }

        } else if (hcEvents) {
        
            events = hcEvents[type] || [];
            len = events.length;

            // Attach a simple preventDefault function to skip default handler if called
            preventDefault = function () {
                eventArguments.defaultPrevented = true;
            };
        
            for (i = 0; i < len; i++) {
                fn = events[i];

                // eventArguments is never null here
                if (eventArguments.stopped) {
                    return;
                }

                eventArguments.preventDefault = preventDefault;
                eventArguments.target = el;

                // If the type is not set, we're running a custom event (#2297). If it is set,
                // we're running a browser event, and setting it will cause en error in
                // IE8 (#2465).
                if (!eventArguments.type) {
                    eventArguments.type = type;
                }
            
                // If the event handler return false, prevent the default handler from executing
                if (fn.call(el, eventArguments) === false) {
                    eventArguments.preventDefault();
                }
            }
        }

        // Run the default if not prevented
        if (defaultFunction && !eventArguments.defaultPrevented) {
            defaultFunction(eventArguments);
        }
    };

    /**
     * The global animate method, which uses Fx to create individual animators.
     */
    animate = function (el, params, opt) {
        var start,
            unit = '',
            end,
            fx,
            args,
            prop;

        if (!isObject(opt)) { // Number or undefined/null
            args = arguments;
            opt = {
                duration: args[2],
                easing: args[3],
                complete: args[4]
            };
        }
        if (!isNumber(opt.duration)) {
            opt.duration = 400;
        }
        opt.easing = Math[opt.easing] || Math.easeInOutSine;
        opt.curAnim = merge(params);

        for (prop in params) {
            fx = new Fx(el, opt, prop);
            end = null;

            if (prop === 'd') {
                fx.paths = fx.initPath(
                    el,
                    el.d,
                    params.d
                );
                fx.toD = params.d;
                start = 0;
                end = 1;
            } else if (el.attr) {
                start = el.attr(prop);
            } else {
                start = parseFloat(getStyle(el, prop)) || 0;
                if (prop !== 'opacity') {
                    unit = 'px';
                }
            }

            if (!end) {
                end = params[prop];
            }
            if (end.match && end.match('px')) {
                end = end.replace(/px/g, ''); // #4351
            }
            fx.run(start, end, unit);
        }
    };

    /**
     * Register Highcharts as a plugin in jQuery
     */
    if (win.jQuery) {
        win.jQuery.fn.highcharts = function () {
            var args = [].slice.call(arguments);

            if (this[0]) { // this[0] is the renderTo div

                // Create the chart
                if (args[0]) {
                    new Highcharts[ // eslint-disable-line no-new
                        isString(args[0]) ? args.shift() : 'Chart' // Constructor defaults to Chart
                    ](this[0], args[0], args[1]);
                    return this;
                }

                // When called without parameters or with the return argument, return an existing chart
                return charts[attr(this[0], 'data-highcharts-chart')];
            }
        };
    }


    /**
     * Compatibility section to add support for legacy IE. This can be removed if old IE 
     * support is not needed.
     */
    if (doc && !doc.defaultView) {
        getStyle = function (el, prop) {
            var val,
                alias = { width: 'clientWidth', height: 'clientHeight' }[prop];
            
            if (el.style[prop]) {
                return pInt(el.style[prop]);
            }
            if (prop === 'opacity') {
                prop = 'filter';
            }

            // Getting the rendered width and height
            if (alias) {
                el.style.zoom = 1;
                return el[alias] - 2 * getStyle(el, 'padding');
            }
        
            val = el.currentStyle[prop.replace(/\-(\w)/g, function (a, b) {
                return b.toUpperCase();
            })];
            if (prop === 'filter') {
                val = val.replace(
                    /alpha\(opacity=([0-9]+)\)/, 
                    function (a, b) { 
                        return b / 100; 
                    }
                );
            }
        
            return val === '' ? 1 : pInt(val);
        };
    }

    if (!Array.prototype.forEach) {
        each = function (arr, fn) { // legacy
            var i = 0, 
                len = arr.length;
            for (; i < len; i++) {
                if (fn.call(arr[i], arr[i], i, arr) === false) {
                    return i;
                }
            }
        };
    }

    if (!Array.prototype.indexOf) {
        inArray = function (item, arr) {
            var len, 
                i = 0;

            if (arr) {
                len = arr.length;
            
                for (; i < len; i++) {
                    if (arr[i] === item) {
                        return i;
                    }
                }
            }

            return -1;
        };
    }

    if (!Array.prototype.filter) {
        grep = function (elements, fn) {
            var ret = [],
                i = 0,
                length = elements.length;

            for (; i < length; i++) {
                if (fn(elements[i], i)) {
                    ret.push(elements[i]);
                }
            }

            return ret;
        };
    }

    //--- End compatibility section ---

    // Expose utilities
    Highcharts.Fx = Fx;
    Highcharts.inArray = inArray;
    Highcharts.each = each;
    Highcharts.grep = grep;
    Highcharts.offset = offset;
    Highcharts.map = map;
    Highcharts.addEvent = addEvent;
    Highcharts.removeEvent = removeEvent;
    Highcharts.fireEvent = fireEvent;
    Highcharts.animate = animate;
    Highcharts.stop = stop;

    /* ****************************************************************************
     * Handle the options                                                         *
     *****************************************************************************/
    defaultOptions = {
        colors: ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c',
                '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'],
        symbols: ['circle', 'diamond', 'square', 'triangle', 'triangle-down'],
        lang: {
            loading: 'Loading...',
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July',
                    'August', 'September', 'October', 'November', 'December'],
            shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            weekdays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            // invalidDate: '',
            decimalPoint: '.',
            numericSymbols: ['k', 'M', 'G', 'T', 'P', 'E'], // SI prefixes used in axis labels
            resetZoom: 'Reset zoom',
            resetZoomTitle: 'Reset zoom level 1:1',
            thousandsSep: ' '
        },
        global: {
            useUTC: true,
            //timezoneOffset: 0,
            canvasToolsURL: 'http://code.highcharts.com/modules/canvas-tools.js',
            VMLRadialGradientURL: 'http://code.highcharts.com/4.2.1/gfx/vml-radial-gradient.png'
        },
        chart: {
            //animation: true,
            //alignTicks: false,
            //reflow: true,
            //className: null,
            //events: { load, selection },
            //margin: [null],
            //marginTop: null,
            //marginRight: null,
            //marginBottom: null,
            //marginLeft: null,
            borderColor: '#4572A7',
            //borderWidth: 0,
            borderRadius: 0,
            defaultSeriesType: 'line',
            ignoreHiddenSeries: true,
            //inverted: false,
            //shadow: false,
            spacing: [10, 10, 15, 10],
            //spacingTop: 10,
            //spacingRight: 10,
            //spacingBottom: 15,
            //spacingLeft: 10,
            //style: {
            //    fontFamily: '"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif', // default font
            //    fontSize: '12px'
            //},
            backgroundColor: '#FFFFFF',
            //plotBackgroundColor: null,
            plotBorderColor: '#C0C0C0',
            //plotBorderWidth: 0,
            //plotShadow: false,
            //zoomType: ''
            resetZoomButton: {
                theme: {
                    zIndex: 20
                },
                position: {
                    align: 'right',
                    x: -10,
                    //verticalAlign: 'top',
                    y: 10
                }
                // relativeTo: 'plot'
            }
        },
        title: {
            text: 'Chart title',
            align: 'center',
            // floating: false,
            margin: 15,
            // x: 0,
            // verticalAlign: 'top',
            // y: null,
            style: {
                color: '#333333',
                fontSize: '18px'
            }

        },
        subtitle: {
            text: '',
            align: 'center',
            // floating: false
            // x: 0,
            // verticalAlign: 'top',
            // y: null,
            style: {
                color: '#555555'
            }
        },

        plotOptions: {
            line: { // base series options
                allowPointSelect: false,
                showCheckbox: false,
                animation: {
                    duration: 1000
                },
                //connectNulls: false,
                //cursor: 'default',
                //clip: true,
                //dashStyle: null,
                //enableMouseTracking: true,
                events: {},
                //legendIndex: 0,
                //linecap: 'round',
                lineWidth: 2,
                //shadow: false,
                // stacking: null,
                marker: {
                    //enabled: true,
                    //symbol: null,
                    lineWidth: 0,
                    radius: 4,
                    lineColor: '#FFFFFF',
                    //fillColor: null,
                    states: { // states for a single point
                        hover: {
                            enabled: true,
                            lineWidthPlus: 1,
                            radiusPlus: 2
                        },
                        select: {
                            fillColor: '#FFFFFF',
                            lineColor: '#000000',
                            lineWidth: 2
                        }
                    }
                },
                point: {
                    events: {}
                },
                dataLabels: {
                    align: 'center',
                    // defer: true,
                    // enabled: false,
                    formatter: function () {
                        return this.y === null ? '' : Highcharts.numberFormat(this.y, -1);
                    },
                    style: {
                        color: 'contrast',
                        fontSize: '11px',
                        fontWeight: 'bold',
                        textShadow: '0 0 6px contrast, 0 0 3px contrast'
                    },
                    verticalAlign: 'bottom', // above singular point
                    x: 0,
                    y: 0,
                    // backgroundColor: undefined,
                    // borderColor: undefined,
                    // borderRadius: undefined,
                    // borderWidth: undefined,
                    padding: 5
                    // shadow: false
                },
                cropThreshold: 300, // draw points outside the plot area when the number of points is less than this
                pointRange: 0,
                //pointStart: 0,
                //pointInterval: 1,
                //showInLegend: null, // auto: true for standalone series, false for linked series
                softThreshold: true,
                states: { // states for the entire series
                    hover: {
                        //enabled: false,
                        lineWidthPlus: 1,
                        marker: {
                            // lineWidth: base + 1,
                            // radius: base + 1
                        },
                        halo: {
                            size: 10,
                            opacity: 0.25
                        }
                    },
                    select: {
                        marker: {}
                    }
                },
                stickyTracking: true,
                //tooltip: {
                    //pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b>'
                    //valueDecimals: null,
                    //xDateFormat: '%A, %b %e, %Y',
                    //valuePrefix: '',
                    //ySuffix: ''
                //}
                turboThreshold: 1000
                // zIndex: null
            }
        },
        labels: {
            //items: [],
            style: {
                //font: defaultFont,
                position: ABSOLUTE,
                color: '#3E576F'
            }
        },
        legend: {
            enabled: true,
            align: 'center',
            //floating: false,
            layout: 'horizontal',
            labelFormatter: function () {
                return this.name;
            },
            //borderWidth: 0,
            borderColor: '#909090',
            borderRadius: 0,
            navigation: {
                // animation: true,
                activeColor: '#274b6d',
                // arrowSize: 12
                inactiveColor: '#CCC'
                // style: {} // text styles
            },
            // margin: 20,
            // reversed: false,
            shadow: false,
            // backgroundColor: null,
            /*style: {
                padding: '5px'
            },*/
            itemStyle: {
                color: '#333333',
                fontSize: '12px',
                fontWeight: 'bold'
            },
            itemHoverStyle: {
                //cursor: 'pointer', removed as of #601
                color: '#000'
            },
            itemHiddenStyle: {
                color: '#CCC'
            },
            itemCheckboxStyle: {
                position: ABSOLUTE,
                width: '13px', // for IE precision
                height: '13px'
            },
            // itemWidth: undefined,
            // symbolRadius: 0,
            // symbolWidth: 16,
            symbolPadding: 5,
            verticalAlign: 'bottom',
            // width: undefined,
            x: 0,
            y: 0,
            title: {
                //text: null,
                style: {
                    fontWeight: 'bold'
                }
            }
        },

        loading: {
            // hideDuration: 100,
            labelStyle: {
                fontWeight: 'bold',
                position: RELATIVE,
                top: '45%'
            },
            // showDuration: 0,
            style: {
                position: ABSOLUTE,
                backgroundColor: 'white',
                opacity: 0.5,
                textAlign: 'center'
            }
        },

        tooltip: {
            enabled: true,
            animation: hasSVG,
            //crosshairs: null,
            backgroundColor: 'rgba(249, 249, 249, .85)',
            borderWidth: 1,
            borderRadius: 3,
            dateTimeLabelFormats: {
                millisecond: '%A, %b %e, %H:%M:%S.%L',
                second: '%A, %b %e, %H:%M:%S',
                minute: '%A, %b %e, %H:%M',
                hour: '%A, %b %e, %H:%M',
                day: '%A, %b %e, %Y',
                week: 'Week from %A, %b %e, %Y',
                month: '%B %Y',
                year: '%Y'
            },
            footerFormat: '',
            //formatter: defaultFormatter,
            headerFormat: '<span style="font-size: 10px">{point.key}</span><br/>',
            pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b><br/>',
            shadow: true,
            //shape: 'callout',
            //shared: false,
            snap: isTouchDevice ? 25 : 10,
            style: {
                color: '#333333',
                cursor: 'default',
                fontSize: '12px',
                padding: '8px',
                pointerEvents: 'none', // #1686 http://caniuse.com/#feat=pointer-events
                whiteSpace: 'nowrap'
            }
            //xDateFormat: '%A, %b %e, %Y',
            //valueDecimals: null,
            //valuePrefix: '',
            //valueSuffix: ''
        },

        credits: {
            enabled: true,
            text: 'Highcharts.com',
            href: 'http://www.highcharts.com',
            position: {
                align: 'right',
                x: -10,
                verticalAlign: 'bottom',
                y: -5
            },
            style: {
                cursor: 'pointer',
                color: '#909090',
                fontSize: '9px'
            }
        }
    };



    /**
     * Set the time methods globally based on the useUTC option. Time method can be either
     * local time or UTC (default).
     */
    function setTimeMethods() {
        var globalOptions = defaultOptions.global,
            useUTC = globalOptions.useUTC,
            GET = useUTC ? 'getUTC' : 'get',
            SET = useUTC ? 'setUTC' : 'set';


        Date = globalOptions.Date || win.Date;
        timezoneOffset = useUTC && globalOptions.timezoneOffset;
        getTimezoneOffset = useUTC && globalOptions.getTimezoneOffset;
        makeTime = function (year, month, date, hours, minutes, seconds) {
            var d;
            if (useUTC) {
                d = Date.UTC.apply(0, arguments);
                d += getTZOffset(d);
            } else {
                d = new Date(
                    year,
                    month,
                    pick(date, 1),
                    pick(hours, 0),
                    pick(minutes, 0),
                    pick(seconds, 0)
                ).getTime();
            }
            return d;
        };
        getMinutes =      GET + 'Minutes';
        getHours =        GET + 'Hours';
        getDay =          GET + 'Day';
        getDate =         GET + 'Date';
        getMonth =        GET + 'Month';
        getFullYear =     GET + 'FullYear';
        setMilliseconds = SET + 'Milliseconds';
        setSeconds =      SET + 'Seconds';
        setMinutes =      SET + 'Minutes';
        setHours =        SET + 'Hours';
        setDate =         SET + 'Date';
        setMonth =        SET + 'Month';
        setFullYear =     SET + 'FullYear';

    }

    /**
     * Merge the default options with custom options and return the new options structure
     * @param {Object} options The new custom options
     */
    function setOptions(options) {

        // Copy in the default options
        defaultOptions = merge(true, defaultOptions, options);

        // Apply UTC
        setTimeMethods();

        return defaultOptions;
    }

    /**
     * Get the updated default options. Until 3.0.7, merely exposing defaultOptions for outside modules
     * wasn't enough because the setOptions method created a new object.
     */
    function getOptions() {
        return defaultOptions;
    }






    // Series defaults
    var defaultPlotOptions = defaultOptions.plotOptions,
        defaultSeriesOptions = defaultPlotOptions.line;

    // set the default time methods
    setTimeMethods();


    /**
     * Handle color operations. The object methods are chainable.
     * @param {String} input The input color in either rbga or hex format
     */
    function Color(input) {
        // Backwards compatibility, allow instanciation without new
        if (!(this instanceof Color)) {
            return new Color(input);
        }
        // Initialize
        this.init(input);
    }
    Color.prototype = {

        // Collection of parsers. This can be extended from the outside by pushing parsers
        // to Highcharts.Colors.prototype.parsers.
        parsers: [{
            // RGBA color
            regex: /rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]?(?:\.[0-9]+)?)\s*\)/,
            parse: function (result) {
                return [pInt(result[1]), pInt(result[2]), pInt(result[3]), parseFloat(result[4], 10)];
            }
        }, {
            // HEX color
            regex: /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/,
            parse: function (result) {
                return [pInt(result[1], 16), pInt(result[2], 16), pInt(result[3], 16), 1];
            }
        }, {
            // RGB color
            regex: /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/,
            parse: function (result) {
                return [pInt(result[1]), pInt(result[2]), pInt(result[3]), 1];
            }
        }],

        /**
         * Parse the input color to rgba array
         * @param {String} input
         */
        init: function (input) {
            var result,
                rgba,
                i,
                parser;

            this.input = input;

            // Gradients
            if (input && input.stops) {
                this.stops = map(input.stops, function (stop) {
                    return new Color(stop[1]);
                });

            // Solid colors
            } else {
                i = this.parsers.length;
                while (i-- && !rgba) {
                    parser = this.parsers[i];
                    result = parser.regex.exec(input);
                    if (result) {
                        rgba = parser.parse(result);
                    }
                }
            }
            this.rgba = rgba || [];
        },

        /**
         * Return the color a specified format
         * @param {String} format
         */
        get: function (format) {
            var input = this.input,
                rgba = this.rgba,
                ret;

            if (this.stops) {
                ret = merge(input);
                ret.stops = [].concat(ret.stops);
                each(this.stops, function (stop, i) {
                    ret.stops[i] = [ret.stops[i][0], stop.get(format)];
                });

            // it's NaN if gradient colors on a column chart
            } else if (rgba && !isNaN(rgba[0])) {
                if (format === 'rgb' || (!format && rgba[3] === 1)) {
                    ret = 'rgb(' + rgba[0] + ',' + rgba[1] + ',' + rgba[2] + ')';
                } else if (format === 'a') {
                    ret = rgba[3];
                } else {
                    ret = 'rgba(' + rgba.join(',') + ')';
                }
            } else {
                ret = input;
            }
            return ret;
        },

        /**
         * Brighten the color
         * @param {Number} alpha
         */
        brighten: function (alpha) {
            var i, 
                rgba = this.rgba;

            if (this.stops) {
                each(this.stops, function (stop) {
                    stop.brighten(alpha);
                });

            } else if (isNumber(alpha) && alpha !== 0) {
                for (i = 0; i < 3; i++) {
                    rgba[i] += pInt(alpha * 255);

                    if (rgba[i] < 0) {
                        rgba[i] = 0;
                    }
                    if (rgba[i] > 255) {
                        rgba[i] = 255;
                    }
                }
            }
            return this;
        },

        /**
         * Set the color's opacity to a given alpha value
         * @param {Number} alpha
         */
        setOpacity: function (alpha) {
            this.rgba[3] = alpha;
            return this;
        }
    };


    /**
     * A wrapper object for SVG elements
     */
    function SVGElement() {}

    SVGElement.prototype = {

        // Default base for animation
        opacity: 1,
        // For labels, these CSS properties are applied to the <text> node directly
        textProps: ['direction', 'fontSize', 'fontWeight', 'fontFamily', 'fontStyle', 'color',
            'lineHeight', 'width', 'textDecoration', 'textOverflow', 'textShadow'],

        /**
         * Initialize the SVG renderer
         * @param {Object} renderer
         * @param {String} nodeName
         */
        init: function (renderer, nodeName) {
            var wrapper = this;
            wrapper.element = nodeName === 'span' ?
                    createElement(nodeName) :
                    doc.createElementNS(SVG_NS, nodeName);
            wrapper.renderer = renderer;
        },

        /**
         * Animate a given attribute
         * @param {Object} params
         * @param {Number} options Options include duration, easing, step and complete
         * @param {Function} complete Function to perform at the end of animation
         */
        animate: function (params, options, complete) {
            var animOptions = pick(options, this.renderer.globalAnimation, true);
            stop(this); // stop regardless of animation actually running, or reverting to .attr (#607)
            if (animOptions) {
                animOptions = merge(animOptions, {}); //#2625
                if (complete) { // allows using a callback with the global animation without overwriting it
                    animOptions.complete = complete;
                }
                animate(this, params, animOptions);
            } else {
                this.attr(params, null, complete);
            }
            return this;
        },

        /**
         * Build an SVG gradient out of a common JavaScript configuration object
         */
        colorGradient: function (color, prop, elem) {
            var renderer = this.renderer,
                colorObject,
                gradName,
                gradAttr,
                radAttr,
                gradients,
                gradientObject,
                stops,
                stopColor,
                stopOpacity,
                radialReference,
                n,
                id,
                key = [],
                value;

            // Apply linear or radial gradients
            if (color.linearGradient) {
                gradName = 'linearGradient';
            } else if (color.radialGradient) {
                gradName = 'radialGradient';
            }

            if (gradName) {
                gradAttr = color[gradName];
                gradients = renderer.gradients;
                stops = color.stops;
                radialReference = elem.radialReference;

                // Keep < 2.2 kompatibility
                if (isArray(gradAttr)) {
                    color[gradName] = gradAttr = {
                        x1: gradAttr[0],
                        y1: gradAttr[1],
                        x2: gradAttr[2],
                        y2: gradAttr[3],
                        gradientUnits: 'userSpaceOnUse'
                    };
                }

                // Correct the radial gradient for the radial reference system
                if (gradName === 'radialGradient' && radialReference && !defined(gradAttr.gradientUnits)) {
                    radAttr = gradAttr; // Save the radial attributes for updating
                    gradAttr = merge(gradAttr,
                        renderer.getRadialAttr(radialReference, radAttr),
                        { gradientUnits: 'userSpaceOnUse' }
                        );
                }

                // Build the unique key to detect whether we need to create a new element (#1282)
                for (n in gradAttr) {
                    if (n !== 'id') {
                        key.push(n, gradAttr[n]);
                    }
                }
                for (n in stops) {
                    key.push(stops[n]);
                }
                key = key.join(',');

                // Check if a gradient object with the same config object is created within this renderer
                if (gradients[key]) {
                    id = gradients[key].attr('id');

                } else {

                    // Set the id and create the element
                    gradAttr.id = id = PREFIX + idCounter++;
                    gradients[key] = gradientObject = renderer.createElement(gradName)
                        .attr(gradAttr)
                        .add(renderer.defs);

                    gradientObject.radAttr = radAttr;

                    // The gradient needs to keep a list of stops to be able to destroy them
                    gradientObject.stops = [];
                    each(stops, function (stop) {
                        var stopObject;
                        if (stop[1].indexOf('rgba') === 0) {
                            colorObject = Color(stop[1]);
                            stopColor = colorObject.get('rgb');
                            stopOpacity = colorObject.get('a');
                        } else {
                            stopColor = stop[1];
                            stopOpacity = 1;
                        }
                        stopObject = renderer.createElement('stop').attr({
                            offset: stop[0],
                            'stop-color': stopColor,
                            'stop-opacity': stopOpacity
                        }).add(gradientObject);

                        // Add the stop element to the gradient
                        gradientObject.stops.push(stopObject);
                    });
                }

                // Set the reference to the gradient object
                value = 'url(' + renderer.url + '#' + id + ')';
                elem.setAttribute(prop, value);
                elem.gradient = key;

                // Allow the color to be concatenated into tooltips formatters etc. (#2995)
                color.toString = function () {
                    return value;
                };
            }
        },

        /**
         * Apply a polyfill to the text-stroke CSS property, by copying the text element
         * and apply strokes to the copy.
         *
         * Contrast checks at http://jsfiddle.net/highcharts/43soe9m1/2/
         *
         * docs: update default, document the polyfill and the limitations on hex colors and pixel values, document contrast pseudo-color
         */
        applyTextShadow: function (textShadow) {
            var elem = this.element,
                tspans,
                hasContrast = textShadow.indexOf('contrast') !== -1,
                styles = {},
                forExport = this.renderer.forExport,
                // IE10 and IE11 report textShadow in elem.style even though it doesn't work. Check
                // this again with new IE release. In exports, the rendering is passed to PhantomJS.
                supports = forExport || (elem.style.textShadow !== UNDEFINED && !isMS);

            // When the text shadow is set to contrast, use dark stroke for light text and vice versa
            if (hasContrast) {
                styles.textShadow = textShadow = textShadow.replace(/contrast/g, this.renderer.getContrast(elem.style.fill));
            }

            // Safari with retina displays as well as PhantomJS bug (#3974). Firefox does not tolerate this,
            // it removes the text shadows.
            if (isWebKit || forExport) {
                styles.textRendering = 'geometricPrecision';
            }

            /* Selective side-by-side testing in supported browser (http://jsfiddle.net/highcharts/73L1ptrh/)
            if (elem.textContent.indexOf('2.') === 0) {
                elem.style['text-shadow'] = 'none';
                supports = false;
            }
            // */

            // No reason to polyfill, we've got native support
            if (supports) {
                this.css(styles); // Apply altered textShadow or textRendering workaround
            } else {

                this.fakeTS = true; // Fake text shadow

                // In order to get the right y position of the clones,
                // copy over the y setter
                this.ySetter = this.xSetter;

                tspans = [].slice.call(elem.getElementsByTagName('tspan'));
                each(textShadow.split(/\s?,\s?/g), function (textShadow) {
                    var firstChild = elem.firstChild,
                        color,
                        strokeWidth;

                    textShadow = textShadow.split(' ');
                    color = textShadow[textShadow.length - 1];

                    // Approximately tune the settings to the text-shadow behaviour
                    strokeWidth = textShadow[textShadow.length - 2];

                    if (strokeWidth) {
                        each(tspans, function (tspan, y) {
                            var clone;

                            // Let the first line start at the correct X position
                            if (y === 0) {
                                tspan.setAttribute('x', elem.getAttribute('x'));
                                y = elem.getAttribute('y');
                                tspan.setAttribute('y', y || 0);
                                if (y === null) {
                                    elem.setAttribute('y', 0);
                                }
                            }

                            // Create the clone and apply shadow properties
                            clone = tspan.cloneNode(1);
                            attr(clone, {
                                'class': PREFIX + 'text-shadow',
                                'fill': color,
                                'stroke': color,
                                'stroke-opacity': 1 / mathMax(pInt(strokeWidth), 3),
                                'stroke-width': strokeWidth,
                                'stroke-linejoin': 'round'
                            });
                            elem.insertBefore(clone, firstChild);
                        });
                    }
                });
            }
        },

        /**
         * Set or get a given attribute
         * @param {Object|String} hash
         * @param {Mixed|Undefined} val
         */
        attr: function (hash, val, complete) {
            var key,
                value,
                element = this.element,
                hasSetSymbolSize,
                ret = this,
                skipAttr;

            // single key-value pair
            if (typeof hash === 'string' && val !== UNDEFINED) {
                key = hash;
                hash = {};
                hash[key] = val;
            }

            // used as a getter: first argument is a string, second is undefined
            if (typeof hash === 'string') {
                ret = (this[hash + 'Getter'] || this._defaultGetter).call(this, hash, element);

            // setter
            } else {

                for (key in hash) {
                    value = hash[key];
                    skipAttr = false;



                    if (this.symbolName && /^(x|y|width|height|r|start|end|innerR|anchorX|anchorY)/.test(key)) {
                        if (!hasSetSymbolSize) {
                            this.symbolAttr(hash);
                            hasSetSymbolSize = true;
                        }
                        skipAttr = true;
                    }

                    if (this.rotation && (key === 'x' || key === 'y')) {
                        this.doTransform = true;
                    }

                    if (!skipAttr) {
                        (this[key + 'Setter'] || this._defaultSetter).call(this, value, key, element);
                    }

                    // Let the shadow follow the main element
                    if (this.shadows && /^(width|height|visibility|x|y|d|transform|cx|cy|r)$/.test(key)) {
                        this.updateShadows(key, value);
                    }
                }

                // Update transform. Do this outside the loop to prevent redundant updating for batch setting
                // of attributes.
                if (this.doTransform) {
                    this.updateTransform();
                    this.doTransform = false;
                }

            }

            // In accordance with animate, run a complete callback
            if (complete) {
                complete();
            }

            return ret;
        },

        updateShadows: function (key, value) {
            var shadows = this.shadows,
                i = shadows.length;
            while (i--) {
                shadows[i].setAttribute(
                    key,
                    key === 'height' ?
                            Math.max(value - (shadows[i].cutHeight || 0), 0) :
                            key === 'd' ? this.d : value
                );
            }
        },

        /**
         * Add a class name to an element
         */
        addClass: function (className) {
            var element = this.element,
                currentClassName = attr(element, 'class') || '';

            if (currentClassName.indexOf(className) === -1) {
                attr(element, 'class', currentClassName + ' ' + className);
            }
            return this;
        },
        /* hasClass and removeClass are not (yet) needed
        hasClass: function (className) {
            return attr(this.element, 'class').indexOf(className) !== -1;
        },
        removeClass: function (className) {
            attr(this.element, 'class', attr(this.element, 'class').replace(className, ''));
            return this;
        },
        */

        /**
         * If one of the symbol size affecting parameters are changed,
         * check all the others only once for each call to an element's
         * .attr() method
         * @param {Object} hash
         */
        symbolAttr: function (hash) {
            var wrapper = this;

            each(['x', 'y', 'r', 'start', 'end', 'width', 'height', 'innerR', 'anchorX', 'anchorY'], function (key) {
                wrapper[key] = pick(hash[key], wrapper[key]);
            });

            wrapper.attr({
                d: wrapper.renderer.symbols[wrapper.symbolName](
                    wrapper.x,
                    wrapper.y,
                    wrapper.width,
                    wrapper.height,
                    wrapper
                )
            });
        },

        /**
         * Apply a clipping path to this object
         * @param {String} id
         */
        clip: function (clipRect) {
            return this.attr('clip-path', clipRect ? 'url(' + this.renderer.url + '#' + clipRect.id + ')' : NONE);
        },

        /**
         * Calculate the coordinates needed for drawing a rectangle crisply and return the
         * calculated attributes
         * @param {Number} strokeWidth
         * @param {Number} x
         * @param {Number} y
         * @param {Number} width
         * @param {Number} height
         */
        crisp: function (rect) {

            var wrapper = this,
                key,
                attribs = {},
                normalizer,
                strokeWidth = rect.strokeWidth || wrapper.strokeWidth || 0;

            normalizer = mathRound(strokeWidth) % 2 / 2; // mathRound because strokeWidth can sometimes have roundoff errors

            // normalize for crisp edges
            rect.x = mathFloor(rect.x || wrapper.x || 0) + normalizer;
            rect.y = mathFloor(rect.y || wrapper.y || 0) + normalizer;
            rect.width = mathFloor((rect.width || wrapper.width || 0) - 2 * normalizer);
            rect.height = mathFloor((rect.height || wrapper.height || 0) - 2 * normalizer);
            rect.strokeWidth = strokeWidth;

            for (key in rect) {
                if (wrapper[key] !== rect[key]) { // only set attribute if changed
                    wrapper[key] = attribs[key] = rect[key];
                }
            }

            return attribs;
        },

        /**
         * Set styles for the element
         * @param {Object} styles
         */
        css: function (styles) {
            var elemWrapper = this,
                oldStyles = elemWrapper.styles,
                newStyles = {},
                elem = elemWrapper.element,
                textWidth,
                n,
                serializedCss = '',
                hyphenate,
                hasNew = !oldStyles;

            // convert legacy
            if (styles && styles.color) {
                styles.fill = styles.color;
            }

            // Filter out existing styles to increase performance (#2640)
            if (oldStyles) {
                for (n in styles) {
                    if (styles[n] !== oldStyles[n]) {
                        newStyles[n] = styles[n];
                        hasNew = true;
                    }
                }
            }
            if (hasNew) {
                textWidth = elemWrapper.textWidth =
                    (styles && styles.width && elem.nodeName.toLowerCase() === 'text' && pInt(styles.width)) ||
                    elemWrapper.textWidth; // #3501

                // Merge the new styles with the old ones
                if (oldStyles) {
                    styles = extend(
                        oldStyles,
                        newStyles
                    );
                }

                // store object
                elemWrapper.styles = styles;

                if (textWidth && (useCanVG || (!hasSVG && elemWrapper.renderer.forExport))) {
                    delete styles.width;
                }

                // serialize and set style attribute
                if (isMS && !hasSVG) {
                    css(elemWrapper.element, styles);
                } else {
                    hyphenate = function (a, b) {
                        return '-' + b.toLowerCase();
                    };
                    for (n in styles) {
                        serializedCss += n.replace(/([A-Z])/g, hyphenate) + ':' + styles[n] + ';';
                    }
                    attr(elem, 'style', serializedCss); // #1881
                }


                // re-build text
                if (textWidth && elemWrapper.added) {
                    elemWrapper.renderer.buildText(elemWrapper);
                }
            }

            return elemWrapper;
        },

        /**
         * Add an event listener
         * @param {String} eventType
         * @param {Function} handler
         */
        on: function (eventType, handler) {
            var svgElement = this,
                element = svgElement.element;

            // touch
            if (hasTouch && eventType === 'click') {
                element.ontouchstart = function (e) {
                    svgElement.touchEventFired = Date.now();
                    e.preventDefault();
                    handler.call(element, e);
                };
                element.onclick = function (e) {
                    if (userAgent.indexOf('Android') === -1 || Date.now() - (svgElement.touchEventFired || 0) > 1100) { // #2269
                        handler.call(element, e);
                    }
                };
            } else {
                // simplest possible event model for internal use
                element['on' + eventType] = handler;
            }
            return this;
        },

        /**
         * Set the coordinates needed to draw a consistent radial gradient across
         * pie slices regardless of positioning inside the chart. The format is
         * [centerX, centerY, diameter] in pixels.
         */
        setRadialReference: function (coordinates) {
            var existingGradient = this.renderer.gradients[this.element.gradient];

            this.element.radialReference = coordinates;

            // On redrawing objects with an existing gradient, the gradient needs
            // to be repositioned (#3801)
            if (existingGradient && existingGradient.radAttr) {
                existingGradient.animate(
                    this.renderer.getRadialAttr(
                        coordinates,
                        existingGradient.radAttr
                    )
                );
            }

            return this;
        },

        /**
         * Move an object and its children by x and y values
         * @param {Number} x
         * @param {Number} y
         */
        translate: function (x, y) {
            return this.attr({
                translateX: x,
                translateY: y
            });
        },

        /**
         * Invert a group, rotate and flip
         */
        invert: function () {
            var wrapper = this;
            wrapper.inverted = true;
            wrapper.updateTransform();
            return wrapper;
        },

        /**
         * Private method to update the transform attribute based on internal
         * properties
         */
        updateTransform: function () {
            var wrapper = this,
                translateX = wrapper.translateX || 0,
                translateY = wrapper.translateY || 0,
                scaleX = wrapper.scaleX,
                scaleY = wrapper.scaleY,
                inverted = wrapper.inverted,
                rotation = wrapper.rotation,
                element = wrapper.element,
                transform;

            // flipping affects translate as adjustment for flipping around the group's axis
            if (inverted) {
                translateX += wrapper.attr('width');
                translateY += wrapper.attr('height');
            }

            // Apply translate. Nearly all transformed elements have translation, so instead
            // of checking for translate = 0, do it always (#1767, #1846).
            transform = ['translate(' + translateX + ',' + translateY + ')'];

            // apply rotation
            if (inverted) {
                transform.push('rotate(90) scale(-1,1)');
            } else if (rotation) { // text rotation
                transform.push('rotate(' + rotation + ' ' + (element.getAttribute('x') || 0) + ' ' + (element.getAttribute('y') || 0) + ')');

                // Delete bBox memo when the rotation changes
                //delete wrapper.bBox;
            }

            // apply scale
            if (defined(scaleX) || defined(scaleY)) {
                transform.push('scale(' + pick(scaleX, 1) + ' ' + pick(scaleY, 1) + ')');
            }

            if (transform.length) {
                element.setAttribute('transform', transform.join(' '));
            }
        },
        /**
         * Bring the element to the front
         */
        toFront: function () {
            var element = this.element;
            element.parentNode.appendChild(element);
            return this;
        },


        /**
         * Break down alignment options like align, verticalAlign, x and y
         * to x and y relative to the chart.
         *
         * @param {Object} alignOptions
         * @param {Boolean} alignByTranslate
         * @param {String[Object} box The box to align to, needs a width and height. When the
         *        box is a string, it refers to an object in the Renderer. For example, when
         *        box is 'spacingBox', it refers to Renderer.spacingBox which holds width, height
         *        x and y properties.
         *
         */
        align: function (alignOptions, alignByTranslate, box) {
            var align,
                vAlign,
                x,
                y,
                attribs = {},
                alignTo,
                renderer = this.renderer,
                alignedObjects = renderer.alignedObjects;

            // First call on instanciate
            if (alignOptions) {
                this.alignOptions = alignOptions;
                this.alignByTranslate = alignByTranslate;
                if (!box || isString(box)) { // boxes other than renderer handle this internally
                    this.alignTo = alignTo = box || 'renderer';
                    erase(alignedObjects, this); // prevent duplicates, like legendGroup after resize
                    alignedObjects.push(this);
                    box = null; // reassign it below
                }

            // When called on resize, no arguments are supplied
            } else {
                alignOptions = this.alignOptions;
                alignByTranslate = this.alignByTranslate;
                alignTo = this.alignTo;
            }

            box = pick(box, renderer[alignTo], renderer);

            // Assign variables
            align = alignOptions.align;
            vAlign = alignOptions.verticalAlign;
            x = (box.x || 0) + (alignOptions.x || 0); // default: left align
            y = (box.y || 0) + (alignOptions.y || 0); // default: top align

            // Align
            if (align === 'right' || align === 'center') {
                x += (box.width - (alignOptions.width || 0)) /
                        { right: 1, center: 2 }[align];
            }
            attribs[alignByTranslate ? 'translateX' : 'x'] = mathRound(x);


            // Vertical align
            if (vAlign === 'bottom' || vAlign === 'middle') {
                y += (box.height - (alignOptions.height || 0)) /
                        ({ bottom: 1, middle: 2 }[vAlign] || 1);

            }
            attribs[alignByTranslate ? 'translateY' : 'y'] = mathRound(y);

            // Animate only if already placed
            this[this.placed ? 'animate' : 'attr'](attribs);
            this.placed = true;
            this.alignAttr = attribs;

            return this;
        },

        /**
         * Get the bounding box (width, height, x and y) for the element
         */
        getBBox: function (reload, rot) {
            var wrapper = this,
                bBox,// = wrapper.bBox,
                renderer = wrapper.renderer,
                width,
                height,
                rotation,
                rad,
                element = wrapper.element,
                styles = wrapper.styles,
                textStr = wrapper.textStr,
                textShadow,
                elemStyle = element.style,
                toggleTextShadowShim,
                cache = renderer.cache,
                cacheKeys = renderer.cacheKeys,
                cacheKey;

            rotation = pick(rot, wrapper.rotation);
            rad = rotation * deg2rad;

            if (textStr !== UNDEFINED) {

                // Properties that affect bounding box
                cacheKey = ['', rotation || 0, styles && styles.fontSize, element.style.width].join(',');

                // Since numbers are monospaced, and numerical labels appear a lot in a chart,
                // we assume that a label of n characters has the same bounding box as others
                // of the same length.
                if (textStr === '' || numRegex.test(textStr)) {
                    cacheKey = 'num:' + textStr.toString().length + cacheKey;

                // Caching all strings reduces rendering time by 4-5%.
                } else {
                    cacheKey = textStr + cacheKey;
                }
            }

            if (cacheKey && !reload) {
                bBox = cache[cacheKey];
            }

            // No cache found
            if (!bBox) {

                // SVG elements
                if (element.namespaceURI === SVG_NS || renderer.forExport) {
                    try { // Fails in Firefox if the container has display: none.

                        // When the text shadow shim is used, we need to hide the fake shadows
                        // to get the correct bounding box (#3872)
                        toggleTextShadowShim = this.fakeTS && function (display) {
                            each(element.querySelectorAll('.' + PREFIX + 'text-shadow'), function (tspan) {
                                tspan.style.display = display;
                            });
                        };

                        // Workaround for #3842, Firefox reporting wrong bounding box for shadows
                        if (isFirefox && elemStyle.textShadow) {
                            textShadow = elemStyle.textShadow;
                            elemStyle.textShadow = '';
                        } else if (toggleTextShadowShim) {
                            toggleTextShadowShim(NONE);
                        }

                        bBox = element.getBBox ?
                            // SVG: use extend because IE9 is not allowed to change width and height in case
                            // of rotation (below)
                            extend({}, element.getBBox()) :
                            // Canvas renderer and legacy IE in export mode
                            {
                                width: element.offsetWidth,
                                height: element.offsetHeight
                            };

                        // #3842
                        if (textShadow) {
                            elemStyle.textShadow = textShadow;
                        } else if (toggleTextShadowShim) {
                            toggleTextShadowShim('');
                        }