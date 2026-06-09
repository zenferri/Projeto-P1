<fieldset class="border rounded-3 p-3 mb-4">
    <legend class="float-none w-auto px-2 fs-6 fw-semibold mb-0">Endereço</legend>
    <div class="row g-3 mt-1">
        <div class="col-md-5">
            <label class="form-label" for="cep">CEP</label>
            <input class="form-control" id="cep" name="cep" value="<?php echo htmlspecialchars($dados["cep"] ?? ""); ?>">
            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["cep"] ?? ""); ?></small>
        </div>
        <div class="col-md-7">
            <label class="form-label" for="logradouro">Logradouro</label>
            <input class="form-control" id="logradouro" name="logradouro" value="<?php echo htmlspecialchars($dados["logradouro"] ?? ""); ?>">
            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["logradouro"] ?? ""); ?></small>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="numero">Número</label>
            <input class="form-control" id="numero" name="numero" value="<?php echo htmlspecialchars($dados["numero"] ?? ""); ?>">
            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["numero"] ?? ""); ?></small>
        </div>
        <div class="col-md-5">
            <label class="form-label" for="complemento">Complemento</label>
            <input class="form-control" id="complemento" name="complemento" value="<?php echo htmlspecialchars($dados["complemento"] ?? ""); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="bairro">Bairro</label>
            <input class="form-control" id="bairro" name="bairro" value="<?php echo htmlspecialchars($dados["bairro"] ?? ""); ?>">
            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["bairro"] ?? ""); ?></small>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="cidade">Cidade</label>
            <input class="form-control" id="cidade" name="cidade" value="<?php echo htmlspecialchars($dados["cidade"] ?? ""); ?>">
            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["cidade"] ?? ""); ?></small>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="estado">UF</label>
            <input class="form-control text-uppercase" id="estado" name="estado" maxlength="2"
                   value="<?php echo htmlspecialchars($dados["estado"] ?? ""); ?>">
            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["estado"] ?? ""); ?></small>
        </div>
    </div>
</fieldset>
