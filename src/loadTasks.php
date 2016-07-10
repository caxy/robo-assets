<?php

namespace Bangpound\Robo\Task\Assets;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
   * Return services.
   */
  public static function getAssetsServices()
  {
      return new SimpleServiceProvider(
      [
        'taskSassc' => Sassc::class,
      ]
    );
  }

  /**
   * @param $input
   *
   * @return Sassc
   */
  protected function taskSassc($input)
  {
      return $this->task(__FUNCTION__, $input);
  }
}
