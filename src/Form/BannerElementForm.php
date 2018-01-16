<?php declare(strict_types = 1);

namespace App\Form;

use App\Entity\BannerElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see BannerElement
 */
class BannerElementForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active',      CheckboxType::class, [
                'empty_data' => false,
            ])
            ->add('title',       TextType::class, [
                'empty_data' => '',
            ])
            ->add('expiresAt',   DateTimeType::class, [
                'empty_data' => '',
                'required'   => false,
            ])
            ->add('url',         UrlType::class, [
                'empty_data' => '',
            ])
            ->add('imageUrl',    UrlType::class, [
                'empty_data' => '',
            ])
            ->add('description', TextType::class, [
                'empty_data' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => BannerElement::class,
            'trait_choices' => [],
        ]);
    }
}
