<?php declare(strict_types = 1);

namespace App\Infrastructure\Form;

use App\Domain\Entity\BannerGroup;
use App\Form\BannerGroupForm;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @see BannerGroup
 */
class NewGroupFormType extends GroupFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('id', TextType::class);
    }
}
