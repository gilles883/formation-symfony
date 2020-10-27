<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="admin_comments_index")
     */
    public function index(CommentRepository $repo)
    {
        return $this->render('admin/comment/index.html.twig',
            [ 'comments' => $repo->findAll() ]
        );
    }

    /**
     * Permet d'éditer et de modifier un commentaire
     *
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     * 
     * @param Comment $comment
     * @param Request $request
     * @param EntityManagerInterface $entityManagerInterface
     * @return Response
     */
    public function edit(Comment $comment, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire numéro <strong>{$comment->getId()}</strong> a bien été modifié"
            );
            return $this->redirectToRoute('admin_comments_index');
        }

        return $this->render('admin/comment/edit.html.twig',
            [
                'comment' => $comment,
                'form'    => $form->createView()
            ]
        );

    }

    /**
     * Permet de supprimer un commentaire
     *
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     * 
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Comment $comment, EntityManagerInterface $manager)
    {
       $manager->remove($comment);
       $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de {$comment->getAuthor()->getFullName()} à bien été supprimé!"
        );

        return $this->redirectToRoute('admin_comments_index');
    }
}
