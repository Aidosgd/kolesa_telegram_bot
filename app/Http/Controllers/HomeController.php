<?php

namespace App\Http\Controllers;

use App\Vehicle;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use PhpParser\Node\Expr\Cast\Object_;
use Telegram\Bot\Laravel\Facades\Telegram;

class HomeController extends Controller
{
    public function index()
    {
        $telegram_channel_id = -1001440750992;
        $url = 'https://kolesa.kz/cars/mercedes-benz/e-klasse/almaty/?auto-emergency=1&auto-fuel=1&auto-car-transm=2345&auto-car-order=1&year[to]=1995';

        $html = file_get_html($url);
        $i = 0;
        foreach ($html->find('div.vw-item') as $item){
            if(isset($item->children[1]->children[3]->children[1]->children[0])){
               $i++;
            }
        }

        $searchRes = $html->find('div.vw-item')[$i];

        // get item
        $data_id = $searchRes->attr['data-id'];
        $href = $searchRes->children[0]->attr['href'];

        $lastVehicle = Vehicle::orderBy('id', 'desc')->first();

        if($data_id != $lastVehicle->data_id){
            // save new vehicle
            $vehicle = new Vehicle();
            $vehicle->fill([
               'data_id' => $data_id,
               'href' => $href,
            ]);
            $vehicle->save();

            $text = "Новое объявление на сайте\n"
                . "https://kolesa.kz" .$href. "\n";

            Telegram::sendMessage([
                'chat_id' => $telegram_channel_id,
                'parse_mode' => 'HTML',
                'text' => $text
            ]);

            return 'Надо отправить сообщение в телегу';
        }else{
            return 'НЕ надо отправлять сообщение в телегу';
        }
    }
}
