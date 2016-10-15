<?php

namespace Bangpound\Robo\Task\Assets;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
  /**
   * @param $input
   *
   * @return Sassc
   */
  protected function taskSassc($input)
  {
      return $this->task(Sassc::class, $input);
  }
}
