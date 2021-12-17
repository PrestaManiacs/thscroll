{*
* 2006-2021 THECON SRL
*
* NOTICE OF LICENSE
*
* DISCLAIMER
*
* YOU ARE NOT ALLOWED TO REDISTRIBUTE OR RESELL THIS FILE OR ANY OTHER FILE
* USED BY THIS MODULE.
*
* @author    THECON SRL <contact@thecon.ro>
* @copyright 2006-2021 THECON SRL
* @license   Commercial
*}

<div class="back-to-top">
    <a href="#" {if $THSCROLL_SHOW_AFTER}style="display: none;"{/if}>
        {if $THSCROLL_ICON_SHOW}
            <span class="back-icon">
                {if $THSCROLL_ICON_LIBRARY eq 'material_icons'}
                    <i class="material-icons">expand_less</i>
                {elseif $THSCROLL_ICON_LIBRARY eq 'font_awesome'}
                    {if $THSCROLL_PS_VERSION eq 6}
                        <i class="icon-angle-up" aria-hidden="true"></i>
                    {else}
                        <i class="fa fa-angle-up" aria-hidden="true"></i>
                    {/if}
                {else}
                    {if $THSCROLL_ICON_HTML}
                        {$THSCROLL_ICON_HTML nofilter}
                    {/if}
                {/if}
            </span>
        {/if}
        {if $THSCROLL_TEXT_SHOW}
            <span class="back-text">{$THSCROLL_TEXT_CONTENT|escape:'htmlall':'UTF-8'}</span>
        {/if}
    </a>
</div>
