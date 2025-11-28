<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'required' => true,
                'label' => 'Type',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('value', TextType::class, [
                'required' => true,
                'label' => 'Value',
                'attr' => ['class' => 'form-control'],
            ])
            // usa il nome della proprietà dell'entità: contactBy (non contact_by)
            ->add('contactBy', TextType::class, [
                'required' => false,
                'label' => 'Contact by',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('label', TextType::class, [
                'required' => false,
                'label' => 'Label',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary mt-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
