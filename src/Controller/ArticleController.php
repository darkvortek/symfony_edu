<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/blog", name="article")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository(Article::class)->findAll();

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/blog/single/{post}", name="single_post")
     */
    public function single(Article $post)
    {

        return $this->render('article/single.html.twig', [
            'post' => $post
        ]);
    }
    /**
     * @Route("/blog/create_post", name="create_post")
     */
    public function create(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setCreateAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/blog/update/{post}", name="update_post")
     */
    public function update(Request $request, Article $post)
    {

        $form = $this->createForm(ArticleType::class, $post, [
            'action' => $this->generateUrl('update_post', [
                'post' => $post->getId()
            ]),
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setUpdateAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();

            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('single_post', ['post' => $post->getId()]);

        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/blog/delete/{post}", name="delete_post")
     */
    public function delete(Article $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('article');
    }
}
