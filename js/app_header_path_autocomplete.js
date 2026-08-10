(() => {
    const $input = $("#header-goto-path");
    const $form = $("#header-goto-path-form");
    const $toggle = $("#header-goto-path-toggle");

    let my_request = null;
    let last_query = "";

    const move_cursor_to_end = () => {
        const el = $input[0];

        el.focus();

        try {
            el.setSelectionRange(el.value.length, el.value.length);
        } catch (error) {}

        el.scrollLeft = el.scrollWidth;
    };

    const escape_html = (value) => $("<div>").text(value).html();
    const escape_regex = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

    const get_dir_base = () => {
        const trimmed = $input.val().trim();

        if (trimmed === "" || trimmed.endsWith("/")) {
            return trimmed;
        }

        const slash_index = trimmed.lastIndexOf("/");

        if (slash_index === -1) {
            return "";
        }

        return trimmed.slice(0, slash_index + 1);
    };

    const get_search_segment = (value) => {
        const trimmed = value.trim();

        if (trimmed.endsWith("/")) {
            return "";
        }

        const slash_index = trimmed.lastIndexOf("/");

        return slash_index === -1 ? trimmed : trimmed.slice(slash_index + 1);
    };

    const gen_autocomplete = () => {
        try {
            my_request.abort();
        } catch (error) {}

        my_request = fm_ajax({ act: "autocomplete_path", path: $input.val().trim() }, function (res) {
            const items = res.data;
    
            if ($input.data("ui-autocomplete")) {
                $input.autocomplete("destroy");
            }
    
            $input.autocomplete({
                source: function (request, response) {
                    const keyword = get_search_segment(request.term).toLowerCase();
                    response(keyword === "" ? items : items.filter((item) => String(item).toLowerCase().includes(keyword)));
                },
                minLength: 1,
                focus: function (event) {
                    event.preventDefault();
                },
                select: function (event, ui) {
                    event.preventDefault();
    
                    const value = get_dir_base() + ui.item.value;
                    $(this).val(value);
    
                    // for file
                    if (!String(ui.item.value).endsWith("/")) {
                        window.location.href = "file_info.php?path=" + encodeURIComponent(value);
                        return;
                    }
    
                    // for dir
                    move_cursor_to_end();
                    gen_autocomplete();
                },
            });
    
            $input.autocomplete("instance")._renderItem = function (ul, item) {
                const term = get_search_segment(this.term || "");
                let label = escape_html(item.label || item.value || "");
    
                if (term !== "") {
                    label = label.replace(new RegExp(escape_regex(term), "i"), '<span class="autocomplete-match">$&</span>');
                }
    
                return $("<li>").append($("<div>").html(label)).appendTo(ul);
            };
    
            if ($toggle.attr("data-status") === "on") {
                $input.autocomplete("search", $input.val());
            }
        });
    };

    $toggle.on("click", () => {
        const is_off = $toggle.attr("data-status") === "off";

        if (is_off) {
            $toggle.attr("data-status", "on");
            $form.addClass("is-visible");
            move_cursor_to_end();
        } else {
            $toggle.attr("data-status", "off");
            $form.removeClass("is-visible");

            if ($input.data("ui-autocomplete")) {
                $input.autocomplete("close");
            }
        }
    });

    $input.on("focus", () => {
        gen_autocomplete();
    });

    $input.on("input", () => {
        const query = get_dir_base();

        if (query === last_query) {
            return;
        }

        last_query = query;
        gen_autocomplete();
    });
})();
