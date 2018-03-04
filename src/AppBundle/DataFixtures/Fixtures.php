<?php

/**
 * Created by PhpStorm.
 * User: ftome
 * Date: 03/03/18
 * Time: 17:53
 */
namespace AppBundle\DataFixtures;

use AppBundle\Entity\Client;
use AppBundle\Entity\ClientAddress;
use AppBundle\Entity\ClientOrder;
use AppBundle\Entity\ClientOrderLine;
use AppBundle\Entity\Product;
use AppBundle\Entity\Shop;
use AppBundle\Entity\Shopper;
use AppBundle\Entity\ShopperAssignment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

class Fixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {

        list($client, $address) = $this->loadClient($manager);
        $shoppers = $this->loadShoppers($manager);
        list($mercadona, $lidl) = $this->loadShops($manager);
        $this->loadProducts($manager, $mercadona, $lidl);
        $clientOrder = $this->loadClientOrder($manager, $client, $address, [$mercadona, $lidl]);


        $shopperAssignmentMercadona = new ShopperAssignment();
        $shopperAssignmentMercadona
            ->setClientOrder($clientOrder)
            ->setShop($mercadona)
            ->setShopper($shoppers[0])
            ->setStatus(ShopperAssignment::ASSIGNED);

        $shopperAssignmentLidl = new ShopperAssignment();
        $shopperAssignmentLidl
            ->setClientOrder($clientOrder)
            ->setShop($lidl)
            ->setShopper($shoppers[1])
            ->setStatus(ShopperAssignment::ASSIGNED)
        ;


        $manager->persist($shopperAssignmentLidl);
        $manager->persist($shopperAssignmentMercadona);

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    protected function loadShoppers(ObjectManager $manager)
    {
        $names = ['Fernando', 'Luis', 'Juan'];
        $emails = ['fernando@example.com', 'luis@example.com', 'juan@example.com'];
        $shoppers = [];
        foreach ($names as $i => $name) {
            $shopper = new Shopper();
            $shopper
                ->setName($name)
                ->setEmail($emails[$i])
            ;
            $shoppers[] = $shopper;
            $manager->persist($shopper);
        }
        return $shoppers;
    }

    /**
     * @param ObjectManager $manager
     * @return array
     */
    protected function loadShops(ObjectManager $manager)
    {
        $mercadona = new Shop();
        $mercadona
            ->setName('Mercadona')
            ->setDescription('Description');
        $lidl = new Shop();
        $lidl
            ->setName('Lidl')
            ->setDescription('Description');
        $manager->persist($lidl);
        $manager->persist($mercadona);
        return array($mercadona, $lidl);
    }

    /**
     * @param ObjectManager $manager
     * @param $mercadona
     * @param $lidl
     */
    protected function loadProducts(ObjectManager $manager, $mercadona, $lidl)
    {
        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $shop = (0 === $i % 2) ? $mercadona : $lidl;
            $product
                ->setName('Product Name ' . random_int(0, 1000))
                ->setStatus(Product::STATUS_AVAILABLE)
                ->setCreatedAt(new \DateTime())
                ->setDescription('Descripcion')
                ->setShop($shop)
                ->setPrice(new Money('300.00', new Currency('EUR')));
            $manager->persist($product);
        }
    }

    protected function loadClient(ObjectManager $manager)
    {
        $client = new Client();
        $client
            ->setName('Fernando')
            ->setEmail('fernando@example.com')
            ->setPhone('1312311233')
            ->setLastname('Last Name');
        $address = new ClientAddress();
        $address
            ->setAddress('C Street 55')
            ->setCity('Madrid')
            ->setPostalCode('28013')
            ->setStreetType('Calle')
            ->setCountry('Spain')
            ->setClient($client);

        $manager->persist($address);
        $manager->persist($client);
        return [$client, $address];
    }

    /**
     * @param ObjectManager $manager
     * @param $client
     * @param array $shops
     * @return ClientOrder
     */
    protected function loadClientOrder(ObjectManager $manager, Client $client, ClientAddress $address, array $shops)
    {

        $clientOrder = new ClientOrder();
        $clientOrder
            ->setClient($client)
            ->setAddress($address)
            ->setPrice(new Money('30000', new Currency('EUR')))
            ->setStatus(ClientOrder::PLACED)
            ->setPurchaseDate(new \DateTime())
            ->setSelectedDeliveryDate(new \DateTime());

        $hummusMercadona = new Product();
        $hummusMercadona
            ->setShop($shops[0])
            ->setPrice(new Money('230', new Currency('EUR')))
            ->setName('Hummus')
            ->setDescription('Hummus')
            ->setCreatedAt(new \DateTime())
            ->setStatus(Product::STATUS_AVAILABLE);
        $manager->persist($hummusMercadona);

        $pastaLidl = new Product();
        $pastaLidl
            ->setShop($shops[1])
            ->setPrice(new Money('530', new Currency('EUR')))
            ->setName('Pasta')
            ->setDescription('Pasta')
            ->setCreatedAt(new \DateTime())
            ->setStatus(Product::STATUS_AVAILABLE);
        $manager->persist($pastaLidl);

        $manager->flush();

        $hummusOrderLine = new ClientOrderLine();
        $hummusOrderLine
            ->setProduct($hummusMercadona)
            ->setPrice((new Money($hummusMercadona->getPrice(), new Currency('EUR')))->multiply(3))
            ->setQuantity(3)
            ->setClientOrder($clientOrder);

        $pastaOrderLine = new ClientOrderLine();
        $pastaOrderLine
            ->setPrice(new Money('140', new Currency('EUR')))
            ->setProduct($pastaLidl)
            ->setQuantity(1)
            ->setClientOrder($clientOrder);

        $manager->persist($clientOrder);
        $manager->persist($hummusOrderLine);
        $manager->persist($pastaOrderLine);
        $manager->flush();


        return $clientOrder;
    }
}