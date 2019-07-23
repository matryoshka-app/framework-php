<?php


namespace Matryoshka\Handlers\app6;

use Matryoshka\Handlers\Handler;
use Matryoshka\Response\Widgets\Button;
use Matryoshka\Response\Widgets\InputText;


class Add extends \Matryoshka\Handlers\app6\Start {


    /**
     * URI name of virtual route for handler. Example 'catalog/items/12'
     * @return string
     */
    static function getURI() {
        return '/add';
    }

    /**
     * @return void
     */
    function handler() {
	    $this->getResponse()->id = $_ENV['APP_ID'];


	    $text = new InputText();
	    $text->key = 'title';
	    $text->hint = 'Title';
		$this->getResponse()->addWidget($text);

	    $text = new InputText();
	    $text->key = 'url';
	    $text->hint = 'RSS URL';
		$this->getResponse()->addWidget($text);

		$button = new Button();
		$button->title = 'Save';
		$button->uri = '/save';
		$this->getResponse()->addWidget($button);

		$button = new Button();
		$button->title = '<< Back';
		$button->uri = '/';
		$this->getResponse()->addWidget($button);



    }

}