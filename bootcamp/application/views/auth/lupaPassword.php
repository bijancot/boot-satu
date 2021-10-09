<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?= $title; ?></title>
    <link href="<?= base_url('assets/css/styles.css'); ?>" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Password Recovery</h3>
                                </div>
                                <div class="card-body">
                                    <div class="small mb-3 text-muted">Masukkan alamat email Anda dan kami akan mengirimkan
                                        tautan untuk mengatur ulang kata sandi Anda.</div>
                                    <?= $this->session->flashdata('message'); ?>
                                    <form method="POST" action="<?= site_url('auth/lupa-password'); ?>">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" />
                                            <label for="inputEmail">Alamat Email</label>
                                            <?= form_error('email', '<small class="text-danger pl-3">', '</small>'); ?>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="<?= site_url('auth'); ?>">Kembali ke login</a>
                                            <button class="btn btn-primary" type="submit">Reset Password</button>
                                        </div>
                                    </form>
                                </div>
                                <div class=" card-footer text-center py-3">
                                    <div class="small"><a href="<?= site_url('auth/registrasi'); ?>">Butuh akun? Silahkan daftar</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/js/scripts.js'); ?>"></script>
</body>

</html>