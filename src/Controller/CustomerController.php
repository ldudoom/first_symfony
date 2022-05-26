<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Customer as StripeCustomer;

use Symfony\Component\Serializer\Encoder\JsonDecoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/customer')]
class CustomerController extends AbstractController
{
    protected string $_stripeSecretKey;
    protected StripeClient $_stripeClient;

    protected $_encoders;
    protected $_normalizers;
    protected $_serializer;

    public function __construct()
    {
        $this->_stripeSecretKey = $_ENV["STRIPE_SECRET_KEY"];
        Stripe::setApiKey($this->_stripeSecretKey);
        $this->_stripeClient = new StripeClient($this->_stripeSecretKey);

        $this->_encoders = [new XmlEncoder(), new JsonEncoder()];
        $this->_normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($this->_normalizers, $this->_encoders);
    }

    #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    public function index(CustomerRepository $customerRepository): Response
    {
        //return $this->json(StripeCustomer::all(), Response::HTTP_OK);
        return $this->render('customer/index.html.twig', [
           // 'customers_entity' => $customerRepository->findAll(),
            'customers' => StripeCustomer::all(),
        ]);
        //$customer = StripeCustomer::retrieve('cus_LXSfDXJ7TxQrCo');

        //return $this->json($customer);
        //return $this->json($this->_serializer);
        //$jsonContent = $this->_serializer->serialize($customer, 'xml');
        //return new Response($jsonContent);
        //return $this->json($jsonContent);
    }



    #[Route('/new', name: 'app_customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CustomerRepository $customerRepository): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer);
            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show($id = null): Response
    {
        return $this->render('customer/show.html.twig', [
            'customer' => StripeCustomer::retrieve($id),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer);
            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['POST'])]
    public function delete(Request $request, $id = null): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id, $request->request->get('_token'))) {
            $this->_stripeClient->customers->delete($id);
        }

        return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
