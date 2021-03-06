<?php declare(strict_types=1);

\Swoole\Runtime::enableCoroutine();


go(function () {
    global $argv, $argc;

    /*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

    if (version_compare('7.2.0', PHP_VERSION, '>')) {
        fwrite(
            STDERR,
            sprintf(
                'This version of PHPUnit is supported on PHP 7.2 and PHP 7.3.' . PHP_EOL .
                'You are using PHP %s (%s).' . PHP_EOL,
                PHP_VERSION,
                PHP_BINARY
            )
        );

        die(1);
    }

    if (!ini_get('date.timezone')) {
        ini_set('date.timezone', 'UTC');
    }

    if (file_exists('./vendor/autoload.php')) {
        define('PHPUNIT_COMPOSER_INSTALL', './vendor/autoload.php');
    }


    if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
        fwrite(
            STDERR,
            'You need to set up the project dependencies using Composer:' . PHP_EOL . PHP_EOL .
            '    composer install' . PHP_EOL . PHP_EOL .
            'You can learn all about Composer on https://getcomposer.org/.' . PHP_EOL
        );

        die(1);
    }

    $options = getopt('', array('prepend:'));

    if (isset($options['prepend'])) {
        require $options['prepend'];
    }

    unset($options);


    require './vendor/autoload.php';


    PHPUnit\TextUI\Command::main(false);


});


die(0);


