<?php

namespace App\Controller;

use App\Classes\Utils;
use App\Entity\BDD\ApiUser;
use App\Entity\BDD\Contact;
use App\Entity\ES\Dso;
use App\Forms\ContactFormType;
use App\Forms\RegisterApiUsersFormType;
use App\Repository\DsoRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Exception\CacheException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PageController
 * @package App\Controller
 */
class PageController extends AbstractController
{
    private DsoRepository $dsoRepository;
    private TranslatorInterface $translator;
    private LoggerInterface $logger;

    /**
     * PageController constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(DsoRepository $dsoRepository, TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->dsoRepository = $dsoRepository;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @Route({
     *     "fr": "/contactez-nous",
     *     "en": "/contact-us",
     *     "de": "/kontaktiere-uns",
     *     "es": "/contactenos",
     *     "pt": "/contate-nos"
     * }, name="contact")
     *
     * @param Request $request
     * @param MailService $mailService
     * @param string $receiverMail
     *
     * @return Response
     */
    public function contact(Request $request, MailService $mailService, string $receiverMail): Response
    {
        /** @var Router $router */
        $router = $this->get('router');

        $isValid = false;
        $optionsForm = [
            'method' => 'POST',
            'action' => $router->generate('contact'),
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ];

        /** @var Contact $contact */
        $contact = new Contact();
        $contactForm = $this->createForm(ContactFormType::class, $contact, $optionsForm);

        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted()) {
            if ($contactForm->isValid()) {
                /** @var Contact $contactData */
                $contactData = $contactForm->getData();
                $contactData->setLabelCountry(Countries::getName($contactData->getCountry(), $request->getLocale()));

                $templates = [
                    'html' => 'includes/emails/contact.html.twig',
                    'text' => 'includes/emails/contact.txt.twig'
                ];

                $subject = '[Contact] - ' . $this->translator->trans(Utils::listTopicsContact()[$contactData->getTopic()]);
                $content['contact'] = $contactData;

                try {
                    $mailService->sendMail($contactData->getEmail(), $receiverMail, $subject, $templates, $content);
                    $sendMail = true;
                } catch(TransportExceptionInterface $e) {
                    $this->logger->error(sprintf('Error sending mail : %s', $e->getMessage()));
                    $sendMail = false;
                }

                if (true === $sendMail) {
                    $this->addFlash('form.success','form.ok.sending');
                    $isValid = true;
                } else {
                    $this->addFlash('form.failed','form.error.sending');
                }
            } else {
                $this->addFlash('form.failed','form.error.message');
            }
        }

        $result['formContact'] = $contactForm->createView();
        $result['is_valid'] = $isValid;

        /** @var Response $response */
        $response = $this->render('pages/contact.html.twig', $result);
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);
        $response->setPublic();

        return $response;
    }

    /**
     * @Route({
     *     "fr": "/mentions-legales",
     *     "en": "/legal-notice",
     *     "de": "/legal-notice",
     *     "es": "/legal-notice",
     *     "pt": "/legal-notice"
     * }, name="legal_notice")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function legalnotice(Request $request): Response
    {
        $result = [];

        /** @var RouterInterface $router */
        $router = $this->get('router');

        $result['title'] = $this->translator->trans('legal_notice.title');
        $result['first_line'] = $this->translator->trans('legal_notice.line_first', ['%dso%' => $this->translator->trans('dso')]);
        $result['second_line'] = $this->translator->trans('legal_notice.line_sec');
        $result['host'] = [
            'name' => $this->translator->trans('legal_notice.host.name'),
            'adress' => $this->translator->trans('legal_notice.host.adress'),
            'cp' => $this->translator->trans('legal_notice.host.cp'),
            'city' => $this->translator->trans('legal_notice.host.city'),
            'country' => $this->translator->trans('legal_notice.host.country')
        ];
        $result['third_line'] = $this->translator->trans('legal_notice.contact', ['%url_contact%' => $router->generate(sprintf('contact.%s', $request->getLocale())), '%label_contact%' => $this->translator->trans('contact.title')]);

        /** @var Response $response */
        $response = $this->render('pages/random.html.twig', $result);
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);

        return $response;
    }


    /**
     * @Route({
     *     "fr": "/aide/api",
     *     "en": "/help/api",
     *     "es": "/help/api",
     *     "de": "/help/api",
     *     "pt": "/help/api"
     * }, name="help_api_page")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function helpApiPage(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $isValid = false;
        $apiUser = new ApiUser();

        $optionsForm = [
            'method' => 'POST',
            'action' => $this->get('router')->generate('help_api_page', ['_locale' => $request->getLocale()])
        ];

        $registerApiUserForm = $this->createForm(RegisterApiUsersFormType::class, $apiUser, $optionsForm);

        $registerApiUserForm->handleRequest($request);
        if ($registerApiUserForm->isSubmitted()) {
            if ($registerApiUserForm->isValid()) {
                /** @var EntityManagerInterface $em */
                $em = $this->getDoctrine()->getManager();

                $apiUser->setPassword(
                    $passwordEncoder->encodePassword($apiUser, $registerApiUserForm->get('rawPassword')->getData())
                );

                $em->persist($apiUser);
                $em->flush();

                $isValid = true;
                $this->addFlash('form.success', 'form.api.success');
            } else {
                $isValid = false;
                $this->addFlash('form.failed', 'form.error.message');
            }
        }

        /**  */
        $result['formRegister'] = $registerApiUserForm->createView();
        $result['is_valid'] = $isValid;

        $response = $this->render('pages/help_api.html.twig', $result);
        $response->setPublic();
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);

        return $response;
    }

    /**
     * @Route({
     *     "fr": "/telechargement-donnees",
     *     "en": "/download-data",
     *     "de": "/download-data",
     *     "es": "/download-data",
     *     "pt": "/download-data",
     * }, name="download_data")
     * @param Request $request
     *
     * @return StreamedResponse
     * @throws \Exception
     */
    public function download(Request $request): StreamedResponse
    {
        $filters = [];

        $header = [
            'Id',
            $this->translator->trans('desigs'),
            'Name',
            $this->translator->trans('type'),
            'Constellation',
            $this->translator->trans('magnitude'),
            $this->translator->trans('ra'),
            $this->translator->trans('dec'),
            $this->translator->trans('distAl')
        ];

        // Retrieve list filters
        if (0 < $request->query->count()) {
            $authorizedFilters = $this->dsoRepository->getListAggregates(true);

            // Removed unauthorized keys
            $filters = array_filter($request->query->all(), static function($key) use($authorizedFilters) {
                return in_array($key, $authorizedFilters, true);
            }, ARRAY_FILTER_USE_KEY);

            // Sanitize data (todo : try better)
            array_walk($filters, static function (&$value, $key) {
                $value = filter_var($value, FILTER_SANITIZE_STRING);
            });
        }

        [$listDso,,] = $this->dsoRepository->setLocale($request->getLocale())->getObjectsCatalogByFilters(0, $filters, DsoRepository::MAX_SIZE, true);
        $data = array_map(function(Dso $dso) {
            return [
                $dso->getId(),
                implode(Utils::COMA_GLUE, array_filter($dso->getDesigs())),
                $dso->getAlt(),
                $this->translator->trans(sprintf('type.%s', $dso->getType())),
                $dso->getConstId(),
                $dso->getMag(),
                $dso->getRa(),
                $dso->getDec(),
                $dso->getDistAl()
            ];
        }, iterator_to_array($listDso));

        $data = array_merge([$header], $data);

        /** @var \DateTime $now */
        $now = new \DateTime();
        $fileName = sprintf('dso_data_%s_%s.csv', $request->getLocale(), $now->format('Ymd_His'));

        /** @var StreamedResponse $response */
        $response = new StreamedResponse(function() use ($data) {
            $handle = fopen('php://output', 'r+');
            foreach ($data as $r) {
                fputcsv($handle, $r, Utils::CSV_DELIMITER, Utils::CSV_ENCLOSURE);
            }
            fclose($handle);
        });

        $response->headers->set('content-type', 'application/force-download');
        $response->headers->set('Content-Disposition', sprintf('attachement; filename="%s"', $fileName));

        return $response;
    }

    /**
     * @Route({
     *   "en": "/skymap",
     *   "fr": "/carte-du-ciel",
     *   "es": "/skymap",
     *   "de": "/skymap",
     *   "pt": "/skymap"
     * }, name="skymap")
     */
    public function skymap(): Response
    {
        $response = $this->render('pages/skymap.html.twig', []);
        $response->setSharedMaxAge(LayoutController::HTTP_TTL)->setPublic();

        return $response;
    }

    /**
     * @Route({
     *   "en": "/support-astro-otter",
     *   "fr": "/soutenir-le-site"
     * }, name="help_astro-otter")
     *
     * @param Request $request
     * @param string|null $paypalLink
     * @param string|null $tipeeeLink
     *
     * @return Response
     */
    public function helpAstroOtter(Request $request, ?string $paypalLink, ?string $tipeeeLink): Response
    {
        $params = [];

        $params['links'] = [
            'paypal' => [
                'label' => ucfirst('paypal'),
                'path' => $paypalLink,
                'blank' => true,
                'icon_class' => 'paypal'
            ],
            'tipeee' => [
                'label' => ucfirst('tipeee'),
                'path' => $tipeeeLink,
                'blank' => true,
                'icon_class' => 'tipeee'
            ]
        ];


        //$response->setPublic()->setSharedMaxAge(LayoutController::HTTP_TTL);

        return $this->render('pages/support.html.twig', $params);
    }

    /**
     * @Route("/notindexed", name="not_indexed")
     * @param DsoRepository $dsoRepository
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function tosearch(DsoRepository $dsoRepository): Response
    {
        $data = [];
        $limit = 313;
        $i = 1;

        $fullArray = array_map(static function($i) {
            return sprintf('Sh2-%d', $i);
        }, range(1, $limit, $i));


        $results = $dsoRepository->getObjectsCatalogByFilters(0, ['catalog' => 'sh'], 1000, true);
        /** @var Dso $dso */
        foreach ($results[0] as $dso) {
            if (0 === stripos($dso->getId(), 'sh')) {
                $data[] = $dso->getId();
            } else {
                $item = preg_grep('/^Sh2-\d*/', $dso->getDesigs());
                if (false !== reset($item)) {
                    $data[] = reset($item);
                }
            }
        }

        usort($data, static function($a, $b) {
            [, $nA] = explode('-', $a);
            [, $nB] = explode('-', $b);

            return $nA > $nB;
        });


        $notIndexedItems = array_diff($fullArray, $data);

        return new Response(print_r(array_values($notIndexedItems)));
    }

    /**
     * @Route("/dmcupphiirjvaesw", name="debug")
     */
    public function debug(): void
    {
        echo phpinfo();
    }
}
