@extends('layouts.app')

@section('css')
    @parent
    <style>
        .page-content, .content {
            display: table;
        }
        .content {
            background: white;
            padding: 20px;
        }
    </style>
    <style type="text/css">
        .box-shadow {
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
        }
        .box-pagamento {
            padding: 1.5rem;
            background-color:#fff;
            border-radius:15px;
            margin-bottom:25px;
        }
        .box-pagamento h4 {
            margin-bottom: 1rem;
        }
        #submit-button {
            margin-top: 1rem;
            background: #009bf2;
        }
        div#main-container {
            margin-top: 0rem;
            margin-bottom: 3rem;
        }
        select[readonly] {
            background: #eee;
            pointer-events: none;
            touch-action: none;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <h1>
            Link Pagamento
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row-fluid" style="padding-left: 20px">
                    <div class="container" id="main-container">

                        <form action="#" method="POST" id="form-pagamento">

                            <div class="row-fluid">
                                <div class="col-md-4">
                                    <div class="box-pagamento box-shadow">
                                        <h4 class="text-center">Seus dados</h4>
                                        <div class="form-group">
                                            <label for="cep">Nome Completo:</label>
                                            <input type="nome" class="form-control" id="name" value="<?= $linkPagamento->cliente->nome_cliente ?>" readonly="readonly" required="required" name="name" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email:</label>
                                            <input type="email" class="form-control" value="<?= $linkPagamento->cliente->email ?>" id="email" readonly="readonly" required="required" name="email" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="celular">Celular:</label>
                                            <input type="celular" class="form-control" value="<?= $linkPagamento->cliente->celular ?>" id="celular" required="required" readonly="readonly" name="celular" placeholder="">
                                        </div>

                                        <div class="form-group">
                                            <label for="cpf">CPF:</label>
                                            <input type="text" class="form-control" id="cpf" value="<?= $linkPagamento->cliente->cpf ?>" required="required" name="cpf" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="cep">CEP:</label><div class="row-fluid">
                                                <div class="col-6">

                                                    <input type="text" class="form-control" id="cep" required="required" name="cep" placeholder="" maxlength="8">
                                                </div>
                                                <div class="col-4">
                                                    <a href="javascript:;" onclick="loadCEPInfo()" class="btn btn-primary" id="loadCEPButton">BUSCAR</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-fluid">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="country">País:</label>
                                                    <input type="text" class="form-control" readonly="readonly" id="country" required="required" name="country" placeholder="" value="Brasil">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="state">Estado (UF):</label>
                                                    <select name="state" class="form-control" required="required" id="state" readonly>
                                                        <option value="ES" selected>ES</option>
                                                        <option value="SP">SP</option>
                                                    </select>
                                                    <!-- <input type="text" class="form-control" readonly="readonly" id="state" required="required" name="state" placeholder=""> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-fluid">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="city">Cidade:</label>
                                                    <input type="text" class="form-control" readonly="readonly" id="city" required="required" name="city" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="neighbourhood">Bairro:</label>
                                                    <input type="text" class="form-control" readonly="readonly" id="neighbourhood" required="required" name="neighbourhood" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-fluid">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="street">Rua:</label>
                                                    <input type="text" class="form-control" readonly="readonly" id="street" required="required" name="street" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="address_number">Número:</label>
                                                    <input type="text" class="form-control" id="address_number" required="required" name="address_number" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="box-pagamento box-shadow">
                                        <h4 class="text-center">Pagamento</h4>
                                        <div class="form-group">
                                            <label for="cep">Forma de pagamento:</label>
                                            <input type="text" class="form-control" id="payment_type" required="required" name="payment_type" placeholder="" value="CARTÃO DE CRÉDITO" readonly="readonly">
                                        </div>
                                        <div class="form-group">
                                            <label for="cpf">Número do cartão:</label>
                                            <input type="number" class="form-control" id="card_number" required="required" name="card_number" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="brand">Bandeira:</label>
                                            <select required="required" name="brand" id="brand" class="form-control">
                                                <option value="mastercard">MASTERCARD</option>
                                                <option value="visa">VISA</option>
                                                <option value="diners">DINERS</option>
                                                <option value="amex">AMEX</option>
                                                <option value="elo">ELO</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="holder">Nome como escrito no cartão:</label>
                                            <input type="text" class="form-control" id="holder" required="required" name="holder" placeholder="">
                                        </div>
                                        <div class="row-fluid">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="expires_in">Validade:</label>
                                                    <input type="text" class="form-control" id="expires_in" required="required" name="expires_in" placeholder="00/00">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="cvv">CVV:</label>
                                                    <input type="text" class="form-control" id="ccv" name="ccv" required="required" placeholder="000">
                                                </div>

                                                <img src="https://lifepet.com.br/wp-content/uploads/2020/03/fundossl-300x72.png" alt="" width="175" height="42">

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="box-pagamento box-shadow">
                                        <h4 class="text-center">Resumo do pedido</h4>
                                        <table class="table" id="resume">
                                            <tr>
                                                <td><span id="client_name"><?= $linkPagamento->cliente->primeir_nome_cliente ?></span><br/></td>
                                                <td>R$ <?= number_format($linkPagamento->valor, 2, ',', '.') ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <button type="submit" id="submit-button" class="btn btn-primary form-control text-center"><i class="fas fa-lock"></i> Comprar agora</button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection
