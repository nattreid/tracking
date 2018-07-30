(function (window) {

    /**
     * Tracking
     * @returns {nTracker}
     */
    function nTracker() {

        /**
         * cookie name
         */
        var cookie = 'tracker';

        /**
         * tracking URL
         */
        var trackingUrl = '/track';

        /**
         * click
         */
        var clickUrl = '/clickTrack';

        /**
         * Send AJAX
         * @param {array} data
         * @param {string} url
         * @returns {boolean}
         */
        function post(data, url) {
            var xmlhttp;
            try {
                // Opera 8.0+, Firefox, Safari
                xmlhttp = new XMLHttpRequest();
            } catch (e) {
                // Internet Explorer Browsers
                try {
                    xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
                } catch (e) {
                    try {
                        xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
                    } catch (e) {
                        // Something went wrong
                        return false;
                    }
                }
            }

            xmlhttp.open('POST', url, true);
            var cookieName = cookie;
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4) {
                    if (xmlhttp.status === 200) {
                        document.cookie = cookieName + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC';
                    }
                }
            };
            xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xmlhttp.send(data);
        }

        /**
         * Add parameter from window.search do data
         * @param {array} data
         * @param {string} parameter
         */
        function addParameter(data, parameter) {
            var regex = '(' + parameter + '=[a-z0-9-]+)';
            var search = window.location.search.match(regex);
            if (search) {
                data.push(search[1]);
            }
            var hash = window.location.hash.match(regex);
            if (hash) {
                data.push(hash[1]);
            }
        }

        /**
         * Get browser
         * @returns {string}
         */
        function getBrowser() {
            var ua = navigator.userAgent, tem,
                M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
            if (/trident/i.test(M[1])) {
                tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
                return 'IE ' + (tem[1] || '');
            }
            if (M[1] === 'Chrome') {
                tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
                if (tem !== null)
                    return tem.slice(1).join(' ').replace('OPR', 'Opera');
            }
            M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
            if ((tem = ua.match(/version\/(\d+)/i)) !== null)
                M.splice(1, 1, tem[1]);
            return M.join(' ');
        }

        /**
         * Get click dataset
         * @param {array} path
         * @returns {array|null}
         */
        function getClickDataset(path) {
            var el;
            for (var i = 0; i < path.length; i++) {
                el = path[i];
                if (el.dataset.nctr !== undefined) {
                    return el.dataset;
                }
            }
            return null;
        }

        /**
         * Track
         * @param {string} url
         * @param {string} referrer
         * @param {boolean} leaving
         */
        this.track = function (url, referrer, leaving) {
            if (url == null) {
                url = encodeURIComponent(document.location.href);
            }
            if (referrer == null) {
                referrer = document.referrer;
            }

            var data = [];
            data.push('url=' + url);
            data.push('referer=' + referrer);
            data.push('browser=' + getBrowser());
            addParameter(data, 'utm_source');
            addParameter(data, 'utm_medium');
            addParameter(data, 'utm_campaign');

            if (leaving != null) {
                data.push('leave=' + Math.floor(Math.random() * 10000));
            }

            post(data.join('&'), trackingUrl);
        };

        /**
         * Track leave page
         */
        function leave() {
            var data = [];
            data.push('leave=' + Math.floor(Math.random() * 10000));

            post(data.join('&'), trackingUrl);
        }

        /**
         * Click Track on click to element
         */
        function initOnClick() {
            var clicks = document.querySelectorAll('[data-nctr]');

            for (var i = 0; i < clicks.length; i++) {
                var tag = clicks[i];
                if (tag.addEventListener) {
                    tag.addEventListener("click", clickTrack, false);
                } else {
                    if (tag.attachEvent) {
                        tag.attachEvent("onclick", clickTrack);
                    }
                }

            }
        }

        /**
         * Handler for click event
         * @param event
         */
        function clickTrack(event) {
            var dataset = getClickDataset(event.path);

            var data = [];
            data.push('click=' + dataset.nctr);
            data.push('browser=' + getBrowser());
            if (dataset.ncval !== undefined) {
                data.push('value=' + dataset.ncval);
            }
            if (dataset.average !== undefined) {
                data.push('average=' + dataset.average);
            }
            if (dataset.ncsum !== undefined) {
                data.push('sum=' + dataset.ncsum);
            }

            post(data.join('&'), clickUrl);
        }

        this.run = function () {
            var obj = this;

            // leave
            window.onbeforeunload = leave;

            // onLoad
            var onLoad = function () {
                obj.track();
                initOnClick();
            };

            if (window.addEventListener) {
                window.addEventListener('load', onLoad, false);
            } else {
                if (window.attachEvent) {
                    window.attachEvent('onload', onLoad);
                }
            }
        };
    }

    window.nTracker = new nTracker();
    window.nTracker.run();

})(window);
