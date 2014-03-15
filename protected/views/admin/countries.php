<?php
/* @var $this AdminController */
/* @var $countries Country[] */
?>

        <h2 class="orange_head"><a href="/admin/">Admin</a> -> Countries <a href="">[refresh page]</a></h2>
        <div class="cushion_box">
        <table class="adminTable">
            <thead>
            <tr>
                <th>ID</th>
                <th>Country Name</th>
                <th>Cities</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="3"></td>
                <td><a href="/admin/country/0">Add new</a></td>
            </tr>
            <?foreach ($countries as $country): ?>
                <tr>
                    <td><?=$country->id?></td>
                    <td><?=$country->name?></td>
                    <td><?=count($country->cities)?></td>
                    <td>
                        <a href="/admin/country/<?= $country->id ?>">Edit</a>
                        <a href="#"
                           onclick="if (confirm('Вы действительно хотите УДАЛИТЬ <?= $country->name ?>?')) document.location.href='/admin/delCountry/<?= $country->id ?>'">Delete</a>
                    </td>
                </tr>
            <? endforeach;?>
            <tr>
                <td colspan="3"></td>
                <td><a href="/admin/country/0">Add new</a></td>
            </tr>
            </tbody>
        </table>
    </div>