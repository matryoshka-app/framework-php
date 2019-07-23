<?php


namespace Matryoshka\Handlers\app5;

use Matryoshka\Handlers\Handler;
use Matryoshka\Request\Request;
use Matryoshka\Response\Params\MenuItem;
use Matryoshka\Response\Params\Style;
use Matryoshka\Response\Types\ResponseFull;
use Matryoshka\Response\Widgets\Button;
use Matryoshka\Response\Widgets\InputSelect;
use Matryoshka\Response\Widgets\Text;
use mysqli;

class Calculating extends Handler {
    private $lang = 'en';
    private $translations = [
        'Search' => [
            'ru' => 'Поиск',
        ],
        '🔍 Find' => [
            'ru' => '🔍 Искать',
        ],
        'More' => [
            'ru' => 'Еще',
        ],
        'Calories' => [
            'ru' => 'Каллории',
        ],
        'Proteins' => [
            'ru' => 'Белки',
        ],
        'Fats' => [
            'ru' => 'Жиры',
        ],

    ];

    protected function t($key) {
        return $this->translations[$key][$this->lang] ?? $key;
    }

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$style = new Style();
		$style->color = '#6699cc';
		$this->getResponse()->style = $style;
		$this->getResponse()->id = 5;
        $this->lang = $this->getRequest()->user->lang;

    }

	public $doza = [
        'st' => 'Столовая ложка',
        'ch' => 'Чайная ложка',
//        '100gr' => '100 гр.',
        '1gr' => 'Граммы',
    ];

    /**
     * URI name of virtual route for handler. Example 'catalog/items/12'
     * @return string
     */
    static function getURI() {
        return '/calculating';
    }

    public function addBackButton($uri = '/') {
	    $back = new Button();
	    $back->title = '<< Назад';
	    $back->uri = $uri;
	    $back->progressbar = true;
	    $back->style->background = '#b3cce6';
	    $this->getResponse()->addWidget($back);
    }

    /**
     * @return void
     */
    function handler() {
        // Create new Response or use exists. Default response created is full type
//        $response = new ResponseFull(); // then use
//        $this->setResponse($response);// for set new response
        $input = $this->getRequest()->input;
//        var_dump($input);exit;
        $mysqli = new mysqli($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASS'], $_ENV['MYSQL_USER']);
        $mysqli->set_charset('utf8');

        $response = $this->getResponse();
        $response->id = $_ENV['APP_ID'];
        $count = $input->count;
        if (!$count) {
            $count = 1;
        }
        $doza = $input->doza;
        $product = $input->product;
        if(empty($doza) || empty($product)) {
            $text = new Text('Не все поля указаны: d:'.$doza." - p: {$product}");
            $text->style->size = 12;
            $response->addWidget($text);
            return;
        }

        $text = new Text('Продукт: '.$product);
        $text->style->size = 16;
        $response->addWidget($text);

        $text = new Text('Дозировка: ' . $doza . ' X ' . $count);
        $text->style->size = 16;
        $response->addWidget($text);
        $dozes = array_flip($this->doza);
        $sql = "SELECT `".$dozes[$doza]."` as weight, `calories`.* FROM `calories` where `title` = '".addslashes($product) . "' limit 1";
        $grms = 0;
        $blk = 0;
        $jir = 0;
        $ugl = 0;
        $kkal = 0;
        if ($result = $mysqli->query($sql)) {
            $result = $result->fetch_assoc();
            $grms = $result['weight'];
            $blk = $result['bel'];
            $jir = $result['jir'];
            $ugl = $result['ugl'];
            $kkal = $result['kkal'];
        }
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

        $text = new Text('Угл: ' . ($kkal * $ugl));
        $text->style->size = 14;
        $text->style->bold = true;
        $response->addWidget($text);

        $this->addBackButton();
    }

}