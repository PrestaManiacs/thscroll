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

<style>
    .back-to-top a {
        position: fixed;
        z-index: 999999;
        {if $THSCROLL_ALLIGN && $THSCROLL_SIDE_DISTANCE}
        {$THSCROLL_ALLIGN|escape:'htmlall':'UTF-8'}: {$THSCROLL_SIDE_DISTANCE|escape:'htmlall':'UTF-8'}px;
        {/if}

        {if $THSCROLL_BOTTOM_DISTANCE}
            bottom: {$THSCROLL_BOTTOM_DISTANCE|escape:'htmlall':'UTF-8'}px;
        {/if}
        {if $THSCROLL_WIDTH}
            width: {$THSCROLL_WIDTH|escape:'htmlall':'UTF-8'}px;
        {/if}
        {if $THSCROLL_HEIGHT}
            height: {$THSCROLL_HEIGHT|escape:'htmlall':'UTF-8'}px;
        {/if}
        {if $THSCROLL_BORDER_RADIUS}
            border-radius: {$THSCROLL_BORDER_RADIUS|escape:'htmlall':'UTF-8'}px;
        {/if}
        {if $THSCROLL_BACK_COLOR}
            background-color: {$THSCROLL_BACK_COLOR|escape:'htmlall':'UTF-8'};
        {/if}
        {if $THSCROLL_TEXT_COLOR}
            border: 1px solid {$THSCROLL_BORDER_COLOR|escape:'htmlall':'UTF-8'};
        {/if}
        display: flex;
        align-items: center;
        justify-content: center;
        flex-flow: column;
    }

    .back-to-top a:hover {
    {if $THSCROLL_H_BACK_COLOR}
        background-color: {$THSCROLL_H_BACK_COLOR|escape:'htmlall':'UTF-8'};
    {/if}
    {if $THSCROLL_H_TEXT_COLOR}
        border: 1px solid {$THSCROLL_H_BORDER_COLOR|escape:'htmlall':'UTF-8'};
    {/if}
    }

    .back-text {
    {if $THSCROLL_TEXT_COLOR}
        color: {$THSCROLL_TEXT_COLOR|escape:'htmlall':'UTF-8'};
    {/if}
    {if $THSCROLL_TEXT_SIZE}
        font-size: {$THSCROLL_TEXT_SIZE|escape:'htmlall':'UTF-8'}px;
    {/if}
    {if $THSCROLL_TEXT_LH}
        line-height: {$THSCROLL_TEXT_LH|escape:'htmlall':'UTF-8'}px;
        height: {$THSCROLL_TEXT_LH|escape:'htmlall':'UTF-8'}px;
    {/if}
    }
    .back-to-top a:hover .back-text {
    {if $THSCROLL_H_TEXT_COLOR}
        color: {$THSCROLL_H_TEXT_COLOR|escape:'htmlall':'UTF-8'};
    {/if}
    }

    .back-icon > *{
    {if $THSCROLL_ICON_COLOR}
        color: {$THSCROLL_ICON_COLOR|escape:'htmlall':'UTF-8'};
    {/if}
    {if $THSCROLL_ICON_SIZE}
        font-size: {$THSCROLL_ICON_SIZE|escape:'htmlall':'UTF-8'}px;
        line-height: {$THSCROLL_ICON_SIZE|escape:'htmlall':'UTF-8'}px;
        height: {$THSCROLL_ICON_SIZE|escape:'htmlall':'UTF-8'}px;
    {/if}
        display: block;
    }

    .back-to-top a:hover .back-icon {
    {if $THSCROLL_H_ICON_COLOR}
        color: {$THSCROLL_H_ICON_COLOR|escape:'htmlall':'UTF-8'};
    {/if}
    }
</style>
