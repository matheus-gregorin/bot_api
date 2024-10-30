<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo a BOTT!</title>
    <style>
        /* Estilos básicos para o corpo do e-mail */
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }

        /* Estilos para o título */
        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        /* Estilos para o parágrafo */
        p {
            color: #666;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Bem-vindo!</h1>
    <p>Olá, {{ $user['name'] }},</p>
    <p>Agradecemos por se conectar com nosso BOTT! Estamos aqui para te ajudar com suas tarefas.</p>
    <p>Para começar, você pode acessar os site e todas as nossas ferramentas.</p>
    <p>Se tiver alguma dúvida, não hesite em nos contatar.</p>
    <p>Atenciosamente,</p>
    <p> Bott </p>
</body>
</html>