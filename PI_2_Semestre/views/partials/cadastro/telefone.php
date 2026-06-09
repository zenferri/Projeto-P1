<fieldset class="border rounded-3 p-3 mb-4">
    <legend class="float-none w-auto px-2 fs-6 fw-semibold mb-0">Telefone de contato</legend>
    <div class="row g-3 mt-1">
        <div class="col-md-3">
            <label class="form-label" for="codigo_pais">País</label>
            <input class="form-control" id="codigo_pais" name="codigo_pais" maxlength="5"
                   value="<?php echo htmlspecialchars($dados["codigo_pais"] ?? "55"); ?>">
            <small class="text-muted">Ex.: 55 (Brasil)</small>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="ddd">DDD</label>
            <input class="form-control" id="ddd" name="ddd" required maxlength="3"
                   inputmode="numeric" placeholder="11"
                   value="<?php echo htmlspecialchars($dados["ddd"] ?? ""); ?>">
            <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["ddd"] ?? ""); ?></small>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="numero_telefone">Número (sem DDD)</label>
            <input class="form-control" id="numero_telefone" name="numero_telefone" required maxlength="11"
                   inputmode="numeric" placeholder="Celular: 9 dígitos / Fixo: 8 dígitos"
                   value="<?php echo htmlspecialchars($dados["numero_telefone"] ?? ""); ?>">
            <small class="text-muted">Celular: 9 dígitos começando com 9. Fixo: 8 dígitos.</small>
            <small class="text-danger mvc-error d-block"><?php echo htmlspecialchars($msg["numero_telefone"] ?? ""); ?></small>
        </div>
        <?php if (!empty($msg["telefone"])) { ?>
            <div class="col-12">
                <small class="text-danger mvc-error"><?php echo htmlspecialchars($msg["telefone"]); ?></small>
            </div>
        <?php } ?>
    </div>
</fieldset>
