<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\Formation;
use App\Form\FormationType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FormationController extends AbstractFOSRestController
{
    /**
     * @Route("/formation", name="formation")
     * @return JsonResponse

     */
    public function getFormationAction()
    {
        $repository = $this->getDoctrine()->getRepository(Formation::class);
        $formations = $repository->findall();
        return $this->handleView($this->view($formations)); //returns json
    }
    /**
     * @Route("/formation", name="newFormation", methods={"POST"})
     * @return Response
     * @throws \Exception
     */
    public function postFormationAction(Request $request)
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $data = json_decode($request->getContent(), true);
        $response = [];
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getConnection()->beginTransaction();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($formation);
                $em->flush();
                $this->getDoctrine()->getConnection()->commit();
                return $this->handleView($this->view($formation, Response::HTTP_CREATED));
            } catch (\Exception $e) {
                // throw $e;
                $this->getDoctrine()->getConnection()->rollback();
            }
        } else {
            $errors = [];
            foreach ($form->getErrors(true, true) as $formError) {
                $errors[] = $formError->getMessage();
            }

            $response["errors"] = $errors;
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
