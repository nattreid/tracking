(function (window) {
    
    /**
     * Trackovani
     * @returns {nTracker}
     */
    function nTracker() {

        /**
         * nazev cookie
         */
        var cookie = 'tracker';

        /**
         * url pro tracking
         */
        var trackingUrl = '/track';

        /**
         * url pro tracking
         */
        var clickUrl = '/clickTrack';

        /**
         * Trackovani parametru
         * @param {string} url
         * @param {string} referrer
         * @param {string} leaving
         */
        this.track = function (url, referrer, leaving) {
            if (typeof url === 'undefined') {
                url = encodeURIComponent(document.location.href);
            }
            if (typeof referrer === 'undefined') {
                referrer = document.referrer;
            }

            var data = new Array();
            data.push('url=' + url);
            data.push('referer=' + referrer);
            data.push('browser=' + this.getBrowser());
            this.getParameter(data, 'utm_source');
            this.getParameter(data, 'utm_medium');
            this.getParameter(data, 'utm_campaign');

            if (typeof leaving !== 'undefined') {
                data.push('leave=' + Math.floor(Math.random() * 10000));
            }

            this.post(data.join('&'), trackingUrl);
        };

        /**
         * Trackovani parametru pres ajax (zaloguje i odchod ze stranky)
         * @param {string} url
         */
        this.ajaxTrack = function (url) {
            this.track(url, encodeURIComponent(document.location.href), true);
        };

        /**
         * Track kliku po kliknuti na odkaz (nebo jakykoli html prvek s data atributem)
         */
        this.onClick = function () {
            var obj = this;
            var clicks = document.querySelectorAll('[data-nctr]');

            for (var i = 0; i < clicks.length; i++) {
                var func = function (event) {
                    var name = event.path[0].dataset.nctr;
                    var value = event.path[0].dataset.ncval;
                    var average = event.path[0].dataset.ncavg;
                    var sum = event.path[0].dataset.ncsum;
                    obj.click(name, value, average, sum);
                };
                var tag = clicks[i];
                if (tag.addEventListener) {
                    tag.addEventListener("click", func, false);
                } else {
                    if (tag.attachEvent) {
                        tag.attachEvent("onclick", func);
                    }
                }

            }
        };

        /**
         * Trackovani kliku
         * @param {string} name
         * @param {string} value
         * @param {float} average
         * @param {float} sum
         */
        this.click = function (name, value, average, sum) {
            var data = new Array();
            data.push('click=' + name);
            data.push('browser=' + this.getBrowser());
            if (typeof value !== 'undefined') {
                data.push('value=' + value);
            }
            if (typeof average !== 'undefined') {
                data.push('average=' + average);
            }
            if (typeof sum !== 'undefined') {
                data.push('sum=' + sum);
            }

            this.post(data.join('&'), clickUrl);
        };

        /**
         * Trackovani opusteni stranky
         */
        this.leaving = function () {
            var data = new Array();
            data.push('leave=' + Math.floor(Math.random() * 10000));

            this.post(data.join('&'), trackingUrl);
        };

        this.getBrowser = function () {
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
        };

        this.getParameter = function (data, parameter) {
            var regex = '(' + parameter + '=[a-z0-9-]+)';
            var match = window.location.search.match(regex);
            if (match) {
                data.push(match[1]);
            }
            var match = window.location.hash.match(regex);
            if (match) {
                data.push(match[1]);
            }
        };

        this.post = function (data, url) {
            var xmlhttp;
            try {
                // Opera 8.0+, Firefox, Safari
                xmlhttp = new XMLHttpRequest();
            } catch (e) {
                // Internet Explorer Browsers
                try {
                    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                    try {
                        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e) {
                        // Something went wrong
                        return false;
                    }
                }
            }

            xmlhttp.open("POST", url, true);
            var cookieName = cookie;
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4) {
                    if (xmlhttp.status === 200) {
                        document.cookie = cookieName + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC';
                    }
                }
            };
            xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlhttp.send(data);
        };

        /**
         * Spusteni skriptu
         */
        this.run = function () {
            var obj = this;

            obj.onLeave(function () {
                obj.leaving();
            });

            obj.onLoad(function () {
                obj.track();
                obj.onClick();
            });
        };

        /**
         * Metoda pro spusteni pri opusteni stranky
         * @param {function} func
         */
        this.onLeave = function (func) {
            window.onbeforeunload = func;
        };

        /**
         * Metoda pro spusteni az po nacteni stranky
         * @param {function} func
         */
        this.onLoad = function (func) {
            if (window.addEventListener) {
                window.addEventListener("load", func, false);
            } else {
                if (window.attachEvent) {
                    window.attachEvent("onload", func);
                }
            }
        };

    }

    new nTracker().run();

})(window);
