<?php
/* @var $this UserController */
/* @var $property Property */
?>
<div class="glass_wrapper">
    <div class="rounded">
        <h2 class="orange_head"><?=$property->name?>(<?=$property->name_en?>)</h2>
        <h3><?=$property->city->name?>, Болгария</h3>
        <h4 class="propertySKU">ID: <?=$property->view_id?></h4>
        <h5>Цена от: <?=$property->CheapestApartment?> Eur</h5>
        <div class="ProductThumbs" style="text-align:center;">
            <!--<img style="width:640px;" src="/img/property/<?=$property->main_img?>.jpg"
                 alt="<?=$property->name?> <?=$property->city->name?> Болгария, недвижимость в Болгарии от застройщика"
                 title="<?=$property->name?> <?=$property->city->name?> Болгария, недвижимость в Болгарии от застройщика">-->

            <div class="gallery">
                <script type="text/javascript" src="/scripts/jquery.easing.1.3.js"></script>
                <script type="text/javascript" src="/scripts/jquery.timers-1.2.js"></script>
                <script type="text/javascript" src="/scripts/jquery.galleryview-3.0-dev.js"></script>
                <script type="text/javascript">
                    //<![CDATA[
                    $(function ()
                    {
                        $('#gallery').galleryView({
                            transition_speed:500,
                            panel_width: 528,
                            enable_overlays: true,
                            frame_width: 108,
                            frame_height: 70
                        });
                    })
                </script>
                <ul id="gallery">
                    <?foreach ($property->propertyImages(array('order'=>'`order` asc')) as $image): ?>
                    <li>
                        <img src="/img/property/<?=$image->src?>.jpg"
                             data-description="<?=$property->name?> <?=$property->city->name?> Болгария, недвижимость в Болгарии от застройщика" title="<?=$property->name?>">
                    </li>
                    <? endforeach;?>
                </ul>
            </div>
        </div>
</div>
                <div class="rounded description">
                    <h2 class="orange_head">Описание Комплекса</h2><br>
                    <?=$property->description;?>
                    Цена апартаментов в жилом комплексе <?=$property->name?>(<?=$property->name_en?>)
                    <?=$property->city->name?>, Болгария от <?=round($property->CheapestAreaPrice,0)?> Eur/m<sup>2</sup>
                    <table>
                        <?foreach ($property->cheapestApartmentsByRoomCount as $cheapestApartment): ?>
                            <tr>
                                <td><? if ($cheapestApartment->room_count==1)
                                        echo 'Студия';
                                    elseif ($cheapestApartment->room_count==2) echo 'Апартамент с 1 спальней';
                                    else echo 'Апартамент с ' . ($cheapestApartment->room_count -1) . ' спальнями';
                                    ?></td><td>от <?=$cheapestApartment->minPriceByRoomCount?> Eur</td>
                            </tr>
                        <?endforeach;?>
                    </table>


                    <p>
                    </p>
                </div>

                <div class="rounded payment">
                    <h2 class="orange_head">Схемы оплаты</h2><br>
                      Резервационная сумма: <?=$property->reservation?> Eur;<br>
                    <?foreach (array('hypothec' => 'Ипотека', 'installment' => 'В рассрочку') as $key => $val): ?>
                         <?if (in_array($key, explode(',', $property->payment))): ?>
                         <li><?=$val?></li>
                         <? endif;?>
                    <? endforeach;?>
                    <?=$property->payment_info;?>
                </div>

                <div class="rounded additionalInfo">
                    <h2 class="orange_head">Дополнительная информация</h2><br>
                    <? if($property->maintenance):?> Обслуживание комплекса: <?=number_format(round($property->maintenance/100,2),2);?> Eur/м<sup>2</sup><br>
                    <?endif;?>
                    <?=$property->other_info;?>
                    Нотариальное оформлении апартамента 4.5%.
                </div>

                <div class="rounded extras">
                    <h2 class="orange_head">Инфраструктура комплекса</h2><br>
                      <?foreach ($property->extras as $extra): ?>
                    <li>
                        <?=$extra->name_ru;?>
                    </li>
                    <? endforeach;?>
                </div>

                <div class="rounded apartments">
                    <h2 class="orange_head">Апартаменты комплекса</h2><br>
                    <table class="appartmentList">
                        <tr>
                            <th>Номер</th>  <th>Количество<br>комнат</th> <th>Этаж</th> <th>Общая<br>площадь</th> <th>Жилая<br>площадь</th> <th>Идеальные<br>части</th> <th>Короткое<br>описание</th> <th>Цена за м<sup>2</sup></th> <th>Цена</th> <th>Вид</th>
                        </tr>
                      <?foreach ($property->apartments as $apartment): ?>
                        <tr>
                            <td><?=$apartment->name_ru;?></td>
                            <td><?=$apartment->room_count;?></td>
                            <td><?=$apartment->floor;?></td>
                            <td><?=($apartment->area)/100;?> м<sup>2</td>
                            <td><?=($apartment->area-$apartment->common_area)/100;?> м<sup>2</td>
                            <td><?=($apartment->common_area)/100;?> м<sup>2</td>
                            <td><?=$apartment->short_description_ru;?></td>
                            <td><?=(($apartment->area/100) > 0 ? round($apartment->price/($apartment->area/100),0) : 0);?> Eur</td>
                            <td><?=$apartment->price;?> Eur</td>
                            <td>
                                <?foreach (array('dunes' => 'Дюны', 'park' => 'Парк','sea' => 'Море', 'mountains' => 'Горы', 'forest' => 'Лес', 'pool' => 'Бассейн') as $key => $val): ?>
                                    <?if (in_array($key, explode(',', $apartment->view))): ?><?=$val.'<br>'?><? endif;?>
                                <? endforeach;?>
                            </td>
                        </tr>
                    
                    <? endforeach;?>
                    </table>
                </div>
</div>
