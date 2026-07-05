<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperação de senha</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: Arial, Helvetica, sans-serif; color: #333333;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f8; padding: 40px 0;">
        <tr>
            <td align="center">

                <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);">

                    <tr>
                        <td style="background-color: #2563eb; padding: 28px 32px; text-align: center;">
                            <h1 style="margin: 0; font-size: 24px; color: #ffffff; font-weight: 700;">
                                Recupere sua senha
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 36px 32px;">

                            <p style="margin: 0 0 18px; font-size: 16px; line-height: 1.6;">
                                Olá, <strong>{{ $user->name }}</strong>.
                            </p>

                            <p style="margin: 0 0 24px; font-size: 16px; line-height: 1.6;">
                                Para recuperar sua senha, clique no botão abaixo:
                            </p>

                            <table cellpadding="0" cellspacing="0" width="100%" style="margin: 32px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $forgotPassword->url }}"
                                           style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-size: 16px; font-weight: 700;">
                                            Recuperar senha
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #666666;">
                                Se o botão não funcionar, copie e cole o link abaixo no seu navegador:
                            </p>

                            <p style="margin: 0 0 28px; font-size: 14px; line-height: 1.6; word-break: break-all;">
                                <a href="{{ $forgotPassword->url }}" style="color: #2563eb; text-decoration: underline;">
                                    {{ $forgotPassword->url }}
                                </a>
                            </p>

                            <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #666666;">
                                Se você não pediu essa recuperação, ignore este email.
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 32px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 12px; color: #888888;">
                                Este é um email automático. Por favor, não responda.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>