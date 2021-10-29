<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\Skill;
use App\Form\SkillType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SkillController extends AbstractFOSRestController
{
    /**
     * @Route("/skill", name="skill")
     * @return JsonResponse
     */
    public function getSkillAction()
    {
        $repository = $this->getDoctrine()->getRepository(Skill::class);
        $skill = $repository->findall();
        return $this->handleView($this->view($skill)); //returns json
    }
    /**
     * @Route("/skill ", name="newSkill", methods={"POST"})
     * @return Response
     * @throws \Exception
     */
    public function postSkillAction(Request $request)
    {
        $skills = new Skill();
        $form = $this->createForm(SkillType::class, $skills);
        $data = json_decode($request->getContent(), true);
        $response = [];
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getConnection()->beginTransaction();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($skills);
                $em->flush();
                $this->getDoctrine()->getConnection()->commit();
                return $this->handleView($this->view($skills, Response::HTTP_CREATED));
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
