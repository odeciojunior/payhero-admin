<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,600,700,800" rel="stylesheet">
</head>

<style>
    body {
        font-family: 'Muli', Arial, Helvetica, sans-serif;
        margin: 0;
        line-height: 135% !important;
    }

    #btn {
        padding: 12px 35px;
        color: white;
        background-color: #f2571f;
        font-weight: 700;
        font-size: 14px;
        border-radius: 5px;
    }

    a#btn {
        text-decoration: none;
    }


    .mtop20 {
        margin-top: 20px;
    }

    .verde {
        color: #3fa85d;
    }

    .a-content {
        color: #f2571f;
        text-decoration: none;
        font-weight: 700;
    }

    .dark-grey {
        color: #444444;
    }

    #clear {
        width: 100%;
        height: 30px;
    }

    #half {
        width: 100%;
        height: 15px;
    }

    .whitebg {
        background: white;
    }

    .pad-padrao {
        padding: 25px 80px;
    }

    .pad-bottom {
        padding-bottom: 20px;
        margin-bottom: 20px;
    }

    @media (min-width:300px) and (max-width: 1024px) {
        * {
            font-size: 1.45rem;
        }

        h1 {
            font-size: 2rem;
        }

        .small {
            font-size:0.8rem;
        }

        .logo {
            width: 200px;
            }

        #btn {
            font-size: 1.3rem;
        }

    }
</style>

<body style="font-family: 'Muli', sans-serif; background-color: #f6f6f6;">

    <table style="width:600px; text-align:center" class="container" cellpadding="0" cellspacing="0">

        <table style="padding: 20px 5px;text-align:center">
            <tr>
                <td style="width:600px; text-align:center">
                    <img src="{!! asset('modules/global/assets/img/logoCloudfox.png') !!}" width="150px;" class="logo">
                </td>
            </tr>
        </table>

        <table width="600px" style="border-radius-top: 10px; line-height: 135%; padding: 20px 80px;background-color: white" class="container" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%" style="padding-top: 20px;text-align:center">
                    <img src="{!! asset('modules/global/assets/img/beta-tester.png') !!}">
                </td>
            </tr>
            <tr>
                <td>
                    <h1 class="blue" style="text-align:center"> Seu convite chegou! </h1>
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 20px;text-align:center">
                    <span class="dark-grey"> Beta Tester, só falta você! </span>
                </td>
            </tr>
        </table>

        <table style="width:600px;text-align:center" class="container whitebg" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="3" style="width: 100%; height:2px; background-color:#ededed "> </td>
            </tr>
        </table>

        <table width="600px" class="container pad-padrao whitebg" style="text-align: center" cellpadding="0" cellspacing="0">
            <tr class="dark-grey pad-bottom">
                <td width="100%">
                    <p> 

                        Você solicitou um convite como Beta Tester e agora chegou a sua hora de fazer parte da <strong> nossa revolução!</strong>
                        <br><br>

                        Nosso trabalho é construir uma plataforma com a sua cara. Que bom que você quer construir essa história com a gente!

                        <br><br>  Sinta-se a vontade e em casa. 
                        A plataforma é sua e feita para você!
                        Boas vendas!

                    </p>

                </td>
            </tr>

            <tr id="half"> </tr>

        </table>

        <table style="width:600px; text-align:center" class="container whitebg" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="3" style="width: 100%; height:2px;background-color:#ededed"> </td>
            </tr>
        </table>

        <table style="width:600px;" class="container whitebg" style="padding: 10px 80px; padding-bottom: 20px; border-bottom-radius: 10px;" align="center" cellpadding="0" cellspacing="0">

            <tr id="half"> </tr>

            <tr class="dark-grey pad-bottom">
                <td style="width:100%; text-align:center">
                    <a href="{!! $link !!}" id="btn">FINALIZAR CADASTRO </a>
                </td>
            </tr>
            <tr id="half"> </tr>
        </table>

        <table style="width:600px; text-align:center" style="padding: 0px 80px; ">
            <tr>
                <td height="70">
                    <p style="color: #757575; font-size: 0.7rem; margin: 0; text-align:center;" class="small">
                        Esse é um e-mail automático. Não responda esse e-mail. Caso queira contatar nosso suporte,
                        envie um e-mail para sac@cloudfox.net
                    </p>
                </td>
            </tr>
        </table>

    </table>

</body>

</html>