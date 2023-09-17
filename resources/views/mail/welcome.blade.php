<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <style>
        @media (max-width: 616px) {
            .buttons-app {
                flex-direction: column;
                align-items: center;
                margin-bottom: 0;
            }

            .buttons-app a {
                margin: 0 0 15px !important;
            }
        }
    </style>
</head>

<body style="width: 100%!important; margin: 0; padding: 0;">
<div style="padding: 10px; line-height: 28px; font-family: 'Roboto',Verdana,Arial,sans-serif; font-size: 16px; color:#616161;">

    <table style="width: 100%; margin: 0 auto;">
        <td style="background-color: #F2F2FF;">
            <table style="width: 90%; margin: 20px auto;">
                <tr>
                    <td style=" background: #6A67E8; border-radius: 6px 6px 0px 0px;">
                        <p style="text-align: center">
                            <img src="https://app.lifepet.com.br/assets/images/logo.svg" alt="logotipo Lifepet" style="width: 180px; height: 65px;">
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="background: #fff; border-radius: 0px 0px 6px 6px;">
                        <table  style="width: 90%; margin: 40px auto 0;">
                            <tr>
                                <td>

                                    <h2 style="margin-bottom: 40px; font-weight: 500;">Olá, <span style="color: #6A67E8;">{{$customerName}}</span>!</h2>

                                    <p style="color: #616161;text-align: center; margin: 10px 0; font-size:18px;">Agora seu amigo de quatro patas está protegido 💜</p>

                                    <p style="color: #616161;text-align: center;margin: 10px 0; font-size:18px;">Ficamos muito felizes em ter vocês no plano {{$planName}}!</p>

                                    <p style="color: #616161; margin-top: 55px;">Usar o Plano é muito fácil. Confira os próximos passos:</p>

                                    <h3 style="color: #616161">1 - Baixe o App</h3>

                                    <p style="color: #616161">
                                        Após baixar o App, toque em "Já sou cliente". Na parte de baixo do app você verá a frase
                                        "É seu primeiro acesso? Finalize seu cadastro".
                                    </p>

                                    <div class="buttons-app" style="text-align: center; margin: 30px 0; display:flex; justify-content: center;">
                                        <a
                                                href="https://play.google.com/store/apps/details?id=com.lifepet.lifepet"
                                                style="text-decoration: none;border-radius: 6px;-moz-border-radius: 6px;-webkit-border-radius: 6px;-ms-border-radius: 6px;background-color: #676CE1;color: #FFFFFF;width: 209px;height: 66px;display: inline-block; margin: 0 15px;"
                                                title="Baixar via Play Store"
                                                target="_blank">
                                            <figure style="margin: 15px 16px 0px 0px;line-height: 0;display: inline-block;">
                                                <img src="https://new.lifepet.com.br/images/icon-play-store.svg" alt="" title="">
                                            </figure>
                                            <div class="txt" style="font-size: 16px;font-weight: 600;display: inline-block;line-height: 18px;">
                                                <span style="font-size: 12px;font-weight: 600;">Baixar via</span>
                                                <br>
                                                Play Store
                                            </div>
                                        </a>

                                        <a
                                                href="https://apps.apple.com/br/app/lifepet/id1361154811"
                                                style="text-decoration: none;border-radius: 6px;-moz-border-radius: 6px;-webkit-border-radius: 6px;-ms-border-radius: 6px;background-color: #676CE1;color: #FFFFFF;width: 209px;height: 66px;display: inline-block; margin: 0 15px;"
                                                title="Baixar via App Store"
                                                target="_blank">
                                            <figure style="margin: 15px 16px 0px 0px;line-height: 0;display: inline-block;">
                                                <img src="https://new.lifepet.com.br/images/icon-app-store.svg" alt="" title="">
                                            </figure>
                                            <div class="txt" style="font-size: 16px;font-weight: 600;display: inline-block;line-height: 18px;">
                                                <span style="font-size: 12px;font-weight: 600;">Baixar via</span>
                                                <br>
                                                App Store
                                            </div>
                                        </a>
                                    </div>

                                    <h3 style="color: #616161; margin: 30px 0 0 0;">2 - Guarde a carteirinha para agendamento</h3>

                                    <p style="color: #616161">
                                        No aplicativo, clique no perfil do pet e acesse a Carteirinha do PET. 
                                        Para agendamento ou atendimento, você deve apresentar ou informar o número que consta na carteirinha.
                                    </p>

                                    <h3 style="color: #616161; margin: 30px 0 0 0;">3 - Microchipagem</h3>

                                    <p style="color: #616161">
                                        *Exclusivo para os planos integrais ou Plus e Prime participativos*
                                    </p>

                                    <p style="color: #616161">
                                        Se você contratou essa modalidade de plano no Espírito Santo, você deve efetuar o agendamento para realizar a microchipagem do seu pet.
                                    </p>

                                    <p style="color: #616161">
                                        Não se preocupe: o microchip não machuca o pet e é do tamanho de um grão de arroz. 
                                        Sua aplicação é semelhante a uma injeção.
                                    </p>

                                    <ul style="list-style-type: none;">
                                        <li>a) Se você estiver no Espírito Santo, entre em contato com a nossa Referenciada Pet Prime na Praia da Costa e faça o agendamento.</li>
                                        <li>b) Se você estiver em outros estados, em breve informaremos os locais para realizarem a microchipagem, não se preocupe, estamos buscando Clínicas Referenciadas.</li>
                                    </ul>

                                    <p style="color: #616161; font-weight: bold;">
                                        Desejamos uma ótima semana para você e seu pet.
                                    </p>

                                    <p style="color: #616161; margin-top: 30px;">
                                        Lambeijos, <span style="color: #6A67E8; font-weight: 500;">Equipe Lifepet 💜</span>
                                    </p>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="font-size: 14px; text-align: center; color: #9E9E9E;">
                                        Tem alguma dúvida? Entre em contato conosco pelo WhatsApp (11) 99831-5729.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <hr style="border: 0; border-top: 1px solid #E0E0E0;">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="text-align: center;">
                                        <a href="https://www.facebook.com/lifepetsaude/" style="margin: 0 1.5% 0 1.5%; text-decoration: none;">
                                            <img src="https://app.lifepet.com.br/assets/images/facebook-icon.png">
                                        </a>

                                        <a href="https://www.instagram.com/lifepetsaude/"  style="margin: 0 1.5% 0 1.5%; text-decoration: none;">
                                            <img src="https://app.lifepet.com.br/assets/images/instagram-icon.png">
                                        </a>

                                        <a href="https://www.linkedin.com/company/lifepetsaude/mycompany/" style="margin: 0 1.5% 0 1.5%; text-decoration: none;">
                                            <img src="https://app.lifepet.com.br/assets/images/linkedin-icon.png">
                                        </a>

                                        <a href="https://api.whatsapp.com/send/?phone=5527981018538&text&app_absent=0" style="margin: 0 1.5% 0 1.5%; text-decoration: none;">
                                            <img src="https://app.lifepet.com.br/assets/images/whatsapp-icon.png">
                                        </a>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="font-size: 14px; line-height: 150%; text-align: center; color: #9E9E9E;">
                                        @ LIFEPET BRASIL PLANO DE SAÚDE S.A.<br>
                                        CNPJ sob o nº 32.618.650/0001-91
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="font-size: 14px; text-align: center; margin-top: 0;">
                                        <a href="https://www.lifepet.com.br/">
                                            www.lifepet.com.br
                                        </a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </table>
</div>
</body>
</html>