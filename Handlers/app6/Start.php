<?php


namespace Matryoshka\Handlers\app6;

use Matryoshka\Handlers\Handler;
use Matryoshka\Request\Request;
use Matryoshka\Response\Params\Menu;
use Matryoshka\Response\Params\MenuItem;
use Matryoshka\Response\Params\Style;
use Matryoshka\Response\Widgets\Button;
use Matryoshka\Response\Widgets\Card;
use Matryoshka\Response\Widgets\Text;

class Start extends Handler {


	public function  __construct(Request $request)
	{
		parent::__construct($request);
		$this->getResponse()->id = $_ENV['APP_ID'];
		$style = new Style();
		$style->color = '#f59042';
		$this->getResponse()->style = $style;


		$menu = new Menu();

		$menuItem = new MenuItem();
		$text = new Text('➕ Add channel');
		$text->style->color = '#f56642';
		$text->style->bold = true;
		$menuItem->title = $text;
		$menuItem->uri = '/add';

		$menu->add($menuItem);

		$menuItem = new MenuItem();
		$menuItem->title = '📚 Catalog';
		$menuItem->uri = '/catalog';
		$menu->add($menuItem);

		$this->getResponse()->menu = $menu;

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
	    $this->getResponse()->id = $_ENV['APP_ID'];
        // get my channels
	    $my = \ORM::for_table('channels')
		    ->distinct()
		    ->selectMany('c.id', 'c.title', 'c.url')
		    ->table_alias('c')
		    ->where('cu.user_id', $this->getRequest()->user->id)
		    ->left_outer_join('channels_users', 'cu.user_id = c.user_id or c.id = cu.channel_id', 'cu')
		    ->find_array();



        if ($my) {
	        $text = new Text('My Watches');
	        $text->style->bold = true;
	        $text->style->center = true;
	        $text->style->size = 18;
	        $this->getResponse()->addWidget($text);

	    	foreach ($my as $channel) {
	    		$card = new Card();
	    		$title = new Text($channel['title']);
	    		$title->style->size = 18;
	    		$title->style->bold = true;
	    		$title->style->color = 'CD6A1F';
				$card->title = $title;
				$card->image = "https://matryoshka.app/uploads/covers/6.jpeg";
	    		$card->uri = '/show?id='.$channel['id'];
//
//	    		$but = new Button();
//	    		$but->uri = '/delete';
//	    		$but->title = 'Delete';
//	    		$card->buttons[] = $but;

//	    		$but = new Button();
//	    		$but->uri = '/show?id='.$channel['id'];
//	    		$but->title = 'Open';
//	    		$card->buttons[] = $but;
	    		$this->getResponse()->addWidget($card);
		    }
	    }
	    else {

		    $add = new Button();
		    $add->title = 'Catalog';
		    $add->uri = '/catalog';
		    $this->getResponse()->addWidget($add);

		    $add = new Button();
		    $add->title = 'Add channel to catalog';
		    $add->uri = '/add';
		    $this->getResponse()->addWidget($add);
	    }




    }

}