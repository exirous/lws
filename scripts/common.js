$(function ()
{
    $('.section.departments').on('click', function ()
    {
        var angle = 90;
        if ($('.category_list').is(':hidden'))
            angle = 0;
        $('.category_list').slideToggle();

        $(this).find('.selector').animate(
            {rotation: angle},
            {
                duration: 300,
                step: function (now, fx)
                {
                    $(this).rotate(now);
                }
            }
        );
        return false;
    });
})
