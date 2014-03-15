<? /** @var $categories Category[] */ ?>
<div class="category_list">
    <?foreach ($categories as $category): ?>
    <div href="" class="item"<?= $category->id ?>">
    <a href="/c/<?=$category->name?>" class="link" data-id="<?= $category->id ?>"><?=$category->title?></a>
    <?if (count($category->categories)): ?>
        <div class="subcategory_list">
            <i></i>
            <?foreach ($category->categories as $subCategory): ?>
                <a href="/c/<?=$subCategory->name?>" data-id="<?= $subCategory->id ?>"><?=$subCategory->title?></a>
            <? endforeach;?>
        </div>
    <? endif;?>
</div>
<? endforeach; ?>
</div>
<div class="location_filter">
    <div class="head">In your area</div>
    <div class="selector">City</div>
    <div class="container">
        <a href="">Lorem.</a>
        <a href="">Autem.</a>
        <a href="">Repellat.</a>
        <a href="">Iusto.</a>
        <a href="">Vel!</a>
        <a href="">Eos.</a>
        <a href="">Pariatur.</a>
        <a href="">Explicabo.</a>
        <a href="">Ad?</a>
        <a href="">Pariatur.</a>
    </div>
    <div class="selector">United Kingdom</div>
</div>