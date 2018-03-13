<?php
declare(strict_types=1);

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication(): \Laravel\Lumen\Application
    {
        /** @noinspection UsingInclusionReturnValueInspection Inherited from Lumen */
        return require __DIR__ . '/../bootstrap/app.php';
    }
}
