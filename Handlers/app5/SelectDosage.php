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

class SelectDosage extends Calculating {


    /**
     * URI name of virtual route for handler. Example 'catalog/items/12'
     * @return string
     */
    static function getURI() {
        return '/dosage';
    }

    /**
     * @return void
     */
    function handler() {
        if (!$id = (int)$this->getRequest()->query['id']) {
        	$this->getResponse()->addWidget(new Text('Ошибка. Попробуйте позже'));
	        $this->addBackButton(SelectItem::getURI());
	        return;
        }

	    $input = $this->getRequest()->input;
	    $doza = $input->doza;
	    $count = $input->count;

	    if (!$count) {
		    $count = 1;
	    }

	    $response = $this->getResponse();

        $item = \ORM::for_table('calories')
	        ->find_one($id);

	    $card = new Card();
	    $title = new Text($item['title']);
	    $title->style->bold = true;
	    $card->title = $title;
	    $card->image = $item['img'];
	    $card->subtitle = "Каллории: {$item['kkal']}\nБелки: {$item['bel']}\nЖиры: {$item['jir']}";
	    $response->addWidget($card);

        $select = new InputSelect();
        $select->title = 'Выберите дозировку';
        $select->key = 'doza';
        $select->value = $doza ?? '';
        $select->options = array_values($this->doza);
        $response->addWidget($select);

        $text = new InputText();
        $text->key = 'count';
        $text->label = 'Количество';
        $text->value = $count;
        $response->addWidget($text);

        $button = new Button();
        $button->title = 'Подсчитать';
        $button->uri = self::getURI().'?id='.$item['id'].'&calc=1';
        $button->progressbar = true;
        $response->addWidget($button);

        // result
	    if(empty($this->getRequest()->query['calc'])) {
	    	return;
	    }


	    if(empty($doza)) {
		    $text = new Text('Выберите дозировку');
		    $text->style->size = 12;
		    $response->addWidget($text);
		    return;
	    }
	    $dozes = array_flip($this->doza);
	    $grms = $item[$dozes[$doza]] * $count;
	    $blk = $item['bel'];
	    $jir = $item['jir'];
	    $ugl = $item['ugl'];
	    $kkal = $item['kkal'];

	    $text = new Text('Дозировка: ' . $doza . ' X ' . $count);
	    $text->style->size = 16;
	    $response->addWidget($text);

	    $kkal = $kkal / 100;
	    $text = new Text('Ккал: ' . ($kkal * $grms));
	    $text->style->size = 14;
	    $text->style->bold = true;
	    $response->addWidget($text);

	    $text = new Text('Белки: ' . ($kkal * $blk));
	    $text->style->size = 14;
	    $text->style->bold = true;
	    $response->addWidget($text);

	    $text = new Text('Жиры: ' . ($kkal * $jir));
	    $text->style->size = 14;
	    $text->style->bold = true;
	    $response->addWidget($text);

	    $text = new Text('Углеводы: ' . ($kkal * $ugl));
	    $text->style->size = 14;
	    $text->style->bold = true;
	    $response->addWidget($text);

	    $this->addBackButton();


    }

}