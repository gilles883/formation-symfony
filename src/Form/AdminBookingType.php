<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class,
                [   'label'  => "Date d'arrivée",
                    'widget' => 'single_text' ])
            ->add('endDate', DateType::class,
                [   'label'  => "Date de départ",
                    'widget' => 'single_text' ])
            ->add('comment', TextareaType::class,
                [   'label'  => "Commentaire"])
            ->add('booker', EntityType::class,
                [
                    'label'        => "Visiteur",
                    'class'        => User::class,
                    'choice_label' => function($user)
                        { return $user->getFirstName() . " " . strtoupper($user->getLastName()); }
                ])
            ->add('ad', EntityType::class, 
                [
                    'label'        => "Annonce concernée",
                    'class'        => Ad::class,
                    'choice_label' => 'title'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
