<?php

class HomeController extends BaseController
{
    private PlanModel $planModel;

    public function __construct()
    {
        $this->planModel = new PlanModel();
    }

    public function index(): void
    {
        $plans = $this->planModel->getPlans();
        $pageTitle = 'Singularys • Provisionamento de VPS';
        $pageDescription = 'Servidores virtuais autogerenciáveis com provisionamento automático e interface clara.';
        $this->render('home', compact('plans', 'pageTitle', 'pageDescription'));
    }

    public function cadastro(): void
    {
        $plans = $this->planModel->getPlans();
        $selectedPlan = null;
        $planCode = $_GET['plano'] ?? ''; 

        if ($planCode) {
            $selectedPlan = $this->planModel->getPlan($planCode);
            if ($selectedPlan) {
                $_SESSION['selected_plan'] = $selectedPlan;
            }
        }

        if (isset($_SESSION['selected_plan'])) {
            $selectedPlan = $_SESSION['selected_plan'];
        }

        $pageTitle = 'Cadastro • Singularys';
        $pageDescription = 'Crie sua conta e finalize a contratação do seu servidor virtual.';
        $this->render('cadastro', compact('plans', 'selectedPlan', 'pageTitle', 'pageDescription'));
    }

    public function dashboard(): void
    {
        if (empty($_SESSION['user'])) {
            $this->redirect('/cadastro');
        }

        $user = $_SESSION['user'];
        $plan = $_SESSION['selected_plan'] ?? $this->planModel->getPlan('essencial');
        $pageTitle = 'Dashboard • Singularys';
        $pageDescription = 'Acompanhe a sua VPS, métricas de uso e ações de suporte em um único painel.';
        $this->render('dashboard', compact('user', 'plan', 'pageTitle', 'pageDescription'));
    }

    public function equipe(): void
    {
        $pageTitle = 'Equipe • Singularys';
        $pageDescription = 'Conheça o time e a filosofia por trás do portal de provisionamento de servidores.';
        $this->render('equipe', compact('pageTitle', 'pageDescription'));
    }

    public function termos(): void
    {
        $pageTitle = 'Termos de Uso • Singularys';
        $pageDescription = 'Leia os termos e condições do serviço de servidores virtuais Singularys.';
        $this->render('termos', compact('pageTitle', 'pageDescription'));
    }
}
