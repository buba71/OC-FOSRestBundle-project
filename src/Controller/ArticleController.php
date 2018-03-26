<?php

namespace App\Controller;

use App\Entity\Article;
use App\Exception\ResourceValidationException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;



class ArticleController extends FOSRestController
{
    /**
     * @Rest\Get("/articles", name="app_article_list")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="10",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     */
    public function listAction(ParamFetcherInterface $paramFetcher, EntityManagerInterface $entityManager)
    {
        $pager = $entityManager->getRepository(Article::class)->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );



        return $pager->getCurrentPageResults();
    }

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
    public function createArticle(EntityManagerInterface $entityManager, ConstraintViolationList $violations, UrlGeneratorInterface $urlGenerator, Article $article)
    {

        if (count($violations)){
            $message = 'The Json sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation)
            {
                $message .= sprintf("Field %s: %s", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $entityManager->persist($article);
        $entityManager->flush();


        $url = $urlGenerator->generate('app_article_show', array(
            'id' => $article->getId(),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        return $this->view($article, Response::HTTP_CREATED, ['location' => $url]);
    }



}