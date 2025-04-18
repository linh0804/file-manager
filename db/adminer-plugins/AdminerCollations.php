<?php

/**
 * Custom character sets in collation select boxes.
 *
 * @link https://github.com/pematon/adminer-plugins
 *
 * @author Peter Knut
 * @copyright 2015-2018 Pematon, s.r.o. (http://www.pematon.com/)
 */
class AdminerCollations
{
    /** @var array */
    private $characterSets;

    /**
     * @param array $characterSets Array of allowed character sets.
     */
    public function __construct(array $characterSets = ["utf8mb4_general_ci"])
    {
        $this->characterSets = $characterSets;
    }

    /**
     * Prints HTML code inside <head>.
     */
    public function head()
    {
        if (empty($this->characterSets)) {
            return;
        }

        ?>

        <script <?php echo Adminer\nonce(); ?>>
            (function(document) {
                "use strict";

                const characterSets = [
                    <?php
                        $data = [];
                        foreach ($this->characterSets as $characterSet) {
                            $data[] = "'" . $characterSet . "'";
                        }
                        echo implode(',', $data);
                    ?>
                ];

                document.addEventListener("DOMContentLoaded", function () {
                    var selects = document.querySelector("datalist[id='collations']");
                    var html = "";

                    for (var i = 0; i < characterSets.length; i++) {
                        html += '<option>' + characterSets[i];
                    }

                    if (selects) {
                        selects.innerHTML = html;
                    }
                }, false);
            })(document);
        </script>

        <?php
    }
}
