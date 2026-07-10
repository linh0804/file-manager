(async () => {
    const $input = $('#header-goto-path');
    const $form = $('#header-goto-path-form');
    const $toggle = $('#header-goto-path-toggle');

    if ($input.length === 0 || $form.length === 0 || $toggle.length === 0) {
        return;
    }

    let keep_open_after_select = false;
    let reopen_timer = null;
    let autocomplete_request = null;
    let last_slash_count = ($input.val().match(/\//g) || []).length;

    if ($toggle.attr('data-status') === 'off') {
        $form.removeClass('is-visible');
    }

    const move_cursor_to_end = () => {
        const el = $input[0];

        el.focus();

        try {
            el.setSelectionRange(el.value.length, el.value.length);
        } catch (error) {
        }

        el.scrollLeft = el.scrollWidth;
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
    const get_paths = async (str) => {
        if (autocomplete_request) {
            autocomplete_request.abort();
        }

        autocomplete_request = new AbortController();

        try {
            const response = await fm_fetch("api_autocomplete_path.php", {
                method: 'POST',
                body: new URLSearchParams({ path: str }),
                signal: autocomplete_request.signal
            });
            const res = await response.json();

            return res.data;
        } catch (error) {
            return [];
        }
    };
    const gen_autocomplete = async () => {
        const items = await get_paths($input.val().trim());

        if ($input.data('ui-autocomplete')) {
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
            select: async function (event, ui) {
                event.preventDefault();

                const value = to_full_path(this.value, ui.item.value);

                $(this).val(value);
                move_cursor_to_end();

                if (!String(ui.item.value).endsWith('/')) {
                    keep_open_after_select = false;
                    window.location.href = 'file.php?act=info&path=' + encodeURIComponent(value);
                    return;
                }

                keep_open_after_select = true;

                const slash_count = count_slashes(value);

                if (slash_count !== last_slash_count) {
                    last_slash_count = slash_count;
                    keep_open_after_select = false;
                    await gen_autocomplete();
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

        if ($toggle.attr('data-status') === 'on') {
            $input.autocomplete('search', $input.val());
        }
    };

    const reopen_autocomplete = (value) => {
        clearTimeout(reopen_timer);

        reopen_timer = setTimeout(() => {
            $input.autocomplete('search', value);
            keep_open_after_select = false;
            reopen_timer = null;
            move_cursor_to_end();
        }, 0);
    };

    const toggle_form = async () => {
        const is_off = $toggle.attr('data-status') === 'off';

        if (is_off) {
            $toggle.attr('data-status', 'on');
            $form.addClass('is-visible');
            move_cursor_to_end();
            await gen_autocomplete();
        } else {
            $toggle.attr('data-status', 'off');
            $form.removeClass('is-visible');
            clearTimeout(reopen_timer);
            reopen_timer = null;
            keep_open_after_select = false;

            if ($input.data('ui-autocomplete')) {
                $input.autocomplete('close');
            }
        }
    };

    $toggle.on('click', toggle_form);

    $toggle.on('keydown', async (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        event.preventDefault();
        await toggle_form();
    });

    $input.on('focus', async () => {
        move_cursor_to_end();
        await gen_autocomplete();
    });

    $input.on('input', async () => {
        const slash_count = count_slashes($input.val());

        if (slash_count === last_slash_count) {
            return;
        }

        last_slash_count = slash_count;
        await gen_autocomplete();
    });

    await gen_autocomplete();
})();
