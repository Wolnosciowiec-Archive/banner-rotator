<?php declare(strict_types = 1);

namespace App\Form;

use App\Entity\BannerGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see BannerGroup
 */
class BannerGroupForm extends AbstractType
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
            ->add('description', TextType::class, [
                'empty_data' => '',
            ]);

        if ($options['trait_choices']['is_new'] ?? false) {
            $builder->add('id', TextType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => BannerGroup::class,
            'trait_choices' => [],
        ]);
    }
}
