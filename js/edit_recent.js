var edit_recent = (function () {
    var KEY = APP_NAME + '_edit_recent';
    var MAX = 20;

    function get() {
        try {
            var data = localStorage.getItem(KEY);
            return data ? JSON.parse(data) : [];
        } catch (e) {
            return [];
        }
    }

    function save(list) {
        try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) {}
    }

    function escapeHtml(str) {
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    return {
        get: get,
        add: function (path) {
            var list = get();
            var filtered = [];
            for (var i = 0; i < list.length; i++) {
                if (list[i] !== path) filtered.push(list[i]);
            }
            filtered.unshift(path);
            save(filtered.slice(0, MAX));
        },
        clear: function () {
            try { localStorage.removeItem(KEY); } catch (e) {}
        },
        render: function (containerId) {
            var el = document.getElementById(containerId);
            if (!el) return;
            var list = get();
            var html = '';
            for (var i = 0; i < list.length; i++) {
                var path = list[i];
                var idx = path.lastIndexOf('/');
                var dir = idx >= 0 ? path.substring(0, idx) : '';
                var base = idx >= 0 ? path.substring(idx + 1) : path;
                var link = 'file.php?act=edit_text&path=' + encodeURIComponent(path);
                html += '<div style="font-size:12px;font-style:italic;border-bottom:1px dotted #ddd;padding:4px">'
                    + '<a href="' + link + '">' + escapeHtml(dir) + '/<b>' + escapeHtml(base) + '</b></a></div>';
            }
            el.innerHTML = html;
        }
    };
})();
