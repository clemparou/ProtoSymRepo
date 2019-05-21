<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Projet;
use App\Entity\Category;
use App\Entity\Comment;

class ProjetFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create('fr_FR');

        // Créer 3 categories fakées
        for($i = 0; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);

            //Créer entre 4 et 6 projets
            for($j = 1; $j <= mt_rand(4, 6); $j++ ){
                $projet = new Projet();

                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                $projet->setTitle($faker->sentence())
                       ->setContent($content)
                       ->setImage($faker->imageUrl())
                       ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                       ->setCategory($category);

                $manager->persist($projet);
                //On donne des commentaires au projet
                for($k = 1;$k <= mt_rand(4,10);$k++) {

                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                    $now = new \DateTime();
                    $days = $now->diff($projet->getCreatedAt())->days;
                    $minimum = '-' . $days . ' days';

                    $comment = new Comment();
                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimum))
                            ->setProjet($projet);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
