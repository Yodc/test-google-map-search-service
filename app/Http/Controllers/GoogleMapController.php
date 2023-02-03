<?php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
 
class GoogleMapController extends Controller
{
    
    private function default_search_google_map()
    {
        
        return Cache::rememberForever('default_search', function() {
            // สร้าง Class มาจาก lib
            // https://packagist.org/packages/skagarwal/google-places-api
            $googlePlaces = new \SKAgarwal\GoogleApi\PlacesApi("AIzaSyCkLHX8ZMK89h84sjvl1Uxx0wQmM0DX7hs");
            return $googlePlaces->textSearch("Bang sue");
         });

    }

    private function search_google_map($searchText)
    {

        $keyCache = "search_{$searchText}";
        if(Cache::has($keyCache)){
            return Cache::get($keyCache);
        }else{
            // สร้าง Class มาจาก lib
            // https://packagist.org/packages/skagarwal/google-places-api
            $googlePlaces = new \SKAgarwal\GoogleApi\PlacesApi("AIzaSyCkLHX8ZMK89h84sjvl1Uxx0wQmM0DX7hs");
            $items = $googlePlaces->textSearch($searchText);
            
            Cache::add($keyCache, $items, 15);

            return Cache::get($keyCache);
        }
    }

    public function index(Request $request)
    {
        // สร้าง Class มาจาก lib
        // https://packagist.org/packages/skagarwal/google-places-api
        $googlePlaces = new \SKAgarwal\GoogleApi\PlacesApi("AIzaSyCkLHX8ZMK89h84sjvl1Uxx0wQmM0DX7hs");

        if( (! $request->has('search_text')) || strtolower($request->search_text) == strtolower('Bang sue')){
            $items = $this->default_search_google_map();
        }else{
            $items = $this->search_google_map($request->search_text);
        }

        return response()->json($items, 200);
    }
}