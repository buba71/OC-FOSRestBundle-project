<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleController extends FOSRestController
{
    /**
     * @Get(
     *     path= "/articles/{id}",
     *     name= "app_article_show",
     *     requirements= {"id" = "\d+"}
     * )
     * @View()
     */
    public function showArticle()
    {
        $article = new Article();
        $article
            ->setTitle('Titre de mon premier article')
            ->setContent('Le contenu de on premier article');

        return $article;

    }

    /**
     * @Rest\Post(
     *     path = "/articles",
     *     name = "app_article_create"
     * )     *
     * @View(StatusCode = 201)
     * @ParamConverter("article", converter="fos_rest.request_body")
     */
    public function createArticle(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, Article $article)
    {
        $entityManager->persist($article);
        $entityManager->flush();


        $url = $urlGenerator->generate('app_article_show', array(
            'id' => $article->getId(),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        return $this->view($article, Response::HTTP_CREATED, ['location' => $url]);
    }

    /**
     * @Get("/articles", name = "app_article_list")
     * @View()
     */
    public function listArticles(EntityManagerInterface $entityManager)
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $articles;

    }

}