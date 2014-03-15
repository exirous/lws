<?php
/* @var $this AdminController */
/* @var $addTable AddTableConfig */
?>
<script src="/scripts/jquery-ui.js"></script>
<script src='/scripts/slugify.js'></script>
<h2 class="orange_head"><a href="/admin/">Admin</a> -> <a href="/admin/addTables/">AddTables</a> ->
    <?if ($addTable->id): ?>
        (ID: <?= $addTable->id ?>) <?= $addTable->title ?>
    <? else: ?>
        Add new AddTable
    <?endif;?>
    <a href="">[refresh page]</a></h2>
<div class="cushion_box">
<form class="adminForm" id="addTableForm" action="" method="POST">
    <?if ($message): ?>
        <div class="message" style="background: <?= $message[1] ?>;">
            <?=$message[0]?>
        </div>
    <? endif;?>
    <div class="row">
        <label>Name</label>
        <input type="text" name="title" value="<?= $addTable->title ?>">
    </div>
    <div class="row buttonPanel">
        <input type="submit" name="submit" value="Save">
    </div>
</form>
<? if ($addTable->id): ?>
    <a href="" id="adminAddNewCat">Add new Field</a>
    <div style="white-space: nowrap;" class="adminExtras">
        <ol class="nestedSortable adminCategories addFields">
            <?foreach ($addTable->addTableFields(array('order' => '`order` asc')) as $field): ?>
                <li data-id="<?= $field->id ?>">
                    <div>
                        <a href="" class="delete" title="Delete">X</a>
                        <a href="" class="edit" title="Edit">E</a>
                        <span><?=$field->title?> [<?=$field->type?>
                            ] <?=$field->visible == 'yes' ? 'visible' : 'hidden'?></span>
                    </div>
                </li>
            <? endforeach;?>
        </ol>
        <form class="adminForm adminCategoryEdit" id="addTableFieldForm" style="">
            <input type="hidden" id="addTableField_id" disabled>

            <div class="row">
                <label>Field Title</label>
                <input type="text" id="addTableField_title" disabled><br>
                <input type="text" id="addTableField_name" disabled readonly="readonly">
            </div>
            <div class="row">
                <label for="addTableField_searchable" style="display: inline;vertical-align: middle">Searchable</label>
                <input type="checkbox" name="addTableField_searchable" id="addTableField_searchable"
                       style="vertical-align: middle" disabled>
                <label for="addTableField_visible"
                       style="display: inline;vertical-align: middle;margin-left:20px;">Visible</label>
                <input type="checkbox" name="addTableField_visible" id="addTableField_visible"
                       style="vertical-align: middle" disabled>
            </div>
            <div class="row">
                <label>Field Type</label>
                <select name="addTableField_type" id="addTableField_type" disabled>
                    <option value=""></option>
                    <?foreach (array('brand' => 'Brand/Make', 'model' => 'Make Model', 'varchar' => 'Text', 'enum' => 'Value chooser', 'set' => 'Set of values', 'int' => 'Integer', 'date' => 'Date', 'checkbox' => 'Tick box (check box)', 'range' => 'Range of Integer values') as $type => $title): ?>
                        <option value="<?= $type ?>"><?=$title?></option>
                    <? endforeach;?>
                </select>
            </div>
            <div class="row" id="addTableField_value_row" style="display: none;">
                <a href="" id="addAddTableFieldValue" class="adminAddNewBtn">Add new Value</a><br>
                <ol class="nestedSortable adminCategories" id="addTableField_values" style="width:100%;">
                </ol>
                <div class="row buttonPanel">
                    <input type="submit" name="submit" value="Save" disabled>
                </div>
            </div>
        </form>
    </div>
</div>
<? endif; ?>
<script id="addTableFieldItemTemplate" type="text/x-jquery-tmpl">
    <li class="disabled mjs-nestedSortable-no-nesting">
        <div>
            <a href="" class="delete" title="Delete">X</a>
            <a href="" class="edit" title="Edit">E</a>
            <span>Empty Field</span>
        </div>
    </li>
</script>
<script id="addTableFieldValueItemTemplate" type="text/x-jquery-tmpl">
    <li class="disabled mjs-nestedSortable-no-nesting">
        <div>
            <a href="" class="delete" title="Delete">X</a>
            <input type="text" name="values[]" value="${value}">
        </div>
    </li>
</script>


<script>
$(function ()
{
    $('.adminTabs a').on('click', function ()
    {
        var $this = $(this);
        $this.parent().children().removeClass('active');
        $this.addClass('active');
        $('.tabContent').hide();
        $($this.attr('href')).show();
        return false;
    })


    $('.adminCategories.addFields').sortable({
        handle: 'div',
        helper: 'clone',
        items: 'li',
        opacity: .6,
        placeholder: 'placeholder',
        revert: 250,
        tolerance: 'pointer',
        toleranceElement: '> div',
        maxLevels: 1,
        isTree: false,
        stop: function (event, ui)
        {
            saveAddTableFieldOrder();
        }
    });

    $('#addTableField_values').sortable({
        handle: 'div',
        helper: 'clone',
        items: 'li',
        opacity: .6,
        placeholder: 'placeholder',
        revert: 250,
        tolerance: 'pointer',
        toleranceElement: '> div',
        maxLevels: 1,
        isTree: false,
        stop: function (event, ui)
        {
        }
    });


    $('#addTableField_type').on('change', function ()
    {
        if ($(this).val() == 'set' || $(this).val() == 'enum')
            $('#addTableField_value_row').slideDown('100')
        else
            $('#addTableField_value_row').slideUp('100');
    });


    $('.adminCategories.addFields').on('click', 'li .edit',function ()
    {
        var oldId = $('#addTableField_id').val();
        clearAddTableFieldForm('disable');
        var $addTableFieldItem = $(this).parents('li').first();
        if (!$addTableFieldItem.data('id') || ($addTableFieldItem.data('id') == oldId))
            return false;
        var url = '/admin/getAddTableFieldInfo/';
        var data = {
            'id': $addTableFieldItem.data('id')
        };
        $addTableFieldItem.addClass('editing');

        $.ajax(url,
            {
                type: 'get',
                dataType: 'json',
                data: data,
                success: function (response)
                {
                    if (response.result == 'ok')
                    {
                        $('#addTableField_name').val(response.data.name);
                        $('#addTableField_title').val(response.data.title);
                        $('#addTableField_id').val(response.data.id);
                        $('#addTableField_type').val(response.data.type);
                        if (response.data.type == 'set' || response.data.type == 'enum')
                        {
                            var container = $('#addTableField_values');
                            for (var i in response.data.values)
                            {
                                $('#addTableFieldValueItemTemplate').tmpl({value: response.data.values[i]}).appendTo(container);
                            }
                            $('#addTableField_value_row').slideDown('100');
                        }
                        $('#addTableField_visible').get(0).checked = (response.data.visible == 'yes')
                        $('#addTableField_searchable').get(0).checked = (response.data.searchable == 'yes')

                        clearAddTableFieldForm('enable');
                    }
                    else
                    {
                        alert('Error getting AddTableField!');
                    }
                },
                error: function ()
                {
                    alert('Error getting AddTableField!');
                }
            });
        return false;


        return false;
    }).on('click', 'li .delete', function ()
        {
            if (!confirm('Are you sure you want to delete this field?'))
                return false;
            clearAddTableFieldForm('disable');
            var $addTableFieldItem = $(this).parents('li').first();
            if (!$addTableFieldItem.data('id'))
            {
                return false;
            }
            var url = '/admin/delAddTableField/';
            var data = {
                'id': $addTableFieldItem.data('id')
            };

            $.ajax(url,
                {
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (response)
                    {
                        if (response.result == 'ok')
                        {
                            $addTableFieldItem.data('id', null).removeAttr('data-id').slideUp(200, function ()
                            {
                                $(this).remove();
                            });
                        }
                        else
                        {
                            alert('Error deleting AddTableField');
                        }
                    },
                    error: function ()
                    {
                        alert('Error deleting AddTableField');
                    }
                });

            return false;

        });


    $('#addTableField_values').on('click', 'li .delete', function ()
    {
        if (!confirm('Are you sure you want to delete this field Value?'))
            return false;
        $(this).parents('li').first().slideUp(100, function ()
        {
            $(this).remove();
        })
        return false;
    });

    $('#addTableFieldForm').on('submit', function ()
    {
        var id = $('#addTableField_id').val();
        var $addTableField = null;
        if (!id)
        {
            id = null;
            $addTableField = $('.adminCategories.addFields').find('li:not([data-id])').first();
        }
        else
        {
            $addTableField = $('.adminCategories.addFields').find('li[data-id=' + id + ']').first();
        }

        var values = [];

        $('#addTableField_values').find('input').each(function ()
        {
            if ($(this).val())
                values.push($(this).val());
        })


        var url = '/admin/saveAddTableField/<?=$addTable->id?>';
        var data = {
            'id': id,
            'title': $('#addTableField_title').val(),
            'name': $('#addTableField_name').val(),
            'type': $('#addTableField_type').val(),
            'visible': $('#addTableField_visible').is(':checked') ? 'yes' : 'no',
            'searchable': $('#addTableField_searchable').is(':checked') ? 'yes' : 'no',
            'values': values
        };
        $('.adminCategoryEdit').find('input').attr('disabled', true)
        $.ajax(url,
            {
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (response)
                {
                    if (response.result == 'ok')
                    {
                        $addTableField.removeClass('disabled').removeClass('mjs-nestedSortable-no-nesting')
                            .data('id', response.data.id)
                            .attr('data-id', response.data.id)
                            .find('span').first().text(response.data.title);
                        clearAddTableFieldForm('disable');
                        if (!id)
                            saveAddTableFieldOrder();
                    }
                    else
                    {
                        alert('Error saving AddTableField');
                        clearAddTableFieldForm('disable');
                    }
                },
                error: function ()
                {
                    alert('Error saving AddTableField');
                    clearAddTableFieldForm('disable');
                }
            });


        return false;
    });


    $('#adminAddNewCat').on('click', function ()
    {
        clearAddTableFieldForm('disable');
        var $addTableFieldList = $('.adminCategories.addFields').first();
        $addTableFieldList.append($('#addTableFieldItemTemplate').tmpl().css('display', 'none'));
        $addTableFieldList.children(':last-child').slideDown(300);
        clearAddTableFieldForm('enable');
        return false;
    });

    $('#addAddTableFieldValue').on('click', function ()
    {
        var $addTableFieldList = $('#addTableField_values').first();
        $addTableFieldList.append($('#addTableFieldValueItemTemplate').tmpl({value: ''}).css('display', 'none'));
        $addTableFieldList.children(':last-child').slideDown(100).find('input').focus();

        return false;
    });


    $('#addTableField_title').keyup(function ()
    {
        $('#addTableField_name').val(url_slug($(this).val(), {delimiter: '_'}));
    });

    $('#addTableField_name').on('blur', function ()
    {
        $(this).val(url_slug($(this).val(), {delimiter: '_'}));
    })


    function saveAddTableFieldOrder(item)
    {
        var addTableFieldIds = []
        $('.adminCategories.addFields').find('li[data-id]').each(function ()
        {
            addTableFieldIds.push($(this).data('id'));
        });

        var url = '/admin/saveAddTableFieldOrder/';
        var data = {
            'order': addTableFieldIds.join(',')
        }

        if (item && $(item).data('id'))
        {
            data.addTableField_id = $(item).data('id');
        }

        $.ajax(url,
            {
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (response)
                {
                    if (response.result == 'ok')
                    {
                    }
                    else
                    {
                        alert('Error saving AddTableField order');
                    }
                },
                error: function ()
                {
                    alert('Error saving AddTableField order');
                }
            });
    }


    function clearAddTableFieldForm(disable)
    {
        if (disable == 'disable')
        {
            $('.adminCategoryEdit').find('input, select').attr('disabled', true).end().get(0).reset();
            $('.adminCategories.addFields').find('li:not([data-id])').slideUp(200,function ()
            {
                $(this).remove();
            }).end().find('li').removeClass('editing');
            $('#addTableField_values').html('');
            $('#addTableField_value_row').slideUp('100');
            $('#addTableField_id').val('');
            $('#addTableField_name').val('');
            $('#addTableField_title').val('');
            $('#addTableField_type').val('');
            $('#addTableField_searchable').get(0).checked = false;
            $('#addTableField_visible').get(0).checked = false;
            $('#addTableFieldForm').blur();
        }
        else
        {
            $('.adminCategoryEdit').find('input, select').attr('disabled', false);
            $('#addTableField_title').focus();
        }
    }
});


</script>
