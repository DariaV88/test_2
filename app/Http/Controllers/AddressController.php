<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    public function index()
    {
        $resultData = session('results');
        $address = old('address');

        return view('address', compact('address', 'resultData'));
    }

    public function show(AddressRequest $request)
    {

        // достаём текст из запроса пользователя
        $address = trim($request->input('address'));

        // проверяем на уникальность и в случае уникальности добавляем в таблицу
        $addressModel = Address::firstOrCreate(['address' => $address]);

        // делаем запрос к геокодеру
        $apiKey = "da34836b-5de8-48c1-88b7-fbb789e1616c"; 
        $params = [
            'apikey' => $apiKey,
            'geocode' => $address,
            'lang' => 'ru_RU',
            'format' => 'json',
            'bbox' => '37.398,55.516~37.876,55.945', // границы Москвы
            'rspn' => 1, 
            'results' => 5, 
        ];
        $data = $this->apiRequest($params);
        
        //распаковываем полученный массив
        $results = [];
        if (isset($data['response']['GeoObjectCollection']['featureMember'])) {
            foreach ($data['response']['GeoObjectCollection']['featureMember'] as $member) {

            $geocoderMetaData = $member['GeoObject']['metaDataProperty']['GeocoderMetaData'];
            $fullAddress = $geocoderMetaData['text'] ?? ''; 
            $components = $geocoderMetaData['Address']['Components'];

            // т.к. выдача массива components бывает разной, проходимся циклом, чтобы найти именно street и house
            foreach ($components as $component) {
                if ($component['kind'] === 'street') {
                    $street = $component['name'];
                } elseif ($component['kind'] === 'house') {
                    $house = $component['name'];
                }
            }

        // т.к. апи яндекса не выдает так просто станцию метро, отправляем доп запрос, используя обратное геокодирование
                $cord = str_replace(" ", ",", $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']);
                
                $params = array(
                    'apikey' => $apiKey,
                    'geocode' => $cord,
                    'kind' => 'metro', 
                    'format' => 'json',
                    'results' => 1, 
                );

              $data = $this->apiRequest($params);

    $metro = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'] ?? '';
   
            $results[] = [
                    'fullAddress' => $fullAddress,
                    'street' => $street ?? null,
                    'house' => $house ?? null,
                    'metro' => $metro ?? null,
                ];
            }
        }

        // передаём данные на страницу
        return redirect()->route('index')->with('results', $results)->withInput();
    }

        public function apiRequest($params) {
        $url = 'https://geocode-maps.yandex.ru/1.x/?' . http_build_query($params);

        $response = Http::get($url);
        if (!$response->ok()) {
            return back()->with('error', 'Ошибка при обращении к API');
        }
        $data = $response->json();
        return $data;
    }
}