<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Storage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Storage controller.
 *
 * @Route("storage")
 */
class StorageController extends Controller
{
    /**
     * Lists all storage entities.
     *
     * @Route("/", name="storage_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $storages = $em->getRepository('AppBundle:Storage')->findAllByUser($this->getUser());

        return $this->render('storage/index.html.twig', array(
            'storages' => $storages,
        ));
    }

    /**
     * Creates a new storage entity.
     *
     * @Route("/new", name="storage_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $storage = new Storage();
        $form = $this->createForm('AppBundle\Form\StorageType', $storage, array(
            'user' => $this->getUser(),
            'picturePath' => $storage->getPictureWebPath()
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $storage->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($storage);
            $em->flush();

            return $this->redirectToRoute('storage_show', array('id' => $storage->getId()));
        }

        return $this->render('storage/new.html.twig', array(
            'storage' => $storage,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a storage entity.
     *
     * @Route("/{id}", name="storage_show")
     * @Method("GET")
     */
    public function showAction(Storage $storage)
    {
        $doctrine = $this->getDoctrine();

        $repo = $doctrine->getRepository("AppBundle:Product");
        $products = $repo->findProductsByStorageAndUser($storage, $this->getUser());

        $categoryRepo = $doctrine->getRepository('AppBundle:Category');
        $storageRepo = $doctrine->getRepository('AppBundle:Storage');


        return $this->render('storage/show.html.twig', array(
            'storage' => $storage,
            'products' => $products,
            'categoryRepo' => $categoryRepo,
            'storageRepo' => $storageRepo
        //    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing storage entity.
     *
     * @Route("/{id}/edit", name="storage_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Storage $storage)
    {
        $deleteForm = $this->createDeleteForm($storage);
        $editForm = $this->createForm('AppBundle\Form\StorageType', $storage, array(
            'user' => $this->getUser(),
            'picturePath' => $storage->getPictureWebPath()
        ));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('storage_edit', array('id' => $storage->getId()));
        }

        return $this->render('storage/edit.html.twig', array(
            'storage' => $storage,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a storage entity.
     *
     * @Route("/{id}", name="storage_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Storage $storage)
    {
        $form = $this->createDeleteForm($storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($storage);
            $em->flush();
        }

        return $this->redirectToRoute('storage_index');
    }

    /**
     * Creates a form to delete a storage entity.
     *
     * @param Storage $storage The storage entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Storage $storage)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('storage_delete', array('id' => $storage->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}