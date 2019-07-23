<?php


namespace Matryoshka\Handlers\app7;

use Matryoshka\Handlers\Handler;
use Matryoshka\Request\Request;
use Matryoshka\Response\Params\Menu;
use Matryoshka\Response\Params\MenuItem;
use Matryoshka\Response\Params\Style;
use Matryoshka\Response\Widgets\Button;
use Matryoshka\Response\Widgets\Card;
use Matryoshka\Response\Widgets\Image;
use Matryoshka\Response\Widgets\Text;

class Start extends Handler {


	public function  __construct(Request $request)
	{
		parent::__construct($request);
		$this->getResponse()->id = 7;
		$style = new Style();
		$style->color = '#b3b3b3';
		$this->getResponse()->style = $style;

	}

	/**
     * URI name of virtual route for handler. Example 'catalog/items/12'
     * @return string
     */
    static function getURI() {
        return '/';
    }

    /**
     * @return void
     */
    function handler() {
		$image = new Image('http://poster.jokerhost.ru/kitty/800/800/random?'.time());
		$image->fillable = Image::FILLABLE_FILL_WIDTH;
		$this->getResponse()->addWidget($image);

		$button = new Button();
		$button->uri = self::getURI();
		$button->title = '👍';
		$button->progressbar = true;
		$this->getResponse()->addWidget($button);


    }

}