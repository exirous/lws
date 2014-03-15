<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stalker
 * Date: 2/6/13
 * Time: 2:03 PM
 * To change this template use File | Settings | File Templates.
 */
/* @var $this NewsController */
/* @var $news News[] */
?>
<div class="glass_wrapper">
    <div class="rounded">
        <div class="newsBlock">
            <ul>
                <?foreach ($news as $article): ?>
                    <li>
                        <h3><a href="/news/<?=$article->id;?>"><?=$article->title;?></a></h3><br>
                        <div class="newsArticle"><?=$article->short_text;?>...</div>
                    </li>
                <? endforeach;?>
            </ul>
        </div>
    </div>
</div>