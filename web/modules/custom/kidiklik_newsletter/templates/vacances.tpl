<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>

        <style type="text/css">
            /*Media Queries*/
            @media screen and (max-width: 480px) {
                .left-sidebar.item .left,
                .left-sidebar.item .right,
                .right-sidebar .left,
                .right-sidebar .right
                {
                    max-width: 100% !important;
                }
            }

            @media screen and (max-width: 620px) {
                .immanquable.left-sidebar .left,
                .immanquable.left-sidebar .right,
                .immanquable.right-sidebar .left,
                .immanquable.right-sidebar .right
                {
                    max-width: 100% !important;
                }
            }
        </style>
        <!--[if (gte mso 9)|(IE)]>
        <style type="text/css">
            table { border-collapse: collapse; }
            .trait{ display:none; }
        </style>
        <![endif]-->
    </head>
    <body>
        <center class="wrapper" style="width: 100%; table-layout: fixed; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
            <div class="webkit" style="max-width: 600px;">
                <!--[if (gte mso 9)|(IE)]>
                <table width="600" align="center">
                <tr>
                <td>
                <![endif]-->
                <table class="outer" align="center" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; Margin: 0 auto; width: 100%; max-width: 600px;">
                    <tr>
                        <td class="one-column">
                            <table width="100%">
                                <tr>
                                    <td class="one-column" style="padding: 0;">
                                        <table width="100%" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c;">
                                            <tr>
                                                <td class="inner contents mentions" style="padding: 15px 10px 15px 10px; width: 100%; text-align: left;">
                                                    <p style="Margin: 0; font-size: 11px; Margin-bottom: 10px; text-align: center; color: #000000;">
                                                        <a href="{$newsletter->url}" style="color: #000000; text-decoration: underline; font-size: 11px;">Consulter la version en ligne.</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="one-column" style="padding: 0;">
                            <table width="100%" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c;">
                                <tr>
                                    <td class="full-width-image" style="padding: 0;">
                                        <a href="{$newsletter->url}">
                                            <img src="{$site}/images/newsletters/entetes/vacances.png" alt="Les bonnes idées vacances de kidiklik" width="600" style="border: 0; width: 100%; height: auto;">
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    {if $bloc}
                    <tr>
                        <td class="left-sidebar item" style="padding: 0; text-align: center; font-size: 0; background-color: #2f9cbe;">
                            <!--[if (gte mso 9)|(IE)]>
                            <table width="100%">
                            <tr>
                            <td width="160">
                            <![endif]-->
                            <table class="column right" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #ffffff; width: 100%; display: inline-block; vertical-align: middle; max-width: 160px;">
                                <tr>
                                    <td class="inner full-width-image" style="padding: 10px;">
                                        <img src="{$site}/images/newsletters/{$entete->upload_dir}/{$entete->image}" alt="{$entete->titre}" width="140" style="border: 0; width: 100%; height: auto;">
                                    </td>
                                </tr>
                            </table>
                            <!--[if (gte mso 9)|(IE)]>
                            </td><td width="440">
                            <![endif]-->
                            <table class="column left" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #ffffff; width: 100%; display: inline-block; vertical-align: middle; max-width: 440px;">
                                <tr>
                                    <td class="inner contents" style="padding: 15px 10px 15px 10px; width: 100%; font-size: 14px; text-align: left;">
                                        <p class="h2" style="Margin: 0; Margin-bottom: 5px; line-height: 18px; font-size: 18px; color: #ffffff; text-transform: uppercase">
                                            {$entete->titre}
                                        </p>
                                        <p style="Margin: 0; Margin-bottom: 9px; color: #ffffff; font-size: 15px;">{$entete->sous_titre}</p>
                                        <p style="Margin: 0;color:#ffffff">
                                                {$bloc->texte}
                                        </p>
                                    </td>
                                </tr>
                            </table>        
                            <!--[if (gte mso 9)|(IE)]>
                            </td>
                            </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>     
                    <tr>
                        <td class="one-column" style="padding: 0;">
                            <table width="100%" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c;">
                                <tr>
                                    <td class="inner contents entete" style="padding: 15px 0 0 0; width: 100%; padding-top: 15px; text-align: center; color: #000;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>       
                    {/if}                
                    {if $immanquable}
                        <tr>
                            <td class="left-sidebar item immanquable" style="padding: 0; text-align: center; font-size: 0; background-color: #e00025;">
                                <!--[if (gte mso 9)|(IE)]>
                                <table width="100%">
                                <tr>
                                <td width="265">
                                <![endif]-->
                                <table class="column left" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #ffffff; width: 100%; display: inline-block; vertical-align: middle; max-width: 265px;">
                                    <tr>
                                        <td class="inner contents" style="padding: 15px 10px 15px 10px; width: 100%; font-size: 14px; text-align: left;">
                                            <p class="h2" style="Margin: 0; font-weight: bold; Margin-bottom: 9px; line-height: 18px; font-size: 18px; color: #ffffff;"><a href="{$immanquable->url}" style="text-decoration: none; color: #ffffff;">{$immanquable->titre}</a></p>
                                            <p style="Margin: 0;">
                                                <a href="{$immanquable->url}" style="text-decoration: none; color: #ffffff;">
                                                    {$immanquable->texte}
                                                </a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                <!--[if (gte mso 9)|(IE)]>
                                </td><td width="335">
                                <![endif]-->
                                <table class="column right" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #ffffff; width: 100%; display: inline-block; vertical-align: middle; max-width: 335px;">
                                    <tr>
                                        <td class="inner full-width-image" style="padding: 0;">
                                            <a href="{$immanquable->url}" style="text-decoration: none; color: #ffffff;">
                                                <img src="{$site}/images/accueil/{$immanquable->image}" alt="$immanquable->titre}" width="335" style="border: 0; width: 100%; height: auto;">
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                <!--[if (gte mso 9)|(IE)]>
                                </td>
                                </tr>
                                </table>
                                <![endif]-->
                            </td>
                        </tr>    
                    {/if}
                    {foreach $datas as $data}
                        <tr>
                            {if $data@index %2 == 0}
                                {if $data@first && $newsletter->bandeau_rose}
                                    <td class="left-sidebar item immanquable" style="padding: 0; text-align: center; font-size: 0; background-color: #e00025;">
		                                <!--[if (gte mso 9)|(IE)]>
		                                <table width="100%">
		                                <tr>
		                                <td width="265">
		                                <![endif]-->
		                                <table class="column left" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #ffffff; width: 100%; display: inline-block; vertical-align: middle; max-width: 265px;">
		                                    <tr>
		                                        <td class="inner contents" style="padding: 15px 10px 15px 10px; width: 100%; font-size: 14px; text-align: left;">
		                                            <p class="h2" style="Margin: 0; font-weight: bold; Margin-bottom: 9px; line-height: 18px; font-size: 18px; color: #ffffff;"><a href="{$data->url}" style="text-decoration: none; color: #ffffff;">{$data->titre}</a></p>
		                                            <p style="Margin: 0;">
		                                                <a href="{$data->url}" style="text-decoration: none; color: #ffffff;">
		                                                    {$data->texte}
		                                                </a>
		                                            </p>
		                                        </td>
		                                    </tr>
		                                </table>
		                                <!--[if (gte mso 9)|(IE)]>
		                                </td><td width="335">
		                                <![endif]-->
		                                <table class="column right" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #ffffff; width: 100%; display: inline-block; vertical-align: middle; max-width: 335px;">
		                                    <tr>
		                                        <td class="inner full-width-image" style="padding: 0;">
		                                            <a href="{$data->url}" style="text-decoration: none; color: #ffffff;">
		                                                <img src="{$site}/images/accueil/{$data->image}" alt="$data->titre}" width="335" style="border: 0; width: 100%; height: auto;">
		                                            </a>
		                                        </td>
		                                    </tr>
		                                </table>
		                                <!--[if (gte mso 9)|(IE)]>
		                                </td>
		                                </tr>
		                                </table>
		                                <![endif]-->
		                            </td>
                                {else}
                                        <td class="left-sidebar item bloc" style="padding: 0; text-align: center; font-size: 0; border-top: 2px dotted #e95d0f;">
                                  
                                        <!--[if (gte mso 9)|(IE)]>
                                        <table width="100%">
                                        <tr>
                                        <td width="265">
                                        <![endif]-->
                                        <table class="column left" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; width: 100%; display: inline-block; vertical-align: middle; max-width: 265px;">
                                            <tr>
                                                <td class="inner full-width-image" style="padding: 15px 10px 15px 10px;">
                                                    <a href="{$data->url}" style="text-decoration: none; color: #8c8c8c;">
                                                        <img src="{$site}/images/{$data->upload_dir}/{$data->image}" alt="{$data->alt}" width="265" style="border: 0; width: 100%; height: auto;">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        </td><td width="335">
                                        <![endif]-->
                                        <table class="column right" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; width: 100%; display: inline-block; vertical-align: middle; max-width: 335px;">
                                            <tr>
                                                <td class="inner contents" style="padding: 15px 10px 15px 10px; width: 100%; font-size: 14px; text-align: left;">
                                                    <p class="h2" style="Margin: 0; font-size: 18px; font-weight: bold; Margin-bottom: 9px; line-height: 18px; color: #e95d0f;">
                                                        <a href="{$data->url}" style="text-decoration: none; color: #e95d0f;">{$data->titre}</a>
                                                    </p>
                                                    {if $data->ss_titre}
                                                        <p class="h3" style="Margin: 0; font-size: 12px; font-weight: bold; Margin-bottom: 9px; color: #000; text-transform: uppercase; line-height: 12px;">
                                                            {$data->ss_titre}
                                                        </p>
                                                    {/if}
                                                    <p class="trait" style="Margin: 0; width: 32px; height: 5px; margin-bottom: 9px; background-color: #e95d0f;">&nbsp;</p>
                                                    <p style="Margin: 0;">
                                                        <a href="{$data->url}" style="text-decoration: none; color: #8c8c8c;">{$data->texte}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                    </td>
                                 {/if}
                           {else}
                                    <td class="right-sidebar item bloc" dir="rtl" style="padding: 0; text-align: center; font-size: 0; border-top: 2px dotted #e95d0f;">
                                        <!--[if (gte mso 9)|(IE)]>
                                        <table width="100%">
                                        <tr>
                                        <td width="265">
                                        <![endif]-->
                                        <table class="column left" dir="ltr" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; width: 100%; display: inline-block; vertical-align: middle; max-width: 265px;">
                                            <tr>
                                                <td class="inner full-width-image" style="padding: 15px 10px 15px 10px;">
                                                    <a href="{$data->url}" style="text-decoration: none; color: #8c8c8c;">
                                                        <img src="{$site}/images/{$data->upload_dir}/{$data->image}" alt="{$data->alt}" width="265" style="border: 0; width: 100%; height: auto;">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        </td><td width="335">
                                        <![endif]-->
                                        <table class="column right" dir="ltr" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; width: 100%; display: inline-block; vertical-align: middle; max-width: 335px;">
                                            <tr>
                                                <td class="inner contents" style="padding: 15px 10px 15px 10px; width: 100%; font-size: 14px; text-align: left;">
                                                    <p class="h2" style="Margin: 0; font-size: 18px; font-weight: bold; Margin-bottom: 9px; line-height: 18px; color: #e95d0f;">
                                                        <a href="{$data->url}" style="text-decoration: none; color: #e95d0f;">
                                                            {$data->titre}
                                                        </a>
                                                    </p>
                                                    {if $data->ss_titre}<p class="h3" style="Margin: 0; font-size: 12px; font-weight: bold; Margin-bottom: 9px; color: #000; text-transform: uppercase; line-height: 12px;">{$data->ss_titre}</p>{/if}                                        
                                                    <p class="trait" style="Margin: 0; width: 32px; height: 5px; margin-bottom: 9px; background-color: #e95d0f;">&nbsp;</p>
                                                    <p style="Margin: 0;">
                                                        <a href="{$data->url}" style="text-decoration: none; color: #8c8c8c;">{$data->texte}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                    </td>    
                            {/if}
                        </tr>    
                    {/foreach}
                    {if $publicite}
                        <tr>
                            <td class="one-column" style="padding: 0;">
                                <table width="100%" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c;">
                                    <tr>
                                        <td class="inner full-width-image" style="padding: 15px 10px 15px 10px;">
                                            <p style="Margin: 0; font-size: 14px; Margin-bottom: 10px;">
                                                <a href="{$publicite->url}" style="color: #ee6a56; text-decoration: none;">
                                                    <img src="http://www.{$basesite}/images/vendos/{$publicite->image}" alt="{$publicite->titre}" width="600" style="border: 0; width: 100%; height: auto;">
                                                </a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>    
                    {/if}
                    {if $concours}
                        <tr>
                            <td class="one-column" style="padding: 0;">
                                <table width="100%" style="border-spacing: 0;">
                                    <tr>
                                        <td class="full-width-image entete" style="padding: 0; padding-top: 15px;">
                                            <img src="http://www.{$basesite}/images/newsletters/plaisir.png" alt="" width="600" style="border: 0; width: 100%; height: auto;">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        {foreach $concours as $concour}
                            <tr>
                                <td class="one-column" style="padding: 0;">
                                    <table width="100%" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c;">
                                        <tr>
                                            <td class="inner" style="padding: 15px 10px 15px 10px;">
                                                <p class="h1" style="Margin: 0; font-weight: bold; color: #000; text-transform: uppercase; text-align: center; line-height: 21px; font-size: 14px; Margin-bottom: 0;">{$concour->titre}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="inner-full full-width-image" style="padding: 0 10px 0 10px;">
                                                <a href="{$concour->url}" style="text-decoration: none;">
                                                    <img src="http://www.{$basesite}/images/{$concour->upload_dir}/{$concour->image}" alt="{$concour->alt}" width="600" style="border: 0; width: 100%; height: auto;">
                                                </a>    
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="inner" style="padding: 15px 10px 15px 10px;">
                                                <p class="jouer" style="Margin: 0; font-size: 14px; Margin-bottom: 10px; background-color: #2f9cbe; text-align: center; text-transform: uppercase; text-decoration: none; font-weight: bold; margin-bottom: 0;">
                                                    <a href="{$concour->url}" style="color: #fff; text-decoration: none; background-color: #2f9cbe; text-align: center; display: block; padding: 5px;">Jouer</a></p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        {/foreach}
                    {/if}
                    {if $donnees}
                    {foreach $donnees as $donnee}
                        <tr>
                            {if $donnee@index %2 == 0}
                                {if $donnee@first && $newsletter->bandeau_rose}
                                    <td class="left-sidebar item bloc first" style="padding: 0; text-align: center; font-size: 0; border-top: 0;">   
                                    {else}
                                        <td class="left-sidebar item bloc" style="padding: 0; text-align: center; font-size: 0; border-top: 2px dotted #e95d0f;">
                                        {/if}
                                        <!--[if (gte mso 9)|(IE)]>
                                        <table width="100%">
                                        <tr>
                                        <td width="265">
                                        <![endif]-->
                                        <table class="column left" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; width: 100%; display: inline-block; vertical-align: middle; max-width: 265px;">
                                            <tr>
                                                <td class="inner full-width-image" style="padding: 15px 10px 15px 10px;">
                                                    <a href="{$donnee->url}" style="text-decoration: none; color: #8c8c8c;">
                                                        <img src="{$site}/images/{$donnee->upload_dir}/{$donnee->image}" alt="{$donnee->alt}" width="265" style="border: 0; width: 100%; height: auto;">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        </td><td width="335">
                                        <![endif]-->
                                        <table class="column right" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; width: 100%; display: inline-block; vertical-align: middle; max-width: 335px;">
                                            <tr>
                                                <td class="inner contents" style="padding: 15px 10px 15px 10px; width: 100%; font-size: 14px; text-align: left;">
                                                    <p class="h2" style="Margin: 0; font-size: 18px; font-weight: bold; Margin-bottom: 9px; line-height: 18px; color: #e95d0f;">
                                                        <a href="{$donnee->url}" style="text-decoration: none; color: #e95d0f;">{$donnee->titre}</a>
                                                    </p>
                                                    {if $donnee->ss_titre}
                                                        <p class="h3" style="Margin: 0; font-size: 12px; font-weight: bold; Margin-bottom: 9px; color: #000; text-transform: uppercase; line-height: 12px;">{$donnee->ss_titre}</p>
                                                    {/if}
                                                    <p class="trait" style="Margin: 0; width: 32px; height: 5px; margin-bottom: 9px; background-color: #e95d0f;">&nbsp;</p>
                                                    <p style="Margin: 0;">
                                                        <a href="{$donnee->url}" style="text-decoration: none; color: #8c8c8c;">{$donnee->texte}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                    </td>
                                {else}
                                    <td class="right-sidebar bloc" dir="rtl" style="padding: 0; text-align: center; font-size: 0; border-top: 2px dotted #e95d0f;">
                                        <!--[if (gte mso 9)|(IE)]>
                                        <table width="100%">
                                        <tr>
                                        <td width="265">
                                        <![endif]-->
                                        <table class="column left" dir="ltr" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; width: 100%; display: inline-block; vertical-align: middle; max-width: 265px;">
                                            <tr>
                                                <td class="inner full-width-image" style="padding: 15px 10px 15px 10px;">
                                                    <a href="{$donnee->url}" style="text-decoration: none; color: #8c8c8c;">
                                                        <img src="{$site}/images/{$donnee->upload_dir}/{$donnee->image}" alt="{$donnee->alt}" width="265" style="border: 0; width: 100%; height: auto;">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        </td><td width="335">
                                        <![endif]-->
                                        <table class="column right" dir="ltr" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; width: 100%; display: inline-block; vertical-align: middle; max-width: 335px;">
                                            <tr>
                                                <td class="inner contents" style="padding: 15px 10px 15px 10px; width: 100%; font-size: 14px; text-align: left;">
                                                    <p class="h2" style="Margin: 0; font-size: 18px; font-weight: bold; Margin-bottom: 9px; line-height: 18px; color: #e95d0f;">
                                                        <a href="{$donnee->url}" style="text-decoration: none; color: #e95d0f;">{$donnee->titre}</a></p>
                                                    {if $donnee->ss_titre}<p class="h3" style="Margin: 0; font-size: 12px; font-weight: bold; Margin-bottom: 9px; color: #000; text-transform: uppercase; line-height: 12px;">{$donnee->ss_titre}</p>{/if}
                                                    <p class="trait" style="Margin: 0; width: 32px; height: 5px; margin-bottom: 9px; background-color: #e95d0f;">&nbsp;</p>
                                                    <p style="Margin: 0;">
                                                        <a href="{$donnee->url}" style="text-decoration: none; color: #8c8c8c;">{$donnee->texte}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                    </td>    
                                {/if}
                        </tr>    
                    {/foreach}
                    {/if}
                    <tr>
                        <td class="one-column" style="padding: 30px 0 0 0; ">
                            <table class="column cont-footer" align="center" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; margin: 0 auto; padding-top: 20px; border-top: 1px solid #ccc;">
                                <tr>
                                    <td class="left-sidebar footer" style="padding: 0; text-align: center; font-size: 0;">
                                        <!--[if (gte mso 9)|(IE)]>
                                        <table width="100%">
                                        <tr>
                                        <td width="100">
                                        <![endif]-->
                                        <table class="column left" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; width: 100%; display: inline-block; vertical-align: middle; max-width: 100px;">
                                            <tr>
                                                <td class="inner full-width-image" style="padding: 15px 10px 15px 10px;">
                                                    <a href="{$site}" style="text-decoration: none; color: #8c8c8c;">
                                                        <img src="{$site}/images/newsletters/piaf.png" alt="Aller sur le site kidiklik {$mondept->code}" width="100" style="border: 0; width: 100%; height: auto;">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        </td><td width="100">
                                        <![endif]-->
                                        <table class="column right" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c; width: 100%; display: inline-block; vertical-align: middle; max-width: 100px;">
                                            <tr>
                                                <td class="inner contents" style="padding: 15px 10px 15px 10px; width: 100%; text-align: left; font-size: 14px;">
                                                    <p style="Margin: 0; font-size: 14px; Margin-bottom: 10px; margin-bottom: 0;">
                                                        <a href="{$site}" style="text-decoration: none; color: #f00; text-transform: uppercase; font-size: 13px; font-weight: bold;">
                                                            Aller sur le site kidiklik {$mondept->code}
                                                        </a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        </td>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="one-column" style="padding: 0;">
                            <table width="100%" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c;">
                                <tr>
                                    <td class="inner contents rs" style="padding: 15px 10px 15px 10px; width: 100%; padding-bottom: 0; text-align: left;">
                                        <p style="Margin: 0; font-size: 14px; Margin-bottom: 10px; text-align: center; color: #000;">
                                            Suivez-nous pour découvrir toutes nos bonnes idées
                                        </p>
                                        <p class="cont-rs" style="Margin: 0; font-size: 14px; Margin-bottom: 10px; text-align: center; color: #000; margin-bottom: 0;">
                                            {foreach $reseaux as $rs}
                                                <a href="{$rs->url}" style="color: #ee6a56; text-decoration: none;">
                                                    <img src="{$site}/images/newsletters/rs/{$rs->icon}.png" alt="{$rs->nom}" style="border: 0;">
                                                </a>
                                            {/foreach}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>                                
                    <tr>
                        <td class="one-column">
                            <table width="100%">
                                <tr>
                                    <td class="one-column" style="padding: 0;">
                                        <table width="100%" style="border-spacing: 0; font-family: Arial, Helvetica, sans-serif; color: #8c8c8c;">
                                            <tr>
                                                <td class="inner contents mentions" style="padding: 15px 10px 15px 10px; width: 100%; text-align: left;">
                                                    <p style="Margin: 0; font-size: 11px; Margin-bottom: 10px; text-align: center; color: #000000;">
                                                        Si vous ne parvenez pas à afficher cet e-mail, veuillez consulter la version en <a href="{$newsletter->url}" style="color: #000000; text-decoration: underline; font-size: 11px;">ligne.</a>
                                                    </p>
                                                    <p style="Margin: 0; font-size: 11px; Margin-bottom: 10px; text-align: center; color: #000000;">
                                                        Si vous ne souhaitez plus recevoir de newsletter de la part de Kidiklik, ce serait bien dommage mais cliquez sur <a href="[[UNSUB_LINK_FR]]" style="color: #000000; text-decoration: underline; font-size: 11px;">desinscription</a> et vous ne recevrez plus nos bonnes idées.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </div>
        </center>
    </body>
</html>
