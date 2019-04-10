<?php

namespace Drupal\weather_info\Controller;


class WeatherController {


  /**
   * @return array
   */
  public function build(){


    $message = 'This is a secret message';
    $decorated_message = \Drupal::service(' weather_info.weather_parser')->message($message);

    return [
      '#markup' => $decorated_message,
    ];


  }

}