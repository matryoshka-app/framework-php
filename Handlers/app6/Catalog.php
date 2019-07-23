<?php


namespace Matryoshka\Handlers\app6;

use Matryoshka\Handlers\Handler;
use Matryoshka\Request\Request;
use Matryoshka\Response\Params\Style;
use Matryoshka\Response\Widgets\Button;
use Matryoshka\Response\Widgets\Card;
use Matryoshka\Response\Widgets\Text;

class Catalog extends Start {


	public function  __construct(Request $request)
	{
		parent::__construct($request);
		$this->getResponse()->id = $_ENV['APP_ID'];

	}

	/**
     * URI name of virtual route for handler. Example 'catalog/items/12'
     * @return string
     */
    static function getURI() {
        return '/catalog';
    }

    /**
     * @return void
     */
    function handler() {
	    $this->getResponse()->id = $_ENV['APP_ID'];
        // get my channels
	    $my = \ORM::for_table('channels')
		    ->selectMany('id', 'title', 'url')
		    ->find_array();

	    $add = new Button();
        $add->title = 'Add channel to catalog';
        $add->uri = '/add';
        $this->getResponse()->addWidget($add);

        $add = new Button();
        $add->title = '<< Back';
        $add->uri = '/';
        $this->getResponse()->addWidget($add);

        $style = new Style();
        $style->color = '#f59042';

        if ($my) {
	    	foreach ($my as $channel) {
	    		$card = new Card();
			    $title = new Text($channel['title']);
			    $title->style->size = 18;
			    $title->style->bold = true;
			    $title->style->color = 'CD6A1F';
			    $card->title = $title;
			    $card->image = "https://matryoshka.app/uploads/covers/6.jpeg";
			    $card->uri = '/show?id='.$channel['id'];
	    		$this->getResponse()->addWidget($card);
		    }
	    }



    }

}