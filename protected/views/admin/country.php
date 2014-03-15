<?php
/* @var $this AdminController */
/* @var $country Country */
?>
<script src="/scripts/jquery-ui.js"></script>
<script src='/scripts/slugify.js'></script>
<h2 class="orange_head"><a href="/admin/">Admin</a> -> <a href="/admin/countries/">Countries</a> ->
    <?if ($country->id): ?>
        (ID: <?= $country->id ?>) <?= $country->name ?>
    <? else: ?>
        Add new Country
    <?endif;?>
    <a href="">[refresh page]</a></h2>
<div class="cushion_box">
<form class="adminForm" id="countryForm" action="" method="POST">
    <?if ($message): ?>
        <div class="message" style="background: <?= $message[1] ?>;">
            <?=$message[0]?>
        </div>
    <? endif;?>
    <div class="row">
        <label>Name</label>
        <input type="text" name="name" value="<?= $country->name ?>">
    </div>
    <div class="row buttonPanel">
        <input type="submit" name="submit" value="Save">
    </div>
</form>
<? if ($country->id): ?>
    <a href="" id="adminAddNewCat">Add new City</a>
    <div style="white-space: nowrap;" class="adminExtras">
        <ol class="nestedSortable adminCategories addFields">
            <?foreach ($country->cities(array('order' => '`order` asc')) as $field): ?>
                <li data-id="<?= $field->id ?>">
                    <div>
                        <a href="" class="delete" title="Delete">X</a>
                        <a href="" class="edit" title="Edit">E</a>
                        <span><?=$field->name?></span>
                    </div>
                </li>
            <? endforeach;?>
        </ol>
        <form class="adminForm adminCategoryEdit" id="cityForm" style="">
            <input type="hidden" id="city_id" disabled>

            <div class="row">
                <label>City Name</label>
                <input type="text" id="city_name" disabled>
            </div>
            <div class="row buttonPanel">
                <input type="submit" name="submit" value="Save" disabled>
            </div>
        </form>
    </div>
</div>
<? endif; ?>
<script id="cityItemTemplate" type="text/x-jquery-tmpl">
    <li class="disabled mjs-nestedSortable-no-nesting">
        <div>
            <a href="" class="delete" title="Delete">X</a>
            <a href="" class="edit" title="Edit">E</a>
            <span>New City</span>
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
            saveCityOrder();
        }
    });


    $('.adminCategories.addFields').on('click', 'li .edit',function ()
    {
        var oldId = $('#city_id').val();
        clearCityForm('disable');
        var $cityItem = $(this).parents('li').first();
        if (!$cityItem.data('id') || ($cityItem.data('id') == oldId))
            return false;
        var url = '/admin/getCityInfo/';
        var data = {
            'id': $cityItem.data('id')
        };
        $cityItem.addClass('editing');

        $.ajax(url,
            {
                type: 'get',
                dataType: 'json',
                data: data,
                success: function (response)
                {
                    if (response.result == 'ok')
                    {
                        $('#city_name').val(response.data.name);
                        $('#city_id').val(response.data.id);
                        clearCityForm('enable');
                    }
                    else
                    {
                        alert('Error getting City!');
                    }
                },
                error: function ()
                {
                    alert('Error getting City!');
                }
            });
        return false;


        return false;
    }).on('click', 'li .delete', function ()
        {
            if (!confirm('Are you sure you want to delete this field?'))
                return false;
            clearCityForm('disable');
            var $cityItem = $(this).parents('li').first();
            if (!$cityItem.data('id'))
            {
                return false;
            }
            var url = '/admin/delCity/';
            var data = {
                'id': $cityItem.data('id')
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
                            $cityItem.data('id', null).removeAttr('data-id').slideUp(200, function ()
                            {
                                $(this).remove();
                            });
                        }
                        else
                        {
                            alert('Error deleting City');
                        }
                    },
                    error: function ()
                    {
                        alert('Error deleting City');
                    }
                });

            return false;

        });


    $('#cityForm').on('submit', function ()
    {
        var id = $('#city_id').val();
        var $city = null;
        if (!id)
        {
            id = null;
            $city = $('.adminCategories.addFields').find('li:not([data-id])').first();
        }
        else
        {
            $city = $('.adminCategories.addFields').find('li[data-id=' + id + ']').first();
        }


        var url = '/admin/saveCity/<?=$country->id?>';
        var data = {
            'id': id,
            'name': $('#city_name').val()
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
                        $city.removeClass('disabled').removeClass('mjs-nestedSortable-no-nesting')
                            .data('id', response.data.id)
                            .attr('data-id', response.data.id)
                            .find('span').first().text(response.data.title);
                        clearCityForm('disable');
                        if (!id)
                            saveCityOrder();
                    }
                    else
                    {
                        alert('Error saving City');
                        clearCityForm('disable');
                    }
                },
                error: function ()
                {
                    alert('Error saving City');
                    clearCityForm('disable');
                }
            });


        return false;
    });


    $('#adminAddNewCat').on('click', function ()
    {
        clearCityForm('disable');
        var $cityList = $('.adminCategories.addFields').first();
        $cityList.append($('#cityItemTemplate').tmpl().css('display', 'none'));
        $cityList.children(':last-child').slideDown(300);
        clearCityForm('enable');
        return false;
    });


    function saveCityOrder(item)
    {
        var cityIds = []
        $('.adminCategories.addFields').find('li[data-id]').each(function ()
        {
            cityIds.push($(this).data('id'));
        });

        var url = '/admin/saveCityOrder/';
        var data = {
            'order': cityIds.join(',')
        }

        if (item && $(item).data('id'))
        {
            data.city_id = $(item).data('id');
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
                        alert('Error saving City order');
                    }
                },
                error: function ()
                {
                    alert('Error saving City order');
                }
            });
    }


    function clearCityForm(disable)
    {
        if (disable == 'disable')
        {
            $('.adminCategoryEdit').find('input, select').attr('disabled', true).end().get(0).reset();
            $('.adminCategories.addFields').find('li:not([data-id])').slideUp(200,function ()
            {
                $(this).remove();
            }).end().find('li').removeClass('editing');
            $('#city_id').val('');
            $('#city_name').val('');
            $('#cityForm').blur();
        }
        else
        {
            $('.adminCategoryEdit').find('input, select').attr('disabled', false);
            $('#city_name').focus();
        }
    }
});


</script>
