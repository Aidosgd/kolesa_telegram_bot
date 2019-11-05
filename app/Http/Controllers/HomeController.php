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
        $urls = [
//            'https://kolesa.kz/cars/vaz/lada-priora/almaty/?_sys-hasphoto=2&auto-emergency=1&auto-car-order=1',
            'https://kolesa.kz/cars/mercedes-benz/e-55/almaty/',
//            'https://kolesa.kz/cars/vaz/2114/almaty/?_sys-hasphoto=2',
            'https://kolesa.kz/cars/nissan/primera/almaty/?_sys-hasphoto=2&auto-custom=2&auto-emergency=1&auto-fuel=1&auto-car-transm=1&auto-car-order=1&price[to]=1%20000%20000',
//            'https://kolesa.kz/cars/daewoo/nexia/almaty/?_sys-hasphoto=2&auto-custom=2&auto-emergency=1&auto-fuel=1&auto-car-order=1',
            'https://kolesa.kz/cars/bmw/5-series/almaty/?_sys-hasphoto=2&auto-custom=2&auto-emergency=1&auto-fuel=1&auto-car-transm=1&auto-car-order=1&year[from]=1990&year[to]=1995',
        ];

        foreach ($urls as $url){
            $html = file_get_html($url);
            $i = 0;
            foreach ($html->find('div.a-elem') as $item){
                if(isset($item->children[1]->children[3]->children[1]->children[0])){
                    $i++;
                }
            }

            $searchRes = $html->find('div.a-elem')[$i];

            // get item
            $data_id = $searchRes->attr['data-id'];
            $href = $searchRes->children[0]->attr['href'];
            $price = $searchRes->find('.price');

            $lastVehicle = Vehicle::where('data_id', $data_id)->first();

            if(!$lastVehicle){
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

                echo "Надо отправить сообщение в телегу\n \n \n";
            }else{
                echo "НЕ надо отправлять сообщение в телегу\n \n \n";
            }
        }
    }
}
