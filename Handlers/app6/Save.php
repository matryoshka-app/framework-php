<?php


namespace Matryoshka\Handlers\app6;

use Feed;
use Matryoshka\Handlers\Handler;
use Matryoshka\Response\Widgets\Button;
use Matryoshka\Response\Widgets\InputText;
use Matryoshka\Response\Widgets\Text;


class Save extends \Matryoshka\Handlers\app6\Start {


    /**
     * URI name of virtual route for handler. Example 'catalog/items/12'
     * @return string
     */
    static function getURI() {
        return '/save';
    }

    /**
     * @return void
     */
    function handler() {
	    $this->getResponse()->id = $_ENV['APP_ID'];
		$input = $this->getRequest()->input;
		$title = $input->title;
		$url = $input->url;


	    if (!empty($title) && !empty($url)) {
		try {
			$rss = Feed::loadRss($url);
			if (empty($rss)) {
				throw new \FeedException('empty');
			}
			$userId = $this->getRequest()->user->id;
			$channel = \ORM::for_table('channels')
				->create([
					'title' => $title,
					'url' => $url,
					'user_id' => $userId,
				]);
			$channel->save();
			\ORM::for_table('channels_users')
				->create([
					'channel_id' => $channel->id(),
					'user_id' => $userId,
				])->save();



			$text = new Text('saved');
			$this->getResponse()->addWidget($text);

			$button = new Button();
			$button->title = '<< Back';
			$button->uri = Add::getURI();
			$this->getResponse()->addWidget($button);
		} catch (\FeedException $e) {
			$text = new Text('URL is not supported or this channel use not correct rss format');
			$this->getResponse()->addWidget($text);

			$button = new Button();
			$button->title = '<< Back';
			$button->uri = Add::getURI();
			$this->getResponse()->addWidget($button);
		}
		}
		else {
			$text = new Text('All fields required');
			$this->getResponse()->addWidget($text);

			$button = new Button();
			$button->title = '<< Back';
			$button->uri = Add::getURI();
			$this->getResponse()->addWidget($button);
		}


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
		$button->uri = Save::getURI();
		$this->getResponse()->addWidget($button);

		$button = new Button();
		$button->title = '<< Back';
		$button->uri = Start::getURI();
		$this->getResponse()->addWidget($button);



    }

}