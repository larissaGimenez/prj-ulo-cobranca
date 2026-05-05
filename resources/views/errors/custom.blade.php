@extends('layouts.app')

@section('content')
<div class="px-3">
    <div class="row min-vh-75 flex-center p-5">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
            <div class="text-center">
                <img class="img-fluid mb-5 d-dark-none" src="{{ asset('assets/img/spot-illustrations/error-500.png') }}" alt="" width="400" />
                <img class="img-fluid mb-5 d-light-none" src="{{ asset('assets/img/spot-illustrations/error-500-dark.png') }}" alt="" width="400" />
                
                <h1 class="text-body-secondary fw-bolder mb-3">Ops! Algo deu errado.</h1>
                
                <p class="text-body-tertiary mb-5">
                    Erro ao processar essa requisição. Nossa equipe técnica já foi notificada automaticamente. 
                    <br class="d-none d-sm-block">Entre em contato com a equipe de desenvolvimento se o problema persistir.
                </p>
                
                <a class="btn btn-lg btn-primary shadow-none fw-bold" href="{{ url('/') }}">
                    <span class="fas fa-home me-2"></span>Voltar para a Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Ajuste para centralizar caso o layout padrão tenha margens */
    .min-vh-75 { min-height: 75vh; }
    .flex-center { display: flex; align-items: center; justify-content: center; }
</style>
@endsection
