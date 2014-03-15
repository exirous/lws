<?php
/* @var $this AdminController */
/* @var $categories Category[] */
/* @var $addTables AddTableConfig[] */

?>
<script src="/scripts/jquery-ui.js"></script>
<script src="/scripts/jquery.nestedsortable.js"></script>
<script src="/scripts/slugify.js"></script>
<h2 class="orange_head"><a href="/admin/">Admin</a> -> Categories <a href="">[refresh page]</a></h2>
<div class="cushion_box">
<a href="" id="adminAddNewCat">Add new Category</a>
<div style="white-space: nowrap;">
    <ol class="nestedSortable adminCategories">
        <?foreach ($categories as $category): ?>
            <li data-id="<?= $category->id ?>">
                <div>
                    <a href="" class="delete" title="Delete">X</a>
                    <a href="" class="edit" title="Edit">E</a>
                    <a href="" class="add" title="Add New Subcategory">+</a>
                    <span><?=$category->title?><?=$category->add_table_id ? ' [' . $category->addTable->title . ']' : ''?></span>
                </div>
                <? if (count($category->categories(array('order' => 'id asc')))): ?>
                    <ol>
                        <? foreach ($category->categories(array('order' => '`order` asc')) as $subcategory): ?>
                            <li data-id="<?= $subcategory->id ?>">
                                <div>
                                    <a href="" class="delete" title="Delete">X</a>
                                    <a href="" class="edit" title="Edit">E</a>
                                    <a href="" class="add" title="Add New Subcategory">+</a>
                                    <span><?=$subcategory->title?><?=$subcategory->add_table_id ? ' [' . $subcategory->addTable->title . ']' : ''?></span>
                                </div>
                            </li>
                        <? endforeach; ?>
                    </ol>
                <? endif; ?>
            </li>
        <? endforeach;?>
    </ol>
    <form class="adminForm adminCategoryEdit" style="">
        <input type="hidden" id="category_id" disabled>

        <div class="row">
            <label>Category title</label>
            <input type="text" id="category_title" disabled>
        </div>
        <div class="row">
            <label>Category name (slug)</label>
            <input type="text" id="category_name" disabled>
        </div>
        <div class="row">
            <label>Category AddTable</label>
            <select id="category_add_table" disabled>
                <option value=""></option>
                <?foreach ($addTables as $addTable): ?>
                    <option value="<?= $addTable->id ?>"><?=$addTable->title?></option>
                <? endforeach;?>
            </select>
        </div>
        <div class="row buttonPanel">
            <input type="submit" name="submit" value="Save" disabled>
        </div>
    </form>
</div>
</div>
<script id="catItemTemplate" type="text/x-jquery-tmpl">
    <li class="disabled mjs-nestedSortable-no-nesting">
        <div>
            <a href="" class="delete" title="Delete">X</a>
            <a href="" class="edit" title="Edit">E</a>
            <a href="" class="add" title="Add New Subcategory">+</a>
            <span>Empty Category</span>
        </div>
    </li>
</script>
<script>
$(function ()
{
    $('.nestedSortable').nestedSortable({
        handle: 'div',
        helper: 'clone',
        items: 'li',
        opacity: .6,
        placeholder: 'placeholder',
        revert: 250,
        tabSize: 25,
        tolerance: 'pointer',
        toleranceElement: '> div',
        maxLevels: 2,
        isTree: true,
        stop: function (event, ui)
        {
            saveCategoryOrder(ui.item.get(0));
        }
    });

    $('.adminCategories').on('click', 'li .edit',function ()
    {
        var oldId = $('#category_id').val();
        clearCatForm('disable');
        var $catItem = $(this).parents('li').first();
        if (!$catItem.data('id') || ($catItem.data('id') == oldId))
            return false;
        var url = '/admin/getCategoryInfo/';
        var data = {
            'id': $catItem.data('id')
        };
        $catItem.addClass('editing');

        $.ajax(url,
            {
                type: 'get',
                dataType: 'json',
                data: data,
                success: function (response)
                {
                    if (response.result == 'ok')
                    {
                        $('#category_name').val(response.data.name);
                        $('#category_title').val(response.data.title);
                        $('#category_id').val(response.data.id);
                        $('#category_add_table').val(response.data.add_table);
                        clearCatForm('enable');
                    }
                    else
                    {
                        alert('Error getting category');
                    }
                },
                error: function ()
                {
                    alert('Error getting category');
                }
            });
        return false;


        return false;
    }).on('click', 'li .delete',function ()
        {
            if (!confirm('Are you sure you want to delete this cateogry?'))
                return false;
            clearCatForm('disable');
            var $catItem = $(this).parents('li').first();
            if (!$catItem.data('id'))
            {
                return false;
            }
            var url = '/admin/delCategory/';
            var data = {
                'id': $catItem.data('id')
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
                            $catItem.data('id', null).removeAttr('data-id').slideUp(200, function ()
                            {
                                $(this).remove();
                            });
                        }
                        else
                        {
                            alert('Error deleting category');
                        }
                    },
                    error: function ()
                    {
                        alert('Error deleting category');
                    }
                });

            return false;

        }).on('click', 'li .add', function ()
        {
            clearCatForm('disable');
            var $catItem = $(this).parents('li').first();
            if (!$catItem.data('id'))
                return false;
            var $subcatList = $catItem.children('ol')
            if (!$subcatList.length)
                $subcatList = $('<ol></ol>').appendTo($catItem);
            $subcatList.append($('#catItemTemplate').tmpl().css('display', 'none'));
            $subcatList.children(':last-child').slideDown(300);
            $('#adminSaveCatOrder').addClass('disabled');
            clearCatForm('enable');
            return false;
        });
    $('.adminForm').on('submit', function ()
    {
        var parent_id = null;
        var id = $('#category_id').val();
        var $cat = null;
        if (!id)
        {
            id = null;
            $cat = $('.adminCategories').find('li:not([data-id])').first();
        }
        else
        {
            $cat = $('.adminCategories').find('li[data-id=' + id + ']').first();
        }

        parent_id = $cat.parents('li').first().data('id');
        if (!parent_id)
            parent_id = null;

        var url = '/admin/saveCategory/';
        var data = {
            'id': id,
            'name': $('#category_name').val(),
            'title': $('#category_title').val(),
            'add_table': $('#category_add_table').val(),
            'category_id': parent_id
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
                        $cat.removeClass('disabled').removeClass('mjs-nestedSortable-no-nesting')
                            .data('id', response.data.id)
                            .attr('data-id', response.data.id)
                            .find('span').first().text(response.data.title);
                        clearCatForm('disable');
                        if (!id)
                            saveCategoryOrder();
                    }
                    else
                    {
                        alert('Error saving category');
                        clearCatForm('disable');
                    }
                },
                error: function ()
                {
                    alert('Error saving category');
                    clearCatForm('disable');
                }
            });


        return false;
    });


    /*$('#adminSaveCatOrder').on('click', function ()
     {
     if ($(this).hasClass('disabled'))
     return false;
     saveCategoryOrder();

     return false;
     });*/

    $('#adminAddNewCat').on('click', function ()
    {
        clearCatForm('disable');
        var $subcatList = $('.adminCategories').first();
        $subcatList.append($('#catItemTemplate').tmpl().css('display', 'none'));
        $subcatList.children(':last-child').slideDown(300);
        $('#adminSaveCatOrder').addClass('disabled');
        clearCatForm('enable');
        return false;
    });

    $("#category_title").keyup(function ()
    {
        $("#category_name").val(url_slug($(this).val()));
    });

    $("#category_name").on('blur', function ()
    {
        $(this).val(url_slug($(this).val()));
    })

    function saveCategoryOrder(item)
    {
        var catIds = []
        $('.adminCategories').find('li[data-id]').each(function ()
        {
            catIds.push($(this).data('id'));
        });

        var url = '/admin/saveCategoryOrder/';
        var data = {
            'order': catIds.join(',')
        }
        if (item && $(item).data('id'))
        {
            data.cat_id = $(item).data('id');
            data.cat_parent = $(item).parents('li').first().data('id');
        }

        $('#adminSaveCatOrder').addClass('disabled');
        $.ajax(url,
            {
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (response)
                {
                    if (response.result == 'ok')
                    {
                        if (!$('.adminCategories').find('li:not([data-id])').length)
                            $('#adminSaveCatOrder').removeClass('disabled')
                    }
                    else
                    {
                        alert('Error saving category order');
                        $('#adminSaveCatOrder').removeClass('disabled')
                    }
                },
                error: function ()
                {
                    alert('Error saving category order');
                    $('#adminSaveCatOrder').removeClass('disabled')
                }
            });
    }

    function clearCatForm(disable)
    {
        if (disable == 'disable')
        {
            $('.adminCategoryEdit').find('input, select').attr('disabled', true).end().get(0).reset();
            $('.adminCategories').find('li:not([data-id])').slideUp(200,function ()
            {
                $(this).remove();
            }).end().find('li').removeClass('editing');
            $('#adminSaveCatOrder').removeClass('disabled');
            $('#category_id').val('');
            $('#category_add_table').val('');
            $('#category_title').blur();
            $('#category_name').blur();
        }
        else
        {
            $('.adminCategoryEdit').find('input, select').attr('disabled', false);
            $('#category_title').focus();
        }
    }

})
;
</script>
