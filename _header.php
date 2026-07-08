<?php

defined('ACCESS') or exit;

$site_title = $site_title ?? SITE_TITLE;
$site_sidebar = '';
$header_goto_path = '';

if (IS_LOGIN) {
    $header_goto_path = !empty($path) ? $path : '';
    $header_goto_path = (string) $header_goto_path;

    if ($header_goto_path !== '/') {
        $header_goto_path = rtrim($header_goto_path, '/');

        if (is_dir($header_goto_path)) {
            $header_goto_path .= '/';
        }
    }
}
?><!DOCTYPE html>
<html lang="vi">

<head>
    <title><?= htmlspecialchars((string) $site_title) ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="icon/icon.png">
    <link rel="icon" type="image/x-icon" href="icon/icon.ico" />
    <link rel="shortcut icon" type="image/x-icon" href="icon/icon.ico" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.2/themes/base/jquery-ui.min.css" integrity="sha512-EPUSESSvM4jLngGTPXMyezlH1YxB96b4ZSUvvavOR2m2lu9uyRw4K9IdMqf6Gj/awwqAXopEvjljsdqNJM9W4A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.2/themes/base/theme.min.css" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.2/jquery-ui.min.js" integrity="sha512-sJcXQUDDRzmJucAnIvFskH17pgX+JW0pjjfgzRyV0HQdUV3ljURrYP8VzbGviocumNEPSV5E9Ue7L6PW+Aly4A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

    <link rel="stylesheet" type="text/css" href="<?= asset('style.css') ?>" media="all,handheld" />
    <link rel="stylesheet" type="text/css" href="<?= asset('js/nightmare_scrolltop.css') ?>" media="all,handheld" />
    <script>const APP_NAME = '<?= APP_NAME ?>';</script>
    <script src="<?= asset('js/app.js') ?>" defer></script>
    <script src="<?= asset('js/nightmare_scrolltop.js') ?>"></script>
    <script src="<?= asset('js/edit_recent.js') ?>"></script>
</head>

<body>
<div id="app">

<div id="app-header">
    <ul>
        <?php if (IS_LOGIN) { ?>
            <button id="nav-menu">&#9776;</button>
        <?php } ?>
        <li><a href="<?= action_link('index') ?>"><img src="icon/home.png" /></a></li>
        <?php if (IS_LOGIN) { ?>
            <li><a href="db/"><img src="icon/database.png"/></a></li>
            <li><a href="<?= action_link('setting') ?>"><img src="icon/setting.png" /></a></li>
            <li>
                <img id="header-goto-path-toggle" src="icon/search.png" alt="Goto path" role="button" tabindex="0" aria-controls="header-goto-path-form" aria-expanded="false" />
            </li>
        <?php } ?>
    </ul>
    <?php if (IS_LOGIN) { ?>
        <form id="header-goto-path-form" class="is-hidden" action="<?= action_link('index') ?>" method="get">
            <input id="header-goto-path" name="path" type="text" value="<?= htmlspecialchars($header_goto_path) ?>">
            <input type="submit" value="GO">
        </form>
    <?php } ?>
    <div style="clear: both"></div>
</div>

<div id="app-body">

<style>
#header-goto-path-toggle {
    display: block;
    cursor: pointer;
    vertical-align: middle;
}

#header-goto-path-form {
    display: flex;
    gap: 4px;
    align-items: center;
    margin-top: 6px;
}

#header-goto-path-form.is-hidden {
    display: none;
}

#header-goto-path-form #header-goto-path {
    flex: 1 1 auto;
    min-width: 0;
    width: auto !important;
}

#header-goto-path-form input[type=submit] {
    flex: 0 0 auto;
}

.ui-autocomplete {
    max-height: 240px;
    max-width: 90%;
    box-sizing: border-box;
    padding-right: 4px;
    overflow-x: auto;
    overflow-y: auto;
}

.ui-autocomplete .ui-menu-item,
.ui-autocomplete .ui-menu-item-wrapper {
    white-space: nowrap;
}

.autocomplete-match {
    font-weight: bold;
}
</style>

<?php if (IS_LOGIN) { ?>
<script>
(() => {
    const $input = $('#header-goto-path');
    const $form = $('#header-goto-path-form');
    const toggle = document.getElementById('header-goto-path-toggle');
    const input = document.getElementById('header-goto-path');

    if ($input.length === 0 || $form.length === 0 || toggle === null || input === null) {
        return;
    }

    let keep_open_after_select = false;
    let reopen_timer = null;
    let last_slash_count = (input.value.match(/\//g) || []).length;
    let autocomplete_load_id = 0;

    const move_caret_to_end = () => {
        const length = input.value.length;

        input.focus();

        try {
            input.setSelectionRange(length, length);
        } catch (error) {
        }

        input.scrollLeft = input.scrollWidth;
    };

    const escape_html = (value) => $('<div>').text(value).html();
    const escape_regex = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const get_directory_prefix = (value) => {
        const trimmed = value.trim();

        if (trimmed === '' || trimmed.endsWith('/')) {
            return trimmed;
        }

        const slash_index = trimmed.lastIndexOf('/');

        if (slash_index === -1) {
            return '';
        }

        return trimmed.slice(0, slash_index + 1);
    };
    const get_search_segment = (value) => {
        const trimmed = value.trim();

        if (trimmed.endsWith('/')) {
            return '';
        }

        const slash_index = trimmed.lastIndexOf('/');

        return slash_index === -1 ? trimmed : trimmed.slice(slash_index + 1);
    };
    const to_full_path = (value, item) => get_directory_prefix(value) + item;
    const count_slashes = (value) => (value.match(/\//g) || []).length;
    const filter_autocomplete_items = (items, keyword) => {
        const normalized_keyword = keyword.toLowerCase();

        if (normalized_keyword === '') {
            return items;
        }

        return items.filter((item) => String(item).toLowerCase().includes(normalized_keyword));
    };
    const render_autocomplete_item = function (ul, item) {
        const term = get_search_segment(this.term || '');
        let label = escape_html(item.label || item.value || '');

        if (term !== '') {
            label = label.replace(
                new RegExp(escape_regex(term), 'i'),
                '<span class="autocomplete-match">$&</span>'
            );
        }

        return $('<li>').append($('<div>').html(label)).appendTo(ul);
    };
    const has_autocomplete = () => Boolean($input.data('ui-autocomplete'));
    const init_autocomplete = (items) => {
        if (has_autocomplete()) {
            $input.autocomplete('destroy');
        }

        $input.autocomplete({
            source: function (request, response) {
                response(filter_autocomplete_items(items, get_search_segment(request.term)));
            },
            minLength: 1,
            focus: function (event) {
                event.preventDefault();
            },
            select: function (event, ui) {
                event.preventDefault();

                const value = to_full_path(this.value, ui.item.value);

                $(this).val(value);
                move_caret_to_end();

                if (!String(ui.item.value).endsWith('/')) {
                    keep_open_after_select = false;
                    return;
                }

                keep_open_after_select = true;

                const slash_count = count_slashes(value);

                if (slash_count !== last_slash_count) {
                    last_slash_count = slash_count;
                    keep_open_after_select = false;
                    load_autocomplete_data();
                    return;
                }
            },
            close: function () {
                if (!keep_open_after_select || $(this).val() === '') {
                    return;
                }

                reopen_autocomplete($(this).val());
            }
        });

        $input.autocomplete('instance')._renderItem = render_autocomplete_item;
    };
    const load_autocomplete_data = async () => {
        const load_id = autocomplete_load_id + 1;

        autocomplete_load_id = load_id;

        try {
            const response = await fm_fetch("api_autocomplete_path.php", {
                method: 'POST',
                body: new URLSearchParams({
                    path: input.value.trim()
                })
            });

            if (!response.ok) {
                throw new Error('Autocomplete request failed');
            }

            const res = await response.json();

            if (load_id !== autocomplete_load_id) {
                return;
            }

            init_autocomplete(Array.isArray(res.data) ? res.data : []);

            if (!$form.hasClass('is-hidden')) {
                $input.autocomplete('search', input.value);
            }
        } catch (error) {
            if (load_id !== autocomplete_load_id) {
                return;
            }

            init_autocomplete([]);
        }
    };

    const reopen_autocomplete = (value) => {
        clearTimeout(reopen_timer);

        reopen_timer = setTimeout(() => {
            $input.autocomplete('search', value);
            keep_open_after_select = false;
            reopen_timer = null;
            move_caret_to_end();
        }, 0);
    };

    const toggle_form = () => {
        const is_hidden = $form.hasClass('is-hidden');

        $form.toggleClass('is-hidden', !is_hidden);
        toggle.setAttribute('aria-expanded', is_hidden ? 'true' : 'false');

        if (is_hidden) {
            move_caret_to_end();
        } else {
            clearTimeout(reopen_timer);
            reopen_timer = null;
            keep_open_after_select = false;

            if (has_autocomplete()) {
                $input.autocomplete('close');
            }
        }
    };

    toggle.addEventListener('click', toggle_form);

    toggle.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        event.preventDefault();
        toggle_form();
    });

    $input.on('focus', () => {
        move_caret_to_end();
        load_autocomplete_data();
    });

    $input.on('input', () => {
        const slash_count = count_slashes(input.value);

        if (slash_count === last_slash_count) {
            return;
        }

        last_slash_count = slash_count;
        load_autocomplete_data();
    });

    init_autocomplete([]);
})();
</script>
<?php } ?>
