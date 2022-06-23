<div class="stk2-column">
    <h3>{$column['category']->name}</h3>
    
    <div class="stk2-column-products">
      {foreach from=$column.products item="product"}

        {include file="module:ps_stk2/views/templates/_partials/product.tpl" product=$product}
 
      {/foreach}
    </div>
    
    <a href="{$column.allProductsLink}">{l s='All products' d='Modules.Stk2.Shop'}</a>

</div>