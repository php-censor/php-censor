<?php

return [
    /** Loggers attached to every command */
    "_" => function() {
        return [
            new \Monolog\Handler\StreamHandler(__DIR__ . DIRECTORY_SEPARATOR . 'errors.log', \Monolog\Logger::ERROR),
        ];
    },
    /** Loggers for the RunCommand */
    'RunCommand' => function() {
        return [
            new \Monolog\Handler\RotatingFileHandler(__DIR__ . DIRECTORY_SEPARATOR . 'everything', 3, \Monolog\Logger::DEBUG),
        ];
    },
];
