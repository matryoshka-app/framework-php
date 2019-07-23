<?php


namespace Matryoshka\Handlers\app5;

use Matryoshka\Handlers\Handler;
use Matryoshka\Request\Request;
use Matryoshka\Response\Params\MenuItem;
use Matryoshka\Response\Types\ResponseFull;
use Matryoshka\Response\Widgets\Button;
use Matryoshka\Response\Widgets\Card;
use Matryoshka\Response\Widgets\InputSelect;
use Matryoshka\Response\Widgets\InputText;
use Matryoshka\Response\Widgets\Text;
use mysqli;

class SelectItem extends Calculating {


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

        $response = $this->getResponse();
        $response->id = $_ENV['APP_ID'];
	    $page = (int) ($this->getRequest()->query['page'] ?? 0);

	    $query = \ORM::for_table('calories');


	    $searchInput = new InputText();
		$searchInput->label = $this->t('Search');
		$searchInput->key = 'search';

		if ($search = $this->getRequest()->input->search) {
			$searchInput->value = $search;
		    $search = addslashes($search);
		    $query->where_raw("LOWER(title) LIKE LOWER('%{$search}%')");
	    }

	    $this->getResponse()->addWidget($searchInput);



	    
		$searchButton = new Button();
		$searchButton->title = $this->t('🔍 Find');;
		$searchButton->progressbar = true;
	    $searchButton->style->background = '#b3cce6';
		$searchButton->uri = self::getURI();

		$this->getResponse()->addWidget($searchButton);

		$limit = 50;

		$items = $query
			->orderByAsc('title')
			->offset($page)
			->limit(50)
			->find_array();

	    foreach ($items as $item) {
	    	$card = new Card();
	    	$title = new Text($item['title']);
	    	$title->style->bold = true;
	    	$card->title = $title;
	    	$card->progressbar = true;
	    	$card->image = $item['img'];
	    	$card->subtitle = $this->t('Calories').": {$item['kkal']}\n".$this->t('Proteins').": {$item['bel']}\n".$this->t('Fats').": {$item['jir']}";
	    	$card->uri = SelectDosage::getURI() . '?id='.$item['id'];
		    $this->getResponse()->addWidget($card);
        }

        if (count($items) == $limit) {

	        if ($page > 0) {
		        $more = new Button();
		        $more->title = 'Назад';
		        $more->uri = self::getURI() . '?page=' . ($page - 1);
		        $more->progressbar = true;
		        $this->getResponse()->addWidget($more);
	        }
	        $more = new Button();
	        $more->title = $this->t('More');
	        $more->progressbar = true;
	        $more->uri = self::getURI().'?page='.($page + 1);
	        $this->getResponse()->addWidget($more);
        }


    }

}