{* HEADER *}


{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}
<p>
  {ts}Would you like us to mark this relationship as ended?{/ts}
</p>
<div class="crm-section">
  <div class="label">{ts}Contact A{/ts}</div>
  <div class="content">{$contact_name_a}</div>
  <div class="clear"></div>
</div>
<div class="crm-section">
  <div class="label">{ts}Contact B{/ts}</div>
  <div class="content">{$contact_name_b}</div>
  <div class="clear"></div>
</div>
<div class="crm-section">
  <div class="label">{ts}Relationship type{/ts}</div>
  <div class="content">{$relationship_type}</div>
  <div class="clear"></div>
</div>

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

{* FIELD EXAMPLE: OPTION 2 (MANUAL LAYOUT)

  <div>
    <span>{$form.favorite_color.label}</span>
    <span>{$form.favorite_color.html}</span>
  </div>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
