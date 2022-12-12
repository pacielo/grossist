<?php

namespace App\Controller\AdminManagement;

use App\Entity\UserManagement\Tracking;
use App\Repository\UserManagement\TrackingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TrackingController.
 */
class TrackingController extends AbstractController
{
    public const max_tracking_per_page = 15;

    /**
     * Tracking list.
     *
     * @Route("/admin/UserManagement/tracking/list", name="admin_tracking_list", defaults={"page": "1"},  methods={"GET"})
     * @Route("/admin/UserManagement/tracking/list/{page}", requirements={"page"="\d+"}, name="admin_tracking_list_paginated")
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @throws NotFoundHttpException
     */
    public function list(int $page, TrackingRepository $trackingRepository): Response
    {
        $total = $trackingRepository->getTotal();

        $pagination = [
            'page' => $page,
            'route' => 'admin_tracking_list_paginated',
            'pages_count' => ceil($total / self::max_tracking_per_page),
            'route_params' => [],
        ];

        $trackings = $trackingRepository->findAllAuthenification($page, self::max_tracking_per_page);

        return $this->render('UserManagement/Tracking/list.html.twig', [
                'trackings' => $trackings,
                'pagination' => $pagination,
        ]);
    }

    /**
     * message log liste.
     *
     * @Route("/admin/UserManagement/mailLog/list", name="admin_mail_log_list", defaults={"page": "1"},  methods={"GET"})
     * @Route("/admin/UserManagement/mailLog/list/{page}", requirements={"page"="\d+"}, name="admin_mail_log_list_paginated")
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @throws NotFoundHttpException
     */
    public function listMailLog(int $page): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $total = $manager->getRepository('App\Entity\UserManagement\LoggedMessage')->getMailHistoryTotal();

        $pagination = [
            'page' => $page,
            'route' => 'admin_mail_log_list_paginated',
            'pages_count' => ceil($total / self::max_tracking_per_page),
            'route_params' => [],
        ];

        $loggedMessages = $manager->getRepository('App\Entity\UserManagement\LoggedMessage')->getMailHistoryByPage($page, self::max_tracking_per_page);

        return $this->render('UserManagement/Tracking/listMailLog.html.twig', [
                'loggedMessages' => $loggedMessages,
                'pagination' => $pagination,
        ]);
    }
}
