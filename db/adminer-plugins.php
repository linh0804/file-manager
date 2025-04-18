<?php

return [
    new AdminerDatabaseHide(["mysql", "sys", "information_schema", "performance_schema"]),
    //new AdminerSimpleMenu(),
    new AdminerCollations(["utf8mb4_general_ci",  "utf8mb4_vietnamese_ci", "utf8mb4_uca1400_ai_ci"]),
];