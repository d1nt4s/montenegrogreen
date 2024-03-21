<?php

namespace Dantes\Montegreen\Statuses\Estate\Input;

interface InputInterface
{
    public function ask($parameters);
    public function put($parameters, $object_id);
}