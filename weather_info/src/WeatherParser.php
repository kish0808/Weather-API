<?php

namespace Drupal\weather_info;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use GuzzleHttp\Client;

class WeatherParser {

  private $currentUser;

  private $httpClient;

  private $entityTypeManager;

  public function __construct(\Drupal\Core\Session\AccountProxy $user, Client $http_client, EntityTypeManagerInterface $entityTypeManager) {
    $this->currentUser = $user;
    $this->httpClient = $http_client;
    $this->entityTypeManager = $entityTypeManager;
  }

  public function weatherInfo($Weatherinfo) {
    // User location.
    $user = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());
    $location_id = $user->get('field_location')->target_id;
    $location = $this->entityTypeManager->getStorage('taxonomy_term')->load($location_id);

    $result = NULL;
    if ($location) {
      $location = $location->getName();

      // Location to lat log.
      $latLng = $this->getLatLong($location);

      //configration collection
      $config = \Drupal::config('weatherform.settings');
      $opencagedata_key = $config->get('weatherform.opencagedata_key');
      $darksky_keyk = $config->get('weather.darksky_key');




      // Get weather information.
      $weatherInfo = $this->getWeatherInfo($latLng['lat'], $latLng['lng']);

      $result = [
        'location' => $location,
        'temp' => $weatherInfo['summary'],
        'details' => $weatherInfo['temperature'],
      ];
    }

    return $result;
  }

  public function getLatLong($location) {
    //configration collection

    $config = \Drupal::config('weatherform.settings');
    $opencagedata_key = $config->get('weatherform.opencagedata_key');
    //$opencagedata_key = '93c8b5d4c6f74a2f84b32fcca4c27ffe';
    $url = "https://api.opencagedata.com/geocode/v1/json?key=$opencagedata_key&q=$location&no_annotations=1";
    $request = $this->httpClient->get($url);
    $response = json_decode($request->getBody(), TRUE);

    return [
      'lat' => $response['results'][0]['geometry']['lat'],
      'lng' => $response['results'][0]['geometry']['lng'],
    ];
  }

  public function getWeatherInfo($latitude, $longitude) {

    $config = \Drupal::config('weatherform.settings');
    $darksky_key = $config->get('weather.darksky_key');
    //$darksky_key = '84751d89a2100fc65f8a7a1a162c269c';
    $url = "https://api.darksky.net/forecast/$darksky_key/$latitude,$longitude";
    $request = $this->httpClient->get($url);
    $response = json_decode($request->getBody(), TRUE);

    return [
      'summary' => $response['currently']['summary'],
      'temperature' => $response['currently']['temperature']
    ];
  }

}