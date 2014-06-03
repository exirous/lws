<?php

/**
 * Created by PhpStorm.
 * User: ExiRouS
 * Date: 5/16/2014
 * Time: 21:11
 */
class ParseController extends Controller
{

    public function actionParse()
    {
        $content = file_get_contents('http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html');
        preg_match_all('/\<tr\>(.*?)\<\/tr\>/', $content, $rows);
        $languages = [];
        $lang = false;
        foreach ($rows[0] as $row)
        {
            if (strpos($row, 'class=\'h\'') !== false)
                break;
            if (strpos($row, '<th') !== false)
            {
                if ($lang)
                {
                    $languages[] = $lang;
                }
                $lang = [];
            }
            else
            {
                if (!isset($lang['id']))
                {
                    preg_match('/\<a name="([a-z]{2,4})"/', $row, $id);
                    $lang['id'] = $id[1];
                    preg_match('/\<tr\>\<td(.*?)\>(.*?)\</', $row, $name);
                    $lang['name'] = $name[2];
                }
                preg_match_all('/\<td class=\'target\'\>(.*?)\<\/td\>/', $row, $rowdata);
                $lang['forms'][$rowdata[1][0]] = $rowdata[1][1];
            }
        }
        /*
         * TODO
         *  Значит, в $languages будет массив всех языков.
        Как добавить это всё в базу:
        Там есть 2 таблицы - translationLanguage и translationLanguageForm (или languageForm)

        У каждой записи из $languages будет id (сокращение языка из 2-3 букв, типа en,lv и.т.д),
         name - название языка и forms - ассоциативный массив из форм, где ключ эелемента -
         название формы (тип формы : zero, one, many и.т.д) а значение элемента - пример,
        т.е что-то типа
        foreach ($lang['forms'] as $type=>$example)
        {
           $form = new LanguageForm();
           $form->form = $type;
           $form->example = $example;
        }
        ВАЖНО!!  НЕ забудь: Когда будешь создавать Язык - кроме этих форм надо в каждый язык ПЕРВОЙ формой
        дополнительно записывать форму NON_PLURAL. ВАЖНО!!
        т.е кто-то типа
        foreach ($languages as $lang)
        {
           $form = new LanguageForm();
           $form->form = $type;
           $form->save()
           parseForms($lang['forms']);
        }
        */
        //echo nl2br(print_r($languages,true));
        die();
    }
} 