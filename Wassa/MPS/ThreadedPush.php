<?php

namespace Wassa\MPS;

/**
 * ThreadedPush
 */
class ThreadedPush extends \Thread
{
    private $loader;

    public function startWithClassLoader($option, $loader)
    {
        $this->loader = $loader;
        $this->loader->add('Wassa\\MPS\\', __DIR__ . '/../../../../../wassa/mobile-push-server/lib/');
        parent::start($option);
    }

    public function run()
    {
        $loader = require __DIR__ . '/../../../../../autoload.php';
        $loader->add('Wassa\\MPS\\', __DIR__ . '/../../../../../wassa/mobile-push-server/lib/');
        $push = new IosPush(null, null);
    }
}
