<?php declare(strict_types = 1);

namespace App\Infrastructure\Form;

use App\Domain\Entity\BannerGroup;
use App\Domain\Form\GroupForm;
use App\Form\BannerGroupForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see BannerGroup
 */
class GroupFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active',      CheckboxType::class)
            ->add('title',       TextType::class, [
                'empty_data' => '',
            ])
            ->add('description', TextType::class, [
                'empty_data' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => GroupForm::class
        ]);
    }
}
