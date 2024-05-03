<?php

namespace OHMedia\TestimonialBundle\Controller;

use Doctrine\DBAL\Connection;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\SecurityBundle\Form\DeleteType;
use OHMedia\TestimonialBundle\Entity\Testimonial;
use OHMedia\TestimonialBundle\Form\TestimonialType;
use OHMedia\TestimonialBundle\Repository\TestimonialRepository;
use OHMedia\TestimonialBundle\Security\Voter\TestimonialVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class TestimonialController extends AbstractController
{
    private const CSRF_TOKEN_REORDER = 'testimonial_reorder';

    #[Route('/testimonials', name: 'testimonial_index', methods: ['GET'])]
    public function index(TestimonialRepository $testimonialRepository): Response
    {
        $newTestimonial = new Testimonial();

        $this->denyAccessUnlessGranted(
            TestimonialVoter::INDEX,
            $newTestimonial,
            'You cannot access the list of testimonials.'
        );

        $testimonials = $testimonialRepository->createQueryBuilder('t')
            ->orderBy('t.ordinal', 'asc')
            ->getQuery()
            ->getResult();

        return $this->render('@OHMediaTestimonial/testimonial/testimonial_index.html.twig', [
            'testimonials' => $testimonials,
            'new_testimonial' => $newTestimonial,
            'attributes' => $this->getAttributes(),
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/testimonials/reorder', name: 'testimonial_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        TestimonialRepository $testimonialRepository,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted(
            TestimonialVoter::INDEX,
            new Testimonial(),
            'You cannot reorder the testimonials.'
        );

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $testimonials = $request->request->all('order');

        $connection->beginTransaction();

        try {
            foreach ($testimonials as $ordinal => $id) {
                $testimonial = $testimonialRepository->find($id);

                if ($testimonial) {
                    $testimonial->setOrdinal($ordinal);

                    $testimonialRepository->save($testimonial, true);
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/testimonial/create', name: 'testimonial_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        TestimonialRepository $testimonialRepository
    ): Response {
        $testimonial = new Testimonial();

        $this->denyAccessUnlessGranted(
            TestimonialVoter::CREATE,
            $testimonial,
            'You cannot create a new testimonial.'
        );

        $form = $this->createForm(TestimonialType::class, $testimonial);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $testimonialRepository->save($testimonial, true);

            $this->addFlash('notice', 'The testimonial was created successfully.');

            return $this->redirectToRoute('testimonial_index');
        }

        return $this->render('@OHMediaTestimonial/testimonial/testimonial_create.html.twig', [
            'form' => $form->createView(),
            'testimonial' => $testimonial,
        ]);
    }

    #[Route('/testimonial/{id}/edit', name: 'testimonial_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Testimonial $testimonial,
        TestimonialRepository $testimonialRepository
    ): Response {
        $this->denyAccessUnlessGranted(
            TestimonialVoter::EDIT,
            $testimonial,
            'You cannot edit this testimonial.'
        );

        $form = $this->createForm(TestimonialType::class, $testimonial);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $testimonialRepository->save($testimonial, true);

            $this->addFlash('notice', 'The testimonial was updated successfully.');

            return $this->redirectToRoute('testimonial_index');
        }

        return $this->render('@OHMediaTestimonial/testimonial/testimonial_edit.html.twig', [
            'form' => $form->createView(),
            'testimonial' => $testimonial,
        ]);
    }

    #[Route('/testimonial/{id}/delete', name: 'testimonial_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        Testimonial $testimonial,
        TestimonialRepository $testimonialRepository
    ): Response {
        $this->denyAccessUnlessGranted(
            TestimonialVoter::DELETE,
            $testimonial,
            'You cannot delete this testimonial.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $testimonialRepository->remove($testimonial, true);

            $this->addFlash('notice', 'The testimonial was deleted successfully.');

            return $this->redirectToRoute('testimonial_index');
        }

        return $this->render('@OHMediaTestimonial/testimonial/testimonial_delete.html.twig', [
            'form' => $form->createView(),
            'testimonial' => $testimonial,
        ]);
    }

    private function getAttributes(): array
    {
        return [
            'create' => TestimonialVoter::CREATE,
            'delete' => TestimonialVoter::DELETE,
            'edit' => TestimonialVoter::EDIT,
        ];
    }
}
