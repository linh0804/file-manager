<?php

namespace app;

define('ACCESS', true);

require '_init.php';

$title = 'Checkhealth';
?>

<?php require '_header.php' ?>

<style>
    table, th, td {
        width: 100%;
  border: 1px solid black;
  border-collapse: collapse;
  padding: 8px;
    }
</style>

<div class="title"><?= $title ?></div>

<div class="list">
    <table>
        <tr>
            <td>PHP</td>
            <td><?= phpversion() ?></td>
        </td>
        <tr>
            <td>Composer</td>
            <td><?php var_export(file_exists('composer.phar')) ?></td>
        </td>
        <tr>
            <td>Prettier</td>
            <td><?php var_export((bool) shell_exec("command -v prettier")) ?></td>
        </td>
    </table>
</div>

<?php require '_footer.php' ?>