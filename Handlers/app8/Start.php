<?php


namespace Matryoshka\Handlers\app8;

use Matryoshka\Handlers\Handler;
use Matryoshka\Request\Request;
use Matryoshka\Response\Params\Menu;
use Matryoshka\Response\Params\MenuItem;
use Matryoshka\Response\Params\Style;
use Matryoshka\Response\Widgets\Button;
use Matryoshka\Response\Widgets\Card;
use Matryoshka\Response\Widgets\Image;
use Matryoshka\Response\Widgets\InputText;
use Matryoshka\Response\Widgets\Text;

class Start extends Handler {


	public function  __construct(Request $request)
	{
		parent::__construct($request);
		$this->getResponse()->id = 8;
		$style = new Style();
		$style->color = '#600080';
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

		$this->lang = $this->getRequest()->user->lang;

	    $height = $this->getRequest()->input->value('height');
	    $weight = $this->getRequest()->input->value('weight');


    	$input = new InputText();
    	$input->label = $this->t('Your height cm');
    	$input->value = $height  ?? 170;
    	$input->key = 'height';
	    $this->getResponse()->addWidget($input);

	    $input = new InputText();
    	$input->label = $this->t('Your weight');
    	$input->value = $weight ?? 60;
    	$input->key = 'weight';
	    $this->getResponse()->addWidget($input);

		$button = new Button();
		$button->uri = self::getURI();
		$button->title = $this->t('Calculate');
		$button->progressbar = true;
		$this->getResponse()->addWidget($button);

	    if ($height && $weight) {
	    	$bmi = $weight / (($height / 100) ** 2);

		    $card = new Card();
		    $text = new Text($this->t('Results') . ":\n");
		    $text->style->bold = true;
		    $text->style->size = 18;
		    $card->title = $text;

		    $resText = null;
		    foreach ($this->results as $res) {
		    	if ($bmi >= $res['min'] && $bmi <= $res['max']) {
		            $card->image = $res['image'];
				    $resText = $res['text'];
		    		break;
			    }
		    }

		    $card->subtitle = $this->t('Your BMI is') . ': ' . number_format($bmi, 2, ',', '') . "\n\n". $this->t($resText);
		    $this->getResponse()->addWidget($card);
	    }

    }

    private $lang = 'en';
    private $translations = [
    	'Your weight' => [
    		'ru' => 'Твой вес',
	    ],
    	'Your height cm' => [
    		'ru' => 'Твой рост см',
	    ],
	    'Calculate' => [
		    'ru' => 'Подсчитать',
	    ],
	    'Your BMI is' => [
		    'ru' => 'Твой ИМТ',
	    ],
	    'Results' => [
		    'ru' => 'Результат',
	    ],
	    'Severe body mass deficiency' => [
		    'ru' => 'Выраженный дефицит массы тела',
	    ],
	    'Insufficient (deficit) body weight' => [
		    'ru' => 'Недостаточная (дефицит) масса тела',
	    ],
	    'Norm' => [
		    'ru' => 'Норма',
	    ],
	    'Overweight' => [
		    'ru' => 'Избыточная масса тела',
	    ],
	    'First degree obesity' => [
		    'ru' => 'Ожирение первой степени',
	    ],
	    'Second degree obesity' => [
		    'ru' => 'Ожирение второй степени',
	    ],
	    'Third degree obesity' => [
		    'ru' => 'Ожирение третьей степени',
	    ],
    ];

    protected $results = [
    	[
    		'min' => 0,
		    'max' => 16,
		    'text' => 'Severe body mass deficiency',
		    'image' => 'https://49.img.avito.st/640x480/4399977849.jpg',
	    ],
	    [
    		'min' => 16,
		    'max' => 18.5,
		    'text' => 'Insufficient (deficit) body weight',
		    'image' => 'http://fb.ru/misc/i/thumb/a/1/3/4/0/0/6/4/1340064.jpg',
	    ],
	    [
    		'min' => 18.6,
		    'max' => 24.99,
		    'text' => 'Norm',
		    'image' => 'https://avatanplus.com/files/resources/mid/594f6bd9f399c15cde3d4b8e.png'
	    ],
	    [
    		'min' => 25,
		    'max' => 30,
		    'text' => 'Overweight',
		    'image' => 'https://i.pinimg.com/originals/2a/3b/17/2a3b175c8b6752a62a6f6915ff472f8c.jpg'
	    ],
	    [
    		'min' => 30,
		    'max' => 35,
		    'text' => 'First degree obesity',
		    'image' => 'https://st3.depositphotos.com/1036149/13003/i/450/depositphotos_130033018-stock-photo-funny-pig-wearing-santa-hat.jpg'
	    ],
	    [
    		'min' => 35,
		    'max' => 40,
		    'text' => 'Second degree obesity',
		    'image' => 'https://st.depositphotos.com/1007168/3972/i/450/depositphotos_39720019-stock-photo-happy-pig-cartoon-mascot-character.jpg',
	    ],
	    [
    		'min' => 40,
		    'max' => 500,
		    'text' => 'Third degree obesity',
		    'image' => 'https://st2.depositphotos.com/1036149/12274/i/450/depositphotos_122744610-stock-photo-cute-funny-elephant.jpg'
	    ],
    ];

    protected function t($key) {
    	return $this->translations[$key][$this->lang] ?? $key;
    }

}