<?php

return array(
    'PopSpider' => new Pop\Config(array(
        'base'   => realpath(__DIR__ . '/../'),
        'config' => realpath(__DIR__ . '/../config'),
        'data'   => realpath(__DIR__ . '/../data'),
        'src'    => realpath(__DIR__ . '/../src'),
        'view'   => realpath(__DIR__ . '/../view')
    ))
);

