<?php

class OrderController extends BaseController
{
    private PlanModel $planModel;
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->planModel = new PlanModel();
        $this->orderModel = new OrderModel();
    }

    public function cart(): void
    {
        $selectedPlan = $_SESSION['selected_plan'] ?? $this->planModel->getPlan('essencial');
        $pageTitle = 'Carrinho • Singularys';
        $pageDescription = 'Revise o plano selecionado e finalize o seu pedido com PIX ou cartão.';
        $this->render('carrinho', compact('selectedPlan', 'pageTitle', 'pageDescription'));
    }

    public function checkout(): void
    {
        if (empty($_SESSION['user'])) {
            $_SESSION['flash_error'] = 'Faça login ou cadastro antes de finalizar o pedido.';
            $this->redirect('/login');
        }

        $selectedPlan = $_SESSION['selected_plan'] ?? $this->planModel->getPlan('essencial');
        if (!$selectedPlan) {
            $_SESSION['flash_error'] = 'Plano inválido. Selecione um plano antes de continuar.';
            $this->redirect('/cadastro');
        }

        $orderData = [
            'usuario_id' => $_SESSION['user']['id_usuario'],
            'plano_id' => $selectedPlan['id_plano'] ?? null,
            'status' => 'pago',
            'valor_total' => $selectedPlan['price'] ?? 29.90,
            'criado_em' => date('Y-m-d H:i:s'),
        ];

        try {
            $order = $this->orderModel->create($orderData);
            $_SESSION['last_order'] = $order;
            $_SESSION['flash_success'] = 'Pagamento aprovado e VPS provisionada com sucesso!';
            $this->redirect('/dashboard');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Não foi possível concluir o pedido: ' . $e->getMessage();
            $this->redirect('/carrinho');
        }
    }
}
