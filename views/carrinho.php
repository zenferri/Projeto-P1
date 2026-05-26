<?php require __DIR__ . '/layout/header.php'; ?>

<section class="py-5 bg-light-soft">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title fw-bold mb-4">Resumo do seu pedido</h3>

                        <?php if (!empty($selectedPlan)): ?>
                            <div class="table-responsive mb-4">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($selectedPlan['title']) ?></td>
                                            <td class="text-end text-muted">1x</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="small text-muted"><?= htmlspecialchars($selectedPlan['cpu']) ?> · <?= htmlspecialchars($selectedPlan['ram']) ?> · <?= htmlspecialchars($selectedPlan['storage']) ?></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="text-muted">Ciclo de faturamento</td>
                                            <td class="text-end">Mensal</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <strong>Ótima escolha!</strong> Você pode adicionar mais VPS a qualquer momento.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?= BASE_URL ?>/checkout" id="checkoutForm" novalidate>
                            <input type="hidden" name="paymentMethod" id="paymentMethod" value="pix">

                            <h5 class="fw-bold mt-4 mb-4">Método de pagamento</h5>

                            <ul class="nav nav-pills mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pixTab" data-bs-toggle="tab" data-bs-target="#pixPanel" type="button" role="tab">💳 PIX</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="cardTab" data-bs-toggle="tab" data-bs-target="#cardPanel" type="button" role="tab">💰 Cartão de crédito</button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pixPanel" role="tabpanel">
                                    <div class="card bg-white border-0 shadow-sm">
                                        <div class="card-body p-4 text-center">
                                            <p class="text-muted mb-3">Código PIX para cópia e cola:</p>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control text-center font-monospace" id="pixCode" value="00020126360014br.gov.bcb.brcode010521.0.0" readonly>
                                                <button class="btn btn-outline-primary" type="button" id="copyPixBtn" title="Copiar código PIX">📋 Copiar</button>
                                            </div>
                                            <small class="text-muted d-block">O código expira em 1 hora</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="cardPanel" role="tabpanel">
                                    <div class="card bg-white border-0 shadow-sm">
                                        <div class="card-body p-4">
                                            <div class="mb-3">
                                                <label for="cardName" class="form-label">Nome do titular</label>
                                                <input type="text" class="form-control" id="cardName" name="cardName" placeholder="Maria Silva" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="cardNumber" class="form-label">Número do cartão</label>
                                                <input type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="cardExpiry" class="form-label">Validade</label>
                                                    <input type="text" class="form-control" id="cardExpiry" name="cardExpiry" placeholder="MM/AA" maxlength="5" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="cardCvv" class="form-label">CVV</label>
                                                    <input type="text" class="form-control" id="cardCvv" name="cardCvv" placeholder="***" maxlength="3" required>
                                                </div>
                                            </div>
                                            <div class="small text-muted">Os dados do cartão são usados apenas para demonstrar o fluxo de checkout.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-4">Finalizar pedido</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Valor a pagar</h5>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span>R$ <?= !empty($selectedPlan) ? number_format($selectedPlan['price'], 2, ',', '.') : '0,00' ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Taxa de processamento</span>
                                <span>R$ 0,00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong class="fs-5 text-primary">R$ <?= !empty($selectedPlan) ? number_format($selectedPlan['price'], 2, ',', '.') : '0,00' ?></strong>
                            </div>
                        </div>

                        <div class="alert alert-light border border-success text-success small">
                            ✓ Conta criada com sucesso!<br>
                            Finalize o pagamento para ativar seu VPS.
                        </div>

                        <small class="text-muted d-block">
                            <strong>Próximos passos:</strong>
                            <ol class="small mb-0 ps-3">
                                <li>Confirme o pagamento</li>
                                <li>Escolha a localização do servidor</li>
                                <li>Configure o seu VPS</li>
                            </ol>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/layout/footer.php'; ?>
