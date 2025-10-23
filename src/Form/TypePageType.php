<?php

namespace App\Form;

use App\Entity\EfClassification;
use App\Entity\TypePageOcr;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypePageType extends AbstractType
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = $this->resolveChoices($options['typePages'] ?? []);

        $builder->add('attribute', ChoiceType::class, [
            'choices' => $choices,
            'placeholder' => 'Sélectionnez un type de page',
            'required' => false,
            'attr' => ['class' => 'form-control  mb-2'],
            'label' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'typePages' => [],
        ]);
    }

    /**
     * @param array<int|string, string> $provided
     * @return array<string, string>
     */
    private function resolveChoices(array $provided): array
    {
        $choices = [];
        foreach ($provided as $value) {
            if (is_string($value) && $value !== '') {
                $choices[$value] = $value;
            }
        }

        if (!empty($choices)) {
            return $choices;
        }

        $entityManager = $this->registry->getManager();

        $rows = $entityManager->createQueryBuilder()
            ->select('DISTINCT e.typeEf AS type')
            ->from(EfClassification::class, 'e')
            ->where('e.typeEf IS NOT NULL')
            ->andWhere('e.typeEf <> :empty')
            ->setParameter('empty', '')
            ->orderBy('e.typeEf', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($rows as $row) {
            $value = $row['type'] ?? null;
            if (is_string($value) && $value !== '') {
                $choices[$value] = $value;
            }
        }

        if (!empty($choices)) {
            return $choices;
        }

        $fallbackRows = $entityManager->createQueryBuilder()
            ->select('DISTINCT tp.labelType AS label')
            ->from(TypePageOcr::class, 'tp')
            ->where('tp.labelType IS NOT NULL')
            ->andWhere('tp.labelType <> :empty')
            ->setParameter('empty', '')
            ->orderBy('tp.labelType', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($fallbackRows as $row) {
            $value = $row['label'] ?? null;
            if (is_string($value) && $value !== '') {
                $choices[$value] = $value;
            }
        }

        return $choices;
    }
}
