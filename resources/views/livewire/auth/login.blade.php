<div>
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); /* Gradasi Biru Sporty */
            min-height: 100vh;
            margin-top: 12%;
            /* display: flex; */
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .btn-football {
            background-color: #1e3c72;
            color: white;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-football:hover {
            background-color: #2a5298;
            transform: translateY(-2px);
            color: white;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #e0e0e0;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25 remote rgba(30, 60, 114, 0.25);
            border-color: #1e3c72;
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card p-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h4 class="font-weight-bold" style="color: #1e3c72;">SSB Beji Putra</h4>
                            <p class="text-muted small">Silakan masuk untuk mengelola data</p>
                        </div>
                        </div>

                        <form wire:submit.prevent="login">
                            @if (session()->has('error'))
                                <div class="alert alert-danger py-2 small">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-secondary">Email</label>
                                <div class="input-group">
                                    <input type="text" wire:model.lazy="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="nama@email.com">
                                </div>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-secondary">Password</label>
                                <input type="password" wire:model.lazy="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-football btn-block w-100 shadow-sm">
                                <i class="fa fa-sign-in mr-2"></i> LOGIN
                            </button>
                        </form>
                    </div>
                </div>
                <p class="text-center mt-4 text-white-50 small">
                    &copy; <?php echo date('Y'); ?> SSB Beji Putra Management System
                </p>
            </div>
        </div>
    </div>
</div>