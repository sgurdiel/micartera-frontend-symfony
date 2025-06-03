<?php

declare(strict_types=1);

namespace Tests;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

trait TestCaseTrait
{
    protected static ManagerRegistry $registry;
    protected static bool $loadFixtures = false;

    public static function _setUpBeforeClass(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel(['environment' => 'test', 'debug' => false]);
        self::$registry = static::getContainer()->get('doctrine');
        self::$loadFixtures = true;
        self::loadFixtures();
    }

    public function _setUp(): void
    {
        self::loadFixtures();
        $this->resetEntityManager();
    }

    public static function runConsoleCommand($command): void
    {
        $application = new Application(
            static::createKernel(['debug' => false])
        );
        $application->setAutoExit(false);
        $command = sprintf('%s --quiet --env=test', $command);
        $application->run(new StringInput($command));
    }

    private static function loadFixtures(): void
    {
        if (self::$loadFixtures) {
            self::runConsoleCommand('doctrine:fixtures:load');
            self::$loadFixtures = false;
        }
    }

    protected function resetEntityManager(): void
    {
        self::$registry->resetManager();
    }

    protected function detachEntity($entity): void
    {
        self::$registry->getManager()->detach($entity);
    }
}
