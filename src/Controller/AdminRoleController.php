<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\AdminAddType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

class AdminRoleController extends AbstractController
{
    /**
    * @Route("/admin/role", name="admin_roles_index")
    */
    public function index(UserRepository $repo)
    {
        return $this->render('admin/role/index.html.twig', 
            [ 'users' => $repo->findAll() ]
        );
    }

    /**
     * Permet d'ajouter un administrateur
     * 
     * @Route("/admin/role/add", name="admin_role_add")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function addAdmin(RoleRepository $role, Request $request, EntityManagerInterface $manager)
    {
        $user =new User();
        $roleAdm = $role->findOneBy(['title' => 'ROLE_ADMIN']);
         
        $form = $this->createForm(AdminAddType::class);
        $form->handleRequest($request);
        
       if($form->isSubmitted() && $form->isValid())
        {   
            $user = $form->get('users')->getData();
            $user->addUserRole($roleAdm);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "{$user->getFullName()} a bien été rajouté comme administrateur"
            );

            return $this->redirectToRoute("admin_roles_index");
        }

        return $this->render('admin/role/add.html.twig',
            [ 'form' => $form->createView() ]
        );
    }

    /**
     * Permet de supprimer un administrateur
     * 
     * @Route("/admin/role/{id}/delete", name="admin_role_delete")
     * 
     * @param Role $role
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function delete(RoleRepository $role, User $user, EntityManagerInterface $manager)
    {
        if($this->getUser() !== $user)
        {
            $roleAdm = $role->findOneBy(['title' => 'ROLE_ADMIN']);
             
            $user->removeUserRole($roleAdm);
    
            $manager->persist($user);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "l'administrateur {$user->getFullName()} a bien été désactivé"
            );
        } else
        {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas désactiver le compte avec lequel vous êtes identifié "
            );

        }

        return $this->redirectToRoute("admin_roles_index");
    }
}
