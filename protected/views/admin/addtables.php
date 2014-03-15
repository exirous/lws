<?php
/* @var $this AdminController */
/* @var $addTables AddTableConfig[] */
?>

        <h2 class="orange_head"><a href="/admin/">Admin</a> -> AddTables <a href="">[refresh page]</a></h2>
<div class="cushion_box">
        <table class="adminTable">
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Linked Categories</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="3"></td>
                <td><a href="/admin/addTable/0">[Add new]</a></td>
            </tr>
            <?foreach ($addTables as $addTable): ?>
                <tr>
                    <td><?=$addTable->id?></td>
                    <td><?=$addTable->title?></td>
                    <td><?=count($addTable->categories)?></td>
                    <td>
                        <a href="/admin/addTable/<?= $addTable->id ?>">[Edit]</a>
                        <a href="#"
                           onclick="if (confirm('Вы действительно хотите УДАЛИТЬ <?= $addTable->title ?>?')) document.location.href='/admin/delAddTable/<?= $addTable->id ?>'">[Delete]</a>
                    </td>
                </tr>
            <? endforeach;?>
            <tr>
                <td colspan="3"></td>
                <td><a href="/admin/addTable/0">[Add new]</a></td>
            </tr>
            </tbody>
        </table>
    </div>
