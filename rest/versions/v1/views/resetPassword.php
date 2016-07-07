<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $user \rest\versions\v1\models\User
 */
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Choose a new password for RMWL</title>
    <style type="text/css" rel="stylesheet" media="all">
        *:not(br):not(tr):not(html) {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        body {
            width: 100% !important;
            height: 100%;
            margin: 0;
            line-height: 1.4;
            background-color: #F2F4F6;
            color: #74787E;
            -webkit-text-size-adjust: none;
        }

        a {
            color: #3869D4;
        }

        h1 {
            margin-top: 0;
            color: #2F3133;
            font-size: 19px;
            font-weight: bold;
            text-align: left;
        }

        h2 {
            margin-top: 0;
            color: #2F3133;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
        }

        h3 {
            margin-top: 0;
            color: #2F3133;
            font-size: 14px;
            font-weight: bold;
            text-align: left;
        }

        p {
            margin-top: 0;
            color: #74787E;
            font-size: 16px;
            line-height: 1.5em;
            text-align: left;
        }
    </style>
</head>
<body>
<table style="width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;" width="100%" cellpadding="0"
       cellspacing="0">
    <tr>
        <td align="center">
            <table style="width: 100%; margin: 0 ;padding: 0;" width="100%" cellpadding="0" cellspacing="0">
                <!-- Logo -->
                <tr>
                    <td style="padding: 25px 0; text-align: center;">
                        <a style="font-size: 16px; font-weight: bold; color: #bbbfc3; text-decoration: none; text-shadow: 0 1px 0 white;">RMWL</a>
                    </td>
                </tr>
                <!-- Email Body -->
                <tr>
                    <td style=" width: 100%;margin: 0;padding: 0;border-top: 1px solid #EDEFF2;border-bottom: 1px solid #EDEFF2;background-color: #FFF;"
                        width="100%">
                        <table style="width: 570px;margin: 0 auto;padding: 0;" align="center" width="570"
                               cellpadding="0" cellspacing="0">
                            <!-- Body content -->
                            <tr>
                                <td style="padding: 35px;">
                                    <h1>Hi <?= $user->username ?>,</h1>
                                    <p>You recently requested to reset your password for your RMWL account.
                                        Click the button below to reset it.</p>
                                    <!-- Action -->
                                    <table style=" width: 100%;margin: 30px auto;padding: 0;text-align: center;"
                                           align="center" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center">
                                                <div>
                                                    <a href="<?= \Yii::$app->params['domainUrl'] . '/update-password?token=' . $user->password_reset_token ?>"
                                                       style="display: inline-block; width: 200px;background-color: #3869D4;border-radius: 3px;color: #ffffff;font-size: 15px;line-height: 45px;text-align: center;text-decoration: none;-webkit-text-size-adjust: none;mso-hide: all;">Reset
                                                        your password</a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <p>If you did not request a password reset, please ignore this email or reply to let
                                        us know.</p>
                                    <p><strong>P.S.</strong> We also love hearing from you and helping you with any
                                        issues you have. Please reply to this email if you want to ask a question</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table style="width: 100% !important" align="center" width="570" cellpadding="0"
                               cellspacing="0">
                            <tr>
                                <td>
                                    <p style="font-size: 12px; text-align: center;">&copy; <?= date('Y');?>RMWL. All
                                        rights reserved.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
