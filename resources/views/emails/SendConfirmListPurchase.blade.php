<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Compra</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #ffffff; border-radius: 8px;">
        <tr>
            <td style="padding: 20px 0; text-align: center; background-color: #4CAF50; color: #ffffff;">
                <h1 style="margin: 0;">Confirmação de Compra</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <p>Olá, {{ $client['name'] }},</p>
                <p>Obrigado pela sua compra! Seu pedido foi confirmado com sucesso.</p>
                <h2>Detalhes do Pedido:</h2>
                <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 8px; background-color: #f9f9f9;">Produto</th>
                        <th style="border: 1px solid #ddd; padding: 8px; background-color: #f9f9f9;">Quantidade</th>
                        <th style="border: 1px solid #ddd; padding: 8px; background-color: #f9f9f9;">Preço Unidade</th>
                    </tr>

                    @foreach ($items as $item)
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;"> {{ $item['name'] }} </td>
                            <td style="border: 1px solid #ddd; padding: 8px;"> {{ $item['qtd'] }} </td>
                            <td style="border: 1px solid #ddd; padding: 8px;">R$ {{ $item['value'] }} </td>
                        </tr>
                    @endforeach
                </table>
                <p><strong>Total:</strong> R$ {{ $list['value'] }} </p>
                <p style="margin-top: 30px;">Atenciosamente,<br>Equipe BOTT</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; text-align: center; background-color: #f4f4f4; color: #777;">
                <p style="margin: 0;">BOOT - Todos os direitos reservados.</p>
                <p style="margin: 0;"><a href="#" style="color: #4CAF50; text-decoration: none;">Política de Privacidade</a> | <a href="#" style="color: #4CAF50; text-decoration: none;">Termos de Uso</a></p>
            </td>
        </tr>
    </table>
</body>
</html>
