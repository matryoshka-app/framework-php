<?php
function get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_ENCODING ,"utf-8");

    $headers = [
        'accept:  application/json, text/javascript, */*; q=0.01',
        'accept-encoding: gzip, deflate, br',
        'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,uk;q=0.6',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36',
        'Cache-Control: no-cache',
        'authority: dietadiary.com',
        'x-requested-with: XMLHttpRequest',
        'referer: https://dietadiary.com/tablica-kalorijnosti-productov', //Your referrer address
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $server_output = curl_exec($ch);
    curl_close($ch);
    return $server_output;
}

include_once 'simple_html_dom.php';
$mysqli = new mysqli('mysql.jokerhost.ru', 'app658', '77E8STcX6oi90bUk0zlDyWIDg2', 'app658');
$mysqli->set_charset('utf8');
for($p=0; $p <=70; $p++) {

    $url = "http://www.calorizator.ru/product/all?page={$p}";
    /** @var simple_html_dom $dom */

    $dom = str_get_html(get($url), null, null, 'utf-8');
//    $dom = file_get_html($url);

    if ($table = $dom->find('.views-table', 0)) {
        $rows = [];
        /** @var simple_html_dom_node $table */
        foreach($table->find('tr') as $tr) {
            $row = [];
            /** @var simple_html_dom_node $tr */
            foreach($tr->find('td') as $idx => $td) {
                /** @var simple_html_dom_node $td */
                switch ($idx) {
                    case 0: {
                        if ($a = $td->find('a', 0)) {
                            /** @var simple_html_dom_node $a */
                            $row['img'] = $a->getAttribute('href');
                        }
                        else {
                            continue;
                        }
                        break;
                    }
                    case 1: {
                        $row['text'] = trim($td->text());
                        break;
                    }
                    case 2: {
                        $row['bel'] = trim($td->text());
                        break;
                    }
                    case 3: {
                        $row['jir'] = trim($td->text());
                        break;
                    }
                    case 4: {
                        $row['ugl'] = trim($td->text());
                        break;
                    }
                    case 5: {
                        $row['kkal'] = trim($td->text());
                        break;
                    }
                }

            }
            if (empty($row['text']))
                continue;
            try {
                $mysqli->query("INSERT INTO `calories` (`id`, `title`, `img`, `bel`, `jir`, `ugl`, `kkal`) 
                                    VALUES (NULL, '" . $row['text'] . "', '{$row['img']}', '{$row['bel']}', '{$row['jir']}', '{$row['ugl']}', '{$row['kkal']}');");
            }
            catch (\Exception $e) {}

        }
    }



}