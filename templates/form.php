<br><br><br><br>
<div class="row justify-content-center">
    <div class="col-6">
        <div class="shadow-lg p-3 mb-5 bg-body rounded">
            <h4>AES Encryption</h4>
            <div class="card-body">
                <div class="row mb-3">
                    <label for="plaintext_input" class="col-sm-2 col-form-label">Plaintext</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="plaintext_input">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="key_input" class="col-sm-2 col-form-label">Key</label>
                    <div class="col-sm-10">
                        <input minlength="16" maxlength="16" type="text" class="form-control" id="key_input">
                        <p class="small text-muted">Should be 16 characters long</p>
                    </div>
                </div>
            </div>
            <div class="bg-light text-wrap response-container px-1" style="line-break: anywhere"></div>
            <div class="text-end">
                <button onclick="window.AES.encrypt()" type="button" class="btn btn-primary">Encrypt</button>
            </div>
        </div>
    </div>
</div>
