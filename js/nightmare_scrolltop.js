var nightmare_scrolltop = {
    init: function () {
        var scroll_to_top_timeout = null;
        var last_scroll_top = window.scrollY || document.documentElement.scrollTop;

        var button = document.getElementById("nightmare-scrolltop");
        if (!button) return;

        button.style.transform = "rotate(180deg)";

        button.addEventListener("click", function () {
            var scroll = 0;

            if (button.style.transform == "rotate(180deg)") {
                scroll = document.documentElement.scrollHeight;
            }

            window.scroll({
                top: scroll,
                left: 0,
                behavior: "smooth",
            });
        });

        window.addEventListener("scroll", function () {
            const scroll_top_position = window.scrollY || document.documentElement.scrollTop;

            if (button.style.display == "none") {
                button.style.display = "block";
            }

            if (scroll_top_position > last_scroll_top) {
                button.style.transform = "rotate(180deg)";
            } else if (scroll_top_position < last_scroll_top) {
                button.style.transform = "rotate(0deg)";
            }

            last_scroll_top = scroll_top_position <= 0 ? 0 : scroll_top_position;

            clearTimeout(scroll_to_top_timeout);
            scroll_to_top_timeout = setTimeout(() => {
                button.style.display = "none";
            }, 3000);
        });
    },
};
