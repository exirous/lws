<?php

namespace JBBCode;

require_once 'CodeDefinition.php';
require_once 'CodeDefinitionBuilder.php';
require_once 'CodeDefinitionSet.php';
require_once 'validators/CssColorValidator.php';
require_once 'validators/UrlValidator.php';

/**
 * Provides a default set of common bbcode definitions.
 *
 * @author jbowens
 */
class DefaultCodeDefinitionSet implements CodeDefinitionSet
{

    /* The default code definitions in this set. */
    protected $definitions = array();

    /**
     * Constructs the default code definitions.
     */
    public function __construct()
    {
        /* [b] bold tag */
        $builder = new CodeDefinitionBuilder('b', '<strong>{param}</strong>');
        array_push($this->definitions, $builder->build());

        /* [center] tag */
        $builder = new CodeDefinitionBuilder('center', '<center>{param}</center>');
        array_push($this->definitions, $builder->build());

        /* [left] tag */
        $builder = new CodeDefinitionBuilder('left', '<left>{param}</left>');
        array_push($this->definitions, $builder->build());


        /* [ol] tag */
        $builder = new CodeDefinitionBuilder('ol', '<ol>{param}</ol>');
        array_push($this->definitions, $builder->build());

        /* [li] tag */
        $builder = new CodeDefinitionBuilder('li', '<li>{param}</li>');
        array_push($this->definitions, $builder->build());

        /* [sup] tag */
        $builder = new CodeDefinitionBuilder('sup', '<sup>{param}</sup>');
        array_push($this->definitions, $builder->build());

        /* [i] italics tag */
        $builder = new CodeDefinitionBuilder('i', '<em>{param}</em>');
        array_push($this->definitions, $builder->build());

        /* [u] italics tag */
        $builder = new CodeDefinitionBuilder('u', '<u>{param}</u>');
        array_push($this->definitions, $builder->build());

        $urlValidator = new \JBBCode\validators\UrlValidator();

        /* [url] link tag */
        $builder = new CodeDefinitionBuilder('url', '<a href="{param}">{param}</a>');
        $builder->setParseContent(false)->setBodyValidator($urlValidator);
        array_push($this->definitions, $builder->build());

        /* [url=http://example.com] link tag */
        $builder = new CodeDefinitionBuilder('url', '<a href="{option}">{param}</a>');
        $builder->setUseOption(true)->setParseContent(true)->setOptionValidator($urlValidator);
        array_push($this->definitions, $builder->build());

        /* [pilot=1][/pilot] pilot tag */
        $builder = new CodeDefinitionBuilder('pilot', '<a pilot="{option}">{param}</a>');
        $builder->setUseOption(true);
        array_push($this->definitions, $builder->build());

        /* [youtube] tag */
        $builder = new CodeDefinitionBuilder('youtube', '<iframe width="812" height="600" src="http://www.youtube.com/embed/{param}?wmode=opaque" data-youtube-id="{param}" frameborder="0" allowfullscreen=""></iframe>');
        $builder->setUseOption(false)->setParseContent(false);
        array_push($this->definitions, $builder->build());


        /* [img] image tag */
        $builder = new CodeDefinitionBuilder('img', '<img style="max-width:812px" src="{param}"/>');
        $builder->setUseOption(false)->setParseContent(false)->setBodyValidator($urlValidator);
        array_push($this->definitions, $builder->build());

        /* [img=alt text] image tag */
        /*$builder = new CodeDefinitionBuilder('img', '<img src="{param} alt="{option}" />');
        $builder->setUseOption(true);
        array_push($this->definitions, $builder->build());*/

        /* [color] color tag */
        $builder = new CodeDefinitionBuilder('color', '<font color="{option}">{param}</font>');
        $builder->setUseOption(true)->setOptionValidator(new \JBBCode\validators\CssColorValidator());
        array_push($this->definitions, $builder->build());

        /* [font] fontface tag */
        $builder = new CodeDefinitionBuilder('font', '<font face="{option}">{param}</font>');
        $builder->setUseOption(true);
        array_push($this->definitions, $builder->build());


        /* [size] color tag */
        $builder = new CodeDefinitionBuilder('size', '<font size="{option}">{param}</font>');
        $builder->setUseOption(true);//->setOptionValidator(new \JBBCode\validators\CssColorValidator());
        array_push($this->definitions, $builder->build());

    }

    /**
     * Returns an array of the default code definitions.
     */
    public function getCodeDefinitions()
    {
        return $this->definitions;
    }

}
