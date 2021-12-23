<?php

namespace App\DataFixtures;


use App\Entity\VirtualPage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VirtualPageFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $page = new VirtualPage();
        $page->setTitle('Книги');
        $page->setSlug('books/allbooks/');
        $page->setIsMenu(true);
        $page->setLevel(0);
        $manager->persist($page);

        /** new page */
        $page1 = new VirtualPage();
        $page1->setTitle('Проза');
        $page1->setSlug('fiction/');
        $page1->setIsMenu(true);
        $page1->setLevel(1);
        $page1->setParentVirtualPage($page);
        $manager->persist($page1);

        /** new page */
        $page2 = new VirtualPage();
        $page2->setTitle('Аудиокниги');
        $page2->setSlug('tag/audioknigi/');
        $page2->setIsMenu(true);
        $page2->setLevel(1);
        $page2->setParentVirtualPage($page);
        $manager->persist($page2);

        /** new page */
        $page3 = new VirtualPage();
        $page3->setTitle('Бизнес');
        $page3->setSlug('business-books/');
        $page3->setIsMenu(true);
        $page3->setLevel(1);
        $page3->setParentVirtualPage($page);
        $manager->persist($page3);

        /** new page */
        $pageGame = new VirtualPage();
        $pageGame->setTitle('Игры');
        $pageGame->setSlug('games/');
        $pageGame->setIsMenu(true);
        $pageGame->setLevel(0);
        $manager->persist($pageGame);

        /** new page */
        $pageItem = new VirtualPage();
        $pageItem->setTitle('Штуки');
        $pageItem->setSlug('games/');
        $pageItem->setIsMenu(true);
        $pageItem->setLevel(0);
        $manager->persist($pageItem);

        /** save all */
        $manager->flush();
    }
}