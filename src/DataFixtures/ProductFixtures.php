<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    /**
     * Example code!
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
//        $count = 15;
//        for ($i = 0; $i < $count; $i++) {
//            $product = new Product();
//
//            $product->setMasterCode('123-1232-123');
//            $product->setFullName('Выход рядом #' . $i);
//            $product->setDescription('Всестороннее исследование депрессии от человека, который побывал по ту сторону «большой Д» и сумел выкарабкаться.
//            Расскажет, как помочь себе или близкому человеку.
//            Книга создана при участии психолога, нейробиолога и психиатров.');
//            $product->setSlug('iz-depressii');
//            $product->setType('paper_book');
//            $product->setPrimaryName('Из депрессии primaryName');
//            $product->setSecondaryName('Из депрессии secondaryName');
//            $product->setUri('books/iz-depressii/');
//            $product->setImage('');
//            $product->setLifeCycleStatus(1);
//            $product->setReleaseData('2020-05-19');
//            $product->setStartSaleDate(\DateTime::createFromFormat('Y-m-d', '2020-05-29'));
//            $product->setInfoInBuyBlock('Всестороннее исследование депрессии от человека, который побывал по ту сторону «большой Д» и сумел выкарабкаться.
//            Расскажет, как помочь себе или близкому человеку.
//            Книга создана при участии психолога, нейробиолога и психиатров.');
//            $product->setBannerPlaceName('banner_name');
//            $product->setIsDimensionlessForPresent(true);
//            // create detail product, Paper book
//            $detailProduct = new PaperBook();
//            $detailProduct->setPriceLabyrinth(123);
//            $detailProduct->setLinkLabyrinth('https://labirint.ru');
//            $detailProduct->setLinkOzon('https://ozone.ru');
//            $detailProduct->setLinkKnigaBiz('https://knigabiz.ru');
//            $detailProduct->setRightsExpiration(\DateTime::createFromFormat('Y-m-d', '2022-06-19'));
//            $detailProduct->setProduct($product);
//            // create essence product, Paper book
//            $essence = new Book();
//            $detailProduct->setEssence($essence);
//            $essence->setIdPublisher('id-publisher');
//            $essence->setOriginalPrimaryName('Из депрессии...');
//            $essence->setOriginalSecondaryName('Из депрессии...');
//            $essence->setCoverImage('assets/images/books-new/nikogda-nibud/NeverEver_big.png');
//            $essence->setSpineImage('assets/images/books-new/nikogda-nibud/NeverEver_big.png');
//            $essence->setSpineImage('assets/images/books-new/nikogda-nibud/NeverEver_big.png');
//            $essence->setVolumeImage('assets/images/books-new/nikogda-nibud/NeverEver_big.png');
//            $essence->setWorkDescription('мы писали, мы писали - наши пальчики устали.');
//
//            $manager->persist($product);
//            $manager->persist($detailProduct);
//            $manager->persist($essence);
//        }
//
//        $manager->flush();
    }
}
