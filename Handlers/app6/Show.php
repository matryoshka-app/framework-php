<?php


namespace Matryoshka\Handlers\app6;

use Feed;
use Matryoshka\Handlers\Handler;
use Matryoshka\Response\Widgets\Button;
use Matryoshka\Response\Widgets\Card;
use Matryoshka\Response\Widgets\InputText;
use Matryoshka\Response\Widgets\Text;


class Show extends \Matryoshka\Handlers\app6\Start {


    /**
     * URI name of virtual route for handler. Example 'catalog/items/12'
     * @return string
     */
    static function getURI() {
        return '/show';
    }

	/**
	 * @return void
	 * @throws \FeedException
	 */
    function handler() {

	    $button = new Button();
	    $button->title = '<< Back';
	    $button->uri = Start::getURI();
	    $this->getResponse()->addWidget($button);
    	try {
		    $id = (int)$this->getRequest()->query['id'];
		    if ($id) {
		    	$ex = \ORM::for_table('channels_users')
				    ->where('user_id', $this->getRequest()->user->id)
				    ->where('channel_id', $id)
				    ->count();
		    	if (!$ex) {
				    \ORM::for_table('channels_users')
					    ->create([
						    'user_id' => $this->getRequest()->user->id,
						    'channel_id' => $id,
					    ])
					    ->save();
			    }
		    }


		    $channel = \ORM::for_table('channels')
			    ->find_one($id);

		    $rss = Feed::loadRss($channel->url);

		    foreach ($rss->item as $item) {
		    	$title = (string)$item->title;
		    	$description = (string)$item->description;
		    	$link = (string)$item->link;
		    	$card = new Card();
			    $text = new Text($title);
			    $text->style->bold = true;
			    $text->style->size = 16;
		    	$card->title = $text;
		    	$card->subtitle = "\n\n***\n\n\n \n\n".strip_tags($description);
		    	if ($link) {
		    	    $card->uri = $link;
				    $but = new Button();
				    $but->title = 'More';
				    $but->uri = $link;
				    $card->buttons[] = $but;
				}

				$this->getResponse()->addWidget($card);

		    }
	    }
	    catch (\Exception $exception) {
    		$text = new Text('error connect to rss. try later.' );
    		$this->getResponse()->addWidget($text);
	    }


	    $button = new Button();
	    $button->title = '<< Back';
	    $button->uri = Start::getURI();
	    $this->getResponse()->addWidget($button);
    }

}