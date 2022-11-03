<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $repoArticle;
    private $repoCategory;

    public function __construct(ArticleRepository $repoArticle, CategoryRepository $repoCategory)
    {
        $this->repoArticle = $repoArticle;
        $this->repoCategory = $repoCategory;
    }
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $categories = $this->repoCategory->findAll();
        $articles = array_reverse($this->repoArticle->findAll());
        return $this->render('home/index.html.twig', [
            "articles" => $articles,
            "categories" => $categories,
            "titre" => "Liste des articles"
        ]);
    }

    /**
     * @Route("/showArticles/{id}", name="show_articles")
     */
    public function showArticles(?Category $category): Response
    {
        $categories = $this->repoCategory->findAll();
        if ($category) {
            $articles = $category->getArticles()->getValues();
        } else {
            return $this->redirectToRoute("home");
        }
        return $this->render('home/index.html.twig', [
            "articles" => $articles,
            "categories" =>  $categories,
            "titre" => $category->getTitle()
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show(Article $article=null): Response
    {
        if (!$article) {
            return $this->redirectToRoute('home');
        }
        return $this->render('home/show.html.twig', [
            "article" => $article
        ]);
    }

}
