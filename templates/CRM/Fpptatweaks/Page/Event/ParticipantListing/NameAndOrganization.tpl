{*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
*}
{* Displays participant listing for an event. *}
{if $rows}
    {include file="CRM/common/pager.tpl" location="top"}
    {* $printLinkUrl is only defined when we're NOT printing to pdf. Therefore we
     * use this to decide whether to show content on screen vs pdf.
     *}
    {if $printLinkUrl}
      {* We are NOT in pdf mode. *}
      <a id="crm-fpptatweaks-print-link" href="{$printLinkUrl}" class="button">
        <span><i class="crm-i fa-print" aria-hidden="true"></i> Print as PDF</span>
      </a>
    {else}
      {* We ARE in pdf mode. *}
      <table id="fppta-participant-listing-header">
        <tr>
          <td><img src="{crmResURL ext=com.joineryhq.fpptatweaks file=images/fppta_logo.png}"/></td>
          <td>
            <h1>{$fpptaTweaksPageTitle}</h1>
          </td>
        </tr>
      </table>
    {/if}
    
       <table cellpadding="0" cellspacing="0" border="0">
         <tr class="columnheader">
        {foreach from=$headers item=header}
        <th scope="col">
          {$header.name}
        </th>
      {/foreach}
         </tr>
      {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"}">
            <td class="crm-participant-name">{$row.display_name}</td>
            <td class="crm-participant-organization">{$row.organization}</td>
         </tr>
      {/foreach}
      </table>
    {include file="CRM/common/pager.tpl" location="bottom"}
{else}
    <div class='spacer'></div>
    <div class="messages status no-popup">
      {icon icon="fa-info-circle"}{/icon}
        {ts}There are currently no participants registered for this event.{/ts}
      </div>
{/if}
