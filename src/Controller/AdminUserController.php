<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users_index")
     */
    public function index(UserRepository $repo)
    {
        return $this->render('admin/user/index.html.twig',
            [  'users' => $repo->findAll() ]
        );
    }

    /**
     * Permet d'éditer un utilisateur
     * 
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminUserType::class, $user);
 
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "l'utilisateur {$user->getFullName()} a bien été modifié"
            );

            return $this->redirectToRoute("admin_users_index");
        }

        return $this->render('admin/user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user
            ]
        );
    }

    /**
     * Permet de supprimer un utilisateur
     * 
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $manager)
    {
        if (count($user->getBookings()) > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'utilisateur <strong>{$user->getFullName()}</strong> car il possède des réservations!"
            );
        } elseif(count($user->getAds()) > 0)
        {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'utilisateur <strong>{$user->getFullName()}</strong> car il possède des annonces!"
            );
        } else
        {
            $manager->remove($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "l'utilisateur a bien été supprimé"
            );
        }
        return $this->redirectToRoute("admin_users_index");
    }
}
