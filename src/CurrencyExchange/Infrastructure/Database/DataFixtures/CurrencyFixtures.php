<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Database\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture //implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
//        $bitcoin = new Currency('BTC', BitcoinClient::getCurrencyAdapterName());
//        $monero = new Currency('XMR', MoneroClient::getCurrencyAdapterName());
//
//        $manager->persist($bitcoin);
//        $manager->persist($monero);
//
//        $manager->flush();
    }

//    public function getDependencies()
//    {
//
//    }
}
