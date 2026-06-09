<fieldset class="border rounded-3 p-3 mb-4">
    <legend class="float-none w-auto px-2 fs-6 fw-semibold mb-0">Acesso ao portal</legend>
    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <label class="form-label" for="email">E-mail</label>
            <input type="email" class="form-control" id="email" name="email"
                   value="<?php echo htmlspecialchars($dados["email"] ?? ""); ?>">
            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["email"] ?? ""); ?></small>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="senha">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" autocomplete="new-password">
            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["senha"] ?? ""); ?></small>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="senha_confirmacao">Confirme a senha</label>
            <input type="password" class="form-control" id="senha_confirmacao" name="senha_confirmacao" autocomplete="new-password">
            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["senha_confirmacao"] ?? ""); ?></small>
        </div>
    </div>
</fieldset>
