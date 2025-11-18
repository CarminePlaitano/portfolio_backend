<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ContactDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $telephoneContact = new Contact();
        $telephoneContact->setType('telephone');
        $telephoneContact->setContactBy('telephone');
        $telephoneContact->setValue('+393272061751');
        $telephoneContact->setLabel('+39 327 2061 751');

        $manager->persist($telephoneContact);

        $whatsAppContact = new Contact();
        $whatsAppContact->setType('whatsapp');
        $whatsAppContact->setContactBy('link');
        $whatsAppContact->setValue('https://wa.me/393272061751');
        $whatsAppContact->setLabel('+39 327 2061 751');

        $manager->persist($whatsAppContact);

        $emailContact = new Contact();
        $emailContact->setType('email');
        $emailContact->setContactBy('link');
        $emailContact->setValue('c.plaitano@gmail.com');

        $manager->persist($emailContact);

        $instagramContact = new Contact();
        $instagramContact->setType('instagram');
        $instagramContact->setContactBy('link');
        $instagramContact->setValue('https://ig.me/m/carmineplaitano.it');
        $instagramContact->setLabel('@carmineplaitano.it');

        $manager->persist($instagramContact);

        $linkedinContact = new Contact();
        $linkedinContact->setType('linkedin');
        $linkedinContact->setContactBy('link');
        $linkedinContact->setValue('https://www.linkedin.com/in/carmine-plaitano-45b286218/');
        $linkedinContact->setLabel('linkedin.com/in/carmineplaitano');

        $manager->persist($linkedinContact);

        $githubContact = new Contact();
        $githubContact->setType('github');
        $githubContact->setContactBy('link');
        $githubContact->setValue('https://github.com/CarminePlaitano');
        $githubContact->setLabel('github.com/CarminePlaitano');
        
        $manager->persist($githubContact);

        $manager->flush();
    }
}
