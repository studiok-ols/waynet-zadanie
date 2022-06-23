{**
 * @author    StudioK
*}

<section>
  
  <h2>{l s='Our Products' d='Modules.Stk2.Shop'}</h2>
  
  <div class="stk2-column-wrapper">
  {foreach from=$columns item="column"}
  	{if $column.products}
    	
        {include file="module:ps_stk2/views/templates/_partials/column.tpl" column=$column}
                       
    {/if}
    
  {/foreach}
  </div>

</section>
