<?php

namespace App\Controller\Api;

use App\Entity\Translation;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/translations')]
class TranslationApiController extends AbstractController
{
    #[Route('/{locale}/{namespace}', methods: ['GET'])]
    public function getTranslations(
        string $locale,
        string $namespace,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        if (!preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $locale)) {
            return $this->json(['error' => 'Invalid locale'], 400);
        }

        $translations = $entityManager->createQueryBuilder()
            ->select('t.tkey, t.tvalue')
            ->from(Translation::class, 't')
            ->where('t.locale = :locale')
            ->andWhere('t.namespace = :namespace')
            ->setParameter('locale', $locale)
            ->setParameter('namespace', $namespace)
            ->getQuery()
            ->getResult();

        $result = [];
        foreach ($translations as $translation) {
            $result[$translation['key']] = $translation['value'];
        }

        return $this->json($result, 200, [
            'Cache-Control' => 'public, max-age=3600', // Cache for 1 hour
        ]);
    }
}
